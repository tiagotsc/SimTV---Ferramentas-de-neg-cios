<?php 
include_once(APPPATH.'modules/base/controllers/base.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class node extends Base {
     
     private $logDados;
     const modulo = 'tcom-node';
     const controller = 'node';
     const pastaView = 'node';
     const assunto = 'node';
     const tabela = 'sistema.tcom_node';
     const perModulo = 274;
     const perPesq = 276;
     const perCadEdit = 277;
     const perDeletar = 278;
     
    /**
     * relatorio::__construct()
     * 
     * Classe resposável por processar os relatórios
     * 
     * @return
     */
    public function __construct(){
        
		parent::__construct();
echo 'aqui'; exit();
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
        
        echo 'teste'; exit();
        
    }
    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */