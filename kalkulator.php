<?php

require_once __DIR__.'/vendor/autoload.php';

use KKP\KKPApi;

KKPApi::isConsole(isset($argv) ? $argv : null);

switch(KKPApi::getEvent()) {
    case 1:
        KKPApi::doCalculations();
        break;
    case 2:
        KKPApi::showSavedRecord();
        break;
    case -1:
        echo "Niepoprawna skladnia.";
        break;
    default:
        if (KKPApi::$isConsole) KKPApi::showProgramDescription();
        else require "formularz.html";
}