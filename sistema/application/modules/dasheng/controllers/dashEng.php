<?php 
include_once(APPPATH.'modules/base/controllers/base.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class dashEng extends Base {
    
    private $logDados;
    const modulo = 'rh';
    const controller = 'rh';
    const tabela = 'sistema.tcom_interface';
    const assunto = 'Interface';
    const modelAssunto = 'tinterface';
    const perModulo = 401;
    
    public function __construct(){
        
        parent::__construct();
        
        $this->load->helper('eng_helper');
        $this->load->model('tbase_model','tbase');
        $this->load->model('tchart_model','tchar');
        
        $this->util->setPositionMenu('');
        $this->menuLateral = $this->util->montaMenuLateral($this->dadosBanco->menuLateralDropDown('RH', $this->session->userdata('permissoes')), $this->dadosBanco->paisMenuLateralDropDown('RH', $this->session->userdata('permissoes')));
        
//        echo '<pre>';
//        print_r($this->menuLateral);
//        echo '</pre>';
//        exit();
        
    }
    
    function index(){
        
        $data = $this->tchar->currmonth2;
        $this->layout->region('html_header', 'view_html_header');

        $this->layout->region('corpo', 'dash_view', $data);

        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    public function teste(){

//        $x = $this->tbase->test();
//        $y = $this->tbase->sim_imb_meta;
//        var_dump($y);exit();
        $x = $this->tchar->imbChart('2016-08');
        $p = "";
        
        foreach ($x['dados'] as $a){
            $p[] = floatval($a['y']).",".floatval($a['meta']);
        }
        
        echo json_encode($p);exit();
        
        imprimeVetor($p); 
   }
    
}