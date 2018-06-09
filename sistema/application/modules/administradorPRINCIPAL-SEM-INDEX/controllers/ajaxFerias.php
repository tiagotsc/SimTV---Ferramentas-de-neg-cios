<?php
#error_reporting(0);
include_once(APPPATH.'modules/base/controllers/base.php');
#include_once(APPPATH.'controllers/base.php');
if(!defined('BASEPATH')) exit('No direct script access allowed');
#date_default_timezone_set('America/Sao_Paulo');
#setlocale(LC_ALL, 'pt_BR.UTF-8', 'Portuguese_Brazil.1252');
/**
* Classe responsável pela usuário
*/
class AjaxFerias extends Base
{
    
	/**
	 * Usuario::__construct()
	 * 
	 * @return
	 */
	public function __construct(){
       
		parent::__construct();
        
        #$this->load->model('administrador/permissaoPerfil_model','permissaoPerfil');
        #$this->load->model('administrador/usuario_model','usuario');  
        $this->load->model('administrador/ferias_model','ferias');

    }
    
    public function index()
    {
        /*
      	$this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
      	
		// Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        */
    }
    
    public function dados(){
        #$_POST['cd_usuario'] = 2912;
        if($this->input->post('cd_usuario')){
            $resDados['dados'] = $this->ferias->dados($this->input->post('cd_usuario'));
        }else{
            $resDados['dados'] = false;
        }
        $this->load->view('view_json',$resDados);
    }
    
    
                
}
