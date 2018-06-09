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
    const perGerenciarRecebeEmail = 293;
    const perPesqCadGrupo = 351;
    const perDeletarGrupo = 352;
    
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
        
        $this->load->library('Crud', '', 'crud');                
        $this->load->model('email/email_model','emailModel');
        $this->load->model('email/email_grupo_model','emailGrupoModel');

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
        $dados['perPesqCadGrupo'] = self::perPesqCadGrupo;
        
        $this->layout->region('html_header', 'view_html_header');
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        if(in_array(self::perGerenciarRecebeEmail, $this->session->userdata('permissoes'))){
        
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
    
    public function grupos(){
        
        $mostra_por_pagina = 30;
        
        $this->crud->ini();
        $par = $this->crud->getParMetodo();
        list($post, $sort_by, $sort_order, $pagina) = $par;

        $resultado = $this->emailGrupoModel->pesquisa($post, $mostra_por_pagina, $sort_by, $sort_order, $pagina);
        
        $resultado['tabela'] = 'gerenciar grupos de e-mail';
        
        $postEncode = (!$post)? 0: $this->util->base64url_encode($post); 

        
        $crud = $this->crud->listarManual($resultado, $mostra_por_pagina, $postEncode, $sort_by, $sort_order, $pagina);
        
        $crud['pasta'] = 'email';
        $crud['controller'] = 'email';
        $crud['metodo'] = 'grupos';
        $crud['perExcluir'] = self::perDeletarGrupo;
        $crud['assunto'] = 'Gerenciar grupos de e-mail';

        $this->layout->region('html_header', 'view_html_header');
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        
   
        if(in_array(self::perPesqCadGrupo, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'grupo/view_psq', $crud);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');

        
    }
    
    public function salvarGrupo(){
        
        $this->crudModel->setTabela('email_grupo');
        $this->crudModel->setCampoId('id');
        #$this->crudModel->setCamposIgnorados(array('idGrupoEmail'));
        
        try{
        
            $status = $this->crudModel->insereMysql();
            $this->logDados['descricao'] = 'Email - grupo de e-mail - Cadastra grupo ('.$status.')';
            $this->logDados['acao'] = 'INSERT';
        
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        
        $this->logGeral->grava($this->logDados);
        
        $_POST['id'] = $status;
        
        if($status){
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Grupo de e-mail salva com sucesso!</strong></div>');
        }else{
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao salvar grupo de e-mail, caso o erro persiste comunique o administrador!</div>');
        }
        redirect(base_url('email/email/grupos/'.$this->input->post('id'))); 
        
    }
    
    public function deletaGrupo(){
        
        $this->crudModel->setTabela('email_grupo');
        $this->crudModel->setCampoId('id');
        
        try{
            $status = $this->crudModel->delete();
            
            $this->logDados['descricao'] = 'Email - grupo de e-mail - Apaga grupo de e-mail';
            $this->logDados['acao'] = 'DELETE'; 
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        $this->logGeral->grava($this->logDados);
        
        if($status){
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Grupo de e-mail apagado com sucesso!</strong></div>');
        }else{
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao apagar grupo de e-mail, caso o erro persiste comunique o administrador!</div>');
        }
        redirect(base_url('email/email/grupos'));

    }
                
}
