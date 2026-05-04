<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Secretaire extends Model
{
    use HasFactory;

    protected $table = 'secretaire';
    protected $primaryKey = 'id_secretaire';
    public $timestamps = false;

    protected $fillable = [
        'nom',
        'prenom',
        'telephone',
        'email'
    ];

    public function rendezVous()
    {
        return $this->hasMany(RendezVous::class, 'id_secretaire');
    }
}
