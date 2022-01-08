<?php

namespace mmerlijn\patient\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Validator;
use mmerlijn\laravelHelpers\Casts\Initials;
use mmerlijn\laravelHelpers\Casts\Phone;
use mmerlijn\patient\Database\Factories\RequesterFactory;

class Requester extends Model
{

    use HasFactory, Notifiable, SoftDeletes, AddressTrait, FormatTrait;

    protected $casts = [
        'initials' => Initials::class,
        'relations' => 'array',
        'deleted_at' => 'date',
        'phone' => Phone::class,
        'mobile' => Phone::class,
        'fax' => Phone::class,
        'labels' => 'array',
    ];
    protected $fillable = [
        'agbcode',
        'initials',
        'prefix',
        'lastname',
        'postcode',
        'city',
        'street',
        'building_nr',
        'phone',
        'fax',
        'mobile',
        'postbus',
        'extra_address',
        'relations',
        'labels',
        'email',
    ];

    protected static function newFactory()
    {
        return RequesterFactory::new();
    }

    public function patients()
    {
        return $this->hasMany(Patient::class, 'last_requester', 'agbcode');
    }

    public function scopeRelatedRequesters($query, $agbcode)
    {
        return $query->whereJsonContains('relations', $agbcode);
    }

    public function scopeFiltered($query, $filter)
    {
        if ($filter['agbcode']) {
            $query = $query->relatedRequesters($filter['agbcode']);
        }
        if ($filter('name')) {
            $query = $query->whereLastname($filter['name']);
        }
        if ($filter(['city'])) {
            $query = $query->whereCity($filter['city']);
        }
        return $query;
    }


    public function getNameAttribute()
    {
        return $this->initials . " " .
            trim($this->own_prefix . " " . $this->own_lastname);
    }

    public function getListItemAttribute()
    {
        return $this->lastname . ", " . trim($this->initials . " " . $this->prefix . " (" . $this->city . ")");
    }

    public static function findOrCreate(string $agbcode, string $lastname, ?string $sex = null, ?string $postbus, ?string $postcode = null, ?string $building_nr = null, ?string $street = null, ?string $city = null, ?string $prefix = null, ?string $initials = null, ?array $labels = null, ?string $phone = null, ?string $mobile = null, ?string $fax = null, ?string $email = null, bool $update = true): Patient
    {
        $v = Validator::make([
            'agbcode' => $agbcode,
        ], [
            'agbcode' => 'required|size:8',
        ]);
        $v->validate();
        $requesterArray = ['agbcode' => $agbcode, 'sex' => $sex, 'initials' => $initials, 'lastname' => $lastname, 'prefix' => $prefix, 'postcode' => $postcode, 'postbus' => $postbus, 'building_nr' => $building_nr, 'street' => $street, 'city' => $city, 'labels' => $labels, 'phone' => $phone, 'fax' => $fax, 'mobile' => $mobile, 'email' => $email];
        return Patient::updateOrCreate(['bsn' => $agbcode], static::reformatInput($requesterArray));
    }
}