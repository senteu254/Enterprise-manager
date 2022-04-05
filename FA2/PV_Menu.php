
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="bootstrap/css/bootstrap.css" rel="stylesheet">
<style>
		.navbar-default{
		opacity: .9;
		z-index: 999;
		}
		.dropdown:hover .dropdown-menu {
		display: block;
		}
		.panel-default{
		opacity: .9;
		}
		hr.message-inner-separator
{
    clear: both;
    margin-top: 10px;
    margin-bottom: 13px;
    border: 0;
    height: 1px;
    background-image: -webkit-linear-gradient(left,rgba(0, 0, 0, 0),rgba(0, 0, 0, 0.15),rgba(0, 0, 0, 0));
    background-image: -moz-linear-gradient(left,rgba(0,0,0,0),rgba(0,0,0,0.15),rgba(0,0,0,0));
    background-image: -ms-linear-gradient(left,rgba(0,0,0,0),rgba(0,0,0,0.15),rgba(0,0,0,0));
    background-image: -o-linear-gradient(left,rgba(0,0,0,0),rgba(0,0,0,0.15),rgba(0,0,0,0));
}

</style>
<style type="text/css">

#form-control {
  
  width: 100%;
  height: 34px;
  padding: 6px 12px;
  font-size: 14px;
  line-height: 1.42857143;
  color: #555;
  background-color: #fff;
  background-image: none;
  border: 1px solid #ccc;
  border-radius: 4px;
  -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
          box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
  -webkit-transition: border-color ease-in-out .15s, -webkit-box-shadow ease-in-out .15s;
       -o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
          transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
}
#form-control:focus {
  border-color: #66afe9;
  outline: 0;
  -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);
          box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);
}
#form-control::-moz-placeholder {
  color: #999;
  opacity: 1;
}
#form-control:-ms-input-placeholder {
  color: #999;
}
#form-control::-webkit-input-placeholder {
  color: #999;
}
#form-control[disabled],
#form-control[readonly],
fieldset[disabled] .form-control {
  cursor: not-allowed;
  background-color: #eee;
  opacity: 1;
}
textarea.form-control {
  height: auto;
}
.label2 {
  display: inline;
  padding: .2em .6em .3em;
  font-size: 75%;
  font-weight: bold;
  line-height: 1;
  color: #fff;
  text-align: center;
  white-space: nowrap;
  vertical-align: baseline;
  border-radius: .25em;
}

</style>

<div class="container-fluid" >
	<!--welcome user in menu-->
	<?php		
		$path='FA2/';
		$view = (isset($_GET['Ref']) && $_GET['Ref'] != '') ? $_GET['Ref'] : '';
								switch ($view) {
								case 'Farm_Description' :
									$content=$path.'Farm_Description.php';		
									break;
								case 'Farm_Serices' :
									$content=$path.'Farm_Services_Maintenance.php';		
									break;
								case 'Farm_Fields' :
									$content=$path.'FarmFields.php';
									break;
								case 'Aged_payment_voucher' :
									$content=$path.'Agedpv.php';
									break;															
								default :
									$content =$path.'Main_Menu.php';
									break;
									
							}
 
					?>
					
			<nav class="navbar navbar-default" role="navigation">
				  <div class="container-fluid">
					<!-- Brand and toggle get grouped for better mobile display -->
					<div class="navbar-header" >
					  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					  </button>
					  <strong><a class="navbar-brand" href="index.php?Application=FA2&Ref=Dashboard">Dashboard</a></strong>
					</div>
					
					<!-- Collect the nav links, forms, and other content for toggling -->
					<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<?php if($_SESSION['CanEditFlow'] == 1){ ?>
					<ul class="nav navbar-nav">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"> Farm Setup <span class="caret"></span></a>
								<ul class="dropdown-menu" role="menu">
								      <li><a href="index.php?Application=FA2&Ref=Farm_Fields">Farm Field Maintenance</a></li>
									  <li><a href="index.php?Application=FA2&Ref=Farm_Serices"> Item Source Maintenance</a></li>
									  <li><a href="index.php?Application=FA2&Ref=Farm_Description"> Farm Services Description</a></li>
								</ul>
						</li>
					</ul>
					<?php } ?>
				
					</div><!-- /.navbar-collapse -->
					
				  </div><!-- /.container-fluid -->
				</nav>
	</div>
