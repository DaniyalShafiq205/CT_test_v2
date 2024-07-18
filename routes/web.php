<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [ProductController::class, 'index']);         // Route to display the products list
Route::post('/products', [ProductController::class, 'store']); // Route to store a new product
Route::put('/products/{id}', [ProductController::class, 'update']); // Route to update an existing product
Route::delete('/products/{id}', [ProductController::class, 'destroy']); // Route to delete an existing product
