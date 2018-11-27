<?php 
include_once(APPPATH.'modules/base/controllers/base.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class edificacao extends Base {
     
     private $logDados;
     const modulo = 'tcom-edificacao';
     const controller = 'edificacao';
     const pastaView = 'edificacao';
     const assunto = 'edificacao';
     const tabela = 'tcom_edificacao';
     const perModulo = 274;
     const perPesq = 282;
     const perReenvioVist = 299;
     const perCadEdit = 283;
     const perImprimir = 284;
     const perMudarStatus = 285;
     const perDeletar = 325;
     const dirUpload = './files/telecom/geodados/edificacao';
     const linkDownload = 'files/telecom/geodados/edificacao';
     
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
        $this->logDados['modulo'] = 'Telecom - edificação';
        $this->logDados['funcao'] = $_SERVER['REDIRECT_QUERY_STRING'];
        
        $this->load->library('Crud', '', 'crud');
        $this->load->model('base/crud_model','crudModel');
        $this->load->model('ura/ura_model','ura');
        $this->load->model('edificacao_model','edificacao');
        $this->load->model('tcom-node/node_model','node');
        $this->load->model('email/email_model','emailModel');
        
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
            $this->layout->region('corpo', 'view_principal');
        
        }else{
            
            $dados['menuLateral'] = false;
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
        $this->layout->region('menu_lateral', 'tcom/view_menu_lateral', $dados);
        
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    public function pesq(){
        
        #$this->output->enable_profiler(TRUE);
        $mostra_por_pagina = 30;
        $this->crud->ini();
        $par = $this->crud->getParMetodo();
        list($post, $sort_by, $sort_order, $pagina) = $par;
        
        $resultado = $this->edificacao->pesquisa($post, $mostra_por_pagina, $sort_by, $sort_order, $pagina);
        
        $postEncode = (!$post)? 0: $this->util->base64url_encode($post); 
        #$postEncode = $this->util->base64url_encode($post); 

        $crud = $this->crud->listarManual($resultado, $mostra_por_pagina, $postEncode, $sort_by, $sort_order, $pagina);
        $crud['permissor'] = $this->dadosBanco->unidade();
        $crud['nodes'] = $this->node->nodes();
        $crud['perReenvioVist'] = self::perReenvioVist;
        $crud['perImprimir'] = self::perImprimir;
        $crud['perEditarCadastrar'] = self::perCadEdit;
        $crud['perExcluir'] = self::perDeletar;
 
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
        
        $tem = false;
        
        if($id){
            
            $dados = $this->crudModel->dadosId($id);
            
            if($dados){
                $tem = true;
            }
            
            $campos = array_keys($dados);
            
            foreach($campos as $campo){
             
				$dados[$campo] = $dados[$campo]; # ALIMENTA OS CAMPOS COM OS DADOS
			}
            
        }else{
            
            $campos = $this->crudModel->camposTabela();
            
            foreach($campos as $campo){
                $dados[$campo] = false;
            }
            
        }
        
        $dados['tem'] = $tem;
        $dados['aval'] = $this->edificacao->aval();
        $dados['permissor'] = $this->edificacao->permissorTemNode();
        $dados['estado'] = $this->dadosBanco->estado();
        $dados['nodes'] = $this->node->nodes();
        $dados['dirDownload'] = self::linkDownload;
        $dados['perMudarStatus'] = self::perMudarStatus;
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
        
        $this->load->helper("datas");
        $this->crudModel->setTabela(BANCO_TELECOM.'.'.self::tabela);
        $this->crudModel->setCampoId('id');
        $this->crudModel->setTextArea(array('referencia', 'observacao', 'anexo'));
        $this->crudModel->setCamposIgnorados(array('anexoOrigem'));
        
        array_pop($_POST);
        $inicio = $this->util->formataData($this->input->post('inicio'), 'USA');
        $data = date('Y-m-d', strtotime("+3 days",strtotime($inicio))); 
        $_POST['previsao'] = proximoDiaUtil($data);

        if($this->input->post('id')){

            try{
                
                $this->anexaArquivo();
                
                if($this->input->post('concluido') == 'NAO'){
                    $_POST['conclusao'] = '';
                }
                
                $status = $this->crudModel->atualiza();
                $this->logDados['descricao'] = 'Telecom - Edificação - Atualiza edificação ('.$this->input->post('id').')';
                $this->logDados['idAcao'] = $this->input->post('id');
                $this->logDados['acao'] = 'UPDATE';
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
            $this->logGeral->grava($this->logDados);
            
            if($status){
                
                if($this->input->post('concluido') == 'NAO'){
                    $this->envioEmailOrdemPendente($this->input->post('id'), 'Atualizada');
                }else{
                    $this->envioEmailOrdemConcluida($this->input->post('id'));
                }
                
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Edifica&ccedil;&atilde;o salva com sucesso!</strong></div>');
            }else{
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao salvar edifica&ccedil;&atilde;o, caso o erro persiste comunique o administrador!</div>');
            }
            redirect(self::modulo.'/'.self::controller.'/ficha/'.$this->input->post('id'));
            
        }else{

            try{
                
                $mesSigla = $this->util->mesSigla(date('m')).date('y');
                $_POST['controle'] = $mesSigla.'-'.$this->edificacao->proximoControle($mesSigla);
                $status = $this->crudModel->insereMysql();
                $this->logDados['descricao'] = 'Telecom - Edificação - Cadastra edificação ('.$status.')';
                $this->logDados['idAcao'] = $status;
                $this->logDados['acao'] = 'INSERT';
                
                
                
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
            $this->logGeral->grava($this->logDados);
            
            $_POST['id'] = $status;
            
            if($status){
                
                $this->envioEmailOrdemPendente($this->input->post('id'), 'Cadastrada');
                
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Edifica&ccedil;&atilde;o salvo com sucesso!</strong></div>');
            }else{
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao salvar edifica&ccedil;&atilde;o, caso o erro persiste comunique o administrador!</div>');
            }
            redirect(self::modulo.'/'.self::controller.'/ficha/'.$this->input->post('id'));
            
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
            $config['allowed_types'] = 'pdf';
    		#$config['allowed_types'] = '*';
    		$config['max_size'] = '50000';
    		#$config['max_width'] = '0';
    		#$config['max_height'] = '0';
    		#$config['encrypt_name'] = true;
            $file_name = $this->input->post('controle');
            $config['file_name'] = $file_name;
            
            $statusUpload = $this->util->uploadArquivo('anexo', $config);
            
            if($statusUpload['status'] === true){ #echo '<pre>'; print_r($statusUpload['arquivo']); exit();
                $_POST['anexo'] = $statusUpload['arquivo']['file_name'];
            }else{echo 'nao subiu'; exit();
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
    
    public function envioEmailOrdemConcluida($id){
     
        # Usuários de rede interna pela unidade informada
        $usuario = $this->emailModel->usuarioEnviaEmail(2,$this->input->post('cd_unidade'));
        $dados = $this->edificacao->dadosEdificacao($id);
        
        $nomeDe = 'Sim TV - Ferramenta de negocios | Telecom - Edificação';
        $emailDe = 'naoresponda@simtv.com.br';
        $titulo = utf8_decode('Controle de Geodados - Contrato n° '.$dados->contrato.' - Operação: '.$dados->sigla_estado.' - '.$dados->unidade);
        $para = 'equipe.sistemas@simtv.com.br';
        $msg = utf8_decode('<strong>Vistoria Concluída</strong><br><br>').$titulo;
        
        foreach($usuario as $usu){
            
            $this->util->enviaEmail($nomeDe, $emailDe, $usu->email_usuario, $titulo, $msg);
        
        }
        
    }
    
    public function envioEmailOrdemPendente($id, $tipo = 'Cadastrada'){
        
        $ordemPdf = $this->geraOrdemPDF($id);
        
        # Usuários de rede interna pela unidade informada
        $usuario = $this->emailModel->usuarioEnviaEmail(1,$this->input->post('cd_unidade'));
        
        $dados = $this->edificacao->dadosEdificacao($id);
        $dados->tipo = $tipo; 
        $dados->email = 'sim';
        $dados->pdf = 'nao';
        
        $nomeDe = 'Sim TV - Ferramenta de negocios | Telecom - Edificação';
        $emailDe = 'naoresponda@simtv.com.br';
        $titulo = utf8_decode('Controle de Geodados - Contrato n° '.$dados->contrato.' - Operação: '.$dados->sigla_estado.' - '.$dados->unidade);
        $para = 'equipe.sistemas@simtv.com.br';
        $msg = "<strong>Vistoria ".$tipo.'</strong> - Segue em anexo<br><br>'.$titulo;
        
        foreach($usuario as $usu){
            
            $this->util->enviaEmail($nomeDe, $emailDe, $usu->email_usuario, $titulo, $msg, $ordemPdf);
        
        }
        
        $this->util->apagaArquivo('./temp/'.$ordemPdf.'.pdf');
        
    }
    
    public function imprimirOrdem($id){
        
        $id = $this->util->base64url_decode($id);
        
        $dados = $this->edificacao->dadosEdificacao($id);
        $dados->email = 'nao';
        $dados->pdf = 'nao';
        $this->load->view(self::pastaView.'/view_imprimir', $dados);
        
    }
    
    public function geraOrdemPDF($id){
        
        $dados = $this->edificacao->dadosEdificacao($id);
        $dados->pdf = 'sim';
        $html = utf8_encode($this->load->view(self::pastaView.'/view_imprimir', $dados, true));
        //this the the PDF filename that user will get to download
		$pdfFilePath = "ordem.pdf";
        
        //load mPDF library
		$this->load->library('m_pdf');
        
        $stylesheet1 = file_get_contents('./assets/css/bootstrap.css');
        $this->m_pdf->pdf->WriteHTML($stylesheet1,1);
        
       //generate the PDF from the given html
		$this->m_pdf->pdf->WriteHTML($html);

        //download it.
		#$this->m_pdf->pdf->Output($pdfFilePath, "D");	
        #$this->m_pdf->pdf->Output();
        $ordem = date("YmdHis").rand(1000,9999);
        $this->m_pdf->pdf->Output("./temp/".$ordem.".pdf");
        
        return $ordem;
        
    }
    
    public function reEnviarOrdemEmail($id){
        
        $id = $this->util->base64url_decode($id);
        
        $ordemPdf = $this->geraOrdemPDF($id);
        
        $dados = $this->edificacao->dadosEdificacao($id);
        
        if(!$dados){
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Vistoria n&atilde;o localizada ou incorreta!</strong></div>');
        
            redirect(self::modulo.'/'.self::controller.'/pesq');
        }
        
        $usuario = $this->emailModel->usuarioEnviaEmail(1,$dados->cd_unidade);
        
        $nomeDe = 'Sim TV - Ferramenta de negocios | Telecom - Edificação';
        $emailDe = 'naoresponda@simtv.com.br';
        $titulo = utf8_decode('Controle de Geodados - Contrato n° '.$dados->contrato.' - Operação: '.$dados->sigla_estado.' - '.$dados->unidade);
        $para = 'equipe.sistemas@simtv.com.br';
        $msg = '<strong>Vistoria - Reenvio</strong> - Segue em anexo<br><br>'.$titulo;
        
        foreach($usuario as $usu){
            
            $this->util->enviaEmail($nomeDe, $emailDe, $usu->email_usuario, $titulo, $msg, $ordemPdf);
        
        }
        
        $this->util->apagaArquivo('./temp/'.$ordemPdf.'.pdf');
        
        $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Vistoria renviada para '.count($usuario).' usu&aacute;rios com sucesso!</strong></div>');
        
        redirect(self::modulo.'/'.self::controller.'/pesq');
        
    }
    
    public function deleta(){
        
        $this->crudModel->setTabela(BANCO_TELECOM.'.'.self::tabela);
        $this->crudModel->setCampoId('id');
        
        try{
            $status = $this->crudModel->delete();
            
            $this->logDados['descricao'] = 'Telecom - Edificação - Apaga edificação ('.$this->input->post('apg_id').')';
            $this->logDados['idAcao'] = $this->input->post('apg_id');
            $this->logDados['acao'] = 'DELETE';
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        $this->logGeral->grava($this->logDados);
        
        if($status){
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Edifica&ccedil;&atilde;o apagada com sucesso!</strong></div>');
        }else{
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao apagar edifica&ccedil;&atilde;o, caso o erro persiste comunique o administrador!</div>');
        }
        redirect(base_url(self::modulo.'/'.self::controller.'/pesq'));

    }
    
    public function importaArquivosCivisResposta(){
        # ÚLTIMA IMPORTAÇÃO - 2016-07-25 18:12:37
        # PRÓXIMA IMPORTAÇÃO A PARTIR DE - 26/07/2016
        $arquivos = array(
        'http://civis.simtv.com.br/form_viabilidade/anexos/352e20826d829e61153b02438ba15db3.pdf',
        'http://civis.simtv.com.br/form_viabilidade/anexos/d864ffda867d0ad57d781741f463d07a.pdf',
        'http://civis.simtv.com.br/form_viabilidade/anexos/dd3041dbf05eef417629e5bb8dd39856.pdf',
        'http://civis.simtv.com.br/form_viabilidade/anexos/fd7b7bbf13b38fcf94a2f53a81052b47.pdf'
        
        );
        
        foreach($arquivos as $file){
        
            #$file = 'http://civis.simtv.com.br/edificacao/arquivo/86966.pdf';
            $newfile = './files/telecom/viabilidade/resposta/antigo/'.basename($file);
            
            if (!copy($file, $newfile)) {
                echo "falha ao copiar $file...<br>";
            }else{
                echo "Copiou $file<br>";
            }
        
        }
        exit();
        
    }
    
    public function importaArquivosCivisHistorico(){
        # ÚLTIMA IMPORTAÇÃO - 2016-07-25 18:12:37
        # PRÓXIMA IMPORTAÇÃO A PARTIR DE - 26/07/2016
        $arquivos = array(
        'http://civis.simtv.com.br//form_viabilidade/arquivo/VISTORIA_INTERNA_POLITEC_-_716411-170572TVC12.xls',
        'http://civis.simtv.com.br//form_viabilidade/arquivo/RelatorioDeVistoria_842062.xls',
        'http://civis.simtv.com.br//form_viabilidade/arquivo/842062_1750TVC2014.pdf'
        );
        
        foreach($arquivos as $file){
        
            #$file = 'http://civis.simtv.com.br/edificacao/arquivo/86966.pdf';
            $newfile = './files/telecom/viabilidade/historico/antigo/'.basename($file);
            
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