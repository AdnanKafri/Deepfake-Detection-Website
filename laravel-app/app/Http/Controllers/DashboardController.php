<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Analysis;
use Illuminate\Http\Request;


class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12); // عدد العناصر في الصفحة
        
        // بناء الاستعلام مع البحث
        $query = Analysis::where('user_id', Auth::id());
        
        // البحث في اسم الملف أو رقم التحليل
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('file_name', 'LIKE', "%{$search}%")
                  ->orWhere('id', 'LIKE', "%{$search}%");
            });
        }
        
        // فلترة حسب نوع الملف
        if ($request->filled('file_type')) {
            $query->where('file_type', $request->input('file_type'));
        }
        
        // فلترة حسب النتيجة
        if ($request->filled('prediction')) {
            $query->where('prediction', $request->input('prediction'));
        }
        
        // فلترة حسب الحالة
        if ($request->filled('status')) {
            switch ($request->input('status')) {
                case 'reported':
                    $query->where('report_flag', true);
                    break;
                case 'feedback':
                    $query->whereNotNull('user_feedback');
                    break;
                case 'no_feedback':
                    $query->whereNull('user_feedback')->where('report_flag', false);
                    break;
            }
        }
        
        $analyses = $query->latest()->paginate($perPage);
        
        // إضافة معاملات البحث للـ pagination
        $analyses->appends($request->query());
        
        return view('dashboard', compact('analyses'));
    }

    // DashboardController.php
    public function show($id)
    {
        $analysis = Analysis::findOrFail($id);
        $this->authorize('view', $analysis);
        
        // استخراج البيانات من result_json بشكل صحيح مثل ReportController
        $resultData = json_decode($analysis->result_json, true);
        $mainResult = $resultData['result'] ?? $resultData;
        $details = $mainResult['details'] ?? [];
        
        // استخدام $details كـ $json للتوافق مع analysis.blade.php
        $json = $details;

        return view('dashboard.show', compact('analysis', 'json', 'mainResult'));
    }

    public function destroy($id)
    {
        $analysis = Analysis::findOrFail($id);
        $this->authorize('delete', $analysis);

        // حذف الملف المرتبط إذا كان موجوداً
        if ($analysis->file_path && file_exists(public_path($analysis->file_path))) {
            unlink(public_path($analysis->file_path));
        }

        // حذف التحليل من قاعدة البيانات
        $analysis->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف التحليل بنجاح'
        ]);
    }

    public function report($id)
    {
        $analysis = Analysis::findOrFail($id);
        $this->authorize('ownerActions', $analysis);

        // تفادي التكرار
        if (!$analysis->report_flag) {
            $analysis->report_flag = true;
            $analysis->save();
        }

        return redirect()->back()->with('status', '🚨 تم الإبلاغ عن المحتوى بنجاح.');
    }

    public function feedback(Request $request, $id)
    {
        $request->validate([
            'feedback' => 'required|in:CORRECT,INCORRECT'
        ]);

        $analysis = Analysis::findOrFail($id);
        $this->authorize('ownerActions', $analysis);

        // تأكد ما تم التقييم سابقًا
        if (is_null($analysis->user_feedback)) {
            $analysis->user_feedback = $request->feedback;
            $analysis->save();
            return redirect()->back()->with('status', 'تم تسجيل رأيك بنجاح، شكرًا لك ❤️');
        }

        return redirect()->back()->with('status', 'لقد قمت بتقييم هذا التحليل مسبقًا.');
    }


}
