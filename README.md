# FuelPHP Package for Twitter [![Build Status](https://travis-ci.org/mp-php/fuel-packages-twitter.png)](https://travis-ci.org/mp-php/fuel-packages-twitter)

***

## Install
### Setup to fuel/packages/twitter
* Use composer https://packagist.org/packages/mp-php/fuel-packages-twitter
* git submodule
* Download zip

### Configuration

##### One
In app/config/config.php

	'always_load' => array('packages' => array(
		'twitter',
		...

or in your code

	Package::load('twitter');

##### Two
Copy packages/twitter/config/twitter.php to under app/config directory and edit

## Example

### Authorization

	<?php
	
	class Controller_Twitter extends Controller
	{
	
		public function action_signin()
		{
			Session::delete('twitter');
			
			$twitter = Twitter::forge('oauth');
			
			$request_token = $twitter->get_request_token();
			Session::set('twitter.request_token', $request_token);
			
			$url = $twitter->get_authorize_url($request_token['oauth_token']);
			Response::redirect($url);
		}
		
		public function action_callback()
		{
			$request_token = Session::get('twitter.request_token');
			Session::delete('twitter');
			
			$twitter = Twitter::forge('oauth');
			
			$access_token = $twitter->get_access_token(
				$request_token['oauth_token'],
				$request_token['oauth_token_secret'],
				Input::get('oauth_verifier'));
				
			// $access_token has user_id, screen_name, oauth_token and oauth_token_secret
			Debug::dump($access_token);
		}
		
	}

### Tweet

		$twitter = Twitter::forge('default', array(
			'user_token'  => 'xxxxxxxxxx',
			'user_secret' => 'yyyyyyyyyy',
		));
		
		$response = $twitter->post('1.1/statuses/update', array('status' => 'Hello!'));
		Debug::dump($response);

## License

Copyright 2013, Mamoru Otsuka. Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php
