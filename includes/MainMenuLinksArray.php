<?php

/* $Id: MainMenuLinksArray.php 6190 2013-08-12 02:12:02Z rchacon $*/

/* webERP menus with Captions and URLs. */

$ModuleLink = array('orders', 'AR', 'PO', 'AP', 'stock', 'FA2', 'IRQ2','MRS', 'HR', 'SEC2', 'manuf',  'GL', 'FA', 'PC', /*'PV',*/ 'PVM', 'system', 'Utilities','QA','CON');
$ReportList = array('orders'=>'ord',
					'AR'=>'ar',
					'PO'=>'prch',
					'AP'=>'ap',
					'stock'=>'inv',
					'FM2'=>'fa2',
					'IRQ2'=>'irq2',
					'MRS'=>'mrs',
					'HR'=>'hr',
					'SEC2'=>'sec2',
					'manuf'=>'man',
					'GL'=>'gl',
					'FA'=>'fa',
					'PC'=>'pc',
					/*'PV'=>'pv',*/
					'PVM'=>'pvm',
					'system'=>'sys',
					'Utilities'=>'utils',
					'QA'=>'qa',
					'CON'=>'con'
					);

/*The headings showing on the tabs accross the main index used also in WWW_Users for defining what should be visible to the user */
$ModuleList = array(_('Commercial Services'),
					_('Accounts Receivables'),
					_('Procurement'),
					_('Accounts Payables'),
					_('Stores & Warehouse'),
					_('Farm'),
					_('Requisition'),
					_('MRS'),
					_('Human Resource'),
					_('Security'),
					_('Production'),
					_('Finance'),
					_('Maintenance'),
					_('Petty Cash'),
					_('Payment Voucher'),
					/*_('Payment Voucher2'),*/
					_('Setup'),
					_('Utilities'),
					_('Quality Assurance'),
					_('Contract'));
$MenuItems['PV']['Transactions']['Caption'] = array(_('Create Payment Voucher/VBC Certificate'),
														_('Procurement Certificate'),
														_('AIE Holder Certificate'),
														_('Internal Audit'),
														_('Voucher Examination'),
														_('Voucher Authorization/Payment'));

$MenuItems['PV']['Transactions']['URL'] = array('/Create_PV.php',
												'/Process_Exam_PV.php',
												'/Process_VB_PV.php',
												'/Process_IA_PV.php',
												'/Process_Auth_PV.php',
												'/Process_CP_PV.php');
												
$MenuItems['PV']['Reports']['Caption'] = array(_('Payment Voucher Report'),
												_('Payment Voucher Report'),
												_('Payment Voucher Report'),
												_('Payment Voucher Report'),
												_('Payment Voucher Report'));

$MenuItems['PV']['Reports']['URL'] = array('/PaymentVoucherReports.php',
											'/PaymentVoucherReportsPC.php',
											'/PaymentVoucherReportsAIE.php',
											'/PaymentVoucherReportsIA.php',
											'/PaymentVoucherReportsVE.php');	

$MenuItems['FM']['Transactions']['Caption'] = array(_('Farm Production'));
$MenuItems['FM']['Transactions']['URL'] = array('/Farm_Production.php?New=Yes',
                                                 '/Farm_CropHusbandry.php',
                                                  '/FarmPasture_Havesting_Transportation.php');
$MenuItems['FM']['Reports']['Caption'] = array(	_('Farm Production Report'),
                                                _('Farm Fields'),
                                                _('View Farm Description'));

$MenuItems['FM']['Reports']['URL'] = array('/Farmproductionview.php',
                                           '/Farmfields.php',
                                            '/Farm_DescriptionView.php');												  
$MenuItems['FM']['Maintenance']['Caption'] = array(	_('Farm Field Maintenance'),
                                                    _('Farm Services Maintenance'),
												     _('Item Source Maintenance'),
                                                    _('Farm Services Description'));
$MenuItems['FM']['Maintenance']['URL'] = array('/FarmField_Maintenance.php',
                                                '/Farm_ServicesMaintenance.php',
												'/Farm_ItemSource.php',
                                                 '/Farm_Description.php');
												 
