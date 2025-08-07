# Deepfake Detection System - مشروع التخرج

An integrated system for detecting manipulated media (images, videos, and audio) using deep learning models. The system consists of a Laravel frontend and a FastAPI backend, providing a user-friendly interface for deepfake detection.

نظام متكامل لكشف الوسائط المحرفة (الصور والفيديوهات والصوت) باستخدام نماذج التعلم العميق. يتكون النظام من واجهة أمامية بلارافيل وخلفية بفاست إي بي آي، مما يوفر واجهة سهلة الاستخدام لكشف التزييف العميق.

## Features - المميزات

- **Multi-modal Analysis - تحليل متعدد الوسائط**
  - Image analysis with face detection using EfficientNet-B4
  - Video analysis with frame sampling
  - Audio analysis with MFCC features using BiLSTM
- **Real-time Processing - معالجة فورية**
  - Fast response times
  - Efficient resource utilization
- **User-friendly Interface - واجهة سهلة الاستخدام**
  - Simple file upload
  - Clear result presentation
  - Detailed analysis information

## System Architecture - هيكل النظام

The system is built with a two-part architecture:

1. **Frontend (Laravel) - الواجهة الأمامية**
   - Port: 8001
   - Handles user interface
   - Manages file uploads
   - Displays analysis results
   - Implements file validation
   - Provides real-time feedback

2. **Backend (FastAPI) - الخلفية**
   - Port: 8000
   - Processes media files
   - Runs deep learning models
   - Returns analysis results
   - Handles file cleanup
   - Provides detailed logging

## Project Structure - هيكل المشروع

```
Graduation Project/
├── laravel-app/              # Laravel Frontend Application
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   └── Services/
│   │   └── Models/
│   ├── resources/
│   │   └── views/
│   ├── routes/
│   └── .env
├── python-api/               # FastAPI Backend
│   ├── core/                 # Core analysis modules
│   ├── models/               # AI model files
│   ├── uploads/              # Temporary file storage
│   ├── main.py              # FastAPI application
│   └── requirements.txt     # Python dependencies
├── resources/                # Additional resources
├── PROJECT_DOCUMENTATION.md  # Detailed documentation
└── README.md                # This file
```

## Quick Start - البدء السريع

### 1. Clone the Repository
```bash
git clone <repository-url>
cd "Graduation Project"
```

### 2. Backend Setup (Python API)
```bash
cd python-api
python -m venv venv
venv\Scripts\activate  # Windows
pip install -r requirements.txt
pip install facenet-pytorch
```

### 3. Frontend Setup (Laravel)
```bash
cd laravel-app
composer install
cp .env.example .env
php artisan key:generate
```

### 4. Run the Application
```bash
# Terminal 1 - Backend
cd python-api
uvicorn main:app --reload --port 8000

# Terminal 2 - Frontend
cd laravel-app
php artisan serve --port 8001

# Terminal 3 - Frontend Assets
cd laravel-app
npm run dev
```

## Detailed Installation - التثبيت المفصل

### Prerequisites - المتطلبات الأساسية
- Python 3.12+
- PHP 8.1+
- Composer
- Node.js & npm
- Git

### Backend Setup - إعداد الخلفية

1. Navigate to the Python API directory:
```bash
cd python-api
```

2. Create and activate a virtual environment:
```bash
# Windows
python -m venv venv
venv\Scripts\activate

# Linux/Mac
python -m venv venv
source venv/bin/activate
```

3. Install dependencies:
```bash
pip install -r requirements.txt
pip install facenet-pytorch
```

### Frontend Setup - إعداد الواجهة الأمامية

1. Navigate to the Laravel directory:
```bash
cd laravel-app
```

2. Install dependencies:
```bash
composer install
npm install
```

3. Configure environment:
```bash
cp .env.example .env
php artisan key:generate
```

4. Update the `.env` file with the correct API URL:
```
API_URL=http://localhost:8000
```

## Usage - الاستخدام

1. Open your browser and go to `http://localhost:8001`
2. Upload an image, video, or audio file
3. Wait for the analysis to complete
4. View the results and confidence scores

## API Endpoints - نقاط النهاية

### POST /analyze
Analyzes uploaded media files for deepfake detection.

**Request:**
- Content-Type: multipart/form-data
- Body: file (image, video, or audio)

**Response:**
```json
{
  "success": true,
  "result": {
    "is_fake": false,
    "confidence": 0.85,
    "analysis_type": "image",
    "details": "..."
  }
}
```

## Contributing - المساهمة

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## License - الترخيص

This project is part of a graduation thesis and is for educational purposes.

## Contact - التواصل

For questions or support, please contact the project maintainer.

---

**Note:** This project requires significant computational resources for optimal performance. A CUDA-capable GPU is recommended for faster processing.
