<?php

namespace Accountancy\ContractSalary;

abstract class ContractTaxCalculationPolicy {

	protected $netValue;
	protected $grossValue;
	protected $costOfEmployer;

    abstract function calculateFromNetValue($netValue);

	public function getNet() {
		return $this->netValue;
	}

	public function getGross() {
		return $this->grossValue;
	}

	public function getCostOfEmployer() {
		return $this->costOfEmployer;
	}
}