/*$MenuItems['SEC']['Transactions']['Caption'] = array(_('Visitors Booking Register'),
                                                     _('Vehicles Booking Register'),
                                                     _('Staff Vehicles Register'),
													 _('Staff Booking Register'));
													 
$MenuItems['SEC']['Transactions']['URL'] = array('/Sec_Visitors.php',
                                                 '/Sec_Vehicles.php',
                                                 '/Sec_VehiclesStaff.php',
												 '/Sec_Staff.php');
												 
$MenuItems['SEC']['Reports']['Caption'] = array( _('Visitors Booking Report'),
												 _('Material Booking Report'),
												 _('Vehicles Booking Report'));

$MenuItems['SEC']['Reports']['URL'] = array('/Sec_VisitorBookingReport.php',
											'/Sec_MaterialBookingReport.php',
											'/Sec_VehicleBookingReport.php');

$MenuItems['SEC']['Maintenance']['Caption'] = array(	_('Gates Maintenance'));
$MenuItems['SEC']['Maintenance']['URL'] = array('/GateMaintenance.php');*/
//added by kalfrique for request for purchase
/*$MenuItems['IRQ']['Transactions']['Caption'] = array(_('Request for Purchase or Service'),
														_('Store Requisition and Issue Voucher'),
														_('Transport Requisition'),
														_('Maintenance Requisition'),
														_('Gate Pass Requisition'));

$MenuItems['IRQ']['Transactions']['URL'] = array('/IRQ_PurchaseOrService.php?New=Document',
												 '/IRQ_StoresRequisition.php?New=Document&Doc=4',
												 '/IRQ_TransportRequest.php?New=Document',
												  '/IRQ_MaintenanceRequest.php?New=Document',
												  '/IRQ_GatepassRequest.php?New=Document');
$MenuItems['IRQ']['Maintenance']['Caption'] = array(_('Create Procurement Plan') ,
													_('Edit Procurement Plan'),
													_('Requisition Flow Authorised Users Maintenance'));

$MenuItems['IRQ']['Maintenance']['URL'] = array('/Create_ProcurementPlan.php?New=Yes',
												'/IRQ_Edit_ProcurementPlan.php',
												'/RequisitionUserRoles.php');

$MenuItems['IRQ']['Reports']['Caption'] = array( _('Procurement Plan Report Simple'),
												 _('Procurement Plan Report Detailed'),
												 _('Procurement Plan Report(Group By Item)'),
												 _('Requisition Report'));

$MenuItems['IRQ']['Reports']['URL'] = array('/IRQ_PDFProcurementPlanSimple.php',
											'/IRQ_PDFProcurementPlan.php',
											'/IRQ_PDFProcurementPlanGeneral.php',
											'/PDFRequisitionReport.php');*/
//end added by kalfrique for request for purchase
$MenuItems['MRS']['Transactions']['Caption'] = array(_('Patient Record'),
														_('Add Doctors Details'));

$MenuItems['MRS']['Transactions']['URL'] = array('/EmployeeMedicalRecord.php',
												 '/Doctors.php');

$MenuItems['MRS']['Maintenance']['Caption'] = array();

$MenuItems['MRS']['Maintenance']['URL'] = array();

$MenuItems['MRS']['Reports']['Caption'] = array( _('Individual Report'));

$MenuItems['MRS']['Reports']['URL'] = array('/IndividualReport.php');



$MenuItems['orders']['Transactions']['Caption'] = array(_('Enter Counter Sales'),
														_('New Sales Order or Quotation'),
														//_('Enter Counter Returns'),
														_('Outstanding Sales Orders/Quotations'),
														_('Special Order'),
														_('Recurring Order Template'),
														_('Process Recurring Orders'));

$MenuItems['orders']['Transactions']['URL'] = array('/CounterSales.php',
													'/SelectOrderItems.php?NewOrder=Yes',
													//'/CounterReturns.php',
													'/SelectSalesOrder.php',
													'/SpecialOrder.php',
													'/SelectRecurringSalesOrder.php',
													'/RecurringSalesOrdersProcess.php');

$MenuItems['orders']['Reports']['Caption'] = array( _('Sales Order Inquiry'),
													_('Print Price Lists'),
													_('Order Status Report'),
													_('Orders Invoiced Reports'),
													_('Daily Sales Inquiry'),
													_('Sales Invoice Inquiry'),
													_('Sales By Sales Type Inquiry'),
													_('Sales By Category Inquiry'),
													_('Top Sellers Inquiry'),
													_('Order Delivery Differences Report'),
													_('Delivery In Full On Time (DIFOT) Report'),
													_('Sales Order Detail Or Summary Inquiries'),
													_('Top Sales Items Inquiry'),
													_('Top Customers Inquiry'),
													_('Worst Sales Items Report'),
													_('Sales With Low Gross Profit Report'),
													_('Sell Through Support Claims Report'));

$MenuItems['orders']['Reports']['URL'] = array( '/SelectCompletedOrder.php',
												'/PDFPriceList.php',
												'/PDFOrderStatus.php',
												'/PDFOrdersInvoiced.php',
												'/DailySalesInquiry.php',
												'/PDFSalesInvoiceInquiry.php',
												'/SalesByTypePeriodInquiry.php',
												'/SalesCategoryPeriodInquiry.php',
												'/SalesTopItemsInquiry.php',
												'/PDFDeliveryDifferences.php',
												'/PDFDIFOT.php',
												'/SalesInquiry.php',
												'/TopItems.php',
												'/SalesTopCustomersInquiry.php',
												'/NoSalesItems.php',
												'/PDFLowGP.php',
												'/PDFSellThroughSupportClaim.php');

$MenuItems['orders']['Maintenance']['Caption'] = array(_('Sell Through Support Deals'));

$MenuItems['orders']['Maintenance']['URL'] = array('/SellThroughSupport.php');

$MenuItems['AR']['Transactions']['Caption'] = array(_('Select Order to Invoice'),
													_('Create A Credit Note'),
													/*_('Enter Cash Sale Receipts'),*/
													_('Enter Receipts'),
													_('Allocate Receipts or Credit Notes'));
