<?php
error_reporting(0);
if(!defined('BASEPATH')) exit('No direct script access allowed');
date_default_timezone_set('America/Sao_Paulo');
#setlocale(LC_ALL, 'pt_BR.UTF-8', 'Portuguese_Brazil.1252');
/**
* Classe criada para controlar todas as buscas sicronas (Sem refresh)
*/
class ajaxViabilidadePend extends MX_Controller
{
    
    private $logDados;
    const tabela = 'tcom_viab_pend';
    const modulo = 'tcom-viabilidade-pend';
    const assunto = 'Pendência vistoria AJAX';
    const perPendCadPerg = 342;
    const perPendCadResp = 336;
    const emailEnviaAbertura = 7; 
    const emailEnviaResponde = 8;
    
	/**
	 * ajaxviabilidadePend::__construct()
	 * 
	 * @return
	 */
	public function __construct(){
		parent::__construct();
        
        # Configurações do sistema
        include_once('configSistema.php');
        
        $this->logDados['usuario'] = $this->session->userdata('cd');
        $this->logDados['aplicacao'] = SISTEMA;
        $this->logDados['modulo'] = ucfirst(self::modulo).' - '.utf8_encode(self::assunto);
        $this->logDados['funcao'] = $_SERVER['REDIRECT_QUERY_STRING'];
        
        $this->load->model('tcom-viabilidade-pend/viabilidade_pend_model','viabilidadePend');
        $this->load->model('tcom-viabilidade/viabilidade_model','viabilidade');
        $this->load->model('email/email_model','emailModel');
        $this->load->model('base/crud_model','crudModel');
        $this->load->model('base/log_model','logGeral');
        $this->load->library('Util', '', 'util');     
        
        if(!$this->session->userdata('session_id') || !$this->session->userdata('logado')){
			redirect(base_url('home'));
		}
    }
    
    public function salvarPergunta(){
        
        #$_POST['idviab'] = 27;
        $_POST['cd_usuario_pergunta'] = $this->session->userdata('cd');
        #$_POST['pergunta'] = 'são servidores de e-mail, usados para o envio e recebimentos de mensagens';
         
        if(!$this->input->post('idviab') or $this->input->post('pergunta') == ''){
            return $this->load->view('view_json',array('dados'=>'ERRO'));
        } 
            
        $this->crudModel->setTabela(BANCO_TELECOM.'.'.self::tabela);
        $this->crudModel->setCampoId('id');
        $this->crudModel->setTextArea(array('pergunta'));
        try{
        
            $status = $this->crudModel->insereMysql();
            
            $this->logDados['descricao'] = ucfirst(self::modulo).' - '.utf8_encode(self::assunto).' - Cadastra Pergunta '.utf8_encode(self::assunto).' ('.$status.')';
            $this->logDados['acao'] = 'INSERT';
        
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        
        $this->logGeral->grava($this->logDados);
        
        if($status == true){
            $this->enviaEmail($status, 'Aberta');
            $dados['dados'] = 'OK';
        }else{
            $dados['dados'] = 'ERRO';
        }
        
        $this->load->view('view_json',$dados);
        
    }
    
    public function salvarResposta(){
        
        #$_POST['id'] = 9;
        #$_POST['idviab'] = 27;
        $_POST['cd_usuario_resposta'] = $this->session->userdata('cd');
        #$_POST['resposta'] = 'são servidores de e-mail, usados para o envio e recebimentos de mensagens';
        $_POST['status'] = 'Respondido';
        $_POST['data_cadastro_resposta'] = date('Y-m-d h:i:s');
        
        if(!$this->input->post('idviab') or $this->input->post('resposta') == ''){
            return $this->load->view('view_json',array('dados'=>'ERRO'));
        } 
        
        $this->crudModel->setTabela(BANCO_TELECOM.'.'.self::tabela);
        $this->crudModel->setCampoId('id');
        $this->crudModel->setTextArea(array('resposta'));
        try{
        
            $status = $this->crudModel->atualiza();
            
            $this->logDados['descricao'] = ucfirst(self::modulo).' - '.utf8_encode(self::assunto).' - Cadastra Resposta '.utf8_encode(self::assunto).' ('.$status.')';
            $this->logDados['acao'] = 'UPDATE';
        
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        
        $this->logGeral->grava($this->logDados);
        
        if($status == true){
            $this->enviaEmail($this->input->post('id'), 'Concluída');
            $dados['dados'] = 'OK';
        }else{
            $dados['dados'] = 'ERRO';
        }
        
        $this->load->view('view_json',$dados);
        
    }
    
    public function enviaEmail($idPend = false, $tipo = 'Aberta'){
        
        #$_POST['id'] = 9;
        #$_POST['idviab'] = 27;
        
        $dados['viab'] = $this->viabilidade->dadosViabilidade($this->input->post('idviab'));
        $dados['tipo'] = $tipo;
        $dados['dadosPendencia'] = $this->viabilidadePend->dadosPendencia($idPend);
        
        $msg = $this->load->view('view_imprimir', $dados, true);

        $nomeDe = 'Sim TV - Ferramenta de negocios | Telecom - '.self::assunto;
        $emailDe = 'naoresponda@simtv.com.br';
        $titulo = utf8_decode('Controle de Telecom - Pendencia de viabilidade - '.utf8_encode($tipo));
        $para = 'equipe.sistemas@simtv.com.br';
        
        if($tipo == 'Aberta'){
            $emailEnvia = self::emailEnviaAbertura;
        }else{
            $emailEnvia = self::emailEnviaResponde;
        }
        
        $usuario = $this->emailModel->usuarioEnviaEmail($emailEnvia,$dados['viab']['viabilidade']->cd_unidade);
        
        #$this->util->enviaEmail($nomeDe, $emailDe, 'tiago.costa@simtv.com.br', $titulo, $msg, false);
        foreach($usuario as $usu){
            
            $this->util->enviaEmail($nomeDe, $emailDe, $usu->email_usuario, $titulo, $msg, false);
        
        }
        
        
    }
    
    /**
	 * ajaxviabilidadePend::dadosViabilidade()
	 * 
     * Pega dados da viabilidade
     * 
	 */
    public function pendenciasViabilidade(){
        
        #$_POST['idviab'] = 27;
        
        $resDados['dados'] = $this->viabilidadePend->pendenciasViabilidade($this->input->post('idviab'));
        #echo '<pre>'; print_r($resDados['dados']); exit();
        $this->load->view('view_json',$resDados);
        
    }
                
}
