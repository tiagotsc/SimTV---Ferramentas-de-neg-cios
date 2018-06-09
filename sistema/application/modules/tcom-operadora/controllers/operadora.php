<?php 
include_once(APPPATH.'modules/base/controllers/base.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class operadora extends Base {
     
     private $logDados;
     const modulo = 'tcom-operadora';
     const controller = 'operadora';
     const pastaView = 'operadora';
     const tabela = 'tcom_oper';
     const assunto = 'Operadora (Ponto A)';
     const modelAssunto = 'toperadora';
     const perModulo = 274;
     const perPesq = 316;
     const perCadEdit = 317;
     const perDeletar = 318;
     
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
        $this->load->model('operadora_model','toperadora');
        
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
        $crud['perEditarCadastrar'] = self::perCadEdit;
        $crud['perExcluir'] = self::perDeletar;
        $crud['assunto'] = self::assunto;
 
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
             
				$dados[$campo] = $dadosRes[$campo]; # ALIMENTA OS CAMPOS COM OS DADOS
			}
            
            # DADOS INSTALAÇÃO
            $dadosRes = $this->crudModel->dadosIDSimples(BANCO_TELECOM.'.tcom_oper_inst','idOper',$id);
            $campos = array_keys($dadosRes);
            
            foreach($campos as $campo){
                if(!in_array($campo, array('id', 'idOper','data_cadastro'))){
				    $dados[$campo] = $dadosRes[$campo]; # ALIMENTA OS CAMPOS COM OS DADOS
                }
			}
            
            # DADOS COBRANÇA
            $dadosRes = $this->crudModel->dadosIDSimples(BANCO_TELECOM.'.tcom_oper_cobr','idOper',$id);
            $campos = array_keys($dadosRes);
            
            $idCobr = 0;
            
            foreach($campos as $campo){
                if(!in_array($campo, array('idOper','data_cadastro'))){
                    if($campo == 'id'){
                        $idCobr = $dadosRes[$campo];
                        $dados['idCob'] = $dadosRes[$campo];
                    }else{
				        $dados[$campo.'_cob'] = $dadosRes[$campo]; # ALIMENTA OS CAMPOS COM OS DADOS
                    }
                
                }
			}

            # TELEFONES COBRANÇA
            $dados['telefones'] = $this->$modelAssunto->telefonesCobranca($idCobr);
            
        }else{
            
            # DADOS BASE
            $campos = $this->crudModel->camposTbSimples(BANCO_TELECOM.'.'.self::tabela);
            
            foreach($campos as $campo){
                $dados[$campo] = '';
            }
            
            # DADOS INSTALAÇÃO
            $campos = $this->crudModel->camposTbSimples(BANCO_TELECOM.'.tcom_oper_inst');
            
            foreach($campos as $campo){
                $dados[$campo] = '';
            }
            
            # DADOS COBRANÇA
            $campos = $this->crudModel->camposTbSimples(BANCO_TELECOM.'.tcom_oper_cobr');
            
            foreach($campos as $campo){
                $dados[$campo.'_cob'] = '';
            }
            
            $dados['idCob'] = false;
            $dados['telefones'] = false;
            
        }
        
        $dados['assunto'] = self::assunto;
        $dados['modulo'] = self::modulo; 
        $dados['controller'] = self::controller; 
        $dados['estado'] = $this->dadosBanco->estado(); 
        $dados['unidade'] = $this->dadosBanco->unidade(); 
        $dados['pais'] = $this->$modelAssunto->pais($id); 

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
        
        array_pop($_POST);
        
        if($this->input->post('id')){
            
            try{
            
                $status = $this->$modelAssunto->atualiza();
                $this->logDados['descricao'] = ucfirst(self::modulo).' - '.self::assunto.' - Atualiza '.self::assunto.' ('.$this->input->post('id').')';
                $this->logDados['acao'] = 'UPDATE';
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
            $this->logGeral->grava($this->logDados);
            
            if($status){
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>'.self::assunto.' salva com sucesso!</strong></div>');
            }else{
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger"><strong>Erro ao salvar '.strtolower(self::assunto).', caso o erro persiste comunique o administrador!</strong></div>');
            }
            redirect(base_url(self::modulo.'/'.self::controller.'/ficha/'.$this->input->post('id'))); 
            
        }else{
            
            try{
            
                $status = $this->$modelAssunto->insere();
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