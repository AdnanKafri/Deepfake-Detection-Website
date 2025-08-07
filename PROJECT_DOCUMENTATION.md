# Deepfake Detection Website - Technical Documentation
# موقع لكشف التزييف العميق - التوثيق التقني

---

## Table of Contents - فهرس المحتويات

1. [Introduction - مقدمة](#introduction)
2. [System Architecture - هيكل النظام](#system-architecture)
3. [Technologies & Libraries - التقنيات والمكتبات](#technologies--libraries)
4. [User Permissions & Privacy - صلاحيات المستخدمين والخصوصية](#user-permissions--privacy)
5. [Component Overview - نظرة عامة على المكونات](#component-overview)
6. [Data Flow - تدفق البيانات](#data-flow)
7. [AI/ML Implementation - تنفيذ الذكاء الاصطناعي](#aiml-implementation)
8. [User Interface - واجهة المستخدم](#user-interface)
9. [Evaluation & Reporting System - نظام التقييم والإبلاغ](#evaluation--reporting-system)
10. [PDF Report Generation - توليد تقارير PDF](#pdf-report-generation)
11. [File Relationships - العلاقات بين الملفات](#file-relationships)
12. [Use Cases - حالات الاستخدام](#use-cases)
13. [Error Handling - معالجة الأخطاء](#error-handling)
14. [API Reference - مرجع API](#api-reference)

---

## Important Updates & Clarifications - تحديثات وتوضيحات هامة

### 1. Audio Model Decision Rules - قواعد القرار لنموذج الصوت

- **Confidence Threshold - عتبة الثقة:**
  - Threshold for classifying audio segment as "high-confidence fake": **0.75**
  - If fake probability > 0.75, segment is classified as fake with high confidence
- **Segment Count Rule - قاعدة عدد المقاطع:**
  - If **3 or more segments** are classified as "high-confidence fake", the entire audio file is considered fake
- **Configurability - قابلية التخصيص:**
  - These values are **hardcoded** in the current implementation
  - Can be modified programmatically if needed
- **Validation - التحقق:**
  - Rules are based on initial observations and practical experiments
  - Recommended to update based on real data and broader testing

### 2. Privacy Policy & Analysis Deletion - سياسة الخصوصية وحذف التحليلات

- **Analysis Retention - مدة حفظ التحليلات:**
  - Analyses are stored in database **until manually deleted by user**
  - No automatic deletion after report generation or time period
- **Analysis Deletion - حذف التحليل:**
  - Users can delete any analysis from their dashboard
  - All related details (AnalysisDetail) are removed from database
- **File Cleanup - تنظيف الملفات:**
  - Associated files (frame images, faces, MFCC images) are deleted from storage
  - Ensures privacy protection and no sensitive data retention
- **Privacy Policy - سياسة الخصوصية:**
  - All analyses are private to the user
  - No access by administrators except for evaluated/reported content
  - Users can delete their data at any time
  - No data retention after deletion

### 3. Queue System & Scalability - نظام الصف والتوسعة

- **Current Implementation - التنفيذ الحالي:**
  - Files are sent **directly** from Laravel to FastAPI via HTTP (synchronous)
  - No queue system or job processing in current version
- **Concurrent Requests - الطلبات المتزامنة:**
  - Multiple large files or concurrent requests may cause performance issues
  - Server resources may be limited with heavy load
- **Future Recommendations - التوصيات المستقبلية:**
  - Implement Laravel Queue/Jobs system for analysis requests
  - Use separate workers to process analyses
  - Distribute load across multiple FastAPI servers
  - Use containers for better scalability
  - Monitor analysis status and notify users upon completion

---

## 1. Introduction - مقدمة

The Deepfake Detection Website is a comprehensive web application that enables users to upload media files (images, videos, audio) for analysis and detection of deepfake content using advanced artificial intelligence techniques. The system provides a user-friendly interface, detailed reports, evaluation mechanisms, and advanced admin management while maintaining strict user privacy.

موقع كشف التزييف العميق هو تطبيق ويب شامل يتيح للمستخدمين رفع ملفات الوسائط (الصور والفيديوهات والصوت) لتحليلها وكشف المحتوى المزيف باستخدام تقنيات الذكاء الاصطناعي المتقدمة. يوفر النظام واجهة سهلة الاستخدام وتقارير مفصلة وآليات تقييم وإدارة متقدمة للأدمن مع الحفاظ على خصوصية المستخدمين بشكل صارم.

---

## 2. System Architecture - هيكل النظام

```
Deepfake Detection Website/
├── laravel-app/              # Laravel Frontend Application
│   ├── app/
│   │   ├── Http/Controllers/ # Request handling logic
│   │   ├── Services/         # Business logic services
│   │   ├── Models/           # Database models
│   │   └── Policies/         # Authorization policies
│   ├── resources/views/      # User interface templates
│   ├── routes/               # Application routing
│   └── public/               # Public assets
├── python-api/               # FastAPI Backend
│   ├── core/                 # Core analysis modules
│   │   ├── analyzer.py       # Main analysis engine
│   │   └── audio_analyzer.py # Audio processing
│   ├── models/               # AI model files
│   ├── uploads/              # Temporary file storage
│   └── main.py              # FastAPI application
└── storage/app/public/analysis_frames/  # Shared storage for analysis images
```

---

## 3. Technologies & Libraries - التقنيات والمكتبات

### **Backend (Laravel):**
- Laravel 9+
- PHP 8+
- Eloquent ORM
- DomPDF (PDF generation)
- Laravel Sanctum (API authentication)
- Tailwind CSS (responsive design)
- SweetAlert (modal dialogs)

### **Frontend:**
- Blade Templates (Laravel)
- Tailwind CSS
- JavaScript (interactive features)
- SweetAlert

### **AI/ML (Python - FastAPI):**
- FastAPI (REST API framework)
- PyTorch (EfficientNet-B4)
- facenet-pytorch (MTCNN face detection)
- OpenCV (video processing)
- Pillow (image processing)
- TensorFlow/Keras (BiLSTM for audio)
- librosa (audio processing and feature extraction)
- numpy, matplotlib (analysis and visualization)
- streamlit (model testing interface)

### **Infrastructure:**
- Shared storage between Laravel and FastAPI
- User privacy protection
- Comprehensive error handling

---

## 4. User Permissions & Privacy - صلاحيات المستخدمين والخصوصية

### **Registered User - المستخدم المسجل:**
- Upload and analyze media files (images/video/audio)
- Review all analyses and reports from dashboard
- Download PDF reports for any analysis
- Evaluate analysis results (correct/incorrect)
- Report concerning or sensitive content

### **Guest User - المستخدم الزائر:**
- Upload and analyze one file at a time
- Cannot save or review previous analyses
- Cannot download PDF reports

### **Administrator - المدير:**
- Access only to evaluated or reported content
- Full access to reported content details (including images, frames, audio segments)
- Transfer reported content to relevant authorities
- User and system management

### **Privacy - الخصوصية:**
- All analyses and reports are private to the user
- No administrator access except for evaluated/reported content
- User consent required for evaluation or reporting
- Complete data deletion upon user request

---

## 5. Component Overview - نظرة عامة على المكونات

### **Controllers - المتحكمات:**
- **AnalysisController:** File upload, type detection, AI service communication, result storage
- **DashboardController:** User dashboard, analysis details, evaluation and reporting
- **ReportController:** PDF generation and download, analysis image display
- **AdminController:** User management, analysis oversight, permissions
- **ProfileController:** Account management

### **Services - الخدمات:**
- **DeepfakeDetectionService:** Mediator between Laravel and FastAPI, HTTP communication

### **Models - النماذج:**
- **Analysis:** Represents a single analysis operation (one file)
- **AnalysisDetail:** Details for each file component (frame/face/segment)
- **User:** User data and permissions

### **Views - الواجهات:**
- **analysis/index:** File upload and analysis page
- **dashboard/show:** Detailed analysis report display
- **components/analysis:** Reusable components (general info, technical details, detailed analysis, actions, modals)
- **admin/:** Administrator management pages
- **reports/analysis:** PDF report template
- **auth/:** Authentication pages

### **FastAPI (Python):**
- **analyzer.py:** Image and video analysis (face detection, classification, image saving)
- **audio_analyzer.py:** Audio analysis (segmentation, MFCC feature extraction, classification, jitter/shimmer analysis)
- **main.py:** FastAPI application and endpoints

---

## 6. Data Flow - تدفق البيانات

1. **File Upload:** User uploads file through web interface
2. **Validation & Routing:** AnalysisController validates file type and routes to DeepfakeDetectionService
3. **API Communication:** Service sends file to FastAPI via HTTP POST
4. **AI Analysis:**
   - Image: Face detection and classification
   - Video: Frame selection and analysis
   - Audio: Segmentation and analysis
5. **Result Processing:** FastAPI returns JSON with results and details
6. **Storage & Display:** Laravel stores results in database and displays to user
7. **PDF Generation:** Detailed PDF report generation upon request

---

## 7. AI/ML Implementation - تنفيذ الذكاء الاصطناعي

### **Image Processing - معالجة الصور:**
1. Receive image from Laravel to FastAPI
2. Open image and convert to RGB format
3. Detect all faces using MTCNN (facenet-pytorch)
4. Crop each face and resize to 224x224
5. Convert face to Tensor and normalize according to EfficientNet standards
6. Classify face using EfficientNet-B4 (PyTorch) (REAL/FAKE) with confidence calculation
7. Calculate final result based on majority and confidence
8. Save original and cropped images in shared folder with Laravel
9. Return JSON with details for each face, paths, final result, confidence, face count

### **Video Processing - معالجة الفيديو:**
1. Receive video from Laravel to FastAPI
2. Read video using OpenCV
3. Select specific number of frames distributed across video length (e.g., 10 frames)
4. For each frame: convert to image and apply same image processing steps
5. Save frame images and faces
6. Calculate final result based on frame voting and confidence
7. Return JSON with details for each frame, faces, paths, final result, confidence

### **Audio Processing - معالجة الصوت:**
1. Receive audio file from Laravel to FastAPI
2. Load audio using librosa and convert to fixed frequency (16kHz)
3. Segment audio into short segments (2.5 seconds) using VAD
4. For each segment: extract MFCC, Delta, and Delta-Delta features, plus advanced features (jitter, shimmer, pitch variance)
5. Classify segment using BiLSTM (TensorFlow/Keras)
6. Calculate final result based on fake segment count, confidence, and additional rules (e.g., if 3+ segments are fake with high confidence, consider entire file fake)
7. Return JSON with details for each segment, features, final result, confidence, MFCC images (base64)

---

## 8. User Interface - واجهة المستخدم

### **analysis/index.blade.php**
- File upload and analysis page
- Displays upload form, file type selection, analysis button
- Shows results immediately for guest users

### **dashboard/show.blade.php**
- Detailed analysis report display page
- Sections include:
  - General information (filename, result, confidence, user, date)
  - Technical details (model, analysis type, features, statistics)
  - Detailed analysis (table of frames/faces/segments with images and results)
  - Actions (download PDF, evaluate result, report)
  - Modals (image display, evaluation, reporting)
- Uses components: `general-info`, `technical-details`, `detailed-analysis`, `actions`, `modals`

### **admin/**
- Administrator management pages (users, analyses, details)
- Admin sees only evaluated or reported content
- Can transfer reported content to relevant authorities

### **reports/analysis.blade.php**
- Detailed PDF report template
- Displays all details, images, and results

### **auth/**
- Login, registration, password recovery pages

---

## 9. Evaluation & Reporting System - نظام التقييم والإبلاغ

- **Evaluation:** After analysis results appear, users can evaluate if results are correct or incorrect, helping improve AI accuracy
- **Reporting:** If report contains dangerous or sensitive content, users can report it, reaching administrators who can transfer to relevant authorities
- **Privacy Notice:** During evaluation or reporting, users consent to full report details (including images, frames) being available to site administrators
- **Administrator Access:** Admins only see evaluated or reported content, maintaining user privacy

---

## 10. PDF Report Generation - توليد تقارير PDF

- **ReportController@generate:** Retrieves analysis and details from database
- Converts images (frames/faces) to base64 for PDF display
- Uses DomPDF to convert Blade template (`reports.analysis`) to PDF
- Sends file to user for download with unique name (deepfake_report_{id}.pdf)
- PDF download only available to registered users

---

## 11. File Relationships - العلاقات بين الملفات

- **Controllers ↔ Services:** Controllers call services to send files and analyze them
- **Services ↔ FastAPI:** Services send files via HTTP and receive results
- **Models ↔ Database:** Models represent database tables (analysis, details, user)
- **Views ↔ Controllers:** Views display data from controllers
- **Components:** Reusable components for displaying details, actions, modals

---

## 12. Use Cases - حالات الاستخدام

### Case 1: Image Analysis
1. User uploads image
2. System detects and classifies faces
3. Displays results with face images
4. User can evaluate result or report
5. Can download PDF report if registered

### Case 2: Video Analysis
1. User uploads video
2. System selects multiple frames, analyzes each as image
3. Displays results for each frame with face images
4. Can download detailed PDF report

### Case 3: Audio Analysis
1. User uploads audio file
2. System segments audio, analyzes each segment
3. Displays results for each segment with MFCC image

### Case 4: Administrator Management
1. Admin accesses management panel
2. Sees only evaluated or reported content
3. Can transfer reported content to relevant authorities
4. Reviews user evaluations and reports

---

## 13. Error Handling - معالجة الأخطاء

| Error Case | Description/Cause |
|------------|-------------------|
| File open failure | File corrupted, unreadable, or not found |
| No faces detected | No faces detected in image/video (MTCNN found no faces) |
| Unsupported format | Uploaded file in unsupported format (e.g., PDF or unsupported audio format) |
| FastAPI connection failure | Unable to access AI service (server unavailable or timeout) |
| UNKNOWN cases and reasons | - No valid faces/frames/segments<br>- All results with low confidence<br>- Internal error during analysis<br>- File too short or invalid for analysis |
| Preprocessing error | Failure in converting image/audio/video to required model format |
| File size exceeded | File larger than allowed limit (e.g., > 100MB) |
| Upload error | Connection interruption during upload or storage failure |

**Note:** In all these cases, a clear error message is returned in JSON with failure reason, and `prediction` or `type` field is typically `UNKNOWN` with explanation in `details.reason`.

---

## 14. API Reference - مرجع API

| Endpoint | Method | Description | Response |
|----------|--------|-------------|----------|
| /analyze | POST | Send image/video/audio for analysis | JSON: type, result, confidence, details |
| /health | GET | Check FastAPI service health | JSON: status: ok/error |
| /models | GET | Get information about supported models | JSON: list of models and supported file types |

### Example /analyze response:
```json
{
  "type": "image", // or "video" or "audio"
  "prediction": "REAL", // or "FAKE" or "UNKNOWN"
  "confidence": 0.92,
  "details": {
    "faces_detected": 2,
    "face_details": [ ... ],
    "reason": "No faces detected" // in case of UNKNOWN
  }
}
```

**Notes:**
- All responses in JSON format
- In case of error, `status: error` field returned with explanatory message
- Files must be sent as multipart/form-data
- API can be extended for additional endpoints (user management, analysis history, etc.)

---

## Decision Logic - منطق اتخاذ القرار

### **Image Decision Logic:**
1. **Face Detection:** Use MTCNN to detect all faces
2. **Face Classification:** Each face cropped, resized to 224x224, passed to EfficientNet-B4
3. **Result Aggregation:** Count REAL and FAKE faces, sum confidence for each category
4. **Final Decision:** Majority vote with confidence consideration

### **Video Decision Logic:**
1. **Frame Selection:** Select specific number of frames distributed across video
2. **Frame Analysis:** Each frame treated as image (same logic as images)
3. **Result Aggregation:** Majority voting across frames
4. **Final Decision:** Frame majority determines final result

### **Audio Decision Logic:**
1. **Audio Segmentation:** Segment audio file into short segments (2.5 seconds) using VAD
2. **Segment Analysis:** Extract MFCC features, classify using BiLSTM
3. **Result Aggregation:** Count high-confidence fake segments
4. **Final Decision:** If 3+ segments fake with confidence > 0.75, file is fake

---

**This documentation covers all technical and functional aspects of the project and serves as a comprehensive reference for developers, administrators, and advanced users.** 