<?php

namespace Accountancy\ContractSalary\SpecificTaskContract;

class SpecificTaskContractTaxCalculationPolicy extends \Accountancy\ContractSalary\ContractTaxCalculationPolicy {

	public function calculateFromNetValue($netValue) {
		$this->netValue = $netValue;
		$this->grossValue = floor($netValue * 100 / 91);
		$this->costOfEmployer = $this->grossValue;
	}
}