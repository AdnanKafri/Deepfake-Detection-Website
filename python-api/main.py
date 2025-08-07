from fastapi import FastAPI, UploadFile, File, Form, BackgroundTasks
from fastapi.middleware.cors import CORSMiddleware
from fastapi.responses import JSONResponse
import os
import shutil
import uuid
import requests
from core.analyzer import DeepfakeAnalyzer
import time
import threading

# إعدادات FastAPI مع timeout أطول
app = FastAPI(
    title="Deepfake Detection API",
    description="API for detecting deepfake media",
    version="1.0.0",
    docs_url="/docs",
    redoc_url="/redoc"
)

# CORS إعدادات
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# مُحلل التزييف العميق
analyzer = DeepfakeAnalyzer()

# مجلد رفع الملفات
UPLOAD_DIR = "uploaded"
os.makedirs(UPLOAD_DIR, exist_ok=True)

# --- Background cleanup thread for temp_frames/ ---
TEMP_FRAMES_DIR = "temp_frames"
CLEANUP_INTERVAL = 600  # seconds (10 minutes)
FOLDER_MAX_AGE = 1800   # seconds (30 minutes)

TEMP_UPLOADS_DIR = "temp_uploads"
os.makedirs(TEMP_UPLOADS_DIR, exist_ok=True)

def cleanup_old_temp_folders():
    while True:
        now = time.time()
        if os.path.exists(TEMP_FRAMES_DIR):
            for folder in os.listdir(TEMP_FRAMES_DIR):
                folder_path = os.path.join(TEMP_FRAMES_DIR, folder)
                if os.path.isdir(folder_path):
                    try:
                        mtime = os.path.getmtime(folder_path)
                        if now - mtime > FOLDER_MAX_AGE:
                            shutil.rmtree(folder_path)
                    except Exception:
                        pass
        time.sleep(CLEANUP_INTERVAL)

threading.Thread(target=cleanup_old_temp_folders, daemon=True).start()

def run_analysis_and_callback(temp_path: str, media_type: str, task_id: str):
    """
    This function runs in the background. It performs the analysis,
    sends the result to the Laravel callback URL, and cleans up the temp file.
    """
    try:
        if media_type == "image":
            result = analyzer.analyze_image(temp_path)
        elif media_type == "video":
            result = analyzer.analyze_video(temp_path)
        elif media_type == "audio":
            result = analyzer.analyze_audio_segments(temp_path)
        else:
            result = {"status": "error", "message": "Unsupported file type"}

        # Get callback URL from environment variable, with a default for local dev
        callback_base_url = os.getenv("LARAVEL_CALLBACK_URL", "http://host.docker.internal:8000/api")
        callback_url = f"{callback_base_url}/analysis-callback/{task_id}"
        
        # Send the result back to Laravel
        requests.post(callback_url, json=result, timeout=60)

    except Exception as e:
        # If analysis fails, try to notify Laravel anyway
        try:
            callback_base_url = os.getenv("LARAVEL_CALLBACK_URL", "http://host.docker.internal:8000/api")
            callback_url = f"{callback_base_url}/analysis-callback/{task_id}"
            error_payload = {"status": "error", "message": f"Analysis failed: {str(e)}"}
            requests.post(callback_url, json=error_payload, timeout=60)
        except Exception:
            pass # Silently fail if callback fails
    finally:
        # Clean up the temporary file
        if os.path.exists(temp_path):
            os.remove(temp_path)

