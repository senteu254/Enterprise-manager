<?php

Class StockRequest {

	var $LineItems; /*array of objects of class LineDetails using the product id as the pointer */
	var $DispatchDate;
	var $Location;
	var $Department;
	var $Narrative;
	var $LineCounter=0;

	function StockRequest(){
	/*Constructor function initialises a new shopping cart */
		$this->DispatchDate = date($_SESSION['DefaultDateFormat']);
		$this->LineItems=array();
	}

	function AddLine($StockID,
					$ItemDescription,
					$Quantity,
					$Quantity2,
					$Quantity3,
					$UOM,
					$DecimalPlaces,
					$cost,
					$LineNumber=-1) {

		if ($LineNumber==-1){
			$LineNumber = $this->LineCounter;
		}
		$this->LineItems[$LineNumber]=new LineDetails($StockID,
												$ItemDescription,
												$Quantity,
												$Quantity2,
												$Quantity3,
												$UOM,
												$DecimalPlaces,
												$cost,
												$LineNumber);
		$this->LineCounter = $LineNumber + 1;
	}
}

Class LineDetails {
	var $StockID;
	var $ItemDescription;
	var $Quantity;
	var $Quantity2;
	var $Quantity3;
	var $UOM;
	var $LineNumber;
	var $cost;

	function LineDetails($StockID,
						$ItemDescription,
						$Quantity,
						$Quantity2,
						$Quantity3,
						$UOM,
						$DecimalPlaces,
						$cost,
						$LineNumber) {

		$this->LineNumber=$LineNumber;
		$this->StockID=$StockID;
		$this->ItemDescription=$ItemDescription;
		$this->Quantity=$Quantity;
		$this->Quantity2=$Quantity2;
		$this->Quantity3=$Quantity3;
		$this->DecimalPlaces=$DecimalPlaces;
		$this->UOM=$UOM;
		$this->cost=$cost;
	}

}

?>