<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class contratoExterno extends MX_Controller {
    
    private $logDados;
    private $usuarioOk = false;
    const emailEnviaRecebeRespostaAnalise = 39;
    
        /**
     * relatorio::__construct()
     * 
     * Classe resposável por processar os relatórios
     * 
     * @return
     */
    public function __construct(){
        
		parent::__construct();
        
        # Configurações do sistema
        include_once('configSistema.php'); 
        
        $this->load->helper('url');
        $this->load->library('pagination');
		$this->load->helper('form');
        $this->load->helper('text');
        $this->load->library('table');
        $this->load->library('Util', '', 'util'); 
        
        $this->load->model('base/log_model','logGeral');   
        $this->load->model('base/crud_model','crudModel');
        
        $this->load->model('base/autenticacaoExterna_model','autenticacaoExterna');     
        $this->load->model('tcom-contrato/contrato_model','contrato');
        $this->load->model('tcom-contrato/analiseFinanceira_model','analiseFin');
                      
        /*$this->logDados['usuario'] = $this->session->userdata('cd');
        $this->logDados['aplicacao'] = SISTEMA;
        $this->logDados['modulo'] = ucfirst(self::modulo).' - '.self::assunto;
        $this->logDados['funcao'] = $_SERVER['REDIRECT_QUERY_STRING'];*/
        
        /*$this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('text');
        $this->load->library('Util', '', 'util'); 
        $this->load->library('Crud', '', 'crud');
        $this->load->model('email/email_model','emailModel');
        $this->load->model('base/crud_model','crudModel');
        $this->load->model('base/log_model','logGeral');
        $this->load->model('base/dadosBanco_model','dadosBanco');
        $this->load->model('administrador/permissaoPerfil_model','permissaoPerfil');
        $this->load->model('viabilidade_resp_hist_model',self::modelAssunto);
        
        if(isset($this->uri->segments[5]) and strstr($this->uri->segments[5], 'token_')){
            $token = $this->uri->segments[5]; 
        }elseif(isset($this->uri->segments[6]) and strstr($this->uri->segments[6], 'token_')){
            $token = $this->uri->segments[6]; 
        }else{
            $token = false;
        }
        
        if($token){
            #$token = 'token_'.$this->util->base64url_encode('tiago.costa'); 
            #echo $token; exit();
            $this->autenticaToken($token);
        }
        
        if(!$this->session->userdata('session_id') || !$this->session->userdata('logado')){
			redirect(base_url('home'));
		}*/
        
                        
        #echo 'aqui'; exit();
	} 
    
    private function checaUsuario($identificacao){
        
        $resultado = $this->autenticacaoExterna->dadosUsuario($identificacao);
        
        if(!$resultado){
            echo '<strong>Acesso não autorizados!</strong>';
            exit();
        }
        
        if($resultado){
            $this->usuarioOk = true;
            
            $dados = array(
                                'cd' => $resultado->cd_usuario,
                                'login' => $resultado->login_usuario,
                                'matricula' => $resultado->matricula_usuario,
                                'nome' => $resultado->nome_usuario, 
                                'email' => $resultado->email_usuario, 
                                #'bem_vindo' => '<div id="usuario"><strong>Ol&aacute;!</strong> '.$resultado->nome_usuario.'</div>',
                                'departamento' => $resultado->cd_departamento,
                                'bem_vindo' => '<strong>Ol&aacute;!</strong> '.$resultado->nome_usuario.'&nbsp<a href="'.base_url('home/logout').'">Sair</a>',
                                #'perfil' => $resultado->cd_perfil,
                                #'indexHabilita' => $resultado->index_php_usuario,
                                #'indexPHP' => ($resultado->index_php_usuario == 'SIM')? 'index.php/': '',
                                #'permissoes' => $permissoesDoPerfil,
                                'logado' => true/*,
                                'horaLogin' => $resultado->data_hora_atual,
                                'chatStatus' => $resultado->status_chat_usuario,
                                'chatDataHora' => $resultado->data_chat_usuario,
                                'chatRequestStatus' => $resultado->data_hora_atual, # Último horário da verificação de status de usuário
                                'chatRequestNewConversa' => $resultado->data_hora_atual, # Último horário da verificação de novas conversas
                                'chatRequestNewMsg' => $resultado->data_hora_atual, # Último horário da verificação de nova mensagem enviada por quem você conversa
                                'chatOpen' => 'nao',
                                'chatContOpen' => 0*/
                                );
            $this->session->set_userdata($dados);  
            
        }
        return $resultado;
    }
    
    public function aprovacaoAnaliseFinanceira($identificacao, $idContrato){
        
        if(!$this->usuarioOk){
            $this->checaUsuario($identificacao);
        }
        
        $contrato = $this->contrato->dadosContratoValores($idContrato);

        if(!$contrato){
            echo '<strong>Contrato não localizado!</strong>';
            exit();
        }
        
        $dados['responsaveis'] = $this->analiseFin->responsaveisAprovacao($idContrato);
        
        #echo '<pre>'; print_r($dados['responsaveis']); exit();
        #echo '<pre>'; print_r($contrato); exit();
        $dados['contrato'] = $contrato;
        #echo '<pre>'; print_r($usuario); exit();
        #echo '<pre>'; print_r($this->uri->segments); exit();        
        $menu['menu'] = false;
        $menu['menu_satva'] = false;
        $this->layout->region('html_header', 'view_html_header');
        $this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('corpo', 'externo/view_frm', $dados);
        #$this->layout->region('corpo', 'view_permissao');
        $this->layout->region('menu_lateral', 'view_menu_lateral', false);
        
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
            
    }   
    
    public function analiseReposta($idContrato, $resposta){
        
        if(!$this->session->userdata('session_id') || !$this->session->userdata('logado')){
			echo '<strong>Acesso não autorizados!</strong>';
            exit();
		}
        
        $status = $this->analiseFin->salvarResposta($idContrato, $resposta);
        
        if($status){
            
            $this->enviaEmailRespostaAnalise($idContrato);
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Respondido com sucesso!</strong></div>');
        }else{
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao salvar responder, caso o erro persiste comunique o administrador!</div>');
        }
        redirect(base_url('tcom-contrato/contratoExterno/aprovacaoAnaliseFinanceira/'.md5($this->session->userdata('email')).'/'.$idContrato ));
        
    } 
    
    public function enviaEmailRespostaAnalise($idContrato){
        
        $usuario = $this->contrato->usuarioEnviaEmail(self::emailEnviaRecebeRespostaAnalise,false,'objeto');
        
        $dados['contrato'] = $this->contrato->dadosContratoValores($idContrato);
        $dados['responsaveis'] = $this->analiseFin->responsaveisAprovacao($idContrato);
        
        $nomeDe = 'Sim TV - Ferramenta de negocios | Telecom - contrato';
        $emailDe = 'naoresponda@simtv.com.br';
        $titulo = utf8_decode('Telecom - Resposta Analise Financeira - Contrato: '.$dados['contrato']->numero);
        
        $msg = $this->load->view('contrato/view_resp_analise_fin', $dados, true);

        foreach($usuario as $usu){
            $this->util->enviaEmail($nomeDe, $emailDe, $usu->email_usuario, $titulo, $msg, false, false);
        }
        
    }   
    
}