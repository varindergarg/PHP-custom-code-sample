<?php
$path = dirname(__FILE__); 
include_once("./App/Model/DB.php");
Class Home_Model{
	
	public function __construct(){
		$this->DB	=	new DB();
	}
	
	/*********** requierd data for filter home page **********/
	public function getCountryRegion()
	{
		$result = pg_query($this->DB->conn,"SELECT * FROM country_region");
		return pg_fetch_all($result);
	}
	
	public function getTier()
	{
		$result = pg_query($this->DB->conn,"SELECT * FROM tier");
		return pg_fetch_all($result);
	}
	
	public function getRewardProgram()
	{
		$result = pg_query($this->DB->conn,"SELECT reward_program_code FROM reward_program");
		return pg_fetch_all($result);
	}
	
	public function getAccountType()
	{
		$result = pg_query($this->DB->conn,"SELECT * FROM account_types");
		return pg_fetch_all($result);
	}
	
	public function getMemberStatus()
	{
		$result = pg_query($this->DB->conn,"SELECT * FROM member_status_types WHERE status IN('Active','Signed Up','On Hold','Inactive (6mth)','Inactive (9mth)','Inactive (12mth+)') AND is_deleted = 'F'");
		return pg_fetch_all($result);
	}
	
	public function getMemberType()
	{
		$result = pg_query($this->DB->conn,"SELECT * FROM member_types");
		return pg_fetch_all($result);
	}
	
	// HIGGSY UPDATE - get reward program name
	public function getRewardProgramName($reward_program_code)
	{
		$result = pg_query($this->DB->conn,"SELECT program_name FROM reward_program WHERE reward_program_code = '" . $reward_program_code . "' LIMIT 1");
        $row = pg_fetch_assoc($result);
		return $row['program_name'];
	}
    
    // HIGGSY UPDATE - get closing balance
	public function getClosingBalance($account_type, $month, $year)
	{
        $enddate = "'{$year}-{$month}-01 00:00:00'::timestamp + '1 month'::interval";
        $result = pg_query($this->DB->conn,"SELECT  sum(points) as closing_balance
                                            FROM    member_activity
                                            WHERE   account_type_id = {$account_type}
                                                    AND activity_date < {$enddate}
                                                    AND activity_type_id NOT IN (10)
                                            ");
        $row = pg_fetch_assoc($result);
		return $row['closing_balance'];
	}
	
	/************ Table-1 Start ************/
	/************ Counting total member per member status ***********/
	public function getMemberCountPerStatus($member_status_id,$filters)
	{
		extract($filters);
		$reward_program_code = "'".$reward_program_code."'";
		
		$more_filter = ' ';
		if(!empty($tier_id)){
			$more_filter .= "AND m.tier_id = $tier_id ";
		}
		if(!empty($member_type)){
			$more_filter .= "AND m.member_type_id = $member_status ";
		}
		
        // HIGGSY UPDATE - removed date_created from where clause
        // the proper where clause should be
        // WHERE date_joined_program <= last day of month of report (i.e. 01/month/year + 1 month)
        $enddate = "'{$year}-{$month}-01 00:00:00'::timestamp + '1 month'::interval";
		$result = pg_query($this->DB->conn,'SELECT COUNT(*) as total_member 
			FROM member m 
			WHERE m.member_status_id = '.$member_status_id.' 
            AND m.date_joined_program < ' . $enddate . '
            AND m.is_test_member = \'F\'
			--AND EXTRACT(month FROM "date_joined_program") = '.$month.' 
			--AND EXTRACT(year FROM "date_joined_program") = '.$year.' 
			'.$more_filter.'
			AND m.reward_program_code = '.$reward_program_code);
            
		return $result;
	}
	/************ Table-1 End ************/
	
	/************ Table-2 Start ************/
	/************ getting earned points by memebers monthly ***********/
	public function getEarnedPointsYearly($filters)
	{
		extract($filters);
		$reward_program_code = "'".$reward_program_code."'";
		$end_date   = $year.'-'.($month+1).'-01';
		$start_date = ($year -1).'-'.($month+1).'-01';
		
		$more_filter = ' ';
		if(!empty($tier_id)){
			$more_filter .= "AND m.tier_id = $tier_id ";
		}
		if(!empty($member_status)){
			$more_filter .= "AND m.member_status_id = $member_status ";
		}
		if(!empty($region_id)){
			$more_filter .= "AND mad.region_id = $region_id ";
		}
		if(!empty($member_type)){
			$more_filter .= "AND m.member_type_id = $member_type ";
		}
		
        // HIGGSY UPDATE - mad.address_type_id = 1
        // should *always* be in the where clause not just only if region_id is set
		$result = pg_query($this->DB->conn,"SELECT to_char(activity_date,'Mon') as month,
					extract(year from activity_date) as yyyy, SUM(points) AS earned_point 
					FROM member_activity ma 
					JOIN member m ON m.member_uid = ma.member_uid 
					JOIN member_address mad ON mad.member_uid = ma.member_uid 
					WHERE ma.activity_type_id IN (1,3) 
					AND m.reward_program_code = $reward_program_code 
					AND ma.activity_date >= '$start_date' 
					AND ma.activity_date < '$end_date' 
					AND account_type_id = $account_type 
                    AND mad.address_type_id = 1
					$more_filter
					GROUP BY 1,2,extract(month from activity_date) 
					ORDER BY yyyy ASC, extract(month from activity_date) ASC");
		
		return pg_fetch_all($result);
	}
	
	/************* getting redeemed points by memebers monthly ************/
	public function getRedeemPointsYearly($filters)
	{
		extract($filters);
		$reward_program_code = "'".$reward_program_code."'";
		$end_date   = $year.'-'.($month+1).'-01';
		$start_date = ($year -1).'-'.($month+1).'-01';
		
		$more_filter = '';
		if(!empty($tier_id)){
			$more_filter .= "AND m.tier_id = $tier_id ";
		}
		if(!empty($member_status)){
			$more_filter .= "AND m.member_status_id = $member_status ";
		}
		if(!empty($region_id)){
			$more_filter .= "AND mad.region_id = $region_id ";
		}
		if(!empty($member_type)){
			$more_filter .= "AND m.member_type_id = $member_type ";
		}
                    
        // HIGGSY UPDATE - mad.address_type_id = 1
        // should *always* be in the where clause not just only if region_id is set
		$result = pg_query($this->DB->conn,"SELECT to_char(activity_date,'Mon') as month,
					extract(year from activity_date) as yyyy, SUM(points) AS redeem_point 
					FROM member_activity ma 
					JOIN member m ON m.member_uid = ma.member_uid 
					JOIN member_address mad ON mad.member_uid = ma.member_uid 
					WHERE ma.activity_type_id IN (2,6) 
					AND m.reward_program_code = $reward_program_code 
                    AND m.is_test_member = 'F'
					AND ma.activity_date >= '$start_date' 
					AND ma.activity_date < '$end_date' 
					AND account_type_id = $account_type 
                    AND mad.address_type_id = 1
					$more_filter
					GROUP BY 1,2,extract(month from activity_date) 
					ORDER BY yyyy ASC, extract(month from activity_date) ASC");
		
		return pg_fetch_all($result);
	}
	
	
	/************* getting points balance (total) by members monthly ************/
    // HIGGSY UPDATE
    // the where clause 
    //     ma.activity_type_id NOT IN (10) 
    // excludes any points on hold, as we assume they will be released at some point
    // and so need to be included in the liability 
	public function getPointsBalanceMonthly($filters)
	{
		extract($filters);
		$reward_program_code = "'".$reward_program_code."'";
		$end_date   = $year.'-'.($month+1).'-01';
		$start_date = ($year -1).'-'.($month+1).'-01';
		
		$more_filter = '';
		if(!empty($tier_id)){
			$more_filter .= "AND m.tier_id = $tier_id ";
		}
		if(!empty($member_status)){
			$more_filter .= "AND m.member_status_id = $member_status ";
		}
		if(!empty($region_id)){
			$more_filter .= "AND mad.region_id = $region_id ";
		}
		if(!empty($member_type)){
			$more_filter .= "AND m.member_type_id = $member_type ";
		}
                    
        // HIGGSY UPDATE - mad.address_type_id = 1
        // should *always* be in the where clause not just only if region_id is set
        // no start date is required
        
        $result = array();

        for ($i = 11; $i >= 0; $i--) {
            // we need to get the points balance at the end of each of the previous months
            $enddate = "'{$year}-{$month}-01 00:00:00'::timestamp + '1 month'::interval - '" . $i . " months'::interval";

            $query = pg_query($this->DB->conn,"
                        SELECT month, yyyy, sum(points_balance) as points_balance
                        FROM (
                            SELECT  to_char(" . $enddate . " - '1 month'::interval,'Mon') as month,
                                    to_char(" . $enddate . " - '1 month'::interval,'yyyy') as yyyy, 
                                    SUM(points) AS points_balance 
                            FROM    member_activity ma 
                                    JOIN member m ON m.member_uid = ma.member_uid 
                                    JOIN member_address mad ON mad.member_uid = ma.member_uid 
                            WHERE   ma.activity_type_id NOT IN (10) 
                                    AND m.reward_program_code = $reward_program_code 
                                    AND m.is_test_member = 'F'
                                    AND ma.activity_date < " . $enddate . "
                                    AND account_type_id = $account_type 
                                    AND mad.address_type_id = 1
                                    $more_filter
                            GROUP BY 1,2,extract(month from activity_date) 
                        ) pb
                        GROUP BY month, yyyy
                        ");
            $row = pg_fetch_all($query);
            if ( isset( $row[0]) ){
            	$result[$i] = $row[0];
            }
        }

		return $result;
	}
	
	/************ getting dollor value of one reward point ************/
	public function getPointValueByReward($filters)
	{
		extract($filters);
		$reward_program_code = "'".$reward_program_code."'";
		$result = pg_query($this->DB->conn,'SELECT point_value FROM reward_program WHERE reward_program_code = '.$reward_program_code);
		return pg_fetch_assoc($result);
	}
	
	/************ counting the total ordered rewards by member for year *************/
	public function getRewardsRedeemed($filters)
	{		
		extract($filters);
		$reward_program_code = "'".$reward_program_code."'";
		$end_date   = $year.'-'.($month+1).'-01';
		$start_date = ($year -1).'-'.($month+1).'-01';
		
		$more_filter = ' ';
		if(!empty($tier_id)){
			$more_filter .= "AND m.tier_id = $tier_id ";
		}
		if(!empty($member_status)){
			$more_filter .= "AND m.member_status_id = $member_status ";
		}
		if(!empty($region_id)){
			$more_filter .= "AND mad.region_id = $region_id ";
		}
		if(!empty($member_type)){
			$more_filter .= "AND m.member_type_id = $member_type ";
		}
		
		$result = pg_query($this->DB->conn,"SELECT to_char(order_date,'Mon') as month,
					extract(year from order_date) as yyyy, COUNT(o.order_id) AS total_items 
					FROM member m 
					INNER JOIN \"order\" o on m.member_uid = o.member_uid 
					INNER JOIN order_line ol on o.order_uid = ol.order_uid 
					INNER JOIN member_address mad ON mad.member_uid = m.member_uid 
					WHERE m.reward_program_code = $reward_program_code 
					AND order_date >= '$start_date' 
					AND order_date < '$end_date' 
					AND ol.purchase_order_uid IS NOT NULL 
                    AND mad.address_type_id = 1
					$more_filter
					GROUP BY 1,2,extract(month from order_date) 
					ORDER BY yyyy ASC, extract(month from order_date) ASC");
		
		return pg_fetch_all($result);
	}
	/************ Table-2 End ************/
	
	/************ Table-3 Start ************/
	/************ getting avrage point earned by member for current  month ***********/
	public function getAvgPointEarnActiveMember($filters)
	{
		extract($filters);
		$reward_program_code = "'".$reward_program_code."'";
		
		$more_filter = ' ';
		if(!empty($tier_id)){
			$more_filter .= "AND m.tier_id = $tier_id ";
		}
		if(!empty($member_status)){
			$more_filter .= "AND m.member_status_id = $member_status ";
		}
		if(!empty($region_id)){
			$more_filter .= "AND mad.region_id = $region_id ";
		}
		if(!empty($member_type)){
			$more_filter .= "AND m.member_type_id = $member_type ";
		}
		
        // HIGGSY UPDATE - mad.address_type_id = 1
        // should *always* be in the where clause not just only if region_id is set
		$result = pg_query($this->DB->conn,'SELECT SUM(points) AS earned_point 
			FROM member_activity ma
			INNER JOIN member m ON m.member_uid = ma.member_uid 
			INNER JOIN member_address mad ON mad.member_uid = m.member_uid 
			WHERE ma.activity_type_id IN (1,3) 
            AND mad.address_type_id = 1
			AND EXTRACT(month FROM "activity_date") = '.$month.' 
			AND EXTRACT(year FROM "activity_date") = '.$year.' 
			AND m.reward_program_code = '.$reward_program_code.' 
            AND m.is_test_member = \'F\'
			AND account_type_id = '.$account_type.' '.$more_filter);
		return pg_fetch_assoc($result);
	}
	
	/************ getting avrage point redeemed by member for current  month ***********/
	public function getAvgPointRedeemActiveMember($filters)
	{
		extract($filters);
		$reward_program_code = "'".$reward_program_code."'";
		
		$more_filter = ' ';
		if(!empty($tier_id)){
			$more_filter .= "AND m.tier_id = $tier_id ";
		}
		if(!empty($member_status)){
			$more_filter .= "AND m.member_status_id = $member_status ";
		}
		if(!empty($region_id)){
			$more_filter .= "AND mad.region_id = $region_id ";
		}
		if(!empty($member_type)){
			$more_filter .= "AND m.member_type_id = $member_type ";
		}
		
        // HIGGSY UPDATE - mad.address_type_id = 1
        // should *always* be in the where clause not just only if region_id is set
		$result = pg_query($this->DB->conn,'SELECT SUM(points) AS redeem_point 
			FROM member_activity ma 
			INNER JOIN member m ON m.member_uid = ma.member_uid 
			INNER JOIN member_address mad ON mad.member_uid = m.member_uid 
			WHERE ma.activity_type_id IN (2,6) 
            AND mad.address_type_id = 1
			AND EXTRACT(month FROM "activity_date") = '.$month.' 
			AND EXTRACT(year FROM "activity_date") = '.$year.' 
			AND m.reward_program_code = '.$reward_program_code.' 
            AND m.is_test_member = \'F\'
			AND account_type_id = '.$account_type.' '.$more_filter);
		return pg_fetch_assoc($result);
	}
	
	/************ counting total earned point by memebers for one year ***********/
	public function getEarnPointPerYear($filters)
	{
		extract($filters);
		$reward_program_code = "'".$reward_program_code."'";
		
		$more_filter = ' ';
		if(!empty($tier_id)){
			$more_filter .= "AND m.tier_id = $tier_id ";
		}
		if(!empty($member_status)){
			$more_filter .= "AND m.member_status_id = $member_status ";
		}
		if(!empty($region_id)){
			$more_filter .= "AND mad.region_id = $region_id ";
		}
		if(!empty($member_type)){
			$more_filter .= "AND m.member_type_id = $member_type ";
		}
		
        // HIGGSY UPDATE - mad.address_type_id = 1
        // should *always* be in the where clause not just only if region_id is set
        
        // this needs to be the points earned in the 12 months of the report
        // i.e. if the report is for March 2020
        // points should be points earned April 2019 to (end) March 2020
        
        $end_date   = $year.'-'.($month+1).'-01';
		$start_date = ($year -1).'-'.($month+1).'-01';
        
		$result = pg_query($this->DB->conn,"SELECT to_char(activity_date, 'YYYY-MM'),SUM(points) AS earned_point 
			FROM member_activity ma 
			INNER JOIN member m ON m.member_uid = ma.member_uid 
			INNER JOIN member_address mad ON mad.member_uid = m.member_uid 
			WHERE ma.activity_type_id IN (1,3) 
            AND mad.address_type_id = 1
			--AND activity_date > date_trunc('month', CURRENT_DATE) - INTERVAL '12 month' 
            AND activity_date >= '$start_date' 
            AND activity_date < '$end_date'             
			AND m.reward_program_code = $reward_program_code 
            AND m.is_test_member = 'F'
			AND account_type_id = $account_type 
			$more_filter 
			GROUP BY 1");
		return $result;
	}
	
	/************ counting total redeemed point by memebers for one year ***********/
	public function getRedeemPointPerYear($filters)
	{
		extract($filters);
		$reward_program_code = "'".$reward_program_code."'";
		$end_date   = $year.'-'.($month+1).'-01';
		$start_date = ($year -1).'-'.($month+1).'-01';
		
		$more_filter = ' ';
		if(!empty($tier_id)){
			$more_filter .= "AND m.tier_id = $tier_id ";
		}
		if(!empty($member_status)){
			$more_filter .= "AND m.member_status_id = $member_status ";
		}
		if(!empty($region_id)){
			$more_filter .= "AND mad.region_id = $region_id ";
		}
		if(!empty($member_type)){
			$more_filter .= "AND m.member_type_id = $member_type ";
		}
		
        // HIGGSY UPDATE - mad.address_type_id = 1
        // should *always* be in the where clause not just only if region_id is set
		$result = pg_query($this->DB->conn,"SELECT to_char(activity_date, 'YYYY-MM'),SUM(points) AS redeem_point 
			FROM member_activity ma 
			INNER JOIN member m ON m.member_uid = ma.member_uid 
			INNER JOIN member_address mad ON mad.member_uid = m.member_uid 
			WHERE ma.activity_type_id IN (2,6) 
            AND mad.address_type_id = 1
			AND ma.activity_date >= '$start_date' 
			AND ma.activity_date < '$end_date' 
			AND m.reward_program_code = $reward_program_code 
			AND account_type_id = $account_type 
			$more_filter 
			GROUP BY 1");
		return $result;
	}
	/************ Table-3 End ************/
	
	/************ Chart-1 Start ************/
	/************ counting members per tier for one year ************/
	public function getMemberPerTier($filters)
	{
		extract($filters);
		
		$more_filter = ' ';
		if(!empty($tier_id)){
			$more_filter .= "AND m.tier_id = $tier_id ";
		}
		if(!empty($member_status)){
			$more_filter .= "AND m.member_status_id = $member_status ";
		}
        
		if(!empty($member_type)){
			$more_filter .= "AND m.member_type_id = $member_type ";
		}
		
        $enddate = "'{$year}-{$month}-01 00:00:00'::timestamp + '1 month'::interval";
        
		$result = pg_query($this->DB->conn,"SELECT COUNT(*) as y,tier.tier_description as label 
			FROM member m 
			INNER JOIN tier ON tier.tier_id = m.tier_id
			WHERE m.reward_program_code = '$reward_program_code' 
            and m.member_status_id = 4
            and m.is_test_member = 'F'
            AND m.date_joined_program < {$enddate}
			$more_filter
			GROUP BY m.tier_id,tier.tier_description");
		return $result;
	}
	/************ Chart-1 End ************/
	
	/************ Chart-3 Start ************/
	/************ Counting number of member access website by each month for one year ************/
	public function getLoginPerMonth($filters)
	{
		extract($filters);
		$reward_program_code = "'".$reward_program_code."'";
		$selected_month = $year.'-'.$month.'-01';
		$selected_month = strtotime("$selected_month -11 month");
		$this_month = mktime(0, 0, 0, date('m',$selected_month), 1, date('Y',$selected_month));
		$total_login  = array();
		
		for ($i = 0; $i < 12; $i++) {
			$sel_month = date('m', strtotime($i.' month', $this_month));
			$sel_year  = date('Y', strtotime($i.' month', $this_month));
			
			$result = pg_query($this->DB->conn,'SELECT COUNT(mlh.login_history_id) AS logins
				FROM member m
				INNER JOIN member_login ml ON m.member_uid = ml.member_uid
				INNER JOIN member_login_history mlh ON ml.member_login_uid = mlh.member_login_uid
				WHERE EXTRACT(month FROM "login_date") = '.$sel_month.' 
				AND EXTRACT(year FROM "login_date") = '.$sel_year.' 
				AND m.is_test_member = \'F\'
                AND m.reward_program_code = '.$reward_program_code);
				
			$key = date('My',strtotime($sel_year.'-'.$sel_month));
			$total_login[$key] = pg_fetch_assoc($result);
			
		}
		return $total_login;
	}
	/************ Chart-3 End ************/
	
	/************ Chart-4 Start ************/
	/************ Counting point of contact that the member utilized for one year from selected month/year ************/
	public function getOpenTaskByMember($filters)
	{
		extract($filters);
		$reward_program_code = "'".$reward_program_code."'";
		
		$more_filter = ' ';
		if(!empty($tier_id)){
			$more_filter .= "AND m.tier_id = $tier_id ";
		}
		if(!empty($member_status)){
			$more_filter .= "AND m.member_status_id = $member_status ";
		}
		if(!empty($member_type)){
			$more_filter .= "AND m.member_type_id = $member_type ";
		}
		
		$end_date   = $year.'-'.($month+1).'-01';
		$start_date = ($year -1).'-'.($month+1).'-01';
		$result = pg_query($this->DB->conn,"SELECT COUNT(tmdt.direction_id),tmdt.direction_name 
			FROM member m 
			INNER JOIN task ON task.member_uid = m.member_uid 
			INNER JOIN task_message ON task_message.task_uid = task.task_uid 
			INNER JOIN task_message_direction_types tmdt ON task_message.direction_id = tmdt.direction_id 
			WHERE task_date BETWEEN '$start_date' AND '$end_date'
			AND m.reward_program_code = $reward_program_code 
            AND m.is_test_member = 'F'
            and tmdt.direction_id IN(1,3) -- 'Inbound Call','Email (Inbound)'
            AND task_message.task_message_type_id <> 0
            AND tmt.is_system_message = 'F'
			$more_filter 
			GROUP BY tmdt.direction_id,tmdt.direction_name");
		return pg_fetch_all($result);
	}
	
	public function getWebsiteAccessMember($filters)
	{
		extract($filters);
		$reward_program_code = "'".$reward_program_code."'";
		
		$more_filter = ' ';
		if(!empty($tier_id)){
			$more_filter .= "AND m.tier_id = $tier_id ";
		}
		if(!empty($member_status)){
			$more_filter .= "AND m.member_status_id = $member_status ";
		}
		if(!empty($region_id)){
			$more_filter .= "AND mad.region_id = $region_id ";
		}
		if(!empty($member_type)){
			$more_filter .= "AND m.member_type_id = $member_type ";
		}
		
        // HIGGSY UPDATE - mad.address_type_id = 1
        // should *always* be in the where clause not just only if region_id is set
		$end_date   = $year.'-'.($month+1).'-01';
		$start_date = ($year -1).'-'.($month+1).'-01';
		$result = pg_query($this->DB->conn,"SELECT COUNT(mlh.login_history_id) AS total_website_acess
			FROM member m 
			INNER JOIN member_login ml on m.member_uid = ml.member_uid 
			INNER JOIN member_login_history mlh on ml.member_login_uid = mlh.member_login_uid 
			INNER JOIN member_address mad ON mad.member_uid = m.member_uid 
			WHERE m.reward_program_code = $reward_program_code 
            AND m.is_test_member = 'F'
			AND mlh.login_date >= '$start_date' 
			AND mlh.login_date < '$end_date' 
            AND mad.address_type_id = 1 
			$more_filter");
		return pg_fetch_assoc($result);
	}
	/************ Chart-4 End ************/
	
	/************ Chart-5 Start ************/
	/************ getting top 10 earner for selected month with name and sum of points ************/
	public function getTopMonthEarner($filters)
	{
		extract($filters);
		$reward_program_code = "'".$reward_program_code."'";
		
		$more_filter = ' ';
		if(!empty($tier_id)){
			$more_filter .= "AND m.tier_id = $tier_id ";
		}
		if(!empty($member_status)){
			$more_filter .= "AND m.member_status_id = $member_status ";
		}
		if(!empty($region_id)){
			$more_filter .= "AND mad.region_id = $region_id ";
		}
		if(!empty($member_type)){
			$more_filter .= "AND m.member_type_id = $member_type ";
		}
		
        // HIGGSY UPDATE - mad.address_type_id = 1
        // should *always* be in the where clause not just only if region_id is set
		$result = pg_query($this->DB->conn,'SELECT SUM(points) AS y,m.display_name as label, SUM(points) AS indexLabel 
			FROM member_activity ma 
			INNER JOIN member m ON m.member_uid = ma.member_uid 
			INNER JOIN member_address mad ON mad.member_uid = m.member_uid 
			WHERE ma.activity_type_id IN (1,3) 
			AND EXTRACT(month FROM "activity_date") = '.$month.' 
			AND EXTRACT(year FROM "activity_date") = '.$year.' 
			AND m.reward_program_code = '.$reward_program_code.' 
            AND m.is_test_member = \'F\'
			AND account_type_id = '.$account_type.' 
            AND mad.address_type_id = 1 
			'.$more_filter.' 
			GROUP BY ma.member_uid,m.display_name 
			ORDER BY y DESC LIMIT 10');
		return $result;
	}
	/************ Chart-5 End ************/
	
	/************ Chart-6 Start ************/
	/************ getting top 10 reward redeemer for selected month with name and sum of rewards ************/
	public function getTopMonthRewards($filters)
	{
		extract($filters);
		$reward_program_code = "'".$reward_program_code."'";
		
		$more_filter = ' ';
		if(!empty($tier_id)){
			$more_filter .= "AND m.tier_id = $tier_id ";
		}
		if(!empty($member_status)){
			$more_filter .= "AND m.member_status_id = $member_status ";
		}
		if(!empty($region_id)){
			$more_filter .= "AND mad.region_id = $region_id ";
		}
		if(!empty($member_type)){
			$more_filter .= "AND m.member_type_id = $member_type ";
		}
		
        // HIGGSY UPDATE - mad.address_type_id = 1
        // should *always* be in the where clause not just only if region_id is set
		$result = pg_query($this->DB->conn,'SELECT COUNT(o.order_id) AS y,m.display_name as label, COUNT(o.order_id) AS indexLabel
			FROM member m
			INNER JOIN "order" o on m.member_uid = o.member_uid
			INNER JOIN order_line ol on o.order_uid = ol.order_uid 
            INNER JOIN purchase_order po on ol.purchase_order_uid = po.purchase_order_uid
			INNER JOIN member_address mad ON mad.member_uid = m.member_uid 
			WHERE EXTRACT(month FROM po.po_date) = '.$month.' 
			AND EXTRACT(year FROM po.po_date) = '.$year.' 
			AND m.reward_program_code = '.$reward_program_code.' 
            AND m.is_test_member = \'F\'
			AND ol.purchase_order_uid is not null
            AND mad.address_type_id = 1 
			'.$more_filter.' 
			GROUP BY ol.order_qty,m.display_name
			ORDER BY y DESC LIMIT 10');
		return pg_fetch_all($result);
	}
	/************ Chart-6 End ************/
	
	/************ Chart-7 Start ************/
	/************ getting top 10 point redeemer for selected month with name and sum of points ************/
	public function getTopMonthRedeemer($filters)
	{
		extract($filters);
		$reward_program_code = "'".$reward_program_code."'";
		
		$more_filter = ' ';
		if(!empty($tier_id)){
			$more_filter .= "AND m.tier_id = $tier_id ";
		}
		if(!empty($member_status)){
			$more_filter .= "AND m.member_status_id = $member_status ";
		}
		if(!empty($region_id)){
			$more_filter .= "AND mad.region_id = $region_id ";
		}
		if(!empty($member_type)){
			$more_filter .= "AND m.member_type_id = $member_type ";
		}
		
        // HIGGSY UPDATE - mad.address_type_id = 1
        // should *always* be in the where clause not just only if region_id is set
		$result = pg_query($this->DB->conn,'SELECT ABS(SUM(points)) AS y,m.display_name as label, ABS(SUM(points)) AS indexLabel 
			FROM member_activity ma 
			INNER JOIN member m ON m.member_uid = ma.member_uid 
			INNER JOIN member_address mad ON mad.member_uid = ma.member_uid 
			WHERE ma.activity_type_id IN (2,6) 
			AND EXTRACT(month FROM "activity_date") = '.$month.' 
			AND EXTRACT(year FROM "activity_date") = '.$year.' 
			AND m.reward_program_code = '.$reward_program_code.' 
            AND m.is_test_member = \'F\'
			AND account_type_id = '.$account_type.' 
            AND mad.address_type_id = 1 
			'.$more_filter.' 
			GROUP BY ma.member_uid,m.display_name 
			ORDER BY y DESC 
			LIMIT 10');
		return $result;
	}
	/************ Chart-7 End ************/
	
	/************ Chart-8 Start ************/
	/************ Counting top rewards redeemer per tier for one year and month wise ************/
	
	public function getTopRedeemerPerTier($filters)
	{
        
        // HIGGSY - UPDATE
        // we'll use number of orders as value
        // and not sum(quantity) because donations and transfers skew the data.
        
        // SARITA to fix
        // the data is in the correct order
        // but the labels on the axis are not in the correct order!
        
		extract($filters);
		$reward_program_code = "'".$reward_program_code."'";
		$result     = pg_query($this->DB->conn,'SELECT tier_id FROM "member" WHERE reward_program_code = '.$reward_program_code.' GROUP BY tier_id');
		$tier_ids   = pg_fetch_all($result);
		$total_sum  = array();
		$end_date   = $year.'-'.($month+1).'-01';
		$start_date = ($year -1).'-'.($month+1).'-01';
		
		if(!empty($tier_ids)){
			foreach($tier_ids as $key=>$t_id){
				$tier_id = $t_id['tier_id'];
				$result = pg_query($this->DB->conn,"SELECT to_char(po.po_date,'Mon') as month,
					extract(year from po.po_date) as yyyy,
                    --SUM(ol.order_qty) AS total_items
                    COUNT(o.order_id) as total_items
					FROM \"member\" m 
					INNER JOIN \"order\" o on m.member_uid = o.member_uid 
					INNER JOIN order_line ol on o.order_uid = ol.order_uid 
                    INNER JOIN purchase_order po on ol.purchase_order_uid = po.purchase_order_uid
					WHERE m.reward_program_code = $reward_program_code 
					AND po.po_date >= '$start_date' 
					AND po.po_date < '$end_date' 
					AND m.tier_id = $tier_id 
                    AND m.is_test_member = 'F'
					AND ol.purchase_order_uid is not null 
					GROUP BY 1,2, yyyy, to_char(po.po_date,'MM')
                    ORDER BY yyyy, to_char(po.po_date,'MM')");
				$total_sum[$t_id['tier_id']] = pg_fetch_all($result);
			}
		}
		return $total_sum;
	}
	/************ Chart-8 End ************/
	
	/************ Chart-9 Start ************/
	/************ This is a line chart displaying the number of rewards redeemed per members of a giving tier, during the 12 months selected for the report ************/
	public function getRewardPerCategory($filters)
	{
		extract($filters);
		$reward_program_code = "'".$reward_program_code."'";
		$end_date   = $year.'-'.($month+1).'-01';
		$start_date = $year.'-'.$month.'-01';
		$result = pg_query($this->DB->conn,"SELECT rc.reporting_category, COUNT(o.order_id) AS num_orders
				FROM \"member\" m 
				INNER JOIN \"order\" o on m.member_uid = o.member_uid 
				INNER JOIN order_line ol on o.order_uid = ol.order_uid 
                INNER JOIN purchase_order po on ol.purchase_order_uid = po.purchase_order_uid 
				INNER JOIN product p on ol.product_code = p.product_code 
				INNER JOIN(
					SELECT cr.reporting_category_id, 
					case when 
					POSITION('>' in 
					replace(
						replace(
						replace(coalesce(cr3.category_name,'') || '>' || 
						coalesce(cr2.category_name,'') || '>' || 
						coalesce(cr1.category_name,'') || '>' || 
						coalesce(cr.category_name,''), 
						'>>1:STD REPORT>', '') ,
						'>1:STD REPORT>', ''),
					'1:STD REPORT>', '') ) = 0 then
					replace(
						replace(
						replace(coalesce(cr3.category_name,'') || '>' || 
						coalesce(cr2.category_name,'') || '>' || 
						coalesce(cr1.category_name,'') || '>' || 
						coalesce(cr.category_name,''), 
						'>>1:STD REPORT>', '') ,
						'>1:STD REPORT>', ''),
					'1:STD REPORT>', '') 
					else 
					substring(
						replace(
							replace(
							replace(coalesce(cr3.category_name,'') || '>' || 
							coalesce(cr2.category_name,'') || '>' || 
							coalesce(cr1.category_name,'') || '>' || 
							coalesce(cr.category_name,''), 
							'>>1:STD REPORT>', '') ,
							'>1:STD REPORT>', ''),
						'1:STD REPORT>', '')  from 1 for POSITION('>' in 
						replace(
							replace(
							replace(coalesce(cr3.category_name,'') || '>' || 
							coalesce(cr2.category_name,'') || '>' || 
							coalesce(cr1.category_name,'') || '>' || 
							coalesce(cr.category_name,''), 
							'>>1:STD REPORT>', ''), 
							'>1:STD REPORT>', ''), 
					'1:STD REPORT>', '') ) -1 ) 
					end as reporting_category 
					FROM category_reporting AS cr 
					LEFT JOIN category_reporting AS cr1 ON cr.parent_category_id = cr1.reporting_category_id 
					LEFT JOIN category_reporting AS cr2 ON cr1.parent_category_id = cr2.reporting_category_id 
					LEFT JOIN category_reporting AS cr3 ON cr2.parent_category_id = cr3.reporting_category_id 
				) rc on p.reporting_category_id = rc.reporting_category_id 
				WHERE m.reward_program_code = $reward_program_code 
                AND m.is_test_member = 'F'
				AND ol.purchase_order_uid is not null 
				AND po.po_date >= '$start_date' 
				AND po.po_date < '$end_date' 
				GROUP BY rc.reporting_category 
				ORDER BY count(o.order_id) DESC
				LIMIT 10");
		
		return pg_fetch_all($result);
	}
	/************ Chart-9 End ************/
}