$MenuItems['AR']['Transactions']['URL'] = array('/SelectSalesOrder.php',
												'/SelectCreditItems.php?NewCredit=Yes',
												/*'/CounterSalesReceipt.php',*/
												'/CustomerReceipt.php?NewReceipt=Yes&amp;Type=Customer',
												'/CustomerAllocations.php');

$MenuItems['AR']['Reports']['Caption'] = array(	_('Where Allocated Inquiry'),
												_('Print Invoices or Credit Notes'),
												_('Print Statements'),
												_('Sales Analysis Reports'),
												_('Aged Customer Balances/Overdues Report'),
												_('Re-Print Receipt'),
												_('Re-Print A Deposit Listing'),
												_('Debtor Balances At A Prior Month End'),
												_('Customer Listing By Area/Salesperson'),
												_('Sales Graphs'),
												_('List Daily Transactions'),
												_('Customer Transaction Inquiries'),
												_('Customer Activity and Balances'));

if ($_SESSION['InvoicePortraitFormat']==0){
	$PrintInvoicesOrCreditNotesScript = '/PrintCustTrans.php';
} else {
	$PrintInvoicesOrCreditNotesScript = '/PrintCustTransPortrait.php';
}

$MenuItems['AR']['Reports']['URL'] = array(	'/CustWhereAlloc.php',
											$PrintInvoicesOrCreditNotesScript,
											'/PrintCustStatements.php',
											'/SalesAnalRepts.php',
											'/AgedDebtors.php',
											'/ReprintReceipt.php',
											'/PDFBankingSummary.php',
											'/DebtorsAtPeriodEnd.php',
											'/PDFCustomerList.php',
											'/SalesGraph.php',
											'/PDFCustTransListing.php',
											'/CustomerTransInquiry.php',
											'/CustomerBalancesMovement.php' );

$MenuItems['AR']['Maintenance']['Caption'] = array(	_('Close Customer End Year Balance'),
                                                    _('Add Customer'),
													_('Select Customer'));
$MenuItems['AR']['Maintenance']['URL'] = array(	'/CustomerBalsAtPeriod.php',
                                                 '/Customers.php',
												'/SelectCustomer.php');

$MenuItems['AP']['Transactions']['Caption'] = array(_('Select Supplier'),
													_('Supplier Allocations'));
$MenuItems['AP']['Transactions']['URL'] = array('/SelectSupplier.php',
												'/SupplierAllocations.php');

$MenuItems['AP']['Reports']['Caption'] = array(	_('Aged Supplier Report'),
												_('Payment Run Report'),
												_('Remittance Advices'),
												_('Outstanding GRNs Report'),
												_('Supplier Balances At A Prior Month End'),
												_('List Daily Transactions'),
												_('Supplier Transaction Inquiries'));

$MenuItems['AP']['Reports']['URL'] = array(	'/AgedSuppliers.php',
											'/SuppPaymentRun.php',
											'/PDFRemittanceAdvice.php',
											'/OutstandingGRNs.php',
											'/SupplierBalsAtPeriodEnd.php',
											'/PDFSuppTransListing.php',
											'/SupplierTransInquiry.php');

$MenuItems['AP']['Maintenance']['Caption'] = array(	_('Add Supplier'),
													_('Select Supplier'),
													_('Maintain Factor Companies'));
$MenuItems['AP']['Maintenance']['URL'] = array(	'/Suppliers.php',
												'/SelectSupplier.php',
												'/Factors.php');

$MenuItems['PO']['Transactions']['Caption'] = array(_('New Purchase Order'),
                                                    _('New Service Order'),
													_('New Overseas Order'),
													_('Service Orders'),
													_('Purchase Orders'),
													_('Overseas Orders'),
													//_('Purchase Order Grid Entry'),
													_('Create a New Quotation'),
													_('Edit Existing Quotations'),
													_('Supplier Quotation Report'),
													_('Process Quotation and Offers'),
													_('Orders to Authorise'),
													_('Shipment Entry'),
													_('Select A Shipment'),
													_('Votebook Commitment'),
													_('Reverse Votebook Commitment'),
													_('Votebook Decommitment'));
$MenuItems['PO']['Transactions']['URL'] = array(	'/PO_Header.php?NewOrder=Yes',
                                                    '/LSO_Header.php?NewOrder=Yes',
													'/Overseas_Header.php?NewOrder=Yes',
													'/LSO_SelectOSPurchOrder.php',													
													'/PO_SelectOSPurchOrder.php',
													'/PO_SelectOSPurchOrderOveseas.php',
													//'/PurchaseByPrefSupplier.php',
													'/SupplierTenderCreate.php?New=Yes',
													'/SupplierTenderCreate.php?Edit=Yes',
													'/SupplierTendersReport.php',
													'/OffersReceived.php',
													'/PO_AuthoriseMyOrders.php',
													'/SelectSupplier.php',
													'/Shipt_Select.php',
													'/Votebook_Commitment.php',
													'/Votebook_ReverseCommitment.php',
													'/Votebook_Decommitment.php');

