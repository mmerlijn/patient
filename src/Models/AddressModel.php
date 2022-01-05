<?php

namespace mmerlijn\patient\Models;

class AddressModel
{
    public ?string $building_nr_full;
    public ?string $building_nr_addition;

    public function __construct(
        public string $postcode,
        public string $building_nr,
        public string $street,
        public string $city,
    )
    {
        $this->expand();
    }

    public function expand(): void
    {
        $building_nr = "";
        $addition = "";
        $building_nr_end = false;
        foreach (str_split($this->building_nr) as $value) {
            if (is_numeric($value) and !$building_nr_end) {
                $building_nr .= $value;
            } else {
                $building_nr_end = true;
            }
            if ($building_nr_end) {
                $addition .= $value;
            }
        }
        $this->building_nr = $building_nr;
        $this->building_nr_addition = preg_replace('/[^a-zA-Z0-9]/i', '', $addition);
        $this->building_nr_full = trim($this->building_nr . " " . $this->building_nr_addition);
    }

    public function toArray(): array
    {
        return [
            'postcode' => $this->postcode,
            'building_nr' => $this->building_nr,
            'city' => $this->city,
            'street' => $this->street,
            'building_nr_full' => $this->building_nr_full,
            'building_nr_addition' => $this->building_nr_addition,
        ];
    }

    public function __toString(): string
    {
        return $this->street . " " . $this->building_nr_full . "\n" . $this->postcode . " " . $this->city;
    }
}