						  
<script language="javascript" src="CO/js/datepickr.js"></script>
<div id="sidebar">
				
				<!-- Box -->
				<div class="box" style="margin-top:-17%;">
					
					<!-- Box Head -->
					<div class="box-head" style="background:#337ab7; color: white; border-radius: 7px; no-repeat 0 0; padding:0 0 0 15px;">
						<h2>Management</h2>
					</div>
					<!-- End Box Head-->
					<div class="box-content">
						<a href="PDFReport_Active_Contracts.php" class="add-button"><span>Print Report</span></a>
						<div class="cl">&nbsp;</div></div>
					<div class="box-content">
						<a href="<?php echo $mainlink; ?>Contract" class="add-button"><span>Add new Contract</span></a>
						<div class="cl">&nbsp;</div>
						
						<p class="select-all"><input name="users[]" value="<?=DB_result($objQuery,$i,"Acc_ID");?>" onClick="toggle(this)"  type="checkbox" class="checkbox" />
						<label>select all</label></p>
						<p><a href="javascript:document.forms[0].submit();">Delete Selected</a></p>
						
						<!-- Sort -->
						<div class="sort">
							<label>Sort by</label>							
							<form action=""  method="post">						
						<!-- Form -->
						<div class="form">
					
						  <input class="field" name="fdate" id="datepickr" size="14" placeholder="Sort by Date" readonly="readonly" value="" type="text" required/>

						<p>
							<input type="submit" on name="Submit" value="Submit" />
							</p>
						</div>
						<!-- End Form Buttons -->
					</form>
							
						</div>
						<!-- End Sort -->
						
					</div>
				</div>
				<!-- End Box -->
			</div>
