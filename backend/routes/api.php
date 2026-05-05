<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InfirmierController;

Route::get('/infirmier/consultations', [InfirmierController::class, 'getConsultationsDuJour']);