<?php

include_once(APPPATH . 'modules/base/controllers/base.php');
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class telefonia extends Base {

    public function __construct() {

        parent::__construct();

        $this->load->model('dashboard_model', 'dashboard');
        $this->load->model('asterisk-pbx/asteriskpbx_model', 'asteriskpbx');
    }

    public function registraAcesso() {

        $this->dashboard->gravaAcesso();
    }

    public function teste() {

        $colunas_db = [2001, 2002, 2003, 2004, 2005, 2006, 2007, 2008];

        $c = 0;

        foreach ($colunas_db as $coldb) {
            $formato = array(
                floor($coldb / 3600),
                floor($coldb / 60 % 60),
                floor($coldb % 60)
            );

            $convert[] = sprintf('%02d:%02d:%02d', $formato[0], $formato[1], $formato[2]);

            $c++;
        }

        echo '<pre>';
        print_r($convert);
        echo '</pre>';
    }

    ### Dashboard

    public function index() {
        $dashboardPermitidos = $this->dashboard->dashboardPermitidos($this->session->userdata('permissoes'));
        foreach ($dashboardPermitidos as $dP) {
            $dashboard[] = $dP->cd_grafico;
        }

        $dados['dashboardPermitidos'] = $dashboard;

        $this->layout->region('html_header', 'view_html_header');
        if (in_array(237, $this->session->userdata('permissoes'))) {
            $this->layout->region('corpo', 'asterisk-pbx/view_asteriskPBX', $dados);
        } else {
            $this->layout->region('corpo', 'view_permissao');
        }
        if (in_array(237, $this->session->userdata('permissoes'))) {
            $this->layout->region('menu_lateral', 'asterisk-pbx/view_menu_asteriskPBX');
        } else {
            $this->layout->region('corpo', 'view_permissao');
        }
        $this->layout->region('rodape', 'view_rodape');
        $this->layout->region('html_footer', 'view_html_footer');

        // Então chama o layout que irá exibir as views parciais...
        $this->layout->show('layout');
    }

    public function ativoChamadasIntelig() {
        $dashboardPermitidos = $this->dashboard->dashboardPermitidos($this->session->userdata('permissoes'));
        foreach ($dashboardPermitidos as $dP) {
            $dashboard[] = $dP->cd_grafico;
        }

        $dados['dashboardPermitidos'] = $dashboard;

        $this->layout->region('html_header', 'view_html_header');
        if (in_array(237, $this->session->userdata('permissoes'))) {
            $this->layout->region('corpo', 'asterisk-pbx/view_asteriskPBX_ativoIntelig', $dados);
        } else {
            $this->layout->region('corpo', 'view_permissao');
        }
        if (in_array(237, $this->session->userdata('permissoes'))) {
            $this->layout->region('menu_lateral', 'asterisk-pbx/view_menu_asteriskPBX');
        } else {
            $this->layout->region('corpo', 'view_permissao');
        }
        $this->layout->region('rodape', 'view_rodape');
        $this->layout->region('html_footer', 'view_html_footer');

        $this->layout->show('layout');
    }

    public function ativoChamadasGSM() {
        $dashboardPermitidos = $this->dashboard->dashboardPermitidos($this->session->userdata('permissoes'));
        foreach ($dashboardPermitidos as $dP) {
            $dashboard[] = $dP->cd_grafico;
        }

        $dados['dashboardPermitidos'] = $dashboard;

        $this->layout->region('html_header', 'view_html_header');
        if (in_array(237, $this->session->userdata('permissoes'))) {
            $this->layout->region('corpo', 'asterisk-pbx/view_asteriskPBX_ativoGSM', $dados);
        } else {
            $this->layout->region('corpo', 'view_permissao');
        }
        if (in_array(237, $this->session->userdata('permissoes'))) {
            $this->layout->region('menu_lateral', 'asterisk-pbx/view_menu_asteriskPBX');
        } else {
            $this->layout->region('corpo', 'view_permissao');
        }
        $this->layout->region('rodape', 'view_rodape');
        $this->layout->region('html_footer', 'view_html_footer');

        $this->layout->show('layout');
    }

    public function qmt8668() {
        $dashboardPermitidos = $this->dashboard->dashboardPermitidos($this->session->userdata('permissoes'));
        foreach ($dashboardPermitidos as $dP) {
            $dashboard[] = $dP->cd_grafico;
        }

        $dados['dashboardPermitidos'] = $dashboard;
//        $dados['undArray'] = $this->printserver->unidadeArray();

        $this->layout->region('html_header', 'view_html_header');
        if (in_array(237, $this->session->userdata('permissoes'))) {
            $this->layout->region('corpo', 'asterisk-pbx/view_asteriskPBX_qmt8668', $dados);
        } else {
            $this->layout->region('corpo', 'view_permissao');
        }
        if (in_array(237, $this->session->userdata('permissoes'))) {
            $this->layout->region('menu_lateral', 'asterisk-pbx/view_menu_asteriskPBX');
        } else {
            $this->layout->region('corpo', 'view_permissao');
        }
        $this->layout->region('rodape', 'view_rodape');
        $this->layout->region('html_footer', 'view_html_footer');

        // Então chama o layout que irá exibir as views parciais...
        $this->layout->show('layout');
    }

    public function qmt9988() {
        $dashboardPermitidos = $this->dashboard->dashboardPermitidos($this->session->userdata('permissoes'));
        foreach ($dashboardPermitidos as $dP) {
            $dashboard[] = $dP->cd_grafico;
        }

        $dados['dashboardPermitidos'] = $dashboard;
//        $dados['undArray'] = $this->printserver->unidadeArray();

        $this->layout->region('html_header', 'view_html_header');
        if (in_array(237, $this->session->userdata('permissoes'))) {
            $this->layout->region('corpo', 'asterisk-pbx/view_asteriskPBX_qmt9988', $dados);
        } else {
            $this->layout->region('corpo', 'view_permissao');
        }
        if (in_array(237, $this->session->userdata('permissoes'))) {
            $this->layout->region('menu_lateral', 'asterisk-pbx/view_menu_asteriskPBX');
        } else {
            $this->layout->region('corpo', 'view_permissao');
        }
        $this->layout->region('rodape', 'view_rodape');
        $this->layout->region('html_footer', 'view_html_footer');

        // Então chama o layout que irá exibir as views parciais...
        $this->layout->show('layout');
    }

    public function tridigito() {
        $dashboardPermitidos = $this->dashboard->dashboardPermitidos($this->session->userdata('permissoes'));
        foreach ($dashboardPermitidos as $dP) {
            $dashboard[] = $dP->cd_grafico;
        }

        $dados['dashboardPermitidos'] = $dashboard;

        $this->layout->region('html_header', 'view_html_header');
        if (in_array(237, $this->session->userdata('permissoes'))) {
            $this->layout->region('corpo', 'asterisk-pbx/view_asteriskPBX_tridigito', $dados);
        } else {
            $this->layout->region('corpo', 'view_permissao');
        }
        if (in_array(237, $this->session->userdata('permissoes'))) {
            $this->layout->region('menu_lateral', 'asterisk-pbx/view_menu_asteriskPBX');
        } else {
            $this->layout->region('corpo', 'view_permissao');
        }
        $this->layout->region('rodape', 'view_rodape');
        $this->layout->region('html_footer', 'view_html_footer');

        // Então chama o layout que irá exibir as views parciais...
        $this->layout->show('layout');
    }

    public function comparativoPorPeriodo() {
        $dashboardPermitidos = $this->dashboard->dashboardPermitidos($this->session->userdata('permissoes'));
        foreach ($dashboardPermitidos as $dP) {
            $dashboard[] = $dP->cd_grafico;
        }

        $dados['dashboardPermitidos'] = $dashboard;

        $this->layout->region('html_header', 'view_html_header');
        if (in_array(237, $this->session->userdata('permissoes'))) {
            $this->layout->region('corpo', 'asterisk-pbx/view_asteriskPBX_compPeriodo', $dados);
        } else {
            $this->layout->region('corpo', 'view_permissao');
        }
        if (in_array(237, $this->session->userdata('permissoes'))) {
            $this->layout->region('menu_lateral', 'asterisk-pbx/view_menu_asteriskPBX');
        } else {
            $this->layout->region('corpo', 'view_permissao');
        }
        $this->layout->region('rodape', 'view_rodape');
        $this->layout->region('html_footer', 'view_html_footer');
        $this->layout->show('layout');
    }

    public function comparativoMensal() {
        $dashboardPermitidos = $this->dashboard->dashboardPermitidos($this->session->userdata('permissoes'));
        foreach ($dashboardPermitidos as $dP) {
            $dashboard[] = $dP->cd_grafico;
        }

        $dados['dashboardPermitidos'] = $dashboard;

        $this->layout->region('html_header', 'view_html_header');
        if (in_array(237, $this->session->userdata('permissoes'))) {
            $this->layout->region('corpo', 'asterisk-pbx/view_asteriskPBX_compMensal', $dados);
        } else {
            $this->layout->region('corpo', 'view_permissao');
        }
        if (in_array(237, $this->session->userdata('permissoes'))) {
            $this->layout->region('menu_lateral', 'asterisk-pbx/view_menu_asteriskPBX');
        } else {
            $this->layout->region('corpo', 'view_permissao');
        }
        $this->layout->region('rodape', 'view_rodape');
        $this->layout->region('html_footer', 'view_html_footer');

        // Então chama o layout que irá exibir as views parciais...
        $this->layout->show('layout');
    }

    public function dadosTelefoniaTridigito($data1, $data2) {
        $dadosTelefonia = $this->asteriskpbx->receptivoTridigito($data1, $data2);
        echo json_encode($dadosTelefonia);
    }

    public function dadosTelefonia40038668($data1, $data2) {
        $dadosTelefonia = $this->asteriskpbx->receptivoQmt8668($data1, $data2);
        echo json_encode($dadosTelefonia);
    }

    public function dadosTelefonia40039988($data1, $data2) {
        $dadosTelefonia = $this->asteriskpbx->receptivoQmt9988($data1, $data2);
        echo json_encode($dadosTelefonia);
    }

    public function dadosTelefoniaAtivoIntelig($data1, $data2) {
        $dadosTelefonia = $this->asteriskpbx->ativoChamadasIntelig($data1, $data2);
        echo json_encode($dadosTelefonia);
    }

    public function dadosTelefoniaAtivoGSM($data1, $data2) {
        $dadosTelefonia = $this->asteriskpbx->ativoChamadasGSM($data1, $data2);
        echo json_encode($dadosTelefonia);
    }

    public function dadosComparativoPorPeriodo($data1, $data2) {
        $dadosTelefonia = $this->asteriskpbx->comparativoPeriodo($data1, $data2);
        echo json_encode($dadosTelefonia);
    }

    public function dadosComparativoMensal($data1, $data2) {
        $dadosTelefonia = $this->asteriskpbx->comparativoMensal($data1, $data2);
        echo json_encode($dadosTelefonia);
    }

//    public function dadosPrintServerDetalhado($und, $ano, $mes) {
//        $dadosTelefonia = $this->asteriskPBX->acompanhamentoDetalhado($und, $ano, $mes);
//        echo json_encode($dadosTelefonia);
//    }
//
//    public function dadosPrintServerDetalhadoArray($und = NULL) {
//
//        return $this->asteriskPBX->unidadeArray($und);
//    }
}
