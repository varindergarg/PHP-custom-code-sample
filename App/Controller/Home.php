<?php 
include_once("./App/Model/Home_Model.php");
require_once './vendor/phpmailer/PHPMailerAutoload.php';
require_once './vendor/phpmailer/class.smtp.php';

require_once './vendor/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

class Home {
	public $model;	

	public function __construct()  
	{  
		$this->model = new Home_Model();
	} 
	
    public function index()
	{
		
			$data['account_type'] 	 = $this->model->getAccountType();
			$data['reward_program']  = $this->model->getRewardProgram();
			$data['country_region']  = $this->model->getCountryRegion();
			$data['tiers']           = $this->model->getTier();
			$data['member_status']   = $this->model->getMemberStatus();
			$data['member_type']     = $this->model->getMemberType();
			include('./Resources/Views/Home.php'); 
		
    }
	
	public function search()
	{
		if($_SERVER['REQUEST_METHOD'] === 'POST'){
			$data['member_status_types'] = $this->model->getMemberStatus();
 			$data['get_earned_points_yearly']         = $this->model->getEarnedPointsYearly($_POST);
 			$data['get_redeem_points_yearly']         = $this->model->getRedeemPointsYearly($_POST);
 			
            $data['get_points_balance_monthly']        = $this->model->getPointsBalanceMonthly($_POST);       // HIGGSY added
            
			$data['point_value_by_reward']            = $this->model->getPointValueByReward($_POST);
			$data['rewards_redeemed']                 = $this->model->getRewardsRedeemed($_POST);
 			$data['avg_point_earn_active_member']     = $this->model->getAvgPointEarnActiveMember($_POST);
 			$data['avg_point_redeem_active_member']   = $this->model->getAvgPointRedeemActiveMember($_POST);
 			$data['earn_point_per_year']         = $this->model->getEarnPointPerYear($_POST);
 			$data['get_redeem_point_per_year']   = $this->model->getRedeemPointPerYear($_POST);
 			$data['member_per_tier']    = $this->model->getMemberPerTier($_POST);
 			$data['login_per_month']    = $this->model->getLoginPerMonth($_POST);
			$data['top_month_earner']   = $this->model->getTopMonthEarner($_POST);
			$data['top_month_rewards']  = $this->model->getTopMonthRewards($_POST);
			$data['top_month_redeemer'] = $this->model->getTopMonthRedeemer($_POST);
			$data['open_task_by_member']= $this->model->getOpenTaskByMember($_POST);
			$data['access_website_member']= $this->model->getWebsiteAccessMember($_POST);
			$data['rewards_per_category'] = $this->model->getRewardPerCategory($_POST);
			$data['top_redeemer_per_tier']= $this->model->getTopRedeemerPerTier($_POST);
			include('./Resources/Views/dashboard.php');
		}
	}	

	public function send_report()
	{		
		if(empty($_POST['email'])){
			echo 'email empty';
		}else{
			
			$file_name = uniqid().'.pdf';
			$html = $_POST['html_inner'];
			$pdf  = new Dompdf();
			$pdf->load_html($html);
			$customPaper = array(0,0,1123,1123);
			$pdf->set_paper('a4', 'landscape');
			$pdf->render();
			$pdf_name = $pdf->output();
			$file_location = $_SERVER['DOCUMENT_ROOT']."/reward_program_dashboard/Uploads/pdf/".$file_name;
			file_put_contents($file_location,$pdf_name); 	
			
			$subject = 'Report';
			$email_content = $_POST['commentry_box'];
			$config = array(
				'charset'=>'utf-8',
				'wordwrap'=> TRUE,
				'mailtype' => 'html'
			);

			//Create a new PHPMailer instance
			$mail = new PHPMailer;
			$mail->isSMTP();
			$mail->Host = 'Host name';
			$mail->Port = 587;
			$mail->SMTPSecure = 'tls';
			$mail->SMTPAuth = true;
			$mail->Username = "email";
			$mail->Password = "*********";
			$mail->setFrom('Name', 'IC Automation');
			$emails = explode(',',$_POST['email']);
			foreach ($emails as $email) {
				$mail->AddAddress( trim($email) );       
			}
			
			$mail->Body = ($_POST['commentry_box']) ? $_POST['commentry_box'] : "Report";
			$mail->Subject = 'Reward Program Report';
			$mail->AddAttachment($file_location);	
			if(!$mail->send()) {
				echo 'fail';
			}else{
				unlink($file_location);
				echo 'success';
			}
		}
	}
	
	public function save_report()
	{
		$file_name = uniqid().'.pdf';
		header("Content-Disposition: attachment; filename=" . $file_name);
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		
		$html = $_POST['print_pdf_html'];
		$pdf  = new Dompdf();
		$pdf->load_html($html);
		$customPaper = array(0,0,1123,1123);
		$pdf->set_paper('a4', 'landscape');
		$pdf->render();
		$pdf->stream($file_name, array("Attachment" => false));
	}
}
?>