<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\PointController;
use App\Http\Controllers\MaterialController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::resource('meetings', MeetingController::class);

Route::get('/meetings/{id}', [MeetingController::class, 'getMeetingById'])
    ->name('meetings.getMeetingById');

// Route::get('/meetings/{meeting}/points', [MeetingController::class, 'getMeetingPoints'])
// ->name('meetings.points.index');

Route::get('/meetings/{meetingId}/top-level-points', [MeetingController::class, 'getTopLevelPoints'])
    ->name('meetings.top-level-points.index');


Route::get('/get-subpoints/{meetingId}/{pointId}', [PointController::class, 'getSubpoints'])
    ->name('get-subpoints');
    

Route::get('/meetings/without-points', [MeetingController::class, 'getMeetingsWithoutPoints']);

Route::get('/points/{id}', [PointController::class, 'show'])
    ->name('points');

Route::put('/points/{id}', [PointController::class, 'update']);

Route::delete('/points/{id}', [PointController::class, 'destroy']);

Route::post('/points/{parentId}/subpoints', [PointController::class, 'createSubpoint']);

Route::post('/points/{point_id}/upload-material', [MaterialController::class, 'uploadForPoint']);


Route::delete('/api/materials/{materialId}', [MaterialController::class, 'deleteMaterial']);

Route::put('/meetings/{meetingId}', [MeetingController::class, 'update']);

// Route::get('/api/meetings/all', [MeetingController::class, 'index']);








Route::resource('points', PointController::class);

Route::resource('materials', MaterialController::class);

Route::post('/meetings/{meeting}/add-point', [PointController::class, 'addPoint'])
    ->name('meetings.points.add');

Route::post('/meetings/{meeting}/points/{point}/upload-material', [PointController::class, 'uploadMaterial'])
    ->name('meetings.points.uploadMaterial');

Route::post('/meetings/{meeting}/points/{point}/add-subpoint', [PointController::class, 'addSubpoint'])
->name('meetings.points.addSubpoint');





