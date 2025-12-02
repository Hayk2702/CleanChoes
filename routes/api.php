<?php

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => '/', 'middleware' => 'blockcors'], function () {

    Route::group(['middleware' => ['loggable']], function () {

        Route::get('getContacts', [ApiController::class, 'getContacts'])->name("getContacts");

        Route::get('getCategories', [ApiController::class, 'getCategories'])->name("getCategories");

        Route::get('getServicesByCategories', [ApiController::class, 'getServicesByCategories'])->name("getServicesByCategories");

        Route::get('getOurWorks', [ApiController::class, 'getOurWorks'])->name("getOurWorks");

        Route::get('getWorkPhotos', [ApiController::class, 'getWorkPhotos'])->name("getWorkPhotos");

        Route::get('getWorkPhotosByCategories', [ApiController::class, 'getWorkPhotosByCategories'])->name("getWorkPhotosByCategories");

        Route::post('createReview', [ApiController::class, 'createReview'])->name("createReview");

        Route::get('getReviews', [ApiController::class, 'getReviews'])->name("getReviews");

        Route::post('sendContact', [ApiController::class, 'sendContact'])->name("sendContact");



    });
});


