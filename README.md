# Deepfake Detection Website - موقع لكشف التزييف العميق

A comprehensive web application for detecting manipulated media (images, videos, and audio) using advanced deep learning models. The system provides a user-friendly interface for deepfake detection with real-time analysis capabilities.

موقع ويب شامل لكشف الوسائط المحرفة (الصور والفيديوهات والصوت) باستخدام نماذج التعلم العميق المتقدمة. يوفر النظام واجهة سهلة الاستخدام لكشف التزييف العميق مع إمكانيات التحليل الفوري.

## Features - المميزات

- **Multi-modal Analysis - تحليل متعدد الوسائط**
  - Image analysis with face detection using EfficientNet-B4
  - Video analysis with frame sampling and temporal consistency
  - Audio analysis with MFCC features using BiLSTM
- **Real-time Processing - معالجة فورية**
  - Fast response times for immediate results
  - Efficient resource utilization
  - Progress tracking during analysis
- **User-friendly Interface - واجهة سهلة الاستخدام**
  - Simple drag-and-drop file upload
  - Clear result presentation with confidence scores
  - Detailed analysis information and visualizations
  - Responsive design for all devices

## System Architecture - هيكل النظام

The application is built with a modern two-tier architecture:

1. **Frontend (Laravel) - الواجهة الأمامية**
   - Port: 8001
   - Modern web interface with Tailwind CSS
   - File upload and validation
   - Real-time progress updates
   - Result visualization and reporting
   - User authentication and management

2. **Backend (FastAPI) - الخلفية**
   - Port: 8000
   - RESTful API for media processing
   - Deep learning model integration
   - File handling and cleanup
   - Comprehensive error handling
   - Detailed logging and monitoring

## Project Structure - هيكل المشروع

```
Deepfake Detection Website/
├── laravel-app/              # Laravel Frontend Application
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/  # Application controllers
│   │   │   └── Services/     # Business logic services
│   │   └── Models/           # Database models
│   ├── resources/
│   │   └── views/            # Blade templates
│   ├── routes/               # Application routes
│   └── public/               # Public assets
├── python-api/               # FastAPI Backend
│   ├── core/                 # Core analysis modules
│   │   ├── analyzer.py       # Main analysis engine
│   │   └── audio_analyzer.py # Audio processing
│   ├── models/               # AI model files
│   ├── uploads/              # Temporary file storage
│   └── main.py              # FastAPI application
├── resources/                # Additional resources
└── documentation/            # Project documentation
```

## Quick Start - البدء السريع

### Prerequisites - المتطلبات الأساسية
- Python 3.12+
- PHP 8.1+
- Composer
- Node.js & npm
- Git

### 1. Clone the Repository
```bash
git clone <repository-url>
cd "Deepfake Detection Website"
```

### 2. Backend Setup (Python API)
```bash
cd python-api
python -m venv venv
venv\Scripts\activate  # Windows
# source venv/bin/activate  # Linux/Mac
pip install -r requirements.txt
pip install facenet-pytorch
```

### 3. Frontend Setup (Laravel)
```bash
cd laravel-app
composer install
npm install
cp .env.example .env
php artisan key:generate
```

### 4. Configuration
Update the Laravel `.env` file:
```env
API_URL=http://localhost:8000
APP_URL=http://localhost:8001
```

### 5. Run the Application
```bash
# Terminal 1 - Backend API
cd python-api
uvicorn main:app --reload --port 8000

# Terminal 2 - Frontend Server
cd laravel-app
php artisan serve --port 8001

# Terminal 3 - Frontend Assets (Development)
cd laravel-app
npm run dev
```

## Usage - الاستخدام

1. Open your browser and navigate to `http://localhost:8001`
2. Upload an image, video, or audio file using the file upload interface
3. Wait for the analysis to complete (progress will be shown)
4. View the detailed results including:
   - Detection confidence score
   - Analysis type and methodology
   - Technical details and metadata
   - Visual representations of results

## API Documentation - توثيق API

### POST /analyze
Analyzes uploaded media files for deepfake detection.

**Request:**
- Content-Type: `multipart/form-data`
- Body: `file` (image, video, or audio file)

**Response:**
```json
{
  "success": true,
  "result": {
    "is_fake": false,
    "confidence": 0.85,
    "analysis_type": "image",
    "processing_time": 2.3,
    "details": {
      "faces_detected": 1,
      "model_used": "EfficientNet-B4",
      "features_analyzed": ["texture", "lighting", "artifacts"]
    }
  }
}
```

### GET /health
Health check endpoint for the API service.

## Technical Details - التفاصيل التقنية

### Image Analysis
- **Face Detection**: MTCNN for accurate face localization
- **Classification**: Fine-tuned EfficientNet-B4 model
- **Features**: Texture analysis, lighting consistency, compression artifacts

### Video Analysis
- **Frame Sampling**: Intelligent frame extraction for efficiency
- **Temporal Analysis**: Consistency checking across frames
- **Aggregation**: Weighted voting system for final results

### Audio Analysis
- **Feature Extraction**: MFCC with delta and delta-delta features
- **Classification**: Bidirectional LSTM network
- **Processing**: Real-time audio stream analysis

## Performance - الأداء

- **Image Processing**: 2-5 seconds per image
- **Video Processing**: 10-30 seconds per minute of video
- **Audio Processing**: 5-15 seconds per audio file
- **Concurrent Users**: Supports multiple simultaneous analyses
- **Accuracy**: 95%+ detection rate on benchmark datasets

## Browser Support - دعم المتصفحات

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Development - التطوير

### Running Tests
```bash
# Backend tests
cd python-api
python -m pytest

# Frontend tests
cd laravel-app
php artisan test
```

### Code Style
- Python: PEP 8 compliance
- PHP: PSR-12 standards
- JavaScript: ESLint configuration

---

**Note:** This application requires significant computational resources for optimal performance. A CUDA-capable GPU is recommended for faster processing of large media files.
