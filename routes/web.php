<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OurWorkController;
use App\Http\Controllers\ReviewsController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkPhotoController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => '{locale?}', 'middleware' => ['multiLang']], function () {

    Route::get('/', function () {
        return redirect(app()->getLocale() . '/dashboard');
    });

    Route::group(['prefix' => 'dashboard', 'middleware' => ['auth']], function () {

        Route::group(['middleware' => ['loggable']], function () {

            Route::resource('/', HomeController::class)->names('dashboard');
            Route::post('/setLocale/{lang}', [HomeController::class, 'setLocale'])->name('setLocale');
            Route::get('abort403', [HomeController::class, 'abort403'])->name('abort403');

            Route::resource('contacts', ContactController::class)->names('contacts');

            Route::get('categories', [CategoriesController::class, 'index'])->name('categories.index');
            Route::post('categories', [CategoriesController::class, 'store'])->name('categories.store');
            Route::delete('categories/{category}', [CategoriesController::class, 'destroy'])->name('categories.destroy');


            Route::get('categories/{category}/services', [ServiceController::class, 'index'])->name('services.index');
            Route::post('services', [ServiceController::class, 'store'])->name('services.store');
            Route::delete('services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');

            Route::get('ourworks', [OurWorkController::class, 'index'])->name('ourworks.index');
            Route::post('ourworks', [OurWorkController::class, 'store'])->name('ourworks.store');
            Route::delete('ourworks/{ourwork}', [OurWorkController::class, 'destroy'])->name('ourworks.destroy');

            Route::get('workphotos', [WorkPhotoController::class, 'index'])->name('workphotos.index');
            Route::get('workphotos/categories', [WorkPhotoController::class, 'categories'])->name('workphotos.categories'); // for select
            Route::post('workphotos', [WorkPhotoController::class, 'store'])->name('workphotos.store');
            Route::delete('workphotos/{workphoto}', [WorkPhotoController::class, 'destroy'])->name('workphotos.destroy');

            Route::get('reviews', [ReviewsController::class, 'index'])->name('reviews.index');
            Route::post('reviews/{review}/toggle', [ReviewsController::class, 'toggle'])->name('reviews.toggle');

        });

    });

    Auth::routes();
});

