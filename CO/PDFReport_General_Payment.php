<?php
ob_start();
   session_start();
    if(!isset($_SESSION['Username'])){
         header("Location: login.php");
    }
require('pdf/fpdf.php');

unset($_SESSION['comname'],$_SESSION['slogan'],$_SESSION['address'],$_SESSION['logo']);

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
    $this->Cell(0,10,'CONTRACTS PAYMENT REPORT',0,0,'C');
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
function FancyTable($header, $data,$qry)
{
	// Colors, line width and bold font
	$this->SetFillColor(47,79,79);
	$this->SetTextColor(255);
	$this->SetDrawColor(128,128,128);
	$this->SetLineWidth(.3);
	$this->SetFont('','B');
	// Header
	$w = array(10, 110, 30, 30);
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
	while($row = mysqli_fetch_array($data))
{
    if($row['Contract_Name'] != $group)
    {
        $this->Cell(array_sum($w),6,$row['Contract_Name'],1,0,'C');
		$this->Ln();
        $group = $row['Contract_Name'];
		$fill = false;
		
    }
		$truncated = (strlen($row['Description']) > 85) ? substr($row['Description'], 0, 85) . '...' : $row['Description'];
		$date=date("M d, Y", strtotime($row['Date_Paid']));
		$this->Cell($w[0],6,$row['PaymentID'],'LR',0,'L',$fill);
		$this->Cell($w[1],6,$truncated,'LR',0,'L',$fill);
		$this->Cell($w[2],6,$date,'LR',0,'R',$fill);
		$this->Cell($w[3],6,number_format($row['Amount_LC'], 2),'LR',0,'R',$fill);
		$this->Ln();
		
		$fill = !$fill;
	}
		// Closing line
		$this->Cell(array_sum($w),0,'','T');
		$this->Ln();	
}
}
// Column headings
require_once('inc/config.php');
$SQL = "SELECT a.ContractID,a.PaymentID,a.Amount_LC,a.Date_Paid,a.Description,b.Contract_Name FROM contract_payment a INNER JOIN contract_details b ON a.ContractID=b.ContractID";
	$data = mysqli_query($conn,$SQL) or die ("Error Query [".$SQL."]");

$qry = mysqli_query($conn," SELECT SUM(Amount_LC) AS total_paid FROM contract_payment");
$header = array('P.ID', 'Description', 'Date Paid', 'Amnt (L.C)');

	$SQL = "SELECT * FROM company_preference";
	$res = mysqli_query($conn,$SQL);
	while($rows=mysqli_fetch_array($res)){
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
$pdf->FancyTable($header,$data,$qry);
		
$pdf->Output();
?>
