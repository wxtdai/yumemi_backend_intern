<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\TasksController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [LoginController::class, 'authenticate']);

Route::post('/logout', [LoginController::class, 'logout']);

//API中で認証情報を使う方法のサンプル
Route::middleware('auth:web')->group(function () {
    // 全タスク取得
    Route::get('/{rimotatsu}/tasks', [TasksController::class, 'getTasks']);

    // 達成したタスクのidと数を取得
    Route::get('/{rimotatsu}/tasks/achieved', [TasksController::class, 'getAchievedTasks']);

    // タスクを達成する
    Route::post('/{rimotatsu}/tasks/{task}', [TasksController::class, 'achieveTask']);

    // タスクの達成を取り消す
    Route::delete('/{rimotatsu}/tasks/{task}', [TasksController::class, 'undoneTask']);

    // ユーザーが投票できるかどうか確認する
    Route::get('/{rimotatsu}/vote', [VoteController::class, 'checkUserCanVote']);

    // 数字を投票
    Route::post('/{rimotatsu}/vote', [VoteController::class, 'store']);

    // 宝くじの当選者・当選番号取得
    Route::get('/{rimotatsu}/vote/winner', [VoteController::class, 'getWinner']);
});

