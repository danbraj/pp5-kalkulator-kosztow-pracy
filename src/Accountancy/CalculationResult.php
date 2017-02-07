<?php

namespace Accountancy;

class CalculationResult implements iSaveToArray {

	private $contractId;
	private $netValue;
	private $grossValue;
	private $costOfEmployer;
	
	public function __construct($contractId, $netValue, $grossValue, $costOfEmployer) {
		$this->contractId = $contractId;
		$this->netValue = $netValue;
		$this->grossValue = $grossValue;
		$this->costOfEmployer = $costOfEmployer;
	}

	public function getcontractId() {
		return $this->contractId;
	}

	public function getNetValue() {
		return $this->netValue;
	}

	public function getGrossValue() {
		return $this->grossValue;
	}

    public function getCostOfEmployerValue() {
	    return $this->costOfEmployer;
    }

	public function toArray() {
		$result['contractId'] = $this->contractId;
		$result['netValue'] = $this->netValue;
		$result['grossValue'] = $this->grossValue;
		$result['costOfEmployer'] = $this->costOfEmployer;
		return $result;
	}
}