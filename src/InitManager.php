<?php
/**
 * @author Iuri Cardoso Araújo 
 */
namespace system;

use system\Route;
use system\mvc\ViewControl;
/** 
 * Classe de Inicialização, obtém as urlPatterns, aplica na Route e envia a resposta 
 * */
class InitManager{

	public $urlpatterns;
	private $pageContent;

	private $routeDisplay = 'index';
	private $templateName = 'index.tpl';

	protected function run()
	{
		// $this->tmpManager();
        
        /** Seta os dados definidos em .env para o $_ENV */
        app_set_env();  
        
        /** Obtém a informação resultante da Controller escolhida por Route */
		$this->pageContent = Route::getPage($this->urlpatterns);

        /** se a resposta é um array ou um objeto, envia no formato json */
		if(is_object($this->pageContent) || is_array($this->pageContent) ) echo json_encode($this->pageContent);
		else $this->viewControl();
	}

	private function viewControl(){
		$smartManager = new ViewControl();
		$smartManager->smartySettings();
		$smartManager->assign($this->routeDisplay, $this->pageContent);
		try {
			$smartManager->display($this->templateName);
		} catch (\SmartyException $e) {
		} catch (\Exception $e) {
		}
	}

}