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
        else if (is_numeric($argv[1]) && is_numeric($argv[2]) && $argv[2] >= 0 && $argv[2] < 3) {
            $kwotaNetto = $argv[1];
            $idUmowy = $argv[2];
            $czyZapis = false;
            if (isset($argv[3]) && filter_var($argv[3], FILTER_VALIDATE_BOOLEAN)) $czyZapis = $argv[3];
            
            $result = KKPApi::obliczKosztyPracy($idUmowy, $kwotaNetto);
            $kwotaBrutto = $result['kwota_brutto'];
            $kosztPracodawcy = $result['koszt_pracodawcy'];

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
    if (isset($_GET['q']) && is_numeric($_GET['q'])) {  
        
        $pdo = DataBase::connectDataBase();
        DataBase::createTableIfNotExists($pdo);

        if ($rekord = DataBase::selectRow($pdo, $_GET['q'])) {
            echo sprintf(
                "<p>Rodzaj umowy: %s</p><p>Wartosc netto: %s</p><p>Wartosc brutto: %s</p><p>Kosz pracodawcy: %s</p>",
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
    else {
        if (isset($_POST['kwota_netto']) && isset($_POST['typ_umowy'])) {
            if (is_numeric($_POST['kwota_netto']) && is_numeric($_POST['typ_umowy']) && $_POST['typ_umowy'] >=0 && $_POST['typ_umowy'] < 3) {
                $idUmowy = $_POST['typ_umowy'];
                $kwotaNetto = $_POST['kwota_netto'];
                if (isset($_POST['zapis'])) $czyZapis = true;
                else $czyZapis = false;

                $result = KKPApi::obliczKosztyPracy($idUmowy, $kwotaNetto);
                $kwotaBrutto = $result['kwota_brutto'];
                $kosztPracodawcy = $result['koszt_pracodawcy'];
                
                echo sprintf(
                    "<p>Rodzaj umowy: %s</p><p>Wartosc netto: %s</p><p>Wartosc brutto: %s</p><p>Kosz pracodawcy: %s</p>",
                    KKPApi::$aUmowy[$idUmowy],
                    $kwotaNetto,
                    $kwotaBrutto,
                    $kosztPracodawcy
                );

                if ($czyZapis) {
                    $pdo = DataBase::connectDataBase();
                    DataBase::createTableIfNotExists($pdo);
                    $lastID = DataBase::addRowToDataBaseAndReturnId($pdo, $idUmowy, $kwotaNetto, $kwotaBrutto, $kosztPracodawcy);
                    $sLink = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'?q='.$lastID;
                    echo "Mozesz odtworzyc ten wynik używając linka: <a href='{$sLink}'>{$sLink}</a>";
                }
                exit();
            }
            else {
                echo 'Proszę nie kombinować!';
                exit();
            }
        }
        else {
            require "formularz.html";
            exit();
        }
    }
}