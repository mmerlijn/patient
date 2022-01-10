<?php

namespace mmerlijn\patient\Models;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use mmerlijn\laravelHelpers\Casts\Initials;
use mmerlijn\laravelHelpers\Casts\Phone;
use mmerlijn\laravelHelpers\Rules\Bsn;
use mmerlijn\patient\Database\Factories\PatientFactory;

class Patient extends Model
{
    use HasFactory, Notifiable, AddressTrait, FormatTrait;

    protected $dates = [
        'dob'
    ];
    protected $casts = [
        'dob' => 'date:Y-m-d',
        'initials' => Initials::class,
        'phone' => Phone::class,
        'phone2' => Phone::class,
        'labels' => 'array',
    ];
    protected $fillable = [
        'sex',
        'initials',
        'own_lastname',
        'lastname',
        'own_prefix',
        'prefix',
        'dob',
        'postcode',
        'building',
        'street',
        'city',
        'uzovi',
        'policy_nr',
        'bsn',
        'lbsnr',
        'email',
        'last_requester',
        'phone',
        'phone2',
        'labels',

    ];

    protected static function newFactory()
    {
        return PatientFactory::new();
    }

    public function action()
    {
        return $this->hasOne(PatientAction::class, 'id', 'id');
    }

    public function requester()
    {
        return $this->belongsTo(Requester::class, 'agbcode', 'last_requester')->withDefault([
            'name' => 'Niet bekend',
            'agbcode' => '00000000',
        ]);
    }

    public function scopeFiltered($query, $filter)
    {
        if ($filter['requester'] ?? false) {
            $query = $query->whereLastRequester($filter['requester']);
        }
        if ($filter['name'] ?? false) {
            $query = $query->where(fn($q) => $q->where('lastname', 'like', $filter['name'] . "%")->orWhere('own_lastname', 'like', $filter['name'] . '%'));
        }
        if ($filter['city'] ?? false) {
            $query = $query->whereCity($filter['city']);
        }
        if ($filter['postcode'] ?? false) {
            $query = $query->wherePostcode($filter['postcode']);
        }
        if ($filter['email'] ?? false) {
            $query = $query->whereEmail($filter['email']);
        }
        if ($filter['dob'] ?? false) {
            try {
                $date = Carbon::parse($filter['dob']);
            } catch (InvalidFormatException $e) {
                $date = false;
            }
            if ($date) {
                $query = $query->whereDob($date->format('Y-m-d'));
            }
        }
        if ($filter['bsn'] ?? false) {
            try {
                $validator = Validator::make($filter, ['bsn' => [new Bsn]]);
                if ($validator->validate()) {
                    $query = $query->whereBsn($filter['bsn']);
                }
            } catch (\Exception $e) {
            }


        }
        return $query;
    }

    public static function findOrCreate(string $sex, string $own_lastname, Carbon|string $dob, string $postcode, string $building, string $street, string $city, ?string $bsn = null, ?string $lastname = null, ?string $prefix = null, ?string $own_prefix = null, ?string $initials = null, ?string $uzovi = null, ?string $policy_nr = null, ?array $labels = null, ?string $last_requester = null, ?string $lbsnr = null, ?string $phone = null, ?string $phone2 = null, ?string $email = null, bool $update = true): Patient
    {
        $dob = ($dob instanceof Carbon) ? $dob->format('Y-m-d') : $dob;
        $v = Validator::make([
            'bsn' => $bsn,
            'dob' => $dob,
            'own_lastname' => $own_lastname,
            'sex' => $sex,
        ], [
            'bsn' => [new Bsn],
            'dob' => 'required|date',
            'own_lastname' => 'required|min:1',
            'sex' => [Rule::in(['M', "F", "O", "m", "f", "o"])],
        ]);
        $v->validate();
        $patientArray = ['bsn' => $bsn, 'sex' => strtoupper($sex), 'dob' => $dob, 'initials' => $initials, 'lastname' => $lastname, 'prefix' => $prefix, 'own_lastname' => $own_lastname, 'own_prefix' => $own_prefix, 'postcode' => $postcode, 'building' => $building, 'street' => $street, 'city' => $city, 'uzovi' => $uzovi, 'policy_nr' => $policy_nr, 'labels' => $labels, 'lbsnr' => $lbsnr, 'last_requester' => $last_requester, 'phone' => $phone, 'phone2' => $phone2, 'email' => $email];
        if ($bsn) {
            return Patient::updateOrCreate(['bsn' => $bsn], static::reformatInput($patientArray));
        } else {
            return Patient::updateOrCreate([
                'dob' => $dob->format('Y-m-d'), 'sex' => $sex, 'own_lastname' => $own_lastname, 'postcode' => $postcode, 'bsn' => $bsn
            ],
                static::reformatInput($patientArray)
            );
        }
    }

    public function addNote(string $note): void
    {
        $n = (array)$this->action->notes;
        array_push($n, [
            'date' => now()->format('Y-m-d H:i:s'),
            'by' => Auth::id() ?? 500,
            'note' => $note,
        ]);
        $this->action->notes = $n;
        $this->action->save();
    }

    public function rmNote(string $date): void
    {
        $n = $this->action->notes;
        foreach ($n as $k => $item) {
            if ($item['date'] == $date) {
                unset($n[$k]);
            }
        }
        $this->action->notes = $n;
        $this->action->save();
    }

    public function addAction(string $type, string $subject, mixed $details)
    {
        $n = (array)$this->action->actions;
        $n[$type][] = [
            'date' => now()->format('Y-m-d H:i:s'),
            'subject' => $subject,
            'details' => $details
        ];
        $this->action->notes = $n;
        $this->action->save();
    }


    public function getNameAttribute()
    {
        return $this->initials . " " . trim(
                ($this->lastname ? trim($this->prefix . " " . $this->lastname) : "") . " " .
                trim($this->own_prefix . " " . $this->own_lastname)
            );
    }

    protected function salutation(): Attribute
    {
        return new Attribute(
            get: function ($value, $attributes) {
                $name = match ($attributes['sex']) {
                    "M", "m" => "Dhr. ",
                    "F", "f", "V", "v" => "Mevr. ",
                    default => "",
                };
                return $name . $this->name;
            }
        );
    }


}