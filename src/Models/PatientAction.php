<?php

namespace mmerlijn\patient\Models;

use Illuminate\Database\Eloquent\Model;

class PatientAction extends Model
{
    protected $table = "patient_actions";

    public function patient()
    {
        return $this->hasOne(Patient::class, 'id', 'id');
    }
}