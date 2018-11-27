<?php 
include_once('viabilidadeResp.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ativacao extends viabilidadeResp {
    
    const assunto = 'Viabilidade resposta - Ativação';
    const idStatusAtivacao = 16;
    const dirUpload = './files/telecom/viabilidade/historico';
    
    public function __construct(){
        
        parent::__construct();
        
        $this->load->model('ativacao_model','ativacao');
        $this->load->model('tcom-contrato/contrato_model','contrato');
        $this->load->model('tcom-contrato/contratoValor_model','contratoValor');        
        $this->load->model('tcom-viabilidade-resp-hist/viabilidade_resp_hist_model','viabilidadeRespHist');
        $this->load->model('tcom-equipamento/equipModelo_model','equipModelo');
        $this->load->model('tcom-circuito/circuito_model','circuito');
        
    }
    
    function index()
    {
        #echo parent::modulo; exit();
    }
    
    public function realizarAtivacao()
    {   
        if(isset($_SERVER['HTTP_REFERER'])){
            $redirect = explode('/', substr($_SERVER['HTTP_REFERER'],7));
            $redirect = $redirect[2].'/'.$redirect[3].'/'.$redirect[4];
        }else{
            $redirect = self::modulo.'/'.self::controller.'/pesq';
        }
        
        $dados['redirect'] = $redirect;
        $dados['pendenciaAtivacao'] = $this->ativacao->pendenciaAtivacao(); 
        $dados['equipamentos'] = $this->equipModelo->equipMarcaModeloCodigoDisponiveis();
        $dados['assunto'] = self::assunto;
        $dados['modulo'] = self::modulo; 
        $dados['controller'] = self::controller;  
        $dados['dirDownload'] = self::linkDownload;
        $dados['perPendCadPerg'] = self::perPendCadPerg;
        $menu['menuLateral'] = $this->menuLateral;
        $this->layout->region('html_header', 'view_html_header');
        $this->layout->region('menu_lateral', 'tcom/view_menu_lateral', $menu);

        if(in_array(self::perCadEdit, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', self::pastaView.'/view_ativacao', $dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
    }
    
    public function salvarAtivacao(){
        
        #echo '<pre>'; print_r(array_unique($this->input->post('equip')) ); exit();
        #echo '<pre>'; print_r($_POST); exit();
        
        $this->anexaArquivo();

        switch($this->input->post('idTipo')){
            
            case 1: # Ativação
            case 7: # Co-Location
                $_POST['idStatusHist'] = self::idStatusAtivacao;
                $status = $this->executaAtivacao();
                break;
            case 2: # Upgrade
            case 3: # Downgrade
            case 6: # Outras mudanças
                $_POST['idStatusHist'] = self::idStatusAtivacao;
                $status = $this->executaUpgradeDowngrade();
                break;
            case 5: # Mudança de endereço
                $_POST['idStatusHist'] = self::idStatusAtivacao;
                $status = $this->executaMudancaEndereco();
                break;
        }
        
        if($status){
            
            $this->envioEmail($this->input->post('idResposta'), $this->input->post('idStatusHist'));
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Ativado com sucesso!</strong></div>');
        }else{
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao ativar, caso o erro persiste comunique o administrador!</div>');
        }
        #redirect(base_url(self::modulo.'/'.self::controller.'/pesq'));
        redirect(base_url($this->input->post('redirect')));
        
    }
    
    public function executaAtivacao(){
        
        return $this->ativacao->aplicaAtivacao();
    }
    
    public function executaUpgradeDowngrade(){
        
        return $this->ativacao->aplicaUpgradeDowngrade();
        
    }
    
    public function executaMudancaEndereco(){
        
        return $this->ativacao->aplicaMudancaEndereco();
        
    }
    
    public function envioEmail($id, $idStatus){
        
        $dados = $this->viabilidadeRespHist->dadosViabilidade($id);
        
        $idEmailEnvia = $this->viabilidadeRespHist->pegaIdEmailEnvia($idStatus);

        $usuario = $this->emailModel->usuarioEnviaEmail($idEmailEnvia, $dados->cd_unidade);
        
        $nomeDe = 'Sim TV - Ferramenta de negocios | Telecom - Ativação';
        $emailDe = 'naoresponda@simtv.com.br';
        #$titulo = utf8_decode("Controle de Telecom - ".self::assunto." - ".$dados->controle);
        #$titulo = "Ativação - (".$dados->n_solicitacao.") - Operação: ".utf8_decode($dados->unidade);
        $titulo = "Ativação - (".$this->input->post('numeroContrato').") - Operação: ".utf8_decode($dados->unidade);
        $para = 'equipe.sistemas@simtv.com.br';
        
        #$msg = "<strong>:: CONCLUSÃO ATIVAÇÃO - (".$dados->n_solicitacao.") - Operação: ".utf8_decode($dados->unidade)."</strong><br><br>";
        $msg = "<strong>:: CONCLUSÃO ATIVAÇÃO - (".$this->input->post('numeroContrato').") - Operação: ".utf8_decode($dados->unidade)."</strong><br><br>";
        #$msg .= "<strong>Conteúdo:</strong><br>";
        
        if($this->input->post('equip-nome')){
            $msg .= "Equipamentos usados:<br>"; 
            foreach($this->input->post('equip-nome') as $equipNome){
                $msg .= utf8_encode($equipNome."<br>");
            }
            $msg .="<br>";
        }
        
        $msg .= utf8_decode($this->input->post('obs_ativacao')).'<br>';
        
        foreach($usuario as $usu){
            
            $token = 'token_'. $this->util->base64url_encode($usu->login_usuario);
            $link = '<a title="Para visualizar ou responder" href="'.base_url('tcom-viabilidade-resp-hist/viabilidadeRespHist/listarHistorico/'.$id.'/'.$token).'">CLIQUE AQUI</a>';
    
            $this->util->enviaEmail($nomeDe, $emailDe, $usu->email_usuario, $titulo, nl2br($msg).$link, false);
        
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
    
}