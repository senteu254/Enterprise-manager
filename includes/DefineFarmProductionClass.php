<?php

Class StockRequest1 {

	var $LineItems; /*array of objects of class LineDetails using the product id as the pointer */
	var $DispatchDate;
	var $Location;
	var $SelectedService;
	var $Narrative;
	var $LineCounter=0;

	function StockRequest1(){
	/*Constructor function initialises a new shopping cart */
		$this->DispatchDate = date($_SESSION['DefaultDateFormat']);
		$this->LineItems=array();
	}
	function AddLine($StockID,
					$ItemDescription,
					$Quantity,
					$AreaCovered,
					$UOM,
					$Source,
					$DecimalPlaces,
					$cost,
					$factor,
					$LineNumber=-1) {

		if ($LineNumber==-1){
			$LineNumber = $this->LineCounter;
		}
		$this->LineItems[$LineNumber]=new LineDetails1($StockID,
												$ItemDescription,
												$Quantity,
												$AreaCovered,
												$UOM,
												$Source,
												$DecimalPlaces,
												$cost,
												$factor,
												$LineNumber);
		$this->LineCounter = $LineNumber + 1;
	}
}

Class LineDetails1 {
	var $StockID;
	var $ItemDescription;
	var $Quantity;
	var $AreaCovered;
	var $UOM;
	var $Source;
	var $LineNumber;
	var $cost;
	var $factor;

	function LineDetails1($StockID,
						$ItemDescription,
						$Quantity,
						$AreaCovered,
						$UOM,
						$Source,
						$DecimalPlaces,
						$cost,
						$factor,
						$LineNumber) {

		$this->LineNumber=$LineNumber;
		$this->StockID=$StockID;
		$this->ItemDescription=$ItemDescription;
		$this->Quantity=$Quantity;
		$this->AreaCovered=$AreaCovered;
		$this->Source=$Source;
		$this->DecimalPlaces=$DecimalPlaces;
		$this->UOM=$UOM;
		$this->cost=$cost;
		$this->factor=$factor;
	}

}

?>