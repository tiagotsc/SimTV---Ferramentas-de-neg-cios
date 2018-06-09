<?php 
include_once(APPPATH.'modules/base/controllers/base.php');
#include_once(APPPATH.'controllers/base.php');
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
        
        #$this->output->cache(400);
        
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
    
    //ANTIGO DASHBOARD TELEFONIA
//    public function telefonia(){
//        #$this->output->enable_profiler(TRUE);
//        $this->load->model('telefonia/fatura_model','faturaModel');
//        
//        $dashboardPermitidos = $this->dashboard->dashboardPermitidos($this->session->userdata('permissoes')); 
//        foreach($dashboardPermitidos as $dP){
//            $dashboard[] = $dP->cd_grafico;
//        }
//
//        $dados['dashboardPermitidos'] = $dashboard;
//
//        $this->layout->region('html_header', 'view_html_header');
//        $this->layout->region('menu_lateral', 'view_menu_lateral');
//        $this->layout->region('corpo', 'dashboard/view_telefonia',$dados);
//        $this->layout->region('rodape', 'view_rodape');
//        $this->layout->region('html_footer', 'view_html_footer');
//        
//        // Então chama o layout que irá exibir as views parciais...
//        $this->layout->show('layout');
//        
//    }
    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */