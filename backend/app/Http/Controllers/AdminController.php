<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Medecin;
use App\Models\Secretaire;
use App\Models\Infirmier;
use App\Models\RendezVous;
use App\Models\Consultation;
use App\Models\Facture;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // ==================== PATIENTS ====================

    public function getPatients()
    {
        $patients = Patient::with('dossierMedical')->get();
        return response()->json([
            'success' => true,
            'data' => $patients
        ]);
    }

    public function addPatient(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'date_naissance' => 'required|date',
            'sexe' => 'required|string|max:10',
            'telephone' => 'required|string|max:20',
            'email' => 'nullable|email|max:100',
            'adresse' => 'nullable|string|max:255',
            'groupe_sanguin' => 'nullable|string|max:5'
        ]);

        $patient = Patient::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Patient ajouté avec succès',
            'data' => $patient
        ], 201);
    }

    public function updatePatient(Request $request, $id)
    {
        try {
            $patient = Patient::findOrFail($id);

            $validated = $request->validate([
                'nom' => 'required|string|max:100',
                'prenom' => 'required|string|max:100',
                'date_naissance' => 'required|date',
                'sexe' => 'required|string|max:10',
                'telephone' => 'required|string|max:20',
                'email' => 'nullable|email|max:100',
                'adresse' => 'nullable|string|max:255',
                'groupe_sanguin' => 'nullable|string|max:5'
            ]);

            $patient->update($validated);

            return response()->json([
                'success' => true,  // ← Ajoute ça
                'message' => 'Patient modifié avec succès',
                'data' => $patient
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deletePatient($id)
    {
        try {
            $patient = Patient::findOrFail($id);
            $patient->delete();

            return response()->json([
                'success' => true,  // ← C'est cette ligne qui manque peut-être
                'message' => 'Patient supprimé avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== MEDECINS ====================

    public function getMedecins()
    {
        $medecins = Medecin::all();
        return response()->json([
            'success' => true,
            'data' => $medecins
        ]);
    }

    public function addMedecin(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'specialite' => 'required|string|max:100',
            'telephone' => 'required|string|max:20',
            'email' => 'nullable|email|max:100',
            'matricule' => 'required|string|max:50|unique:medecin'
        ]);

        $medecin = Medecin::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Médecin ajouté avec succès',
            'data' => $medecin
        ], 201);
    }

    public function updateMedecin(Request $request, $id)
    {
        $medecin = Medecin::findOrFail($id);
        $medecin->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Médecin modifié avec succès',
            'data' => $medecin
        ]);
    }

    public function deleteMedecin($id)
    {
        Medecin::findOrFail($id)->delete();
        return response()->json([
            'success' => true,
            'message' => 'Médecin supprimé avec succès'
        ]);
    }

    // ==================== SECRETAIRES ====================

    public function getSecretaires()
    {
        $secretaires = Secretaire::all();
        return response()->json([
            'success' => true,
            'data' => $secretaires
        ]);
    }

    public function addSecretaire(Request $request)
    {
        $secretaire = Secretaire::create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Secrétaire ajouté avec succès',
            'data' => $secretaire
        ], 201);
    }

    public function updateSecretaire(Request $request, $id)
    {
        $secretaire = Secretaire::findOrFail($id);
        $secretaire->update($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Secrétaire modifié avec succès',
            'data' => $secretaire
        ]);
    }

    public function deleteSecretaire($id)
    {
        Secretaire::findOrFail($id)->delete();
        return response()->json([
            'success' => true,
            'message' => 'Secrétaire supprimé avec succès'
        ]);
    }

    // ==================== INFIRMIERS ====================

    public function getInfirmiers()
    {
        $infirmiers = Infirmier::all();
        return response()->json([
            'success' => true,
            'data' => $infirmiers
        ]);
    }

    public function addInfirmier(Request $request)
    {
        $infirmier = Infirmier::create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Infirmier ajouté avec succès',
            'data' => $infirmier
        ], 201);
    }

    public function updateInfirmier(Request $request, $id)
    {
        $infirmier = Infirmier::findOrFail($id);
        $infirmier->update($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Infirmier modifié avec succès',
            'data' => $infirmier
        ]);
    }

    public function deleteInfirmier($id)
    {
        Infirmier::findOrFail($id)->delete();
        return response()->json([
            'success' => true,
            'message' => 'Infirmier supprimé avec succès'
        ]);
    }

    // ==================== RENDEZ-VOUS ====================

    public function getRendezVous()
    {
        $rdvs = RendezVous::with(['patient', 'medecin', 'secretaire'])->get();
        return response()->json([
            'success' => true,
            'data' => $rdvs
        ]);
    }

    public function addRendezVous(Request $request)
    {
        $rdv = RendezVous::create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Rendez-vous ajouté',
            'data' => $rdv
        ], 201);
    }

    public function updateRendezVous(Request $request, $id)
    {
        $rdv = RendezVous::findOrFail($id);
        $rdv->update($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Rendez-vous modifié',
            'data' => $rdv
        ]);
    }

    public function deleteRendezVous($id)
    {
        RendezVous::findOrFail($id)->delete();
        return response()->json([
            'success' => true,
            'message' => 'Rendez-vous supprimé'
        ]);
    }

    public function updateRendezVousStatus(Request $request, $id)
    {
        $rdv = RendezVous::findOrFail($id);
        $rdv->update(['statut' => $request->statut]);

        return response()->json([
            'success' => true,
            'message' => 'Statut mis à jour',
            'data' => $rdv
        ]);
    }

    // ==================== CONSULTATIONS ====================

    public function getConsultations()
    {
        $consultations = Consultation::with(['rendezVous.patient', 'medecin', 'infirmier', 'facture'])->get();
        return response()->json([
            'success' => true,
            'data' => $consultations
        ]);
    }
    public function addConsultation(Request $request)
    {
        $request->validate([
            'id_rdv' => 'required|integer|exists:rendez_vous,id_rdv',
            'id_medecin' => 'required|integer|exists:medecin,id_medecin',
            'id_infirmier' => 'nullable|integer|exists:infirmier,id_infirmier',
            'date' => 'required|date',
            'diagnostic' => 'nullable|string',
            'traitement' => 'nullable|string',
            'observations' => 'nullable|string'
        ]);

        $consultation = Consultation::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Consultation ajoutée avec succès',
            'data' => $consultation
        ], 201);
    }

    // ==================== FACTURES ====================

    public function getFactures()
    {
        $factures = Facture::with('consultation.rendezVous.patient')->get();
        return response()->json([
            'success' => true,
            'data' => $factures
        ]);
    }

    public function addFacture(Request $request)
    {
        $request->validate([
            'id_consultation' => 'required|integer|exists:consultation,id_consultation',
            'date' => 'required|date',
            'montant_total' => 'required|numeric|min:0',
            'statut_paiement' => 'required|string|in:payé,non payé',
            'mode_paiement' => 'nullable|string'
        ]);

        $facture = Facture::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Facture ajoutée avec succès',
            'data' => $facture
        ], 201);
    }

    public function updateFacture(Request $request, $id)
    {
        $facture = Facture::findOrFail($id);
        $facture->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Facture mise à jour',
            'data' => $facture
        ]);
    }

    public function deleteFacture($id)
    {
        Facture::findOrFail($id)->delete();
        return response()->json([
            'success' => true,
            'message' => 'Facture supprimée avec succès'
        ]);
    }

    // ==================== DASHBOARD ====================

    public function getDashboardStats()
    {
        $stats = [
            'total_patients' => Patient::count(),
            'total_medecins' => Medecin::count(),
            'total_secretaires' => Secretaire::count(),
            'total_infirmiers' => Infirmier::count(),
            'rdv_aujourdhui' => RendezVous::whereDate('date_rdv', today())->count(),
            'rdv_en_attente' => RendezVous::where('statut', 'en attente')->count(),
            'total_factures' => Facture::count(),
            'montant_total_factures' => Facture::sum('montant_total')
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
