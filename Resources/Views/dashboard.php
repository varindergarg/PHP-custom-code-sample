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
<link rel="icon" href="Public/images/favi-icon.png">
<link rel="stylesheet" type="text/css" href="Public/css/style.css">
<link rel="stylesheet" type="text/css" href="Public/css/responsive.css">
</head>
<body>
<!------- 	 main-page-begin   ------->
<div class="main-page">
	<!-- main-header begin -->
	<div class="main-header">
		<div class="header-left">
			<a href="#"><img src="Public/images/hot-reward-logo.jpg" alt="program-logo" class="program-logo"/></a>
		</div>
		<div class="header-mid text-center">
			<h2><?php echo $reward_program_name; ?> Dashboard</h2>
			<h5><?php echo $month_name = date("F", mktime(0, 0, 0, $_POST['month'], 10)); ?> <?php echo $_POST['year']; ?><h5>
		</div>
		<div class="header-right text-right">
			<a href="#"><img src="Public/images/incentive-logo.jpg" alt="incentive-logo" class="incentive-logo"/></a>
		</div>
	</div><!-- End main-header -->
	<div class="inner-row">
		<!-- member-status-table begin -->
		<div class="member-status-bx">
			<h3 class="inner-heading">Members per Status</h3>
			<table class="member-status-table" >
				<?php $total_member = 0;
					if(!empty($total_member_per_status)){
					$active_member_count = 0;
					foreach($total_member_per_status as $total_count){
						$total_member += $total_count['count'];
                        if($total_count['status'] == 'Active') $active_member_count = $total_count['count'];    // save active member count for later use
						?>
						<tr>
							<td><?php echo $total_count['status']; ?></td>
							<td><?php echo number_format($total_count['count']); ?></td>
						</tr>
					<?php	}
				}?>
				<tr>
					<td>Total</td>
					<td><?php echo number_format($total_member); ?></td>
				</tr>
			</table>
		</div><!-- End member-status-table begin -->
		<!----	program-overview-table begin	---->
		<div class="program-over-sec">
			<h3 class="inner-heading">Program Overview</h3>
			<table class="program-over-table" >
				<thead>
					<td></td>
					<?php
						$selected_month = $_POST['year'].'-'.$_POST['month'].'-01';
						$selected_month = strtotime("$selected_month -11 month");
						$this_month = mktime(0, 0, 0, date('m',$selected_month), 1, date('Y',$selected_month));
						for ($i = 0; $i < 12; $i++) {
							$class = '';
							if($i == 11){
								$class = 'class="td-active"';
							}
							
							echo '<td '.$class.'>'.date('M.Y', strtotime($i.' month', $this_month)).'</td>';
						}
					?>
					<td>Total</td>
				</thead>
				<tbody>
					<tr>
						<td>Points Earned</td>
						<?php 
						$total_earn_point_year = 0;
						if(!empty($data['get_earned_points_yearly'])){ 
							foreach($data['get_earned_points_yearly'] as $total_earn_point_monthly){
								if(!empty($total_earn_point_monthly['earned_point'])){
									echo '<td>'.number_format($total_earn_point_monthly['earned_point']).'</td>';
								}else{
									echo '<td></td>';
								}
								
								$total_earn_point_year += $total_earn_point_monthly['earned_point'];
							}
						}?>
						<td><?php echo number_format($total_earn_point_year); ?></td>
					</tr>  
					<tr>
						<td>Points Redeemed</td>
						<?php 
						$total_redeem_point_year = 0;
						if(!empty($data['get_redeem_points_yearly'])){ 
							foreach($data['get_redeem_points_yearly'] as $total_redeem_point_monthly){
								if(!empty($total_redeem_point_monthly['redeem_point'])){
									echo '<td>'.number_format(abs($total_redeem_point_monthly['redeem_point'])).'</td>';
								}else{
									echo '<td></td>';
								}
								
								$total_redeem_point_year += $total_redeem_point_monthly['redeem_point'];
							}
						}?>
						<td><?php echo number_format(abs($total_redeem_point_year)); ?></td>
					</tr>
					<tr>
						<td> Dollar Value of Rewards Redeemed</td>
						<?php 
						if(!empty($data['get_redeem_points_yearly'])){ 
							foreach($data['get_redeem_points_yearly'] as $total_redeem_point_monthly){
								if(!empty($total_redeem_point_monthly['redeem_point'])){
									echo '<td>$'.number_format(abs($total_redeem_point_monthly['redeem_point']) * $data['point_value_by_reward']['point_value']).'</td>';
								}else{
									echo '<td></td>';
								}
							}
						}?>
						<td><?php echo number_format(abs($total_redeem_point_year) * $data['point_value_by_reward']['point_value']); ?></td>
					</tr>  
					<tr>
						<td>Number of Rewards Redeemed</td>
						<?php 
						$total_redeem_rewards = 0;
						if(!empty($data['rewards_redeemed'])){ 
							foreach($data['rewards_redeemed'] as $rewards_redeemed){ 
								if(!empty($rewards_redeemed['total_items'])){
									echo '<td>'.number_format($rewards_redeemed['total_items']).'</td>';
								}else{
									echo '<td></td>';
								}
								
								$total_redeem_rewards += $rewards_redeemed['total_items'];
							}
						}?>
						<td><?php echo number_format($total_redeem_rewards); ?></td>
					</tr>
					<tr>
						<td>Points Balance</td>
						<?php 
                        // HIGGSY edit
						if(!empty($data['get_points_balance_monthly'])){ 
							foreach($data['get_points_balance_monthly'] as $key=>$total_point_balance_monthly){ ?>
								<td><?php echo number_format( $total_point_balance_monthly['points_balance'] ); ?></td>
						<?php }
						}?>
                        <!-- this should just be the same as the current month -->
						<td><?php echo number_format($closing_balance); ?></td>
					</tr>
					<tr>
						<td>Program Liability</td>
						<?php 
                        // HIGGSY edit
						if(!empty($data['get_points_balance_monthly'])){ 
							foreach($data['get_points_balance_monthly'] as $key=>$total_point_balance_monthly){ ?>
								<td>$<?php echo number_format( $total_point_balance_monthly['points_balance'] * $data['point_value_by_reward']['point_value']); ?></td>
						<?php }
						}?>
                        <!-- this should just be the same as the current month -->
						<td><?php echo number_format(($closing_balance) * $data['point_value_by_reward']['point_value']); ?></td>
					</tr>
				</tbody>
			</table>
		</div>	<!----	End program-overview-table ---->
	</div>
		<!---- left-average-chart-begin ---->
		<div class="inner-row">
			<div class="left-average-chart">
			<!--  burn-vs-earn-section begin  -->
				<div class="burn-vs-earn-section">
				<!-- left-area-sect begin -->
					<div class="left-area-sect">
						<h3 class="inner-heading">Average Points Earned Per Active Member</h3>
						<table class="average-point-table" >
						  <tr>
							<td><?php echo $month_name = date("F", mktime(0, 0, 0, $_POST['month'], 10)); ?></td>
							<?php
								$total_month_earned_point = $data['avg_point_earn_active_member'];
								if(!empty($total_month_earned_point) && $total_member != 0){
									//echo '<td>'.number_format(round($total_month_earned_point['earned_point']/$total_member)).'</td>';
									echo '<td>'.number_format(round($total_month_earned_point['earned_point']/$active_member_count)).'</td>';
								}
							?>
						  </tr>
						  <tr>
							<td>Last 12 Months</td>
							<td><?php 
									$year_earned_points = pg_fetch_all($data['earn_point_per_year']);
									$total_year_points = 0;
									if(!empty($year_earned_points)){
										foreach($year_earned_points as $key=>$y_earned_points){
											$total_year_points += $y_earned_points['earned_point'];
										}
									}
									if($total_year_points != 0 && $total_member != 0){
										//echo number_format(round(($total_year_points/12)/$total_member));
										echo number_format(round(($total_year_points/12)/$active_member_count));
									}else{
										echo 0;
									}
									
								?>
							</td>
						  </tr>				  
						</table>
					</div><!-- left-area-sect end -->
					<!-- right-area-sect begin -->
					<div class="right-area-sect">
						<h3 class="inner-heading">Burn vs Earn</h3>
						<table class="member-status-table">
						  <tr>
							<td><?php echo $month_name = date("F", mktime(0, 0, 0, $_POST['month'], 10)); ?></td>
							<?php
								$total_month_redeem_point = $data['avg_point_redeem_active_member'];
								if(!empty($total_month_redeem_point) && $total_member != 0){
									echo '<td>'.number_format(abs(round($total_month_redeem_point['redeem_point']/$total_member))).'</td>';
								}
							?>
						  </tr>
						  <tr>
							<td>Last 12 Months</td>
							<td><?php 
									$year_redeem_points = pg_fetch_all($data['get_redeem_point_per_year']);
									
									$total_year_redeem_points = 0;
									if(!empty($year_redeem_points)){
										foreach($year_redeem_points as $key=>$y_earned_points){
											$total_year_redeem_points += $y_earned_points['redeem_point'];
										}
									}
									/* echo '<pre>';
									print_r($total_year_redeem_points);
									exit; */
									if($total_year_redeem_points != 0 && $total_member != 0){
										echo number_format(round((abs($total_year_redeem_points)/12)/$total_member));
									}else{
										echo 0;
									}
									
								?>%
							</td>
						  </tr>			  
						</table>
					</div><!-- right-area-sect begin -->
				</div><!---- End burn-vs-earn  ---->
				<div class="doughnut-chart margin-top-area">
					<h3 class="inner-headings">Active Members per Tier</h3>
					<div id="chartContainer-1" style="height:180px; padding-left:2px; margin-left:1px; max-width:240px; width:100%"></div>
				</div>
			<!-- End left-average-chart -->
		</div>
		<!------	point-earned-vs-point-burned begin	------>
		<div class="point-earned-vs-point-burned">
			<h3 class="inner-headings">Points Earned vs Points Burned</h3>
			<div id="bar-chart-2" style="height: 290px; width: 100%;"></div>
		</div><!-- 	End point-earned-vs-point-burned -->
		<!-- right-average-chart -->
		<div class="right-average-chart">
			<div class="weblogin-average">
				<h3 style="font-size:11px; font-family:'FranklinBold'; color:#585858; text-align:center; margin-top:0; margin-bottom:0px; margin-top:0; letter-spacing:0.3;">Website Logins per Month</h3>										
				<div id="chartContainer" style="height:130px; width: 267px;"></div>
			</div>
			<?php 
				$X = 0;
				$Y = 0;
				$Z = 0;
				$X1 = 0;
				$Y1 = 0;
				$Z1 = 0;
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
				
				if(($X == 0) && ($Y == 0) && ($Z == 0)){
					$X1=$Y1=$Z1 = 33;
				}
			?>
			<!--  Point-contact Begin  -->
			<div class="Point-contact margin-top-area">
				<h3 class="inner-heading">Points of Contact – <?php echo $month_name = date("F", mktime(0, 0, 0, $_POST['month'], 10)); ?> <?php echo $_POST['year']; ?></h3>
				<div class="Point-contact-measurement">
					<div style="width: <?php echo round($X).$X1; ?>%;">
						<div class="email-area">
							<h4 class="percents"><?php echo round($X); ?>%</h4>
						</div>
					</div>
					<div class="login-area" style="width: <?php echo round($Z).$Z1; ?>%;">
						<h4 class="percents-login"><?php echo round($Z); ?>%</h4>
					</div>
					<div style="width: <?php echo round($Y).$Y1; ?>%;">
						<div class="call-area">
							<h4 class="percents"><?php echo round($Y); ?>%</h4>
						</div>
					</div>
				</div>
				<div class="data-series">
					<ul>
						<li class="login-li"><span></span>Login</li>
						<li class="email-li"><span></span>Email(inbound)</li>
						<li class="call-li"><span></span>Calls</li>
					</ul>
				</div>
			</div><!-- End-Point-contact   -->
		 </div>
	</div>
	<!----	point-bar begin	---->
	<div class="inner-row point-bar">
		<!--Top 10 Earners begin-->
		<div class="left-charts">
			<h3 class="inner-headings">Top 10 Earners – <?php echo $month_name = date("F", mktime(0, 0, 0, $_POST['month'], 10)); ?> <?php echo $_POST['year']; ?> </h3>
			<div id="chartContainer-4" style="height:190px; max-width: 340px; width:100%">
			</div>
		</div><!--End Top 10 Earners -->
		<!--Top 10 Redeemers - Rewards View begin-->
		<div class="mid-charts">
			<h3 class="inner-headings">Top 10 Redeemers - Rewards View – <?php echo $month_name = date("F", mktime(0, 0, 0, $_POST['month'], 10)); ?> <?php echo $_POST['year']; ?></h3>
			<div id="chartContainer-5" style="height:190px; max-width: 340px; width:100%"></div>
		</div><!--End Top 10 Redeemers - Rewards View -->
		<!-- Top 10 Redeemers - Points View begin-->
		<div class="right-charts">
			<h3 class="inner-headings">Top 10 Redeemers - Points View – <?php echo $month_name = date("F", mktime(0, 0, 0, $_POST['month'], 10)); ?> <?php echo $_POST['year']; ?></h3>
			<div id="chartContainer-6" style="height:190px; max-width: 340px; width:100%"></div>
		</div><!--End Top 10 Redeemers - Points View-->
	</div><!---- End point-bar 	 ---->
	<!-- bottom-chart begin -->
	<div class="inner-row">
		<div class="linee-chart">
			<h3 class="inner-headings">Monthly Redemptions per Tier – <?php echo $month_name = date("F", mktime(0, 0, 0, $_POST['month'], 10)); ?> <?php echo $_POST['year']; ?></h3>
			<div id="chartContainer-7" style="height:235px; max-width:345px; width:100%"></div>
		</div>
		<div class="columnn-chart">
			<h3 class="inner-headings">Top 10 Rewards Categories <?php echo $month_name = date("F", mktime(0, 0, 0, $_POST['month'], 10)); ?> <?php echo $_POST['year']; ?></h3>
			<div id="chartContainer-8" style="height: 235px; max-width:710px; width: 100%;"></div>	
		</div>
	</div><!-- End bottom-chart  -->
	<!-- bottom-buttons-begin -->
	<div style="text-align:center;margin-bottom:20px">
		<div class="frm-area">
			<a href="<?php echo base_url; ?>" style="color: #fff;" class="send-report-btn">Back</a>
		</div>
		<div class="frm-area">
			<button type="button" class="send-report-btn" data-toggle="modal" data-target="#shareReport">Share Report</button>
		</div>
		<form class="frm-area" action="<?php echo base_url; ?>/?class=Home&method=save_report" method="post" id="print_pdf" target="_blank">
			<input type="hidden" name="print_pdf_html">
			<button type="button" class="send-report-btn" id="print_pdf_html" class="btn btn-success btn-xs">Print Report</button>
		</form>
	</div><!-- End bottom-buttons -->
