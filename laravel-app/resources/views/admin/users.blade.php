@extends('layouts.dashboard')

@section('title', 'إدارة المستخدمين')

@section('content')
<div class="min-h-[90vh] bg-gradient-to-br from-indigo-50 via-purple-50 to-sky-100 flex flex-col" dir="rtl">
    <!-- Header -->
    <section class="w-full max-w-7xl mx-auto text-center mb-8 mt-6">
        <div class="bg-white/90 rounded-3xl shadow-xl p-6 md:p-10 border border-indigo-100">
            <h2 class="text-3xl md:text-4xl font-extrabold text-indigo-800 mb-2 flex items-center justify-center gap-3">
                <i class="fas fa-users text-indigo-600 text-2xl"></i>
                إدارة المستخدمين
            </h2>
            <p class="text-gray-600 text-base md:text-lg mb-6">إدارة حسابات المستخدمين وتعديل صلاحياتهم</p>
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-arrow-right"></i>
                العودة للوحة المدير
            </a>
        </div>
    </section>

    <!-- Users Table -->
    <section class="w-full max-w-7xl mx-auto px-4 mb-8">
        <div class="bg-white rounded-2xl shadow-lg border border-indigo-100 overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-bold text-indigo-800">قائمة المستخدمين ({{ $users->total() }})</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المستخدم</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">البريد الإلكتروني</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الدور</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التحليلات</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ التسجيل</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                    <div class="mr-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">ID: {{ $user->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $user->role == 'admin' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $user->role == 'admin' ? 'مدير' : 'مستخدم' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-chart-bar text-indigo-600"></i>
                                    {{ $user->analyses_count ?? 0 }} تحليل
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $user->created_at->format('d/m/Y') }}
                                <div class="text-xs text-gray-500">{{ $user->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.user.details', $user->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($user->id !== auth()->id())
                                        <!-- Role Update Form -->
                                        <form method="POST" action="{{ route('admin.user.update-role', $user->id) }}" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <select name="role" onchange="this.form.submit()" class="text-xs border border-gray-300 rounded px-2 py-1 {{ $user->role == 'admin' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                                                <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>مستخدم</option>
                                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>مدير</option>
                                            </select>
                                        </form>
                                        
                                        <!-- Delete Form -->
                                        <form method="POST" action="{{ route('admin.user.delete', $user->id) }}" class="inline delete-user-form" data-user-name="{{ $user->name }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 delete-user-btn">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-gray-400 text-xs">حسابك</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                لا يوجد مستخدمين
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $users->links() }}
            </div>
            @endif
        </div>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // SweetAlert للرسائل
    @if(session('status'))
        Swal.fire({
            title: 'تم بنجاح!',
            text: '{{ session('status') }}',
            icon: 'success',
            confirmButtonText: 'حسناً',
            confirmButtonColor: '#10B981'
        });
    @endif
    @if(session('error'))
        Swal.fire({
            title: 'خطأ!',
            text: '{{ session('error') }}',
            icon: 'error',
            confirmButtonText: 'حسناً',
            confirmButtonColor: '#EF4444'
        });
    @endif

    // تأكيد حذف المستخدم
    document.querySelectorAll('.delete-user-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const userName = this.getAttribute('data-user-name');
            
            Swal.fire({
                title: 'تأكيد حذف المستخدم',
                html: `
                    <div class="text-center">
                        <div class="mb-4">
                            <i class="fas fa-exclamation-triangle text-red-500 text-4xl mb-3"></i>
                            <p class="text-lg font-semibold text-gray-800">هل أنت متأكد من حذف المستخدم؟</p>
                        </div>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4">
                            <p class="text-red-800 font-bold mb-1">المستخدم: <span class="text-red-600">${userName}</span></p>
                            <p class="text-red-700 text-sm">سيتم حذف جميع تحليلاته أيضاً ولا يمكن التراجع عن هذا الإجراء!</p>
                        </div>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-trash ml-1"></i> نعم، احذف المستخدم',
                cancelButtonText: '<i class="fas fa-times ml-1"></i> إلغاء',
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'swal2-confirm-delete',
                    cancelButton: 'swal2-cancel-delete'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // إظهار loading
                    Swal.fire({
                        title: 'جاري الحذف...',
                        html: `
                            <div class="text-center">
                                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-red-500 mx-auto mb-4"></div>
                                <p class="text-gray-600">يرجى الانتظار، جاري حذف المستخدم...</p>
                            </div>
                        `,
                        allowOutsideClick: false,
                        showConfirmButton: false
                    });
                    
                    // إرسال النموذج
                    form.submit();
                }
            });
        });
    });
});
</script>

<style>
.swal2-confirm-delete {
    background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%) !important;
    border: none !important;
    box-shadow: 0 4px 14px 0 rgba(239, 68, 68, 0.4) !important;
    transition: all 0.3s ease !important;
}

.swal2-confirm-delete:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 20px 0 rgba(239, 68, 68, 0.6) !important;
}

.swal2-cancel-delete {
    background: linear-gradient(135deg, #6B7280 0%, #4B5563 100%) !important;
    border: none !important;
    box-shadow: 0 4px 14px 0 rgba(107, 114, 128, 0.4) !important;
    transition: all 0.3s ease !important;
}

.swal2-cancel-delete:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 20px 0 rgba(107, 114, 128, 0.6) !important;
}
</style>
@endsection 