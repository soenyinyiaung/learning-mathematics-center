<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TeacherSalaryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SaleItemController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', function () {
    if (Auth::check()) {
        return redirect()->route('admin.dashboard');
    }
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// API Routes
Route::prefix('api')->group(function () {
    Route::apiResource('grades', GradeController::class);
    Route::apiResource('subjects', SubjectController::class);
    Route::apiResource('students', StudentController::class);
    Route::post('students/{id}/toggle-status', [StudentController::class, 'toggleStatus']);
    Route::apiResource('teachers', TeacherController::class);
    Route::post('teachers/{id}/toggle-status', [TeacherController::class, 'toggleStatus']);
    Route::apiResource('teacher-salaries', TeacherSalaryController::class);
    Route::apiResource('expenses', ExpenseController::class);
    Route::apiResource('expense-categories', ExpenseCategoryController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('sale-items', SaleItemController::class);
    Route::apiResource('vouchers', VoucherController::class);
    Route::apiResource('invoices', InvoiceController::class);
    Route::apiResource('payments', PaymentController::class);
    Route::post('/settings/change-password', [SettingController::class, 'changePassword'])->middleware('auth');
});

Route::prefix('admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard')->middleware('auth');
    
    Route::get('/students', function () {
        return view('admin.students', ['title' => 'Students List']);
    })->name('admin.students')->middleware('auth');

    Route::get('/student-fees', function () {
        return view('admin.student-fees', ['title' => 'Student Fees']);
    })->name('admin.student-fees')->middleware('auth');

    Route::get('/student-fees/checkout', function () {
        return view('admin.student-fees.checkout', ['title' => 'Fee Payment']);
    })->name('admin.student-fees.checkout')->middleware('auth');

    Route::get('/teachers', function () {
        return view('admin.teachers', ['title' => 'Teachers List']);
    })->name('admin.teachers')->middleware('auth');

    Route::get('/teacher-salaries', function () {
        return view('admin.teacher-salaries', ['title' => 'Teacher Salaries']);
    })->name('admin.teacher-salaries')->middleware('auth');

    Route::get('/expenses', function () {
        return view('admin.expenses', ['title' => 'Expenses']);
    })->name('admin.expenses')->middleware('auth');

    Route::get('/sales', function () {
        return view('admin.sales', ['title' => 'Sales']);
    })->name('admin.sales')->middleware('auth');

    Route::get('/sales/checkout', function () {
        return view('admin.sales.checkout', ['title' => 'Sales Checkout']);
    })->name('admin.sales.checkout')->middleware('auth');

    Route::get('/vouchers', function () {
        return view('admin.vouchers', ['title' => 'Vouchers']);
    })->name('admin.vouchers')->middleware('auth');

    Route::get('/subjects', function () {
        return view('admin.subjects', ['title' => 'Subjects']);
    })->name('admin.subjects')->middleware('auth');

    Route::get('/grades', function () {
        return view('admin.grades', ['title' => 'Grades']);
    })->name('admin.grades')->middleware('auth');

    Route::get('/users', function () {
        return view('admin.users', ['title' => 'Users']);
    })->name('admin.users')->middleware('auth');

    Route::get('/settings', function () {
        return view('admin.settings', ['title' => 'Settings']);
    })->name('admin.settings')->middleware('auth');
});