</div>
<div style="display:none;">
	<?php include 'index-pdf.php'; ?>
</div>
	<div class="modal fade" id="shareReport" role="dialog">
		<div class="modal-dialog cust-dialog">
			<!-- Modal content-->
			<div class="modal-content custom-popup">
				<div class="modal-header">
					<button type="button" class="close-btns" data-dismiss="modal">&times;</button>
				</div>
				<div id="fail_msg"></div>
				<form action="<?php echo base_url; ?>/?class=Home&method=send_report" method="post" id="make_pdf">
					<div class="modal-body">
						<div class="form-groups">
							<label for="email">Email</label>
							<div class="right-frm">
								<input type="email" name="email" class="form-control tagsinput" id="email">
							</div>
						</div>
						<div class="form-groups">
							<label for="comment">Commentary Box</label>
							<div class="right-frm">
								<textarea class="form-control" rows="3" name="commentry_box" id="commentry_box"></textarea>
							</div>
						</div> 
						<input type="hidden" name="html_inner" id="html_inner">
						<div class="text-right">
							<button type="button" name="create_pdf" id="create_pdf" class="send-report-btn">Send</button>
						</div>		
					</div>
				</form>
				<div class="loader" id="loader" style="display:none;"></div>	
			</div>
		</div>
	</div><!----	end  popup	  ---->
	<!-- success_modal begin -->
	<div id="success_modal" class="modal fade">
		<div class="modal-dialog modal-confirm">
			<div class="modal-content">
				<div class="modal-header">			
					<h4 class="modal-title">Success</h4>	
				</div>
				<div class="modal-body">
					<p class="text-center">Report has been sent successfully.</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="close-btns btn-custt" data-dismiss="modal">OK</button>
				</div>
			</div>
		</div>
	</div>
