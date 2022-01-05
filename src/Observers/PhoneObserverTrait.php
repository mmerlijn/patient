<?php

namespace mmerlijn\patient\Observers;

trait PhoneObserverTrait
{
    private function phone($phone, $city = false): string
    {

        $value = preg_replace('/[^0-9]/', '', $phone);
        if (!strlen($value)) {
            return "";
        }
        if (strlen($value) < 8 and $city) {
            $value = $this->netnummersLijst($city) . $value;
        } else if (strlen($value) > 10) {
            return preg_replace('/^(0*)(31)/', '0', $value);
        }
        return $value;
    }

    private function netnummersLijst($city)
    {
        switch (strtolower($city)) {

            case "volendam":
            case "edam":
            case "monnickendam":
            case "katwoude":
            case "marken":
            case "purmerend":
            case "purmerland":
            case "wijdewormer":
            case "zuidoostbeemster":
            case "middenbeemster":
            case "noordbeemster":
            case "westbeemster":
            case "kwadijk":
            case "middelie":
            case "warder":
            case "oosthuizen":
            case "beets":
            case "schardam":
            case "hobrede":
            case "purmer":
            case "de rijp":
            case "graft":
            case "noordeinde":
            case "grootschermer":
            case "driehuizen":
                return "0299";
            case "oosterleek" :
            case "hoorn":
            case "oudendijk":
            case "avenhorn":
            case "scharwoude":
            case "spierdijk":
            case "berkhout":
            case "de goorn":
            case "zuidermeer":
            case "benningbroek":
            case "sijbekarspel":
            case "abbekerk":
            case "lambertschaag":
            case "de weere":
            case "oostwoud":
            case "midwoud":
            case "wognum":
            case "nibbixwoud":
            case "zwaag":
            case "hauwert":
            case "blokker":
            case "oosterblokker":
            case "schellinkhout":
            case "aartswoud":
            case "wijdenes":
                return "0229";
            case "amstelveen":
            case "broek in waterland":
            case "badhoevedorp":
            case "amsterdam":
            case "diemen":
            case "den ilp":
            case "duivendrecht":
            case "ilpendam":
            case "landsmeer":
            case "ouderkerk aan de amstel":
            case "oude meer":
            case "ransdorp":
            case "watergang":
            case "halfweg":
            case "uitdam":
            case "zuiderwoude":
            case "zuidoost":
            case "zwanenburg":
                return "020";
            case "castricum":
            case "bakkem":
            case "uitgeest":
            case "akersloot":
            case "beverwijk":
            case "heemskerk":
            case "wijk aan zee":
            case "velzen-noord":
                return "0251";
            case "alkmaar":
            case "egmond aan den hoef":
            case "egmond aan zee":
            case "egmond-binnen":
            case "groet":
            case "heerhugowaard":
            case "heiloo":
            case "limmen":
            case "zuidschermer":
                return "072";
            case "ijmuiden":
                return "0255";
            case "assendelft":
            case "jisp":
            case "koog aan de zaan":
            case "koog a/d zaan":
            case "krommenie":
            case "oostzaan":
            case "west-grafdijk":
            case "westknollendam":
            case "markenbinnen":
            case "de woude":
            case "westzaan":
            case "wormer":
            case "wormerveer":
            case "zaandam":
            case "zaandijk":
            case "zaanstad":
                return "075";

            case "velserbroek":
            case "haarlem":
            case "zandvoort":
            case "overveen":
            case "bloemendaal":
            case "spaarndam-west":
            case "spaarndam":
            case "haarlemmerliede":
            case "santpoort-noord":
            case "santpoort-zuid":
            case "heemstede":
            case "aerdenhout":
            case "vogelenzang":
            case "bentveld":
            case "bennebroek":
            case "hoofddorp":
            case "zwaanshoek":
            case "vijfhuizen":
            case "cruquius":
            case "boesingheliede":
            case "lijnden":
                return "023";
            case "enkhuizen":
            case "venhuizen":
            case "hem":
            case "bovenkarspel":
            case "grootebroek":
            case "lutjebroek":
            case "hoogkarspel":
            case "westwoud":
            case "andijk":
            case "zwaagdijk":
            case "wervershoof":
            case "onderdijk":
                return "0228";
            default:
                return "";
        }
    }
}