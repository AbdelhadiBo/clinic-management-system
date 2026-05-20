<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CliniqueSeeder extends Seeder
{
    public function run(): void
    {
        // ==================== PATIENTS ====================
        DB::table('patient')->insert([
            [
                'nom' => 'Benali',
                'prenom' => 'Karim',
                'date_naissance' => '1990-05-15',
                'sexe' => 'Homme',
                'adresse' => 'Alger, Bab El Oued',
                'telephone' => '0555123456',
                'email' => 'karim.benali@email.com',
                'groupe_sanguin' => 'A+'
            ],
            [
                'nom' => 'Amrani',
                'prenom' => 'Fatima',
                'date_naissance' => '1985-03-22',
                'sexe' => 'Femme',
                'adresse' => 'Oran, Es Senia',
                'telephone' => '0555987654',
                'email' => 'fatima.amrani@email.com',
                'groupe_sanguin' => 'O-'
            ],
            [
                'nom' => 'Haddad',
                'prenom' => 'Ahmed',
                'date_naissance' => '1978-11-08',
                'sexe' => 'Homme',
                'adresse' => 'Constantine, El Khroub',
                'telephone' => '0555345678',
                'email' => 'ahmed.haddad@email.com',
                'groupe_sanguin' => 'B+'
            ],
            [
                'nom' => 'Boudiaf',
                'prenom' => 'Sofia',
                'date_naissance' => '1995-07-30',
                'sexe' => 'Femme',
                'adresse' => 'Annaba, Sidi Amar',
                'telephone' => '0555765432',
                'email' => 'sofia.boudiaf@email.com',
                'groupe_sanguin' => 'AB+'
            ],
            [
                'nom' => 'Merad',
                'prenom' => 'Youssef',
                'date_naissance' => '1982-01-12',
                'sexe' => 'Homme',
                'adresse' => 'Tlemcen, Mansourah',
                'telephone' => '0555123987',
                'email' => 'youssef.merad@email.com',
                'groupe_sanguin' => 'O+'
            ]
        ]);

        // ==================== MEDECINS ====================
        DB::table('medecin')->insert([
            [
                'nom' => 'Dr. Khelifi',
                'prenom' => 'Samir',
                'specialite' => 'Cardiologie',
                'telephone' => '0555111111',
                'email' => 'samir.khelifi@clinique.dz',
                'matricule' => 'MED-001'
            ],
            [
                'nom' => 'Dr. Bouzid',
                'prenom' => 'Nadia',
                'specialite' => 'Pédiatrie',
                'telephone' => '0555222222',
                'email' => 'nadia.bouzid@clinique.dz',
                'matricule' => 'MED-002'
            ],
            [
                'nom' => 'Dr. Hamidi',
                'prenom' => 'Rachid',
                'specialite' => 'Chirurgie',
                'telephone' => '0555333333',
                'email' => 'rachid.hamidi@clinique.dz',
                'matricule' => 'MED-003'
            ]
        ]);

        // ==================== SECRETAIRES ====================
        DB::table('secretaire')->insert([
            [
                'nom' => 'Saadi',
                'prenom' => 'Lamia',
                'telephone' => '0555444444',
                'email' => 'lamia.saadi@clinique.dz'
            ],
            [
                'nom' => 'Brahimi',
                'prenom' => 'Amina',
                'telephone' => '0555555555',
                'email' => 'amina.brahimi@clinique.dz'
            ]
        ]);

        // ==================== INFIRMIERS ====================
        DB::table('infirmier')->insert([
            [
                'nom' => 'Touati',
                'prenom' => 'Khaled',
                'telephone' => '0555666666',
                'service' => 'Urgences'
            ],
            [
                'nom' => 'Zeroual',
                'prenom' => 'Nawel',
                'telephone' => '0555777777',
                'service' => 'Chirurgie'
            ],
            [
                'nom' => 'Lounis',
                'prenom' => 'Meriem',
                'telephone' => '0555888888',
                'service' => 'Pédiatrie'
            ]
        ]);

        // ==================== ADMIN ====================
        DB::table('admin')->insert([
            [
                'nom' => 'Admin',
                'prenom' => 'Principal',
                'email' => 'admin@clinique.dz',
                'mot_de_passe' => Hash::make('password123')  // Mot de passe : password123
            ]
        ]);

        // ==================== RENDEZ-VOUS ====================
        DB::table('rendez_vous')->insert([
            [
                'id_patient' => 1,
                'id_medecin' => 1,
                'id_secretaire' => 1,
                'date_rdv' => now()->format('Y-m-d'),
                'heure' => '09:00:00',
                'motif' => 'Consultation cardiaque',
                'statut' => 'en attente'
            ],
            [
                'id_patient' => 2,
                'id_medecin' => 2,
                'id_secretaire' => 1,
                'date_rdv' => now()->format('Y-m-d'),
                'heure' => '10:30:00',
                'motif' => 'Vaccination enfant',
                'statut' => 'confirmé'
            ],
            [
                'id_patient' => 3,
                'id_medecin' => 3,
                'id_secretaire' => 2,
                'date_rdv' => now()->addDay()->format('Y-m-d'),
                'heure' => '14:00:00',
                'motif' => 'Pré-opératoire',
                'statut' => 'en attente'
            ]
        ]);

        // ==================== DOSSIERS MEDICAUX ====================
        DB::table('dossier_medical')->insert([
            [
                'id_patient' => 1,
                'date_creation' => '2024-01-15',
                'antecedents' => 'Hypertension artérielle, Diabète type 2',
                'allergies' => 'Pénicilline, Aspirine'
            ],
            [
                'id_patient' => 2,
                'date_creation' => '2024-03-10',
                'antecedents' => 'Asthme',
                'allergies' => 'Pollen, Poussière'
            ],
            [
                'id_patient' => 3,
                'date_creation' => '2024-06-20',
                'antecedents' => 'Cholécystectomie en 2020',
                'allergies' => 'Iode'
            ]
        ]);

        // ==================== CONSULTATIONS ====================
        DB::table('consultation')->insert([
            [
                'id_rdv' => 1,
                'id_medecin' => 1,
                'id_infirmier' => 1,
                'date' => now()->format('Y-m-d'),
                'diagnostic' => 'Hypertension artérielle non contrôlée',
                'traitement' => 'Amlor 5mg, 1 fois par jour',
                'observations' => 'Surveillance tensionnelle recommandée'
            ],
            [
                'id_rdv' => 2,
                'id_medecin' => 2,
                'id_infirmier' => 3,
                'date' => now()->format('Y-m-d'),
                'diagnostic' => 'Vaccination à jour',
                'traitement' => 'Vaccin DTP',
                'observations' => 'Prochain rendez-vous dans 6 mois'
            ]
        ]);

        // ==================== ORDONNANCES ====================
        DB::table('ordonnance')->insert([
            [
                'id_consultation' => 1,
                'date' => now()->format('Y-m-d'),
                'medicaments' => 'Amlor 5mg, Aspegic 100mg',
                'posologie' => 'Amlor: 1 comprimé le matin, Aspegic: 1 sachet si douleur'
            ],
            [
                'id_consultation' => 2,
                'date' => now()->format('Y-m-d'),
                'medicaments' => 'Vaccin DTP',
                'posologie' => 'Injection intramusculaire, 1 dose'
            ]
        ]);

        // ==================== FACTURES ====================
        DB::table('facture')->insert([
            [
                'id_consultation' => 1,
                'date' => now()->format('Y-m-d'),
                'montant_total' => 2500.00,
                'statut_paiement' => 'payé',
                'mode_paiement' => 'Espèces'
            ],
            [
                'id_consultation' => 2,
                'date' => now()->format('Y-m-d'),
                'montant_total' => 1500.00,
                'statut_paiement' => 'payé',
                'mode_paiement' => 'Carte bancaire'
            ]
        ]);
    }
}
