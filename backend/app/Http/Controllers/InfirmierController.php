<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class InfirmierController extends Controller
{
    public function getConsultationsDuJour()
    {
        // On récupère les rendez-vous du jour en faisant le lien avec le patient
        $rendez_vous = DB::select("
            SELECT r.id_rdv, r.date_rdv, r.heure, r.motif, r.statut, 
                   p.nom, p.prenom
            FROM rendez_vous r
            JOIN patient p ON r.id_patient = p.id_patient
            WHERE r.date_rdv = CURDATE()
        ");

        return response()->json([
            'success' => true,
            'consultations' => $rendez_vous
        ]);
    }
}
