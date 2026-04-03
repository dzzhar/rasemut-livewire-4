<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/login', 'pages::auth.login')->name('login')->middleware('guest');

Route::middleware(['auth', 'role:employee'])->group(function () {
    Route::livewire('/', 'pages::homepage.index')->name('homepage');
    Route::livewire('/history', 'pages::attendance.history')->name('history');
    Route::livewire('/permission', 'pages::permission.index')->name('permission');
    Route::livewire('/leave', 'pages::leave.index')->name('leave');
    Route::livewire('/settings', 'pages::settings.index')->name('settings');
});
