<?php

namespace App\Http\Controllers;

use App\Models\RendezVous;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RendezVousController extends Controller
{
    // Get all appointments
    public function index()
    {
        $rdvs = RendezVous::with(['patient', 'medecin', 'secretaire'])
            ->orderBy('date_rdv', 'desc')
            ->orderBy('heure', 'asc')
            ->get();

        return response()->json(['data' => $rdvs]);
    }

    // Get single appointment
    public function show($id)
    {
        $rdv = RendezVous::with(['patient', 'medecin', 'secretaire'])->findOrFail($id);
        return response()->json(['data' => $rdv]);
    }

    // Create appointment
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_patient' => 'required|integer|exists:patients,id_patient',
            'id_medecin' => 'required|integer|exists:medecins,id_medecin',
            'date_rdv' => 'required|date',
            'heure' => 'required|date_format:H:i',
            'motif' => 'nullable|string|max:255',
            'statut' => 'nullable|in:en attente,confirmé,annulé,terminé'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $rdv = RendezVous::create([
            'id_patient' => $request->id_patient,
            'id_medecin' => $request->id_medecin,
            'id_secretaire' => auth()->id(),
            'date_rdv' => $request->date_rdv,
            'heure' => $request->heure,
            'motif' => $request->motif,
            'statut' => $request->statut ?? 'en attente'
        ]);

        return response()->json(['data' => $rdv, 'message' => 'Appointment created successfully'], 201);
    }

    // Update appointment
    public function update(Request $request, $id)
    {
        $rdv = RendezVous::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'id_patient' => 'sometimes|integer|exists:patients,id_patient',
            'id_medecin' => 'sometimes|integer|exists:medecins,id_medecin',
            'date_rdv' => 'sometimes|date',
            'heure' => 'sometimes|date_format:H:i',
            'motif' => 'nullable|string|max:255',
            'statut' => 'nullable|in:en attente,confirmé,annulé,terminé'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $rdv->update($request->only([
            'id_patient', 'id_medecin', 'date_rdv', 'heure', 'motif', 'statut'
        ]));

        return response()->json(['data' => $rdv, 'message' => 'Appointment updated successfully']);
    }

    // Delete appointment
    public function destroy($id)
    {
        $rdv = RendezVous::findOrFail($id);
        $rdv->delete();

        return response()->json(['message' => 'Appointment deleted successfully']);
    }

    // Get dashboard stats
    public function dashboardStats()
    {
        $today = now()->toDateString();

        $stats = [
            'total_patients' => \App\Models\Patient::count(),
            'rdv_aujourdhui' => RendezVous::whereDate('date_rdv', $today)->count(),
            'montant_total_factures' => \App\Models\Facture::sum('montant_total') ?? 0,
            'total_medecins' => \App\Models\Medecin::count()
        ];

        return response()->json(['data' => $stats]);
    }
}
