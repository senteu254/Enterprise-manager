<?php

/* $Id: DefineCartClass.php 6942 2014-10-27 02:48:29Z daintree $*/

/* Definition of the cart class
this class can hold all the information for:

i)   a sales order
ii)  an invoice
iii) a credit note

*/


Class Pay {

	var $LineItems; /*array of objects of class LineDetails using the product id as the pointer */

	function Pay(){
	/*Constructor function initialises a new shopping cart */
		$this->LineItems = array();
		$this->ItemsOrdered=0;
		$this->LineCounter=0;
	}

	function add_to_pay($ID,
						 $Descr,
						 $Qty,
						 $LineNumber=-1){

		if (isset($ID) AND $ID!="" AND $Qty>0 AND isset($Qty)){

			if ($LineNumber==-1){
				$LineNumber = $this->LineCounter;
			}

			$this->LineItems[$LineNumber] = new PayDetails($LineNumber,
															$ID,
															$Descr,
															$Qty);
			$this->ItemsOrdered++;

			$this->LineCounter = $LineNumber + 1;
			Return 1;
		}
		Return 0;
	}

	function update_pay_item( $UpdateLineNumber,$Qty){

		if ($Qty>0){
			$this->LineItems[$UpdateLineNumber]->AmtPay = $Qty;
		}
	}

	function remove_from_pay($LineNumber, $UpdateDB='No', $identifier=0){

		if (!isset($LineNumber) OR $LineNumber=='' OR $LineNumber < 0){ /* over check it */
			prnMsg(_('No Line Number passed to remove_from_pay, so nothing has been removed.'), 'error');
			return;
		}
		unset($this->LineItems[$LineNumber]);

	}//remove_from_cart()


} /* end of cart class defintion */

Class PayDetails {
	Var $LineNumber;
	Var $ID;
	Var $PaymentType;
	Var $PaymentDescription;
	Var $AmtPay;

	function PayDetails ($LineNumber,
							$StockItem,
							$Descr,
							$Qty){

/* Constructor function to add a new LineDetail object with passed params */
		$this->LineNumber = $LineNumber;
		$this->PaymentType =$StockItem;
		$this->PaymentDescription = $Descr;
		$this->AmtPay = $Qty;
	} //end constructor function for LineDetails

}

?>
