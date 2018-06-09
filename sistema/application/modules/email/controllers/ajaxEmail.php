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
class AjaxEmail extends Base
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
        #$this->load->model('administrador/email_model','emailModel');

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
    
    public function dadosRecebeEmail(){
        /*$_POST['tipo'] = 'todos';
        $_POST['tipo_email'] = 1;
        $_POST['permissor'] = 'todos';*/
        #$_POST['departamento'] = 35;
        $this->load->model('email/email_model','emailModel');
        $this->load->model('email/email_grupo_model','emailGrupoModel');
        
        if($this->input->post('tipo') == 'todos' or $this->input->post('tipo') == 'usuario'){
            $resDados['dados']['usuario'] = $this->emailModel->usuariosEmail(
                $this->input->post('tipo_email'),
                $this->input->post('permissor'),
                $this->input->post('nome_email'),
                $this->input->post('departamento'), 
                $this->input->post('unidade'),
                $this->input->post('funcao')
                );
        }else{
            $resDados['dados']['usuario'] = false;
        }
            
        if($this->input->post('tipo') == 'todos' or $this->input->post('tipo') == 'grupo'){
            $resDados['dados']['grupo'] = $this->emailGrupoModel->gruposEmail(
                $this->input->post('tipo_email'),
                $this->input->post('permissor'),
                $this->input->post('nome_email')
            );
        }else{
            $resDados['dados']['grupo'] = false;
        }
        
        $this->load->view('view_json',$resDados);
    }
                
}
