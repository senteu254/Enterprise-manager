<?php
ob_start();

require('pdf/fpdf.php');
//unset($_SESSION['comname'],$_SESSION['slogan'],$_SESSION['address'],$_SESSION['logo']);
class PDF extends FPDF
{
// Load data
function LoadData($file)
{
	// Read file lines
	$lines = file($file);
	$data = array();
	foreach($lines as $line)
		$data[] = explode(';',trim($line));
	return $data;
}

// Page header
function Header()
{
	$this->Ln();
	$this->Image(''.$_SESSION['logo'].'',20,10,25,'L');
    $this->SetFont('Arial','B',15);
    $this->Cell(0,10,''.$_SESSION['comname'].'',0,0,'C');
    $this->Ln();
    $this->SetFont('Arial','B',10);
    $this->Cell(0,3,''.$_SESSION['slogan'].'',0,0,'C');
    $this->Ln();
    $this->Cell(0,5,''.$_SESSION['address'].'',0,0,'C');
    $this->SetFont('Arial','B',12);
	 $this->Ln(5);
    $this->Cell(0,10,'ACTIVE CONTRACTS REPORT',0,0,'C');
	$this->Image('css/images/spacer.png',65,37,'C');
    $this->Ln(15);
}

// Page footer
function Footer()
{
    // Position at 1.5 cm from bottom
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    // Page number
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	$this->Cell(0,10,'Printed On: '.date("F j, Y h:i:s A"),0,0,'R');
}
// Colored table
function FancyTable($header, $data)
{
	// Colors, line width and bold font
	$this->SetFillColor(47,79,79);
	$this->SetTextColor(255);
	$this->SetDrawColor(128,128,128);
	$this->SetLineWidth(.3);
	$this->SetFont('','B');
	// Header
	$w = array(25, 65, 25, 25, 25, 20);
	for($i=0;$i<count($header);$i++)
		$this->Cell($w[$i],7,$header[$i],1,0,'C',true);
	$this->Ln();
	// Color and font restoration
	$this->SetFillColor(224,235,255);
	$this->SetTextColor(0);
	$this->SetFont('');
	// Data
	$fill = false;
	$group = null;
	while($row = DB_fetch_array($data))
{
    if($row['SupplierID'] != $group)
    {
        $this->SetFont('','B');
		$this->Cell(array_sum($w),6,$row['Name'],1,0,'C');
		$this->Ln();
        $group = $row['SupplierID'];
		$fill = false;
		
    }
	$this->SetFont('');
	
		$start=date("M d, Y", strtotime($row['Begin_Date']));
		$end=date("M d, Y", strtotime($row['End_Date']));
		$truncated = (strlen($row['Contract_Name']) > 30) ? substr($row['Contract_Name'], 0, 30) . '...' : $row['Contract_Name'];
		$this->Cell($w[0],6,$row['Contract_Number'],'LR',0,'L',$fill);
		$this->Cell($w[1],6,$truncated,'LR',0,'L',$fill);
		$this->Cell($w[2],6,$start,'LR',0,'R',$fill);
		$this->Cell($w[3],6,$end,'LR',0,'R',$fill);
		$this->Cell($w[4],6,number_format($row['Amount'], 2),'LR',0,'R',$fill);
		$this->Cell($w[5],6,$row['Currency'],'LR',0,'R',$fill);
		$this->Ln();
		
		$fill = !$fill;
	}
		// Closing line
		$this->Cell(array_sum($w),0,'','T');
		$this->Ln();	
}
}
// Column headings
//require_once('inc/config.php');
$SQL = "SELECT  * FROM 
							contract_details a
								inner join
							contract_assignment b
								on a.ContractID = b.ContractID
								inner join 
							suppliers c
								on b.SupplierID = c.SupplierID
						";
	//$data = DB_query($SQL);

$header = array('Contract No.', 'Contract Name', 'Begin Date', 'End Date', 'Amount(L.C)', 'Currency');

	$SQL = "SELECT * FROM company_preference";
	$res = DB_query($SQL);
	while($rows=DB_fetch_array($res)){
	$_SESSION['comname']=$rows['Name'];
	$_SESSION['slogan']=$rows['Slogan'];
	$_SESSION['address']=$rows['Address'];
	$_SESSION['logo']=$rows['Logo'];
	
	}

// Instanciation of inherited class
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',10);
$pdf->FancyTable($header,$data);
		
$pdf->Output();
?>
