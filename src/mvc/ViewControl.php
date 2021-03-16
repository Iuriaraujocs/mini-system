<?php
/**
 * @author Iuri Cardoso Araújo 
 */
namespace system\mvc;

use Smarty;
/** Configuração do template engine Smarty */
class ViewControl extends Smarty
{
	public $pathTemplate;

	public function __construct()
	{
		parent::__construct();
	}

	public function smartySettings()
	{
        $this->setTemplateDir(__DIR__ . DS . 'views');
        $this->setCompileDir(__DIR__ . DS . 'views' . DS . 'tmp' . DS . 'compile' . DS);
        $this->setCacheDir(__DIR__ . DS . 'views' . DS . 'tmp' . DS . 'cache' . DS);
 
		// $this->debugging = true;
    }

}



