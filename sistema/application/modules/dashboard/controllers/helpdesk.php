<?php

include_once(APPPATH . 'modules/base/controllers/base.php');
#include_once(APPPATH.'controllers/base.php');
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Helpdesk extends Base {
    ### Controla Dashboard

    public function __construct() {

        parent::__construct();

        $this->load->model('helpdesk/helpdesk_model', 'helpdesk');
        $this->load->model('dashboard_model', 'dashboard');
    }

    ### RegistraAcesso

    public function registraAcesso() {

        $this->dashboard->gravaAcesso();
    }

    public function teste() {
//        echo '<pre>';
//        print_r($corrente);
//        echo 1;
//        echo '</pre>';
//        exit();
        echo base_url('/dashboard/helpdesk/dadosGraficoHelpDesk/');
    }

    ### Dashboard sobre Impressões

    public function index() {

        $dashboardPermitidos = $this->dashboard->dashboardPermitidos($this->session->userdata('permissoes'));
        foreach ($dashboardPermitidos as $dP) {
            $dashboard[] = $dP->cd_grafico;
        }

        $dados['dashboardPermitidos'] = $dashboard;

        $this->layout->region('html_header', 'view_html_header');

        if (in_array(363, $this->session->userdata('permissoes'))) {
            $this->layout->region('corpo', 'helpdesk/view_helpdesk_abertos', $dados);
        } else {
            $this->layout->region('corpo', 'view_permissao');
        }

        if (in_array(363, $this->session->userdata('permissoes'))) {
            $this->layout->region('menu_lateral', 'helpdesk/view_menu_helpdesk');
        } else {
            $this->layout->region('corpo', 'view_permissao');
        }

        $this->layout->region('rodape', 'view_rodape');
        $this->layout->region('html_footer', 'view_html_footer');

        // Então chama o layout que irá exibir as views parciais...
        $this->layout->show('layout');
    }

    public function concluidos_area() {
        $dashboardPermitidos = $this->dashboard->dashboardPermitidos($this->session->userdata('permissoes'));
        foreach ($dashboardPermitidos as $dP) {
            $dashboard[] = $dP->cd_grafico;
        }

        $dados['dashboardPermitidos'] = $dashboard;

        $this->layout->region('html_header', 'view_html_header');

        if (in_array(363, $this->session->userdata('permissoes'))) {
            $this->layout->region('corpo', 'helpdesk/view_helpdesk_concluidos_area', $dados);
        } else {
            $this->layout->region('corpo', 'view_permissao');
        }

        if (in_array(363, $this->session->userdata('permissoes'))) {
            $this->layout->region('menu_lateral', 'helpdesk/view_menu_helpdesk');
        } else {
            $this->layout->region('corpo', 'view_permissao');
        }

        $this->layout->region('rodape', 'view_rodape');
        $this->layout->region('html_footer', 'view_html_footer');

        // Então chama o layout que irá exibir as views parciais...
        $this->layout->show('layout');
    }

    public function concluidos_tecnico() {
        $dashboardPermitidos = $this->dashboard->dashboardPermitidos($this->session->userdata('permissoes'));
        foreach ($dashboardPermitidos as $dP) {
            $dashboard[] = $dP->cd_grafico;
        }

        $dados['dashboardPermitidos'] = $dashboard;

        $this->layout->region('html_header', 'view_html_header');

        if (in_array(363, $this->session->userdata('permissoes'))) {
            $this->layout->region('corpo', 'helpdesk/view_helpdesk_concluidos_tecnico', $dados);
        } else {
            $this->layout->region('corpo', 'view_permissao');
        }

        if (in_array(363, $this->session->userdata('permissoes'))) {
            $this->layout->region('menu_lateral', 'helpdesk/view_menu_helpdesk');
        } else {
            $this->layout->region('corpo', 'view_permissao');
        }

        $this->layout->region('rodape', 'view_rodape');
        $this->layout->region('html_footer', 'view_html_footer');

        // Então chama o layout que irá exibir as views parciais...
        $this->layout->show('layout');
    }

    public function concluidos_unidade() {
        $dashboardPermitidos = $this->dashboard->dashboardPermitidos($this->session->userdata('permissoes'));
        foreach ($dashboardPermitidos as $dP) {
            $dashboard[] = $dP->cd_grafico;
        }

        $dados['dashboardPermitidos'] = $dashboard;

        $this->layout->region('html_header', 'view_html_header');

        if (in_array(363, $this->session->userdata('permissoes'))) {
            $this->layout->region('corpo', 'helpdesk/view_helpdesk_concluidos_unidade', $dados);
        } else {
            $this->layout->region('corpo', 'view_permissao');
        }

        if (in_array(363, $this->session->userdata('permissoes'))) {
            $this->layout->region('menu_lateral', 'helpdesk/view_menu_helpdesk');
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
        $dados['ANO'] = $this->dadosHelpDeskComparativoArray();

        $this->layout->region('html_header', 'view_html_header');

        if (in_array(363, $this->session->userdata('permissoes'))) {
            $this->layout->region('corpo', 'helpdesk/view_helpdesk_comparativo', $dados);
        } else {
            $this->layout->region('corpo', 'view_permissao');
        }

        if (in_array(363, $this->session->userdata('permissoes'))) {
            $this->layout->region('menu_lateral', 'helpdesk/view_menu_helpdesk');
        } else {
            $this->layout->region('corpo', 'view_permissao');
        }

        $this->layout->region('rodape', 'view_rodape');
        $this->layout->region('html_footer', 'view_html_footer');

        // Então chama o layout que irá exibir as views parciais...
        $this->layout->show('layout');
    }

    public function dadosGraficoHelpDesk() {

        $dadosChamadosAberto = $this->helpdesk->chamadosEmAberto();
        //var_dump($dadosChamadosAberto);

        echo json_encode($dadosChamadosAberto);
    }

    public function dadosHelpDeskConcluidoArea($data1, $data2) {

        $dadosChamadosAberto = $this->helpdesk->chamadosConcluidosArea($data1, $data2);
        //var_dump($dadosChamadosAberto);

        echo json_encode($dadosChamadosAberto);
    }

    public function dadosHelpDeskConcluidoUnidade($data1, $data2) {

        $dadosChamadosAberto = $this->helpdesk->chamadosConcluidosUnidade($data1, $data2);
        //var_dump($dadosChamadosAberto);

        echo json_encode($dadosChamadosAberto);
    }

    public function dadosHelpDeskConcluidoTecnico($data1, $data2) {

        $dadosChamadosAberto = $this->helpdesk->chamadosConcluidosTecnico($data1, $data2);
        //var_dump($dadosChamadosAberto);

        echo json_encode($dadosChamadosAberto);
    }

    public function dadosHelpDeskComparativo($data1, $data2) {

        $dadosChamadosAberto = $this->helpdesk->chamadosComparativo($data1, $data2);
        //var_dump($dadosChamadosAberto);

        echo json_encode($dadosChamadosAberto);
    }

    public function dadosHelpDeskComparativoArray() {
        return $this->helpdesk->chamadosComparativo($data1, $data2, TRUE);

//        echo '<pre>';
//        print_r($arrayOptions);
//        echo '</pre>';
//        exit();
//        foreach ($arrayOptions as $ar) {
//            echo $ar->ANO;            echo '<br>';
//        }
//        exit();
    }

}
