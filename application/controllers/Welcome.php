<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/userguide3/general/urls.html
	 */
	public function index()
	{
		$data['amount'] = 100;
		$this->load->view('welcome_message', $data);
	}
	public function process() {
    
        // PhonePe API credentials
        // API URL
        $url = 'https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/pay';
    
        // API request data
        $merchantTransactionId = substr(sha1(time()), 0, 38);
        $requestData = [
            'merchantId' => 'PGTESTPAYUAT',
            'merchantTransactionId' => $merchantTransactionId,
            'merchantUserId' => 'MUID123',
            'amount' => $this->input->post('amount')*100,
            'redirectUrl' => site_url('payment/success'),
            'redirectMode' => 'REDIRECT',
            'callbackUrl' => site_url('payment/payment_failure'),
            'mobileNumber' => '9999999999',
            'paymentInstrument' => [
                'type' => 'PAY_PAGE',
            ],
        ];
    
$base64Data = base64_encode(json_encode($requestData));
$saltkey = '099eb0cd-02cf-4e2a-8aca-3e6c6aff0399';
$saltIndex = 1;
$string = $base64Data . '/pg/v1/pay' . $saltkey;
$sha256 = hash('sha256', $string);
$finalxheader = $sha256 . '###' . $saltIndex;

// API call using native cURL functions
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['request' => $base64Data])); // Pass the associative array directly
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-VERIFY: ' . $finalxheader,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Handle the API response
$rdata = json_decode($response);
redirect($rdata->data->instrumentResponse->redirectInfo->url);
    }
    

    public function success() {
        $input = $this->input->post(); // Assuming the callback data is sent via POST
    print_r($input); die;

        // Log the callback data for debugging
        log_message('info', 'Callback data: ' . json_encode($input));
    
        // Check if the payment was successful based on the callback data
        if (isset($input['status']) && $input['status'] === 'success') {
            // Payment was successful
            $transactionId = isset($input['transactionId']) ? $input['transactionId'] : '';
            // Extract other relevant information as needed
    
            // Perform any necessary actions with the transaction details (e.g., store in the database)
    
            $data['transactionId'] = $transactionId;
    
            // Load the view for success page with transaction details
            $this->load->view('payment_success', $data);
        } else {
            // Payment failed or other status
            $this->load->view('payment_failure');
        }
        //$this->load->view('payment_success');
    }

    public function failure() {
        $this->load->view('payment_failure');
    }
}
