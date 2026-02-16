<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::attendance.index')->name('attendance');
Route::livewire('/permission', 'pages::permission.index')->name('permission');
Route::livewire('/leave', 'pages::leave.index')->name('leave');
Route::livewire('/account', 'pages::auth.account')->name('account');
