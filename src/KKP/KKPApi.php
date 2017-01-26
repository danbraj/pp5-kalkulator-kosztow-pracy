<?php

namespace KKP;

class KKPApi {

    static $aUmowy = array("Umowa zlecenie", "Umowa o dzielo", "Umowa o prace");
    static $isConsole;
    static $pattern;

    static function showProgramDescription($sFileName) {
        echo "\n\tSkladnia:\tphp {$sFileName} [kwota_netto] [typ_umowy] [zapis = false]\n";
        echo "\t\t\tphp {$sFileName} -rekord [id]\n\n";
        echo "\tid\t\tliczba\n";
        echo "\tkwota_netto\tliczba\n";
        echo "\ttyp_umowy\t0 => Umowa zlecenie\n";
        echo "\t\t\t1 => Umowa o dzielo\n";
        echo "\t\t\t2 => Umowa o prace\n";
        echo "\tzapis\t\twartosc logiczna (true, 1, false, 0)\n";
    }

    static function isConsole() {
        if (PHP_SAPI === 'cli') {
            self::$pattern = "Rodzaj umowy: %s\nWartosc netto: %s\nWartosc brutto: %s\nKosz pracodawcy: %s";
            self::$isConsole = true;
            return true;
        }
        else {
            self::$pattern = "<p>Rodzaj umowy: %s</p><p>Wartosc netto: %s</p><p>Wartosc brutto: %s</p><p>Kosz pracodawcy: %s</p>";
            self::$isConsole = false;
            return false;
        }
    }

    static function showCalculationResult($row) {
        echo sprintf(
            self::$pattern,
            self::$aUmowy[$row['typ_umowy']],
            $row['kwota_netto'],
            $row['kwota_brutto'],
            $row['koszt_pracodawcy']
        );
    }

    static function calculateCosts($idUmowy, $kwotaNetto) {
        switch ($idUmowy) {
            case 0:
                // algorytm liczący wartości wynagrodzenia - umowa zlecenie
                $result['kwota_brutto'] = 0;
                $result['koszt_pracodawcy'] = 0;
                break;
            case 1:
                // algorytm liczący wartości wynagrodzenia - umowa o dzieło
                $result['kwota_brutto'] = 1;
                $result['koszt_pracodawcy'] = 1;
                break;
            case 2:
                // algorytm liczący wartości wynagrodzenia - umowa o pracę
                $result['kwota_brutto'] = 2;
                $result['koszt_pracodawcy'] = 2;
        }
        return $result;
    }
}