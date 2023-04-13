<div id="reportPage">
<?php
	$total_member_per_status = array();
	foreach($data['member_status_types'] as $key=>$type){
		$status_total_member = $this->model->getMemberCountPerStatus($type['member_status_id'], $_POST);
		$status_total_member = pg_fetch_assoc($status_total_member);
		$total_member_per_status[$key]['count']  = $status_total_member['total_member'];
		$total_member_per_status[$key]['status'] = $type['status'];
	}
    // HIGGSY update
    $reward_program_name    = $this->model->getRewardProgramName($_POST['reward_program_code']);
    $closing_balance        = $this->model->getClosingBalance($_POST['account_type'], $_POST['month'], $_POST['year']);

?>
<!DOCTYPE html>
<html>
	<head>
		<title>IC Automation</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<script src="https://kit.fontawesome.com/a076d05399.js"></script>
		<link rel="stylesheet" type="text/css" href="Public/css/style.css">
		<link rel="stylesheet" type="text/css" href="Public/css/responsive.css">
		<style>
			.email-li:before {
				background-color: #92d050;
				position: absolute;
				left: -15px;
				top: 4px;
				height: 9px;
				width: 9px;
				content: "";
			}
			.call-li:before {
				background-color:#fbc003;
				position: absolute;
				left: -15px;
				top: 4px;
				height: 9px;
				width: 9px;
				content: "";
			}
			.login-li:before {
				background-color:#c00000;;
				position: absolute;
				left: -15px;
				top: 4px;
				height: 9px;
				width: 9px;
				content: "";
			}
			@page {
				margin:30px 7.5px;
			}
			body{
				padding:0 0px ;
			}
		</style>
	</head>
