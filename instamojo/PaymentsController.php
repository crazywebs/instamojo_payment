<?php
require "instamojo.php";

Class PaymentsController extends AppController{
	
	public $helpers = array('Html', 'Form', 'Session');
	
	function beforeFilter() {
        //$this->autoRender = false;
		$this->loadModel('Product');
	}
	public function request(){
		
		$this->layout = false;
		$this->autoRender = false;
		$data = $this->params['url'];
		$_SESSION["order_id"] = $data['order_id'];
		$amount = number_format($data['amount'],2, '.', '');
		$billing_email = $data['billing_email'];
		$billing_name = $data['billing_name'];
		$billing_tel = $data['billing_tel'];
		
		$api = new Instamojo('123456478978977', '123sdgsd4g56sg1g15d6fg4dfg');

		try {
			$response = $api->paymentRequestCreate(array( 
				"purpose" => "Purchase Payment",
				"amount" => $amount,
				"send_email" => false,
				"email" => $billing_email,
				//"buyer_name" =>$billing_name,
				"phone" =>$billing_tel,
				"redirect_url" => "http://www.xyz.com/Payments/response",
				"webhook" => "http://www.xyz/Payments/response"
				));
			//print_r($response);
			$_SESSION["payment_request_id"] = $response['id'];
			if($response['longurl']){
				$location = $response['longurl']."?embed=form";
				return $this->redirect($location);
			}
		}
		catch (Exception $e) {
			print('Error: ' . $e->getMessage());
		}
	}
	public function response(){
		$this->layout = false;
		$this->autoRender = false;
		$this->loadModel('Order');
		
		$id =$_SESSION["payment_request_id"];
		$order_id = $_SESSION["order_id"];
		
		$api = new Instamojo('1324564913215647', '1321ds4dsbv65dsbf1f145df5df6');

		try {
			$response = $api->paymentRequestStatus($id);
			$email = $response['payment_request']['email'];
			$mobile = $response['payment_request']['phone'];
			
			if($response['payment_request']['payments']['status']=='Credit'){
				
				echo "payment done";
			}
			else{
				echo "payment failed";
			}
			
			//print_r($response);
		}
		catch (Exception $e) {
			print('Error: ' . $e->getMessage());
		}
	}
}
?>