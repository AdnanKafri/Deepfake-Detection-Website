import torch
import torchvision.transforms as transforms
from PIL import Image
import cv2
import numpy as np
import os
from pathlib import Path
from facenet_pytorch import MTCNN
from efficientnet_pytorch import EfficientNet
import shutil
import uuid
from faker import Faker
try:
    import tensorflow as tf  # noqa: F401
except ImportError:
    tf = None

fake = Faker()

PYTHON_API_DIR = Path(__file__).resolve().parent.parent
PROJECT_ROOT = PYTHON_API_DIR.parent
MODELS_DIR = PYTHON_API_DIR / "models"
LARAVEL_ANALYSIS_FRAMES_DIR = PROJECT_ROOT / "laravel-app" / "storage" / "app" / "public" / "analysis_frames"
IMAGE_MODEL_PATH = MODELS_DIR / "fine_tuned_epoch_25.pt"

class MediaAnalyzer:
    def __init__(self):
        self.device = torch.device("cuda" if torch.cuda.is_available() else "cpu")

        self.model = EfficientNet.from_name('efficientnet-b4')
        self.model._fc = torch.nn.Linear(self.model._fc.in_features, 2)
        self.model.load_state_dict(torch.load(IMAGE_MODEL_PATH, map_location=self.device))
        self.model = self.model.to(self.device).eval()

        self.mtcnn = MTCNN(image_size=224, margin=40, keep_all=True, device=self.device, post_process=False, min_face_size=20).eval()

        self.transform = transforms.Compose([
            transforms.Resize((224, 224)),
            transforms.ToTensor(),
            transforms.Normalize(mean=[0.485, 0.456, 0.406],
                                 std=[0.229, 0.224, 0.225])
        ])

    def _delete_dir_if_exists(self, dir_path):
        if os.path.exists(dir_path):
            shutil.rmtree(dir_path)

    def classify_face(self, face_img_pil):
        face_tensor = self.transform(face_img_pil).unsqueeze(0).to(self.device)
        with torch.no_grad():
            outputs = self.model(face_tensor)
            probs = torch.softmax(outputs, dim=1)
            conf_score, pred_class = torch.max(probs, 1)
        label = "REAL" if pred_class.item() == 1 else "FAKE"
        return label, conf_score.item()

    def analyze_image(self, image_path):
        base_id = os.path.basename(image_path).split('.')[0]
        unique_suffix = uuid.uuid4().hex[:8]
        analysis_id = f"{base_id}_{unique_suffix}"
        
        # إنشاء مجلد مشترك مع Laravel
        shared_dir = LARAVEL_ANALYSIS_FRAMES_DIR
        analysis_dir = shared_dir / analysis_id
        original_dir = os.path.join(analysis_dir, "original")
        cropped_dir = os.path.join(analysis_dir, "cropped")
        
        # إنشاء المجلدات
        os.makedirs(original_dir, exist_ok=True)
        os.makedirs(cropped_dir, exist_ok=True)

        img_pil = Image.open(image_path).convert('RGB')
        
        # حفظ الصورة الأصلية
        original_name = f"original_{fake.random_int(10)}.jpg"
        original_path = os.path.join(original_dir, original_name)
        img_pil_resized = img_pil.resize((640, 480), Image.Resampling.LANCZOS)
        img_pil_resized.save(original_path, quality=85, optimize=True)

        boxes, probs = self.mtcnn.detect(img_pil)
        if boxes is None:
            return {"type": "image", "prediction": "UNKNOWN", "confidence": 0.0, "details": {"reason": "No faces detected"}}

        face_details = []
        real_conf_sum, fake_conf_sum = 0.0, 0.0
        real_count, fake_count = 0, 0

        for box_idx, (box, prob) in enumerate(zip(boxes, probs)):
            if prob < 0.9:
                continue

            box = [int(b) for b in box]
            face = img_pil.crop(box)
            label, conf = self.classify_face(face)

            # حفظ الوجه المقصوص
            face_resized = face.resize((224, 224), Image.Resampling.LANCZOS)
            cropped_name = f"cropped_{fake.random_int(10)}.jpg"
            cropped_path = os.path.join(cropped_dir, cropped_name)
            face_resized.save(cropped_path, quality=85, optimize=True)

            face_details.append({
                "original_path": f"storage/analysis_frames/{analysis_id}/original/{original_name}",
                "cropped_face_path": f"storage/analysis_frames/{analysis_id}/cropped/{cropped_name}",
                "prediction": label,
                "confidence": float(conf)
            })

            if label == "REAL":
                real_count += 1
                real_conf_sum += conf
            else:
                fake_count += 1
                fake_conf_sum += conf

        if not face_details:
            return {"type": "image", "prediction": "UNKNOWN", "confidence": 0.0, "details": {"reason": "No valid faces detected"}}

        final_prediction = "REAL" if real_count > fake_count else "FAKE"
        if real_count == fake_count:
            final_prediction = "REAL" if real_conf_sum > fake_conf_sum else "FAKE"

        final_conf = (real_conf_sum / real_count) if final_prediction == "REAL" and real_count else (fake_conf_sum / fake_count if fake_count else 0.0)

        return {
            "type": "image",
            "prediction": final_prediction,
            "confidence": round(final_conf, 2),
            "details": {
                "faces_detected": len(face_details),
                "real_faces": real_count,
                "fake_faces": fake_count,
                "average_real_confidence": round(real_conf_sum / real_count, 2) if real_count else 0.0,
                "average_fake_confidence": round(fake_conf_sum / fake_count, 2) if fake_count else 0.0,
                "face_details": face_details,
                "model": "EfficientNet-B4"
            }
        }

    def analyze_video(self, video_path, frames_to_sample=10, save_frames=False):
        base_id = os.path.basename(video_path).split('.')[0]
        # Add a unique suffix to avoid reusing the same folder between tests
        unique_suffix = uuid.uuid4().hex[:8]
        analysis_id = f"{base_id}_{unique_suffix}"
        
        # إنشاء مجلد في storage مشترك مع Laravel
        if save_frames:
            # استخدام مجلد مشترك مع Laravel
            shared_dir = LARAVEL_ANALYSIS_FRAMES_DIR
            analysis_dir = shared_dir / analysis_id
            original_dir = os.path.join(analysis_dir, "original")
            cropped_dir = os.path.join(analysis_dir, "cropped")
            
            # إنشاء المجلدات
            os.makedirs(original_dir, exist_ok=True)
            os.makedirs(cropped_dir, exist_ok=True)

        cap = cv2.VideoCapture(video_path)
        frame_count = int(cap.get(cv2.CAP_PROP_FRAME_COUNT))
        if frame_count == 0:
            return {"type": "video", "prediction": "UNKNOWN", "confidence": 0.0, "details": {"reason": "Empty or invalid video file"}}

        selected_indices = np.linspace(0, frame_count - 1, frames_to_sample).astype(int)
        frame_images = []
        prediction_votes = []
        real_conf_sum, fake_conf_sum = 0.0, 0.0
        real_count, fake_count = 0, 0
        analyzed_frames = 0
        frames_with_faces = 0

        for idx in selected_indices:
            cap.set(cv2.CAP_PROP_POS_FRAMES, idx)
            success, frame = cap.read()
            if not success:
                continue

            analyzed_frames += 1
            frame_rgb = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
            img_pil = Image.fromarray(frame_rgb)
            
            # حفظ الفريم فقط إذا كان مطلوباً (مع تقليل الحجم للسرعة)
            if save_frames:
                # تقليل حجم الصورة للسرعة
                img_pil_resized = img_pil.resize((640, 480), Image.Resampling.LANCZOS)
                original_name = f"original_{fake.random_int(10)}.jpg"
                original_path = os.path.join(original_dir, original_name)
                img_pil_resized.save(original_path, quality=85, optimize=True)

            boxes, probs = self.mtcnn.detect(img_pil)
            if boxes is None:
                continue

            face_predictions = []
            for box_idx, (box, prob) in enumerate(zip(boxes, probs)):
                if prob < 0.9:
                    continue

                box = [int(b) for b in box]
                face = img_pil.crop(box)
                label, conf = self.classify_face(face)

                # حفظ الوجه المقصوص فقط إذا كان مطلوباً (مع تقليل الحجم)
                if save_frames:
                    # تقليل حجم الوجه للسرعة
                    face_resized = face.resize((224, 224), Image.Resampling.LANCZOS)
                    cropped_name = f"cropped_{fake.random_int(10)}.jpg"
                    cropped_path = os.path.join(cropped_dir, cropped_name)
                    face_resized.save(cropped_path, quality=85, optimize=True)

                face_predictions.append((label, conf))
                
                # إضافة تفاصيل الفريم فقط إذا كان مطلوباً
                if save_frames:
                    frame_images.append({
                        "original_path": f"storage/analysis_frames/{analysis_id}/original/{original_name}",
                        "cropped_face_path": f"storage/analysis_frames/{analysis_id}/cropped/{cropped_name}",
                        "prediction": label,
                        "confidence": float(conf)
                    })

            if face_predictions:
                frames_with_faces += 1
                main_label, main_conf = max(face_predictions, key=lambda x: x[1])
                prediction_votes.append(main_label)
                if main_label == "REAL":
                    real_count += 1
                    real_conf_sum += main_conf
                else:
                    fake_count += 1
                    fake_conf_sum += main_conf

        cap.release()

        if not prediction_votes:
            return {"type": "video", "prediction": "UNKNOWN", "confidence": 0.0, "details": {"reason": "No valid frames analyzed"}}

        final_prediction = "REAL" if real_count > fake_count else "FAKE"
        if real_count == fake_count:
            final_prediction = "REAL" if real_conf_sum > fake_conf_sum else "FAKE"

        final_conf = (real_conf_sum / real_count) if final_prediction == "REAL" and real_count else (fake_conf_sum / fake_count if fake_count else 0.0)

        return {
            "type": "video",
            "prediction": final_prediction,
            "confidence": round(final_conf, 2),
            "details": {
                "frames_analyzed": analyzed_frames,
                "frames_with_faces": frames_with_faces,
                "real_frames": real_count,
                "fake_frames": fake_count,
                "average_real_confidence": round(real_conf_sum / real_count, 2) if real_count else 0.0,
                "average_fake_confidence": round(fake_conf_sum / fake_count, 2) if fake_count else 0.0,
                "frame_images": frame_images if save_frames else [],
                "model": "EfficientNet-B4"
            }
        }
