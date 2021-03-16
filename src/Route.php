<?php
/**
 * @author Iuri Cardoso Araújo 
 */
namespace system;
/**
 * Classe responsável capturar a url amigável, e chamar a respectiva controller e action relacionada 
 */
Class Route
{
	private static $url = '';
	private static $fullUrl;
	private static $controller;
	private static $action;
	private static $params = array();       //valor onde será armazenado os parametros
	private static $hasParams;
	private static $numberofParams;
	
	//url da rota
	private static $urlBase;
	private static $numberofUrlBase;

	private static $emptyUrl;


	public static function getPage($routes = array())   //routes é o array contendo as rotas definidas no init
	{
		$fetch = self::getFetch($routes);
		return $fetch;
	}

	private static function getFetch($routes = array())
	{
		foreach ($routes as $key => $route) {   //key é o endereço da url amigável    //endereco do controle associado
			self::findrouteParams($key);   //aqui será passado pelo array de rotas, analisará se  o mesmo possui parâmetros
			self::findUrl();     //encontrará a url digitada de fato


			if (self::$url == self::$urlBase) {     //urlBase é a url sem parâmetros
				$resultado = self::findController($route);
				return $resultado;
			}
		}
		$resultado = ''; //Page Not Found
		return $resultado;
	}

	private static function findController($route)
	{
		self::getParamsIf(self::$fullUrl);
		$hasAction = self::getActionif($route);
		
		if(class_exists(self::$controller)) $controller = new self::$controller();
		else return false;

		if ($hasAction === true) {
			$action = self::$action;
			$params = self::$params;

			if (self::$hasParams === true) {
				$output = $controller->$action($params);
			} else $output = $controller->$action();
			return $output;
		}

		if(method_exists($controller,'index')) return $controller->index();   //método padrão
		else return false;
	}


	private static function getActionif($route)
	{
		$numberofActions = substr_count($route, '@');
		if ($numberofActions != 1) {
			self::$controller = $route;
			return false;
		}   //just one action at time is permited, with 0, call construct only
		$aux = explode('@', $route);
		self::$controller = $aux[0];
		self::$action = $aux[1];
		return true;

	}

	private static function findUrl()
	{
		$urlAux = self::getif('pag');

		if(!$urlAux){ $urlAux = ''; self::$emptyUrl = true;}
		$urlAux = self::lastCaractere($urlAux);
		$urls = explode('/', $urlAux);
		if (self::$hasParams == true) self::getUrl($urls);
		else self::$url = $urlAux;
	}

	private static function lastCaractere($url)
	{
		if (substr($url, -1, 1) == '/') $url = substr($url, 0, -1);
		return $url;
	}

	private static function getUrl($urls = array())
	{
		$aux = array();
		$aux2 = '';
		for ($i = 0; $i < self::$numberofUrlBase; $i++) {
			array_push($aux, $urls[$i]);
			$aux2 = implode('/', $aux);
		}
		self::$url = $aux2;     //base sem parametros
		self::$fullUrl = $urls;
	}

	private static function getParamsIf($urls)
	{
		$aux = array();
		if(is_array($urls)) $countUrl = count($urls); else $countUrl = 0;
		for ($i = self::$numberofUrlBase; $i < $countUrl; $i++) array_push($aux, $urls[$i]);
		self::$params = $aux;
		if(count(self::$params) > self::$numberofParams) die('Page Not Found');
	}

	private static function getIf($name)
	{
		if (isset($_REQUEST[$name])) return $_REQUEST[$name];
		return false;
	}

	private static function findrouteParams($key)
	{
		self::$hasParams = false;
		$auxs = explode('/', $key);
		$arrayAux = array();
		$paramsAux = array();

		foreach ($auxs as $aux) {
			$hascaract = substr_count($aux, '{$');
			$ultimocaract = substr($aux, -1);

			if ($hascaract > 0) {
				if ($ultimocaract == '}') {
					$tamanho = strlen($aux) - 3;
					$var = substr($aux, 2, $tamanho);    //coloca o conteudo da variavel em var . Nao é necessario, só o tamanho dele já bastaria para este propósito
					array_push($paramsAux, $var);     //empurra o var para dentro do vetor de parametros
					self::$hasParams = true;
				}
			} else array_push($arrayAux, $aux);   //se nao for parametro empurra para o array auxiliar
		}

		self::$numberofParams = sizeof($paramsAux);
		if($key != '')self::$urlBase = implode('/', $arrayAux);
		else self::$urlBase = '';
//		if(!self::$emptyUrl) self::$urlBase = implode('/', $arrayAux);      //obtem url base
//		else self::$urlBase = '';

		self::$numberofUrlBase = sizeof($arrayAux);

	}

}