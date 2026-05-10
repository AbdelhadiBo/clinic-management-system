<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\InfirmierController;
use App\Http\Controllers\AuthController;


Route::post('/admin/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->prefix('admin')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // --- PATIENTS ---
    Route::get('/patients', [AdminController::class, 'getPatients']);
    Route::post('/patients', [AdminController::class, 'addPatient']);
    Route::put('/patients/{id}', [AdminController::class, 'updatePatient']);
    Route::delete('/patients/{id}', [AdminController::class, 'deletePatient']);

    // --- MEDECINS ---
    Route::get('/medecins', [AdminController::class, 'getMedecins']);
    Route::post('/medecins', [AdminController::class, 'addMedecin']);
    Route::put('/medecins/{id}', [AdminController::class, 'updateMedecin']);
    Route::delete('/medecins/{id}', [AdminController::class, 'deleteMedecin']);

    // --- SECRETAIRES ---
    Route::get('/secretaires', [AdminController::class, 'getSecretaires']);
    Route::post('/secretaires', [AdminController::class, 'addSecretaire']);
    Route::put('/secretaires/{id}', [AdminController::class, 'updateSecretaire']);
    Route::delete('/secretaires/{id}', [AdminController::class, 'deleteSecretaire']);

    // --- INFIRMIERS ---
    Route::get('/infirmiers', [AdminController::class, 'getInfirmiers']);
    Route::post('/infirmiers', [AdminController::class, 'addInfirmier']);
    Route::put('/infirmiers/{id}', [AdminController::class, 'updateInfirmier']);
    Route::delete('/infirmiers/{id}', [AdminController::class, 'deleteInfirmier']);

    // --- RENDEZ-VOUS ---
    Route::get('/rendez-vous', [AdminController::class, 'getRendezVous']);
    Route::post('/rendez-vous', [AdminController::class, 'addRendezVous']);
    Route::put('/rendez-vous/{id}', [AdminController::class, 'updateRendezVous']);
    Route::delete('/rendez-vous/{id}', [AdminController::class, 'deleteRendezVous']);
    Route::put('/rendez-vous/{id}/status', [AdminController::class, 'updateRendezVousStatus']);

    // --- CONSULTATIONS ---
    Route::get('/consultations', [AdminController::class, 'getConsultations']);
    Route::post('/consultations', [AdminController::class, 'addConsultation']);

    // --- FACTURES ---
    Route::get('/factures', [AdminController::class, 'getFactures']);
    Route::post('/factures', [AdminController::class, 'addFacture']);
    Route::put('/factures/{id}', [AdminController::class, 'updateFacture']);
    Route::delete('/factures/{id}', [AdminController::class, 'deleteFacture']);

    // --- DASHBOARD ---
    Route::get('/dashboard', [AdminController::class, 'getDashboardStats']);
});


// --- ROUTES POUR L'INFIRMIER (RAED) ---
Route::get('/infirmier/consultations', [InfirmierController::class, 'getConsultationsDuJour']);
