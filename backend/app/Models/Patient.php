<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $table = 'patient';        // Nom de ta table MySQL
    protected $primaryKey = 'id_patient'; // Clé primaire
    public $timestamps = false;          // Pas de created_at/updated_at

    protected $fillable = [
        'nom',
        'prenom',
        'date_naissance',
        'sexe',
        'adresse',
        'telephone',
        'email',
        'groupe_sanguin'
    ];

    // Relation : un patient a un dossier médical
    public function dossierMedical()
    {
        return $this->hasOne(DossierMedical::class, 'id_patient');
    }

    // Relation : un patient a plusieurs rendez-vous
    public function rendezVous()
    {
        return $this->hasMany(RendezVous::class, 'id_patient');
    }
}
