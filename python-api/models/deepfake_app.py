import streamlit as st
import torch
import numpy as np
from PIL import Image
import cv2
from facenet_pytorch import MTCNN
from efficientnet_pytorch import EfficientNet
from torchvision import transforms
from collections import Counter
import tempfile
import os

device = torch.device("cuda" if torch.cuda.is_available() else "cpu")

@st.cache_resource
def load_model():
    model = EfficientNet.from_name('efficientnet-b4')
    model._fc = torch.nn.Linear(model._fc.in_features, 2)
    model.load_state_dict(torch.load("fine_tuned_epoch_25.pt", map_location=device))
    model = model.to(device)
    model.eval()
    return model

model = load_model()

mtcnn = MTCNN(image_size=224, margin=40, keep_all=True, device=device, post_process=False, min_face_size=20).eval()

face_transform = transforms.Compose([
    transforms.Resize((224, 224)),
    transforms.ToTensor(),
    transforms.Normalize(mean=[0.485, 0.456, 0.406],
                         std=[0.229, 0.224, 0.225])
])

def classify_face(face_img_pil):
    face_tensor = face_transform(face_img_pil).unsqueeze(0).to(device)
    with torch.no_grad():
        outputs = model(face_tensor)
        probs = torch.softmax(outputs, dim=1)
        conf_score, pred_class = torch.max(probs, 1)
    label = "REAL" if pred_class.item() == 1 else "FAKE"
    return label, conf_score.item()

def analyze_image(img_pil):
    boxes, probs = mtcnn.detect(img_pil)
    results = []
    if boxes is not None:
        for box, prob in zip(boxes, probs):
            if prob < 0.9: continue
            box = [int(b) for b in box]
            face = img_pil.crop(box)
            label, conf = classify_face(face)
            results.append((box, label, conf))
    return results

def analyze_video(video_path, frames_to_sample=10):
    cap = cv2.VideoCapture(video_path)
    frame_count = int(cap.get(cv2.CAP_PROP_FRAME_COUNT))
    selected_indices = np.linspace(0, frame_count - 1, frames_to_sample).astype(int)
    frame_predictions = []

    for idx in selected_indices:
        cap.set(cv2.CAP_PROP_POS_FRAMES, idx)
        success, frame = cap.read()
        if not success: continue
        frame_rgb = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
        img_pil = Image.fromarray(frame_rgb)
        results = analyze_image(img_pil)
        if results:
            label = max(results, key=lambda x: x[2])[1]
            frame_predictions.append(label)
    cap.release()

    final = "Uncertain"
    if frame_predictions:
        counts = Counter(frame_predictions)
        final = counts.most_common(1)[0][0]
    return final, frame_predictions

# Streamlit UI
st.title("🧠 Deepfake Detection App")

uploaded_file = st.file_uploader("📤 ارفع صورة أو فيديو", type=["jpg", "png", "jpeg", "mp4", "mov", "avi"])

if uploaded_file:
    ext = uploaded_file.name.split(".")[-1].lower()
    is_video = ext in ["mp4", "mov", "avi"]

    with tempfile.NamedTemporaryFile(delete=False, suffix=f".{ext}") as tmp:
        tmp.write(uploaded_file.read())
        tmp_path = tmp.name

    if is_video:
        st.video(tmp_path)
        label, votes = analyze_video(tmp_path)
        st.success(f"القرار النهائي للفيديو: **{label}**")
        st.write(f"نتائج الإطارات: {votes}")
    else:
        image = Image.open(tmp_path).convert("RGB")
        st.image(image, caption="الصورة الأصلية")
        results = analyze_image(image)
        if not results:
            st.warning("لم يتم اكتشاف أي وجه.")
        else:
            for box, label, conf in results:
                st.markdown(f"📌 وجه عند {box} → **{label}** ({conf:.2f})")

    os.remove(tmp_path)
