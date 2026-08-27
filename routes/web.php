<?php

use App\Http\Controllers\LogoutController;
use App\Livewire\Admin\Desa\DesaIndex;
use App\Livewire\Admin\ReviewDetail;
use App\Livewire\Admin\ReviewIndex;
use App\Livewire\Admin\User\UserIndex;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use App\Livewire\Konteks\KonteksForm;
use App\Livewire\Konteks\KonteksIndex;
use App\Livewire\Pemantauan\PemantauanForm;
use App\Livewire\Risiko\PetaRisiko;
use App\Livewire\Risiko\RisikoForm;
use App\Livewire\Risiko\RisikoIndex;
use App\Livewire\Sasaran\SasaranForm;
use App\Livewire\StrukturPelaksana\StrukturPelaksanaForm;
use Illuminate\Support\Facades\Route;

// ─── Guest Routes ─────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

// ─── Authenticated Routes ─────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    // Dashboard Hub (Launcher 5 Modul)
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::post('/logout', [LogoutController::class, 'destroy'])->name('logout');

    // Modul Manajemen Risiko (Konteks & Formulir)
    Route::prefix('manajemen-risiko')->group(function () {
        Route::get('/', KonteksIndex::class)->name('konteks.index');

        Route::prefix('konteks/{konteks}')->middleware('konteks.access')->group(function () {
            Route::get('/', KonteksForm::class)->name('konteks.form');
            Route::get('/sasaran', SasaranForm::class)->name('sasaran.form');
            Route::get('/struktur', StrukturPelaksanaForm::class)->name('struktur.form');
            Route::get('/risiko', RisikoIndex::class)->name('risiko.index');
            Route::get('/layanan-digital', \App\Livewire\LayananDigital\LayananDigitalIndex::class)->name('layanan-digital.index');
            Route::get('/risiko/{risiko}', RisikoForm::class)->name('risiko.form');
            Route::get('/peta-risiko', PetaRisiko::class)->name('risiko.peta');
            Route::get('/pemantauan', PemantauanForm::class)->name('pemantauan.form');
        });
    });

    // ─── Admin Only Routes ────────────────────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/review', ReviewIndex::class)->name('review.index');
        Route::get('/review/{konteks}', ReviewDetail::class)->name('review.detail');
        Route::get('/desa', DesaIndex::class)->name('desa.index');
        Route::get('/user', UserIndex::class)->name('user.index');
    });
});
