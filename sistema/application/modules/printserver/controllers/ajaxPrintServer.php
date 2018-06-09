<?php

#error_reporting(0);
include_once(APPPATH . 'modules/base/controllers/base.php');
#include_once(APPPATH.'controllers/base.php');
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
#date_default_timezone_set('America/Sao_Paulo');
#setlocale(LC_ALL, 'pt_BR.UTF-8', 'Portuguese_Brazil.1252');

class AjaxPrintServer extends Base {

    public function __construct() {
        parent::__construct();

        $this->load->model('printserver/printserver_model', 'printserver');
    }

    public function dados() {

        $dados['dados']['mes'] = $this->printserver->unidadeArray($_GET['und']);
        $dados['dados']['ano'] = $this->printserver->unidadeArray($_GET['ano']);
        
        $this->load->view('view_json', $dados);
        
        
    }

}
