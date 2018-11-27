<?php

include_once(APPPATH . 'modules/base/controllers/base.php');
#include_once(APPPATH.'controllers/base.php');
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class printserver extends Base {
    ### Controla Dashboard

    public function __construct() {

        parent::__construct();

        $this->load->model('dashboard_model', 'dashboard');
        $this->load->model('printserver/printserver_model', 'printserver');
    }

    ### RegistraAcesso

    public function registraAcesso() {

        $this->dashboard->gravaAcesso();
    }

    public function teste() {
        $this->load->model('dashboard_model', 'dashboard');
        $dashboardPermitidos = $this->dashboard->dashboardPermitidos($this->session->userdata('permissoes'));
        echo '<pre>';
        print_r($dashboardPermitidos);
//        echo 1;
        echo '</pre>';
        exit();
    }

    ### Dashboard sobre Impressões

    public function index() {
        $dashboardPermitidos = $this->dashboard->dashboardPermitidos($this->session->userdata('permissoes'));
        foreach ($dashboardPermitidos as $dP) {
            $dashboard[] = $dP->cd_grafico;
        }

        $dados['dashboardPermitidos'] = $dashboard;

        $this->layout->region('html_header', 'view_html_header');
        if (in_array(400, $this->session->userdata('permissoes'))) {
            $this->layout->region('corpo', 'printserver/view_printserver', $dados);
        } else {
            $this->layout->region('corpo', 'view_permissao');
        }
        if (in_array(400, $this->session->userdata('permissoes'))) {
            $this->layout->region('menu_lateral', 'printserver/view_menu_printserver');
        } else {
            $this->layout->region('corpo', 'view_permissao');
        }
        $this->layout->region('rodape', 'view_rodape');
        $this->layout->region('html_footer', 'view_html_footer');

        // Então chama o layout que irá exibir as views parciais...
        $this->layout->show('layout');
    }

    public function detalhado() {
        $dashboardPermitidos = $this->dashboard->dashboardPermitidos($this->session->userdata('permissoes'));
        foreach ($dashboardPermitidos as $dP) {
            $dashboard[] = $dP->cd_grafico;
        }

        $dados['dashboardPermitidos'] = $dashboard;
        $dados['undArray'] = $this->printserver->unidadeArray();
        
        $this->layout->region('html_header', 'view_html_header');
        if (in_array(400, $this->session->userdata('permissoes'))) {
            $this->layout->region('corpo', 'printserver/view_printserver_detalhado', $dados);
        } else {
            $this->layout->region('corpo', 'view_permissao');
        }
        if (in_array(400, $this->session->userdata('permissoes'))) {
            $this->layout->region('menu_lateral', 'printserver/view_menu_printserver');
        } else {
            $this->layout->region('corpo', 'view_permissao');
        }
        $this->layout->region('rodape', 'view_rodape');
        $this->layout->region('html_footer', 'view_html_footer');

        // Então chama o layout que irá exibir as views parciais...
        $this->layout->show('layout');
    }

    public function comparativo() {
        $dashboardPermitidos = $this->dashboard->dashboardPermitidos($this->session->userdata('permissoes'));
        foreach ($dashboardPermitidos as $dP) {
            $dashboard[] = $dP->cd_grafico;
        }

        $dados['dashboardPermitidos'] = $dashboard;

        $this->layout->region('html_header', 'view_html_header');
        if (in_array(400, $this->session->userdata('permissoes'))) {
            $this->layout->region('corpo', 'printserver/view_printserver_comparativo', $dados);
        } else {
            $this->layout->region('corpo', 'view_permissao');
        }
        if (in_array(400, $this->session->userdata('permissoes'))) {
            $this->layout->region('menu_lateral', 'printserver/view_menu_printserver');
        } else {
            $this->layout->region('corpo', 'view_permissao');
        }
        $this->layout->region('rodape', 'view_rodape');
        $this->layout->region('html_footer', 'view_html_footer');

        // Então chama o layout que irá exibir as views parciais...
        $this->layout->show('layout');
    }

    public function dadosGraficoPrintServer($data1, $data2) {
        $dadosPrintServer = $this->printserver->acompanhamentoGeral($data1, $data2);
        echo json_encode($dadosPrintServer);
    }

    public function dadosPrintServerDetalhado($und, $ano, $mes) {
        $dadosPrintServer = $this->printserver->acompanhamentoDetalhado($und, $ano, $mes);
        echo json_encode($dadosPrintServer);
    }

    public function dadosPrintServerDetalhadoArray($und = NULL) {

        return $this->printserver->unidadeArray($und);

        //        $arrayOptions = $this->printserver->unidadeArray();
//
//        echo '<pre>';
//        print_r($arrayOptions);
//        echo '</pre>';
//        exit();
//        foreach ($arrayOptions as $ar) {
//            echo $ar->DEPARTAMENTO;
//            echo '<br>';
//        }
//        exit();
    }

}
