<?php

require_once __DIR__.'/vendor/autoload.php';

use KKP\KKPApi;

$api = new KKPApi();
$api->isConsole(isset($argv) ? $argv : null);

switch($api->getEvent()) {
    case 1:
        $api->doCalculations();
        break;
    case 2:
        $api->showSavedCalculation();
        break;
    case -1:
        echo "Niepoprawna skladnia.\n";
        break;
    default:
        if ($api->isConsole) $api->showProgramDescription();
        else require "template/formularz.html";
}