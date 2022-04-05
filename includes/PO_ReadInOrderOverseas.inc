<?php
/* $Id: PO_ReadInOrder.inc 6941 2014-10-26 23:18:08Z daintree $*/
/*PO_ReadInOrder.inc is used by the modify existing order code in PO_Header.php and also by GoodsReceived.php */

	if (isset($_SESSION['PO'.$identifier])){
		unset ($_SESSION['PO'.$identifier]->LineItems);
		unset ($_SESSION['PO'.$identifier]);
	}

	$_SESSION['ExistingOrder']=$_GET['ModifyOrderNumber'];
	$_SESSION['RequireSupplierSelection'] = 0;
	$_SESSION['PO'.$identifier] = new PurchOrder;

	$_SESSION['PO'.$identifier]->GLLink = $_SESSION['CompanyRecord']['gllink_stock'];

/*read in all the guff from the selected order into the PO PurchOrder Class variable  */
	$OrderHeaderSQL = "SELECT overseaoders.supplierno,
								suppliers.suppname,
								overseaoders.comments,
								 overseaoders.orddate,
								 overseaoders.rate,
								 overseaoders.dateprinted,
								 overseaoders.deladd1,
								 overseaoders.deladd2,
								 overseaoders.deladd3,
								 overseaoders.deladd4,
								 overseaoders.deladd5,
								 overseaoders.deladd6,
								 overseaoders.tel,
								 overseaoders.suppdeladdress1,
								 overseaoders.suppdeladdress2,
								 overseaoders.suppdeladdress3,
								 overseaoders.suppdeladdress4,
								 overseaoders.suppdeladdress5,
								 overseaoders.suppdeladdress6,
								 overseaoders.suppliercontact,
								 overseaoders.supptel,
								 overseaoders.contact,
								 overseaoders.allowprint,
								 overseaoders.requisitionno,
								 overseaoders.intostocklocation,
								 overseaoders.initiator,
								 overseaoders.version,
								 overseaoders.status,
								 overseaoders.stat_comment,
								 overseaoders.deliverydate,
								 overseaoders.deliveryby,
								 overseaoders.port,
								suppliers.currcode,
								locations.managed,
								 overseaoders.paymentterms,
								currencies.decimalplaces
							FROM overseaoders
							INNER JOIN locations ON overseaoders.intostocklocation=locations.loccode
							INNER JOIN locationusers ON locationusers.loccode=locations.loccode AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canupd=1
							INNER JOIN suppliers ON overseaoders.supplierno = suppliers.supplierid
							INNER JOIN currencies ON suppliers.currcode=currencies.currabrev
							WHERE overseaoders.orderno = '" . $_GET['ModifyOrderNumber'] . "'";

	   $ErrMsg =  _('The order cannot be retrieved because');
	   $DbgMsg =  _('The SQL statement that was used and failed was');
	   $GetOrdHdrResult = DB_query($OrderHeaderSQL,$ErrMsg,$DbgMsg);

	if (DB_num_rows($GetOrdHdrResult)==1 and !isset($_SESSION['PO'.$identifier]->OrderNo )) {

			$myrow = DB_fetch_array($GetOrdHdrResult);
			$_SESSION['PO'.$identifier]->OrderNo = $_GET['ModifyOrderNumber'];
			$_SESSION['PO'.$identifier]->SupplierID = $myrow['supplierno'];
			$_SESSION['PO'.$identifier]->SupplierName = $myrow['suppname'];
			$_SESSION['PO'.$identifier]->CurrCode = $myrow['currcode'];
			$_SESSION['PO'.$identifier]->CurrDecimalPlaces = $myrow['decimalplaces'];
			$_SESSION['PO'.$identifier]->Orig_OrderDate = $myrow['orddate'];
			$_SESSION['PO'.$identifier]->AllowPrintPO = $myrow['allowprint'];
			$_SESSION['PO'.$identifier]->DatePurchaseOrderPrinted = $myrow['dateprinted'];
			$_SESSION['PO'.$identifier]->Comments = $myrow['comments'];
			$_SESSION['PO'.$identifier]->ExRate = $myrow['rate'];
			$_SESSION['PO'.$identifier]->Location = $myrow['intostocklocation'];
			$_SESSION['PO'.$identifier]->Initiator = $myrow['initiator'];
			$_SESSION['PO'.$identifier]->RequisitionNo = $myrow['requisitionno'];
			$_SESSION['PO'.$identifier]->DelAdd1 = $myrow['deladd1'];
			$_SESSION['PO'.$identifier]->DelAdd2 = $myrow['deladd2'];
			$_SESSION['PO'.$identifier]->DelAdd3 = $myrow['deladd3'];
			$_SESSION['PO'.$identifier]->DelAdd4 = $myrow['deladd4'];
			$_SESSION['PO'.$identifier]->DelAdd5 = $myrow['deladd5'];
			$_SESSION['PO'.$identifier]->DelAdd6 = $myrow['deladd6'];
			$_SESSION['PO'.$identifier]->Tel = $myrow['tel'];
			$_SESSION['PO'.$identifier]->SuppDelAdd1 = $myrow['suppdeladdress1'];
			$_SESSION['PO'.$identifier]->SuppDelAdd2 = $myrow['suppdeladdress2'];
			$_SESSION['PO'.$identifier]->SuppDelAdd3 = $myrow['suppdeladdress3'];
			$_SESSION['PO'.$identifier]->SuppDelAdd4 = $myrow['suppdeladdress4'];
			$_SESSION['PO'.$identifier]->SuppDelAdd5 = $myrow['suppdeladdress5'];
			$_SESSION['PO'.$identifier]->SuppDelAdd6 = $myrow['suppdeladdress6'];
			$_SESSION['PO'.$identifier]->SupplierContact = $myrow['suppliercontact'];
			$_SESSION['PO'.$identifier]->SuppTel = $myrow['supptel'];
			$_SESSION['PO'.$identifier]->Contact = $myrow['contact'];
			$_SESSION['PO'.$identifier]->Managed = $myrow['managed'];
			$_SESSION['PO'.$identifier]->Version = $myrow['version'];
			$_SESSION['PO'.$identifier]->Port = $myrow['port'];
			$_SESSION['PO'.$identifier]->DeliveryBy = $myrow['deliveryby'];
			$_SESSION['PO'.$identifier]->Status = $myrow['status'];
			$_SESSION['PO'.$identifier]->StatusComments = html_entity_decode($myrow['stat_comment'],ENT_QUOTES,'UTF-8');
			$_SESSION['PO'.$identifier]->DeliveryDate = ConvertSQLDate($myrow['deliverydate']);
			$_SESSION['ExistingOrder'] = $_SESSION['PO'.$identifier]->OrderNo;
			$_SESSION['PO'.$identifier]->PaymentTerms= $myrow['paymentterms'];

			$SupplierSQL = "SELECT suppliers.supplierid,
									suppliers.suppname,
									suppliers.address1,
									suppliers.address2,
									suppliers.address3,
									suppliers.address4,
									suppliers.address5,
									suppliers.address6,
									suppliers.currcode
							FROM suppliers
							WHERE suppliers.supplierid='" . $_SESSION['PO'.$identifier]->SupplierID."'
							ORDER BY suppliers.supplierid";

			$ErrMsg = _('The searched supplier records requested cannot be retrieved because');
			$result_SuppSelect = DB_query($SupplierSQL,$ErrMsg);

			if (DB_num_rows($result_SuppSelect)==1){
				$myrow=DB_fetch_array($result_SuppSelect);
			} elseif (DB_num_rows($result_SuppSelect)==0){
				prnMsg( _('No supplier records contain the selected text') . ' - ' .
					_('please alter your search criteria and try again'),'info');
			}

/*now populate the line PO array with the purchase order details records */

			  $LineItemsSQL = "SELECT podetailitem,
									 overseaorderdetails.itemcode,
									stockmaster.description,
									overseaorderdetails.deliverydate,
									overseaorderdetails.itemdescription,
									glcode,
									accountname,
									overseaorderdetails.qtyinvoiced,
									overseaorderdetails.unitprice,
									stockmaster.units,
									overseaorderdetails.quantityord,
									overseaorderdetails.quantityrecd,
									overseaorderdetails.shiptref,
									overseaorderdetails.completed,
									overseaorderdetails.jobref,
									overseaorderdetails.stdcostunit,
									stockmaster.controlled,
									stockmaster.serialised,
									stockmaster.decimalplaces,
									overseaorderdetails.assetid,
									overseaorderdetails.conversionfactor,
									overseaorderdetails.suppliersunit,
									overseaorderdetails.suppliers_partno
									FROM overseaorderdetails
									LEFT JOIN stockmaster
									ON overseaorderdetails.itemcode=stockmaster.stockid
									INNER JOIN overseaoders
									ON overseaoders.orderno=overseaorderdetails.orderno
									LEFT JOIN chartmaster
									ON overseaorderdetails.glcode=chartmaster.accountcode
									WHERE overseaorderdetails.completed=0
									AND overseaorderdetails.orderno ='" . $_GET['ModifyOrderNumber'] . "'
									ORDER BY podetailitem";

			$ErrMsg =  _('The lines on the purchase order cannot be retrieved because');
			$DbgMsg =  _('The SQL statement that was used to retrieve the purchase order lines was');
			$LineItemsResult = DB_query($LineItemsSQL,$ErrMsg,$DbgMsg);

		  if (DB_num_rows($LineItemsResult) > 0) {

				while ($myrow=DB_fetch_array($LineItemsResult)) {

					 if (is_null($myrow['glcode'])){
						$GLCode = '';
					 } else {
						$GLCode = $myrow['glcode'];
					 }
					if (is_null($myrow['units'])){
						$Units = _('each');
					} else {
						$Units = $myrow['units'];
					}
					if (is_null($myrow['itemcode'])){
						$StockID = '';
					} else {
						$StockID = $myrow['itemcode'];
					}

					$_SESSION['PO'.$identifier]->add_to_order($_SESSION['PO'.$identifier]->LinesOnOrder+1,
															$StockID,
															$myrow['serialised'],
															$myrow['controlled'],
															$myrow['quantityord'],
															stripslashes($myrow['itemdescription']),
															$myrow['unitprice'],
															$Units,
															$GLCode,
															ConvertSQLDate($myrow['deliverydate']),
															$myrow['shiptref'],
															$myrow['completed'],
															$myrow['jobref'],
															$myrow['qtyinvoiced'],
															$myrow['quantityrecd'],
															$myrow['accountname'],
															$myrow['decimalplaces'],
															$myrow['suppliersunit'],
															$myrow['conversionfactor'],
															1,
															$myrow['suppliers_partno'],
															$myrow['assetid'] );

					$_SESSION['PO'.$identifier]->LineItems[$_SESSION['PO'.$identifier]->LinesOnOrder]->PODetailRec = $myrow['podetailitem'];
					$_SESSION['PO'.$identifier]->LineItems[$_SESSION['PO'.$identifier]->LinesOnOrder]->StandardCost = $myrow['stdcostunit'];  /*Needed for receiving goods and GL interface */
			 } /* line PO from purchase order details */
	  } //end is there were lines on the order
   } // end if there was a header for the order
?>
