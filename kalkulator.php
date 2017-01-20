<?php

require_once __DIR__.'/vendor/autoload.php';

use KKP\KKPApi;
use KKP\DataBase;

if (PHP_SAPI === 'cli') {
    if (count($argv) > 2) {
        if ($argv[1] == '-rekord' && is_numeric($argv[2])) {
            
            $pdo = DataBase::connectDataBase();
            // $pdo->exec("DROP TABLE rekordy"); wyczyszczenie bazy danych
            DataBase::createTableIfNotExists($pdo);
            if ($rekord = DataBase::selectRow($pdo, $argv[2])) {
                echo sprintf(
                    "Rodzaj umowy: %s\nWartosc netto: %s\nWartosc brutto: %s\nKosz pracodawcy: %s",
                    KKPApi::$aUmowy[$rekord['typ_umowy']],
                    $rekord['kwota_netto'],
                    $rekord['kwota_brutto'],
                    $rekord['koszt_pracodawcy']
                );
            }
            else {
                echo 'Nie ma zapisanego rekordu o takim id.';
            }
            exit();
        }
        else if (is_numeric($argv[1]) && is_numeric($argv[2]) && $argv[2] >= 0 && $argv[2] <= 2) {
            $kwotaNetto = $argv[1];
            $idUmowy = $argv[2];
            $czyZapis = false;
            if (isset($argv[3]) && filter_var($argv[3], FILTER_VALIDATE_BOOLEAN)) $czyZapis = $argv[3];
            
            switch ($idUmowy) {
                case 0:
                    // algorytm liczący wartości wynagrodzenia - umowa zlecenie
                    $kwotaBrutto = 0;
                    $kosztPracodawcy = 0;
                    break;
                case 1:
                    // algorytm liczący wartości wynagrodzenia - umowa o dzieło
                    $kwotaBrutto = 1;
                    $kosztPracodawcy = 1;
                    break;
                case 2:
                    // algorytm liczący wartości wynagrodzenia - umowa o pracę
                    $kwotaBrutto = 2;
                    $kosztPracodawcy = 2;
                    break;
                default:
                    echo "Niepoprawny rodzaj umowy!";
                    exit();
            }

            echo sprintf(
                "Rodzaj umowy: %s\nWartosc netto: %s\nWartosc brutto: %s\nKosz pracodawcy: %s",
                KKPApi::$aUmowy[$idUmowy],
                $kwotaNetto,
                $kwotaBrutto,
                $kosztPracodawcy
            );

            if ($czyZapis) {
                $pdo = DataBase::connectDataBase();
                DataBase::createTableIfNotExists($pdo);
                $lastID = DataBase::addRowToDataBaseAndReturnId($pdo, $idUmowy, $kwotaNetto, $kwotaBrutto, $kosztPracodawcy);
                echo "\nMozesz odtworzyc ten wynik poprzez wpisujac: php {$argv[0]} -rekord {$lastID}";
            }
            exit();
        }
        else echo "Niepoprawna skladnia.\n";
    }
    KKPApi::wyswietlOpisDzialania($argv[0]);
    exit();
}
else {
    echo "W budowie (WWW)";
    exit();
}