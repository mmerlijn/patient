<?php

namespace mmerlijn\patient\Observers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use mmerlijn\patient\Models\Patient;
use mmerlijn\patient\Models\Requester;

class PatientObserver
{
    use NameObserverTrait, PhoneObserverTrait;

    /**
     * Handle the patient "created" event.
     *
     * @param \mmerlijn\patient\Models\Patient $patient
     * @return void
     */
    public function creating(Patient $patient)
    {
        $tmp = $this->nameSplitter($patient->lastname, $patient->prefix);
        $patient->lastname = $tmp['lastname'];
        $patient->prefix = $tmp['prefix'];
        $tmp = $this->nameSplitter($patient->own_lastname, $patient->own_prefix);
        $patient->own_lastname = $tmp['lastname'];
        $patient->own_prefix = $tmp['prefix'];
        $patient->phone = $this->phone($patient->phone, $patient->city);
        $patient->phone2 = $this->phone($patient->phone2, $patient->city);
        if (!$patient->phone && $patient->phone2) {
            $patient->phone = $patient->phone2;
            $patient->phone2 = null;
        }
    }

    /**
     * Handle the patient "created" event.
     *
     * @param \mmerlijn\patient\Models\Patient $patient
     * @return void
     */
    public function updating(Patient $patient)
    {
        if ($patient->isDirty('lastname') or $patient->isDirty('prefix')) {
            $tmp = $this->nameSplitter($patient->lastname, $patient->prefix);
            $patient->lastname = $tmp['lastname'];
            $patient->prefix = $tmp['prefix'];
        }
        if ($patient->isDirty('own_lastname') or $patient->isDirty('own_prefix')) {
            $tmp = $this->nameSplitter($patient->own_lastname, $patient->own_prefix);
            $patient->own_lastname = $tmp['lastname'];
            $patient->own_prefix = $tmp['prefix'];
        }
        if ($patient->isDirty('phone')) {
            $patient->phone = $this->phone($patient->phone, $patient->city);
        }
        if ($patient->isDirty('phone2')) {
            $patient->phone2 = $this->phone($patient->phone2, $patient->city);
        }
        if ($patient->phone == $patient->phone2) {
            $patient->phone2 = null;
        }
    }

    public function updated(Patient $patient)
    {
        $cols = ['sex', 'initials', 'lastname', 'own_lastname', 'own_prefix', 'prefix', 'bsn', 'postcode',
            'city', 'street', 'building_nr', 'last_requester', 'phone', 'phone2', 'uzovi', 'policy_nr',
            'lbsnr', 'labels', 'email'];

        $field_changed = [];
        foreach ($cols as $col) {
            if ($patient->isDirty($col)) {
                if ($patient->getAttributes()[$col] != $patient->getRawOriginal($col)) {
                    $field_changed[$col] = [
                        'new' => $patient->getAttributes()[$col],
                        'old' => $patient->getRawOriginal($col)
                    ];
                }

            }
        }
        if ($patient->isDirty('dob')) {
            $field_changed['dob'] = [
                'new' => $patient->dob->format('Y-m-d'),
                'old' => $patient->getOriginal('dob')->format('Y-m-d')
            ];
        }
        if (count($field_changed)) {
            $u = $patient->action->changes ?? [];
            array_push($u, [
                'date' => Carbon::now()->format('Y-m-d H:i:s'),
                'by' => Auth::id() ?? 500,
                'fields' => $field_changed,
            ]);
            $patient->action->changes = $u;
        }
    }
}