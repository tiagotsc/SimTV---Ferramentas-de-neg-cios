<?php 
include_once(APPPATH.'modules/base/controllers/base.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class viabilidadeResp extends Base {
     
     protected $logDados;
     const modulo = 'tcom-viabilidade-resp';
     const controller = 'viabilidadeResp';
     const pastaView = 'viabilidade_resp';
     const tabela = 'tcom_viab_resp';
     const assunto = 'Viabilidade resposta';
     const modelAssunto = 'viabilidadeResp';
     const perModulo = 274;
     const perPesq = 337;
     const perVisualizarHistorico = 347;
     const perCadEdit = 339;
     const perAtivacao = 343;
     const perAprovacao = 340;
     const perImprimir = 358;
     const perDeletar = 341;
     const perPendCadPerg = 342;
     const emailEnviaResposta = 5; 
     const emailEnviaAprovacao = 6; 
     const dirUpload = './files/telecom/viabilidade/resposta';
     const linkDownload = 'files/telecom/viabilidade/resposta';
     
    /**
     * relatorio::__construct()
     * 
     * Classe resposável por processar os relatórios
     * 
     * @return
     */
    public function __construct(){
        
		parent::__construct();
        $this->logDados['usuario'] = $this->session->userdata('cd');
        $this->logDados['aplicacao'] = SISTEMA;
        $this->logDados['modulo'] = ucfirst(self::modulo).' - '.self::assunto;
        $this->logDados['funcao'] = $_SERVER['REDIRECT_QUERY_STRING'];
        
        $this->load->library('Crud', '', 'crud');
        $this->load->model('email/email_model','emailModel');
        $this->load->model('base/crud_model','crudModel');
        $this->load->model('tcom-viabilidade/viabilidade_model','viabilidade');
        $this->load->model('viabilidade_resp_model',self::modelAssunto);
        $this->load->model('tcom-circuito/circuito_model','circuito');
        $this->load->model('tcom-contrato/contrato_model','contrato');
        $this->load->model('aprovacao_contrato_model','aprovacaoContrato');
        $this->load->model('tcom-cliente/cliente_model','cliente');
        
        $this->util->setPositionMenu('');
        $this->menuLateral = $this->util->montaMenuLateral($this->dadosBanco->menuLateralDropDown('TELECOM', $this->session->userdata('permissoes')), $this->dadosBanco->paisMenuLateralDropDown('TELECOM', $this->session->userdata('permissoes')));
	} 
     
	/**
     * Telefonia::index()
     * 
     * Tela inicial da telefonia
     * 
     * @return
     */
    function index()
    { 
        $this->layout->region('html_header', 'view_html_header');

        $dados['menuLateral'] = $this->menuLateral;
        
        if(in_array(self::perModulo, $this->session->userdata('permissoes'))){
            
            $dados['menuLateral'] = $this->menuLateral;
            $this->layout->region('corpo', 'tcom/view_principal');
        
        }else{
            
            $dados['menuLateral'] = false;
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
        $this->layout->region('menu_lateral', 'view_menu_lateral', $dados);
        
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    public function pesq(){
        
        $mostra_por_pagina = 30;
        $modelAssunto = self::modelAssunto;
        
        $this->crud->ini();
        $par = $this->crud->getParMetodo();
        list($post, $sort_by, $sort_order, $pagina) = $par;
        
        $resultado = $this->$modelAssunto->pesquisa($post, $mostra_por_pagina, $sort_by, $sort_order, $pagina);
        $resultado['tabela'] = self::assunto;
        
        $postEncode = (!$post)? 0: $this->util->base64url_encode($post); 
        
        $crud = $this->crud->listarManual($resultado, $mostra_por_pagina, $postEncode, $sort_by, $sort_order, $pagina);
        $crud['perVisualizarHistorico'] = self::perVisualizarHistorico;
        $crud['perAprovacao'] = self::perAprovacao;
        $crud['perImprimir'] = self::perImprimir;
        $crud['perEditarCadastrar'] = self::perCadEdit;
        $crud['perAtivacao'] = self::perAtivacao;
        $crud['perExcluir'] = self::perDeletar;
        $crud['assunto'] = self::assunto;
        $crud['unidade'] = $this->dadosBanco->unidade();
 
        $dados['menuLateral'] = $this->menuLateral;
        $this->layout->region('html_header', 'view_html_header');
        $this->layout->region('menu_lateral', 'tcom/view_menu_lateral', $dados);
        
        
        if(in_array(self::perPesq, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', self::pastaView.'/view_psq', $crud);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
    }
    
    public function ficha($id = false){
        
        $modelAssunto = self::modelAssunto;
        
        $this->crudModel->setTabela(BANCO_TELECOM.'.'.self::tabela);
        $this->crudModel->setCampoId('id');
        
        if($id){
            
            $dados = $this->crudModel->dadosId($id);
            
            $campos = array_keys($dados);
            
            foreach($campos as $campo){
             
				$dados[$campo] = $dados[$campo]; # ALIMENTA OS CAMPOS COM OS DADOS
			}
            
            $idViab = $dados['idViab'];
            
        }else{
            
            $campos = $this->crudModel->camposTabela();
            
            foreach($campos as $campo){
                $dados[$campo] = '';
            }
            
            $idViab = false;

        }
        
        $dados['vistoriasPendentes'] = $this->$modelAssunto->vistoriasPendentes($idViab); 
        $dados['assunto'] = self::assunto;
        $dados['modulo'] = self::modulo; 
        $dados['controller'] = self::controller;  
        $dados['dirDownload'] = self::linkDownload;
        $dados['perPendCadPerg'] = self::perPendCadPerg;
        $menu['menuLateral'] = $this->menuLateral;
        $this->layout->region('html_header', 'view_html_header');
        $this->layout->region('menu_lateral', 'tcom/view_menu_lateral', $menu);

        if(in_array(self::perCadEdit, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', self::pastaView.'/view_frm', $dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
    }
    
    public function salvar(){
        
        $modelAssunto = self::modelAssunto;
        
        $this->crudModel->setTabela(BANCO_TELECOM.'.'.self::tabela);
        $this->crudModel->setCampoId('id');
        $this->crudModel->setCamposIgnorados(array('idNode', 'idViab_backup', 'anexoOrigem', 'controle'));
        $this->crudModel->setTextArea(array('observacao', 'anexo'));
        
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
                
                $this->envioEmail($this->input->post('id'), 'Atualiza');
                
                
                if($this->input->post('idViab_backup') != $this->input->post('idViab')){
                    $this->$modelAssunto->limpaDadosVistoria();
                    
                    $this->envioEmail($this->input->post('idViab_backup'), 'Desconsiderar');
                    
                }
                $this->$modelAssunto->atualizaDadosVistoria();
                
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>'.self::assunto.' salva com sucesso!</strong></div>');
            }else{
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao salvar '.strtolower(self::assunto).', caso o erro persiste comunique o administrador!</div>');
            }
            redirect(base_url(self::modulo.'/'.self::controller.'/ficha/'.$this->input->post('id'))); 
            
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
                
                $this->envioEmail($this->input->post('id'), 'Cadastrada');
                
                $this->$modelAssunto->atualizaDadosVistoria();
                
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>'.self::assunto.' salva com sucesso!</strong></div>');
            }else{
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao salvar '.strtolower(self::assunto).', caso o erro persiste comunique o administrador!</div>');
            }
            redirect(base_url(self::modulo.'/'.self::controller.'/ficha/'.$this->input->post('id'))); 
            
        }
        
    }
    
    public function imprimir($id){
        $pagina = $this->msgEmailResposta($id, utf8_decode('IMPRESSÃO'));
        echo $pagina;
    }
    
    public function msgEmailResposta($id, $tipo = false){
        
        $modelAssunto = self::modelAssunto; 
        
        $dados['viabResp'] = $this->$modelAssunto->dadosViabilidadeResp($id);
        $dados['viab'] = $this->viabilidade->dadosViabilidade($dados['viabResp']->idViab);
        $dados['circuito'] = $this->$modelAssunto->dadosCircuitoContrato($dados['viabResp']->idContrato);
        $dados['tipo'] = $tipo;
        $dados['dirDownload'] = self::linkDownload;

        return $this->load->view('viabilidade_resp/view_imprimir', $dados, true);
        
    }
    
    public function teste($id){
        
        $msg = $this->msgEmailResposta($id, 'Cadastrada');
        #echo $msg;
        
        $envio = $this->util->enviaEmail('Ferramenta de negocios', 'naoresponda@simtv.com.br', 'tiago.costa@simtv.com.br', 'teste', $msg);
        
        if($envio){
            echo 'Enviou';
        }else{
            echo 'Nao enviou';
        }
        
    }
    
    public function envioEmail($id, $tipo = 'Cadastrada'){
        
        $modelAssunto = self::modelAssunto;
        
        $dados = $this->$modelAssunto->pegaVistoria($id);
        
        $usuario = $this->emailModel->usuarioEnviaEmail(self::emailEnviaResposta, $this->input->post('cd_unidade'));
        
        $nomeDe = 'Sim TV - Ferramenta de negocios | Telecom - '.self::assunto;
        $emailDe = 'naoresponda@simtv.com.br';
        $titulo = utf8_decode('Controle de Telecom - Resposta de Viabilidade Técnica - '.$dados->controle);
        $para = 'equipe.sistemas@simtv.com.br';
        
        $msg = $this->msgEmailResposta($id, $tipo);
        
        foreach($usuario as $usu){
            
            $this->util->enviaEmail($nomeDe, $emailDe, $usu->email_usuario, $titulo, $msg, false);
        
        }
        
    }
    
    public function envioEmailAprovacao($idContrato = false){

        $modelAssunto = self::modelAssunto;
        
        $unidade = $this->$modelAssunto->unidade($this->input->post('idUnidade'));
        
        switch ($this->input->post('aprovacao')) {
            case 'S':
                $aprovacao = 'Aprovação';
                break;
            case 'N':
                $aprovacao = 'Não aprovação';
                break;
            case 'C':
                $aprovacao = 'Cancelamento';
                break;
        }

        $usuario = $this->emailModel->usuarioEnviaEmail(self::emailEnviaAprovacao, $this->input->post('idUnidade'));
        
        $nomeDe = 'Sim TV - Ferramenta de negocios | Telecom - '.$aprovacao.' de viabilidade';
        $emailDe = 'naoresponda@simtv.com.br';
        $titulo = 'Controle de Telecom - '.$aprovacao.' de viabilidade - '.$this->input->post('controle');
        $msg = 'Controle de Telecom - '.$aprovacao.' de viabilidade';
        $msg .= '<br>Controle: '.$this->input->post('controle').'<br><br>';
        $msg .= 'Tipo: '.utf8_decode($this->input->post('tipo')).'<br>';
        $msg .= 'Permissor: '.$unidade->permissor.' - '.$unidade->nome;
        if($idContrato){
            $contrato = $this->contrato->contratos($idContrato);
            $circuito = $this->circuito->circuitos($contrato[0]->idCircuito);  
            if($contrato){
                $msg .= '<br>Contrato: '.$contrato[0]->numero;
                $msg .= '<br>Designação: '.$circuito->designacao;    
            }
        }
        
        foreach($usuario as $usu){
            
            $this->util->enviaEmail($nomeDe, $emailDe, $usu->email_usuario, utf8_decode($titulo), utf8_decode($msg), false);
        
        }
        
    }
    
    public function aprovacao($id){
        
        $modelAssunto = self::modelAssunto;
        
        $dados['id'] = $id;
        $dados['viabResp'] = $this->$modelAssunto->dadosViabilidadeResp($id);
        $dados['viab'] = $this->viabilidade->dadosViabilidade($dados['viabResp']->idViab);
        if($dados['viabResp']->idContrato or $dados['viabResp']->idContratoAtual){
            
            if($dados['viabResp']->idContratoAtual){
                $contrato = $dados['viabResp']->idContratoAtual;
            }else{
                $contrato = $dados['viabResp']->idContrato;
            }
            
            $dados['contrato'] = $this->contrato->contratos($contrato);
            $dados['circuito'] = $this->contrato->contratoCircuito($contrato);
            #$dados['designacaoDados'] = $this->circuito->circuitos($dados['contrato'][0]->idCircuito);
        }else{
            $dados['contrato'] = false;
            $dados['circuito'] = false;
            #$dados['designacaoDados'] = false;
        }
        
        $dados['assunto'] = self::assunto;
        $dados['modulo'] = self::modulo; 
        $dados['controller'] = self::controller;  
        $dados['dirDownload'] = self::linkDownload;
        
        #echo '<pre>'; print_r($dados); exit();
        $menu['menuLateral'] = $this->menuLateral;
        $this->layout->region('html_header', 'view_html_header');
        $this->layout->region('menu_lateral', 'tcom/view_menu_lateral', $menu);

        if(in_array(self::perAprovacao, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', self::pastaView.'/view_aprovacao', $dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    public function salvarAprovacao(){
        
        $modelAssunto = self::modelAssunto;
        
        try{
        
            $status = $this->$modelAssunto->atualizaAprovacao();
            $this->logDados['descricao'] = ucfirst(self::modulo).' - '.self::assunto.' - Aprovação de viabilidade ('.$this->input->post('id').')';
            $this->logDados['idAcao'] = $this->input->post('id');
            $this->logDados['acao'] = 'UPDATE';
        
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        
        $this->logGeral->grava($this->logDados);
        
        if($status['id']){
            switch($status['contratoDados']['status']){
                case 'ok':
                    $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>'.$status['contratoDados']['descricao'].'</strong></div>');                  
                    break;
                case 'erro':
                    $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">'.$status['contratoDados']['descricao'].'</div>'); 
                    break;
                default:
                    $this->session->set_flashdata('statusOperacao', '<div class="alert alert-warning">'.$status['contratoDados']['descricao'].'</div>'); 
            }
            
            $idContrato = ($this->input->post('idContrato'))? $this->input->post('idContrato'): $status['contratoDados']['idContrato'];
            
            $this->envioEmailAprovacao($idContrato);
            
        }else{
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao atualizar / gerar dados, caso o erro persista comunique o administrador!</div>');    
        }
        #echo '<pre>'; print_r(base_url(self::modulo.'/'.self::controller.'/aprovacao/'.$this->input->post('id'))); exit();
        redirect(base_url(self::modulo.'/'.self::controller.'/aprovacao/'.$this->input->post('id')));
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
            $file_name = $this->input->post('controle');
            $config['file_name'] = $file_name;
            
            $statusUpload = $this->util->uploadArquivo('anexo', $config);
            
            if($statusUpload['status'] === true){ 
                $_POST['anexo'] = $statusUpload['arquivo']['file_name'];
            }else{
                if($apagouAnterior){
                    $_POST['anexo'] = '';
                }else{
                    $_POST['anexo'] = $this->input->post('anexoOrigem');
                }
            }
            
        }else{
            $_POST['anexo'] = $this->input->post('anexoOrigem');
        }
        
    }
    
    public function deleta(){
        
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
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>'.self::assunto.' apagada com sucesso!</strong></div>');
        }else{
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao apagar '.strtolower(self::assunto).', caso o erro persiste comunique o administrador!</div>');
        }
        redirect(base_url(self::modulo.'/'.self::controller.'/pesq'));

    }
    
    public function importaArquivosCivis(){
        # ÚLTIMA IMPORTAÇÃO - 14/04/2016
        # PRÓXIMA IMPORTAÇÃO A PARTIR DE - 15/04/2016
        $arquivos = array(
        'http://civis.simtv.com.br/edificacao/arquivo/86996.pdf',
        'http://civis.simtv.com.br/edificacao/arquivo/64-60534.pdf',
        'http://civis.simtv.com.br/edificacao/arquivo/60644.pdf',
        'http://civis.simtv.com.br/edificacao/arquivo/60588.pdf'
        );
        
        foreach($arquivos as $file){
        
            #$file = 'http://civis.simtv.com.br/edificacao/arquivo/86966.pdf';
            $newfile = './temp/migracao/'.basename($file);
            
            if (!copy($file, $newfile)) {
                echo "falha ao copiar $file...<br>";
            }else{
                echo "Copiou $file<br>";
            }
        
        }
        exit();
        
    }
    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */