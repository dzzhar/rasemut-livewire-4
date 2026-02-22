<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/login', 'pages::auth.login')->name('login');

Route::middleware(['auth', 'role:employee'])->group(function () {
    Route::livewire('/', 'pages::attendance.index')->name('attendance');
    Route::livewire('/permission', 'pages::permission.index')->name('permission');
    Route::livewire('/leave', 'pages::leave.index')->name('leave');
    Route::livewire('/profile', 'pages::account.profile')->name('profile');
    Route::livewire('/password', 'pages::account.password')->name('password');
});
