<?php 
include_once(APPPATH.'modules/base/controllers/base.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class teste extends Base {
     
     private $logDados;
     const modulo = 'teste';
     const controller = 'teste';
     const pastaView = 'teste';
     const tabela = 'tcom_interface';
     const assunto = 'Teste';
     const modelAssunto = 'teste';
     const perModulo = 274;
     const perPesq = 304;
     const perCadEdit = 305;
     const perDeletar = 306;
     
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
        
        $this->util->setPositionMenu('');
        
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
        set_time_limit(0);
        ini_set('memory_limit','100M');
        
        $dir = './temp/grafica';
        $arquivos = $this->util->buscaArquivosDiretorios($dir);
        $cont = 1;
        foreach($arquivos as $arq){
            
            $handle = file($arq);
            foreach($handle as $han){
                #echo $han; echo '<br>';
                
                #if(substr($han, 0,1) == 'C'){
                    #echo utf8_encode($han); echo '<br>';
                #}
                
                $sql = "INSERT INTO teste.arquivo(linha) VALUES('".trim(utf8_encode(addslashes($han)))."')";
                #echo $sql; echo '<br>';
                $this->db->query($sql);
            }
            echo $cont++.'<br>';
        }
        
        #echo '<pre>'; print_r($arquivos); exit();
        
    }

    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */