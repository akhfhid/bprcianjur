<?php

use App\Http\Controllers\Api\TelegramLinkApiController;
use Illuminate\Http\Request;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
  return $request->user();
});

/*
|--------------------------------------------------------------------------
| Telegram — integrasi bot (Telegram Pulse)
|--------------------------------------------------------------------------
| Dipanggil server bot saat user mengirim /start <TOKEN> ke bot Telegram.
*/
Route::prefix('v1/telegram')->group(function () {
  Route::post('/link-verify', [TelegramLinkApiController::class, 'verify']);
});
