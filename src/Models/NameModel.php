<?php

namespace mmerlijn\patient\Models;

class NameModel
{
    public function __construct(
        public ?string $initials = null,
        public ?string $prefix = null,
        public ?string $lastname = null,
        public ?string $own_prefix = null,
        public ?string $own_lastname = null,
        public ?string $sex = null
    )
    {
    }

    public function name()
    {
        return $this->initials . " " .
            ($this->lastname ? trim($this->prefix . " " . $this->lastname) . " " : "") .
            ($this->own_lastname ? trim($this->own_prefix . " " . $this->own_lastname) . " " : "");
    }

    public function __toString(): string
    {
        $name = match ($this->sex) {
            "M", "m" => "Dhr. ",
            "F", "f", "V", "v" => "Mevr. ",
            default => "",
        };
        return $name . $this->name();
    }

}