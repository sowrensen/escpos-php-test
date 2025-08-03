<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Printer\Index as PrinterIndex;
use App\Livewire\Printer\Create as PrinterCreate;
use App\Livewire\Printer\Edit as PrinterEdit;
use App\Livewire\QrReader\Index as QrReaderIndex;
use Illuminate\Support\Facades\Route;
use Zxing\QrReader;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

Route::redirect('/dashboard', '/printers', 301);

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    // Printer Routes
    Route::get('printers', PrinterIndex::class)->name('printers.index');
    Route::get('printers/create', PrinterCreate::class)->name('printers.create');
    Route::get('printers/{printer}/edit', PrinterEdit::class)->name('printers.edit');

    // QR Code Reader Route
    Route::get('qr-reader', QrReaderIndex::class)->name('qr-reader.index');
});

require __DIR__.'/auth.php';
