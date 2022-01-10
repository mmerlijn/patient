<?php

namespace mmerlijn\patient\Models;

class AddressModel
{
    public ?string $building_nr;
    public ?string $building_addition;

    public function __construct(
        public string $postcode,
        public string $building,
        public string $street,
        public string $city,
    )
    {
        $this->building_nr = preg_replace('/^(\d+)(.*)/', '$1', $this->building);
        $this->building_addition = trim(preg_replace('/^(\d+)(.*)/', '$2', $this->building));
        $this->building = trim($this->building_nr . " " . $this->building_addition);
    }

    public function toArray(): array
    {
        return [
            'postcode' => $this->postcode,
            'building' => $this->building,
            'city' => $this->city,
            'street' => $this->street,
            'building_nr' => $this->building_nr,
            'building_addition' => $this->building_addition,
        ];
    }

    public function __toString(): string
    {
        return $this->street . " " . $this->building . "\n" . $this->postcode . " " . $this->city;
    }
}