<?php

use Illuminate\Http\Request;

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

/* Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
}); */

Route::get("questions/actives", "QuestionsController@actives");
Route::apiResource("questions", "QuestionsController");

Route::apiResource("users", "UsersController");

Route::post("tests/selectTestQuestion", "TestsController@selectTestQuestion");
Route::get("tests/correctAnswer/{idQuestion}", "TestsController@correctAnswer");
Route::get("tests/ranking", "TestsController@ranking");
Route::get("tests/allUserTests/{userId}", "TestsController@allUserTests");
Route::apiResource("tests", "TestsController");
