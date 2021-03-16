<?php

/**
 * Funções helpers do system
 */
require_once(__DIR__ . '/utils/array.php');
require_once(__DIR__ . '/utils/coding.php');
require_once(__DIR__ . '/utils/string.php');

function session_initialize()
{
	session_name(md5('app_secure_session_name'.$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']));
	session_cache_expire(30);  //expira sessão em 30 minutos
	if (session_status() == PHP_SESSION_NONE) session_start();
}

function app_post($name)
{
	if (isset($_POST[$name])) return $_POST[$name];
	return false;
}

function app_get($name)
{
	if (isset($_GET[$name])) return $_GET[$name];
	return false;
}

function app_request($name)
{
	if (isset($_REQUEST[$name])) return $_REQUEST[$name];
	return false;
}

function sanitize_external_data()
{
	// $string = htmlspecialchars(strip_tags($_POST['example']));
	//ou
	// $string = htmlentities($_POST['example'], ENT_QUOTES, 'UTF-8');
}

function app_config($filename,$arg = null)   // arg1 = nome do arquivo , config
{
	/** Obter configuracoes no diretorio config */
	$file = APP_PATH . DS . 'config' . DS . $filename . '.php';
	if(file_exists($file)) $config = require($file);
	else die('Arquivo de configuração não encontrado');

	if(is_null($arg) && isset($config) ) return $config;
	else if(!is_null($arg) && isset($config[$arg]) ) return $config[$arg];
	return null;
}

function app_session($name)      //se existir retorna a sessao, se nao, retorna false
{
	if (session_status() == PHP_SESSION_NONE) session_start();
	if (isset($_SESSION[$name])) return $_SESSION[$name];
	return false;
}

function app_unsession($name)
{
	if (session_status() == PHP_SESSION_NONE) session_start();
	if (isset($_SESSION[$name])) unset($_SESSION[$name]);
}

/**
 * função para limpar o cache do browser
 *
 * @return boolean
 */
function clearBrowserCache()
{
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '. gmdate('D, d M Y H:i:s') .' GMT');
	header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
	header('Pragma: no-cache');
	header('Expires: 0');

	return true;
}

function is_server()
{
	$server = $_SERVER['SERVER_NAME'];
	if ($server != "localhost") return false;  // alterar
	else return true;
}

function is_local()
{
	$server = $_SERVER['SERVER_NAME'];  //alterar para 127.0.0.1
	if ($server == "localhost") return false;
	else return true;
}

if(!function_exists('app_log'))
{
    function app_log($message = '', $type = 'INFO'){
        $type = strtoupper($type);
        $format = "[%datetime%] %channel%.%level_name%: %message%\n";

        $log = new Monolog\Logger('app');
        $formatter = new Monolog\Formatter\LineFormatter($format);
        $streamHandle = new Monolog\Handler\StreamHandler(LOG_PATH . '/app.log');

        $streamHandle->setFormatter($formatter);
        $log->pushHandler($streamHandle);

        switch ($type) {
            case 'DEBUG':
                $log->addDebug($message);
                break;
            case 'WARNING':
                $log->addWarning($message);
                break;
            case 'ERROR':
                $log->addError($message);
                break;
            default:
                $log->addInfo($message);
                break;
        }
    }
}

if(!function_exists('app_get_env'))
{
    function app_get_env($key,$default = null){
        return $_ENV[$key] ?? $default;
    }
}

if(!function_exists('app_set_env'))
{
    function app_set_env(){
        $Loader = new josegonzalez\Dotenv\Loader( APP_PATH.'/.env');
        // Parse the .env file
        $Loader->parse();
        // Send the parsed .env file to the $_ENV variable
        $Loader->toEnv();
    }
}

if(!function_exists('app_path'))
{
	function app_path($filepath = ''){
		return empty($filepath) ? APP_PATH : APP_PATH . DIRECTORY_SEPARATOR . $filepath;
	}
}

if(!function_exists('app_https'))
{
	/**
	 * Testa se o https está ativo e caso esteja em http faz um redirect permanente
	 */
    function app_https(){

        if($_SERVER['SERVER_PORT'] == '80' && preg_match('/\.php$/', $_SERVER['REQUEST_URI'])) {

            header('HTTP/1.1 301 Moved Permanently');
            header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
            exit();
        }
    }
}

if(!function_exists('app_upload_img'))
{
    function app_upload_img($key){

        if (isset($_FILES[$key]) && file_exists($_FILES[$key]['tmp_name'])) { 
			
            $data = ['status' => false];
			$targetFile = basename($_FILES[$key]['name']);          //retorna o nome do arquivo com a extensao

			$fileType = pathinfo($targetFile, PATHINFO_EXTENSION);    //retorna a extensão do arquivo
            // Converte a extensão para minúsculo
            $fileType = strtolower ( $fileType );
 
            // Somente imagens, .jpg;.jpeg;.gif;.png
            if (!strstr( '.jpg;.jpeg;.gif;.png', $fileType ) ) return false;
            
            $isImage = getimagesize($_FILES[$key]['tmp_name']);

			if ($isImage !== false) {
				$data['image'] = md5(rand(1,10) . time()) . '.' . $fileType;        //nome randomico para a imagem a ser salva no servidor para não ocorrer nomes parecidos
				$data['pathFolder'] = APP_UPLOAD_PATH;
				$data['extension'] = $fileType;
				$data['filename'] = $data['pathFolder'] . DS . $data['image'];

				if (move_uploaded_file($_FILES[$key]['tmp_name'], $data['filename'])) return $data;
				else return false;
			}
		}

    }
}



