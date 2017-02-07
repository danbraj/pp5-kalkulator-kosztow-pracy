<?php

namespace Accountancy;

use Accountancy\ContractSalary\MandatoryContract\MandatoryContractTaxCalculationPolicy;
use Accountancy\ContractSalary\SpecificTaskContract\SpecificTaskContractTaxCalculationPolicy;
use Accountancy\ContractSalary\EmploymentContract\EmploymentContractTaxCalculationPolicy;

class SalaryService {
	
    public function calculateSalary($contractId, $netValue) {
		
        /* założenia:
         * 0 => umowa zlecenie [mandatory contract] (1335 zł - koszt uzyskania przychodu / brak składek zdrowotnych / ubezpieczeniowych)
         * 1 => umowa o dzieło [specific-task contract] (50% udział kosztów uzyskania przychodu / brak składek zdrowotnych / ubezpieczeniowych)
         * 2 => umowa o pracę [employment contract] (1335 zł - koszt uzyskania przychodu / składka zdrowotna / składka ubezpieczeniowa wg. obecnego stanu prawnego)
         */
        switch ($contractId) {
            case 0:
                $obj = new MandatoryContractTaxCalculationPolicy();
                break;
            case 1:
                $obj = new SpecificTaskContractTaxCalculationPolicy();
                break;
            case 2:
                $obj = new EmploymentContractTaxCalculationPolicy();
                break;
            default:
                echo "Niepoprawny rodzaj umowy!\n"; 
                exit();
        }

        $obj->calculateFromNetValue($netValue);
        return new CalculationResult($contractId, $obj->getNet(), $obj->getGross(), $obj->getCostOfEmployer());
	}
}