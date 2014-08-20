<?php defined('SYSPATH') or die('No direct script access.');

class Controller_User extends Controller_Base {

	public function action_index() {
		if (Auth::instance()->logged_in()){
			$user = Auth::instance()->get_user();
			$farmer = ORM::factory('farmer')->where('email', '=', $user->email)->find();
			$this->template->content = View::factory('user/account');
			if (!($farmer->hasAccessToken())) {
				$this->template->content->wepay = "<b>Please create an account to manage your money: <p><a class='wepay-widget-button wepay-blue' href=" . URL::base() . "wepayapi>Click here to create your WePay account</a>";
				$this->template->content->apiregister = "<a class='wepay-widget-button item-sold-out' href=" . URL::base() . "register>Click here to create your WePay account via API</a>";
				$this->template->content->token = false;
			} else {
				$this->template->content->wepay = '';
				$this->template->content->apiregister = '';
				$this->template->content->token = true;
			}

			$this->template->content->name = $farmer->name;
			$this->template->content->email = $farmer->email;
			$this->template->content->farm = $farmer->farm;
			$this->template->content->produce = $farmer->produce;
			$this->template->content->country = $farmer->country;
			$this->template->content->currencies = $farmer->currencies;
			$this->template->content->price = number_format($farmer->produce_price,2);
			$this->template->content->edit = true;
		}
		else {
			$this->template->content = View::factory('welcome/index');
		}

		$this->template->content->base = URL::base($this->request);
	}

	public function action_account() {
		$id = Request::current()->param('id');
		if (!isset($id)) {
			HTTP::redirect('/');
		}
		$farmer = ORM::factory('farmer')->where('id', '=', $id)->find();
		$this->template->content = View::factory('user/account');
		if (Auth::instance()->logged_in()) {
			$user = Auth::instance()->get_user();
			if ($farmer->email == $user->email) {
				$this->template->content->edit = true;
			}
			else {
				$this->template->content->edit = false;
			}

			if ($farmer->hasAccessToken()) {
				$this->template->content->token = true;
			}

			if (!($farmer->hasAccessToken())) {
				$this->template->content->wepay = "<b>Please create an account to manage your money: <p><a class='wepay-widget-button wepay-blue' href=" . URL::base() . "wepayapi>Click here to create your WePay account</a>";
				$this->template->content->apiregister = "<a class='wepay-widget-button item-sold-out' href=" . URL::base() . "register>Click here to create your WePay account via API</a>";
				$this->template->content->token = false;
			}
			else if (!($this->template->content->edit) && $farmer->hasAccountId()) {
				$this->template->content->wepay = "<a href=" . URL::base() . "user/buy/".$id." class='btn btn-danger btn-large' id='buy-now-button'>Buy ".$farmer->produce." Now!</a>";
				$this->template->content->apiregister = '';
			}
			else {
				$this->template->content->wepay = '';
				$this->template->content->apiregister = '';
			}
		}
		else {
			$this->template->content->wepay = '';
			$this->template->content->apiregister = '';
			if ($farmer->hasAccountId()) {
				$this->template->content->wepay = "<a href=". URL::base() . "user/buy/".$id." class='btn btn-danger btn-large' id='buy-now-button'>Buy ".$farmer->produce." Now!</a>";
			}
			$this->template->content->token = true;
			$this->template->content->edit = false;
		}
		$this->template->content->name = $farmer->name;
		$this->template->content->email = $farmer->email;
		$this->template->content->farm = $farmer->farm;
		$this->template->content->produce = $farmer->produce;
		$this->template->content->price = number_format($farmer->produce_price,2);
		$this->template->content->country = $farmer->country;
		$this->template->content->currencies = $farmer->currencies;
		$this->template->content->base = URL::base($this->request);
	}

	public function action_buy() {
		$id = Request::current()->param('id');
		if (!isset($id)) {
			HTTP::redirect('/');
		}
        	$farmer = ORM::factory('farmer')->where('id', '=', $id)->find();
		try {
			$checkout_uri = Controller_Wepayapi::create_checkout($farmer);
		}
		catch (WePayPermissionException $e) {
			$this->template->content = "There was an error: " . var_dump($e->getMessage());
			return;
		}

        	$this->template->content = View::factory('user/buy');
		$this->template->content->checkout_uri = $checkout_uri;
		$this->template->content->return_uri = URL::base() . '/user/account/'.$id;
		$this->template->content->name = $farmer->name;
		$this->template->content->email = $farmer->email;
		$this->template->content->farm = $farmer->farm;
		$this->template->content->produce = $farmer->produce;
                $this->template->content->country = $farmer->country;
                $this->template->content->currencies = $farmer->currencies;
		$this->template->content->price = number_format($farmer->produce_price,2); 
	}

	public function action_register(){
		$this->template->content = View::factory('user/register');
	}

