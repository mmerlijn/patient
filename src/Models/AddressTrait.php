<?php

namespace mmerlijn\patient\Models;

trait AddressTrait
{
    public function getAddressAttribute(): AddressModel
    {
        return new AddressModel(
            postcode: $this->postcode,
            city: $this->city,
            street: $this->street,
            building_nr: $this->building_nr
        );
    }
}