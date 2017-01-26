<?php

require_once __DIR__.'/vendor/autoload.php';

use KKP\KKPApi;
use KKP\DataBase;

if (KKPApi::isConsole()) {
    if (count($argv) > 2) {
        if ($argv[1] == '-rekord' && is_numeric($argv[2])) {

            $oDataBase = new DataBase();
            $oDataBase->createTableIfNotExists();

            if ($row = $oDataBase->getRowById($argv[2])) KKPApi::showCalculationResult($row);
            else echo 'Nie ma zapisanego rekordu o takim id.';

            exit();
        }
        else if (is_numeric($argv[1]) && is_numeric($argv[2]) && $argv[2] >= 0 && $argv[2] < 3) {

            $tablica['kwota_netto'] = $argv[1];
            $tablica['typ_umowy'] = $argv[2];
            $czyZapis = false;
            if (isset($argv[3]) && filter_var($argv[3], FILTER_VALIDATE_BOOLEAN)) $czyZapis = $argv[3];
            
            $result = KKPApi::calculateCosts($tablica['typ_umowy'], $tablica['kwota_netto']);
            $row = array_merge($tablica, $result);
            KKPApi::showCalculationResult($row);
            
            if ($czyZapis) {
                $oDataBase = new DataBase();
                $oDataBase->createTableIfNotExists();

                $idLast = $oDataBase->addRowToDataBaseAndGetId($row);
                echo "\nMozesz odtworzyc ten wynik poprzez wpisujac: php {$argv[0]} -rekord {$idLast}";
            }
            exit();
        }
        else echo "Niepoprawna skladnia.\n";
    }
    else {
        KKPApi::showProgramDescription($argv[0]);
        exit();
    }  
}
else {
    if (isset($_GET['q']) && is_numeric($_GET['q'])) {  
        
        $oDataBase = new DataBase();
        $oDataBase->createTableIfNotExists();

        if ($row = $oDataBase->getRowById($_GET['q'])) KKPApi::showCalculationResult($row);
        else echo 'Nie ma zapisanego rekordu o takim id.';

        exit();
    }
    else {
        if (isset($_POST['kwota_netto']) && isset($_POST['typ_umowy'])) {
            if (is_numeric($_POST['kwota_netto']) && is_numeric($_POST['typ_umowy']) && $_POST['typ_umowy'] >=0 && $_POST['typ_umowy'] < 3) {

                $tablica['typ_umowy'] = $_POST['typ_umowy'];
                $tablica['kwota_netto'] = $_POST['kwota_netto'];
                if (isset($_POST['zapis'])) $czyZapis = true;
                else $czyZapis = false;

                $result = KKPApi::calculateCosts($tablica['typ_umowy'], $tablica['kwota_netto']);
                $row = array_merge($tablica, $result);
                KKPApi::showCalculationResult($row);

                if ($czyZapis) {
                    $oDataBase = new DataBase();
                    $oDataBase->createTableIfNotExists();

                    $idLast = $oDataBase->addRowToDataBaseAndGetId($row);
                    $sLink = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'?q='.$idLast;
                    echo "Mozesz odtworzyc ten wynik używając linka: <a href='{$sLink}'>{$sLink}</a>";
                }
                exit();
            }
            else echo 'Proszę nie kombinować!';
        }
        else {
            require "formularz.html";
            exit();
        }
    }
}