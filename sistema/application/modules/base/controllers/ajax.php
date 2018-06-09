<?php
error_reporting(0);
if(!defined('BASEPATH')) exit('No direct script access allowed');
date_default_timezone_set('America/Sao_Paulo');
#setlocale(LC_ALL, 'pt_BR.UTF-8', 'Portuguese_Brazil.1252');
/**
* Classe criada para controlar todas as buscas sicronas (Sem refresh)
*/
class Ajax extends MX_Controller
{
    
	/**
	 * Ajax::__construct()
	 * 
	 * @return
	 */
	public function __construct(){
		parent::__construct();
        
        if(!$this->session->userdata('session_id') || !$this->session->userdata('logado')){
			redirect(base_url('home'));
		}
        #$this->load->helper('url');
		#$this->load->helper('form');
    }
    
    /*function index($product_id = 2)
    {
        $this->load->model('Product_model', 'product');
        $data['json'] = $this->product->get($product_id);
        if (!$data['json']) show_404();

        $this->load->view('json_view', $data);
    }*/
    
    public function feedbackTempoReal(){

        $resDados['dados']['feedback'] = $this->session->userdata('feedback');
        $resDados['dados']['feedbackStatus'] = $this->session->userdata('feedbackStatus');
        $this->load->view('view_json',$resDados);
        
    }
    
    public function feedbackTempoRealLimpa(){
        
        $this->session->unset_userdata('feedback');
        $this->session->unset_userdata('feedbackStatus');
        
    }
    
    /**
     * Ajax::statusProcesValidacaoRetorno()
     * 
     * @return
     */
    public function statusProcesValidacaoRetorno(){
        
        #echo $_SESSION['totalRetornos']; exit();
        /*$dados['dados'] = array('processados'=>$this->session->userdata('retornoProcessado'));
        $this->session->unset_userdata('retornoProcessado');
        #$dados['totalRetornos'] = $_SESSION['totalRetornos'];
        
        $this->load->view('cobrancaFaturamento/arquivos/view_ajax',$dados);*/
        
    }
    
    /**
    * Ajax::pesquisaBoleto()
    * Função que pesquisa boleto
    */
    public function pesquisaBoleto(){
	
		$this->load->model('cobrancaFaturamento/ArquivoCobranca_model','arquivoCobranca');
		#$this->load->helper('url');
		#$this->load->helper('form');
		$this->load->helper('text');
		
		$resDados['dados'] = $this->arquivoCobranca->buscaBoleto();
        #$teste[0] = array('conteudo_arquivo'=>$this->input->post('boleto'));
        #$resDados['dados'] = $teste;
		
		$this->load->view('view_json',$resDados);
	
	}
    
