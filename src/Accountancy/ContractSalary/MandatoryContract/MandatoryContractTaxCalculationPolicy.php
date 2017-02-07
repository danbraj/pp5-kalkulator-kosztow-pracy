<?php

namespace Accountancy\ContractSalary\MandatoryContract;

class MandatoryContractTaxCalculationPolicy extends \Accountancy\ContractSalary\ContractTaxCalculationPolicy {

	public function calculateFromNetValue($netValue) {
		$this->netValue = $netValue;
		$this->grossValue = 1;
		$this->costOfEmployer = 1;
	}
}