<?php 
	$doughnutDataPoints = pg_fetch_all($data['member_per_tier']);
 
	/* Top 10 Earner */
	$topMonthEarner = pg_fetch_all($data['top_month_earner']);
	array_multisort(array_column($topMonthEarner, 'y'), SORT_ASC, $topMonthEarner);
	$max_top_month_earner = (max(array_column($topMonthEarner, 'y')) * 0.20) + max(array_column($topMonthEarner, 'y'));
	/* Top 10 Reward Earner */
	$topMonthRewards = $data['top_month_rewards'];
	array_multisort(array_column($topMonthRewards, 'y'), SORT_ASC, $topMonthRewards);
	$max_top_month_rewards = (max(array_column($topMonthRewards, 'y')) * 0.20) + max(array_column($topMonthRewards, 'y'));
	
	/* Top 10 Redeemer */
	$top_month_redeemer  = pg_fetch_all($data['top_month_redeemer']);
	
	$top_month_redeemers = array();
	$max_top_month_redeemers = 0;
	if(!empty($top_month_redeemer)){
		foreach($top_month_redeemer as $key=>$month_redeemer){
			$top_month_redeemers[$key]['y']          = abs($month_redeemer['y']);
			$top_month_redeemers[$key]['label']      = $month_redeemer['label'];
			$top_month_redeemers[$key]['indexlabel'] = abs($month_redeemer['indexlabel']);
		}
		$max_top_month_redeemers = (max(array_column($top_month_redeemers, 'y')) * 0.20) + max(array_column($top_month_redeemers, 'y'));
	}
	array_multisort(array_column($top_month_redeemers, 'y'), SORT_ASC, $top_month_redeemers);

	/* To show the earn total in chart 2 */
	$get_earned_points_yearly = array();
	$i = 0;
	foreach($data['get_earned_points_yearly'] as $key=>$year_earn_total){
		$get_earned_points_yearly[$i]['label'] = $year_earn_total['month'].$year_earn_total['yyyy'];
		$get_earned_points_yearly[$i]['y']     = abs($year_earn_total['earned_point']);
		$i++;
	}
	
	/* To show the earn total in chart 2 */
	$get_redeem_points_yearly = array();
	$j = 0;
	foreach($data['get_redeem_points_yearly'] as $key=>$year_redeem_total){
		$get_redeem_points_yearly[$j]['label'] = $year_redeem_total['month'].$year_redeem_total['yyyy'];
		$get_redeem_points_yearly[$j]['y']     = abs($year_redeem_total['redeem_point']);
		$j++;
	}
	
	/* to show the access website month wise in chart 3 */
	$login_per_month_arr = array();
	$k = 0;
	foreach($data['login_per_month'] as $keys=>$login_per_month){
		$login_per_month_arr[$k]['y'] 			= $login_per_month['logins'];
		$login_per_month_arr[$k]['indexLabel']  = '"'.$login_per_month['logins'].'"';
		$login_per_month_arr[$k]['markerColor'] = "#92D050";
		$login_per_month_arr[$k]['label'] = $keys;
		$k++;
	}
	
	/* reward per tier available in system */
	$top_redeemer_per_tier = array();
	
	if(!empty($data['top_redeemer_per_tier'])){
		
		$line_color = array('#C55A11','#7F7F7F','#FFC000','#DAE3F3','#000000','#808080');
		$j = 0;
		foreach(array_filter($data['top_redeemer_per_tier']) as $key=>$top_redeemer_tier){
			$tier_val = array();
			$label_value = array();
			
			/* array_multisort(array_column($top_redeemer_tier, 'yyyy'), SORT_ASC, $top_redeemer_tier); */
			
			/* 16 may code updated by varinder
				here I am putting my logic that if any month don't have records then the line for that tier has to go to 0
				month order is going from Apr.2019 to Mar.2020, chronologically
			*/
			
			foreach($top_redeemer_tier as $k=>$redeemer_tier_val){	
				$tier_val[$k]['y'] = $redeemer_tier_val['total_items'];
				$tier_val[$k]['label'] = $redeemer_tier_val['month'].substr( $redeemer_tier_val['yyyy'], -2);
				
				$label_value[$redeemer_tier_val['total_items']] = $redeemer_tier_val['month'].substr( $redeemer_tier_val['yyyy'], -2);
			}
			$new_array = array();
			for ($i = 0; $i < 12; $i++) {
				$moyr = date('My', strtotime($i.' month', $this_month));
				
				if(array_search($moyr,$label_value)){
					$new_array[$i]['y'] = array_search($moyr,$label_value);
					$new_array[$i]['label'] = $moyr;
				}else{
					$new_array[$i]['y'] = 0;
					$new_array[$i]['label'] = $moyr;
				}				
			}
			
			$top_redeemer_per_tier[] = array(
				'type' => 'line',
				'indexLabelFontSize' => 9,
				'name' => "",
				'color' => $line_color[$j],
				'markerSize' => 0,
				'showInLegend' => 'true',
				'indexLabel' => '{y}',
				'dataPoints' => $new_array
			);
			$j++;
		}
	}
	
	/* Total number of rewards per category */
	$rewards_per_category = $data['rewards_per_category'];
	$category_redeemers = array();
	$max_category_redeemers = 0;
	if(!empty($rewards_per_category)){
		foreach($rewards_per_category as $key=>$category_redeemer){
			$category_redeemers[$key]['y']          = $category_redeemer['num_orders'];
			$category_redeemers[$key]['label']      = $category_redeemer['reporting_category'];
			$category_redeemers[$key]['indexlabel'] = $category_redeemer['num_orders'];
		}
		$max_category_redeemers = (max(array_column($category_redeemers, 'y')) * 0.1) + max(array_column($category_redeemers, 'y'));
	}