$MenuItems['PO']['Reports']['Caption'] = array( _('Procurement Status'),
                                               	_('Purchase Order Inquiry'),
                                                _('Service Order Inquiry'),
											    _('Supply Orders Group Report'),
												_('Purchase Order Detail Or Summary Inquiries'),
												_('Supplier Price List'),
												_('Votebook Expenses'),
												_('Daily Payment Voucher Expenses'),
												_('Votebook Supplimentary Tracking'),
												_('Suppliers Payments'),
												_('Goods Already Received'),
												//_(' Farm Contract Inquiry'),
												_('Procurement Plan Report Simple'),
												 _('Procurement Plan Report Detailed'),
												 _('Procurement Plan Report(Group By Item)'),
												 _('Requisition Report'),
												 _('LPO To Vote'));

$MenuItems['PO']['Reports']['URL'] = array(	'/ProcurementStatusReport.php',
                                            '/PO_SelectPurchOrder.php',
                                            '/LSO_SelectPurchOrder.php',
											'/SupplylpoReport.php',
											'/POReport.php',
											'/SuppPriceList.php',
											'/Votebook_Expenses.php',
											'/daily_payment_voucher_Expenses.php',
											'/VotebookSupplimentaryTracking.php',
											'/SuppliersPayments.php',
											'/GoodsRecievedall.php',
											//'/FarmContractInquiry.php',
											'/IRQ_PDFProcurementPlanSimple.php',
											'/IRQ_PDFProcurementPlan.php',
											'/IRQ_PDFProcurementPlanGeneral.php',
											'/PDFRequisitionReport.php',
											'/lpo_to_vote.php');

$MenuItems['PO']['Maintenance']['Caption'] = array(_('Close Supplier End Year Balance'),
                                                    _('Maintain Supplier Price Lists'),
													//_('Maintain Farm Contract'),
													_('Book Maintenance'),
													_('Voteheads Maintenance'),
													_('Funds Allocations'),
													_('Reverse Funds Allocation'),
													_('Supplimentary Allocation'),
													_('Order Number Control'),
													_('Create Procurement Plan'),
													_('Edit Procurement Plan'),
													_('Procurement Cell Maintenence'),
													_('Data Cells'),
													_('Prequalified Suppliers'));
													
$MenuItems['PO']['Maintenance']['URL'] = array('/SupplierBalsAtPeriod.php',
                                               '/SupplierPriceList.php',
											   //'/Contracts.php',
											    '/Votebookmaintenance.php',
											    '/VoteHeadsMaintenance.php',
											   '/Votebook_Allocate_Funds.php',
											   '/Votebook_ReverseFundsAllocation.php',
											   '/Votebook_SupplimentaryAlloc.php',
											   '/PurchOrderControl.php',
											   '/Create_ProcurementPlan.php?New=Yes',
												'/IRQ_Edit_ProcurementPlan.php',
											    '/ProcurementCellMaintenance.php',
											   '/Data_Cells.php',
											   '/PrequalifiedSuppliers.php');

$MenuItems['stock']['Transactions']['Caption'] = array(	_('Receive Local Purchase Orders'),
                                                        _('Receive Local Service Orders'),
														_('Inventory Location Transfers'),	//"Inventory Transfer - Item Dispatch"
														_('Bulk Inventory Transfer') . ' - ' . _('Dispatch'),	//"Inventory Transfer - Bulk Dispatch"
														_('Bulk Inventory Transfer') . ' - ' . _('Receive'),	//"Inventory Transfer - Receive"
														_('Inventory Adjustments'),
														_('Print Delivery Note'),
														_('Reverse Goods Received'),
														_('Enter Stock Counts'),
														_('Enter Stock Inventory'));

$MenuItems['stock']['Transactions']['URL'] = array(	'/PO_SelectOSPurchOrder.php',
                                                    '/LSO_SelectOSPurchOrder.php',
													'/StockTransfers.php?New=Yes',
													'/StockLocTransfer.php',
													'/StockLocTransferReceive.php',
													'/StockAdjustments.php?NewAdjustment=Yes',
													'/PrintCustOrder_DeliveryNote.php',
													'/ReverseGRN.php',
													'/StockCounts.php',
													'/StockAdjustmentsNew.php');

$MenuItems['stock']['Reports']['Caption'] = array(	_('Serial Item Research Tool'),
													_('Print Price Labels'),
													_('Reprint Receipt Voucher(RV)'),
													_('Inventory Item Movements'),
													_('Inventory Item Status'),
													_('Inventory Item Usage'),
													_('Inventory Quantities'),
													_('Reorder Level'),
													_('Stock Dispatch'),
													_('Inventory Valuation Report'),
													_('Mail Inventory Valuation Report'),
													_('Inventory Planning Report'),
													_('Inventory Planning Based On Preferred Supplier Data'),
													_('Inventory Stock Check Sheets'),
													_('Make Inventory Quantities CSV'),
													_('Compare Counts Vs Stock Check Data'),
													_('All Inventory Movements By Location/Date'),
													_('List Inventory Status By Location/Category'),
													_('Historical Stock Quantity By Location/Category'),
													_('List Negative Stocks'),
													_('Period Stock Transaction Listing'),
													_('Stock Transfer Note'),
													_('Goods Already Recieved'),
													_('Aged Controlled Stock Report'),
													_('Requisition Report'));

