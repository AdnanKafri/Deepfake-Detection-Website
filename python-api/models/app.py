import streamlit as st
import numpy as np
import librosa
import os
import tensorflow as tf

# ---------- واجهة Streamlit - يجب أن تكون أول أمر ----------
st.set_page_config(page_title="🔍 Audio Deepfake Detector", layout="centered")

# ---------- إعداد النموذج ----------
@st.cache_resource
def load_model_cached():
    model_path = "best_lstm_model_v3.keras"  # ضع الملف بجانب السكربت
    model = tf.keras.models.load_model(model_path)
    return model

model = load_model_cached()

# ---------- دالة التحضير ----------
def preprocess_audio(file_path, sr=16000, duration=2.5, n_mfcc=13, max_len=130):
    try:
        y, _ = librosa.load(file_path, sr=sr, mono=True)
        if len(y) < int(sr * 1.0):  # صوت قصير جدًا
            return None

        mfcc = librosa.feature.mfcc(y=y, sr=sr, n_mfcc=n_mfcc)
        delta = librosa.feature.delta(mfcc)
        delta2 = librosa.feature.delta(mfcc, order=2)

        full = np.vstack([mfcc, delta, delta2]).T

        if full.shape[0] < max_len:
            pad_width = max_len - full.shape[0]
            full = np.pad(full, ((0, pad_width), (0, 0)), mode='constant')
        else:
            full = full[:max_len, :]

        return np.expand_dims(full, axis=0)  # (1, 130, 39)

    except Exception as e:
        return None

# ---------- واجهة المستخدم الرئيسية ----------
st.title("🧠 Deepfake Audio Detection (v3)")
st.markdown("قم برفع ملف صوتي بصيغة `.wav` وسيقوم النموذج بتحليله وتصنيفه بأنه 🔴 مزيف أو ✅ حقيقي.")

uploaded_file = st.file_uploader("🎵 ارفع الملف الصوتي هنا", type=["wav"])

if uploaded_file is not None:
    st.audio(uploaded_file, format='audio/wav')

    with open("temp.wav", "wb") as f:
        f.write(uploaded_file.read())

    features = preprocess_audio("temp.wav")
    if features is None:
        st.error("❌ الملف قصير جدًا أو غير صالح.")
    else:
        prob = model.predict(features)[0][0]
        label = "✅ حقيقي (Real)" if prob < 0.5 else "🔴 مزيف (Fake)"
        confidence = (1 - prob) if label.startswith("✅") else prob
        st.success(f"🔎 النتيجة: {label}")
        st.info(f"📊 نسبة الثقة: {confidence * 100:.2f}%")