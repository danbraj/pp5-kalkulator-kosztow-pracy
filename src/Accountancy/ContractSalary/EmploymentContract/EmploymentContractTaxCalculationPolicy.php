<?php

namespace Accountancy\ContractSalary\EmploymentContract;

class EmploymentContractTaxCalculationPolicy extends \Accountancy\ContractSalary\ContractTaxCalculationPolicy {

	public function calculateFromNetValue($netValue) {
		$this->netValue = $netValue;
		$this->grossValue = 3;
		$this->costOfEmployer = 3;
	}
}