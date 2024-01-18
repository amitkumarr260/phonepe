<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include_once APPPATH . "third_party/phonepe/vendor/autoload.php";
use PhonePe\common\Env;
use PhonePe\payments\v1\PhonePePaymentClient;
use PhonePe\payments\v1\models\request\builders\PgPayRequestBuilder;
use PhonePe\payments\v1\models\request\builders\InstrumentBuilder;
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
        $merchantId = "M22OWW4P40ILA";
        $saltKey = "332f13d7-9735-4449-b40b-7fdbcab528b9";
        $saltIndex = 1;
        $env = Env::PROD;
        $shouldPublishEvents = true;
    
        $phonePePaymentsClient = new PhonePePaymentClient($merchantId, $saltKey, $saltIndex, $env, $shouldPublishEvents);
        $merchantTransactionId = substr(sha1(time()), 0, 38);
    
        $request = PgPayRequestBuilder::builder()
            ->mobileNumber("xxxxxxxxx")
            ->callbackUrl(site_url('payment/payment_failure'))
            ->merchantId($merchantId)  // Use the variable $merchantId here
            ->amount($this->input->post('amount')*100)  // Replace with the actual amount
            ->merchantTransactionId($merchantTransactionId)
            ->redirectUrl(site_url('payment/success?tr_no='.$merchantTransactionId))
            ->redirectMode("REDIRECT")
            ->paymentInstrument(InstrumentBuilder::buildPayPageInstrument())
            ->build();
    
        $response = $phonePePaymentsClient->pay($request);
        $pageUrl = $response->getInstrumentResponse()->getRedirectInfo()->getUrl();
        redirect($pageUrl);
        // You may want to use $pageUrl in further processing or redirect the user to this URL.
    }
    

    public function success() {
        $merchantId = "M22OWW4P40ILA";
        $saltKey = "332f13d7-9735-4449-b40b-7fdbcab528b9";
        $saltIndex = 1;
        $env = \PhonePe\Env::UAT;
        $shouldPublishEvents = true;
    
        $phonePePaymentsClient = new PhonePePaymentClient($merchantId, $saltKey, $saltIndex, $env, $shouldPublishEvents);
        $checkStatus = $phonePePaymentsClient->statusCheck($this->input->get('tr_no'));
        echo '<pre>';
        print_r($checkStatus); die;
        $this->load->view('payment_success');
    }

    public function failure() {
        $this->load->view('payment_failure');
    }
}
