import numpy as np
import librosa
import tensorflow as tf
import matplotlib.pyplot as plt
import io
import base64
import librosa.display

FAKE_SEGMENT_COUNT_THRESHOLD = 3
FAKE_SEGMENT_CONFIDENCE_THRESHOLD = 0.75

class AudioAnalyzer:
    def __init__(self):
        self.model = tf.keras.models.load_model("models/best_lstm_model_v3.keras")

    def preprocess_audio(self, file_path, sr=16000, duration=2.5, n_mfcc=13, max_len=130):
        try:
            y, _ = librosa.load(file_path, sr=sr, mono=True)
            if len(y) < int(sr * 0.5):
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

            return np.expand_dims(full, axis=0)
        except Exception:
            return None

    def analyze_audio(self, audio_path):
        features = self.preprocess_audio(audio_path)
        if features is None:
            return {
                "type": "audio",
                "prediction": "UNKNOWN",
                "confidence": 0.0,
                "details": {"reason": "Invalid or too short audio"}
            }

        prob = self.model.predict(features, verbose=0)[0][0]
        prediction = "FAKE" if prob > 0.5 else "REAL"
        confidence = prob if prediction == "FAKE" else (1 - prob)

        return {
            "type": "audio",
            "prediction": prediction,
            "confidence": round(float(confidence), 2),
            "details": {
                "model": "BiLSTM",
                "features": "MFCC + Delta + Delta-Delta"
            }
        }

    def _extract_audio_features(self, y, sr, n_mfcc=13):
        """Extracts a comprehensive set of audio features."""
        # --- Standard Features (for model compatibility) ---
        mfcc = librosa.feature.mfcc(y=y, sr=sr, n_mfcc=n_mfcc)
        
        # --- Advanced Speech Features (for analysis) ---
        # Pitch and Voicing
        f0, voiced_flag, _ = librosa.pyin(y, fmin=librosa.note_to_hz('C2'), fmax=librosa.note_to_hz('C7'), frame_length=2048)
        voiced_f0 = f0[voiced_flag]

        # Jitter (local)
        jitter = 0.0
        if len(voiced_f0) > 1:
            # Calculate periods and their differences
            periods = 1000 / voiced_f0 # Period in ms
            jitter = np.mean(np.abs(np.diff(periods)))

        # Shimmer (local)
        shimmer = 0.0
        rms_energy = librosa.feature.rms(y=y)[0]
        if len(rms_energy) > 1:
            # Calculate amplitude differences
            shimmer = np.mean(np.abs(np.diff(rms_energy)))
        
        # Pitch Variance
        pitch_variance = np.std(voiced_f0) if len(voiced_f0) > 0 else 0.0

        return {
            "jitter": jitter,
            "shimmer": shimmer,
            "pitch_variance": pitch_variance
        }

    def _preprocess_segment(self, y, sr=16000, n_mfcc=13, max_len=130):
        mfcc = librosa.feature.mfcc(y=y, sr=sr, n_mfcc=n_mfcc)
        delta = librosa.feature.delta(mfcc)
        delta2 = librosa.feature.delta(mfcc, order=2)
        full = np.vstack([mfcc, delta, delta2]).T

        if full.shape[0] < max_len:
            pad_width = max_len - full.shape[0]
            full = np.pad(full, ((0, pad_width), (0, 0)), mode='constant')
        else:
            full = full[:max_len, :]

        return np.expand_dims(full, axis=0)

    def analyze_audio_segments(self, audio_path, sr=16000, top_db=20, chunk_duration=2.5, fake_threshold=0.6):
        try:
            y, _ = librosa.load(audio_path, sr=sr, mono=True)
        except Exception:
            return {"status": "error", "message": "Failed to load audio file."}

        # VAD: Split audio into voiced segments
        voiced_segments = librosa.effects.split(y, top_db=top_db)
        if voiced_segments.size == 0:
            return {"type": "audio", "prediction": "UNKNOWN", "confidence": 0, "details": {"reason": "No voice activity detected."}}

        chunk_len = int(sr * chunk_duration)
        all_predictions = []
        segment_count = 0
        min_conf = 1.0
        min_conf_index = -1
        # لتحديد المقطع الأقل ثقة
        segment_chunks = []

        for start, end in voiced_segments:
            segment = y[start:end]
            
            # Divide segment into fixed-size chunks
            for i in range(0, len(segment), chunk_len):
                segment_count += 1
                chunk = segment[i:i + chunk_len]
                if len(chunk) < int(sr * 0.5): # Skip very short trailing chunks
                    continue
                
                # Preprocess and predict
                features = self._preprocess_segment(chunk, sr)
                if features is None:
                    continue

                # Extract and print new features for analysis
                new_features = self._extract_audio_features(chunk, sr)
                
                prob = self.model.predict(features, verbose=0)[0][0]
                prediction = "FAKE" if prob > 0.5 else "REAL"
                confidence = prob if prediction == "FAKE" else (1 - prob)

                # حفظ chunk للمقاطع
                segment_chunks.append(chunk)
                all_predictions.append({
                    "segment": segment_count,
                    "prediction": prediction, 
                    "confidence": float(confidence),
                    "raw_prob": float(prob), # Raw probability of being FAKE
                    "jitter": float(new_features['jitter']) if new_features['jitter'] is not None else None,
                    "shimmer": float(new_features['shimmer']) if new_features['shimmer'] is not None else None,
                    "pitch_variance": float(new_features['pitch_variance']) if new_features['pitch_variance'] is not None else None
                })
                # تحديد الأقل ثقة
                if confidence < min_conf:
                    min_conf = confidence
                    min_conf_index = len(all_predictions) - 1
        
        if not all_predictions:
            return {"type": "audio", "prediction": "UNKNOWN", "confidence": 0, "details": {"reason": "No valid segments to analyze."}}

        # --- Aggregation Logic ---
        final_prediction = None
        override_reason = None
        secondary_override_reason = None

        # 1. Check for any high-confidence FAKE segment (Model-based)
        for p in all_predictions:
            if p["raw_prob"] > fake_threshold:
                final_prediction = "FAKE"
                override_reason = f"Segment {p['segment']} exceeded model FAKE threshold ({p['raw_prob']:.2f} > {fake_threshold})."
                break
        
        # 2. New Rule: Multi-Fake Segment Override
        if final_prediction is None:
            high_conf_fakes = [
                p for p in all_predictions 
                if p["prediction"] == "FAKE" and p["confidence"] > FAKE_SEGMENT_CONFIDENCE_THRESHOLD
            ]

            if len(high_conf_fakes) >= FAKE_SEGMENT_COUNT_THRESHOLD:
                final_prediction = "FAKE"
                culprit_segments = ", ".join([f"#{s['segment']} ({s['confidence']:.2f})" for s in high_conf_fakes])
                secondary_override_reason = (
                    f"Override triggered by {len(high_conf_fakes)} FAKE segments with confidence > "
                    f"{FAKE_SEGMENT_CONFIDENCE_THRESHOLD}. Segments: {culprit_segments}."
                )

        # 3. If no override, fallback to majority vote
        if final_prediction is None:
            votes = [p['prediction'] for p in all_predictions]
            final_prediction = max(set(votes), key=votes.count)

        # Clean up segments list for the final response
        segments_for_response = []
        max_conf = 0
        max_conf_index = -1
        for idx, p in enumerate(all_predictions):
            seg = {
                "prediction": p["prediction"],
                "confidence": p["confidence"],
                "jitter": p.get("jitter"),
                "shimmer": p.get("shimmer"),
                "pitch_variance": p.get("pitch_variance")
            }
            # تحديد الأعلى ثقة
            if p["confidence"] > max_conf:
                max_conf = p["confidence"]
                max_conf_index = idx
            segments_for_response.append(seg)
        
        # توليد MFCC لكل مقطع (مع تحسين الأداء)
        print(f"Generating MFCC for {len(segment_chunks)} segments...")
        for idx, chunk in enumerate(segment_chunks):
            try:
                mfcc = librosa.feature.mfcc(y=chunk, sr=sr, n_mfcc=13)
                segments_for_response[idx]["mfcc_image_base64"] = self.mfcc_to_base64(mfcc, sr)
                print(f"Generated MFCC for segment {idx + 1}/{len(segment_chunks)}")
            except Exception as e:
                print(f"Failed to generate MFCC for segment {idx + 1}: {str(e)}")
                segments_for_response[idx]["mfcc_image_base64"] = None
        
        # تحديد المقطع الأكثر شكًا للتوضيح
        target_index = -1
        if final_prediction == "REAL":
            target_index = max_conf_index  # الأعلى ثقة للملفات REAL
        else:
            target_index = min_conf_index  # الأقل ثقة للملفات FAKE
        
        # إضافة علامة للمقطع الأكثر شكًا
        if target_index >= 0:
            segments_for_response[target_index]["is_most_suspicious"] = True
            print(f"Marked segment {target_index + 1} as most suspicious (confidence: {all_predictions[target_index]['confidence']:.4f}, prediction: {final_prediction})")
        
        avg_confidence = np.mean([p['confidence'] for p in all_predictions])

        details = {
            "segments_analyzed": len(all_predictions),
            "segments": segments_for_response,
            "model": "BiLSTM (Segmented)",
            "features": "MFCC + Delta + Delta-Delta"
        }
        if override_reason:
            details["override_reason"] = override_reason
        if secondary_override_reason:
            details["secondary_override_reason"] = secondary_override_reason

        return {
            "type": "audio",
            "prediction": final_prediction,
            "confidence": round(float(avg_confidence), 2),
            "details": details
        }

    def mfcc_to_base64(self, mfcc, sr):
        """تحويل MFCC إلى صورة base64 مع تحسين الأداء"""
        try:
            # تقليل حجم الصورة لتحسين الأداء
            plt.figure(figsize=(4, 2), dpi=80)  # حجم أصغر
            plt.imshow(mfcc, aspect='auto', origin='lower', cmap='viridis')
            plt.colorbar(format='%+2.0f dB')
            plt.title('MFCC Spectrogram', fontsize=8)
            plt.xlabel('Time Frame', fontsize=7)
            plt.ylabel('MFCC Coefficient', fontsize=7)
            plt.tight_layout()
            
            # حفظ الصورة في buffer مع ضغط
            buffer = io.BytesIO()
            plt.savefig(buffer, format='png', dpi=80, bbox_inches='tight', pad_inches=0.1)
            buffer.seek(0)
            img_base64 = base64.b64encode(buffer.getvalue()).decode()
            plt.close()  # إغلاق الصورة لتوفير الذاكرة
            buffer.close()
            return img_base64
        except Exception as e:
            print(f"Error generating MFCC image: {str(e)}")
            plt.close()  # إغلاق الصورة في حالة الخطأ
            return None
