<?php
require_once(R_P . 'require/nusoap.php');
define ("DATAHOST","adm.cnzz.com");
define ("DATAPORT","80");
define ("DATAHAND","adm_oem/app_server.php");
class Cnzz_Adm_Oem
{
	var $url;
	var $client;
	var $err;
	function init($host=DATAHOST,$port=DATAPORT,$handle=DATAHAND)
	{
		$this->url = "http://".$host.":".$port."/".$handle;
		$this->client = new newsoapclient($this->url);
		$this->err = $this->client->getError();
		if ($this->err) {
			return $this->err;
		}
	}

	function Cnzz_Adm_Oem()
	{
		$numargs = func_num_args();
		$arg_list = func_get_args();
		switch($numargs)
		{
			case 1:
				$this->init($arg_list[0]);
				break;
			case 2:
				$this->init($arg_list[0],$arg_list[1]);
				break;
			case 3:
				$this->init($arg_list[0],$arg_list[1],$arg_list[2]);
				break;
			default:
				$this->init();
				break;
		}
	}
    function get_appkey_once($request)//登陆
	{
		$_request = base64_encode(serialize($request));
		$result = $this->client->call('get_appkey_once',$_request);
		$response = unserialize(base64_decode($result));
		//echo $this->client->request;
		//echo $this->client->response;
		return $response;
    }
	 function reg_user_once($request)//注册
	{
		$_request = base64_encode(serialize($request));
		$result = $this->client->call('reg_user_once',$_request);
		$response = unserialize(base64_decode($result));
		//echo $this->client->request;
		//echo $this->client->response;
		return $response;
    }
	
}
?>