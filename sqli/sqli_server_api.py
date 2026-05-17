from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
import uvicorn
import os
from src.ml.sqli_detector import SQLIDetector

from contextlib import asynccontextmanager

# Initialize Detector
MODEL_PATH = 'models/sqli_model.pkl'
detector = SQLIDetector(model_path=MODEL_PATH)

@asynccontextmanager
async def lifespan(app: FastAPI):
    # Load model on startup
    if not detector.load_model():
        print("[WARN] Model not found. Please run training script first.")
    yield
    # Clean up (if any) on shutdown

# Initialize FastAPI
app = FastAPI(
    title="SQL Injection Detection API",
    description="API untuk mendeteksi serangan SQL Injection menggunakan Naive Bayes",
    version="1.0.0",
    lifespan=lifespan
)

# CORS Configuration
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

class QueryInput(BaseModel):
    query_text: str

class BatchQueryInput(BaseModel):
    queries: list[str]

@app.get("/")
def read_root():
    return {
        "status": "Online",
        "model_loaded": detector.model is not None,
        "message": "SQLi Detection API is running on port 8001"
    }

@app.post("/predict")
async def predict_sqli(data: QueryInput):
    """
    Endpoint untuk memprediksi apakah query mengandung SQL Injection.
    """
    if detector.model is None:
        if not detector.load_model():
            raise HTTPException(status_code=503, detail="Model is not ready.")

    prediction, proba = detector.predict(data.query_text)
    is_sqli = bool(prediction == 1)
    confidence = float(proba[prediction] * 100)
    
    status = "⚠️ SQL INJECTION" if is_sqli else "✅ NORMAL"
    q_display = data.query_text[:50] + "..." if len(data.query_text) > 50 else data.query_text
    print(f"[API LOG] Query: {q_display} | Result: {status} | Confidence: {confidence:.2f}%")

    return {
        "is_sqli": is_sqli,
        "confidence": round(confidence, 2)
    }

@app.post("/predict/batch")
async def predict_batch(data: BatchQueryInput):
    """
    Endpoint untuk memprediksi list query sekaligus (Batch).
    Berguna untuk mempercepat pengecekan di Laravel.
    """
    if detector.model is None:
        if not detector.load_model():
            raise HTTPException(status_code=503, detail="Model is not ready.")

    for q in data.queries:
        prediction, proba = detector.predict(q)
        is_sqli = bool(prediction == 1)
        confidence = float(proba[prediction] * 100)
        
        q_display = q[:80] + "..." if len(q) > 80 else q
        
        # Threshold dinaikkan ke 90% untuk mengurangi false positive
        # + model sudah memiliki pre-screening & post-screening di detector
        if is_sqli and confidence >= 90.0:
            print(f"[BATCH LOG] ⚠️ ATTACK DETECTED in query: {q_display} | Confidence: {confidence:.2f}%")
            return {
                "is_sqli": True,
                "confidence": round(confidence, 2),
                "malicious_query": q[:500] # Increased to 500 characters
            }
        else:
            # Jika prediksi Normal (0), atau prediksi SQLi tapi confidence < 90% (dianggap aman)
            # Maka ambil tingkat keyakinan model terhadap kelas Normal (indeks ke-0)
            normal_confidence = float(proba[0] * 100)
            print(f"[BATCH LOG] ✅ NORMAL INPUT in query: {q_display} | Confidence: {normal_confidence:.2f}%")
        
    return {
        "is_sqli": False,
        "confidence": 0
    }

if __name__ == "__main__":
    uvicorn.run("sqli_server_api:app", host="0.0.0.0", port=8001, reload=True)