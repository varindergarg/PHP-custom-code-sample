<!DOCTYPE html>
<html>
	<head>
		<title>ISL</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<script src="https://kit.fontawesome.com/a076d05399.js"></script>
		<link rel="stylesheet" type="text/css" href="Public/css/style.css">
		<link rel="stylesheet" type="text/css" href="Public/css/responsive.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	</head>
	<body>
		<!------- 		FILTER PAGE	BEGIN	    ------->
		<div class="filter-page">
			<div class="header-filter">
				<img src="Public/images/incentive-logo.jpg" alt="incentive-logo" class="incentive-logo"/>
			</div>
			<!-- filter-form -->
			<div class="filter-form">
				<form class="filter-forms" id="filter_form" action="<?php echo base_url; ?>/?class=Home&method=search" method="post">
					<div class="form-groups">
						<label for="reward_program_code">Reward Program Code</label>
						<div class="right-frm">
							<select name="reward_program_code">
								<option value="">Select Reward Program</option>
								<?php if(!empty($data['reward_program'])){
									foreach($data['reward_program'] as $program_code){
										echo '<option value="'.$program_code['reward_program_code'].'">'.$program_code['reward_program_code'].'</option>';
									}
								}?>
							</select>
						</div>
					</div>
					
					<div class="form-groups">
						<label for="Account Type">Account Type</label>
						<div class="right-frm">
							<select name="account_type">
								<option value="">Select Account Type</option>
								<?php if(!empty($data['account_type'])){
									foreach($data['account_type'] as $acc_type){
										echo '<option value="'.$acc_type['account_type_id'].'">'.$acc_type['account_name'].'</option>';
									}
								}?>
							</select>
						</div>
					</div>
					
					<div class="form-groups">
					  <label for="Date-Range">Month/Year</label>
					  <ul class="month--year_sec">
						<li class="month--dropdown">
							<select name="month">
							<option value="">- Month -</option>
							<option value="01">January</option>
							<option value="02">Febuary</option>
							<option value="03">March</option>
							<option value="04">April</option>
							<option value="05">May</option>
							<option value="06">June</option>
							<option value="07">July</option>
							<option value="08">August</option>
							<option value="09">September</option>
							<option value="10">October</option>
							<option value="11">November</option>
							<option value="12">December</option>
							</select>
						</li>
						<li class="year--dropdown">
							<select name="year">
							<option value="">- Year -</option>
							<option value="2020">2020</option>
							<option value="2019">2019</option>
							<option value="2018">2018</option>
							<option value="2017">2017</option>
							<option value="2016">2016</option>
							<option value="2015">2015</option>
							<option value="2014">2014</option>
							<option value="2013">2013</option>
							<option value="2012">2012</option>
							<option value="2011">2011</option>
							<option value="2010">2010</option>
							<option value="2009">2009</option>
							<option value="2008">2008</option>
							<option value="2007">2007</option>
							<option value="2006">2006</option>
							<option value="2005">2005</option>
							<option value="2004">2004</option>
							<option value="2003">2003</option>
							<option value="2002">2002</option>
							<option value="2001">2001</option>
							<option value="2000">2000</option>
							<option value="1999">1999</option>
							<option value="1998">1998</option>
							<option value="1997">1997</option>
							<option value="1996">1996</option>
							<option value="1995">1995</option>
							<option value="1994">1994</option>
							<option value="1993">1993</option>
							<option value="1992">1992</option>
							<option value="1991">1991</option>
							<option value="1990">1990</option>
							<option value="1989">1989</option>
							<option value="1988">1988</option>
							<option value="1987">1987</option>
							<option value="1986">1986</option>
							<option value="1985">1985</option>
							<option value="1984">1984</option>
							<option value="1983">1983</option>
							<option value="1982">1982</option>
							<option value="1981">1981</option>
							<option value="1980">1980</option>
							<option value="1979">1979</option>
							<option value="1978">1978</option>
							<option value="1977">1977</option>
							<option value="1976">1976</option>
							<option value="1975">1975</option>
							<option value="1974">1974</option>
							<option value="1973">1973</option>
							<option value="1972">1972</option>
							<option value="1971">1971</option>
							<option value="1970">1970</option>
							<option value="1969">1969</option>
							<option value="1968">1968</option>
							<option value="1967">1967</option>
							<option value="1966">1966</option>
							<option value="1965">1965</option>
							<option value="1964">1964</option>
							<option value="1963">1963</option>
							<option value="1962">1962</option>
							<option value="1961">1961</option>
							<option value="1960">1960</option>
							<option value="1959">1959</option>
							<option value="1958">1958</option>
							<option value="1957">1957</option>
							<option value="1956">1956</option>
							<option value="1955">1955</option>
							<option value="1954">1954</option>
							<option value="1953">1953</option>
							<option value="1952">1952</option>
							<option value="1951">1951</option>
							<option value="1950">1950</option>
							<option value="1949">1949</option>
							<option value="1948">1948</option>
							<option value="1947">1947</option>
							<option value="1946">1946</option>
							<option value="1945">1945</option>
							<option value="1944">1944</option>
							<option value="1943">1943</option>
							<option value="1942">1942</option>
							<option value="1941">1941</option>
							<option value="1940">1940</option>
							<option value="1939">1939</option>
							<option value="1938">1938</option>
							<option value="1937">1937</option>
							<option value="1936">1936</option>
							<option value="1935">1935</option>
							<option value="1934">1934</option>
							<option value="1933">1933</option>
							<option value="1932">1932</option>
							<option value="1931">1931</option>
							<option value="1930">1930</option>
							</select>
						</li>
					  </ul>
					</div>
					
						<div class="form-groups">
			
							<div class="right-frm-filter">
								<button type="button" class="send-report-btn" data-toggle="modal" data-target="#filters">More Filters</i></button>
							</div>
						</div>
						<!--<span class="add-more"><i class="far fa-plus-square"></i></span>-->
					
						<div class="form-groups" style="display:none;" id="region_id">
							<label for="Region">Region</label>
							<div class="right-frm">
								<select name="region_id">
									<option value="">Select Region</option>
									<?php if(!empty($data['country_region'])){
										foreach($data['country_region'] as $country_region){
											echo '<option value="'.$country_region['region_id'].'">'.$country_region['region_name'].'</option>';
										}
									}?>
								</select>
							</div>
						</div>
						<div class="form-groups" style="display:none;" id="tier_id">
							<label for="Tier">Tier</label>
							<div class="right-frm">
								<select name="tier_id">
									<option value="">Select Tier</option>
									<?php if(!empty($data['tiers'])){
										foreach($data['tiers'] as $tiers){
											echo '<option value="'.$tiers['tier_id'].'">'.$tiers['tier_description'].'</option>';
										}
									}?>
								</select>
							</div>
						</div>
						<div class="form-groups" style="display:none;" id="member_status">
							<label for="Member Status">Member Status</label>
							<div class="right-frm">
								<select name="member_status">
									<option value="">Select Member Status</option>
									<?php if(!empty($data['member_status'])){
										foreach($data['member_status'] as $member_status){
											echo '<option value="'.$member_status['member_status_id'].'">'.$member_status['status'].'</option>';
										}
									}?>
								</select>
							</div>
						</div>
						<div class="form-groups" style="display:none;" id="member_type">
							<label for="Member Type">Member Type</label>
							<div class="right-frm">
								<select name="member_type">
									<option value="">Select Member Type</option>
									<?php if(!empty($data['member_type'])){
										foreach($data['member_type'] as $member_type){
											echo '<option value="'.$member_type['member_type_id'].'">'.$member_type['type_description'].'</option>';
										}
									}?>
								</select>
							</div>
						</div>
					 
					<div class="text-center">
						<button type="submit" value="Submit" class="submit-btnn">Submit</button>
					</div>
				</form>
			</div><!-- End filter-form -->
		</div>
		
		<div class="modal fade" id="filters" role="dialog">
			<div class="modal-dialog cust-dialog">
				<!-- Modal content-->
				<div class="modal-content custom-popup">
					<div class="modal-header">
						<button type="button" class="close-btns" data-dismiss="modal">&times;</button>
					</div>
					<div class="modal-body">
						<div class="form-groups">
							<label class="container-a">Region
								<input type="checkbox" class="filters" value="region_id">
								<span class="checkmark"></span>
							</label>
							
						</div>
						<div class="form-groups">
							<label class="container-a">Tier
								<input type="checkbox" class="filters" value="tier_id">
								<span class="checkmark"></span>
							</label>
							
						</div>
						<div class="form-groups">
							<label class="container-a">Member Status
								<input type="checkbox" class="filters" value="member_status">
								<span class="checkmark"></span>
							</label>
							
						</div>
						<div class="form-groups">
							<label class="container-a">Member Type
								<input type="checkbox" class="filters" value="member_type">
								<span class="checkmark"></span>
							</label>
							
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<!-------	  END FILTER PAGE		  ------->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
		<script src="https://cdn.jsdelivr.net/jquery/1.12.4/jquery.min.js"></script>
		<script src="https://cdn.jsdelivr.net/jquery.validation/1.15.1/jquery.validate.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
		<script>
			
			$(document).ready(function(){
				
				$(".add-more").click(function(){
					$(".extra-flieds").show();
				});	
				
				$("#filter_form").validate({
					rules:
					{
						reward_program_code:
						{
							required: true
						},
						account_type:
						{
							required: true
						},
						month:
						{
							required: true
						},
						year:
						{
							required: true
						}
					},
					messages:
					{
						reward_program_code:
						{
							required: "Please enter reward program code."
						},
						account_type:
						{
							required: "Please enter account type."
						},
						month:
						{
							required: "Please enter month."
						},
						year:
						{
							required: "Please enter year."
						}
					},
				}); 
				
				$('.filters').click(function(){
					var filter_id = $(this).val();
					if ($(this).prop('checked') == true){ 
						$("#"+filter_id).show();
					}else{
						$('select[name="'+filter_id+'"]').val('');
						$("#"+filter_id).hide();
					}
				})
				
			});
			
		</script>
	</body>
</html>