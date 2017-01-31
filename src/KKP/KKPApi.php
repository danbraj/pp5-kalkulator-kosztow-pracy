<?php

namespace KKP;

class KKPApi {

    static $aUmowy = array("Umowa zlecenie", "Umowa o dzielo", "Umowa o prace");
    static $isConsole = false;
    static $aArgs = array();
    static $pattern = null;

    static function showProgramDescription() {
        echo "\n\tSkladnia:\tphp ".self::$aArgs[0]." [kwota_netto] [typ_umowy] [zapis = false]\n";
        echo "\t\t\tphp ".self::$aArgs[0]." -rekord [id]\n\n";
        echo "\tid\t\tliczba\n";
        echo "\tkwota_netto\tliczba\n";
        echo "\ttyp_umowy\t0 => ".self::$aUmowy[0]."\n";
        echo "\t\t\t1 => ".self::$aUmowy[1]."\n";
        echo "\t\t\t2 => ".self::$aUmowy[2]."\n";
        echo "\tzapis\t\twartosc logiczna (true, 1, false, 0)\n";
    }

    static function isConsole($argv = null) {
        if (PHP_SAPI === 'cli') {
            self::$pattern = "Rodzaj umowy:\t\t%s\nWartosc netto:\t\t%s zl\nWartosc brutto:\t\t%s zl\nKosz pracodawcy:\t%s zl";
            self::$isConsole = true;
            self::$aArgs = $argv;
            return true;
        }
        else {
            self::$pattern = "<table><tr><td>Rodzaj umowy:</td><td>%s</td></tr><tr><td>Wartosc netto:</td><td>%s zł</td></tr><tr><td>Wartosc brutto:</td><td>%s zł</td></tr><tr><td>Kosz pracodawcy:</td><td>%s zł</td></tr></table>";
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

    static function getEvent() {
        if (self::$isConsole)
            if (count(self::$aArgs) > 2)
                if (self::$aArgs[1] == '-rekord' && is_numeric(self::$aArgs[2])) return 2; // con rekord
                else if (is_numeric(self::$aArgs[1]) && is_numeric(self::$aArgs[2]) && self::$aArgs[2] >= 0 && self::$aArgs[2] < 3)
                    return 1; // con - poprawna skladnia
                else return -1; // con - nie poprawna skladnia
            else return 0; // con - opis dzialania
        else
            if (isset($_GET['q']) && is_numeric($_GET['q'])) return 2; // www rekord
            else
                if (isset($_POST['kwota_netto']) && isset($_POST['typ_umowy']))
                    if (is_numeric($_POST['kwota_netto']) && is_numeric($_POST['typ_umowy']) && $_POST['typ_umowy'] >=0 && $_POST['typ_umowy'] < 3)
                        return 1; // www - poprawna skladnia
                    else return -1; // www - niepoprawna skladnia
                else return 0; // www - formularz
    }

    static function doCalculations() {
        if (self::$isConsole) {
            $tablica['kwota_netto'] = self::$aArgs[1];
            $tablica['typ_umowy'] = self::$aArgs[2];
            $czyZapis = false;
            if (isset(self::$aArgs[3]) && filter_var(self::$aArgs[3], FILTER_VALIDATE_BOOLEAN)) $czyZapis = self::$aArgs[3];
        }
        else {
            $tablica['typ_umowy'] = $_POST['typ_umowy'];
            $tablica['kwota_netto'] = $_POST['kwota_netto'];
            if (isset($_POST['zapis'])) $czyZapis = true;
            else $czyZapis = false;
        }

        $result = self::calculateCosts($tablica['typ_umowy'], $tablica['kwota_netto']);
        $row = array_merge($tablica, $result);
        self::showCalculationResult($row);

        if ($czyZapis) {
            $oDataBase = new DataBase();
            $oDataBase->createTableIfNotExists();

            $idLast = $oDataBase->addRowToDataBaseAndGetId($row);
            if (self::$isConsole)
                echo "\nWynik ten mozna ponownie zobaczyc wpisujac: php ".self::$aArgs[0]." -rekord {$idLast}";
            else {
                $sLink = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'?q='.$idLast;
                echo "Wynik ten dostępny jest pod adresem: <a href='{$sLink}'>{$sLink}</a>";
            }   
        }
    }

    static function showSavedRecord() {
        $oDataBase = new DataBase();
        $oDataBase->createTableIfNotExists();
        if ($row = $oDataBase->getRowById(self::$isConsole ? self::$aArgs[2] : $_GET['q'])) self::showCalculationResult($row);
        else echo 'Nie ma zapisanego rekordu o takim id.';
    }
}