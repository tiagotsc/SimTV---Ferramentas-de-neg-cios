<?php 
include_once('base.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class dashboard extends Base {
     
    /**
     * dashboard::__construct()
     * 
     * Responsável por controlar o dashboard
     * 
     * @return
     */
    public function __construct(){
        
		parent::__construct();
        
        $this->load->model('Dashboard_model','dashboard');
        $this->load->model('Financeiro_model','financeiro');
        /*
        if(!$this->session->userdata('session_id') || !$this->session->userdata('logado')){
			redirect(base_url('home'));
		}
        
        # Configurações do sistema
        include_once('configSistema.php');
                        
		$this->load->helper('url');
		$this->load->helper('form');
        $this->load->model('Dashboard_model','dashboard');
        $this->load->model('Financeiro_model','financeiro');
        $this->load->library('Util', '', 'util'); 
        $this->load->model('DadosBanco_model','dadosBanco');
        $this->load->model('AnatelForm_model','anatelForm');
        
        if($this->anatelForm->verificaResponsavel()){ // Se é responsável por responder relatório da Anatel
            
            // Se a data corrente estiver dentro do período pega os formulários
            if(date('d/m/Y') >= $this->session->userdata('SATVA_INICIO') and date('d/m/Y') <= $this->session->userdata('SATVA_FIM')){
                $this->util->setMenuCompleto('');
                $this->util->setPositionMenu('left');
                $menu['menu_satva'] = $this->util->montaMenu($this->anatelForm->menuIndicadoresUnidades(), $this->anatelForm->menuIndicadores());
            }else{
                $menu['menu_satva'] = false;
            }
            
        }else{
            $menu['menu_satva'] = false;
        }
        $this->util->setMenuCompleto('');
        $this->util->setPositionMenu('right');
        $menu['menu'] = $this->util->montaMenu($this->dadosBanco->menu($this->session->userdata('permissoes')), $this->dadosBanco->paisMenu($this->session->userdata('permissoes')));
        $this->layout->region('menu', 'view_menu', $menu);
        */
           
	} 
     
	/**
	 * dashboard::index()
     * 
     * Lista os relatórios existentes
	 * 
	 * @return
	 */
	public function index()
	{ 
        
        #$menu['menu'] = $this->util->montaMenu($this->dadosBanco->menu($this->session->userdata('permissoes')), $this->dadosBanco->paisMenu($this->session->userdata('permissoes')));
        
        $this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        $this->layout->region('corpo', 'view_conteudo');
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
	
    }
    
    /**
	 * dashboard::registraAcesso()
     * 
     * Log de acessos ao dashboard
	 * 
	 * @return
	 */
    public function registraAcesso(){
        
        $this->dashboard->gravaAcesso();
        
    }
    
    /**
	 * dashboard::faturamentoCobranca()
     * 
     * Dashboard da cobrança faturamento
	 * 
	 * @return
	 */
    public function faturamentoCobranca(){
        
        $dashboardPermitidos = $this->dashboard->dashboardPermitidos($this->session->userdata('permissoes')); 
        foreach($dashboardPermitidos as $dP){
            $dashboard[] = $dP->cd_grafico;
        }
        $dados['dashboardPermitidos'] = $dashboard;
        
        for($i=2013; $i<=date('Y'); $i++){
            $anoDashBoard[] = $i;
        }
        
        $dados['anoDashboard'] = array_reverse($anoDashBoard);
        $dados['banco'] = $this->financeiro->banco();
        #$dados['mesAnoStatusBoleto'] = $this->dashboard->comboMesStatusBoleto(); # APAGAR
        $dados['mesesCobranca'] = $this->dashboard->comboMesesCobranca(); 
        
        #$menu['menu'] = $this->util->montaMenu($this->dadosBanco->menu($this->session->userdata('permissoes')), $this->dadosBanco->paisMenu($this->session->userdata('permissoes')));
        
        $this->layout->region('html_header', 'view_html_header');
        #$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        
        $this->layout->region('corpo', 'dashboard/view_cobrancaFaturamento',$dados);
        
        $this->layout->region('rodape', 'view_rodape');
        $this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
        $this->layout->show('layout');
        
    }
    
    /**
	 * dashboard::marketing()
     * 
     * Dashboard do marketing
	 * 
	 * @return
	 */
    public function marketing(){
        
        $dashboardPermitidos = $this->dashboard->dashboardPermitidos($this->session->userdata('permissoes')); 
        foreach($dashboardPermitidos as $dP){
            $dashboard[] = $dP->cd_grafico;
        }
        $dados['dashboardPermitidos'] = $dashboard;
        
        # Rentabilização
        $dados['comoRentabilitacaoTela1'] = $this->dashboard->comboRentabilizacao(); 
        
        # Base de assinantes - Consolidado
        $dados['baseAssinantesConsolidadoMovimentacaoSaida'] = $this->dashboard->movimentacaoBaseConsolidadoSaida();
        $dados['baseAssinantesConsolidadoMovimentacaoEntrada'] = $this->dashboard->movimentacaoBaseConsolidadoEntrada();
        $dados['baseAssinantesConsolidadoComSinal'] = $this->dashboard->baseAssinantesConsolidadoComSinal(); 
        $dados['baseAssinantesConsolidadoSemSinal'] = $this->dashboard->baseAssinantesConsolidadoSemSinal();
        
        # Base de assinantes - Individual 
        $dados['baseAssinantesMovimentacaoSaidaIndividual'] = $this->dashboard->movimentacaoBaseSaidaTipo('INDIVIDUAL');
        $dados['baseAssinantesMovimentacaoEntradaIndividual'] = $this->dashboard->movimentacaoBaseEntradaTipo('INDIVIDUAL');
        $dados['baseAssinantesComSinalIndividual'] = $this->dashboard->baseAssinantesComSinalTipo('INDIVIDUAL');  
        $dados['baseAssinantesSemSinalIndividual'] = $this->dashboard->baseAssinantesSemSinalTipo('INDIVIDUAL');  
        
        # Base de assinantes - Filiado
        $dados['baseAssinantesMovimentacaoSaidaFiliado'] = $this->dashboard->movimentacaoBaseSaidaTipo('FILIADO');
        $dados['baseAssinantesMovimentacaoEntradaFiliado'] = $this->dashboard->movimentacaoBaseEntradaTipo('FILIADO');
        $dados['baseAssinantesComSinalFiliado'] = $this->dashboard->baseAssinantesComSinalTipo('FILIADO'); 
        $dados['baseAssinantesSemSinalFiliado'] = $this->dashboard->baseAssinantesSemSinalTipo('FILIADO');
        
        $dados['baseAssinantesConsolidadoPermissor'] = $this->dashboard->baseAssinantesConsolidadePermissor();

        $dados['metaBase'] = $this->dashboard->metaCorrente();
        
        #$menu['menu'] = $this->util->montaMenu($this->dadosBanco->menu($this->session->userdata('permissoes')), $this->dadosBanco->paisMenu($this->session->userdata('permissoes')));
        
        $this->layout->region('html_header', 'view_html_header');
        #$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        $this->layout->region('corpo', 'dashboard/view_marketing',$dados);
        $this->layout->region('rodape', 'view_rodape');
        $this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
        $this->layout->show('layout');
        
    }
    
    /**
	 * dashboard::diretoria()
     * 
     * Dashboard da diretoria
	 * 
	 * @return
	 */
    public function diretoria(){
        
        $dashboardPermitidos = $this->dashboard->dashboardPermitidos($this->session->userdata('permissoes')); 
        foreach($dashboardPermitidos as $dP){
            $dashboard[] = $dP->cd_grafico;
        }
        $dados['dashboardPermitidos'] = $dashboard;
        $dados['permissor'] = $this->dashboard->unidade();
        #$dados['servico'] = $this->dashboard->servico();
        
        #$menu['menu'] = $this->util->montaMenu($this->dadosBanco->menu($this->session->userdata('permissoes')), $this->dadosBanco->paisMenu($this->session->userdata('permissoes')));
        
        $this->layout->region('html_header', 'view_html_header');
        #$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        $this->layout->region('corpo', 'dashboard/view_diretoria',$dados);
        $this->layout->region('rodape', 'view_rodape');
        $this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
        $this->layout->show('layout');
        
    }
    
    /**
	 * dashboard::telefonia()
     * 
     * Dashboard da telefonia
	 * 
	 * @return
	 */
    public function telefonia(){
        
        $this->load->model('telefonia/fatura_model','faturaModel');
        
        $dashboardPermitidos = $this->dashboard->dashboardPermitidos($this->session->userdata('permissoes')); 
        foreach($dashboardPermitidos as $dP){
            $dashboard[] = $dP->cd_grafico;
        }
        #$dados['grafico1'] = $this->dashboard->telefonia1();
        $dados['dashboardPermitidos'] = $dashboard;
        $dados['permissor'] = $this->dashboard->unidade();
        $dados['mesTelAtivo'] = $this->faturaModel->mesesDisponiveis('ATIVO');
        $dados['mesTelReceptivo0800'] = $this->faturaModel->mesesDisponiveis('0800');
        $dados['mesTelReceptivo4004'] = $this->faturaModel->mesesDisponiveis('4004');
        
        #$menu['menu'] = $this->util->montaMenu($this->dadosBanco->menu($this->session->userdata('permissoes')), $this->dadosBanco->paisMenu($this->session->userdata('permissoes')));
        
        $this->layout->region('html_header', 'view_html_header');
        #$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        $this->layout->region('corpo', 'dashboard/view_telefonia',$dados);
        $this->layout->region('rodape', 'view_rodape');
        $this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
        $this->layout->show('layout');
        
    }
    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */