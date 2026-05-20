<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ordonnance extends Model
{
    use HasFactory;

    protected $table = 'ordonnance';
    protected $primaryKey = 'id_ordonnance';
    public $timestamps = false;

    protected $fillable = [
        'id_consultation',
        'date',
        'medicaments',
        'posologie'
    ];

    public function consultation()
    {
        return $this->belongsTo(Consultation::class, 'id_consultation');
    }
}
