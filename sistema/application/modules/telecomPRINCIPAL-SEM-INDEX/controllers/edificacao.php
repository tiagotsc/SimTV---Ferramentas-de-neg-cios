<?php 
include_once(APPPATH.'modules/base/controllers/base.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class edificacao extends Base {
     
     private $logDados;
     private $perModulo = 274;
     private $perPesq = 282;
     private $perReenvioVist = 299;
     private $perCadEdit = 283;
     private $perImprimir = 284;
     private $perMudarStatus = 285;
     private $perDeletar = '';
     #private $dirUpload = './files/telecom/edificacao';
     #private $linkDownload = 'files/telecom/edificacao';
     private $dirUpload = './files/telecom/geodados/edificacao';
     private $linkDownload = 'files/telecom/geodados/edificacao';
     
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
        $this->load->model('node_model','node');
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
        
        if(in_array($this->perModulo, $this->session->userdata('permissoes'))){
            
            $dados['menuLateral'] = $this->menuLateral;
            $this->layout->region('corpo', 'view_principal');
        
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
    
    public function pesqEdificacao(){
        
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
        $crud['perReenvioVist'] = $this->perReenvioVist;
        $crud['perImprimir'] = $this->perImprimir;
        $crud['perEditarCadastrar'] = $this->perCadEdit;
        $crud['perExcluir'] = $this->perDeletar;
 
        $dados['menuLateral'] = $this->menuLateral;
        $this->layout->region('html_header', 'view_html_header');
        $this->layout->region('menu_lateral', 'view_menu_lateral', $dados);
        
        if(in_array($this->perPesq, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'edificacao/view_psq', $crud);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
    }
    
    public function fichaEdificacao($id = false){
        
        $this->crudModel->setTabela('sistema.tcom_edificacao');
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
        $dados['dirDownload'] = $this->linkDownload;
        $dados['perMudarStatus'] = $this->perMudarStatus;
        $menu['menuLateral'] = $this->menuLateral;
        $this->layout->region('html_header', 'view_html_header');
        $this->layout->region('menu_lateral', 'view_menu_lateral', $menu);

        if(in_array($this->perCadEdit, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'edificacao/view_frm', $dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
    }
    
    public function salvarEdificacao(){
        
        $this->load->helper("datas");
        $this->crudModel->setTabela('sistema.tcom_edificacao');
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
            redirect('telecom/edificacao/fichaEdificacao/'.$this->input->post('id'));
            
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
            redirect('telecom/edificacao/fichaEdificacao/'.$this->input->post('id'));
            
        }
      
    }
    
    public function anexaArquivo(){
        
        if($_FILES['anexo']['tmp_name']){
                    
            $apagouAnterior = false;
            if($this->input->post('anexoOrigem') != ''){
                $apagou = $this->util->apagaArquivo($this->dirUpload.'/'. $this->input->post('anexoOrigem') );
                if($apagou){
                    $apagouAnterior = true;
                }
            }
            
            $config['upload_path'] = $this->dirUpload;
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
        $this->load->view('edificacao/view_imprimir', $dados);
        
    }
    
    public function geraOrdemPDF($id){
        
        $dados = $this->edificacao->dadosEdificacao($id);
        $dados->pdf = 'sim';
        $html = utf8_encode($this->load->view('edificacao/view_imprimir', $dados, true));
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
        
            redirect('telecom/edificacao/pesqEdificacao');
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
        
        redirect('telecom/edificacao/pesqEdificacao');
        
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