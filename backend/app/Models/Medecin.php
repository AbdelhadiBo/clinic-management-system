<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medecin extends Model
{
    use HasFactory;

    protected $table = 'medecin';
    protected $primaryKey = 'id_medecin';
    public $timestamps = false;

    protected $fillable = [
        'nom',
        'prenom',
        'specialite',
        'telephone',
        'email',
        'matricule'
    ];

    public function rendezVous()
    {
        return $this->hasMany(RendezVous::class, 'id_medecin');
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class, 'id_medecin');
    }
}