$MenuItems['stock']['Reports']['URL'] = array(	'/StockSerialItemResearch.php',
												'/PDFPrintLabel.php',
												'/ReprintGRN.php',
												'/StockMovements.php',
												'/StockStatus.php',
												'/StockUsage.php',
												'/InventoryQuantities.php',
												'/ReorderLevel.php',
												'/StockDispatch.php',
												'/InventoryValuation.php',
												'/MailInventoryValuation.php',
												'/InventoryPlanning.php',
												'/InventoryPlanningPrefSupplier.php',
												'/StockCheck.php',
												'/StockQties_csv.php',
												'/PDFStockCheckComparison.php',
												'/StockLocMovements.php',
												'/StockLocStatus.php',
												'/StockQuantityByDate.php',
												'/PDFStockNegatives.php',
												'/PDFPeriodStockTransListing.php',
												'/PDFStockTransfer.php',
												'/GoodsRecievedall.php',
												'/AgedControlledInventory.php',
												'/PDFRequisitionReport.php');

$MenuItems['stock']['Maintenance']['Caption'] = array(	_('Add A New Item'),
														_('Select An Item'),
														_('Review Translated Descriptions'),
														_('Sales Category Maintenance'),
														_('Brands Maintenance'),
														_('Add or Update Prices Based On Costs'),
														_('View or Update Prices Based On Costs'),
														_('Reorder Level By Category/Location'));

$MenuItems['stock']['Maintenance']['URL'] = array(	'/Stocks.php',
													'/SelectProduct.php',
													'/RevisionTranslations.php',
													'/SalesCategories.php',
													'/Manufacturers.php',
													'/PricesBasedOnMarkUp.php',
													'/PricesByCost.php',
													'/ReorderLevelLocation.php');

$MenuItems['manuf']['Transactions']['Caption'] = array(	_('Work Order Entry'),
														_('Select A Work Order'),
														_('Bulk Inventory Transfer - Dispatch'),
														/*_('QA Samples and Test Results'),
														_('AQL Sample Results Remarks(SQAO)'),
														_('AQL Sample Results Remarks(CQAO)'),
														_('AQL Sample Results Remarks(QARD MANAGER)'),
														_('Create Non-Conformance Form(QAT)'),
														_('Non-Conformance Form(M/C Setter Remarks)'),
														_('Non-Conformance Form(CAPO Remarks)'),
														_('Non-Conformance Form(PM Remarks)'),
														_('Non-Conformance Form(CQAO Remarks)'),
														_('Non-Conformance Form(QARDM Remarks)'),
														_('Primer Sensitivity Curve Data Sheet'),
														_('54 QA Daily Report'),
														_('54 QA Daily Report(Foreman)'),
														_('54 QA Daily Report(SQAO)'),
														_('54 QA Daily Report(QA Manager)'),
														_('QA Hardness Annealing Graph'),*/
														_('Create a New Internal Stock Request'),
														/*_('Authorise Internal Stock Requests'),*/
														_('Fulfill Internal Stock Requests'));

$MenuItems['manuf']['Transactions']['URL'] = array(	'/WorkOrderEntry.php',
													'/SelectWorkOrder.php',
													'/StockLocTransferProduction.php',
													/*'/SelectQASamples.php',
													'/TestPlanResultsSQAO.php',
													'/TestPlanResultsCQAO.php',
													'/TestPlanResultsQARD.php',
													'/QACreateNon_ConformanceForm.php',
													'/QASetterNon_ConformanceForm.php',
													'/QACAPONon_ConformanceForm.php',
													'/QAPMNon_ConformanceForm.php',
													'/QACQAONon_ConformanceForm.php',
													'/QAQARDMNon_ConformanceForm.php',
													'/QAPrimerDataSheet.php',
													'/QADailyReport.php',
													'/QADailyReportForeman.php',
													'/QADailyReportSQAO.php',
													'/QADailyReportManager.php',
													'/QA_HardnessAnnealingGraph.php',*/
													'/InternalStockRequest.php?New=Yes',
													/*'/InternalStockRequestAuthorisation.php',*/
													'/InternalStockRequestFulfill.php');
$MenuItems['manuf']['Reports']['Caption'] = array(	_('Select A Work Order'),
													_('Costed Bill Of Material Inquiry'),
													_('Where Used Inquiry'),
													_('Bill Of Material Listing'),
													_('Indented Bill Of Material Listing'),
													_('List Components Required'),
													_('List Materials Not Used Anywhere'),
													_('Indented Where Used Listing'),
													_('WO Items ready to produce'),
													_('Period Stock Transaction Listing'),
													_('Period Stock Listing'),
													_('Stock Transfer Transaction Listing'),
													_('MRP'),
													_('MRP Shortages'),
													_('MRP Suggested Purchase Orders'),
													_('MRP Suggested Work Orders'),
													_('MRP Reschedules Required'),
													_('Print Product Specification'),
													_('Print Certificate of Analysis'),
													_('Historical QA Test Results'),
													_('54 QA Inventory Request Report'));

