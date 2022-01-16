<?php

namespace mmerlijn\patient\Models;

use Illuminate\Database\Eloquent\Model;

class PatientAction extends Model
{
    protected $table = "patient_actions";

    protected $casts = [
        'notes' => 'array',
        'actions' => 'array',
    ];

    public function patient()
    {
        return $this->hasOne(Patient::class, 'id', 'id');
    }
}