@app.post("/analyze-async/{media_type}")
async def analyze_file_async(
    media_type: str,
    background_tasks: BackgroundTasks,
    task_id: str = Form(...),
    file: UploadFile = File(...)
):
    try:
        file_ext = file.filename.split(".")[-1]
        temp_filename = f"{task_id}_{uuid.uuid4()}.{file_ext}"
        temp_path = os.path.join(TEMP_UPLOADS_DIR, temp_filename)

        with open(temp_path, "wb") as buffer:
            shutil.copyfileobj(file.file, buffer)

        # Add the long-running analysis to the background
        background_tasks.add_task(
            run_analysis_and_callback, temp_path, media_type, task_id
        )

        # Immediately return 202 Accepted with the task ID
        return JSONResponse(
            status_code=202,
            content={"status": "processing", "task_id": task_id}
        )

    except Exception as e:
        return JSONResponse(status_code=500, content={"status": "error", "message": f"Failed to start analysis: {str(e)}"})

@app.post("/analyze")
async def analyze_file(
    file: UploadFile = File(...), 
    file_type: str = Form(...),
    frames_to_sample: int = Form(5),
    save_frames: bool = Form(False)
):
    try:
        file_ext = file.filename.split(".")[-1]
        temp_path = os.path.join(UPLOAD_DIR, f"temp_input.{file_ext}")

        with open(temp_path, "wb") as buffer:
            shutil.copyfileobj(file.file, buffer)

        if file_type == "image":
            result = analyzer.analyze_image(temp_path)
        elif file_type == "video":
            result = analyzer.analyze_video(temp_path, frames_to_sample, save_frames)
        elif file_type == "audio":
            result = analyzer.analyze_audio_segments(temp_path)
        else:
            return JSONResponse(status_code=400, content={"status": "error", "message": "نوع الملف غير مدعوم"})

        os.remove(temp_path)
        return {"status": "success", "result": result}

    except Exception as e:
        return JSONResponse(status_code=500, content={"status": "error", "message": f"Failed to analyze file: {str(e)}"})
from core.audio_extractor import AudioExtractor

AUDIO_TEMP_DIR = "temp_audio"
os.makedirs(AUDIO_TEMP_DIR, exist_ok=True)

def background_audio_extract(temp_video_path, output_dir, audio_format, task_id):
    try:
        audio_path = AudioExtractor.extract_audio(temp_video_path, output_dir, audio_format)
        audio_filename = os.path.basename(audio_path)
        result = {
            "status": "success",
            "task_id": task_id,
            "audio_url": f"/uploads/{audio_filename}",
            "format": audio_format
        }
    except Exception as e:
        result = {
            "status": "error",
            "task_id": task_id,
            "error": str(e)
        }
    finally:
        if os.path.exists(temp_video_path):
            os.remove(temp_video_path)

        try: 
            callback_url = "http://127.0.0.1:8000/api/audio-extract-callback"
            print(f"[AUDIO CALLBACK] Sending to {callback_url} with result: {result}")
            resp = requests.post(callback_url, json=result, timeout=30)
            print(f"[AUDIO CALLBACK] Response: {resp.status_code} {resp.text}")
        except Exception as e:
            print(f"[AUDIO CALLBACK] Failed to send callback: {e}")

@app.post("/extract-audio")
async def extract_audio_sync(file: UploadFile = File(...), audio_format: str = Form("mp3")):
    try:
        temp_filename = f"{uuid.uuid4().hex[:12]}_{file.filename}"
        temp_path = os.path.join(AUDIO_TEMP_DIR, temp_filename)

        with open(temp_path, "wb") as f:
            f.write(await file.read())

        audio_path = AudioExtractor.extract_audio(temp_path, UPLOAD_DIR, audio_format)
        audio_filename = os.path.basename(audio_path)

        # نسخ الملف إلى مجلد لارافيل
        laravel_uploads = os.path.abspath("../laravel-app/public/uploads/")
        os.makedirs(laravel_uploads, exist_ok=True)
        import shutil
        shutil.copy(audio_path, os.path.join(laravel_uploads, audio_filename))

        result = {
            "status": "success",
            "audio_url": f"/uploads/{audio_filename}",
            "format": audio_format
        }

        os.remove(temp_path)
        return JSONResponse(status_code=200, content=result)

    except Exception as e:
        return JSONResponse(status_code=500, content={"status": "error", "error": str(e)})
