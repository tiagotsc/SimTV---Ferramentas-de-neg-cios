<?php
#error_reporting(0);
include_once(APPPATH.'modules/base/controllers/base.php');
#include_once(APPPATH.'controllers/base.php');
if(!defined('BASEPATH')) exit('No direct script access allowed');
#date_default_timezone_set('America/Sao_Paulo');
#setlocale(LC_ALL, 'pt_BR.UTF-8', 'Portuguese_Brazil.1252');
/**
* Classe responsável pela e-mail
*/
class Email extends Base
{
    
    private $dominioEmail = "@SIMTV.COM.BR";
    private $logDados;
    
	/**
	 * Usuario::__construct()
	 * 
	 * @return
	 */
	public function __construct(){
       
		parent::__construct();
        
        $this->logDados['usuario'] = $this->session->userdata('cd');
        $this->logDados['aplicacao'] = SISTEMA;
        $this->logDados['modulo'] = 'Email';
        $this->logDados['funcao'] = $_SERVER['REDIRECT_QUERY_STRING'];
        
        $this->load->model('email/email_model','emailModel');

    }
    
    function index()
    {
        
      	$this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
      	
		// Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
    }
    
    /**
     * Usuario::usuarios()
     * 
     * @return
     */
    public function recebeEmail(){
        
        $dados['departamento'] = $this->dadosBanco->departamento();
        $dados['unidade'] = $this->dadosBanco->unidade();
        $dados['funcao'] = $this->dadosBanco->cargos();
        $dados['dominioEmail'] = $this->dominioEmail;
        $dados['tiposEmail'] = $this->emailModel->tiposEmailsPermitidos();
        
        $this->layout->region('html_header', 'view_html_header');
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        if(in_array(293, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'email/view_recebe_email', $dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
      	$this->layout->show('layout');
        
    }
    
    public function salvarRecebeEmail(){
        
        $this->emailModel->setDominioEmail($this->dominioEmail);
        $status = $this->emailModel->gravaQuemRecebe();
        
        if($status){
            $this->session->set_flashdata('tipo_email', $this->input->post('tipo_email'));
            $this->session->set_flashdata('permissor', $this->input->post('permissor'));
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Atualizado com sucesso!</strong></div>');
        
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao atualizar, caso o erro persiste comunique o administrador!</div>');
        
        }
        
        $this->logDados['descricao'] = utf8_encode('Usuários - Configuração de quem recebe email (').$this->input->post('tipo_email').')';
        $this->logDados['idAcao'] = $this->input->post('tipo_email');
        $this->logDados['acao'] = 'UPDATE';
        $this->logGeral->grava($this->logDados);

        redirect(base_url('email/recebeEmail'));
        
    }
                
}
