<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class viabilidadeRespHist extends MX_Controller {
     
     private $logDados;
     const modulo = 'tcom-viabilidade-resp-hist';
     const controller = 'viabilidadeRespHist';
     const pastaView = 'viabilidade_resp_hist';
     const tabela = 'tcom_viab_resp_hist';
     const assunto =  'Intera&ccedil;&atilde;o de Viabilidade';
     const modelAssunto = 'viabilidadeRespHist';
     const perModulo = 274;
     const perVisualizar = 347;
     const perCadEdit = 338;
     const perForcaAlt = 349;
     const perDeletar = 350;
     const dirUpload = './files/telecom/viabilidade/historico';
     const linkDownload = 'files/telecom/viabilidade/historico';
     
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
                      
        $this->logDados['usuario'] = $this->session->userdata('cd');
        $this->logDados['aplicacao'] = SISTEMA;
        $this->logDados['modulo'] = ucfirst(self::modulo).' - '.self::assunto;
        $this->logDados['funcao'] = $_SERVER['REDIRECT_QUERY_STRING'];
        
        $this->load->helper('url');
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
		}
        
	} 
     
	/**
     * Telefonia::index()
     * 
     * Tela inicial da telefonia
     * 
     * @return
     */
    private function index()
    { 
        
    }
    
    public function listarHistorico($id = false){ 

        $modelAssunto = self::modelAssunto;

        // Apelido do campo banco => Nome do campo mostrado na tabela HTML
        $dados['campos'] = array(
                                #'cd' => 'Cd', 
                                'data_cadastro' => 'Data da interação', 
                                'idStatusHist' => 'Status',
                                'anexo' => 'Anexo',
                                'nome_usuario' => 'Usuário'
                                );
        
        if($id){                        
            $dados['id'] = $id; 
            #$dados['statusFinal'] = $this->$modelAssunto->eStatusFinal($id);
            $dados['statusFinal'] = false;
            $dados['dadosViab'] = $this->$modelAssunto->dadosViabilidade($id);
            $dados['dados'] = $this->$modelAssunto->historicoResposta($id);
        }else{
            $dados['id'] = ''; 
            $dados['statusFinal'] = false;
            $dados['dadosViab'] = false;
            $dados['dados'] = false;
        }
        $dados['perCadEdit'] = self::perCadEdit;
        $dados['perForcaAlt'] = self::perForcaAlt;
        $dados['perDeletar'] = self::perDeletar;
        $dados['modulo'] = self::modulo;
        $dados['controller'] = self::controller;
        $dados['dirDownload'] = self::linkDownload;
        
        if(in_array(self::perVisualizar, $this->session->userdata('permissoes'))){
        
            $this->load->view('viabilidade_resp_hist/view_historico',$dados);
        
        }else{
            
            $this->load->view('view_permissao');
            
        }
        
    }
    
    public function frm($idResp, $idHist = false){

        $modelAssunto = self::modelAssunto;
        $this->crudModel->setTabela(BANCO_TELECOM.'.'.self::tabela);
        $this->crudModel->setCampoId('id');
        
        if($idHist){
            
            $dados = $this->crudModel->dadosId($idHist);
            
            $campos = array_keys($dados);
            
            foreach($campos as $campo){
             
				$dados[$campo] = $dados[$campo]; 
			}
            
            #$idViab = $dados['idViab'];
            
        }else{
            
            $campos = $this->crudModel->camposTabela();
            
            foreach($campos as $campo){
                $dados[$campo] = '';
            }
            
            #$idViab = false;

        }
        
        $dados['dadosViab'] = $this->$modelAssunto->dadosViabilidade($idResp);
        $dados['idViabResp'] = $idResp;
        $dados['status'] = $this->$modelAssunto->statusHistorico('FINAL_NAO'); 
        $dados['assunto'] = self::assunto;
        $dados['modulo'] = self::modulo; 
        $dados['controller'] = self::controller;  
        $dados['dirDownload'] = self::linkDownload;
        #var_dump($dados['cd_usuario']); exit();
        
        #$statusFinal = $this->$modelAssunto->eStatusFinal($idResp);
        $statusFinal = false;
        
        if($statusFinal and !in_array(self::perCadEdit, $this->session->userdata('permissoes'))){
            redirect(base_url(self::modulo.'/'.self::controller.'/listarHistorico/'.$idResp));
        }
        
        // Se o usuário for o autor e tiver permissão para alterar ou se o usuário tiver permissão para forçar alteração
        if(
                ( # Se não for status final e usuário tiver permissão para (cadastrar)
                    (!$statusFinal) 
                    and (in_array(self::perCadEdit, $this->session->userdata('permissoes'))) 
                )
                or
                (  # Se não for status final e o autor for igual ao usuário da sessão e usuário tiver permissão para (editar)
                    (!$statusFinal) 
                    and ($dados['cd_usuario'] == $this->session->userdata('cd')) // Se usuário for igual autor
                    and (in_array(self::perCadEdit, $this->session->userdata('permissoes'))) 
                )
                or (    // se o usuário tiver permissão para forçar alteração
                        in_array(self::perForcaAlt, $this->session->userdata('permissoes'))
                )
            )
        {
                
            $this->load->view(self::pastaView.'/view_frm',$dados);
        
        }else{
            
            $this->load->view('view_permissao');
            
        }
        
    }
    
    public function salvar(){
        
        $modelAssunto = self::modelAssunto;
        
        $this->crudModel->setTabela(BANCO_TELECOM.'.'.self::tabela);
        $this->crudModel->setCampoId('id');
        $this->crudModel->setCamposIgnorados(array('anexoOrigem'));
        $this->crudModel->setTextArea(array('observacao', 'anexo_label', 'anexo'));
        
        array_pop($_POST);
        
        $_POST['cd_usuario'] = $this->session->userdata('cd');
        
        if($this->input->post('id')){
            
            try{
                
                $this->anexaArquivo();
            
                $status = $this->crudModel->atualiza();
                $this->logDados['descricao'] = ucfirst(self::modulo).' - '.self::assunto.' - Atualiza '.self::assunto.' ('.$this->input->post('id').')';
                $this->logDados['acao'] = 'UPDATE';
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
            $this->logGeral->grava($this->logDados);
            
            if($status){
                
                $this->$modelAssunto->atualizaStatusViabRes();
                
                $this->envioEmail($this->input->post('idViabResp'), 'Atualizada');
                
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>'.self::assunto.' salva com sucesso!</strong></div>');
            }else{
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao salvar '.strtolower(self::assunto).', caso o erro persiste comunique o administrador!</div>');
            }
            #redirect(base_url(self::modulo.'/'.self::controller.'/frm/'.$this->input->post('idViabResp').'/'.$this->input->post('id'))); 
            redirect(base_url(self::modulo.'/'.self::controller.'/listarHistorico/'.$this->input->post('idViabResp')));
            
        }else{
            
            try{
                
                $this->anexaArquivo();
            
                $status = $this->crudModel->insereMysql();
                $this->logDados['descricao'] = ucfirst(self::modulo).' - '.self::assunto.' - Cadastra '.self::assunto.' ('.$status.')';
                $this->logDados['acao'] = 'INSERT';
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
            $this->logGeral->grava($this->logDados);
            
            $_POST['id'] = $status;
            
            if($status){
                
                $this->$modelAssunto->atualizaStatusViabRes();
                
                $this->envioEmail($this->input->post('idViabResp'), 'Cadastrada');
                
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>'.self::assunto.' salva com sucesso!</strong></div>');
            }else{
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao salvar '.strtolower(self::assunto).', caso o erro persiste comunique o administrador!</div>');
            }
            #redirect(base_url(self::modulo.'/'.self::controller.'/frm/'.$this->input->post('idViabResp').'/'.$this->input->post('id'))); 
            redirect(base_url(self::modulo.'/'.self::controller.'/listarHistorico/'.$this->input->post('idViabResp'))); 
        }
        
    }
    
    public function envioEmail($id, $tipo = 'Cadastrada'){
        
        $modelAssunto = self::modelAssunto;
        
        $dados = $this->$modelAssunto->dadosViabilidade($id);
        
        $numero = ($dados->numero != '')? $dados->numero: $dados->n_solicitacao;
        
        $dadosHist = $this->$modelAssunto->dadosViabilidadeHist($this->input->post('id'));
        $idEmailEnvia = $this->$modelAssunto->pegaIdEmailEnvia($this->input->post('idStatusHist'));
        #echo '<pre>'; print_r($dadosHist); exit();
        # Usuários de rede interna pela unidade informada
        $usuario = $this->emailModel->usuarioEnviaEmail($idEmailEnvia, $dados->cd_unidade);
        
        $nomeDe = 'Sim TV - Ferramenta de negocios | Telecom - '.self::assunto;
        $emailDe = 'naoresponda@simtv.com.br';
        #$titulo = utf8_decode("Controle de Telecom - ".self::assunto." - ".$dados->controle);
        $titulo = utf8_decode("[Histórico de viabilidade] - (".$numero.") - Operação: ".$dados->unidade);
        $para = 'equipe.sistemas@simtv.com.br';
        
        $msg = "<strong>[Histórico de viabilidade] - (".$numero.") - Operação: ".$dados->unidade."</strong><br>";
        $msg .= "Foi gravado um histórico da viabilidade ".$numero." da Operação ".$dados->unidade.".<br><br>";
        $msg .= "<strong>Conteúdo:</strong><br>";
        $msg .= nl2br($dadosHist->observacao);
        $msg .= "<br><br><strong>Status:</strong> ".$dadosHist->nome.'<br><br>';
        #echo $msg; exit();
        foreach($usuario as $usu){
            
            $token = 'token_'. $this->util->base64url_encode($usu->login_usuario);
            $link = '<a title="Para visualizar ou responder" href="'.base_url(self::modulo.'/'.self::controller.'/listarHistorico/'.$this->input->post('idViabResp').'/'.$token).'">CLIQUE AQUI</a>';
    
            $this->util->enviaEmail($nomeDe, $emailDe, $usu->email_usuario, $titulo, utf8_decode($msg).$link, false);
        
        }
        
    }
    
    public function anexaArquivo(){

        if($_FILES['anexo']['tmp_name']){
                    
            $apagouAnterior = false;
            if($this->input->post('anexoOrigem') != ''){
                $apagou = $this->util->apagaArquivo(self::dirUpload.'/'. $this->input->post('anexoOrigem') );
                if($apagou){
                    $apagouAnterior = true;
                }
            }
            
            $config['upload_path'] = self::dirUpload;
            #$config['allowed_types'] = 'pdf|jpg|png';
            #$config['allowed_types'] = 'pdf';
    		$config['allowed_types'] = '*';
    		$config['max_size'] = '50000';
    		#$config['max_width'] = '0';
    		#$config['max_height'] = '0';
    		#$config['encrypt_name'] = true;
            $file_name = md5_file($_FILES['anexo']['tmp_name']);
            $config['file_name'] = $file_name;
            
            $statusUpload = $this->util->uploadArquivo('anexo', $config);
            
            if($statusUpload['status'] === true){ 
                $_POST['anexo_label'] = $_FILES['anexo']['name'];
                $_POST['anexo'] = $statusUpload['arquivo']['file_name'];
            }else{
                if($apagouAnterior){
                    $_POST['anexo_label'] = '';
                    $_POST['anexo'] = '';
                }else{
                    $_POST['anexo'] = $this->input->post('anexoOrigem');
                }
            }
            
        }else{
            $_POST['anexo'] = $this->input->post('anexoOrigem');
        }
        
    }
    
    public function autenticaToken($token){
        
        $modelAssunto = self::modelAssunto;
        
        $token = $this->util->base64url_decode(str_replace('token_','', $token)); 
        
        $usuario = $this->$modelAssunto->autenticaUsuario($token);
        if($usuario){
            
            try{
            
                $permissoesPerfil = $this->permissaoPerfil->permissoesDoPerfil($usuario->cd_perfil);
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
            $permissoesDoPerfil = array_column($permissoesPerfil, 'cd_permissao');
            
            $dados = array(
                'cd' => $usuario->cd_usuario,
                'login' => $usuario->login_usuario,
                'matricula' => $usuario->matricula_usuario,
                'nome' => $usuario->nome_usuario, 
                'email' => $usuario->email_usuario, 
                'departamento' => $usuario->cd_departamento,
                'bem_vindo' => $usuario->nome_usuario,
                'perfil' => $usuario->cd_perfil,
                'indexHabilita' => $usuario->index_php_usuario,
                'indexPHP' => ($usuario->index_php_usuario == 'SIM')? 'index.php/': '',
                'permissoes' => $permissoesDoPerfil,
                'logado' => true,
                'horaLogin' => $usuario->data_hora_atual,
                'chatStatus' => $usuario->status_chat_usuario,
                'chatDataHora' => $usuario->data_chat_usuario,
                'chatRequestStatus' => $usuario->data_hora_atual, # Último horário da verificação de status de usuário
                'chatRequestNewConversa' => $usuario->data_hora_atual, # Último horário da verificação de novas conversas
                'chatRequestNewMsg' => $usuario->data_hora_atual, # Último horário da verificação de nova mensagem enviada por quem você conversa
                'chatOpen' => 'nao',
                'chatContOpen' => 0
            );
                             
            $this->session->set_userdata($dados);  
        }

    }
    
    public function deleta(){
        
        $modelAssunto = self::modelAssunto;
        
        $id = $this->$modelAssunto->pegaIdResposta($this->input->post('apg_id'));
        
        $this->crudModel->setTabela(BANCO_TELECOM.'.'.self::tabela);
        $this->crudModel->setCampoId('id');
        
        try{
            $status = $this->crudModel->delete();
            
            $this->logDados['descricao'] = ucfirst(self::modulo).' - '.self::assunto.' - Apaga '.self::assunto;
            $this->logDados['acao'] = 'DELETE'; 
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        $this->logGeral->grava($this->logDados);
        
        if($status){
            
            $_POST['idStatusHist'] = $this->$modelAssunto->pegaUltimoStatus($id);
            $_POST['idViabResp'] = $id;
            $this->$modelAssunto->atualizaStatusViabRes();
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>'.self::assunto.' apagado com sucesso!</strong></div>');
        }else{
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao apagar '.strtolower(self::assunto).', caso o erro persiste comunique o administrador!</div>');
        }
        redirect(base_url(self::modulo.'/'.self::controller.'/listarHistorico/'.$id));

    }
    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */