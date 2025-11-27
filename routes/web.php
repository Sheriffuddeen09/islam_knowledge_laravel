<?php

use Illuminate\Support\Facades\Route;

use App\Models\User;

Route::get('/all-users', function () {
    return User::all();
});


Route::get('/', function () {
    return view('welcome');
});
