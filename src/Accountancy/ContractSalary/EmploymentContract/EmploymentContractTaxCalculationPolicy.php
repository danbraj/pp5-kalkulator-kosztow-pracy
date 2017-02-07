<?php

namespace Accountancy\ContractSalary\EmploymentContract;

class EmploymentContractTaxCalculationPolicy extends \Accountancy\ContractSalary\ContractTaxCalculationPolicy {

	public function calculateFromNetValue($netValue) {
		$this->netValue = $netValue;
		$this->grossValue = floor((100 * $netValue / 82) - (24030 / 82) + 297.28);
		$this->costOfEmployer = floor($this->grossValue * 1.2061);
	}
}