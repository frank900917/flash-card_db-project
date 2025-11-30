<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return '<script>window.location.replace("http://localhost:3000")</script>';
});
