<?php

namespace Twitter;

if (file_exists(__DIR__.'/../vendor/autoload.php'))
{
	require_once __DIR__.'/../vendor/autoload.php';
}
else
{
	require_once VENDORPATH.'autoload.php';
}

/**
 * FuelPHP Twitter package
 *
 * @author    Mamoru Otsuka http://madroom-project.blogspot.jp/
 * @copyright 2013 Mamoru Otsuka
 * @license   MIT License http://www.opensource.org/licenses/mit-license.php
 */
class Twitter
{
	/**
	 * @var Twitter
	 */
	protected static $_instance;

	/**
	 * @var array
	 */
	protected static $_instances = array();

	/*
	 * Initialize
	 */
	public static function _init()
	{
		\Config::load('twitter', true);
	}

	/**
	 * Forge
	 * 
	 * @param  string $name
	 * @param  array $config
	 * @return Twitter
	 */
	public static function forge($name = 'default', array $config = array())
	{
		
		if ($exists = static::instance($name))
		{
			\Error::notice('Twitter with this name exists already, cannot be overwritten.');
			return $exists;
		}

		static::$_instances[$name] = new static($config);

		if ($name == 'default')
		{
			static::$_instance = static::$_instances[$name];
		}

		return static::$_instances[$name];
	}

	/**
	 * Get instance
	 * 
	 * @param  string $instance
	 * @return mixed
	 */
	public static function instance($instance = null)
	{
		if ($instance !== null)
		{
			if ( ! array_key_exists($instance, static::$_instances))
			{
				return false;
			}

			return static::$_instances[$instance];
		}

		if (static::$_instance === null)
		{
			static::$_instance = static::forge();
		}

		return static::$_instance;
	}

	/**
	 * @var tmhOAuth
	 */
	protected $tmhoauth;

	/**
	 * Constructor
	 * 
	 * @param  array $config
	 */
	public function __construct($config)
	{
		$config = array_merge(\Config::get('twitter'), $config);

		$this->tmhoauth = new \tmhOAuth(array(
			'consumer_key' => $config['consumer_key'],
			'consumer_secret' => $config['consumer_secret'],
		));

		if ( ! empty($config['user_token']) and ! empty($config['user_secret']))
		{
			$this->set_token($config['user_token'], $config['user_secret']);
		}
	}

	/**
	 * Get request token
	 * 
	 * @return array
	 */
	public function get_request_token()
	{
		return $this->post('oauth/request_token', array(), '');
	}

	/**
	 * Get authenticate url
	 * 
	 * @param  string $oauth_token
	 * @return string
	 */
	public function get_authenticate_url($oauth_token)
	{
		return $this->tmhoauth->url('oauth/authenticate', '')."?oauth_token={$oauth_token}";
	}

	/**
	 * Get authorize url
	 * 
	 * @param  string $oauth_token
	 * @return string
	 */
	public function get_authorize_url($oauth_token)
	{
		return $this->tmhoauth->url('oauth/authorize', '')."?oauth_token={$oauth_token}";
	}

	/**
	 * Get access token
	 * 
	 * @param  string $oauth_token
	 * @param  string $oauth_token_secret
	 * @param  string $oauth_verifier
	 * @return array
	 */
	public function get_access_token($oauth_token, $oauth_token_secret, $oauth_verifier)
	{
		$this->set_token($oauth_token, $oauth_token_secret);

		$params = array(
			'oauth_verifier' => $oauth_verifier,
		);

		return $this->post('oauth/access_token', $params, '');
	}

	/**
	 * GET request
	 * 
	 * @param  string $uri
	 * @param  array $params
	 * @param  string $fmt
	 * @return array
	 */
	public function get($uri, array $params = array(), $fmt = 'json')
	{
		return $this->call('GET', $uri, $params, $fmt);
	}

	/**
	 * POST request
	 * 
	 * @param  string $uri
	 * @param  array $params
	 * @param  string $fmt
	 * @return array
	 */
	public function post($uri, array $params = array(), $fmt = 'json')
	{
		return $this->call('POST', $uri, $params, $fmt);
	}

	/**
	 * PUT request
	 * 
	 * @param  string $uri
	 * @param  array $params
	 * @param  string $fmt
	 * @return array
	 */
	public function put($uri, array $params = array(), $fmt = 'json')
	{
		return $this->call('PUT', $uri, $params, $fmt);
	}

	/**
	 * DELETE request
	 * 
	 * @param  string $uri
	 * @param  array $params
	 * @param  string $fmt
	 * @return array
	 */
	public function delete($uri, array $params = array(), $fmt = 'json')
	{
		return $this->call('DELETE', $uri, $params, $fmt);
	}

	/*******************************************************
	 * Protected Methods
	 ******************************************************/
	/**
	 * Set token
	 * 
	 * @param  string $user_token
	 * @param  string $user_secret
	 */
	protected function set_token($user_token, $user_secret)
	{
		$this->tmhoauth->config['user_token'] = $user_token;
		$this->tmhoauth->config['user_secret'] = $user_secret;
	}

	/**
	 * Call API
	 * 
	 * @param  string $method
	 * @param  string $uri
	 * @param  array $params
	 * @param  string $fmt
	 * @return array
	 * @throws \FuelException
	 */
	protected function call($method, $uri, $params, $fmt)
	{
		$code = $this->tmhoauth->request($method, $this->tmhoauth->url($uri, $fmt), $params);

		if ($code != 200)
		{
			throw new \FuelException('Code:'.$code.' Response:'.$this->tmhoauth->response['response']);
		}

		switch ($fmt)
		{
			case 'json':
				return json_decode($this->tmhoauth->response['response']);
			default:
				return $this->tmhoauth->extract_params($this->tmhoauth->response['response']);
		}
	}
}
