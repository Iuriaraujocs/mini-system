<?php
/**
 * @author Iuri Cardoso Araújo 
 */
namespace system\mvc;

use Smarty;

/** Classe base que implementa métodos clássicos de uma Controller */
class BaseController
{
	protected $extraJson = [];

	protected function request($key, $default = null)
	{
		if(array_key_exists($key, $_REQUEST) && (is_numeric($_REQUEST[$key]) || !empty($_REQUEST[$key]))) {
			return $_REQUEST[$key];
		}
		elseif(array_key_exists($key, $_REQUEST)){
            return $_REQUEST[$key];	
		}
		
		return $default;
	}

	protected function isPost()
	{	
		return (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST');
	}
	
	protected function isXmlHttpRequest()
	{    //isXmlHttpRequest   se é ajax	
		return (array_key_exists('HTTP_X_REQUESTED_WITH', $_SERVER) && strtoupper($_SERVER['HTTP_X_REQUESTED_WITH']) === 'XMLHTTPREQUEST' );		
	}

	protected function view($template, $params = array())
	{
		$smarty = new Smarty;
		
		$viewPath = app_config('app','viewPath');
		
		if($viewPath) $smarty->setTemplateDir($viewPath);
		else $smarty->setTemplateDir(APP_PATH . DS . 'app'. DS . 'Views');
		$smarty->setCompileDir(__DIR__ . DS . 'views' . DS . 'tmp' . DS . 'compile' . DS);
        $smarty->setCacheDir(__DIR__ . DS . 'views' . DS . 'tmp' . DS . 'cache' . DS);
 
		foreach ($params as $key => $value) {
			$smarty->assign($key, $value);
		}
		return $smarty->fetch($template . '.tpl');
	}

	protected function jsonResponse($data = [])
	{
        $response = [
            'success' => true,
            'code'    => 200,
            'data'    => $data,
            'datetime' => date('d-m-Y G:i:s')
            // 'method'  => $this->method,
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
	}

    protected function errorResponse($message = 'error in response')
	{
        $response = [
            'success' => false,
            'code'    => 500,
            'msg'     => $message,
            'datetime' => date('d-m-Y G:i:s')
            // 'method'  => $this->method,
        ];
        header('Content-Type: application/json');
        http_response_code(500); //força o code da resposta para 500
        echo json_encode($response);
	}

	protected function redirectPage($url)
	{
		header("Location: $url");
	}

    protected function sendImageResponse($file)
    {
        $type = 'image/png';
        header('Content-Type:'.$type);
        header('Content-Length: ' . filesize($file));
        readfile($file);
    }

	protected function addController($class, $action = '', $params = null)       //call_user_func_array()       funcao(...$array)
	{
		$return = null;
		if (!is_object($class)) {
			$object = new $class();
			$return = $this->doController($object, $action, $params);
		} else $return = $this->doController($class, $action, $params);
		return $return;
	}

	private function doController($object, $action, $params = null)
	{
		$return = $object;
		if ((method_exists($object, $action)) and ($action != '')) {
			if ($params != null) $return = $object->$action($params);
			else $return = $object->$action();
		}
		return $return;
	}

	// public function loadCss()
	// {
	// }

	// public function loadJs()
	// {
	// }
}