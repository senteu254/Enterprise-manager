
<form enctype="multipart/form-data" method="post" class="form-horizontal">
<?php

/*$page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
 
$per_page = 30; 
$startpoint = ($page * $per_page) - $per_page;*/




						$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
						$sql12 = " SELECT  *,c.stockid as groupid,b.units, c.description as desk FROM farmproduction a
									INNER JOIN farmproductionitems b ON a.Fid=b.fid
									INNER JOIN farmdescriptions c ON b.stockid=c.description_Id
									INNER JOIN stockmaster d ON c.stockid=d.stockid
									LEFT JOIN farmitemsource e ON e.source_Id=b.source
									ORDER BY d.stockid";

						$SearchResult = DB_query($sql12,$ErrMsg,$DbgMsg);
						$num_rows = DB_num_rows($SearchResult);
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	?>
					<fieldset>
						<div class="box-body no-padding">
              <div class="mailbox-controls">
                <!-- Check all button -->
				<div class="row">
                <div class="col-xs-1">
                <!-- /.btn-group -->
                <!--<a href="index.php?Application=?Application=FA2&Ref=Dashboard"><button type="button" title="Refresh" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button></a>-->
				</div>
				<div class="col-xs-4">
				<form enctype="multipart/form-data" method="post">
			<!--	<div class="input-group input-group-sm">
                <input type="text" placeholder="Search item..." id="myInput" onkeyup="myFunction()" name="Searchfield" value="<?php //echo isset($_POST['Searchfield']) ? $_POST['Searchfield'] : ""; ?>" class="form-control">
                    <span class="input-group-btn">
                      <button type="submit" class="btn btn-info btn-flat"><i class="fa fa-search"></i></button>
                    </span>
              </div> -->
			  </form>
			  </div>
			  <div class="col-xs-7">
					<div class="pull-right">
				<div class="btn-group">
                  </div>
                  <!-- /.btn-group -->
                </div>
                <!-- /.pull-right -->
				</div>

              </div>
              <div class="table-responsive mailbox-messages">
			  
                <table id="myTable2"  style="width:110%;"class="table table-hover table-striped">
				<thead>
				<th>Code</th>
				<th>Item Description</th>
				<th>Source</th>
				<th>Units</th>
				<th>Unitcost</th>
				<th>Quantity</th>
				<th>Area Covered</th>
				<th>Total Cost</th>
				</thead>
                  <tbody>
				<?php
				$group=NULL;
		$AmountGroup =0;
		$totals = 0;
		while (($myrow = DB_fetch_array($SearchResult))) { 
			$Totalcost=($myrow['unitcost']*$myrow['quantity']);
			
         /*displayin the data */
		/*if($myrow['groupid']!= $group){
		if($AmountGroup>0){
		echo '<tr><td></td><td>Total :</td><td></td><td></td><td></td><td></td><td></td><td>'.locale_number_format($totals, 2).'</td></tr>';
		 }
		
		echo '<tr><td></td><td><td></td></td><td></td><td>'.$myrow['description'].'</td><td></td><td></td><td></td></tr>';
		 $group = $myrow['groupid'];
		$totals = 0;
		 
		 $So= DB_query("SELECT sum((unitcost * quantity)) AS `SUM_TOTAL` 
		 				FROM  farmproductionitems b
						INNER JOIN farmdescriptions c ON b.stockid=c.description_Id
						where c.stockid='".$myrow['groupid']."'
						GROUP BY c.stockid");      
		 $myrow2 = DB_fetch_array($So);
		 $totals = $myrow2['SUM_TOTAL'];
		 }*/
		 
		
		echo'<tr>
			     <td>' . $myrow['description_Id'] . '</td>
				<td>' . $myrow['desk'] . '</td>
				<td>' . $myrow['source_Name'] . '</td>
				<td>' . $myrow['units'] . '</td>
				<td>' . locale_number_format($myrow['unitcost'],2) . '</td>
				<td>' . $myrow['quantity'] . '</td>
				<td>' .$myrow['areacovered']. '</td>
				<td class="number">' . locale_number_format($Totalcost,2) . '</td>
				</tr>';
			
			$z++;
			$AmountGroup++;
/*
			$j++;

			if ($j == 20 AND ($RowIndex + 1 != $_SESSION['DisplayRecordsMax'])) {
				$j = 1;
				echo $TableHeader;
			}
			*/
			//$RowIndex = $RowIndex + 1;
			//end of page full new headings if
		}
				?>
                  </tbody>
                </table>
                <!-- /.table -->
              </div>
              <!-- /.mail-box-messages -->
            </div>
	 	</fieldset>
		</form>
 