import pandas as pd
import numpy as np
import os
import re
import pickle
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.naive_bayes import MultinomialNB
from sklearn.model_selection import train_test_split
from sklearn.metrics import accuracy_score, precision_score, recall_score, f1_score, confusion_matrix, classification_report
import matplotlib.pyplot as plt
import seaborn as sns

class SQLIDetector:
    def __init__(self, model_path='models/sqli_model.pkl'):
        self.model_path = model_path
        self.model = None
        self.vectorizer = None

    def load_and_clean_data(self, filepath):
        print(f"[INFO] Loading dataset from {filepath}...")
        try:
            try:
                df = pd.read_csv(filepath, encoding='utf-8', on_bad_lines='skip', low_memory=False)
            except UnicodeDecodeError:
                df = pd.read_csv(filepath, encoding='utf-16', on_bad_lines='skip', low_memory=False)
            
            df = df.loc[:, ~df.columns.str.contains('^Unnamed')]
            
            target_col = None
            for col in df.columns:
                temp_col = pd.to_numeric(df[col], errors='coerce')
                unique_vals = temp_col.dropna().unique()
                if len(unique_vals) <= 2 and set(unique_vals).issubset({0, 1}):
                    target_col = col
                    break
            
            if target_col:
                possible_text_cols = [c for c in df.columns if c != target_col]
                text_col = max(possible_text_cols, key=lambda c: df[c].astype(str).nunique())
            else:
                text_col = df.columns[0]
                target_col = df.columns[-1]

            df[target_col] = pd.to_numeric(df[target_col], errors='coerce')
            df.dropna(subset=[target_col], inplace=True)
            df[text_col] = df[text_col].astype(str)
            
            # =========================================================================
            # [PERBAIKAN 1]: KITA TIDAK LAGI MEMBUANG DATA NORMAL DARI DATASET PUBLIK
            # Biarkan dataset publik memberikan data "prose" (teks biasa/kompleks)
            # agar model belajar membedakan injeksi dari teks yang sulit, bukan hanya dari
            # data sintetis buatan kita.
            # =========================================================================
            print(f"[INFO] {filepath}: Loaded {len(df)} rows. Keeping ALL data (Normal & SQLi).")
            
            df.drop_duplicates(subset=[text_col], inplace=True)
            df[target_col] = df[target_col].astype(int)
            
            return df, text_col, target_col
        except Exception as e:
            print(f"[ERROR] Failed to load data: {e}")
            return None, None, None

    def train(self, df, text_col, target_col):
        print("[INFO] Cleaning noise from dataset...")
        df['temp_len'] = df[text_col].str.len()
        
        sql_symbols = ["'", "--", ";", "(", ")", "=", "*", "union", "select", "drop"]
        def is_real_sqli(text, label):
            if label == 0: return True
            if len(str(text)) > 3: return True
            return any(sym in str(text).lower() for sym in sql_symbols)

        mask = df.apply(lambda row: is_real_sqli(row[text_col], row[target_col]), axis=1)
        df = df[mask].copy()
        print(f"[INFO] Dataset size after noise filtering: {len(df)} rows")

        print("[INFO] Training model with Char+Word N-grams...")
        self.vectorizer = TfidfVectorizer(
            min_df=2, 
            analyzer='char', 
            ngram_range=(1, 5), 
            max_features=20000,
            lowercase=True
        )
        
        # =========================================================================
        # [PERBAIKAN 2]: MEMPERBAIKI DATA LEAKAGE (KEBOCORAN DATA)
        # Pisahkan Teks (Train/Test) TERLEBIH DAHULU, baru lakukan fit_transform.
        # =========================================================================
        
        X_raw = df[text_col]
        y = df[target_col].values

        # 1. Split text mentah
        X_train_raw, X_test_raw, y_train, y_test = train_test_split(X_raw, y, test_size=0.2, random_state=42)
        
        # 2. Vectorizer HANYA mempelajari (fit) dari data latih (X_train)
        X_train = self.vectorizer.fit_transform(X_train_raw)
        
        # 3. Vectorizer HANYA mentransformasi data uji (X_test), tidak boleh di-fit!
        X_test = self.vectorizer.transform(X_test_raw)
        
        # =========================================================================

        self.model = MultinomialNB(alpha=0.1)
        self.model.fit(X_train, y_train)
        
        y_pred = self.model.predict(X_test)
        
        acc = accuracy_score(y_test, y_pred)
        prec = precision_score(y_test, y_pred, zero_division=0)
        rec = recall_score(y_test, y_pred, zero_division=0)
        f1 = f1_score(y_test, y_pred, zero_division=0)
        
        print("\n" + "="*40)
        print("EVALUASI MODEL (NAIVE BAYES) - REVISI")
        print("="*40)
        print(f"Accuracy  : {acc * 100:.2f}%")
        print(f"Precision : {prec * 100:.2f}%")
        print(f"Recall    : {rec * 100:.2f}%")
        print(f"F1-Score  : {f1 * 100:.2f}%")
        print("="*40)
        print("\nClassification Report:")
        print(classification_report(y_test, y_pred, target_names=['Normal', 'SQLi'], zero_division=0))
        
        # Plot Confusion Matrix
        cm = confusion_matrix(y_test, y_pred)
        plt.figure(figsize=(6,4))
        sns.heatmap(cm, annot=True, fmt='d', cmap='Blues', xticklabels=['Normal', 'SQLi'], yticklabels=['Normal', 'SQLi'])
        plt.xlabel('Predicted Label')
        plt.ylabel('True Label')
        plt.title('Confusion Matrix - SQLi Detection')
        plt.tight_layout()
        
        os.makedirs(os.path.dirname(self.model_path), exist_ok=True)
        graph_path = os.path.join(os.path.dirname(self.model_path), 'confusion_matrix.png')
        plt.savefig(graph_path)
        print(f"[INFO] Confusion matrix graph saved to {graph_path}")
        plt.close()

        # --- KODE TAMBAHAN UNTUK GENERATE BAR CHART METRIK ---
        metrics_names = ['Accuracy', 'Precision', 'Recall', 'F1-Score']
        metrics_values = [acc * 100, prec * 100, rec * 100, f1 * 100]

        plt.figure(figsize=(8, 5))
        bars = plt.bar(metrics_names, metrics_values, color=['#4C72B0', '#55A868', '#C44E52', '#8172B2'])
        plt.ylim(90, 100) # Membatasi sumbu Y dari 90 ke 100 agar perbedaannya terlihat
        plt.ylabel('Persentase (%)')
        plt.title('Evaluasi Kinerja Model Naive Bayes')
        
        # Menambahkan angka di atas setiap batang grafik
        for bar in bars:
            yval = bar.get_height()
            plt.text(bar.get_x() + bar.get_width()/2, yval + 0.1, f'{yval:.2f}%', ha='center', va='bottom', fontweight='bold')

        bar_chart_path = os.path.join(os.path.dirname(self.model_path), 'metrics_barchart.png')
        plt.savefig(bar_chart_path)
        print(f"[INFO] Metrics Bar Chart saved to {bar_chart_path}")
        plt.close()
        # -----------------------------------------------------
        
        self.save_model()
        return True

    def save_model(self):
        os.makedirs(os.path.dirname(self.model_path), exist_ok=True)
        with open(self.model_path, 'wb') as f:
            pickle.dump((self.model, self.vectorizer), f)
        print(f"[INFO] Model saved to {self.model_path}")

    def load_model(self):
        if os.path.exists(self.model_path):
            with open(self.model_path, 'rb') as f:
                self.model, self.vectorizer = pickle.load(f)
            print(f"[INFO] Model loaded from {self.model_path}")
            return True
        print(f"[ERROR] Model file {self.model_path} not found")
        return False

    # ===================================================================
    # PRE-SCREENING: Filter input yang jelas-jelas AMAN sebelum ke model
    # ===================================================================
    
    SQLI_STRUCTURAL_PATTERNS = [
        r"['\"]\s*(OR|AND|UNION|SELECT|INSERT|UPDATE|DELETE|DROP|ALTER|EXEC|EXECUTE|HAVING|GROUP\s+BY|ORDER\s+BY)\s", 
        r"(--|#|/\*)\s*$", 
        r"(--|#|/\*)\s*\w", 
        r"['\"]\s*;\s*(SELECT|INSERT|UPDATE|DELETE|DROP|ALTER|EXEC)", 
        r"\bUNION\s+(ALL\s+)?SELECT\b", 
        r"\bSELECT\s+.+\s+FROM\b", 
        r"\b(DROP|ALTER|TRUNCATE)\s+(TABLE|DATABASE)\b", 
        r"\bINSERT\s+INTO\b", 
        r"\bUPDATE\s+\w+\s+SET\b", 
        r"\bDELETE\s+FROM\b", 
        r"['\"]\s*=\s*['\"]" , 
        r"\b(OR|AND)\s+['\"]?\d+['\"]?\s*=\s*['\"]?\d+", 
        r"\b(OR|AND)\s+['\"]\w+['\"]\s*=\s*['\"]\w+['\"]", 
        r"\bEXEC(UTE)?\s*\(", 
        r"\bWAITFOR\s+DELAY\b", 
        r"\bBENCHMARK\s*\(", 
        r"\bSLEEP\s*\(", 
        r"\bLOAD_FILE\s*\(", 
        r"\bINTO\s+(OUT|DUMP)FILE\b", 
        r"\b(CHAR|CHR|CONCAT|SUBSTRING)\s*\(", 
        r"0x[0-9a-fA-F]{6,}", 
        r"\\x[0-9a-fA-F]{2}", 
    ]
    
    _compiled_sqli_patterns = None
    
    @classmethod
    def _get_compiled_patterns(cls):
        if cls._compiled_sqli_patterns is None:
            cls._compiled_sqli_patterns = [
                re.compile(p, re.IGNORECASE) for p in cls.SQLI_STRUCTURAL_PATTERNS
            ]
        return cls._compiled_sqli_patterns
    
    def _is_safe_input(self, text: str) -> bool:
        if not text or not isinstance(text, str):
            return True
        
        text = text.strip()
        
        if len(text) <= 30:
            if not self._has_sqli_structure(text):
                return True
        
        if re.match(r'^[\d\s.,\-/\\:+()]+$', text):
            return True
        
        if re.match(r'^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$', text):
            return True
        
        sql_special_chars = set("'\"`;#*")
        if not any(c in sql_special_chars for c in text):
            if not self._has_sqli_structure(text):
                return True
        
        if re.match(r'^(https?://|/[a-zA-Z0-9._/-]+)$', text):
            return True
        
        if re.match(r'^(\+62|62|08)[0-9]{8,13}$', text.replace('-', '').replace(' ', '')):
            return True
        
        return False
    
    def _has_sqli_structure(self, text: str) -> bool:
        for pattern in self._get_compiled_patterns():
            if pattern.search(text):
                return True
        return False
    
    def predict(self, query):
        if self.model is None or self.vectorizer is None:
            if not self.load_model():
                return None, None
        
        if self._is_safe_input(query):
            return 0, np.array([1.0, 0.0])
        
        query_vec = self.vectorizer.transform([query])
        prediction = self.model.predict(query_vec)[0]
        proba = self.model.predict_proba(query_vec)[0]
        
        if prediction == 1:
            confidence = float(proba[1] * 100)
            if confidence < 95.0 and not self._has_sqli_structure(query):
                print(f"[OVERRIDE] Model flagged as SQLi ({confidence:.1f}%) but no structural pattern found: {query[:80]}")
                return 0, np.array([1.0, 0.0])
        
        return prediction, proba