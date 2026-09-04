<?php

use App\Http\Controllers\LogoutController;
use App\Livewire\Admin\Dinas\DinasIndex;
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
use App\Livewire\Mpn\PerencanaanForm;
use App\Livewire\Mpn\KonteksIndex as MpnKonteksIndex;
use App\Livewire\Mpn\PengetahuanIndex;
use App\Livewire\Mpn\PengumpulanForm;
use App\Livewire\Mpn\PemanfaatanForm;
use Illuminate\Support\Facades\Route;

// ─── Guest Routes ─────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

// ─── Authenticated Routes ─────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    // Redirect home based on role
    Route::get('/', function () {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.review.index');
        }
        return redirect()->route('layanan.index');
    })->name('home');

    Route::post('/logout', [LogoutController::class, 'destroy'])->name('logout');

    // Deskripsi Layanan
    Route::get('/layanan/baru', \App\Livewire\Layanan\LayananForm::class)->name('layanan.create');
    
    Route::middleware('has_layanan')->group(function () {
        Route::get('/layanan', \App\Livewire\Layanan\LayananIndex::class)->name('layanan.index');
        Route::get('/layanan/{layanan}/edit', \App\Livewire\Layanan\LayananForm::class)->name('layanan.edit');
        Route::get('/layanan/{layanan}/manajemen', Dashboard::class)->name('layanan.dashboard');
        
        // --- Existing Dashboard route fallback for compatibility (optional, will be deprecated) ---
        Route::get('/dashboard', function() {
            return redirect()->route('layanan.index');
        })->name('dashboard');
    });

    // Modul Manajemen Risiko (Konteks & Formulir)
    Route::prefix('manajemen-risiko')->group(function () {
        Route::get('/', KonteksIndex::class)->name('konteks.index');

        Route::prefix('konteks/{konteks}')->group(function () {
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

    // Modul Manajemen Pengetahuan (MPN)
    Route::prefix('manajemen-pengetahuan')->group(function () {
        Route::get('/', MpnKonteksIndex::class)->name('mpn.index');

        Route::prefix('konteks/{konteks}')->group(function () {
            Route::get('/perencanaan', PerencanaanForm::class)->name('mpn.perencanaan');
            
            // Phase 3 & 4 routes
            Route::get('/pengetahuan', PengetahuanIndex::class)->name('mpn.pengetahuan.index');
            Route::get('/pengetahuan/{pengetahuan}/pengumpulan', PengumpulanForm::class)->name('mpn.pengumpulan.form');
            Route::get('/pengetahuan/{pengetahuan}/pemanfaatan', PemanfaatanForm::class)->name('mpn.pemanfaatan.form');
        });
    });

    // ─── Admin Only Routes ────────────────────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/review', ReviewIndex::class)->name('review.index');
        Route::get('/review/{konteks}', ReviewDetail::class)->name('review.detail');
        Route::get('/dinas', DinasIndex::class)->name('dinas.index');
        Route::get('/user', UserIndex::class)->name('user.index');
    });
});
