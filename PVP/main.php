<link rel="stylesheet" href="PVP/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="PVP/dist/css/AdminLTE.min.css">
	
	<?php
	$sql9="SELECT  * FROM  pvroles
		   WHERE authoriser='" . $_SESSION['UserID'] ."'";
				$result9 = DB_query($sql9);
$myrow9 = DB_fetch_array($result9);
 if($myrow9['level'] ==2 or $myrow9['level'] ==7){
 $query2 = "SELECT  * FROM payment_voucher,pvroles,pvlevel 
				WHERE payment_voucher.process_level=12 or payment_voucher.process_level=17
				AND pvlevel.levelcode=pvroles.level
				AND pvroles.authoriser='" . $_SESSION['UserID'] ."'
				AND payment_voucher.state=0
				GROUP BY payment_voucher.voucherid";
 }else{
 $query2 = "SELECT  * FROM payment_voucher,pvroles,pvlevel 
				WHERE payment_voucher.process_level=1".$myrow9['level']." 
				AND pvlevel.levelcode=pvroles.level
				AND pvroles.authoriser='" . $_SESSION['UserID'] ."'
				AND payment_voucher.state=0
				GROUP BY payment_voucher.voucherid";
 }			 			
       $count2 = DB_num_rows(DB_query($query2));
	   
 if($myrow9['level'] ==2 or $myrow9['level'] ==7){
   $query1 = "SELECT  * FROM payment_voucher,pvroles,pvlevel 
				WHERE payment_voucher.process_level=pvlevel.levelcode 
				AND pvlevel.levelcode=pvroles.level
				AND pvroles.level=2
				AND pvroles.authoriser='" . $_SESSION['UserID'] ."'
				AND payment_voucher.state=0";
   }else{
   $query1 = "SELECT  * FROM payment_voucher,pvroles,pvlevel 
				WHERE payment_voucher.process_level=pvlevel.levelcode 
				AND pvlevel.levelcode=pvroles.level
				AND pvroles.authoriser='" . $_SESSION['UserID'] ."'
				AND payment_voucher.state=0";
   }
       $count = DB_num_rows(DB_query($query1));
    $query3 = "SELECT  * FROM payment_voucher,pvroles,pvlevel 
				WHERE payment_voucher.process_level>pvlevel.levelcode 
				AND pvlevel.levelcode=pvroles.level
				AND pvroles.authoriser='" . $_SESSION['UserID'] ."'
				AND payment_voucher.state=0";
	 $count3 = DB_num_rows(DB_query($query3));
	  $query5 = "SELECT  * FROM payment_voucher 
	             WHERE process_level>9 
				 AND process_level <19";
	 $count5 = DB_num_rows(DB_query($query5));
	 
	  $query7 = "SELECT  * FROM payment_voucher 
	             WHERE process_level=7
				 AND state=0";
	 $count7 = DB_num_rows(DB_query($query7));
	 
	 $query6 = "SELECT  * FROM payment_voucher
				WHERE process_level=0";
				
       $count6 = DB_num_rows(DB_query($query6));
	    $amended = "SELECT  * FROM payment_voucher,pvroles,pvlevel 
				WHERE payment_voucher.process_level=pvlevel.levelcode 
				AND pvlevel.levelcode=pvroles.level
				AND pvroles.authoriser='" . $_SESSION['UserID'] ."'
				AND payment_voucher.state=0
				AND review_rejected=2";
	 $c_amend = DB_num_rows(DB_query($amended));
	?>
		<div class="container-fluid">
		<div class="panel-body">
		<div class="row">
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
		   <?php
		 $sql9="SELECT *  FROM pvroles  
				WHERE authoriser='" . $_SESSION['UserID'] ."'  ";
				$result9 = DB_query($sql9);
