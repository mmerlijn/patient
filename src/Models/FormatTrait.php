<?php

namespace mmerlijn\patient\Models;

trait FormatTrait
{
    private static function reformatInput($data): array
    {
        foreach ($data as $k => $v) {
            if ($v === null) {
                unset($data[$k]);
            } elseif ($v === "") {
                $data[$k] = null;
            } else {
                if (in_array($k, ['initials', 'lastname', 'prefix', 'own_lastname', 'own_prefix', 'street', 'city'])) {
                    $data[$k] = trim(preg_replace('[\.\*{\*wx\*}+]', '', trim($v, "-")));
                }
            }
        }
        return $data;
    }
}