<?php

use App\Models\Rimotatsu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/{rimotatsu}/vote', function (Request $request, Rimotatsu $rimotatsu) {
    $user = $request->user();
    if (!is_null($user)) {
        $user->makeHidden('email', 'email_verified_at', 'created_at', 'updated_at');
    }
    return view('vote', compact('rimotatsu', 'user'));
});