<body>
<!------- 		PDF-page	    ------->
	<table style="margin:0 auto; table-layout: fixed;  background-color:#fff;" width="100%"  cellpadding="0" border="0" align="center">
	 <tbody>
		<tr>
		  <td>
			<table style="margin:5px auto 0" width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td>
						<!----	 main-header begin	 ---->
						<table style="border-bottom:5px solid #92D050 ; margin:0 auto;" width="100%" align="center">
							<tr>
								<td style="padding:13px 0" width="358px">
								   <a href="#"><img src="Public/images/hot-reward-logo.jpg" alt="program-logo" class="program-logo" style="max-width:106px"/></a>
								</td>
								<td style="padding:13px 0 ;font-family:'FranklinBold';" align="center" width="358px">
									<table style="width:358px; text-align:center;" align="center">
									  <tr>
										<td style="font-size:20px; text-align:center; font-family:'FranklinBold ;text-transform:uppercase; color:#585858; margin-top:2px; margin-bottom: 0;"><?php echo $reward_program_name; ?> Dashboard</td>	
									   </tr>
									   <tr>
										<td style=" font-size:12px; text-align:center; font-family:'FranklinBold ; font-weight:500;  line-height:4px ;color:#585858; margin-top:1px; margin-bottom:0;"><?php echo $month_name = date("F", mktime(0, 0, 0, $_POST['month'], 10)); ?> <?php echo $_POST['year']; ?></td>
										</tr>
									</table>
								</td>
								<td style="padding:13px 0" align="right" width="358px">
									<a href="#"><img src="Public/images/incentive-logo.jpg" alt="incentive-logo" class="incentive-logo" style="max-width:106px"/></a>	
								</td>
							</tr>
						</table><!----	 main-header End	 ---->
					</td>
				</tr>
			</table>
			</td>
		</tr>
		<!---- second-row  ---->
		<tr>
			<td>
				<!----	 INNER-TABLE	 ---->
				<table style="margin-top:7px; margin-left:2px;"  width="99.8%"  >
					<tr>
					<!-- member-status-table begin -->
						<td style="width:161px; border:1px solid #bfbfbf; padding:6px;">
							<table style="margin-bottom:28px; text-align:center;" align="center" >
								<td  align="center" style="font-size:12px; border:0px; font-family:'FranklinBold'; color:#585858; text-align:center;  margin-top:2px;">Members per Status</td>	
							</table>
							<table style="border:1px solid #c4c4c4; width:100%; border-collapse:collapse; margin-top:0px;" width="100%" cellpadding=" 0;">		  
							    <tbody>
									<?php $total_member = 0;
										if(!empty($total_member_per_status)){
										
										foreach($total_member_per_status as $total_count){
											$total_member += $total_count['count'];
											?>
											<tr>
												<td style="font-size:9px;  font-family:'FranklinGothic-Book'; border:1px solid #c4c4c4; padding: 4.3px 5px;"><?php echo $total_count['status']; ?></td>
												<td style="font-size: 10px;font-family:'FranklinGothic-Book'; text-align:center; border:1px solid #c4c4c4; padding: 4.3px 5px;"><?php echo number_format($total_count['count']); ?></td>
											</tr>
										<?php	}
									}?>
									
									<tr>
										<td style="font-size:9px; font-family:'FranklinGothic-Book'; border:1px solid #c4c4c4; padding: 4.3px 5px;">Total</td>
										<td style="font-size:10px;  font-family:'FranklinGothic-Book'; border:1px solid #c4c4c4; text-align:center; padding: 4.3px 5px;"><?php echo number_format($total_member); ?></td>
									 </tr>
								</tbody>
							</table>
						</td><!-- member-status-table begin -->
						<td style="width:5px">
						</td>
						<!----	program-overview-table begin	---->
						<td style="width:920px; border:1px solid #bfbfbf; padding:0px;">
							<table align="center">
								<td style="font-size:13px; border:0px; font-family:'FranklinBold'; color:#585858; text-align:center; margin-bottom:8px; margin-top:2px;">Program Overview</td>
							</table>
							<table class="program-over-table-1"  style="width:920px"  border="1px solid #dfdfdf" >
								<thead>
									<td style="border: 1px solid #bfbfbf;"></td>
									<?php
										$selected_month = $_POST['year'].'-'.$_POST['month'].'-01';
										$selected_month = strtotime("$selected_month -11 month");
										$this_month = mktime(0, 0, 0, date('m',$selected_month), 1, date('Y',$selected_month));
										for ($i = 0; $i < 12; $i++) {
											$class = '';
											if($i == 11){
												$class = 'class="td-active"';
											}
										?>
										<td style="font-family:'FranklinBold'; padding:4px 1px; text-align:center; border: 1px solid #bfbfbf; font-size:9px;" <?php echo $class; ?>><?php echo date('M.Y', strtotime($i.' month', $this_month)); ?></td>
									<?php } ?>
									<td style="font-family:'FranklinBold'; width:16%; padding:4px 0px; text-align:center; border: 1px solid #bfbfbf; font-size:9px;">Total</td>
								</thead>
								<tbody>
									<tr style="font-size:9px; font-family:'FranklinGothic-Book'; padding:3px 0px; text-align:center;">
										<td style=" width:25%;font-family:'FranklinBold'; padding:6px 3px; text-align:left; border: 1px solid #bfbfbf; font-size:9px;">Points Earned</td>
										<?php 
											$total_earn_point_year = 0;
											if(!empty($data['get_earned_points_yearly'])){ 
												foreach($data['get_earned_points_yearly'] as $total_earn_point_monthly){
													echo '<td style="border: 1px solid #bfbfbf; padding: 3px 0px;">'.number_format($total_earn_point_monthly['earned_point']).'</td>';
													$total_earn_point_year += $total_earn_point_monthly['earned_point'];
												}
										}?>
										<td style="border: 1px solid #bfbfbf; font-size:9px; padding: 3px 2px;"><?php echo number_format($total_earn_point_year); ?></td>
									</tr>
								  <tr style="width:25%; font-size:9px; font-family:'FranklinGothic-Book'; padding:3px 0px;text-align:center;">
									<td style="font-family:'FranklinBold'; padding:6px 3px; text-align:left; border: 1px solid #bfbfbf; font-size:9px;">Points Redeemed</td>
									<?php 
									$total_redeem_point_year = 0;
									if(!empty($data['get_redeem_points_yearly'])){ 
										foreach($data['get_redeem_points_yearly'] as $total_redeem_point_monthly){
											echo '<td style="border:1px solid #bfbfbf; padding:3px 0px;">'.number_format(abs($total_redeem_point_monthly['redeem_point'])).'</td>';
											$total_redeem_point_year += $total_redeem_point_monthly['redeem_point'];
										}
									}?>
									<td style="border:1px solid #bfbfbf; padding:3px 0px;"><?php echo number_format(abs($total_redeem_point_year)); ?></td>
								  </tr>
								  <tr style="font-size:9px;  text-align:center; font-family:'FranklinGothic-Book'; padding:3px 0px;">
									<td style="width:25%;font-family:'FranklinBold'; padding:6px 3px; text-align:left; border: 1px solid #bfbfbf; font-size:9px;"> Dollar Value of Rewards Redeemed</td>
									<?php 
									if(!empty($data['get_redeem_points_yearly'])){ 
										foreach($data['get_redeem_points_yearly'] as $total_redeem_point_monthly){
											echo '<td style="border:1px solid #bfbfbf; padding:3px 0px;">$'.number_format(round(abs($total_redeem_point_monthly['redeem_point']) * $data['point_value_by_reward']['point_value'])).'</td>';
										}
									}?>
									<td style="border:1px solid #bfbfbf; font-size:9px; padding:3px 0px;"><?php echo number_format(abs($total_redeem_point_year) * $data['point_value_by_reward']['point_value']); ?></td>
								  </tr> 				  
								  <tr style="font-size:9px; font-family:'FranklinGothic-Book'; text-align:center; padding:3px 0px;">
									<td style="width:25%; font-family:'FranklinBold'; padding:6px 3px; text-align:left; border: 1px solid #bfbfbf; font-size:9px;">Number of Rewards Redeemed</td>
									<?php 
									$total_redeem_rewards = 0;
									if(!empty($data['rewards_redeemed'])){ 
										foreach($data['rewards_redeemed'] as $rewards_redeemed){ ?>
											<td style="border:1px solid #bfbfbf; padding:3px 0px;"><?php echo number_format($rewards_redeemed['total_items']); ?></td>
											
									<?php $total_redeem_rewards += $rewards_redeemed['total_items']; }
									} ?>
									<td style="border:1px solid #bfbfbf; font-size:9px; padding:3px 0px;"><?php echo number_format($total_redeem_rewards); ?></td>
								  </tr>
								  <tr style="font-size:9px; font-family:'FranklinGothic-Book'; text-align:center; padding:3px 0px;">
									<td style="width:20% ;font-family:'FranklinBold'; padding:6px 3px; text-align:left; border: 1px solid #bfbfbf; font-size:9px;">Points Balance</td>
									<?php 
									if(!empty($data['get_points_balance_monthly'])){ 
										foreach($data['get_points_balance_monthly'] as $key=>$total_point_balance_monthly){ ?>
											<td style="border:1px solid #bfbfbf; padding:3px 4px;"><?php echo number_format( $total_point_balance_monthly['points_balance'] ); ?></td>
									<?php } } ?>
									<td style="border:1px solid #bfbfbf; font-size:10px; padding:3px 0px;"><?php echo number_format($closing_balance); ?></td>
								  </tr>
								  <tr style="font-size:9px; font-family:'FranklinGothic-Book'; text-align:center; padding:3px 4px;">
									<td style="font-family:'FranklinBold'; width:20%; padding:6px 3px; text-align:left; border: 1px solid #bfbfbf; font-size:9px;">Program Liability</td>
									<?php 
									if(!empty($data['get_points_balance_monthly'])){ 
										foreach($data['get_points_balance_monthly'] as $key=>$total_point_balance_monthly){ ?>
											<td style="border:1px solid #bfbfbf;  font-size:10px; padding:3px 0px;">$<?php echo number_format( $total_point_balance_monthly['points_balance'] * $data['point_value_by_reward']['point_value']); ?></td>
									<?php }
									}?>
									<td style="border:1px solid #bfbfbf;  font-size:9px; padding:3px 4px;"><?php echo number_format(($closing_balance) * $data['point_value_by_reward']['point_value']); ?></td>
								  </tr>
							  </tbody>
							</table>
						</td><!-- End program-overview-table -->
					</tr>
				</table><!----	 INNER-TABLE End	 ---->
			</td>
		</tr>
		 <!----	 third-row begin 	---->
			<tr>
				<td>
					<table style="margin-top:7px; height:342px ; margin-left:1px; border-collapse:collapse;" width="100%" cellspacing="0" border="0" align="">
						<tr>
						   <td style="width:256px;">
						   <table style="border:1px solid #bfbfbf; padding:0px; border-collapse:collapse; " width="100%" align="">
							<tr>
							   <td style="width:113px; padding-left:10px; padding-bottom:10px; margin-top:2px;">
								<table width="100%" >
								  <tr><td style="height:6px"></td></tr>
								  <tr>
								     <td colspan="2" style="font-size:7px; font-family:'FranklinBold'; color:#585858; width:100%; padding-left:0px; line-height:8px; text-align:center;">Average Points Earned Per Active Member</td>
								  </tr>
								  <tr><td style="height:14px"></td></tr>
								  <tbody>
									<tr>
										<td style="font-size:9px; width:80%; padding-left:10px; text-align:left;  font-family:'FranklinGothic-Book'; border:1px solid #bfbfbf;"><?php echo $month_name = date("F", mktime(0, 0, 0, $_POST['month'], 10)); ?></td>
										<?php
											$total_month_earned_point = $data['avg_point_earn_active_member'];
											if(!empty($total_month_earned_point) && $total_member != 0){ ?>
												<td style="font-size:9px; width:20%; text-align:center;  font-family:'FranklinGothic-Book'; border:1px solid #bfbfbf;"><?php echo number_format(round($total_month_earned_point['earned_point']/$total_member)); ?></td>
										<?php }
										?>		
									</tr>
									  <tr>
										<td style="font-size:9px;  width:60%; padding-left:10px; text-align:left;font-family:'FranklinGothic-Book'; border:1px solid #bfbfbf;">Last 12 Months</td>									
										<td style="font-size:9px; width:40%; text-align:center;  font-family:'FranklinGothic-Book'; border:1px solid #bfbfbf;"><?php 
											$year_earned_points = pg_fetch_all($data['earn_point_per_year']);
											$total_year_points = 0;
											if(!empty($year_earned_points)){
												foreach($year_earned_points as $key=>$y_earned_points){
													$total_year_points += $y_earned_points['earned_point'];
												}
											}
											if($total_year_points != 0 && $total_member != 0){
												echo number_format(round(($total_year_points/12)/$total_member));
											}else{
												echo 0;
											}
											
										?></td>
									  </tr>				  
									</tbody>
								</table>							
								</td>
								<td style="width:3px;"></td>
								<td style="width:112px; padding-right:10px; padding-bottom:10px;">
									<table style="width:100%">
									  <tbody>
									  	<tr style="height:2px"><td style="height:2px"></td></tr>
										<tr>
										<td colspan="2" style="font-size:14px; font-family:'FranklinBold'; margin-top:6px; padding-left:0px; color:#585858; text-align:center;">Burn vs Earn</td>
										</tr>
										<tr style="height:17px"><td style="height:17px"></td></tr>
										<tr>
											<td style="font-size:9px; width:60%; padding-left:10px; text-align:left; font-family:'FranklinGothic-Book'; border:1px solid #bfbfbf;"><?php echo $month_name = date("F", mktime(0, 0, 0, $_POST['month'], 10)); ?></td>
											<?php
												$total_month_redeem_point = $data['avg_point_redeem_active_member'];
												if(!empty($total_month_redeem_point) && $total_member != 0){ ?>
													<td style="font-size:9px; width:40%; text-align:center; font-family:'FranklinGothic-Book'; border:1px solid #bfbfbf;"><?php echo number_format(abs(round($total_month_redeem_point['redeem_point']/$total_member))); ?></td>
											<?php }
											?>
											
										</tr>
										  <tr>
											<td style="font-size:9px; width:60%; padding-left:10px; text-align:left;  font-family:'FranklinGothic-Book'; border:1px solid #bfbfbf;">last 12 Months</td>
										
											<td style="font-size:9px; width:40%; text-align:center;  font-family:'FranklinGothic-Book'; border:1px solid #bfbfbf;"><?php 
												$year_redeem_points = pg_fetch_all($data['get_redeem_point_per_year']);
												
												$total_year_redeem_points = 0;
												if(!empty($year_redeem_points)){
													foreach($year_redeem_points as $key=>$y_earned_points){
														$total_year_redeem_points += $y_earned_points['redeem_point'];
													}
												}
												
												if($total_year_redeem_points != 0 && $total_member != 0){
													echo number_format(abs(round(($total_year_redeem_points/12)/$total_member)));
												}else{
													echo 0;
												}
												
											?>%</td>
										  </tr>				  
										</tbody>
									 </table>
								</td>
								</tr>
							   </table>
							   <table width="100%" cellspacing="0" border="0" align="center" style="padding:0; height:5px;">
							   <tr></tr>
								</table>
							<!----	 pdf-chartContainer-1 	---->
							   <table width="100%"style="border:1px solid #bfbfbf; padding:0px ;height:168px" cellpadding="0px 0" >
										<td style="font-size:11px; font-family:'FranklinBold';  color:#585858; text-align:center; padding-top:8px; padding-bottom:0px; letter-spacing:0.3;">Active Members per Tier</td>
									<tr>
										<td><p id="pdf-chartContainer-1" style=" padding-left:2px ;width:97%; margin-bottom:10px"></p></td>	
									</tr>
								</table>										   
							</td>
						     <td style="width:8px;"></td>
							<!------	point-earned-vs-point-burned begin	------>
							 <td  class="points-earn"style="width:513px; padding:0px;">
								<table style="height:0.4px;">
								<tr>
									<td style="height:0px; padding:0.5px;"></td>
								 </tr>
								 </table>
								<table  class="point-earned-vs-point-burned-1" style="border:1px solid #bfbfbf;width:100%;">
								<tr>
									<td style="height:5px;"></td>
								 </tr>
								<td style="font-size:11px; font-family:'FranklinBold'; color:#585858; text-align:center; margin-top:10px; margin-bottom:20px; letter-spacing:0.3;">Points Earned vs Points Burned</td>
								 <tr>
									<td style="height:23px;"></td>
								 </tr> 
								  <tr>
									<td>
										<p id="pdf-bar-chart-2" style="height:287px; padding-left:22px; width:520px"></p>
									</td>
								  </tr>
								</table>
							 </td><!------ end	point-earned-vs-point-burned 	------>
						  <td style="width:8px;"></td>
						<!-- right-average-chart -->						  
							<td style="width:246px;">
								<table width="100%"style="border:1px solid #bfbfbf; padding:0px ;" cellpadding="0px 0 0" >
									<td style="font-size:11px; font-family:'FranklinBold'; color:#585858; padding-top:6px; text-align:center; margin-bottom:0px;  letter-spacing:0.3;">Website Logins per Month</td>
									<tr><td style="height:4px;"></td></tr>
									<tr>
										<td>
											<p id="pdf-chartContainer" style="height:132px; padding-left:10px; width:250px"></p>
										</td>			
									</tr>
								</table>
								<table width="100%" style="border:1px solid #bfbfbf; height:3px; padding:0px">
									<tr></tr>
								</table>
								<?php 
									$X = 0; $Y = 0; $Z = 0;
									if(!empty($data['open_task_by_member'])){ 
										$total_opened_task = 0;
										foreach($data['open_task_by_member'] as $total_open_task){
											$total_opened_task += $total_open_task['count'];
										}
										
										if(!empty($data['access_website_member']['total_website_acess'])){
											$total_opened_task = $total_opened_task + $data['access_website_member']['total_website_acess'];
											$X = ($data['access_website_member']['total_website_acess'] * 100 )/$total_opened_task;
										}
										
										foreach($data['open_task_by_member'] as $open_task){
											if($total_opened_task != 0){
												if($open_task['direction_name'] == 'Inbound Call'){
													$Y = ($open_task['count'] * 100 )/$total_opened_task;
												}
												
												if($open_task['direction_name'] == 'Email (Inbound)'){
													$Z = ($open_task['count'] * 100 )/$total_opened_task;
												}
											}
										}
									} 
								?>
								
								<table width="99%" align="center" style="border:1px solid #bfbfbf; height:90px" >
									<tr>
										<td style="height:2px;"></td>
									</tr>
									<td style="font-size:11px; font-family:'FranklinBold'; color:#585858; text-align:center; margin-top:0; margin-bottom:10px; letter-spacing:0.3;">Points of Contact – <?php echo $month_name = date("F", mktime(0, 0, 0, $_POST['month'], 10)); ?> <?php echo $_POST['year']; ?></td>
									<tr>
										<td style="padding:10px 10px 0;">
											<table width="100%;"  >
												<tr>
													<td style="background-color:#C00000; color:#fff; height:90px;  width:<?php echo round($X); ?>%; font-family:'FranklinGothic-Book'; font-size:12px; text-align:center; relative;top: 3%;"><?php echo round($X); ?>%</td>											
													<td style="width:1%; background-color:#fff"></td>
													<td style="background-color:#92D050; font-family:'FranklinGothic-Book'; font-size:12px; height:90px ; width:<?php echo round($Z); ?>%; color:#fff; text-align:center; position:relative; align-content"><?php echo round($Z); ?>%</td>
													<td style="width:1%; background-color:#fff"></td>				
													
													<td style="background-color:#FBC003; color:#fff; text-align:center; font-family:'FranklinGothic-Book'; width:<?php echo round($Y); ?>%; font-size:12px; height:90px; position:relative"><?php echo round($Y); ?>%</td>
													
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td>
											<table width="80%" align="center" style="margin:0px auto 14px; height:30px;">
												<tr>
													<td class="data-series-1">
														<ul style="margin:0px; padding:0; margin-left:30px">
														  <li class="login-li" style="list-style:none; font-family:'FranklinGothic-Book'; font-size:10px; color:#585858; margin-left:11px; margin-right:11px; position:relative; display:inline-block;">Login</li>
														  <li class="email-li" style="list-style:none; font-family:'FranklinGothic-Book'; font-size:10px; color:#585858; margin-left:11px; margin-right:11px; position:relative; display:inline-block;">Email(inbound)</li>
														  <li class="call-li" style="list-style:none; font-family:'FranklinGothic-Book'; font-size:10px; color:#585858; margin-left:11px; margin-right:11px; position:relative; display:inline-block;">Calls</li>
														</ul>
													</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>	<!-- end right-average-chart -->	
							</table>
						   </td>
						</tr><!-- third-row end -->
						<!-- fourth-row begin -->
						<!----	pdf-chartContainer-4	 ---->
						<tr>
							<table width="100%"style="border:0px solid #bfbfbf; padding:0px ;height:54px" cellpadding="0px 0" >
								<td style="height:54px"></td>
							</table>
						</tr>
					<tr>
					  <td>
						<!----	 main-header for second page begin	 ---->
						<table style="border-bottom:5px solid #92D050 ; margin:0 auto;" width="100%" align="center">
							<tr>
								<td style="padding:13px 0" width="358px">
								   <a href="#"><img src="Public/images/hot-reward-logo.jpg" alt="program-logo" class="program-logo" style="max-width:106px"/></a>
								</td>
								<td style="padding:13px 0 ;font-family:'FranklinBold';" align="center" width="358px">
									<table style="width:358px; text-align:center;" align="center">
									  <tr>
										<td style="font-size:20px; text-align:center; font-family:'FranklinBold ;text-transform:uppercase; color:#585858; margin-top:2px; margin-bottom: 0;"><?php echo $_POST['reward_program_code']; ?> Dashbord</td>	
									   </tr>
									   <tr>
										<td style=" font-size:12px; text-align:center; font-family:'FranklinBold ; font-weight:500;  line-height:4px ;color:#585858; margin-top:1px; margin-bottom:0;"><?php echo $month_name = date("F", mktime(0, 0, 0, $_POST['month'], 10)); ?> <?php echo $_POST['year']; ?></td>
										</tr>
									</table>
								</td>
								<td style="padding:13px 0" align="right" width="358px">
									<a href="#"><img src="Public/images/incentive-logo.jpg" alt="incentive-logo" class="incentive-logo" style="max-width:106px"/></a>	
								</td>
							</tr>
						</table><!----	 main-header End	 ---->
					  </td>
					</tr>
					<tr>
						<td>	
							<table style="margin-top:7px; height:233px; border:1px solid #bfbfbf; margin-left:0px" width="99.9%"  >
								<tr>
								<!--Top 10 Earners begin-->
									<td style="width:339px; padding:10px; font-family:'FranklinBold';">
										<table style="width:100%; border-collapse:collapse; margin-top:0px;" width="100%" cellpadding=" 0;">		   
										<td style="font-size:10.5px; font-family:'FranklinBold'; color:#585858; text-align:center; margin-top:0; margin-bottom:10px; letter-spacing:0.3;">Top 10 Earners – <?php echo $month_name = date("F", mktime(0, 0, 0, $_POST['month'], 10)); ?> <?php echo $_POST['year']; ?></td>
										<tbody>
										   <tr>
										    <td>
											 <p id="pdf-chartContainer-4" style="height:190px; width:325px;"></p>
											</td>
											</tr>
										</tbody>
										</table>
									</td><!-- End Top 10 Earners-->
									<td style="width:8px">
									</td>
									<!--  Top 10 Redeemers - Rewards View begin-->
									<td style="width:339px; padding:10px;">
										<table style="width:100%; border-collapse:collapse; margin-top:0px;" width="100%" cellpadding=" 0;">		   
										<td style="font-size:10.5px; font-family:'FranklinBold'; color:#585858; text-align:center; margin-top:0; margin-bottom:10px; letter-spacing:0.3;">Top 10 Redeemers - Rewards View – <?php echo $month_name = date("F", mktime(0, 0, 0, $_POST['month'], 10)); ?> <?php echo $_POST['year']; ?></td>
										<tbody>
										   <tr>
										    <td>
											 <p id="pdf-chartContainer-5" style="height:190px; width: 325px;"></p>
											</td>
											</tr>
										</tbody>
										</table>
									</td> <!-- End Top 10 Redeemers - Rewards View -->
									<!-- Top 10 Redeemers - Points View begin-->
									<td style="width:339px; padding:11px;">
										<table style="width:100%; border-collapse:collapse; margin-top:0px;" width="100%" cellpadding=" 0;">		   
										<td style="font-size:10.5px; font-family:'FranklinBold'; color:#585858; text-align:center; margin-top:0; margin-bottom:20px; letter-spacing:0.3;">Top 10 Redeemers - Points View – <?php echo $month_name = date("F", mktime(0, 0, 0, $_POST['month'], 10)); ?> <?php echo $_POST['year']; ?></td>
										<tbody>
										   <tr>
											  <td>
												 <p id="pdf-chartContainer-6" style="height:190px; width: 325px;"></p>
											   </td>
											</tr>
										</tbody>
										</table>
									</td><!-- End Top 10 Redeemers - Points View -->
								</tr>
							</table>
						</td>
					</tr>
					<!----     fourth-row end 	---->
					<!----	 fivth-row begin	---->
					<tr>
						<td>	
						  <table style="margin-top:7px; margin-left:1px" width="98.6%" >
							<tbody>
								<tr>
								<!--	Monthly Redemptions per Tier begin -->
									<td style="width:331px; border:1px solid #bfbfbf; padding:0px;">
										<table style=" width:100%; border-collapse:collapse; margin-top:0px;" width="100%" cellpadding=" 0;">		   
										 <td style="font-size:11px; font-family:'FranklinBold'; color:#585858; text-align:center; margin-top:0; margin-bottom:10px; letter-spacing:0.3;">Monthly Redemptions per Tier – <?php echo $month_name = date("F", mktime(0, 0, 0, $_POST['month'], 10)); ?> <?php echo $_POST['year']; ?></td>  <tbody>
											  <tr>
												<td>
													<p id="pdf-chartContainer-7" style="height:235px; width:350px; padding-left:10px"></p>
												</td>
											  </tr>
											</tbody>
										</table>
									</td><!-- End	Monthly Redemptions per Tier  -->
									<td style="width:5px">
										<table>
											<tr><td style="width:5px"></td></tr>
										</table>
									</td>
									<!-- Top 10 Rewards Categories begin -->
									<td style="width:715px; border:1px solid #bfbfbf; padding:10px;">
										<table style="width:100%; border-collapse:collapse; margin-top:0px;" width="100%" cellpadding=" 0;">		   
										 <td style="font-size:11px; font-family:'FranklinBold'; color:#585858; text-align:center; margin-top:0; margin-bottom:10px; letter-spacing:0.3;">Top 10 Rewards Categories <?php echo $month_name = date("F", mktime(0, 0, 0, $_POST['month'], 10)); ?> <?php echo $_POST['year']; ?></td>
										   <tbody>
											  <tr>
												<td>
													<p id="pdf-chartContainer-8" style="height: 235px; width:710px;"></p>
												</td>
											  </tr>
											</tbody>
										</table>
									</td><!-- End Top 10 Rewards Categories -->
								</tr>
								</tbody>
							</table>
						</td>
				    </tr><!-- fivth-row  end-->
	  </tbody>
	</table>	
</body>
</html>
</div>