$myrow9 = DB_fetch_array($result9);
	if($myrow9['level']==0){?>
		<a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')."?Application=PVM&Ref=default&Link=inbox_Accountant"; ?>">
          <div class="alert alert-danger">
		  <div class="icon" style="float:right">
              <i class="fa fa-trash" style="font-size:50px"></i>
            </div>
            <div class="inner">
              <strong style="font-size:20px"><?php echo $count5; ?></strong>
              <p>Rejected PV</p>
            </div>
          
          </div>
        </div>
		</a>
		  <!-- ./col -->
		    <div class="col-lg-3 col-xs-6">
          <!-- small box -->
		  <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')."?Application=PVM&Ref=default&Link=drafted_PV"; ?>">
          <div class="alert alert-success" style="height:83px">
		  <div class="icon" style="float:right">
             <i class="fa fa-cogs" style="font-size:50px"></i>
            </div>
            <div class="inner">
              <strong style="font-size:20px"><?php echo $count6; ?></strong>
              <p>Drafted PV</p>
            </div>
            <div class="icon">
              <i class="ion ion-person-add"></i>
            </div>
           
          </div>
        </div>
		</a>
		<?php }else{ ?>
       <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')."?Application=PVM&Ref=default&Link=inbox"; ?>">
          <div class="alert alert-info">
		  <div class="icon" style="float:right">
              <i class="fa  fa-envelope-o" style="font-size:50px"></i>
            </div>
            <div class="inner">
              <strong style="font-size:20px"><?php echo $count; ?></strong>
              <p>New PV</p>
            </div>
          
          </div>
        </div>
		</a>
		  <!-- ./col -->
	  <?php 
	  } 
	  ?>
	  <?php if($myrow9['level']==7 or $myrow9['level']==2){?>
	     <div class="col-lg-3 col-xs-6">
          <!-- small box -->
		  <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')."?Application=PVM&Ref=default&Link=cheque"; ?>">
          <div class="alert alert-success" style="height:83px">
		   <div class="icon" style="float:right">
             <i class="fa  fa-envelope-o" aria-hidden="true" style="font-size:50px"></i>
            </div>
            <div class="inner">
              <strong style="font-size:20px"><?php echo $count7; ?></strong>
              <p>Confirm Cheque</p>
            </div>
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>
            
          </div>
        </div>
		</a>
        <!-- ./col --> 
		<?php 
		}
		?>
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
		  <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')."?Application=PVM&Ref=default&Link=Actioned_PV"; ?>">
          <div class="alert alert-warning" style="height:83px">
		   <div class="icon" style="float:right">
             <i class="fa fa-star" aria-hidden="true" style="font-size:50px"></i>
            </div>
            <div class="inner">
              <strong style="font-size:20px"><?php echo $count3; ?></strong>
              <p>Actioned PV</p>
            </div>
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>
            
          </div>
        </div>
		</a>
        <!-- ./col -->
        <?php if($myrow9['level']>=1){?>
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
		  <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')."?Application=PVM&Ref=default&Link=Rejected_PV"; ?>">
          <div class="alert alert-danger">
		  <div class="icon" style="float:right">
              <i class="fa fa-trash" style="font-size:50px"></i>
            </div>
            <div class="inner">
             <strong style="font-size:20px"><?php echo $count2; ?></strong>
              <p>Rejected PV</p>
            </div>
            <div class="icon">
            
            </div>
          </div>
        </div>
		</a>
        <!-- ./col -->
		<?php 
		}
		?>
      </div>

	</div>
	
</div> <!--end container-->
<!--new row-->

	<!--vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv-->
		<div class="row">
      
	
	  <?php if($myrow9['level']>0){?>
	     <div class="col-lg-3 col-xs-6">
          <!-- small box -->
		  <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')."?Application=PVM&Ref=default&Link=Amended_PV"; ?>">
          <div class="alert alert-info2" style="margin-left:30px;color:#FFFFFF;height:83px">
		   <div class="icon" style="float:right">
             <i class="fa  fa-envelope-o" aria-hidden="true" style="font-size:50px"></i>
            </div>
            <div class="inner">
              <strong style="font-size:20px"><?php echo $c_amend; ?></strong>
              <p>Amended PV</p>
            </div>
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>
            
          </div>
        </div>
		</a>
		
		
        <!-- ./col --> 
		<?php 
		}
		?>
        
       
		 
    
  </div>
	
</div> <!--end container-->


<!--new row-->


