<?php

namespace Accountancy\ContractSalary\SpecificTaskContract;

class SpecificTaskContractTaxCalculationPolicy extends \Accountancy\ContractSalary\ContractTaxCalculationPolicy {

	public function calculateFromNetValue($netValue) {
		$this->netValue = $netValue;
		$this->grossValue = 2;
		$this->costOfEmployer = 2;
	}
}