$MenuItems['manuf']['Reports']['URL'] = array(	'/SelectWorkOrder.php',
												'/BOMInquiry.php',
												'/WhereUsedInquiry.php',
												'/BOMListing.php',
												'/BOMIndented.php',
												'/BOMExtendedQty.php',
												'/MaterialsNotUsed.php',
												'/BOMIndentedReverse.php',
												'/WOCanBeProducedNow.php',
												'/PDFPeriodStockTransListing_Production.php',
												'/PeriodStockListing_Production.php',
												'/PDFPeriodStockTransListing_Transfers.php',
												'/MRPReport.php',
												'/MRPShortages.php',
												'/MRPPlannedPurchaseOrders.php',
												'/MRPPlannedWorkOrders.php',
												'/MRPReschedules.php',
												'/PDFProdSpec.php',
												'/PDFCOA.php',
												'/HistoricalTestResults.php',
												'/QARequisitionReport.php');

$MenuItems['manuf']['Maintenance']['Caption'] = array(	_('Work Centre'),
														_('Define Calibre'),
														_('Bills Of Material'),
														_('Copy a Bill Of Materials Between Items'),
														_('Master Schedule'),
														_('Auto Create Master Schedule'),
														_('MRP Calculation'),
														_('Quality Tests Maintenance'),
														_('Product Specifications'),
														_('Work Orders Authorised Users Maintenance'),
														_('QA Operation Definition'),
														_('QA Operation Type'),
														_('QA Operation Recording Sheet'));

$MenuItems['manuf']['Maintenance']['URL'] = array(	'/WorkCentres.php',
													'/WorkOrderCalibre.php',
													'/BOMs.php',
													'/CopyBOM.php',
													'/MRPDemands.php',
													'/MRPCreateDemands.php',
													'/MRP.php',
													'/QATests.php',
													'/ProductSpecs.php',
													'/WorkOrderUsers.php',
													'/QAOperationDefinition.php',
													'/QAOperationType.php',
													'/QAOperationRecordingSheet.php');

$MenuItems['GL']['Transactions']['Caption'] = array(	_('Bank Account Payments Entry'),
														_('Bank Account Receipts Entry'),
														_('Import Bank Transactions'),
														_('Bank Account Payments Matching'),
														_('Bank Account Receipts Matching'),
														_('Journal Entry'));

$MenuItems['GL']['Transactions']['URL'] = array('/Payments.php?NewPayment=Yes',
												'/CustomerReceipt.php?NewReceipt=Yes&amp;Type=GL',
												'/ImportBankTrans.php',
												'/BankMatching.php?Type=Payments',
												'/BankMatching.php?Type=Receipts',
												'/GLJournal.php?NewJournal=Yes');

$MenuItems['GL']['Reports']['Caption'] = array(	_('Trial Balance'),
												_('Account Inquiry'),
												_('Account Listing'),
												_('Account Listing to CSV File'),
												_('General Ledger Journal Inquiry'),
												_('Bank Account Reconciliation Statement'),
												_('Cheque Payments Listing'),
												_('Daily Bank Transactions'),
												_('Profit and Loss Statement'),
												_('Balance Sheet'),
												_('Horizontal Analysis of Statement of Comprehensive Income'),
												_('Horizontal Analysis of Statement of Financial Position'),
												_('Cash Book'),
												_('Supplier Tax Reference'),
												_('Tag Reports'),
												_('Tax Reports'));

$MenuItems['GL']['Reports']['URL'] = array(	'/GLTrialBalance.php',
											'/SelectGLAccount.php',
											'/GLAccountReport.php',
											'/GLAccountCSV.php',
											'/GLJournalInquiry.php',
											'/BankReconciliation.php',
											'/PDFChequeListing.php',
											'/DailyBankTransactions.php',
											'/GLProfit_Loss.php',
											'/GLBalanceSheet.php',
											'/AnalysisHorizontalIncome.php',
											'/AnalysisHorizontalPosition.php',
											'/Cash_Book.php',
											'/SupplierTaxReference.php',
											'/GLTagProfit_Loss.php',
											'/Tax.php');

$MenuItems['GL']['Maintenance']['Caption'] = array(	_('Account Sections'),
													_('Account Groups'),
													_('GL Accounts'),
													_('GL Account Authorized Users'),
													_('User Authorized GL Accounts'),
													_('GL Budgets'),
													_('GL Tags'),
													_('Bank Accounts'),
													_('Bank Account Authorised Users'),
													_('Bank Accounts Balance'),
													_('PV Tax Accounts Settings'),
													_('Cheque Control'));

$MenuItems['GL']['Maintenance']['URL'] = array(		'/AccountSections.php',
													'/AccountGroups.php',
													'/GLAccounts.php',
													'/GLAccountUsers.php',
													'/UserGLAccounts.php',
													'/GLBudgets.php',
													'/GLTags.php',
													'/BankAccounts.php',
													'/BankAccountUsers.php',
													'/bankAccountBalance.php',
													'/TaxAccountsSettings.php',
													'/ChequeControlBook.php');

$MenuItems['FA']['Transactions']['Caption'] = array(_('Add New Asset'),
                                                    _('Add Existing  Asset'),
													_('Select an Asset'),
													_('Change Asset Location'),
													_('Depreciation Journal'),
													_('Create Preventive Maintenance Plan'));