	public function action_complete_registration() {

		$validation = Validation::factory($this->request->post())
			->rule('username', 'not_empty')
			->rule('password', 'not_empty')
            ->rule('password', 'min_length', array(':value', 6))
            ->rule('email', 'not_empty')
            ->rule('email', 'email')
            ->rule('price', 'numeric')
            ->rule('price', 'not_empty')
            ->rule('farm', 'not_empty')
            ->rule('currencies', 'not_empty')
            ->rule('country', 'not_empty')
            ->rule('produce', 'not_empty');

        	// Validation check
		if (!$validation->check()) {
			$errors = $validation->errors('user');
			$this->template->content = "Your registration was not valid!";
			return;
		}
        	// Create User
		$user = ORM::factory('User');
		$user->username = $_POST['username'];
		$user->email = $_POST['email'];
		$user->password = $_POST['password'];

		try {
			$user->save();
		} catch (ORM_Validation_Exception $e) {
			$this->template->content = "There was a problem creating your user: " . var_dump($e->errors());
			return;
		}

		// Create Farmer
		$farmer = ORM::factory('farmer');
		$farmer->name = $_POST['username'];
		$farmer->email = $_POST['email'];
		$farmer->farm = $_POST['farm'];
		$farmer->produce = $_POST['produce'];
		$farmer->produce_price = $_POST['price'];
		$farmer->currencies = $_POST['currencies'];
		$farmer->country = $_POST['country'];

        // Add login role
        $user->add('roles', ORM::factory('Role', array('name' => 'login')));

        try {
            $farmer->save();
        } catch (ORM_Validation_Exception $e) {
            $this->template->content = "There was a problem creating your farmer: " . var_dump($e->errors());
        }

		$success = Auth::instance()->login($_POST['email'], $_POST['password']);

		if ($success) {
			HTTP::redirect('user');
		} else{
			$this->template->content = "There was an error!";
		}
	}

	public function action_login(){
		$this->template->content = View::factory('user/login');
	}

	public function action_complete_login(){
		try{
			$post = Validation::factory($_POST)
				->rule('email', 'not_empty')
				->rule('email', 'email')
	            ->rule('password', 'not_empty')
	            ->rule('password', 'min_length', array('6'));
		} catch (Validation_Exception $e) {
			$this->template->content = "Your login was not valid: ".$e->errors();
		}

		$success = Auth::instance()->login($_POST['email'], $_POST['password']);

		if ($success){
			HTTP::redirect('user');
		} else{
			$this->template->content = "There was an error, try again";
		}
			
	}

	public function action_logout(){
		#Sign out the user
		Auth::instance()->logout();
 
		#redirect to the user account and then the signin page if logout worked as expected
		HTTP::redirect('/');	

	}

	public function action_edit(){
		if (Auth::instance()->logged_in()){
			$user = Auth::instance()->get_user();
			$farmer = ORM::factory('farmer')->where('email', '=', $user->email)->find();
			$this->template->content = View::factory('user/edit');
			$this->template->content->name = $farmer->name;
			$this->template->content->email = $farmer->email;
			$this->template->content->farm = $farmer->farm;
			$this->template->content->produce = $farmer->produce;
			$this->template->content->price = $farmer->produce_price;
			$this->template->content->country = $farmer->country;
			$this->template->content->currencies = $farmer->currencies;
		}
		else{
			$this->template->content = "Error, you're not logged in!";
		}
	}

    public function action_delete() {
		if (Auth::instance()->logged_in()){
			$this->template->content = "Delete? Really?";
			$user = Auth::instance()->get_user();
			$farmer = ORM::factory('farmer')->where('email', '=', $user->email)->find();

		    Auth::instance()->logout();
            $farmer->delete();
            $user->delete();
		    HTTP::redirect('/');	
		}
		else{
			$this->template->content = "Error, you're not logged in!";
		}

    }

	public function action_update(){
		try{
			$post = Validation::factory($_POST)
            ->rule('name', 'not_empty')
            ->rule('email', 'not_empty')
            ->rule('email', 'email')
            ->rule('price', 'numeric')
            ->rule('price', 'not_empty')
            ->rule('farm', 'not_empty')
            ->rule('produce', 'not_empty')
            ->rule('country', 'not_empty')
            ->rule('currencies', 'not_empty');

		} catch (Validation_Exception $e) {
			$this->template->content = "Your registration was not valid: ".$e->errors();
		}

		if (Auth::instance()->logged_in()){
			$user = Auth::instance()->get_user();
			$farmer = ORM::factory('farmer')->where('email', '=', $user->email)->find();
			$farmer->produce = $_POST['produce'];
			$farmer->farm = $_POST['farm'];
			$farmer->produce_price = $_POST['price'];
			$farmer->country = $_POST['country'];
			$farmer->currencies = $_POST['currencies'];
			$farmer->save();

			HTTP::redirect('user');
		}
		else{
			$this->template->content = "You can't update information for this user!";
		}	
	}
} 
