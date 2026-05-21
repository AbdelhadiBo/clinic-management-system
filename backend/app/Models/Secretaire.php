<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Secretaire extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'secretaires';

    protected $primaryKey = 'id_secretaire';
    public $timestamps = false;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'mot_de_passe',
    ];

    protected $hidden = [
        'mot_de_passe',
    ];
    protected $casts = [
        'telephone',
        'email'
    ];

    public function rendezVous()
    {
        return $this->hasMany(RendezVous::class, 'id_secretaire');
    }
}
