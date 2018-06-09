<?php 
include_once(APPPATH.'modules/base/controllers/base.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contrato extends Base {
     
     private $logDados;
     const modulo = 'tcom-contrato';
     const controller = 'contrato';
     const pastaView = 'contrato';
     const tabela = 'tcom_contrato';
     const assunto = 'Contrato';
     const modelAssunto = 'contrato';
     const perModulo = 274;
     const perPesq = 322;
     const perImprimir = 323;
     const perAnexar = 324;
     const perVisualizarHistorico = 347; # O mesmo do controller "viabilidadeResp"
     const perNumero = 398;
     const perDesignacao = 391;
     const perVigencia = 392;
     const perStatus = 325;
     const perPontaB = 399;
     const perCadEdit = 390;
     const perDeletar = 326;
     const perValores = 364;
     
     const emailEnviaEnviaAnalise = 38;
     const emailEnviaRecebeRespostaAnalise = 39;
     
     const dirUpload = './files/telecom/contrato';
     const linkDownload = 'files/telecom/contrato';
     
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
        $this->load->model('base/crud_model','crudModel');
        $this->load->model('tcom-contrato/contrato_model',self::modelAssunto);
        $this->load->model('tcom-contrato/analiseFinanceira_model','analiseFin');
        $this->load->model('tcom-operadora/operadora_model','operadora');
        $this->load->model('tcom-cliente/cliente_model','cliente');
        $this->load->model('tcom-interface/tinterface_model','interface');
        $this->load->model('tcom-taxa-digital/taxaDigital_model','velocidade');
        
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
        $crud['permissor'] = $this->dadosBanco->unidade();
        $crud['perEditarCadastrar'] = self::perCadEdit;
        $crud['perImprimir'] = self::perImprimir;
        $crud['perVisualizarHistorico'] = self::perVisualizarHistorico;
        $crud['perStatus'] = self::perStatus;
        $crud['perAnexar'] = self::perAnexar;
        $crud['perValores'] = self::perValores;
        $crud['perExcluir'] = self::perDeletar;
        $crud['dirDownload'] = self::linkDownload;
        $crud['assunto'] = self::assunto;
        $crud['modulo'] = self::modulo;
 
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
        
        $this->crudModel->setTabela(BANCO_TELECOM.'.'.self::tabela);
        $this->crudModel->setCampoId('id');
        $abre = false;
        if($id){
            
            $dados = $this->crudModel->dadosId($id);
            
            if($dados){
                $abre = true;
            }
            
            $campos = array_keys($dados);
            
            foreach($campos as $campo){
             
				$dados[$campo] = $dados[$campo]; # ALIMENTA OS CAMPOS COM OS DADOS
			}
            #echo $dados['idCircuito']; exit();
            
            $circuito = $this->crudModel->dadosIDSimples(BANCO_TELECOM.'.tcom_circuito','id', $dados['idCircuito']);
            $campos = array_keys($circuito);
            foreach($campos as $campo){
				    $dados['circuito_'.$campo] = $circuito[$campo]; # ALIMENTA OS CAMPOS COM OS DADOS
			}
            
            /*$valores = $this->crudModel->dadosIDSimples(BANCO_TELECOM.'.tcom_contrato_valor','idContrato', $id);
            $campos = array_keys($valores);
            foreach($campos as $campo){
				    $dados['valor_'.$campo] = $valores[$campo]; # ALIMENTA OS CAMPOS COM OS DADOS
			}*/
            
            $unidade = $this->crudModel->dadosIDSimples('adminti.unidade','cd_unidade', $dados['cd_unidade']);

            $campos = array_keys($unidade);
            foreach($campos as $campo){
				    $dados['unidade_'.$campo] = $unidade[$campo]; # ALIMENTA OS CAMPOS COM OS DADOS
			}
            
        }else{
            
            $campos = $this->crudModel->camposTabela();
            
            foreach($campos as $campo){
                $dados[$campo] = '';
            }
            
        }

        #echo '<pre>'; print_r($dados); exit();
        $dados['assunto'] = self::assunto;
        $dados['modulo'] = self::modulo; 
        $dados['controller'] = self::controller;  
        $dados['interface'] = $this->interface->interfaces();
        $dados['velocidade'] = $this->velocidade->taxas();
        $dados['perNumero'] = self::perNumero;
        $dados['perDesignacao'] = self::perDesignacao;
        $dados['perVigencia'] = self::perVigencia;
        $dados['perStatus'] = self::perStatus;
        $dados['perPontaB'] = self::perPontaB;
        $dados['perAnexar'] = self::perAnexar;
        $dados['perValores'] = self::perValores;
        $dados['estado'] = $this->dadosBanco->estado(); 
        $dados['dadosContCir'] = $this->contrato->dadosContratoCircuito($dados['id'], $dados['idCircuito']);
        $dados['anexos'] = $this->contrato->anexos($id);
        
        $menu['menuLateral'] = $this->menuLateral;
        $this->layout->region('html_header', 'view_html_header');
        $this->layout->region('menu_lateral', 'tcom/view_menu_lateral', $menu);

        if(in_array(self::perCadEdit, $this->session->userdata('permissoes')) and $abre){
        
            $this->layout->region('corpo', self::pastaView.'/view_frm', $dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
    }
    
    public function imprimir($id = false){
        
        $dados['contrato'] = $this->crudModel->dadosIDSimples(BANCO_TELECOM.'.'.self::tabela,'id',$id);
        #$circuito = $this->crudModel->dadosIDSimples(BANCO_TELECOM.'.tcom_circuito','id',$dados['contrato']['idCircuito']);
        $circuito = $this->crudModel->dadosIDSimples(BANCO_TELECOM.'.tcom_contrato_circuito','idContrato',$dados['contrato']['id']);
        $dados['equipamentos'] = $this->contrato->equipamentosAssociados($id);
        $dados['operadora'] = $this->crudModel->dadosIDSimples(BANCO_TELECOM.'.tcom_oper','id',$dados['contrato']['idOper']);
        $dados['operadoraInst'] = $this->crudModel->dadosIDSimples(BANCO_TELECOM.'.tcom_oper_inst','idOper',$dados['operadora']['id']);
        $dados['operadoraCob'] = $this->crudModel->dadosIDSimples(BANCO_TELECOM.'.tcom_oper_cobr','idOper',$dados['operadora']['id']);
        $dados['operadoraCobTel'] = $this->operadora->telefonesCobranca($dados['operadoraCob']['id']);
        $dados['cliente'] = $this->crudModel->dadosIDSimples(BANCO_TELECOM.'.tcom_cliente','id',$dados['contrato']['idCliente']);
        $dados['clienteEnd'] = $this->crudModel->dadosIDSimples(BANCO_TELECOM.'.tcom_cliente_end','idCliente',$dados['cliente']['id']);
        $dados['clienteEndTel'] = $this->cliente->telefones($dados['cliente']['id']);
        $dados['unidade'] = $this->crudModel->dadosIDSimples('adminti.unidade','cd_unidade',$dados['contrato']['cd_unidade']);
        $dados['interface'] = $this->crudModel->dadosIDSimples(BANCO_TELECOM.'.tcom_interface','id',$circuito['idInterface']);
        $dados['taxa_digital'] = $this->crudModel->dadosIDSimples(BANCO_TELECOM.'.tcom_taxa_digital','id',$circuito['idTaxaDigital']);
        $dados['dirDownload'] = self::linkDownload;
        
        $dados['contratoDados'] = $circuito;
        
        $dados['pdf'] = 'nao';
        $dados['email'] = 'nao';
        
        $dados['assunto'] = self::assunto;
        $dados['modulo'] = self::modulo; 
        $dados['controller'] = self::controller;  
        $dados['anexos'] = $this->contrato->anexos($id);
        
        if(in_array(self::perImprimir, $this->session->userdata('permissoes'))){
        
            $this->load->view(self::pastaView.'/view_imprimir', $dados);
        
        }else{
            
            $this->load->view('view_permissao');
            
        }

    }
    
    /*public function alterarStatusVigencia(){
        
        $modelAssunto = self::modelAssunto;
        
        try{
            $status = $this->$modelAssunto->alterarStatusVigencia();
            
            $this->logDados['descricao'] = ucfirst(self::modulo).' - '.self::assunto.' - Altera status '.self::assunto;
            $this->logDados['acao'] = 'UPDATE'; 
            $this->logDados['idAcao'] = $this->input->post('alt_id');
            
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        $this->logGeral->grava($this->logDados);
        
        if($status){
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Status do '.self::assunto.' alterado com sucesso!</strong></div>');
        }else{
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao alterar status do '.strtolower(self::assunto).', caso o erro persiste comunique o administrador!</div>');
        }
        redirect(base_url(self::modulo.'/'.self::controller.'/pesq'));
        
    }*/
    
    public function salvarAnexo(){
        
        array_pop($_POST);
        
        $this->crudModel->setTabela(BANCO_TELECOM.'.tcom_contrato_anexo');
        $this->crudModel->setCampoId('id');
        $this->crudModel->setCamposIgnorados(array('btn'));
        $this->crudModel->setTextArea(array('anexo_label', 'anexo'));
        $_POST['cd_usuario'] = $this->session->userdata('cd');
        try{
        
            $this->anexaArquivo();
            
            $status = $this->crudModel->insereMysql();
            
            $this->logDados['descricao'] = ucfirst(self::modulo).' - '.self::assunto.' - Anexa arquivo '.self::assunto;
            $this->logDados['acao'] = 'INSERT'; 
            $this->logDados['idAcao'] = $this->input->post('idContrato');
        
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }

        $this->logGeral->grava($this->logDados);
        
        if($status){
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Arquivo anexado com sucesso!</strong></div>');
        }else{
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger"><strong>Erro ao anexar o arquivo, caso o erro persista comunique o administrador!</strong></div>');
        }
        redirect(base_url(self::modulo.'/'.self::controller.'/ficha/'.$this->input->post('idContrato')));
    }
    
    public function anexoApaga($idContrato,$arquivo){
        
        try{
            $status = $this->contrato->apagaAnexo($idContrato,$arquivo);
            
            if($status){
                $this->util->apagaArquivo(self::dirUpload.'/'. $arquivo );
            }
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        if($status){
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Arquivo apagado com sucesso!</strong></div>');
        }else{
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger"><strong>Erro ao apagar o arquivo, caso o erro persista comunique o administrador!</strong></div>');
        }
        redirect(base_url(self::modulo.'/'.self::controller.'/ficha/'.$idContrato));
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
    
    public function salvar(){

        $this->crudModel->setTabela(BANCO_TELECOM.'.'.self::tabela);
        $this->crudModel->setCampoId('id');
        $this->crudModel->setTextArea(array('tipo'));
        
        array_pop($_POST);
        
        if($this->input->post('id')){
            
            try{
            
                $status = $this->crudModel->atualiza();
                $this->logDados['descricao'] = ucfirst(self::modulo).' - '.self::assunto.' - Atualiza '.self::assunto.' ('.$this->input->post('id').')';
                $this->logDados['acao'] = 'UPDATE';
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
            $this->logGeral->grava($this->logDados);
            
            if($status){
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>'.self::assunto.' salva com sucesso!</strong></div>');
            }else{
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao salvar '.strtolower(self::assunto).', caso o erro persiste comunique o administrador!</div>');
            }
            redirect(base_url(self::modulo.'/'.self::controller.'/ficha/'.$this->input->post('id'))); 
            
        }else{
            
            try{
            
                $status = $this->crudModel->insereMysql();
                $this->logDados['descricao'] = ucfirst(self::modulo).' - '.self::assunto.' - Cadastra '.self::assunto.' ('.$status.')';
                $this->logDados['acao'] = 'INSERT';
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
            $this->logGeral->grava($this->logDados);
            
            $_POST['id'] = $status;
            
            if($status){
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>'.self::assunto.' salva com sucesso!</strong></div>');
            }else{
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao salvar '.strtolower(self::assunto).', caso o erro persiste comunique o administrador!</div>');
            }
            redirect(base_url(self::modulo.'/'.self::controller.'/ficha/'.$this->input->post('id'))); 
            
        }
        
    }
    
    public function deleta(){
        
        $this->load->model('tcom-contrato/log_contrato_model','contratoLog');
        
        #$this->crudModel->setTabela(BANCO_TELECOM.'.'.self::tabela);
        #$this->crudModel->setCampoId('id');
        
        try{
            #$status = $this->crudModel->delete();
            $status = $this->contrato->deleta();
            
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
    
    public function alteraPontabEndereco(){
        
        $modelAssunto = self::modelAssunto;
        
        try{
            $status = $this->$modelAssunto->alteraPontabEndereco();
            
            $this->logDados['descricao'] = ucfirst(self::modulo).' - '.self::assunto.' - Altera endereço '.self::assunto;
            $this->logDados['acao'] = 'UPDATE'; 
            $this->logDados['idAcao'] = $this->input->post('idContrato');
            
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        
        $this->logGeral->grava($this->logDados);
        
        if($status){
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Endere&ccedil;o do '.strtolower(self::assunto).' alterado com sucesso!</strong></div>');
        }else{
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao alterar endere&ccedil;o do '.strtolower(self::assunto).', caso o erro persiste comunique o administrador!</div>');
        }
        redirect(base_url(self::modulo.'/'.self::controller.'/ficha/'.$this->input->post('idContrato')));
        
    }
    
    public function alteraNumero(){
        
        $modelAssunto = self::modelAssunto;
        
        try{
            $status = $this->$modelAssunto->alterarNumero();
            
            $this->logDados['descricao'] = ucfirst(self::modulo).' - '.self::assunto.' - Altera número '.self::assunto;
            $this->logDados['acao'] = 'UPDATE'; 
            $this->logDados['idAcao'] = $this->input->post('id');
            
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        
        $this->logGeral->grava($this->logDados);
        
        if($status){
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>N&uacute;mero do '.strtolower(self::assunto).' alterado com sucesso!</strong></div>');
        }else{
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao alterar n&uacute;mero do '.strtolower(self::assunto).', caso o erro persiste comunique o administrador!</div>');
        }
        redirect(base_url(self::modulo.'/'.self::controller.'/ficha/'.$this->input->post('id')));
        
    }
    
    public function alteraVigencia(){
        
        $modelAssunto = self::modelAssunto;
        
        try{
            $status = $this->$modelAssunto->alterarVigencia();
            
            $this->logDados['descricao'] = ucfirst(self::modulo).' - '.self::assunto.' - Altera vigência '.self::assunto;
            $this->logDados['acao'] = 'UPDATE'; 
            $this->logDados['idAcao'] = $this->input->post('id');
            
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        $this->logGeral->grava($this->logDados);
        
        if($status){
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Vig&ecirc;ncia do '.strtolower(self::assunto).' alterada com sucesso!</strong></div>');
        }else{
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao alterar vig&ecirc;ncia do '.strtolower(self::assunto).', caso o erro persiste comunique o administrador!</div>');
        }
        redirect(base_url(self::modulo.'/'.self::controller.'/ficha/'.$this->input->post('id')));
        
    }
    
    public function alteraStatus(){

        $modelAssunto = self::modelAssunto;
        
        try{
            $status = $this->$modelAssunto->alterarStatus();
            
            $this->logDados['descricao'] = ucfirst(self::modulo).' - '.self::assunto.' - Altera status '.self::assunto;
            $this->logDados['acao'] = 'UPDATE'; 
            $this->logDados['idAcao'] = $this->input->post('id');
            
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        
        $this->logGeral->grava($this->logDados);
        
        if($status){
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Status do '.strtolower(self::assunto).' alterado com sucesso!</strong></div>');
        }else{
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao alterar status do '.strtolower(self::assunto).', caso o erro persiste comunique o administrador!</div>');
        }
        redirect(base_url(self::modulo.'/'.self::controller.'/ficha/'.$this->input->post('id')));
        
    }
    
    public function salvarValores(){

        $status = $this->contrato->salvaValores();
        if($status){
            
            if($this->input->post('email') == 'sim'){ 
                $this->analiseFinEmail();
            }
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Valores salvo com sucesso!</strong></div>');
        }else{
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao salvar valores do '.strtolower(self::assunto).', caso o erro persiste comunique o administrador!</div>');
        }
        redirect(base_url(self::modulo.'/'.self::controller.'/pesq'));
        
    }
    
    public function analiseFinEmail(){

        $modelAssunto = self::modelAssunto;
        $usuario = $this->$modelAssunto->usuarioEnviaEmail(self::emailEnviaEnviaAnalise,false,'objeto'); 
        $recebeResposta = array_column( $this->$modelAssunto->usuarioEnviaEmail(self::emailEnviaRecebeRespostaAnalise, false, 'array'), 'email_usuario' );
        
        $this->analiseFin->gravaPrimeiroEnvioAnalise($this->input->post('valor_id'), $usuario);
        
        $nomeDe = 'Sim TV - Ferramenta de negocios | Telecom - '.self::assunto;
        $emailDe = 'naoresponda@simtv.com.br';
        $titulo = utf8_decode('Telecom - Análise Financeira - Contrato: '.$this->input->post('nome_contrato_valor'));
        $para = 'equipe.sistemas@simtv.com.br';
        
        $msg = $this->imprimirAnaliseFin($this->input->post('valor_id'),'htmlEmail');
        $ordemPdf = $this->geraOrdemPDF($msg);
        
        foreach($usuario as $usu){
            $link = "<h3>Telecom - Análise Financeira - Contrato: ".$this->input->post('nome_contrato_valor')."</h3>";
            $link .= "<a href='".base_url('tcom-contrato/contratoExterno/aprovacaoAnaliseFinanceira/'.md5($usu->email_usuario).'/'.md5($this->input->post('valor_id')))."'>";
            $link .= "<h1 style='text-align:center'>Clique aqui para responder aprovação</h1>";
            $link .= "</a>";
            $link .= "<h4>Segue em anexo o contrato para análise.</h4>";
            $link .= "<br>Att,<br>Telecom.";
            #$msg = utf8_decode($link).$msg;
            $msg = utf8_decode($link);
            $this->util->enviaEmail($nomeDe, $emailDe, $usu->email_usuario, $titulo, $msg, $ordemPdf, $recebeResposta);
            #$this->util->enviaEmail($nomeDe, $emailDe, 'tiago.costa@simtv.com.br', $titulo, utf8_decode($link), $ordemPdf, $recebeResposta);
            
        }
        
        $this->util->apagaArquivo('./temp/'.$ordemPdf.'.pdf');
     
    }
    
    public function imprimirAnaliseFin($id = false, $tipo = 'html'){
        
        $dados['contrato'] = $this->crudModel->dadosIDSimples(BANCO_TELECOM.'.'.self::tabela,'id',$id);
        $circuito = $this->crudModel->dadosIDSimples(BANCO_TELECOM.'.tcom_circuito','id',$dados['contrato']['idCircuito']);
        $dados['equipamentos'] = $this->contrato->equipamentosAssociados($id);
        $dados['operadora'] = $this->crudModel->dadosIDSimples(BANCO_TELECOM.'.tcom_oper','id',$dados['contrato']['idOper']);
        $dados['operadoraInst'] = $this->crudModel->dadosIDSimples(BANCO_TELECOM.'.tcom_oper_inst','idOper',$dados['operadora']['id']);
        $dados['operadoraCob'] = $this->crudModel->dadosIDSimples(BANCO_TELECOM.'.tcom_oper_cobr','idOper',$dados['operadora']['id']);
        $dados['operadoraCobTel'] = $this->operadora->telefonesCobranca($dados['operadoraCob']['id']);
        $dados['cliente'] = $this->crudModel->dadosIDSimples(BANCO_TELECOM.'.tcom_cliente','id',$dados['contrato']['idCliente']);
        $dados['clienteEnd'] = $this->crudModel->dadosIDSimples(BANCO_TELECOM.'.tcom_cliente_end','idCliente',$dados['cliente']['id']);
        $dados['clienteEndTel'] = $this->cliente->telefones($dados['cliente']['id']);
        $dados['unidade'] = $this->crudModel->dadosIDSimples('adminti.unidade','cd_unidade',$dados['contrato']['cd_unidade']);
        $dados['interface'] = $this->crudModel->dadosIDSimples(BANCO_TELECOM.'.tcom_interface','id',$circuito['idInterface']);
        $dados['taxa_digital'] = $this->crudModel->dadosIDSimples(BANCO_TELECOM.'.tcom_taxa_digital','id',$circuito['idTaxaDigital']);
        $dados['valores'] = $this->crudModel->dadosIDSimples(BANCO_TELECOM.'.tcom_contrato_valor','idContrato',$id);
        $dados['circuito'] = $circuito;
        $dados['viabResp'] = $this->contrato->dadosViabResp($id);
        $dados['pdf'] = 'nao';
        $dados['email'] = 'nao';
        $dados['assunto'] = self::assunto;
        $dados['modulo'] = self::modulo; 
        $dados['controller'] = self::controller;  
        
        if(in_array(self::perImprimir, $this->session->userdata('permissoes'))){
            if($tipo == 'html'){
                $this->load->view(self::pastaView.'/view_analise_fin', $dados);
                #$this->load->view(self::pastaView.'/view_teste', $dados);
            }elseif('htmlEmail'){
                $dados['pdf'] = 'sim';
                return $this->load->view(self::pastaView.'/view_analise_fin', $dados, true);
                #return $this->load->view(self::pastaView.'/view_teste', $dados, true);
            }else{
                
            }
        }else{
            
            $this->load->view('view_permissao');
            
        }

    }
    
    public function geraOrdemPDF($arquivo){
        
        //load mPDF library
		$this->load->library('m_pdf');
        
        $stylesheet1 = file_get_contents('./assets/css/bootstrap.css');
        $this->m_pdf->pdf->WriteHTML($stylesheet1,1);
        
       //generate the PDF from the given html
		$this->m_pdf->pdf->WriteHTML(utf8_encode($arquivo));

        $ordem = date("YmdHis").rand(1000,9999);
        $this->m_pdf->pdf->Output("./temp/".$ordem.".pdf");
        
        return $ordem;
        
    }
    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */