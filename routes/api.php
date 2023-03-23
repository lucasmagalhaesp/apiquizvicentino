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

//Route::delete("questions/{id}", "QuestionsController@destroy");

Route::get("questions/actives", "QuestionsController@actives");
Route::get("questions/{id}/edit", "QuestionsController@edit");
Route::apiResource("questions", "QuestionsController");

//Route::post("users/login", "UsersController@login");

Route::group(["middleware" => "api"], function () {
    Route::apiResource("users", "UsersController");

    Route::post("tests/selectTestQuestion", "TestsController@selectTestQuestion");
    Route::get("tests/correctAnswer/{idQuestion}", "TestsController@correctAnswer");
    Route::get("tests/resultText/{numHits}", "TestsController@resultText");
    Route::get("ranking", "TestsController@ranking");
    Route::get("tests/allUserTests/{userId}", "TestsController@allUserTests");
    Route::get("tests/myTests", "TestsController@myTests");
    Route::get("tests/print/{id}", "TestsController@print");
    Route::apiResource("tests", "TestsController"); 
});

Route::group(["middleware" => "api","prefix" => "auth"], function ($router) {
    Route::post("login", "AuthController@login");
    Route::get("logout", "AuthController@logout");
    Route::post("refresh", "AuthController@refresh");
    Route::get("me", "AuthController@me");

    Route::post("forgotPassword", "AuthController@forgotPassword");
    Route::post("resetPassword", "AuthController@resetPassword");
    Route::post("checkSecurityCode", "AuthController@checkSecurityCode");
});

Route::post("contact/send", "ContactController@send");