$MenuItems['FA']['Transactions']['URL'] = array('/FixedAssetItems.php',
                                                '/FixedAssetExistingItems.php',
												'/SelectAsset.php',
												'/FixedAssetTransfer.php',
												'/FixedAssetDepreciation.php',
												'/CreatePreventiveMaintenancePlan.php');

$MenuItems['FA']['Reports']['Caption'] = array(	_('Asset Register'),
												_('Breakdown Maintenance Schedule'),
												_('Preventive Maintenance Schedule'),
												_('Maintenance Reminder Emails'));

$MenuItems['FA']['Reports']['URL'] = array(	'/FixedAssetRegister.php',
											'/MaintenanceBreakdownSchedule.php',
											'/MaintenancePreventiveSchedule.php',
											'/MaintenanceReminders.php');

$MenuItems['FA']['Maintenance']['Caption'] = array(	_('Fixed Asset Category Maintenance'),
													_('Add or Maintain Asset Locations'),
													_('Fixed Asset Breakdown Maintenance Tasks'),
													_('Fixed Asset Preventive Maintenance Tasks'));

$MenuItems['FA']['Maintenance']['URL'] = array(	'/FixedAssetCategories.php',
												'/FixedAssetLocations.php',
												'/MaintenanceBreakdownTasks.php',
												'/MaintenancePreventiveTasks.php');


$MenuItems['PC']['Transactions']['Caption'] = array(_('Petty Cash Requisition'),
													_('Head Of Department'),
													_('Controlling Department PRM'),
													_('Controlling Department HR'),
													_('Procurement Manager'),
													_('AIE Holder'),
													_('Finance CA\PM'),
													_('Paying Officer/Cashier'),
													_('Expenses Authorisation'));

$MenuItems['PC']['Transactions']['URL'] = array(
												'/Petty_CashRequisition.php',
												'/Imprest_DepartmentProcess.php',
												'/Imprest_ControllingDepartment_PRM.php',
												'/Imprest_ControllingDepartment_HR.php',
												'/Imprest_Procurement.php',
												'/Impressed_AI.php',
												'/Imprest_PMProcess.php',
												'/Impressed_FA.php',
												'/PcAuthorizeExpenses.php');

$MenuItems['PC']['Reports']['Caption'] = array(_('PC Tab General Report'),
                                               _('PC Expenses Analysis'), );

$MenuItems['PC']['Reports']['URL'] = array('/PcReportTab.php',
                                            '/PettycashAnalysis.php');

$MenuItems['PC']['Maintenance']['Caption'] = array(	_('Types of PC Tabs'),
													_('PC Tabs'),
													_('PC Expenses'),
													_('Expenses for Type of PC Tab'),
													_('Assign Cash to PC Tab'),);

$MenuItems['PC']['Maintenance']['URL'] = array(	'/PcTypeTabs.php',
												'/PcTabs.php',
												'/PcExpenses.php',
												'/PcExpensesTypeTab.php',
												'/PcAssignCashToTab.php',);

$MenuItems['system']['Transactions']['Caption'] = array(_('Company Preferences'),
														_('System Parameters'),
														_('Users Maintenance'),
														_('Maintain Security Tokens'),
														_('Access Permissions Maintenance'),
														_('Page Security Settings'),
														_('Currencies Maintenance'),
														_('Tax Authorities and Rates Maintenance'),
														_('Tax Group Maintenance'),
														_('Dispatch Tax Province Maintenance'),
														_('Tax Category Maintenance'),
														_('List Periods Defined'),
														_('Report Builder Tool'),
														_('View Audit Trail'),
														_('Geocode Maintenance'),
														_('Form Designer'),
														_('Web-Store Configuration'),
														_('SMTP Server Details'),
												       	_('Mailing Group Maintenance'));

$MenuItems['system']['Transactions']['URL'] = array('/CompanyPreferences.php',
													'/SystemParameters.php',
													'/WWW_Users.php',
													'/SecurityTokens.php',
													'/WWW_Access.php',
													'/PageSecurity.php',
													'/Currencies.php',
													'/TaxAuthorities.php',
													'/TaxGroups.php',
													'/TaxProvinces.php',
													'/TaxCategories.php',
													'/PeriodsInquiry.php',
													'/reportwriter/admin/ReportCreator.php',
													'/AuditTrail.php',
													'/GeocodeSetup.php',
													'/FormDesigner.php',
													'/ShopParameters.php',
													'/SMTPServer.php',
											       	'/MailingGroupMaintenance.php');

$MenuItems['system']['Reports']['Caption'] = array(	_('Sales Types'),
													_('Customer Types'),
													_('Supplier Types'),
													_('Supplier Group Types'),
													_('Credit Status'),
													_('Payment Terms'),
													_('Set Purchase Order Authorisation levels'),
													_('Payment Methods'),
													_('Sales People'),
													_('Sales Areas'),
													_('Shippers'),
													_('Sales GL Interface Postings'),
													_('COGS GL Interface Postings'),
													_('Freight Costs Maintenance'),
													_('Discount Matrix'));

