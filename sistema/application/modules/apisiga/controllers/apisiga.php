<?php 
include_once(APPPATH.'modules/base/controllers/base.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class apisiga extends Base {
     
     private $logDados;
     const modulo = 'tcom-fiscal';
     const controller = 'imposto';
     const pastaView = 'imposto';
     const tabela = 'tcom_imposto';
     const assunto = 'Imposto';
     const modelAssunto = 'statushist';
     const perModulo = 274;
     const perPesq = 424;
     const perCadEdit = 425;
     const perDeletar = 426;
     
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
        $this->load->model('imposto_model',self::modelAssunto);
        $this->load->model('tcom-contrato/contrato_model','contrato');
        
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
        
        
    }
    
    public function conexao(){
        
        # Produção
        $options = array(
            'uri' => 'http://localhost:8080/server.php',
            'location' => 'http://localhost:8080/server.php'
        );
        
        # Treinamento
        /*$options = array(
            'uri' => 'http://localhost:8080/server.php',
            'location' => 'http://localhost:8080/server.php'
        );*/
        
        return $options;
        
    }
    
    public function rodaExemplo(){
        
        $options = $this->conexao();
        
        $client = new SoapClient(null, $options);
         
        // Já estamos conectados, vamos usar o método "somar" do servidor:
        var_dump($client->somar(10, 15)); // 25
        
    }
    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */