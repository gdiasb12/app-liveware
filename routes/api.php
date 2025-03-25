<?php

use App\Http\Controllers\API\Activities\GetGoogleFitActivitiesController;

Route::middleware(['auth'])->group(function () {
});

Route::get('api/get-google-fit-activities', GetGoogleFitActivitiesController::class)->name('api.get_googlefit_activities');
