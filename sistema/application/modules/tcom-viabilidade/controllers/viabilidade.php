<?php 
include_once(APPPATH.'modules/base/controllers/base.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class viabilidade extends Base {
     
     private $logDados;
     const modulo = 'tcom-viabilidade';
     const controller = 'viabilidade';
     const pastaView = 'viabilidade';
     const tabela = 'tcom_viab';
     const assunto = 'Viabilidade';
     const modelAssunto = 'viabilidade';
     const perModulo = 274;
     const perPesq = 332;
     const perImprimir = 333;
     const perCadEdit = 334;
     const perDeletar = 335;
     const perPendCadResp = 336;
     const emailEnviaPedido = 4; 
     
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
        $this->load->model('viabilidade_model','viabilidade', self::modelAssunto);
        $this->load->model('tcom-taxa-digital/taxaDigital_model','taxaDigital');
        $this->load->model('tcom-interface/tinterface_model','tinterface');
        $this->load->model('tcom-cliente/cliente_model','cliente');
        $this->load->model('tcom-operadora/operadora_model','operadora');
        $this->load->model('tcom-contrato/contrato_model','contrato');
        
        $this->util->setPositionMenu('');
        $this->menuLateral = $this->util->montaMenuLateral($this->dadosBanco->menuLateralDropDown('TELECOM', $this->session->userdata('permissoes')), $this->dadosBanco->paisMenuLateralDropDown('TELECOM', $this->session->userdata('permissoes')));
        
	} 
     
	/**
     * Telefonia::index()
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
        
        $this->load->helper("texto");
        
        $mostra_por_pagina = 30;
        $modelAssunto = self::modelAssunto;
        
        $this->crud->ini();
        $par = $this->crud->getParMetodo();
        list($post, $sort_by, $sort_order, $pagina) = $par;
        
        $resultado = $this->$modelAssunto->pesquisa($post, $mostra_por_pagina, $sort_by, $sort_order, $pagina);
        $resultado['tabela'] = self::assunto;
        
        $postEncode = (!$post)? 0: $this->util->base64url_encode($post); 
        
        $crud = $this->crud->listarManual($resultado, $mostra_por_pagina, $postEncode, $sort_by, $sort_order, $pagina);
        $crud['perImprimir'] = self::perImprimir;
        $crud['perEditarCadastrar'] = self::perCadEdit;
        $crud['perPendCadResp'] = self::perPendCadResp;
        $crud['perExcluir'] = self::perDeletar;
        $crud['assunto'] = self::assunto;
        $crud['unidade'] = $this->dadosBanco->unidade();
        $crud['tiposViab'] = $this->$modelAssunto->tiposViabilidade();
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
        
        if($id){
            # DADOS BASE
            $dadosRes = $this->crudModel->dadosIDSimples(BANCO_TELECOM.'.'.self::tabela,'id',$id);
            $campos = array_keys($dadosRes);
            
            foreach($campos as $campo){
             
				$dados[$campo] = $dadosRes[$campo]; 
			}
            
            if($dados['idViabTipo'] == 5){
                # DADOS NOVO ENDEREÇO
                $dadosRes = $this->crudModel->dadosIDSimples(BANCO_TELECOM.'.tcom_viab_md_end','idViab',$id);
                $campos = array_keys($dadosRes);
                
                $idMdEnd = 0;
                
                foreach($campos as $campo){
                    if(!in_array($campo, array('idViab','data_cadastro'))){
                        if($campo == 'id'){
                            $idMdEnd = $dadosRes[$campo];
                        }else{
    				        $dados[$campo] = $dadosRes[$campo]; 
                        }
                    
                    }
    			}
                $dados['idMdEnd'] = $idMdEnd;
                # TELEFONES NOVO ENDEREÇO
                $dados['telefones'] = $this->$modelAssunto->telefonesMdEndereco($idMdEnd);
            }else{
                # CAMPOS NOVO ENDEREÇO
                $campos = $this->crudModel->camposTbSimples(BANCO_TELECOM.'.tcom_viab_md_end');
                foreach($campos as $campo){
                    if($campo != 'id'){
                        $dados[$campo] = '';
                    }
                }
                
                $dados['idMdEnd'] = false;
                $dados['telefones'] = false;
                
            }
            

        }else{
            
            # CAMPOS BASE
            $campos = $this->crudModel->camposTbSimples(BANCO_TELECOM.'.'.self::tabela);
            
            foreach($campos as $campo){
                $dados[$campo] = '';
            }
            
            # CAMPOS NOVO ENDEREÇO
            $campos = $this->crudModel->camposTbSimples(BANCO_TELECOM.'.tcom_viab_md_end');
            
            foreach($campos as $campo){
                $dados[$campo] = '';
            }
            
            $dados['idMdEnd'] = false;
            $dados['telefones'] = false;
            
        }
        $dados['titulo'] = '';
        $dados['assunto'] = self::assunto;
        $dados['modulo'] = self::modulo; 
        $dados['controller'] = self::controller; 
        $dados['estado'] = $this->dadosBanco->estado(); 
        $dados['unidade'] = $this->dadosBanco->unidade(); 
        $dados['operadoras'] = $this->operadora->operadoras('filho');
        $dados['clientes'] = $this->cliente->clientes();
        $dados['tiposViab'] = $this->viabilidade->tiposViabilidade();
        $dados['taxas'] = $this->taxaDigital->taxas();
        $dados['interfaces'] = $this->tinterface->interfaces();
        $dados['contratos'] = $this->contrato->contratos(false, 'A');

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
        array_pop($_POST);
        $this->load->helper("datas");
        $inicio = $this->util->formataData($this->input->post('dt_solicitacao'), 'USA');
        $data = date('Y-m-d', strtotime("+3 days",strtotime($inicio))); 
        $_POST['dt_prazo'] = proximoDiaUtil($data);
        $modelAssunto = self::modelAssunto;
        
        if($this->input->post('id')){
            
            try{
            
                $status = $this->$modelAssunto->atualiza();
                $this->logDados['idAcao'] = $this->input->post('id');
                $this->logDados['descricao'] = ucfirst(self::modulo).' - '.self::assunto.' - Atualiza '.self::assunto.' ('.$this->input->post('id').')';
                $this->logDados['acao'] = 'UPDATE';
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
            $this->logGeral->grava($this->logDados);
            
            if($status){
                
                $this->envioEmail($this->input->post('id'), 'Atualiza');
                
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>'.self::assunto.' salva com sucesso!</strong></div>');
            }else{
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger"><strong>Erro ao salvar '.strtolower(self::assunto).', caso o erro persiste comunique o administrador!</strong></div>');
            }
            redirect(base_url(self::modulo.'/'.self::controller.'/ficha/'.$this->input->post('id'))); 
            
        }else{
            
            try{
                
                $mesSigla = 'VIAB-'.$this->util->mesSigla(date('m')).date('y');
                $_POST['controle'] = $mesSigla.'-'.$this->$modelAssunto->proximoControle($mesSigla);
                
                $status = $this->$modelAssunto->insere();
                $this->logDados['idAcao'] = $status;
                $this->logDados['descricao'] = ucfirst(self::modulo).' - '.self::assunto.' - Cadastra '.self::assunto.' ('.$status.')';
                $this->logDados['acao'] = 'INSERT';
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
            $this->logGeral->grava($this->logDados);
            
            $_POST['id'] = $status;
            
            if($status){
                
                $this->envioEmail($this->input->post('id'), 'Cadastrada');
                
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>'.self::assunto.' salva com sucesso!</strong></div>');
            }else{
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao salvar '.strtolower(self::assunto).', caso o erro persiste comunique o administrador!</div>');
            }
            redirect(base_url(self::modulo.'/'.self::controller.'/ficha/'.$this->input->post('id'))); 
            
        }
        
    }
    
    public function envioEmail($id, $tipo = 'Cadastrada'){
        
        $modelAssunto = self::modelAssunto;
        $dados = $this->$modelAssunto->dadosViabilidade($id); 
        $dados['tipo'] = $tipo; 
        $dados['email'] = 'sim';
        
        if($dados['viabilidade']->id_tipo == 5){
            $dados['clienteEnd'] = $dados['mdEnd'];
            $dados['clienteEndTel'] = $dados['mdEndTel'];
        }
        
        $ordemPdf = $this->geraOrdemPDF(false, $dados);
        
        # Usuários de rede interna pela unidade informada
        $usuario = $this->emailModel->usuarioEnviaEmail(self::emailEnviaPedido, $this->input->post('cd_unidade'));
        
        $nomeDe = 'Sim TV - Ferramenta de negocios | Telecom - '.self::assunto;
        $emailDe = 'naoresponda@simtv.com.br';
        $titulo = utf8_decode('Controle de Telecom - '.self::assunto.' - '.$dados['viabilidade']->controle);
        $para = 'equipe.sistemas@simtv.com.br';
        $msg = utf8_decode("<strong>Análise de Viabilidade ").$tipo.'</strong> - Segue em anexo<br><br>'.$titulo;
        
        foreach($usuario as $usu){
            
            $this->util->enviaEmail($nomeDe, $emailDe, $usu->email_usuario, $titulo, $msg, $ordemPdf);
            
        }
        
        $this->util->apagaArquivo('./temp/'.$ordemPdf.'.pdf');
        
    }
    
    public function geraOrdemPDF($id, $dados = false){
        
        $modelAssunto = self::modelAssunto;
        $id = $this->util->base64url_decode($id);
        if(!$dados){
            $dados = $this->$modelAssunto->dadosViabilidade($id); 
        }
        $dados['pdf'] = 'sim';
        $html = utf8_encode($this->load->view('viabilidade/view_imprimir', $dados, true));
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
    
    public function imprimirOrdem($id, $dados = false){
        
        $modelAssunto = self::modelAssunto;
        $id = $this->util->base64url_decode($id);
        if(!$dados){
            $dados = $this->$modelAssunto->dadosViabilidade($id); 
        }

        if($dados['viabilidade']->id_tipo == 5){
            $dados['clienteEnd'] = $dados['mdEnd'];
            $dados['clienteEndTel'] = $dados['mdEndTel'];
        }
        
        $dados['email'] = 'nao';
        $dados['pdf'] = 'nao';
        $this->load->view('viabilidade/view_imprimir', $dados);
        
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
    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */