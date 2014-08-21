<?php

/*
 * Model that represents a farmer in WeFarm
 *
 */
class Model_farmer extends ORM {

    public function createAccount($access_token) {
        $wepay = new WePay($access_token);

        try {
            $response = $wepay->request('account/create/', array(
                    'name'          => $this->name,
                    'description'   => $this->name."'s WeFarm account",
                    'country'       => $this->country,
                    'currencies'    => explode(',' , $this->currencies)
                    ));
        }
        catch (Exception $e) {
            // Something went wrong - normally you would log
            // this and give your user a more informative message
            echo $e->getMessage();
            return false;
        }
        $this->saveAccountId($response->account_id);
        $this->saveAccessToken($access_token); 
        return true;
    }
   
    public function checkAccountStatus() {
	if(isset($this->wepay_access_token)) {
		$wepay = new WePay($this->wepay_access_token);
		try {
			$params = array('account_id'     => $this->wepay_account_id);
         		$response = $wepay->request('/account', $params);
		} catch (Exception $e) {
			var_dump($e->getMessage());
            		return "unknown";
		}
		return $response->state;
	}
	return "foobar";
    }

    public function get_update_uri() {
        if($this->wepay_access_token) {
                $wepay = new WePay($this->wepay_access_token);
                try {
                        $params = array('account_id'     => $this->wepay_account_id, 
                                         'mode'           => 'iframe');
                        $response = $wepay->request('/account/get_update_uri', $params);
                } catch (Exception $e) {
                        echo $e->getMessage();
                        return "unknown";
                }
                return $response->uri;
        }
        return "unknown";
    }

    public function saveAccessToken($access_token){
        $this->wepay_access_token = $access_token;
        $this->save();
    }

    public function saveAccountId($account_id){
        $this->wepay_account_id = $account_id;
        $this->save();
    }

    public function getAccessToken(){
        if (isset($this->wepay_access_token)){
            return $this->wepay_access_token;
        }
    }

    public function getAccountId(){
        if (isset($this->wepay_account_id)){
            return $this->wepay_account_id;
        }
    }

    public function hasAccessToken(){
        if (isset($this->wepay_access_token)){
            return true;
        }
        return false;
    }

    public function hasAccountId(){
        if (isset($this->wepay_account_id)){
            return true;
        }
        return false;
    }
}
