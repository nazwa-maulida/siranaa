<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AuthentificationController;
use App\Http\Controllers\ProyekController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\RPController;
use App\Http\Controllers\RekomendasiController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});



// Routes untuk login, register, dan logout tanpa middleware 'auth'
Route::get('/login/{id?}', [AuthentificationController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthentificationController::class, 'login']);
Route::get('register', [AuthentificationController::class, 'showRegisterForm'])->name('register');
Route::post('register', [AuthentificationController::class, 'register']);
Route::post('logout', [AuthentificationController::class, 'logout'])->name('logout');
Route::get('role-selection/{id}', [AuthentificationController::class, 'showRoleSelectionForm'])->name('auth.role-selection');
Route::post('set-role/{id}', [AuthentificationController::class, 'setRole'])->name('auth.set-role');

Route::get('/register/mitra/{id}', [AuthentificationController::class, 'showMitraForm'])->name('mitra.form');
Route::post('/register/mitra/{id}', [AuthentificationController::class, 'submitMitra'])->name('mitra.submit');

//form untuk perusahaan
Route::get('/perusahaan/form/{id}', [AuthentificationController::class, 'showPerusahaanForm'])->name('perusahaan.form');
Route::post('/perusahaan/submit/{id}', [AuthentificationController::class, 'submitPerusahaan'])->name('perusahaan.submit');

//route untuk home
Route::get('home', function () {
    return view('home'); 
})->name('home');

//route untuk resource controller (dengan middelware auth)
//proyeks
Route::resource('proyeks', ProyekController::class)->middleware('auth');
Route::get('/proyeks/{id}/ambil', [ProyekController::class, 'ambilProyek'])->name('ambilProyek');
Route::get('/proyeks/{id}', [ProyekController::class, 'show'])->name('proyeks.show');
Route::get('/proyek/{id}/lanjutkan', [ProyekController::class, 'lanjutkan'])->name('proyek.lanjutkan');
Route::get('/proyek/{id}/tdkselesai', [ProyekController::class, 'tdkselesai'])->name('proyek.tdkselesai');


//surveys
Route::resource('surveys', SurveyController::class)->middleware('auth');
Route::get('surveys/create/{ProyekID}', [SurveyController::class, 'create'])->name('surveys.create');

Route::resource('realisasi_proyeks', RPController::class)->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::resource('rekomendasis', RekomendasiController::class);
    Route::get('rekomendasis/{rekomendasi}/edit', [RekomendasiController::class, 'edit'])->name('rekomendasis.edit');

});


//dashboard berbagai role
Route::get('/admin/dashboard', function () {
    return view('dashboard.admin');
})->name('admin.dashboard')->middleware(['auth', 'admin']);

Route::get('/mitra/dashboard', [AuthentificationController::class, 'dashboardMitra'])
    ->name('mitra.dashboard')
    ->middleware(['auth', 'mitra']);

Route::get('/perusahaan/dashboard', [AuthentificationController::class, 'dashboard'])
    ->name('perusahaan.dashboard')
    ->middleware(['auth', 'perusahaan']);


//notifikasi




// Rute untuk notifikasi
Route::prefix('notifications')->group(function () {
    // Mendapatkan notifikasi untuk mitra
    Route::get('/mitra', [NotificationController::class, 'getNotificationsForMitra']);
    
    // Mendapatkan notifikasi untuk perusahaan
    Route::get('/perusahaan', [NotificationController::class, 'getNotificationsForPerusahaan']);
    
    // Mendapatkan riwayat notifikasi
    Route::get('/history', [NotificationController::class, 'getNotificationHistory']);
    
    // Menghapus satu riwayat notifikasi
    Route::post('/history/delete', [NotificationController::class, 'deleteHistory']);
    
    // Menghapus semua riwayat notifikasi
    Route::post('/history/delete-all', [NotificationController::class, 'deleteAllHistory']);
    
    // Menandai satu notifikasi sebagai dibaca
    Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
    
    // Menandai semua notifikasi sebagai dibaca
    Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
});

// // Routes untuk notifikasi
// Route::middleware(['auth'])->group(function () {
//     // Untuk mitra
//     Route::get('/notifications/mitra', [NotificationController::class, 'getNotificationsForMitra']);
    
//     // Untuk perusahaan
//     Route::get('/notifications/perusahaan', [NotificationController::class, 'getNotificationsForPerusahaan']);
    
//     // Umum
//     Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
//     Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);

//     Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');

//     Route::get('/notifications/history', [NotificationController::class, 'getNotificationHistory']);

    
// });


