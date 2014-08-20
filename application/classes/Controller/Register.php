<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Register extends Controller_Base {

    public function action_index() {
	$farmer = null;
        if (Auth::instance()->logged_in()){
		$user = Auth::instance()->get_user();
		$farmer = ORM::factory('farmer')->where('email', '=', $user->email)->find();
	} 
    	else {
		HTTP::redirect('/');
	}

        $config = Kohana::$config->load('wepay');
        // set API Version. Change this to the API Version you want to use.
        $API_VERSION = "2014-01-08";
	$wepay = new WePay($farmer->wepay_access_token);
        $wepay->useCustom($config->get('client_id'), $config->get('client_secret'), $API_VERSION, $config->get('web_server'), $config->get('api_server'));
        $base_url = URL::site(NULL, TRUE);
	$scope = WePay::$all_scopes;
	$params = (array(
                        'client_id'     => $config->get('client_id'),
                        'client_secret' => $config->get('client_secret'),
                        'scope'         => implode(',', $scope),
			'redirect_uri'  => $base_url,
                        'first_name'    => explode(' ' , $farmer->name)[0], 
			'last_name'     => explode(' ' , $farmer->name)[1],
			'email'         => $farmer->email,
			'original_ip'   => $_SERVER['REMOTE_ADDR'],
			'original_device' => $_SERVER['HTTP_USER_AGENT'],
			'tos_acceptance_time' =>  $_SERVER['REQUEST_TIME'], 
 			'ca_debit_opt_in_time' => $_SERVER['REQUEST_TIME']
         ));
         $response = $wepay->request('user/register', $params);
	 if ($response) {
		if ($farmer->createAccount($response->access_token)) {
			$this->template->content = "WePay Account Created! You can now purchase goods! <a href=\"" . URL::base() . "\">Back</a>";
		} else {
			$this->template->content = "WePay Account Failed! <a href=\"" . URL::base() . "\">Back</a>";
		}
	}
	else {
		// Unable to obtain access token
		$this->template->content = "WePay Account Failed! <a href=\"" . URL::base() . "\">Back</a>";
	}
    }

    public static function create_checkout($merchant) {

        $config = Kohana::$config->load('wepay');
        // set API Version. Change this to the API Version you want to use.
        $API_VERSION = "2014-01-08";
        WePay::useCustom($config->get('client_id'), $config->get('client_secret'), $API_VERSION, $config->get('web_server'), $config->get('api_server'));
        $wepay = new WePay($merchant->getAccessToken());
        $response = $wepay->request('checkout/create/', array(
                    'account_id'          => $merchant->getAccountId(),
                    'short_description'   => "Purchasing ".$merchant->produce." from ".$merchant->name.".",
                    'type' 				  => 'goods',
                    'amount'			  => $merchant->produce_price,
                    'mode'				  => 'iframe',
                    'currency'            => $merchant->currencies
                    ));
        return $response->checkout_uri;
    }
}
