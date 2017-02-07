<?php

namespace KKP;

use Accountancy\SalaryService;
use KKP\DataBase\SQLite_KKPDataBase;

class KKPApi {

    private $aArgs = [];
    private $aContracts = ['Umowa zlecenie', 'Umowa o dzielo', 'Umowa o prace'];
    private $pattern = null;
    public $isConsole = false;

    public function isConsole($argv = null) {
        if (PHP_SAPI === 'cli') {
            $this->pattern = "Rodzaj umowy:\t\t%s\nWartosc netto:\t\t%s zl\nWartosc brutto:\t\t%s zl\nKosz pracodawcy:\t%s zl\n";
            $this->isConsole = true;
            $this->aArgs = $argv;
            return true;
        }
        else {
            $this->pattern = "<table><tr><td>Rodzaj umowy:</td><td>%s</td></tr><tr><td>Wartosc netto:</td><td>%s zł</td></tr><tr><td>Wartosc brutto:</td><td>%s zł</td></tr><tr><td>Kosz pracodawcy:</td><td>%s zł</td></tr></table>";
            $this->isConsole = false;
            return false;
        }
    }

    public function getEvent() {
        if ($this->isConsole)
            if (count($this->aArgs) > 2)
                if ($this->aArgs[1] == '-q' && is_numeric($this->aArgs[2])) return 2; // con rekord
                else if (is_numeric($this->aArgs[1]) && is_numeric($this->aArgs[2]) && $this->aArgs[2] >= 0 && $this->aArgs[2] < 3)
                    return 1; // con - poprawna skladnia
                else return -1; // con - nie poprawna skladnia
            else return 0; // con - opis dzialania
        else
            if (isset($_GET['q']) && is_numeric($_GET['q'])) return 2; // www rekord
            else
                if (isset($_POST['netValue']) && isset($_POST['contractId']))
                    if (is_numeric($_POST['netValue']) && is_numeric($_POST['contractId']) && $_POST['contractId'] >=0 && $_POST['contractId'] < 3)
                        return 1; // www - poprawna skladnia
                    else return -1; // www - niepoprawna skladnia
                else return 0; // www - formularz
    }

    public function doCalculations() {
        $aArguments = $this->getArguments();

        $salaryService = new SalaryService();
        $row = $salaryService->calculateSalary(
            $aArguments['contractId'],
            $aArguments['netValue']
        )->toArray();
        $this->showCalculationResult($row);
        
        if ($aArguments['isSave']) {
            $oDataBase = $this->prepareDataBase();
            $idLast = $oDataBase->addRecordAndGetHisId($row);
            if ($this->isConsole)
                echo "\nWynik ten mozna ponownie zobaczyc wpisujac: php ".$this->aArgs[0]." -q {$idLast}\n";
            else {
                $sLink = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'?q='.$idLast;
                echo "Wynik ten dostępny jest pod adresem: <a href='{$sLink}'>{$sLink}</a>";
            }   
        }
    }

    public function showSavedCalculation() {
        $oDataBase = $this->prepareDataBase();
        if ($row = $oDataBase->getAssocRecordById($this->isConsole ? $this->aArgs[2] : $_GET['q'])) $this->showCalculationResult($row);
        else echo "Nie ma zapisanego rekordu o takim id.\n";
    }

    public function showProgramDescription() {
        echo "\n\tSyntax:\tphp ".$this->aArgs[0]." [netValue] [contractId] [save = false]\n";
        echo "\t\tphp ".$this->aArgs[0]." -q [id]\n\n";
        echo "\tid\t\tint\n";
        echo "\tnetValue\tint\n";
        echo "\tcontractId\t0 => ".$this->aContracts[0]."\n";
        echo "\t\t\t1 => ".$this->aContracts[1]."\n";
        echo "\t\t\t2 => ".$this->aContracts[2]."\n";
        echo "\tsave\t\tbool (true, 1, false, 0)\n";
    }

    private function getArguments() {
        if ($this->isConsole) {
            $tablica['netValue'] = $this->aArgs[1];
            $tablica['contractId'] = $this->aArgs[2];
            $tablica['isSave'] = false;
            if (isset($this->aArgs[3]) && filter_var($this->aArgs[3], FILTER_VALIDATE_BOOLEAN)) $tablica['isSave'] = $this->aArgs[3];
        }
        else {
            $tablica['netValue'] = $_POST['netValue'];
            $tablica['contractId'] = $_POST['contractId'];
            if (isset($_POST['save'])) $tablica['isSave'] = true;
            else $tablica['isSave'] = false;
        }
        return $tablica;
    }

    private function showCalculationResult($row) {
        echo sprintf(
            $this->pattern,
            $this->aContracts[$row['contractId']],
            $row['netValue'],
            $row['grossValue'],
            $row['costOfEmployer']
        );
    }

    private function prepareDataBase() {
        $oDataBase = new SQLite_KKPDataBase(); // ew. PostgreSQL_
        $oDataBase->connectDataBase();
        //$oDataBase->deleteTable();
        $oDataBase->createTableIfNotExists();
        return $oDataBase;
    }
}