    /**
	 * Ajax::graficoLinhaRetornoAno()
	 * 
     * Monta a estrutura de informações para alimentar o gráfico de linhas do dashboard
     * 
	 */
    public function graficoLinhaRetornoAno($ano, $cdBanco = null){
        
        $this->load->model('dashboard/Dashboard_model','dashboard');
        
        $resTitulo = $this->dashboard->qtdTitulos($ano, $cdBanco);
        
        if($resTitulo){
   
            foreach($resTitulo as $rB){
                
                if($rB->qtd_baixado > 0){
                    $qtdBaixados = $rB->qtd_baixado;
                }else{
                    $qtdBaixados = 0;
                }
                
                if($rB->qtd_rejeitado > 0){
                    $qtdRejeitado = $rB->qtd_rejeitado;
                }else{
                    $qtdRejeitado = 0;
                }
                
                $resTitulos[] = array('meses'=>$rB->mes, 'Quantidade baixado'=>$qtdBaixados, 'Quantidade rejeitado'=>$qtdRejeitado);
            }
        
        }else{
            
            $resTitulos[] = array('meses'=>'Nenhum', 'Quantidade baixado'=>0, 'Quantidade rejeitado'=>0);
            
        }
        
        $resDados['dados'] = $resTitulos;
        
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::telefoniaGrafico1()
	 * 
     * 
     * 
	 */
    public function telefoniaGrafico1(){
        
        $this->load->model('dashboard/Dashboard_model','dashboard');
        
        $resultado = $this->dashboard->telefonia1();
        
        if($resultado){
        
            foreach($resultado as $res){
                
                if($res->qtd_liga_fixo > 0){
                    $valorQtdLigaFixo = $res->qtd_liga_fixo;
                }else{
                    $valorQtdLigaFixo = 0;
                }
                
                $resQtdLigaFixo[] = $valorQtdLigaFixo;
                
                if($res->segundos_fixo > 0){
                    $valorSegundosFixo = round($res->segundos_fixo);
                }else{
                    $valorSegundosFixo = 0;
                }
                
                $resSegundosFixo[] = $valorSegundosFixo;
                
                if($res->qtd_liga_celular > 0){
                    $valorQtdLigaCelular = $res->qtd_liga_celular;
                }else{
                    $valorQtdLigaCelular = 0;
                }
                
                $resQtdLigaCelular[] = $valorQtdLigaCelular;
                
                if($res->segundos_celular > 0){
                    $valorSegundosCelular = round($res->segundos_celular);
                }else{
                    $valorSegundosCelular = 0;
                }
                
                $resSegundosCelular[] = $valorSegundosCelular;
                
                $data[] = $res->data;
                
            }
            
            $resDados['dados'] = array('data' => $data, 'Qtd. Fixo' => $resQtdLigaFixo, 'Qtd. Celular' => $resQtdLigaCelular, 'Minutos Fixo' => $resSegundosFixo, 'Minutos Celular' => $resSegundosCelular);
        
        }else{
            
            $resDados['dados'] = array('data' => 'Nenhum', 'Qtd. Fixo' => 0, 'Qtd. Celular' => 0, 'Minutos Fixo' => 0, 'Minutos Celular' => 0);
            
        }
        
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::telefoniaGrafico1()
	 * 
     * 
     * 
	 */
    public function telefoniaGrafico2(){
        
        $this->load->model('dashboard/Dashboard_model','dashboard');
        
        $resultado = $this->dashboard->telefonia2();
        
        $dados = array();
        
        foreach($resultado as $res){
            
            $dados[] = array('campo'=>$res->departamento, 'valor' =>$res->qtd, 'tempo'=>$res->tempo);
            
        }
        
        $resDados['dados'] = $dados;
        
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::telefoniaGrafico3()
	 * 
     * Call center Ativo - Acompanhamento Atual
     * 
	 */
    /*public function telefoniaGrafico3($periodo = 'N', $zoom = 'N'){
        
        $this->load->library('Util', '', 'util');  
        $this->load->model('dashboard/Dashboard_model','dashboard');
        
        $resultado = $this->dashboard->telefonia5($periodo, $zoom, 'ATIVO');
        $somaCusto = 0;
        
        if($resultado){
        
            foreach($resultado as $res){
                
                #$res->custo = str_replace(',','.',$res->custo);
                
                if($res->fonte == 'SERVIDOR ASTERISK'){
                
                    if($res->qtd > 0){
                        $valorQtd = $res->qtd;
                    }else{
                        $valorQtd = 0;
                    }
                    
                    $resQtd[] = $valorQtd;
                    
                    if($res->minutos > 0){
                        $valorMinutos = $res->minutos;
                    }else{
                        $valorMinutos = 0;
                    }
                    
                    $resMinutos[] = $valorMinutos;
    
                    $somaCusto += $res->custo; 
                    
                    $resSoma[] = str_replace(',','.',$somaCusto);
                    
                    if($res->segundos > 0){
                        $bilhetacao = $res->custo;
                    }else{
                        $bilhetacao = 0;
                    }
                    
                    $resBilhetacao[] = $bilhetacao;
                   
                
                }
                    
                if(!in_array($res->mes_ano, $data)){
                    $data[] = $res->mes_ano;
                }      
                
                if($res->fonte == 'CALLCENTER - ATIVO'){
                    
                    if($res->qtd > 0){
                        $valorQtd2 = $res->qtd;
                    }else{
                        $valorQtd2 = 0;
                    }
                    
                    $resQtd2[] = $valorQtd2;
                    
                    if($res->minutos > 0){
                        $valorMinutos2 = $res->minutos;
                    }else{
                        $valorMinutos2 = 0;
                    }
                    
                    $resMinutos2[] = $valorMinutos2;
    
                    $somaCusto2 += $res->custo; 
                    
                    $resSoma2[] = str_replace(',','.',$somaCusto2);
                    
                    if($res->segundos > 0){
                        $bilhetacao2 = $res->custo;
                    }else{
                        $bilhetacao2 = 0;
                    }
                    
                    $resBilhetacao2[] = $bilhetacao2;
                    
                }
                
            }
            
            for($i=1; $i <= count($data); $i++){
                $zero[] = 0;
            }
            
            $resQtd = ($resQtd)? $resQtd: $zero;
            $resMinutos = ($resMinutos)? $resMinutos: $zero;
            $resBilhetacao = ($resBilhetacao)? $resBilhetacao: $zero;
            $resSoma = ($resSoma)? $resSoma: $zero;
            
            $resQtd2 = ($resQtd2)? $resQtd2: $zero;
            $resMinutos2 = ($resMinutos2)? $resMinutos2: $zero;
            $resBilhetacao2 = ($resBilhetacao2)? $resBilhetacao2: $zero;
            $resSoma2 = ($resSoma2)? $resSoma2: $zero;
            
            $resDados['dados'] = array('data' => $data, 'Holding - Qtd.' => $resQtd, 'Holding - Minutos' => $resMinutos, 'Holding - Custo' => $resBilhetacao, 'Holding - PosDia' => $resSoma
            ,'CallCenter - Qtd.' => $resQtd2, 'CallCenter - Minutos' => $resMinutos2, 'CallCenter - Custo' => $resBilhetacao2, 'CallCenter - PosDia' => $resSoma2);
            
        }else{
            
            $resDados['dados'] = array('data' => 'Nenhum', 'Qtd.' => 0, 'Minutos' => 0, 'CallCenter - Custo' => 0, 'CallCenter - PosDia' => 0);
            
        }
        #echo '<pre>'; print_r($resDados['dados']); exit();
        $this->load->view('view_json',$resDados);
        
    }*/
    /*
    public function telefoniaAtivo($mes = 'N'){
        
        $this->load->library('Util', '', 'util');  
        $this->load->model('dashboard/Dashboard_model','dashboard');
        
        $resultado = $this->dashboard->diasMesesLigacao($mes, $fonte, 'sim');
        
        if($resultado){
            foreach($resultado as $res){
                
                $data[] = $res->data_formatada;
                
                $holding = $this->dashboard->dadosTipoLigacao($res->data_parametro, 'holding', "'fixoLocal','celularLocal','fixoLDN','celularLDN'");
                
                if($holding[0]->qtd > 0){
                    $holdingQtd = $holding[0]->qtd;
                }else{
                    $holdingQtd = 0;
                }
                
                $resHoldingQtd[] = $holdingQtd;
                
                if($holding[0]->minutos > 0){
                    $holdingMinutos = $holding[0]->minutos;
                }else{
                    $holdingMinutos = 0;
                }
                
                $resHoldingMinutos[] = $holdingMinutos;
                
                if($holding[0]->custo > 0){
                    $holdingCusto = $holding[0]->custo;
                }else{
                    $holdingCusto = 0;
                }
                
                $resHoldingCusto[] = $holdingCusto;
                
                $holdingCustoDia += $holdingCusto;
                $holdingPosDia[] = str_replace(',','.',$holdingCustoDia);
                
                $callcenter = $this->dashboard->dadosTipoLigacao($res->data_parametro, 'callcenter', "'fixoLocal','celularLocal','fixoLDN','celularLDN'");
                
                if($callcenter[0]->qtd > 0){
                    $callcenterQtd = $callcenter[0]->qtd;
                }else{
                    $callcenterQtd = 0;
                }
                
                $resCallcenterQtd[] = $callcenterQtd;
                
                if($callcenter[0]->minutos > 0){
                    $callcenterMinutos = $callcenter[0]->minutos;
                }else{
                    $callcenterMinutos = 0;
                }
                
                $resCallcenterMinutos[] = $callcenterMinutos;
                
                if($callcenter[0]->custo > 0){
                    $callcenterCusto = $callcenter[0]->custo;
                }else{
                    $callcenterCusto = 0;
                }
                
                $resCallcenterCusto[] = $callcenterCusto;
                
                $callcenterCustoDia += $callcenterCusto;
                $callcenterPosDia[] = str_replace(',','.',$callcenterCustoDia);
                
            }
            
            $resDados['dados'] = array('data' => $data, 
                                        'Holding - Qtd.' => $resHoldingQtd, 'Holding - Minutos' => $resHoldingMinutos, 'Holding - Custo' => $resHoldingCusto, 'Holding - PosDia' => $holdingPosDia,
                                        'CallCenter - Qtd.' => $resCallcenterQtd, 'CallCenter - Minutos' => $resCallcenterMinutos, 'CallCenter - Custo' => $resCallcenterCusto, 'CallCenter - PosDia' => $callcenterPosDia
                                        );
        }else{
            
            $resDados['dados'] = array('data' => $data, 
                                        'Holding - Qtd.' => 0, 'Holding - Minutos' => 0, 'Holding - Custo' => 0, 'Holding - PosDia' => 0,
                                        'CallCenter - Qtd.' => 0, 'CallCenter - Minutos' => 0, 'CallCenter - Custo' => 0, 'CallCenter - PosDia' => 0
                                        );
            
        }
        
        $this->load->view('view_json',$resDados);  
        
    }
    */
    public function dashboardTelefoniaAtivo($mes){
        
        $this->load->library('Util', '', 'util');  
        $this->load->model('dashboard/Dashboard_model','dashboard');
        
        $resultado = $this->dashboard->dashboardTelefoniaAtivo($mes);
        
        if($resultado){
            foreach($resultado as $res){
                
                $data[] = $res->data;
                
                if($res->holding_qtd > 0){
                    $holdingQtd = $res->holding_qtd;
                }else{
                    $holdingQtd = 0;
                }
                
                $resHoldingQtd[] = $holdingQtd;
                
                if($res->holding_minutos > 0){
                    $holdingMinutos = $res->holding_minutos;
                }else{
                    $holdingMinutos = 0;
                }
                
                $resHoldingMinutos[] = $holdingMinutos;
                
                if($res->holding_custo > 0){
                    $holdingCusto = $res->holding_custo;
                }else{
                    $holdingCusto = 0;
                }
                
                $resHoldingCusto[] = $holdingCusto;
                
                $holdingCustoDia += $holdingCusto;
                $holdingPosDia[] = str_replace(',','.',$holdingCustoDia);
                
                if($res->callcenter_qtd > 0){
                    $callcenterQtd = $res->callcenter_qtd;
                }else{
                    $callcenterQtd = 0;
                }
                
                $resCallcenterQtd[] = $callcenterQtd;
                
                if($res->callcenter_minutos > 0){
                    $callcenterMinutos = $res->callcenter_minutos;
                }else{
                    $callcenterMinutos = 0;
                }
                
                $resCallcenterMinutos[] = $callcenterMinutos;
                
                if($res->callcenter_custo > 0){
                    $callcenterCusto = $res->callcenter_custo;
                }else{
                    $callcenterCusto = 0;
                }
                
                $resCallcenterCusto[] = $callcenterCusto;
                
                $callcenterCustoDia += $callcenterCusto;
                $callcenterPosDia[] = str_replace(',','.',$callcenterCustoDia);
                
            }
            
            
            $resDados['dados'] = array('data' => $data, 
                                        'Holding - Qtd.' => $resHoldingQtd, 'Holding - Minutos' => $resHoldingMinutos, 'Holding - Custo' => $resHoldingCusto, 'Holding - PosDia' => $holdingPosDia,
                                        'CallCenter - Qtd.' => $resCallcenterQtd, 'CallCenter - Minutos' => $resCallcenterMinutos, 'CallCenter - Custo' => $resCallcenterCusto, 'CallCenter - PosDia' => $callcenterPosDia
                                        );
        
        }else{
            
            $resDados['dados'] = array('data' => $data, 
                                        'Holding - Qtd.' => 0, 'Holding - Minutos' => 0, 'Holding - Custo' => 0, 'Holding - PosDia' => 0,
                                        'CallCenter - Qtd.' => 0, 'CallCenter - Minutos' => 0, 'CallCenter - Custo' => 0, 'CallCenter - PosDia' => 0
                                        );
            
        }
        
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::telefoniaReceptivo()
	 * 
     * Pega os dados da telefonia receptivo para montar o dashboard
     * 
	 *//*
    public function telefoniaReceptivo($mes, $fonte){
        
        $this->load->library('Util', '', 'util');  
        $this->load->model('dashboard/Dashboard_model','dashboard');
        
        $resultado = $this->dashboard->diasMesesLigacao($mes, $fonte);    
        
        if($resultado){
            foreach($resultado as $res){
                
                $data[] = $res->data_formatada;
                
                $celular = $this->dashboard->dadosTipoLigacao($res->data_parametro, $fonte, "'celularLDN', 'celularLocal'");
                
                if($celular[0]->qtd > 0){
                    $celularQtd = $celular[0]->qtd;
                }else{
                    $celularQtd = 0;
                }
                
                $resCelularQtd[] = $celularQtd;
                
                if($celular[0]->minutos > 0){
                    $celularMinutos = $celular[0]->minutos;
                }else{
                    $celularMinutos = 0;
                }
                
                $resCelularMinutos[] = $celularMinutos;
                
                if($celular[0]->custo > 0){
                    $celularCusto = $celular[0]->custo;
                }else{
                    $celularCusto = 0;
                }            
                
                $resCelularCusto[] = $celularCusto;
                
                $celularCustoDia += $celularCusto;
                $celularPosDia[] = str_replace(',','.',$celularCustoDia);
                
                $fixoLocal  = $this->dashboard->dadosTipoLigacao($res->data_parametro, $fonte, "'fixoLocal'");
                
                if($fixoLocal[0]->qtd > 0){
                    $fixoLocalQtd = $fixoLocal[0]->qtd;
                }else{
                    $fixoLocalQtd = 0;
                }
                
                $resFixoLocalQtd[] = $fixoLocalQtd;
                
                if($fixoLocal[0]->minutos > 0){
                    $fixoLocalMinutos = $fixoLocal[0]->minutos;
                }else{
                    $fixoLocalMinutos = 0;
                }
                
                $resFixoLocalMinutos[] = $fixoLocalMinutos;
                
                if($fixoLocal[0]->custo > 0){
                    $fixoLocalCusto = $fixoLocal[0]->custo;
                }else{
                    $fixoLocalCusto = 0;
                }
                
                $resFixoLocalCusto[] = $fixoLocalCusto;
                
                $fixoLocalCustoDia += $fixoLocalCusto;
                $fixoLocalPosDia[] = str_replace(',','.',$fixoLocalCustoDia);
                
                $fixoLDN  = $this->dashboard->dadosTipoLigacao($res->data_parametro, $fonte, "'fixoLDN'");
                
                if($fixoLDN[0]->qtd > 0){
                    $fixoLDNQtd = $fixoLDN[0]->qtd;
                }else{
                    $fixoLDNQtd = 0;
                }
                
                $resFixoLDNQtd[] = $fixoLDNQtd;
                
                if($fixoLDN[0]->minutos > 0){
                    $fixoLDNMinutos = $fixoLDN[0]->minutos;
                }else{
                    $fixoLDNMinutos = 0;
                }
                
                $resFixoLDNMinutos[] = $fixoLDNMinutos;
                
                if($fixoLDN[0]->custo > 0){
                    $fixoLDNCusto = $fixoLDN[0]->custo;
                }else{
                    $fixoLDNCusto = 0;
                }
                
                $resFixoLDNCusto[] = $fixoLDNCusto;
                
                $fixoLDNCustoDia += $fixoLDNCusto;
                
                $fixoLDNPosDia[] = str_replace(',','.',$fixoLDNCustoDia);
                
            }
            
            $resDados['dados'] = array('data' => $data, 
                                        'Celular - Qtd.' => $resCelularQtd, 'Celular - Minutos' => $resCelularMinutos, 'Celular - Custo' => $resCelularCusto, 'Celular - PosDia' => $celularPosDia,
                                        'Fixo Local - Qtd.' => $resFixoLocalQtd, 'Fixo Local - Minutos' => $resFixoLocalMinutos, 'Fixo Local - Custo' => $resFixoLocalCusto, 'Fixo Local - PosDia' => $fixoLocalPosDia,
                                        'Fixo LDN - Qtd.' => $resFixoLDNQtd, 'Fixo LDN - Minutos' => $resFixoLDNMinutos, 'Fixo LDN - Custo' => $resFixoLDNCusto, 'Fixo LDN - PosDia' => $fixoLDNPosDia
                                        );

        }else{
            
            $resDados['dados'] = array('data' => $data, 
                                        'Celular - Qtd.' => 0, 'Celular - Minutos' => 0, 'Celular - Custo' => 0, 'Celular - PosDia' => 0,
                                        'Fixo Local - Qtd.' => 0, 'Fixo Local - Minutos' => 0, 'Fixo Local - Custo' => 0, 'Fixo Local - PosDia' => 0,
                                        'Fixo LDN - Qtd.' => 0, 'Fixo LDN - Minutos' => 0, 'Fixo LDN - Custo' => 0, 'Fixo LDN - PosDia' => 0
                                        );
            
        }
        
        $this->load->view('view_json',$resDados);  
        
    }*/
    
    public function dashboardTelefoniaReceptivo($mes, $fonte){
        
        $this->load->library('Util', '', 'util');  
        $this->load->model('dashboard/Dashboard_model','dashboard');
        
        $resultado = $this->dashboard->dashboardTelefoniaReceptivo($mes, $fonte);
        
        if($resultado){
            foreach($resultado as $res){
                
                $data[] = $res->data;
                
                if($res->celular_qtd > 0){
                    $celularQtd = $res->celular_qtd;
                }else{
                    $celularQtd = 0;
                }
                
                $resCelularQtd[] = $celularQtd;
                
                if($res->celular_minutos > 0){
                    $celularMinutos = $res->celular_minutos;
                }else{
                    $celularMinutos = 0;
                }
                
                $resCelularMinutos[] = $celularMinutos;
                
                if($res->celular_custo > 0){
                    $celularCusto = $res->celular_custo;
                }else{
                    $celularCusto = 0;
                }            
                
                $resCelularCusto[] = $celularCusto;
                
                $celularCustoDia += $celularCusto;
                $celularPosDia[] = str_replace(',','.',$celularCustoDia);
                    
                if($res->fixo_local_qtd > 0){
                    $fixoLocalQtd = $res->fixo_local_qtd;
                }else{
                    $fixoLocalQtd = 0;
                }
                
                $resFixoLocalQtd[] = $fixoLocalQtd;
                
                if($res->fixo_local_minutos > 0){
                    $fixoLocalMinutos = $res->fixo_local_minutos;
                }else{
                    $fixoLocalMinutos = 0;
                }
                
                $resFixoLocalMinutos[] = $fixoLocalMinutos;
                
                if($res->fixo_local_custo > 0){
                    $fixoLocalCusto = $res->fixo_local_custo;
                }else{
                    $fixoLocalCusto = 0;
                }
                
                $resFixoLocalCusto[] = $fixoLocalCusto;
                
                $fixoLocalCustoDia += $fixoLocalCusto;
                $fixoLocalPosDia[] = str_replace(',','.',$fixoLocalCustoDia);
                
                if($res->fixo_LDN_qtd > 0){
                    $fixoLDNQtd = $res->fixo_LDN_qtd;
                }else{
                    $fixoLDNQtd = 0;
                }
                
                $resFixoLDNQtd[] = $fixoLDNQtd;
                
                if($res->fixo_LDN_minutos > 0){
                    $fixoLDNMinutos = $res->fixo_LDN_minutos;
                }else{
                    $fixoLDNMinutos = 0;
                }
                
                $resFixoLDNMinutos[] = $fixoLDNMinutos;
                
                if($res->fixo_LDN_custo > 0){
                    $fixoLDNCusto = $res->fixo_LDN_custo;
                }else{
                    $fixoLDNCusto = 0;
                }
                
                $resFixoLDNCusto[] = $fixoLDNCusto;
                
                $fixoLDNCustoDia += $fixoLDNCusto;
                
                $fixoLDNPosDia[] = str_replace(',','.',$fixoLDNCustoDia);
                
            }
            
            
            $resDados['dados'] = array('data' => $data, 
                                            'Celular - Qtd.' => $resCelularQtd, 'Celular - Minutos' => $resCelularMinutos, 'Celular - Custo' => $resCelularCusto, 'Celular - PosDia' => $celularPosDia,
                                            'Fixo Local - Qtd.' => $resFixoLocalQtd, 'Fixo Local - Minutos' => $resFixoLocalMinutos, 'Fixo Local - Custo' => $resFixoLocalCusto, 'Fixo Local - PosDia' => $fixoLocalPosDia,
                                            'Fixo LDN - Qtd.' => $resFixoLDNQtd, 'Fixo LDN - Minutos' => $resFixoLDNMinutos, 'Fixo LDN - Custo' => $resFixoLDNCusto, 'Fixo LDN - PosDia' => $fixoLDNPosDia
                                            );
        
        }else{
            
            $resDados['dados'] = array('data' => $data, 
                                        'Celular - Qtd.' => 0, 'Celular - Minutos' => 0, 'Celular - Custo' => 0, 'Celular - PosDia' => 0,
                                        'Fixo Local - Qtd.' => 0, 'Fixo Local - Minutos' => 0, 'Fixo Local - Custo' => 0, 'Fixo Local - PosDia' => 0,
                                        'Fixo LDN - Qtd.' => 0, 'Fixo LDN - Minutos' => 0, 'Fixo LDN - Custo' => 0, 'Fixo LDN - PosDia' => 0
                                        );
            
        }
        
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::totalFatura()
	 * 
     * Total da fatura para o mês informado
     * 
	 */
    public function totalFatura($periodo = 'N', $zoom = 'N', $fonte = 'ATIVO'){
        
        $this->load->library('Util', '', 'util');         
        $this->load->model('dashboard/Dashboard_model','dashboard');
        
        $resultado = $this->dashboard->telefoniaCustoTotal($periodo, $zoom, $fonte);
        
        if($periodo == 'N'){
            $data = ' - D&ecirc; '.date('03/m/Y').' &agrave; '.date('d/m/Y', strtotime("-1 days",strtotime(date('d-m-Y'))));
        }else{
            $pegaData = explode(' ', $this->util->telefoniaPeriodo($periodo));
            $data = ' - D&ecirc; '.$this->util->formataData($pegaData[0], 'BR').' &agrave; '.$this->util->formataData(substr($pegaData[1],0,-2).'02', 'BR');
        }
        
        $dados['dados'] = array('total' => $resultado[0]->custo_total, 'data' => $data);
        
        $this->load->view('view_json',$dados);
        
        #echo '<pre>'; print_r($resultado); exit();
        
    }
    
    /**
	 * Ajax::telefoniaGrafico3()
	 * 
     * Call center - detalhado
     * 
	 */
    public function telefoniaGrafico4($tipo){
        
        $this->load->library('Util', '', 'util');  
        $this->load->model('dashboard/Dashboard_model','dashboard');
        
        $resultado = $this->dashboard->telefonia3($tipo);
        #echo '<pre>'; print_r($resultado); exit();
        if($resultado){
        
            foreach($resultado as $res){
                
                if($res->qtd > 0){
                    $valorQtd = $res->qtd;
                }else{
                    $valorQtd = 0;
                }
                
                $resQtd[] = $valorQtd;
                
                if($res->minutos > 0){
                    $valorMinutos = $res->minutos;
                }else{
                    $valorMinutos = 0;
                }
                
                $resMinutos[] = $valorMinutos;
                /*
                switch ($res->tipo) {
                    case 'Celular': # Celular local
                        $tipo = "celular";
                        break;
                    case 'DDDCel': # Celular à dintância
                        $tipo = "celularddd";
                        break;
                    case 'Fixo': # Fixo local
                        $tipo = "fixo";
                        break;
                    default: # Fixo à distância
                       $tipo = "fixoddd";
                }
                */
                if($res->segundos > 0){
                    $bilhetacao = $res->custo;
                }else{
                    $bilhetacao = 0;
                }
                
                $resBilhetacao[] = $bilhetacao;
                
                $data[] = $res->titulo;
                
            }
            
            $resDados['dados'] = array('data' => $data, 'Qtd.' => $resQtd, 'Minutos' => $resMinutos, 'Custo' => $resBilhetacao);
        
        }else{
            
            $resDados['dados'] = array('data' => 'Nenhum', 'Qtd.' => 0, 'Minutos' => 0, 'Custo' => 0);
            
        }
        
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::telefoniaGrafico5()
	 * 
     * Call center Receptivo - Acompanhamento Atual
     * 
	 */
    public function telefoniaGrafico5($periodo = 'N', $zoom = 'N'){
        
        $this->load->library('Util', '', 'util');  
        $this->load->model('dashboard/Dashboard_model','dashboard');
        
        $resultado = $this->dashboard->telefonia5($periodo, $zoom, 'RECEPTIVO');
        $somaCusto = 0;
        
        if($resultado){
        
            foreach($resultado as $res){
                
                if($res->fonte == 'CALLCENTER - RECEPTIVO'){
                
                    if($res->qtd > 0){
                        $valorQtd = $res->qtd;
                    }else{
                        $valorQtd = 0;
                    }
                    
                    $resQtd[] = $valorQtd;
                    
                    if($res->minutos > 0){
                        $valorMinutos = $res->minutos;
                    }else{
                        $valorMinutos = 0;
                    }
                    
                    $resMinutos[] = $valorMinutos;
    
                    $somaCusto += $res->custo; 
                    
                    $resSoma[] = str_replace(',','.',$somaCusto);
                    
                    if($res->segundos > 0){
                        $bilhetacao = $res->custo;
                    }else{
                        $bilhetacao = 0;
                    }
                    
                    $resBilhetacao[] = $bilhetacao;
                   
                
                }
                    
                if(!in_array($res->mes_ano, $data)){
                    $data[] = $res->mes_ano;
                }      
                
            }
            
            for($i=1; $i <= count($data); $i++){
                $zero[] = 0;
            }
            
            $resQtd = ($resQtd)? $resQtd: $zero;
            $resMinutos = ($resMinutos)? $resMinutos: $zero;
            $resBilhetacao = ($resBilhetacao)? $resBilhetacao: $zero;
            $resSoma = ($resSoma)? $resSoma: $zero;
            
            $resDados['dados'] = array('data' => $data, 'CallCenter - Qtd.' => $resQtd, 'CallCenter - Minutos' => $resMinutos, 'CallCenter - Custo' => $resBilhetacao, 'CallCenter - PosDia' => $resSoma);
            
        }else{
            
            $resDados['dados'] = array('data' => 'Nenhum', 'Qtd.' => 0, 'Minutos' => 0, 'CallCenter - Custo' => 0, 'CallCenter - PosDia' => 0);
            
        }
        #echo '<pre>'; print_r($resDados['dados']); exit();
        $this->load->view('view_json',$resDados);
        
    }
    
    public function testeBilhetacao($segundos = 634, $tipoligacao = 'fixoddd', $fonte = 'callcenter'){
        
        $this->load->library('Util', '', 'util');   
        
        echo $this->util->telefoniaBilhetacao($segundos,$tipoligacao,'callcenter');     
        
    }
    
    /**
	 * Ajax::graficoBarraColadaRetornoAno()
	 * 
     * Monta a estrutura de informações para alimentar o gráfico de barras coladas do dashboard
     * 
	 */
    public function graficoBarraColadaRetornoAno($ano, $cdBanco = null){
        
        $this->load->model('dashboard/Dashboard_model','dashboard');
        
        $resTitulo = $this->dashboard->valorTotalTitulos($ano, $cdBanco);
        
        if($resTitulo){
        
            foreach($resTitulo as $rB){
                
                if($rB->total_baixado > 0){
                    $valorBaixado = $rB->total_baixado;
                }else{
                    $valorBaixado = 0;
                }
                
                $resBaixados[] = $valorBaixado;
                
                if($rB->total_rejeitado > 0){
                    $valorRejeitado = $rB->total_rejeitado;
                }else{
                    $valorRejeitado = 0;
                }
                
                $resRejeitados[] = $valorRejeitado;
                
                $meses[] = $rB->mes;
                
            }
        
            $resDados['dados'] = array('meses'=>$meses, 'Valor baixado'=>$resBaixados, 'Valor rejeitado'=>$resRejeitados);
        
        }else{
            
            $resDados['dados'] = array('meses'=>'Nenhum', 'Valor baixado'=>0, 'Valor rejeitado'=>0);
            
        }

        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::parametrosRelatorio()
	 * 
     * Pega os parâmetros do relatório para consulta
     * 
     * @param $cd_relatorio Cd do relatório
     * 
	 */
    public function parametrosRelatorio($cd_relatorio){
        
        $this->load->model('relatorio/Relatorio_model','relatorio'); 
        $resDados['dados'] = $this->relatorio->parametrosDoRelatorio($cd_relatorio);
        /*
        $resDados['dados'] = Array(
                                array(
                                    "nome"=>"João",
                                    "sobreNome"=>"Silva",
                                    "cidade"=>"Maringá"
                                ),
                                array(
                                    "nome"=>"Ana",
                                    "sobreNome"=>"Rocha",
                                    "cidade"=>"Londrina"
                                ),
                                array(
                                    "nome"=>"Véra",
                                    "sobreNome"=>"Valério",
                                    "cidade"=>"Cianorte"
                                ));
         */                       
        $this->load->view('view_json',$resDados);                        
        
    }
    
    /**
	 * Ajax::rentabilizacaoTela1()
	 * 
     * Pega os dados do gráfico tela 1 da rentabilização
     * 
     * @param $mesAno Parâmetro mês ano para consulta
     * 
	 */
    public function rentabilizacaoTela1($mesAno){
        
        $this->load->model('dashboard/Dashboard_model','dashboard');
        
        $resDados['dados'] = $this->dashboard->rentabilizacaoTela1($mesAno);
        
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::rentabilizacaoTela2()
	 * 
     * Pega os dados do gráfico tela 2 da rentabilização
     * 
     * @param $mesAno Parâmetro mês ano para consulta
     * 
	 */
    public function rentabilizacaoTela2($mesAno){
        
        $this->load->model('dashboard/Dashboard_model','dashboard');
        
        $resDados['dados'] = $this->dashboard->rentabilizacaoTela2($mesAno);
        
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::rentabilizacaoTela3()
	 * 
     * Pega os dados do gráfico tela 3 da rentabilização
     * 
     * @param $mesAno Parâmetro mês ano para consulta
     * 
	 */
    public function rentabilizacaoTela3($mesAno){
        
        $this->load->model('dashboard/Dashboard_model','dashboard');
        
        $resDados['dados'] = $this->dashboard->rentabilizacaoTela3($mesAno);
        
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::titulosPagos()
	 * 
     * Pega os dados do gráfico de títulos pagos
     * 
	 */
    public function titulosPagos(){
        
        $this->load->model('dashboard/Dashboard_model','dashboard');
        
        $resTitulo = $this->dashboard->titulosPagos();
        
        if($resTitulo){
        
            foreach($resTitulo as $rT){
                
                if($rT->VLR_TOT > 0){
                    $valorTotal = $rT->VLR_TOT;
                }else{
                    $valorTotal = 0;
                }
                
                $resTotal[] = $valorTotal;
                
                if($rT->VLR_PAGO > 0){
                    $valorPago = $rT->VLR_PAGO;
                }else{
                    $valorPago = 0;
                }
                
                $resPago[] = $valorPago;
                
                if($rT->VLR_PAGO_OUTROS > 0){
                    $valorPagoOutros = $rT->VLR_PAGO_OUTROS;
                }else{
                    $valorPagoOutros = 0;
                }
                
                $resPagoOutros[] = $valorPagoOutros;
                
                $meses[] = $rT->MES;
                
            }
        
            $resDados['dados'] = array('meses'=>$meses, 'Valor total'=>$resTotal, 'Valor pago'=>$resPago, 'Valor pago outros' => $resPagoOutros);
        
        }else{
            
            $resDados['dados'] = array('meses'=>'Nenhum', 'Valor total'=> 0, 'Valor pago'=> 0, 'Valor pago outros' => 0);
            
        }
        
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::pendenciaInstalacaoDezDias()
	 * 
     * Pega as pendências de instalação dos últimos dez dias
     * 
	 */
    public function pendenciaInstalacaoDezDias($permissor = '0', $servico = '0'){
        
        $this->load->model('dashboard/Dashboard_model','dashboard');
        
        $pendencia10dias = $this->dashboard->pendenciasInstalacaoDezDias($permissor, $servico);

        if($pendencia10dias){
        
            foreach($pendencia10dias as $pend10dias){
                
                if($pend10dias->QTDE_VENDAS > 0){
                    $valorQtdeVendas = $pend10dias->QTDE_VENDAS;
                }else{
                    $valorQtdeVendas = 0;
                }
                
                $resQtdeVendas[] = $valorQtdeVendas;
                
                if($pend10dias->QTDE_INSTAL > 0){
                    $valorQtdeInstal = $pend10dias->QTDE_INSTAL;
                }else{
                    $valorQtdeInstal = 0;
                }
                
                $resQtdeInstal[] = $valorQtdeInstal;
                
                if($pend10dias->QTDE_CANCEL > 0){
                    $valorQtdeCancel = $pend10dias->QTDE_CANCEL;
                }else{
                    $valorQtdeCancel = 0;
                }
                
                $resQtdeCancel[] = $valorQtdeCancel;
                
                if($pend10dias->QTDE_PEND > 0){
                    $valorQtdePend = $pend10dias->QTDE_PEND;
                }else{
                    $valorQtdePend = 0;
                }
                
                $resQtdePend[] = $valorQtdePend;
                
                $diaMes[] = $pend10dias->DIA_MES;
                
            }
        
            $resDados['dados'] = array('diaMes'=>$diaMes, 'Qtd. Vendas'=>$resQtdeVendas, 'Qtd. Instalação'=>$resQtdeInstal, 'Qtd. Cancelamento' => $resQtdeCancel, 'Qtd. Pendência' => $resQtdePend);
        
        }else{
            
            $resDados['dados'] = array('diaMes'=>'Nenhum', 'Qtd. Vendas'=> 0, 'Qtd. Instalação'=>0, 'Qtd. Cancelamento' => 0, 'Qtd. Pendência' => 0);
            
        }
        
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::pendenciaInstalacaoMesAmes()
	 * 
     * Pega as pendências de instalação mês à mês
     * 
	 */
    public function pendenciaInstalacaoMesAmes($permissor = '0', $servico = '0'){
        
        $this->load->model('dashboard/Dashboard_model','dashboard');
        
        $pendenciaMesAmes = $this->dashboard->pendenciasInstalacaoMesAmes($permissor, $servico);

        if($pendenciaMesAmes){
        
            foreach($pendenciaMesAmes as $pendMaM){
                
                if($pendMaM->QTDE_VENDAS > 0){
                    $valorQtdeVendas = $pendMaM->QTDE_VENDAS;
                }else{
                    $valorQtdeVendas = 0;
                }
                
                $resQtdeVendas[] = $valorQtdeVendas;
                
                if($pendMaM->QTDE_INSTAL > 0){
                    $valorQtdeInstal = $pendMaM->QTDE_INSTAL;
                }else{
                    $valorQtdeInstal = 0;
                }
                
                $resQtdeInstal[] = $valorQtdeInstal;
                
                if($pendMaM->QTDE_CANCEL > 0){
                    $valorQtdeCancel = $pendMaM->QTDE_CANCEL;
                }else{
                    $valorQtdeCancel = 0;
                }
                
                $resQtdeCancel[] = $valorQtdeCancel;
                
                if($pendMaM->QTDE_PEND > 0){
                    $valorQtdePend = $pendMaM->QTDE_PEND;
                }else{
                    $valorQtdePend = 0;
                }
                
                $resQtdePend[] = $valorQtdePend;
                
                $mesAno[] = $pendMaM->MES_ANO;
                
            }
        
            $resDados['dados'] = array('mesAno'=>$mesAno, 'Qtd. Vendas'=>$resQtdeVendas, 'Qtd. Instalação'=>$resQtdeInstal, 'Qtd. Cancelamento' => $resQtdeCancel, 'Qtd. Pendência' => $resQtdePend);
        
        }else{
            
            $resDados['dados'] = array('mesAno'=>'Nenhum', 'Qtd. Vendas'=> 0, 'Qtd. Instalação'=>0, 'Qtd. Cancelamento' => 0, 'Qtd. Pendência' => 0);
            
        }
        
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::statusCobranca()
	 * 
     * Pega os dados dos tipos de cobranças
     * 
	 */ 
    public function statusCobranca($mesAno, $tipo){
        
        $this->load->model('dashboard/Dashboard_model','dashboard');
        
        $ciclos = $this->dashboard->comboCicloCobranca($mesAno, $tipo);
        
        if($ciclos){
        
            foreach($ciclos as $ci){
                
                $todosCiclos[] = $ci->CICLO;
                
                $dcc  = $this->dashboard->statusCobranca($mesAno, $ci->CICLO, $tipo);
                
                if($dcc[0]->PAGO > 0){
                    $dccPago = $dcc[0]->PAGO;
                }else{
                    $dccPago = 0;
                }            
                $resDccPago[] = $dccPago;
                
                if($dcc[0]->ENVIADO > 0){
                    $dccEnviado = $dcc[0]->ENVIADO;
                }else{
                    $dccEnviado = 0;
                }
                $resDccEnviado[] = $dccEnviado;
                
                if($dcc[0]->NAO_ENVIADO > 0){
                    $dccNaoEnviado = $dcc[0]->NAO_ENVIADO;
                }else{
                    $dccNaoEnviado = 0;
                }
                $resDccNaoEnviado[] = $dccNaoEnviado;
                
                if($dcc[0]->REJEITADO > 0){
                    $dccRejeitado = $dcc[0]->REJEITADO;
                }else{
                    $dccRejeitado = 0;
                }
                $resDccRejeitado[] = $dccRejeitado;
                
                if($dcc[0]->NAO_GERADO > 0){
                    $dccNaoGerado = $dcc[0]->NAO_GERADO;
                }else{
                    $dccNaoGerado = 0;
                }
                $resDccNaoGerado[] = $dccNaoGerado;
                
                if($dcc[0]->NAO_RETORNADO > 0){
                    $dccNaoRetornado = $dcc[0]->NAO_RETORNADO;
                }else{
                    $dccNaoRetornado = 0;
                }
                $resDccNaoRetornado[] = $dccNaoRetornado;
                
                if($dcc[0]->REGISTRADO > 0){
                    $dccRegistrado = $dcc[0]->REGISTRADO;
                }else{
                    $dccRegistrado = 0;
                }
                $resDccRegistrado[] = $dccRegistrado;                                
                
            }
            
            $resDados['dados']['data'] = $todosCiclos;
            
            if($tipo == 'O'){ # BOLETO
                $resDados['dados']['Pago'] = $resDccPago;
                $resDados['dados']['Registrado'] = $resDccRegistrado;
                $resDados['dados']['Nao retornado'] = $resDccNaoRetornado;
                $resDados['dados']['Nao Enviado'] = $resDccNaoEnviado;
            }
            
            if($tipo == 'T'){ # CARTÃO
                $resDados['dados']['Nao Enviado'] = $resDccNaoEnviado;
                $resDados['dados']['Rejeitado'] = $resDccRejeitado;
                $resDados['dados']['Pago'] = $resDccPago;
            }
            
            if($tipo == 'B'){ # DCC
                $resDados['dados']['Rejeitado'] = $resDccRejeitado;
                $resDados['dados']['Pago'] = $resDccPago;
                $resDados['dados']['Nao Enviado'] = $resDccNaoEnviado;
                $resDados['dados']['Enviado'] = $resDccEnviado;
            }
            /*
            $resDados['dados'] = array(
                'data' => $todosCiclos, 
                'Pago' => $resDccPago, 'Enviado' => $resDccEnviado, 'Nao Enviado' => $resDccNaoEnviado, 'Rejeitado' => $resDccRejeitado, 'Nao gerado' => $resDccNaoGerado, 'Nao retornado' => $resDccNaoRetornado, 'Registrado' => $resDccRegistrado
                                        );
            */
        }else{
            /*
            $resDados['dados'] = array(
                'data' => 0, 
                'Pago' => 0, 'Enviado' => 0, 'Nao Enviado' => 0, 'Rejeitado' => 0, 'Nao gerado' => 0, 'Nao retornado' => 0, 'Registrado' => 0
                                        );
            */
            
            if($tipo == 'O'){ # BOLETO
                $resDados['dados']['Pago'] = 0;
                $resDados['dados']['Registrado'] = 0;
                $resDados['dados']['Nao retornado'] = 0;
                $resDados['dados']['Nao Enviado'] = 0;
            }
            
            if($tipo == 'T'){ # CARTÃO
                $resDados['dados']['Nao Enviado'] = 0;
                $resDados['dados']['Rejeitado'] = 0;
                $resDados['dados']['Pago'] = 0;
            }
            
            if($tipo == 'B'){ # DCC
                $resDados['dados']['Rejeitado'] = 0;
                $resDados['dados']['Pago'] = 0;
                $resDados['dados']['Nao Enviado'] = 0;
                $resDados['dados']['Enviado'] = 0;
            }
            
        }
        
        $this->load->view('view_json',$resDados);  
        
    }
    
    /**
	 * Ajax::statusCicloCobranca()
	 * 
     * Pega os dados dos tipos de cobranças por ciclo
     * 
	 */    
    public function statusCicloCobranca($mesAno, $ciclo, $tipo){
        
        $this->load->model('dashboard/Dashboard_model','dashboard');
        
        $bancos = $this->dashboard->bancosCobranca($mesAno, $tipo);
        
        if($bancos){
        
            foreach($bancos as $ba){
                
                $todosBancos[] = $ba->BANCO;
                
                $dcc  = $this->dashboard->statusCicloCobranca($mesAno, $ciclo, $ba->BANCO_BD, $tipo);
                
                if($dcc[0]->PAGO > 0){
                    $dccPago = $dcc[0]->PAGO;
                }else{
                    $dccPago = 0;
                }            
                $resDccPago[] = $dccPago;
                
                if($dcc[0]->ENVIADO > 0){
                    $dccEnviado = $dcc[0]->ENVIADO;
                }else{
                    $dccEnviado = 0;
                }
                $resDccEnviado[] = $dccEnviado;
                
                if($dcc[0]->NAO_ENVIADO > 0){
                    $dccNaoEnviado = $dcc[0]->NAO_ENVIADO;
                }else{
                    $dccNaoEnviado = 0;
                }
                $resDccNaoEnviado[] = $dccNaoEnviado;
                
                if($dcc[0]->REJEITADO > 0){
                    $dccRejeitado = $dcc[0]->REJEITADO;
                }else{
                    $dccRejeitado = 0;
                }
                $resDccRejeitado[] = $dccRejeitado;
                
                if($dcc[0]->NAO_GERADO > 0){
                    $dccNaoGerado = $dcc[0]->NAO_GERADO;
                }else{
                    $dccNaoGerado = 0;
                }
                $resDccNaoGerado[] = $dccNaoGerado;
                
                if($dcc[0]->NAO_RETORNADO > 0){
                    $dccNaoRetornado = $dcc[0]->NAO_RETORNADO;
                }else{
                    $dccNaoRetornado = 0;
                }
                $resDccNaoRetornado[] = $dccNaoRetornado;
                
                if($dcc[0]->REGISTRADO > 0){
                    $dccRegistrado = $dcc[0]->REGISTRADO;
                }else{
                    $dccRegistrado = 0;
                }
                $resDccRegistrado[] = $dccRegistrado;                  
                
            }
            
            $resDados['dados']['data'] = $todosBancos;
            if($tipo == 'O'){ # BOLETO
                $resDados['dados']['Pago'] = $resDccPago;
                $resDados['dados']['Registrado'] = $resDccRegistrado;
                $resDados['dados']['Nao retornado'] = $resDccNaoRetornado;
                $resDados['dados']['Nao Enviado'] = $resDccNaoEnviado;
            }
            
            if($tipo == 'T'){ # CARTÃO
                $resDados['dados']['Nao Enviado'] = $resDccNaoEnviado;
                $resDados['dados']['Rejeitado'] = $resDccRejeitado;
                $resDados['dados']['Pago'] = $resDccPago;
            }
            
            if($tipo == 'B'){ # DCC
                $resDados['dados']['Rejeitado'] = $resDccRejeitado;
                $resDados['dados']['Pago'] = $resDccPago;
                $resDados['dados']['Nao Enviado'] = $resDccNaoEnviado;
                $resDados['dados']['Enviado'] = $resDccEnviado;
            } 
            /*
            $resDados['dados'] = array(
                'data' => $todosBancos, 
                'Pago' => $resDccPago, 'Enviado' => $resDccEnviado, 'Nao Enviado' => $resDccNaoEnviado, 'Rejeitado' => $resDccRejeitado, 'Nao gerado' => $resDccNaoGerado, 'Nao retornado' => $resDccNaoRetornado, 'Registrado' => $resDccRegistrado
                                        );
            */                           
        
        }else{
            /*
            $resDados['dados'] = array(
                'data' => 0, 
                'Pago' => 0, 'Enviado' => 0, 'Nao Enviado' => 0, 'Rejeitado' => 0, 'Nao gerado' => 0, 'Nao retornado' => 0, 'Registrado' => 0
                                        );
            */
            if($tipo == 'O'){ # BOLETO
                $resDados['dados']['Pago'] = 0;
                $resDados['dados']['Registrado'] = 0;
                $resDados['dados']['Nao retornado'] = 0;
                $resDados['dados']['Nao Enviado'] = 0;
            }
            
            if($tipo == 'T'){ # CARTÃO
                $resDados['dados']['Nao Enviado'] = 0;
                $resDados['dados']['Rejeitado'] = 0;
                $resDados['dados']['Pago'] = 0;
            }
            
            if($tipo == 'B'){ # DCC
                $resDados['dados']['Rejeitado'] = 0;
                $resDados['dados']['Pago'] = 0;
                $resDados['dados']['Nao Enviado'] = 0;
                $resDados['dados']['Enviado'] = 0;
            }                              
            
        }
        
        #echo '<pre>'; print_r($resCartaoPago); exit();
        $this->load->view('view_json',$resDados);  
        
    }
    
    /**
	 * Ajax::reajustadosAssinantes()
	 * 
     * Pega a quantidade de assinantes reajustados por mês
     * 
	 */
    public function reajustadosAssinantes(){
        
        $this->load->model('dashboard/Dashboard_model','dashboard');
        
        $resAssinantes = $this->dashboard->reajustadosAssinantes();
        
        if($resAssinantes){
        
            foreach($resAssinantes as $rA){
                
                if($rA->QTDE > 0){
                    $valorQtde = $rA->QTDE;
                }else{
                    $valorQtde = 0;
                }
                
                $resTotalQtde[] = $valorQtde;
                
                $meses[] = $rA->MES;
                
            }
        
            $resDados['dados'] = array('meses'=>$meses, 'Qtde'=>$resTotalQtde);
        
        }else{
            
            $resDados['dados'] = array('meses'=>'Nenhum', 'Qtde'=> 0);
            
        }
        
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::AnatelIndDepartamento()
	 * 
     * Pega os indicadores da Anatel de acordo com o departamento
     * 
	 */
    public function anatelIndDepartamento(){
        
        $this->load->model('anatel/AnatelForm_model','anatelForm');
        
        $resDados['dados'] = $this->anatelForm->anatelIndDepartamento($this->input->post('cd_departamento'));
        
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::anatelGrupoIndicador()
	 * 
     * Pega os indicadores da Anatel de acordo com o departamento
     * 
	 */
    public function anatelGrupoIndicador(){
        
        $this->load->model('anatel/AnatelForm_model','anatelForm');
        
        $resDados['dados'] = $this->anatelForm->anatelGrupoIndicador($this->input->post('cd_sistema'));
        
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::tipoXmlAnatelFrm()
	 * 
     * Pega o tipo de XML de acordo com o tipo de fomulário
     * 
	 */
    public function tipoXmlAnatelFrm(){
        
        $this->load->model('anatel/AnatelForm_model','anatelForm');
        
        $resDados['dados'] = $this->anatelForm->tiposXml('6');
        
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::anatelUnidadesNaoResponderam()
	 * 
     * Pega as unidade que não responderam
     * 
	 */
    public function anatelUnidadesNaoResponderam(){
        
        $this->load->model('anatel/AnatelForm_model','anatelForm');
        
        if($this->input->post('mes_ano')){
            #$resDados['dados'] = $this->anatelForm->formNaoRespondidos();
            $resDados['dados'] = $this->anatelForm->indicadoresRespondidos('NAO');
        }else{
            $resDados['dados'] = false;
        }
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::comboCicloStatusBoleto()
	 * 
     * Pega os dados para montar a combo de ciclos
     * 
     * @param $mesAno Parâmetro mês ano para consulta
     * 
	 */
    public function comboCicloStatusCobranca($mesAno, $tipo){
        
        $this->load->model('dashboard/Dashboard_model','dashboard');
        
        $resDados['dados'] = $this->dashboard->comboCicloStatusCobranca($mesAno, $tipo);
        
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::comboStatusCobranca()
	 * 
     * Pega os meses/anos existentes para o tipo de cobrança informado
     * 
     * @param $tipo Tipo de cobrança solicitada
     * 
	 */
    public function comboStatusCobranca($tipo){
        
        /*
        $tipo = “B” => Debito em Conta;
        $tipo = “O” => Boleto;
        $tipo = “T” => Cartão.
        */
        
        $this->load->model('dashboard/Dashboard_model','dashboard');
        
        $resDados['dados'] = $this->dashboard->comboStatusCobranca($tipo);
        
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::usuariosDepartamento()
	 * 
     * Pega os usuários de acordo com o departamento
     * 
	 */
    public function usuariosDepartamento(){
        
        $this->load->model('anatel/AnatelForm_model', 'anatelForm');
        $resDados['dados'] = $this->anatelForm->usuariosDepartamento();
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::linhasAssociadasDDD()
	 * 
     * Pega as linhas associadas ao DDD informado
     * 
	 */
    public function linhasAssociadasDDD(){
        
        $this->load->model('telefonia/linha_model','linha');
        $resDados['dados'] = $this->linha->linhasAssociadasDDD();
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::dadosEmprestimos()
	 * 
     * Pega os dados do empréstimo
     * 
	 */
    public function dadosEmprestimos(){

        $this->load->model('telefonia/emprestimo_model','emprestimo');
        $resDados['dados'] = $this->emprestimo->dadosEmprestimos();
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::servicosLinha()
	 * 
     * Pega os serviços da linha
     * 
	 */
    public function servicosLinha(){
        
        $this->load->model('telefonia/emprestimo_model','emprestimo');
        $resDados['dados'] = $this->emprestimo->servicosLinha();
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::associaLinhaEmprestimo()
	 * 
     * Associa linha(s) ao empréstimo
     * 
	 */
    public function associaLinhaEmprestimo(){
        
        $this->load->model('telefonia/emprestimo_model','emprestimo');
        
        foreach($_POST['selecionados'] as $sel){
        
            $extracao[] = str_replace('dado_','',$sel);
            
        }
        
        try{
        
            $resposta = $this->emprestimo->associaLinhaEmprestimo($extracao);
            
        }catch( Exception $e ){
                
            log_message('error', $e->getMessage());
            
        }
        
        if($resposta){
            echo 'Linha associada ao empr&eacute;stimo com sucesso.';
        }else{
            echo 'Erro ao associar linha ao empr&eacute;stimo.';
        }
        
    }
    
    /**
	 * Ajax::desassociaLinhaEmprestimo()
	 * 
     * Associa linha(s) ao empréstimo
     * 
	 */
    public function desassociaLinhaEmprestimo(){
        
        $this->load->model('telefonia/emprestimo_model','emprestimo');
        
        foreach($_POST['selecionados'] as $sel){
        
            $extracao[] = str_replace('dado_','',$sel);
            
        }
        
        try{
        
            $resposta = $this->emprestimo->atualizaStatusLinha($extracao, 'E');
            
        }catch( Exception $e ){
                
            log_message('error', $e->getMessage());
            
        }
        /*
        if($resposta){
            echo 'Linha associada ao empr&eacute;stimo com sucesso.';
        }else{
            echo 'Erro ao associar linha ao empr&eacute;stimo.';
        }
        */
    }
    
    /**
	 * Ajax::dadosTermo()
	 * 
     * Pega os dados do termo
     * 
	 */
    public function dadosTermo(){
        
        $this->load->model('telefonia/emprestimo_model','emprestimo');
        $resDados['dados'] = $this->emprestimo->termo();
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::termoUsuario()
	 * 
     * Pega o termo do usuário para exibição
     * 
	 */
    public function termoUsuario(){
        
        $_POST['cd_operadora'] = 8;
        $_POST['cd_usuario'] = 4373;
        $_POST['cd_emprestimo'] = 67;
        
        $this->load->library('Util', '', 'util');
        $this->load->library('Termo', '', 'termo');
        $this->load->model('telefonia/emprestimo_model','emprestimo');
        $this->load->model('telefonia/linha_model','linha');
        $declaracao = $this->termo->telefoniaDeclaracao($this->input->post('cd_operadora'));
        $regulamento = $this->termo->telefoniaRegulamento($this->input->post('cd_operadora'));
        
        $dadosTermo = $this->emprestimo->termoUsuario($this->input->post('cd_usuario'));
        $acessoriosTermo =  $this->emprestimo->acessoriosTermo($this->input->post('cd_emprestimo'));
        $servicosLinha = $this->linha->servicosLinha($dadosTermo[0]->cd_telefonia_linha, '');
        
        $servicos = '';
        $servicosDescricao = '';
        foreach($servicosLinha as $servLinha){
            $servicos .= $servLinha->nome.'<br>';
            $servicosDescricao .= '•	'.$servLinha->descricao.'<br>';
        }
        
        $acessorios = array();
        foreach($acessoriosTermo as $aT){
            $acessorios[] = $aT->nome;
        }
        $acessorios = implode(', ', $acessorios);
        
        $data = explode('/', $dadosTermo[0]->data_termo); 
        $data = $data[0].' de '.$this->util->mesExtenso($data[1]).' de '.$data[2];
        
        $declaracaoFinal = str_replace("#MODELO", $dadosTermo[0]->marca.' - '.$dadosTermo[0]->modelo, $declaracao);
        $declaracaoFinal = str_replace("#LINHA", $dadosTermo[0]->ddd.' - '.$dadosTermo[0]->numero, $declaracaoFinal);
        $declaracaoFinal = str_replace("#SERVICOS", $servicos, $declaracaoFinal);
        $declaracaoFinal = str_replace("#IMEI", $dadosTermo[0]->imei, $declaracaoFinal);
        $declaracaoFinal = str_replace("#ACESSORIO", $acessorios, $declaracaoFinal);
        $declaracaoFinal = str_replace("#NOME", $dadosTermo[0]->nome_usuario, $declaracaoFinal);
        $declaracaoFinal = str_replace("#CARGO", $dadosTermo[0]->nome_cargo, $declaracaoFinal);
        $declaracaoFinal = str_replace("#RG", $dadosTermo[0]->rg_usuario, $declaracaoFinal);
        $declaracaoFinal = str_replace("#CPF", $dadosTermo[0]->cpf_usuario, $declaracaoFinal);
        $declaracaoFinal = str_replace("#DATA", $data, $declaracaoFinal);
        
        $regulamentoFinal = str_replace("#DATA", $data, $regulamento);
        $regulamentoFinal = str_replace("#SERVICOS_DESC", $servicosDescricao, $regulamentoFinal);
        
        if($dadosTermo[0]->aceite_termo){
            
            $resDados['dados']['respondido'] = true;
            if($dadosTermo[0]->aceite_termo == 'S'){
                $resposta = 'Termo aceito em ';
            }else{
                $resposta = 'Termo n&atilde;o aceito em ';
            }
            $resDados['dados']['dataResposta'] = '<strong>'.$resposta.$dadosTermo[0]->data_aceite_termo.' &agrave;s '.$dadosTermo[0]->hora_aceite.'.</strong><br><br>'; 
        
        }else{
            
            $resDados['dados']['respondido'] = false; 
            $resDados['dados']['dataResposta'] = false;
            
        }
        
        $resDados['dados']['declaracao'] = $declaracaoFinal;
        $resDados['dados']['regulamento'] = $regulamentoFinal;
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::acessorios()
	 * 
     * Pega os acessórios e se o id do empréstimo for informado 
     * informa também se o acessórios esta contido no empréstimo
     * 
	 */
    public function acessorios(){
        
        $this->load->model('telefonia/emprestimo_model','emprestimo');
        $resDados['dados'] = $this->emprestimo->acessorios($this->input->post('cd_emprestimo'));
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::servicosDados()
	 * 
     * Pega os dados dos serviços
     * 
	 */
    public function servicosDados($cd = false){
        
        $this->load->model('telefonia/servico_model','servico');
        $resDados['dados'] = $this->servico->servicos($cd);
        #echo '<pre>'; print_r($resDados['dados']); exit();
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::carregaNodes()
	 * 
     * Pega os nodes de acordo com o permissor
     * 
	 */
    public function carregaNodes(){
        
        $this->load->model('ura/ura_model','ura');
        $resDados['dados'] = $this->ura->nodes($this->input->post('permissor'));
        #echo '<pre>'; print_r($resDados['dados']); exit();
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::mesesLigacaoAtivas()
	 * 
     * Pega os meses que possuem ligações ativas (CallCenter + Asterisk)
     * 
	 */
    public function mesesLigacao(){
        
        $this->load->model('telefonia/fatura_model','faturaModel');
        $resDados['dados'] = $this->faturaModel->mesesDisponiveis($this->input->post('tipo'));
        #echo '<pre>'; print_r($resDados['dados']); exit();
        $this->load->view('view_json',$resDados);
        
    }
    
    public function testeChat(){
        
        $user[] = array('id' => 1,'nome' => 'Paulo Costa', 'logado' => 'S', 'status' => 'ON', 'hora_status' => '2015-12-03 11:33:45');
        $user[] = array('id' => 2,'nome' => 'Rafael Gomes', 'logado' => 'S', 'status' => 'OCU', 'hora_status' => '2015-12-03 11:00:12');
        $resDados['dados'] = $user;
        $this->load->view('view_json',$resDados);
    }
    
    public function teste(){
        
        try{
        
        $this->load->library('Util', '', 'util');  
        $this->util->limpaArquivos();
        $dir = PASTA_REDE_SISTEMA.'movel';
        $arquivos = $this->util->buscaArquivosDiretorios('./temp');
        
        }catch( Exception $e ){
            
            echo $e->getMessage();
        }
        echo '<pre>'; print_r($arquivos);
        exit();
        
    }
    
    public function teste_db(){
        
        $fields = $this->db->field_data('adminti.departamento');

        foreach ($fields as $field)
        {
           echo $field->name; echo '<br>';
           echo $field->type; echo '<br>';
           echo $field->max_length; echo '<br>';
           echo $field->primary_key; echo '<br>';
        }
        exit();
    }
    
    
    public function testePhpMailer(){
        
        $this->load->library("My_phpmailer", '', 'phpMailer');
        
        $mail = $this->phpMailer->inicializar();
        $mail->From = 'naoresponda@simtv.com.br'; // Remetente
        $mail->FromName = utf8_decode('SIM'); // Remetente nome
        
        $mail->IsHTML(true);
        
        $mail->Subject = utf8_decode('Teste final'); // assunto
        $mail->Body = utf8_decode('Conclusão'); // Mensagem
        #$mail->AltBody = "Corpo em texto puro.";
        #$mail->AddAddress('tiagotsc@oi.com.br','Tiago Silva Costa'); // Email e nome do destino
        #$mail->AddReplyTo("response@email.com","Nome Completo"); //Para que a resposta será enviada.
        /*Também é possível adicionar anexos.*/
        #$mail->AddAttachment("images/phpmailer.gif");
        #$mail->AddAttachment("images/phpmailer_mini.gif");
        #$mail->ConfirmReadingTo = 'tiago.costa@simtv.com.br';
        
        if($mail->Send()){
            echo 'Enviado<br><br>';
        }else{
            echo 'Nao enviado<br><br>';
            echo $mail->ErrorInfo;
        }
        exit();
    }
    
    public function testeEmail(){
        
        $this->load->library("My_phpmailer", '', 'phpMailer');
        $this->phpMailer->testeEnvio();
        exit();
        
    }
    
    public function envioEmailUtil(){
        
        $this->load->library('Util', '', 'util'); 
        $envio = $this->util->enviaEmail('Ferramenta de negocios', 'naoresponda@simtv.com.br','tiago.costa@simtv.com.br','Titulo teste', 'Texto teste');
        if($envio){
            echo 'Enviou';
        }else{
            echo 'Nao enviou';
        }
    }
    
    /*
    public function teste(){
        
        $this->load->library('Util', '', 'util');  
        
        #print_r($this->util->telefoniaPeriodo('09-2016'));
        
        echo $this->util->satvaCalculoPorcentagem(451,452);
        
    }*/
                
}
