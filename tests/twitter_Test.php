<?php

require_once __DIR__.'/../vendor/autoload.php';

/**
 * Stub for tmhOAuth
 * 
 * @see tmhOAuth
 */
class Stub_tmhOuth extends tmhOAuth
{
	private $code;

	public function __construct(array $config, $code)
	{
		parent::__construct($config);

		$this->code = $code;
	}

	public function request($method, $url, $params=array(), $useauth=true, $multipart=false, $headers=array())
	{
		return $this->code;
	}
}

/**
 * Stub for Twitter
 * 
 * @see Twitter
 */
class Stub_Twitter extends Twitter
{
	public function __construct(array $config, $code, $response)
	{
		parent::__construct($config);

		$this->tmhoauth = new Stub_tmhOuth($config, $code);
		$this->tmhoauth->response['response'] = $response;
	}
}

/**
 * Test for My_Twitter class
 * 
 * @group Twitter
 */
class Test_Twitter extends TestCase
{

	/**
	 * Test for _init()
	 */
	public function test_init()
	{
		if (isset(\Config::$loaded_files['twitter']))
		{
			unset(\Config::$loaded_files['twitter']);
		}

		$this->assertFalse(isset(\Config::$loaded_files['twitter']));

		Twitter::_init();

		$this->assertTrue(isset(\Config::$loaded_files['twitter']));
 	}

	/**
	 * Test for get_authorize_url()
	 */
	public function test_get_authorize_url()
	{
		$t = Twitter::forge();

		$oauth_token = 'xxx';
		$actual = $t->get_authorize_url($oauth_token);
		$expected = 'https://api.twitter.com/oauth/authorize?oauth_token=xxx';
		$this->assertEquals($expected, $actual);
 	}

	/**
	 * Test for get_authenticate_url()
	 */
	public function test_get_authenticate_url()
	{
		$t = Twitter::forge();

		$oauth_token = 'xxx';
		$actual = $t->get_authenticate_url($oauth_token);
		$expected = 'https://api.twitter.com/oauth/authenticate?oauth_token=xxx';
		$this->assertEquals($expected, $actual);
	}

	/**
	 * Test for get_request_token()
	 */
	public function test_get_request_token()
	{
		$mock = $this->getMock('Twitter', array('call'), array(array()));
	
		$mock->expects($this->any())
			->method('call')
			->with('POST', 'oauth/request_token', array(), '')
			->will($this->returnValue(true));

		$this->assertTrue($mock->get_request_token());
	}

	/**
	 * Test for get_access_token()
	 */
	public function test_get_access_token()
	{
		$mock = $this->getMock('Twitter', array('call'), array(array()));

		$mock->expects($this->any())
			->method('set_token')
			->with('token', 'secret');

		$mock->expects($this->any())
			->method('call')
			->with('POST', 'oauth/access_token', array('oauth_verifier' => 'verifier'), '')
			->will($this->returnValue(true));

		$this->assertTrue($mock->get_access_token('token', 'secret', 'verifier'));
	}

	/**
	 * Test for get()
	 */
	public function test_get()
	{
		$t = new Stub_Twitter(array(), 200, 'dummy_key=dummy_value');
		$actual = $t->get('url', array(), '');
		$expected = array('dummy_key' => 'dummy_value');
		$this->assertEquals($expected, $actual);

		$t = new Stub_Twitter(array(), 200, '{dummy_key:"dummy_value"}');
		$actual = $t->get('url', array(), 'json');
		$expected = json_decode('{dummy_key:"dummy_value"}');
		$this->assertEquals($expected, $actual);
	}
 
	/**
	 * Test for post()
	 */
	public function test_post()
	{
		$t = new Stub_Twitter(array(), 200, 'dummy_key=dummy_value');
		$actual = $t->post('url', array(), '');
		$expected = array('dummy_key' => 'dummy_value');
		$this->assertEquals($expected, $actual);

		$t = new Stub_Twitter(array(), 200, '{dummy_key:"dummy_value"}');
		$actual = $t->post('url', array(), 'json');
		$expected = json_decode('{dummy_key:"dummy_value"}');
		$this->assertEquals($expected, $actual);
	}
 
	/**
	 * Test for put()
	 */
	public function test_put()
	{
		$t = new Stub_Twitter(array(), 200, 'dummy_key=dummy_value');
		$actual = $t->put('url', array(), '');
		$expected = array('dummy_key' => 'dummy_value');
		$this->assertEquals($expected, $actual);

		$t = new Stub_Twitter(array(), 200, '{dummy_key:"dummy_value"}');
		$actual = $t->put('url', array(), 'json');
		$expected = json_decode('{dummy_key:"dummy_value"}');
		$this->assertEquals($expected, $actual);
	}
 
	/**
	 * Test for delete()
	 */
	public function test_delete()
	{
		$t = new Stub_Twitter(array(), 200, 'dummy_key=dummy_value');
		$actual = $t->delete('url', array(), '');
		$expected = array('dummy_key' => 'dummy_value');
		$this->assertEquals($expected, $actual);

		$t = new Stub_Twitter(array(), 200, '{dummy_key:"dummy_value"}');
		$actual = $t->delete('url', array(), 'json');
		$expected = json_decode('{dummy_key:"dummy_value"}');
		$this->assertEquals($expected, $actual);
	}
 
	/**
	 * Test for call()
	 * 
	 * @expectedException FuelException
	 */
    public function test_call_exception()
    {
		$t = new Stub_Twitter(array(), 201, 'dummy_key=dummy_value');
		$t->get('url');
    }

}
