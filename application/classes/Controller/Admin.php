<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin extends Controller_Base {

        public function action_index() {
                $users = ORM::factory('User')->where('last_login', '<', DB::expr('DATE_SUB(NOW(), INTERVAL 1 DAY)'))->find_all();
                foreach ($users as $user) {
                        $farmer = ORM::factory('farmer')->where('email', '=', $user->email)->find();
                        $farmer->delete();
                        $user->delete();
                }
                HTTP::redirect('/');
        }
}
