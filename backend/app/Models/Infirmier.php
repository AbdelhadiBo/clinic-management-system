<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Infirmier extends Model
{
    use HasFactory;

    protected $table = 'infirmier';
    protected $primaryKey = 'id_infirmier';
    public $timestamps = false;

    protected $fillable = [
        'nom',
        'prenom',
        'telephone',
        'service'
    ];

    public function consultations()
    {
        return $this->hasMany(Consultation::class, 'id_infirmier');
    }
}
