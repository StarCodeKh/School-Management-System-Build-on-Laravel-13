<?php

use App\Http\Controllers\AccountsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\Setting;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

// ---------------------------- AUTHENTICATION ------------------------------//
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'login')->name('login');
    Route::post('/login', 'authenticate');
    Route::get('/logout', 'logout')->name('logout');
});

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'register')->name('register');
    Route::post('/register', 'storeUser')->name('register');
});

// ---------------------- APP (login required for everything) ----------------------//
Route::middleware('auth')->group(function () {

    // 1. HOME & SETTING
    Route::controller(HomeController::class)->group(function () {
        Route::get('/home', 'index')->name('home');
        Route::get('user/profile/page', 'userProfile')->name('user/profile/page');
        Route::get('teacher/dashboard', 'teacherDashboardIndex')->name('teacher/dashboard');
        Route::get('student/dashboard', 'studentDashboardIndex')->name('student/dashboard');
    });
    Route::get('setting/page', [Setting::class, 'index'])->name('setting/page');

    // 2. USERS
    Route::controller(UserManagementController::class)->group(function () {
        Route::post('change/password', 'changePassword')->name('change/password');
        Route::get('list/users', 'index')->name('list/users');
        Route::get('view/user/edit/{id}', 'userView');
        Route::post('user/update', 'userUpdate')->name('user/update');
        Route::post('user/delete', 'userDelete')->name('user/delete');
        Route::get('get-users-data', 'getUsersData')->name('get-users-data');
    });

    // 3. STUDENTS
    Route::prefix('student')->controller(StudentController::class)->group(function () {
        Route::get('list', 'student')->name('student/list');
        Route::get('grid', 'studentGrid')->name('student/grid');
        Route::get('add/page', 'studentAdd')->name('student/add/page');
        Route::post('add/save', 'studentSave')->name('student/add/save');
        Route::get('edit/{id}', 'studentEdit');
        Route::post('update', 'studentUpdate')->name('student/update');
        Route::post('delete', 'studentDelete')->name('student/delete');
        Route::get('profile/{id}', 'studentProfile');
    });

    // 4. TEACHERS
    Route::prefix('teacher')->controller(TeacherController::class)->group(function () {
        Route::get('add/page', 'teacherAdd')->name('teacher/add/page');
        Route::get('list/page', 'teacherList')->name('teacher/list/page');
        Route::get('grid/page', 'teacherGrid')->name('teacher/grid/page');
        Route::post('save', 'saveRecord')->name('teacher/save');
        Route::get('edit/{teacher}', 'editRecord');
        Route::post('update', 'updateRecordTeacher')->name('teacher/update');
        Route::post('delete', 'teacherDelete')->name('teacher/delete');
    });

    // 5. DEPARTMENTS
    Route::prefix('department')->controller(DepartmentController::class)->group(function () {
        Route::get('list/page', 'departmentList')->name('department/list/page');
        Route::get('add/page', 'indexDepartment')->name('department/add/page');
        Route::get('edit/{department_id}', 'editDepartment');
        Route::post('save', 'saveRecord')->name('department/save');
        Route::post('update', 'updateRecord')->name('department/update');
        Route::post('delete', 'deleteRecord')->name('department/delete');
        Route::get('get-data-list', 'getDataList')->name('get-data-list');
    });

    // 6. SUBJECTS
    Route::prefix('subject')->controller(SubjectController::class)->group(function () {
        Route::get('list/page', 'subjectList')->name('subject/list/page');
        Route::get('add/page', 'subjectAdd')->name('subject/add/page');
        Route::post('save', 'saveRecord')->name('subject/save');
        Route::get('edit/{subject_id}', 'subjectEdit');
        Route::post('update', 'updateRecord')->name('subject/update');
        Route::post('delete', 'deleteRecord')->name('subject/delete');
    });

    // 7. INVOICES
    Route::prefix('invoice')->controller(InvoiceController::class)->group(function () {
        Route::get('list/page', 'invoiceList')->name('invoice/list/page');
        Route::get('paid/page', 'invoicePaid')->name('invoice/paid/page');
        Route::get('overdue/page', 'invoiceOverdue')->name('invoice/overdue/page');
        Route::get('draft/page', 'invoiceDraft')->name('invoice/draft/page');
        Route::get('recurring/page', 'invoiceRecurring')->name('invoice/recurring/page');
        Route::get('cancelled/page', 'invoiceCancelled')->name('invoice/cancelled/page');
        Route::get('grid/page', 'invoiceGrid')->name('invoice/grid/page');
        Route::get('add/page', 'invoiceAdd')->name('invoice/add/page');
        Route::post('add/save', 'saveRecord')->name('invoice/add/save');
        Route::get('edit/{invoice}', 'invoiceEdit')->name('invoice/edit/page');
        Route::post('update/save', 'updateRecord')->name('invoice/update/save');
        Route::post('delete', 'deleteRecord')->name('invoice/delete');
        Route::get('view/{invoice}', 'invoiceView')->name('invoice/view/page');
        Route::get('settings/page', 'invoiceSettings')->name('invoice/settings/page');
        Route::get('settings/tax/page', 'invoiceSettingsTax')->name('invoice/settings/tax/page');
        Route::get('settings/bank/page', 'invoiceSettingsBank')->name('invoice/settings/bank/page');
    });

    // 8. ACCOUNTS
    Route::controller(AccountsController::class)->group(function () {
        Route::get('account/fees/collections/page', 'index')->name('account/fees/collections/page');
        Route::get('add/fees/collection/page', 'addFeesCollection')->name('add/fees/collection/page');
        Route::post('fees/collection/save', 'saveRecord')->name('fees/collection/save');
    });
});
