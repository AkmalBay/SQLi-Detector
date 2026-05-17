from src.ml.sqli_detector import SQLIDetector
import os
import glob
import pandas as pd

if __name__ == "__main__":
    DATA_DIR = 'data/raw/'
    MODEL_PATH = 'models/sqli_model.pkl'
    
    detector = SQLIDetector(model_path=MODEL_PATH)
    
    csv_files = glob.glob(os.path.join(DATA_DIR, "*.csv"))
    if not csv_files:
        print(f"[ERROR] No CSV files found in {DATA_DIR}")
        exit(1)
        
    print(f"[PROCESS] Found {len(csv_files)} datasets. Merging data...")
    all_data = []
    text_col, target_col = None, None
    
    for filepath in csv_files:
        df, t_col, tgt_col = detector.load_and_clean_data(filepath)
        if df is not None:
            # Standardize column names across datasets based on the first successfully loaded dataset
            if text_col is None:
                text_col, target_col = t_col, tgt_col
            
            df = df.rename(columns={t_col: text_col, tgt_col: target_col})
            all_data.append(df[[text_col, target_col]])
            
    if not all_data:
        print("[ERROR] Could not load any valid data.")
        exit(1)
        
    final_df = pd.concat(all_data, ignore_index=True)
    final_df.dropna(subset=[text_col, target_col], inplace=True)
    final_df[text_col] = final_df[text_col].astype(str)
    final_df.drop_duplicates(subset=[text_col], inplace=True)
    print(f"[INFO] Combined dataset size: {len(final_df)} rows")
    
    success = detector.train(final_df, text_col, target_col)
    if success:
        print("[SUCCESS] Model trained and saved successfully.")
    else:
        print("[ERROR] Training failed.")
