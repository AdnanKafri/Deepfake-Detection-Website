<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Analysis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        // Get statistics
        $stats = $this->getStats();
        
        // Get recent analyses
        $recentAnalyses = Analysis::with('user')
            ->latest()
            ->take(5)
            ->get();
            
        // Get recent users
        $recentUsers = User::latest()
            ->take(10)
            ->get();
            
        // Get analyses by type
        $analysesByType = Analysis::select('file_type', DB::raw('count(*) as count'))
            ->groupBy('file_type')
            ->get();
            
        // Get analyses by status
        $analysesByStatus = Analysis::select('prediction', DB::raw('count(*) as count'))
            ->groupBy('prediction')
            ->get();

        // Get reported analyses
        $reportedAnalyses = Analysis::with('user')
            ->where('report_flag', true)
            ->latest()
            ->take(5)
            ->get();

        // Get analyses with feedback
        $feedbackAnalyses = Analysis::with('user')
            ->whereNotNull('user_feedback')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentAnalyses',
            'recentUsers',
            'analysesByType',
            'analysesByStatus',
            'reportedAnalyses',
            'feedbackAnalyses'
        ));
    }

    /**
     * Show all users
     */
    public function users()
    {
        $users = User::withCount('analyses')
            ->withSum('analyses', 'confidence')
            ->latest()
            ->paginate(20);

        return view('admin.users', compact('users'));
    }

    /**
     * Show all analyses
     */
    public function analyses(Request $request)
    {
        $query = Analysis::with('user');
        $manualSearch = false;

        // Manual search by id, email, or name
        if ($request->filled('search')) {
            $search = $request->input('search');
            $manualSearch = true;
            $query->where(function($q) use ($search) {
                $q->orWhere('id', $search)
                  ->orWhere('file_name', 'like', "%$search%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('email', 'like', "%$search%")
                         ->orWhere('name', 'like', "%$search%") ;
                  });
            });
        }

        // Filter by file type
        if ($request->filled('file_type')) {
            $query->where('file_type', $request->file_type);
        }

        // Filter by prediction result
        if ($request->filled('prediction')) {
            $query->where('prediction', $request->prediction);
        }

        // Filter by type (for reported/evaluated only)
        if ($request->has('filter') && !$manualSearch) {
            switch ($request->filter) {
                case 'reported':
                    $query->where('report_flag', true);
                    break;
                case 'feedback':
                    $query->whereNotNull('user_feedback');
                    break;
                case 'correct_feedback':
                    $query->where('user_feedback', 'CORRECT');
                    break;
                case 'incorrect_feedback':
                    $query->where('user_feedback', 'INCORRECT');
                    break;
            }
        }

        $analyses = $query->latest()->paginate(20);

        // Get counts for filters
        $totalAnalyses = Analysis::count();
        $reportedCount = Analysis::where('report_flag', true)->count();
        $feedbackCount = Analysis::whereNotNull('user_feedback')->count();
        $correctFeedbackCount = Analysis::where('user_feedback', 'CORRECT')->count();
        $incorrectFeedbackCount = Analysis::where('user_feedback', 'INCORRECT')->count();

        $filterCounts = [
            'total' => $totalAnalyses,
            'reported' => $reportedCount,
            'feedback' => $feedbackCount,
            'correct_feedback' => $correctFeedbackCount,
            'incorrect_feedback' => $incorrectFeedbackCount,
        ];

        return view('admin.analyses', compact('analyses', 'filterCounts', 'manualSearch'));
    }

    /**
     * Show user details
     */
    public function userDetails($id)
    {
        $user = User::with('analyses')->findOrFail($id);
        
        return view('admin.user-details', compact('user'));
    }

    /**
     * Show analysis details
     */
    public function analysisDetails($id)
    {
        $analysis = Analysis::with('user', 'details')->findOrFail($id);
        return view('admin.analysis-details', compact('analysis'));
    }

    /**
     * Update user role
     */
    public function updateUserRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:user,admin'
        ]);

        $user = User::findOrFail($id);
        $user->update(['role' => $request->role]);

        return back()->with('success', 'تم تحديث دور المستخدم بنجاح');
    }

    /**
     * Delete user
     */
    public function deleteUser($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Don't allow admin to delete themselves
            if ($user->id === auth()->id()) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'لا يمكنك حذف حسابك الخاص'
                    ], 400);
                }
                return back()->with('error', 'لا يمكنك حذف حسابك الخاص');
            }
            
            // Delete user's analyses first
            $user->analyses()->delete();
            
            // Delete user
            $user->delete();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم حذف المستخدم بنجاح'
                ]);
            }

            return back()->with('success', 'تم حذف المستخدم بنجاح');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء حذف المستخدم'
                ], 500);
            }
            return back()->with('error', 'حدث خطأ أثناء حذف المستخدم');
        }
    }

    /**
     * Delete analysis
     */
    public function deleteAnalysis($id)
    {
        try {
            $analysis = Analysis::findOrFail($id);
            $analysis->delete();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم حذف التحليل بنجاح'
                ]);
            }

            return back()->with('success', 'تم حذف التحليل بنجاح');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء حذف التحليل'
                ], 500);
            }
            return back()->with('error', 'حدث خطأ أثناء حذف التحليل');
        }
    }

    /**
     * Get statistics
     */
    private function getStats()
    {
        $totalUsers = User::count();
        $totalAnalyses = Analysis::count();
        $realAnalyses = Analysis::where('prediction', 'REAL')->count();
        $fakeAnalyses = Analysis::count() - $realAnalyses;
        
        // Today's statistics
        $todayUsers = User::whereDate('created_at', Carbon::today())->count();
        $todayAnalyses = Analysis::whereDate('created_at', Carbon::today())->count();
        
        // This month's statistics
        $monthUsers = User::whereMonth('created_at', Carbon::now()->month)->count();
        $monthAnalyses = Analysis::whereMonth('created_at', Carbon::now()->month)->count();
        
        // Average confidence
        $avgConfidence = Analysis::avg('confidence') * 100;
        
        // Reports and feedback statistics
        $reportedAnalyses = Analysis::where('report_flag', true)->count();
        $correctFeedback = Analysis::where('user_feedback', 'CORRECT')->count();
        $incorrectFeedback = Analysis::where('user_feedback', 'INCORRECT')->count();
        $totalFeedback = $correctFeedback + $incorrectFeedback;
        
        return [
            'total_users' => $totalUsers,
            'total_analyses' => $totalAnalyses,
            'real_analyses' => $realAnalyses,
            'fake_analyses' => $fakeAnalyses,
            'today_users' => $todayUsers,
            'today_analyses' => $todayAnalyses,
            'month_users' => $monthUsers,
            'month_analyses' => $monthAnalyses,
            'avg_confidence' => round($avgConfidence, 2),
            'reported_analyses' => $reportedAnalyses,
            'correct_feedback' => $correctFeedback,
            'incorrect_feedback' => $incorrectFeedback,
            'total_feedback' => $totalFeedback
        ];
    }
}
