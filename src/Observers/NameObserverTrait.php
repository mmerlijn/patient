<?php

namespace mmerlijn\patient\Observers;

trait NameObserverTrait
{
    private function nameSplitter($lastname, $prefix): array
    {
        $prefixes = ['aan', 'af', 'bij', 'de', 'den', 'der', 'd\'', 'het', '\'t', 'in', 'onder', 'op', 'over', '\'s', 'te', 'ten', 'ter', 'tot', 'uit', 'uijt', 'van', 'ver', 'voor',
            'a', 'al', 'am', 'auf', 'aus', 'ben', 'bin', 'da', 'dal', 'dalla', 'della', 'das', 'die', 'den', 'der', 'des', 'deca', 'degli', 'dei', 'del', 'di', 'do', 'don', 'dos', 'du', 'el',
            'i', 'im', 'l', 'la', 'las', 'le', 'les', 'lo', 'los', 'o\'', 'tho', 'thoe', 'thor', 'toe', 'unter', 'vom', 'von', 'vor', 'zu', 'zum', 'zur'];
        $parts = explode(" ", $lastname);
        $lastname = array_pop($parts);
        foreach ($parts as $part) {
            if (in_array(strtolower($part), $prefixes)) { //is prefix
                $prefix .= " " . strtolower($part);
            } else { //belongs to lastname
                $lastname = $part . " " . $lastname;
            }
        }
        return ['lastname' => $lastname, 'prefix' => trim(implode(" ", array_unique(explode(" ", $prefix))))];
    }
}