$MenuItems['system']['Reports']['URL'] = array(	'/SalesTypes.php',
												'/CustomerTypes.php',
												'/SupplierTypes.php',
												'/SupplierGroupType.php',
												'/CreditStatus.php',
												'/PaymentTerms.php',
												'/PO_AuthorisationLevels.php',
												'/PaymentMethods.php',
												'/SalesPeople.php',
												'/Areas.php',
												'/Shippers.php',
												'/SalesGLPostings.php',
												'/COGSGLPostings.php',
												'/FreightCosts.php',
												'/DiscountMatrix.php');

$MenuItems['system']['Maintenance']['Caption'] = array(	_('Inventory Categories Maintenance'),
														_('Inventory Locations Maintenance'),
														_('Inventory Location Authorised Users Maintenance'),
														_('User Authorised Inventory Locations Maintenance'),
														_('Discount Category Maintenance'),
														_('Units of Measure'),
														_('MRP Available Production Days'),
														_('MRP Demand Types'),
														_('Maintain Internal Departments'),
														_('Maintain Departmental Sections'),
														_('Maintain Departmental Chief Officers'),
														_('Maintain Internal Stock Categories to User Roles'),
														_('Human Resource Authorised Users Maintenance'),
														_('Requisition Authorised Users Maintenance'),
														_('Maintenance Planning Authorised Users'),
														_('Label Templates Maintenance'));

$MenuItems['system']['Maintenance']['URL'] = array(	'/StockCategories.php',
													'/Locations.php',
													'/LocationUsers.php',
													'/UserLocations.php',
													'/DiscountCategories.php',
													'/UnitsOfMeasure.php',
													'/MRPCalendar.php',
													'/MRPDemandTypes.php',
													'/Departments.php',
													'/Section_Head.php',
													'/Chief_Officer.php',
													'/InternalStockCategoriesByRole.php',
													'/HumanResourceUserRoles.php',
													'/RequisitionUserRoles.php',
													'/MaintenanceUserRoles.php',
													'/Labels.php');

$MenuItems['Utilities']['Transactions']['Caption'] = array(	_('Change A Customer Code'),
															_('Change A Customer Branch Code'),
															_('Change A Supplier Code'),
															_('Change A Employee Number'),
															_('Change A Stock Category Code'),
															_('Change An Inventory Item Code'),
															_('Change A GL Account Code'),
															_('Change A Location Code'),
															_('Translate Item Descriptions'),
															_('Update costs for all BOM items, from the bottom up'),
															_('Re-apply costs to Sales Analysis'),
															_('Delete sales transactions'),
															_('Reverse all supplier payments on a specified date'),
															_('Update sales analysis with latest customer data'));

$MenuItems['Utilities']['Transactions']['URL'] = array(	'/Z_ChangeCustomerCode.php',
														'/Z_ChangeBranchCode.php',
														'/Z_ChangeSupplierCode.php',
														'/Z_ChangeEmployeeCode.php',
														'/Z_ChangeStockCategory.php',
														'/Z_ChangeStockCode.php',
														'/Z_ChangeGLAccountCode.php',
														'/Z_ChangeLocationCode.php',
														'/AutomaticTranslationDescriptions.php',
														'/Z_BottomUpCosts.php',
														'/Z_ReApplyCostToSA.php',
														'/Z_DeleteSalesTransActions.php',
														'/Z_ReverseSuppPaymentRun.php',
														'/Z_UpdateSalesAnalysisWithLatestCustomerData.php');

$MenuItems['Utilities']['Reports']['Caption'] = array(	_('Debtors Balances By Currency Totals'),
														_('Suppliers Balances By Currency Totals'),
														_('Show General Transactions That Do Not Balance'),
														_('List of items without picture'));

$MenuItems['Utilities']['Reports']['URL'] = array(	'/Z_CurrencyDebtorsBalances.php',
													'/Z_CurrencySuppliersBalances.php',
													'/Z_CheckGLTransBalance.php',
													'/Z_ItemsWithoutPicture.php');

$MenuItems['Utilities']['Maintenance']['Caption'] = array(	_('Maintain Language Files'),
															_('Make New Company'),
															_('Data Export Options'),
															_('Import Customers from .csv file'),
															_('Import Stock Items from .csv file'),
															_('Import Price List from .csv file'),
															_('Import Fixed Assets from .csv file'),
															_('Import GL Payments Receipts Or Journals From .csv file'),
															_('Create new company template SQL file and submit to webERP'),
															_('Re-calculate brought forward amounts in GL'),
															_('Re-Post all GL transactions from a specified period'),
															_('Purge all old prices'));

$MenuItems['Utilities']['Maintenance']['URL'] = array(	'/Z_poAdmin.php',
														'/Z_MakeNewCompany.php',
														'/Z_DataExport.php',
														'/Z_ImportDebtors.php',
														'/Z_ImportStocks.php',
														'/Z_ImportPriceList.php',
														'/Z_ImportFixedAssets.php',
														'/Z_ImportGLTransactions.php',
														'/Z_CreateCompanyTemplateFile.php',
														'/Z_UpdateChartDetailsBFwd.php',
														'/Z_RePostGLFromPeriod.php',
														'/Z_DeleteOldPrices.php');
?>