?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="//bootstrap-tagsinput.github.io/bootstrap-tagsinput/dist/bootstrap-tagsinput.css">
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script> 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="Public/js/canvasjs.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.3/jspdf.debug.js"></script>
<script src="//bootstrap-tagsinput.github.io/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>
<script>
	window.onload = function () {
	/****	doughnut-chart ****/
	var chartt = new CanvasJS.Chart("chartContainer-1", {
		animationEnabled: true,
		title:{
			text: "",
			horizontalAlign: "center"
		},
		data: [{
			type: "doughnut",
			startAngle: 45,
			//innerRadius: 60,
			indexLabelFontSize: 11,
			indexLabel: "{label}:{y} (#percent%)",
			toolTipContent: "<b>{label}:</b> {y} (#percent%)",
			dataPoints: <?php echo json_encode($doughnutDataPoints, JSON_NUMERIC_CHECK); ?>
		}]
	});
	chartt.render();
	/****	End doughnut-chart ****/
	/***	bar-chart-2 begin ***/	
	var chartt2 = new CanvasJS.Chart("bar-chart-2", {
		animationEnabled: true,
		title:{
			text: ""
		},	
		axisY: {
			title: "",
			titleFontColor: "#4F81BC",
			lineColor: "#fff",
			gridColor: "#dedede",
			labelFontColor: "#000",
			tickColor: "#fff",
		},
		axisY2: {
			title: "",
			titleFontColor: "#C0504E",
			lineColor: "fff",
			labelFontColor: "#000",
			tickColor: "#fff",
			fontSize: 10,
			lineThickness: 0,
			tickLength: 0,
			valueFormatString:" "
		},
		axisX:{
			labelAngle: 135,
			interval:1,
			fontSize: 9,
			labelMaxWidth: 200,
		},
		toolTip: {
			shared: true
		},
		legend: {
			cursor:"pointer",
			itemclick: toggleDataSeries,
			fontSize: 10,
		},
		dataPointWidth: 8,
		data: [{
			type: "column",
			name: "Points Earned",
			legendText: "Points Earned",
			showInLegend: true, 
			dataPoints: <?php echo json_encode($get_earned_points_yearly, JSON_NUMERIC_CHECK); ?>
		},
		{
			type: "column",	
			name: "Points Burned",
			legendText: "Points Burned",
			axisYType: "secondary",
			showInLegend: true,
			dataPoints: <?php echo json_encode($get_redeem_points_yearly, JSON_NUMERIC_CHECK); ?>
		}]
	});
	chartt2.render();
	/***  end	bar-chart-2 ***/
	/*Third chart */
	var chart3 = new CanvasJS.Chart("chartContainer", {
		animationEnabled: true,
		axisX:{
			interval: 1
		},
		axisY2:{
			interlacedColor: "rgba(1,77,101,.2)",
			gridColor: "rgba(1,77,101,.1)",
			title: "Number of Companies"
		},
		data: [{
			type: "bar",
			name: "companies",
			axisYType: "secondary",
			color: "#014D65",
			dataPoints: [
				{ y: 3, label: "Sweden" },
				{ y: 7, label: "Taiwan" },
				{ y: 5, label: "Russia" },
				{ y: 9, label: "Spain" },
				{ y: 7, label: "Brazil" },
				{ y: 7, label: "India" },
				{ y: 9, label: "Italy" },
				{ y: 8, label: "Australia" },
				{ y: 11, label: "Canada" },
				{ y: 15, label: "South Korea" },
				{ y: 12, label: "Netherlands" },
				{ y: 15, label: "Switzerland" },
				{ y: 25, label: "Britain" },
				{ y: 28, label: "Germany" },
				{ y: 29, label: "France" },
				{ y: 52, label: "Japan" },
				{ y: 103, label: "China" },
				{ y: 134, label: "US" }
			]
		}]
	});
	chart3.render();
	function toggleDataSeries(e) {
		if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
			e.dataSeries.visible = false;
		}
		else {
			e.dataSeries.visible = true;
		}
		chartt.render();
	}
	/* login-average */
	var chart = new CanvasJS.Chart("chartContainer", {
		animationEnabled: true,
		theme: "light2",
		axisY:{
			includeZero: false,
			gridThickness: 0,
			labelFontSize: 10,
			lineThickness: 0,
			tickLength: 0,
			valueFormatString:" "
		},
		axisX:{
			labelFontSize: 9,
			labelAngle: 118,
			interval:1,
			labelMaxWidth: 280
		},
		dataPointMaxWidth: 8,
		data: [{        
			type: "line",
			indexLabelFontSize: 9,
			name: "green",
			markerSize: 0,
			color: "#92D050",
			dataPoints: <?php echo json_encode($login_per_month_arr, JSON_NUMERIC_CHECK); ?>
		}]
	});
	chart.render();
	/* top  10 earners */	
	var chart = new CanvasJS.Chart("chartContainer-4", {
		animationEnabled: true,	
		title:{
			text:""
		},
		axisX:{
			interval: 1
		},
		axisY2:{
			interlacedColor: "#fff",
			gridColor: "#fff",
			title: "",
			lineThickness: 0,
			tickLength: 0,
			valueFormatString:" ",
			maximum: <?php echo $max_top_month_earner; ?>
		},
		dataPointMaxWidth: 8,
		data: [{
			type: "bar",
			name: "companies",
			axisYType: "secondary",
			color: "#92D050",
			indexLabel : '{y}',
			dataPoints: <?php echo json_encode($topMonthEarner, JSON_NUMERIC_CHECK); ?>
		}]
	});
	chart.render();
	/* top 10 redeemers */	
	var chart = new CanvasJS.Chart("chartContainer-5", {
		animationEnabled: true,
		
		title:{
			text:""
		},
		axisX:{
			interval: 1
		},
		axisY2:{
			interlacedColor: "#fff",
			gridColor: "#fff",
			title: "",
			lineThickness: 0,
			tickLength: 0,
			valueFormatString:" ",
			maximum: <?php echo $max_top_month_rewards; ?>
		},
		dataPointMaxWidth: 8,
		data: [{
			type: "bar",
			name: "companies",
			axisYType: "secondary",
			color: "#C00000",
			indexLabel : '{y}',
			dataPoints: <?php echo json_encode($topMonthRewards, JSON_NUMERIC_CHECK); ?>
		}]
	});
	chart.render();
	/* Top 10 Redeemers - Points View */
	var chart = new CanvasJS.Chart("chartContainer-6", {
		animationEnabled: true,
		
		title:{
			text:""
		},
		axisX:{
			interval: 1
		},
		axisY2:{
			interlacedColor: "#fff",
			gridColor: "#fff",
			title: "",
			lineThickness: 0,
			tickLength: 0,
			valueFormatString:" ",
			maximum: <?php echo $max_top_month_redeemers; ?>
		},
		
		dataPointMaxWidth: 8,
		data: [{
			type: "bar",
			name: "companies",
			axisYType: "secondary",
			color: "#ffd34d",
			indexLabel : '{y}',
			dataPoints: <?php echo json_encode($top_month_redeemers, JSON_NUMERIC_CHECK); ?>
		}]
	});
	chart.render();
	/* chartcontainer-7*/
	var chart = new CanvasJS.Chart("chartContainer-7", {
		animationEnabled: true,
		title:{
			text: ""
		},
		axisX:{
			title: "",
			labelAngle: 135,
			interval:1,
			labelFontSize: 9,
			labelMaxWidth: 280
		},
		axisY:{
			title:"",
			gridThickness: 0,
			lineThickness: 0,
			tickLength: 0,
			valueFormatString:" "
		},
		legend: {
			fontSize: 10,
		},
		toolTip:{ 
			shared: true
		},
		data: <?php echo json_encode($top_redeemer_per_tier, JSON_NUMERIC_CHECK); ?>
	});
	chart.render();
	/* chartcontainer-8 */
	 CanvasJS.addColorSet("greenShades",
			[
				"#c00000"          
			]);
	var chart = new CanvasJS.Chart("chartContainer-8", {
		animationEnabled: true,
		colorSet: "greenShades",
		theme: "light2",
		title:{
			text: ""
		},
		axisY: {
			title: "",
			gridThickness: 0,
			lineColor: "#ccc",
			tickColor: "#ccc",
			lineThickness: 0,
			tickLength: 0,
			valueFormatString:" ",
			maximum: <?php echo $max_category_redeemers; ?>,
		},
		axisX: {
			lineColor: "#ccc",
			labelMaxWidth: 76,
			tickColor: "#ccc",
		},
		dataPointWidth: 23,
		data: [{        
			type: "column",  
			showInLegend: false,
			legendMarkerColor: "#c00000",
			legendText: "",
			indexLabel : '{y}',
			dataPoints: <?php echo json_encode($category_redeemers, JSON_NUMERIC_CHECK); ?>
		}]
	});
	chart.render();
	}
	$(document).ready(function(){
		$('#create_pdf').click(function(){
			$('#loader').show();
			var chartContainer1 = $("#chartContainer-1 .canvasjs-chart-canvas").get(0);
			var dataURL1 = chartContainer1.toDataURL();
			$('#pdf-chartContainer-1').html('<img src="'+dataURL1+'" style="width:200"></img>');
			var chartContainer2 = $("#bar-chart-2 .canvasjs-chart-canvas").get(0);
			var dataURL2 = chartContainer2.toDataURL();
			$('#pdf-bar-chart-2').html('<img src="'+dataURL2+'" style="width:100%"></img>');
			var chartContainer = $("#chartContainer .canvasjs-chart-canvas").get(0);
			var dataURL = chartContainer.toDataURL();
			$('#pdf-chartContainer').html('<img src="'+dataURL+'" style="width:100%"></img>');
			var chartContainer4 = $("#chartContainer-4 .canvasjs-chart-canvas").get(0);
			var dataURL4 = chartContainer4.toDataURL();
			$('#pdf-chartContainer-4').html('<img src="'+dataURL4+'" style="width:100%"></img>');
			var chartContainer5 = $("#chartContainer-5 .canvasjs-chart-canvas").get(0);
			var dataURL5 = chartContainer5.toDataURL();
			$('#pdf-chartContainer-5').html('<img src="'+dataURL5+'" style="width:100%"></img>');
			var chartContainer6 = $("#chartContainer-6 .canvasjs-chart-canvas").get(0);
			var dataURL6 = chartContainer6.toDataURL();
			$('#pdf-chartContainer-6').html('<img src="'+dataURL6+'" style="width:100%"></img>');
			var chartContainer7= $("#chartContainer-7 .canvasjs-chart-canvas").get(0);
			var dataURL7 = chartContainer7.toDataURL();
			$('#pdf-chartContainer-7').html('<img src="'+dataURL7+'" style="width:100%"></img>');
			var chartContainer8 = $("#chartContainer-8 .canvasjs-chart-canvas").get(0);
			var dataURL8 = chartContainer8.toDataURL();
			$('#pdf-chartContainer-8').html('<img src="'+dataURL8+'" style="width:100%"></img>');
			$('input[name="html_inner"]').val($('#reportPage').html());
			/* $('#make_pdf').submit(); */
			
			var emails  = $("#email").val();
			var comment = $("#commentry_box").val();
			var html_inner = $("#html_inner").val();
			
			$.ajax({
				url: '<?php echo base_url; ?>/?class=Home&method=send_report',
				type:"POST",
				data:{ 
					email: emails,
					commentry_box: comment,
					html_inner: html_inner
				},
				success: function( data ) {
					$('#loader').hide();
					if(data == 'email empty'){
						$('#fail_msg').html('<div class="alert alert-danger">Email address should not be empty.</div>');
					}else if(data == 'fail'){
						$('#fail_msg').html('<div class="alert alert-danger">Server Error.</div>');
					}else{
						$('#shareReport').modal('hide');
						$('#success_modal').modal('show'); 
						$("#email").val('');
						$("#commentry_box").val('');
						$("#html_inner").val('');
						setTimeout(function() {$('#success_modal').modal('hide');}, 2000);
					}
				}	
			});
		});
		
		$('#print_pdf_html').click(function(){
			var chartContainer1 = $("#chartContainer-1 .canvasjs-chart-canvas").get(0);
			var dataURL1 = chartContainer1.toDataURL();
			$('#pdf-chartContainer-1').html('<img src="'+dataURL1+'" style="width:200"></img>');
			var chartContainer2 = $("#bar-chart-2 .canvasjs-chart-canvas").get(0);
			var dataURL2 = chartContainer2.toDataURL();
			$('#pdf-bar-chart-2').html('<img src="'+dataURL2+'" style="width:100%"></img>');
			var chartContainer = $("#chartContainer .canvasjs-chart-canvas").get(0);
			var dataURL = chartContainer.toDataURL();
			$('#pdf-chartContainer').html('<img src="'+dataURL+'" style="width:100%"></img>');
			var chartContainer4 = $("#chartContainer-4 .canvasjs-chart-canvas").get(0);
			var dataURL4 = chartContainer4.toDataURL();
			$('#pdf-chartContainer-4').html('<img src="'+dataURL4+'" style="width:100%"></img>');
			var chartContainer5 = $("#chartContainer-5 .canvasjs-chart-canvas").get(0);
			var dataURL5 = chartContainer5.toDataURL();
			$('#pdf-chartContainer-5').html('<img src="'+dataURL5+'" style="width:100%"></img>');
			var chartContainer6 = $("#chartContainer-6 .canvasjs-chart-canvas").get(0);
			var dataURL6 = chartContainer6.toDataURL();
			$('#pdf-chartContainer-6').html('<img src="'+dataURL6+'" style="width:100%"></img>');
			var chartContainer7= $("#chartContainer-7 .canvasjs-chart-canvas").get(0);
			var dataURL7 = chartContainer7.toDataURL();
			$('#pdf-chartContainer-7').html('<img src="'+dataURL7+'" style="width:100%"></img>');
			var chartContainer8 = $("#chartContainer-8 .canvasjs-chart-canvas").get(0);
			var dataURL8 = chartContainer8.toDataURL();
			$('#pdf-chartContainer-8').html('<img src="'+dataURL8+'" style="width:100%"></img>');
			$('input[name="print_pdf_html"]').val($('#reportPage').html());
			$('#print_pdf').submit();
		});
	});
	$('.tagsinput').tagsinput({confirmKeys: [13,44],tagClass: "label label-primary" });
	/* $(document).ready(function(){
		$('#create_pdf').click(function(){
			alert();
			return false;
			$.ajax({
				url: this.href,
				type: 'POST',
				dataType: 'html',
				success: function (data) {
					$('#container').html(data);
				}
			});
		});
	}); */
		</script>
	</body>
</html>