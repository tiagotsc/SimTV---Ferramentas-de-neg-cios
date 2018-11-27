<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
setlocale(LC_ALL, 'pt_BR.UTF-8');
class ConvenioicmsLayout{

     /**
      * ConvenioicmsLayout::__construct()
      * 
      * @return
      */
     public function __construct()
    {
        #parent::Model();
        #$this->load->model('Financeiro_model','financeiro');
        #$this->SE =& get_instance();
        #$this->SE->load->library('session');        
        $this->CI =& get_instance();
        #$this->CI->load->model('ArquivoCobranca_model','ArquivoCobranca');   
        #$this->CI->load->model('Financeiro_model','financeiro');   
        #$this->CI->load->library('Util', '', 'util');  
    }
    
    public function arquivoUmCampos(){
        
        $posicao = 0;
        # CNPJ ou CPF
        $dados[$posicao]['qtd'] = 14;
        $dados[$posicao]['campo'] = 'cnpj_cpf'; 
        $dados[$posicao]['formato'] = 'N';
        $dados[$posicao]['inicio'] = '1';
        $dados[$posicao]['fim'] = '14';
                                        
        # UF
        $dados[++$posicao]['qtd'] = 2;
        $dados[$posicao]['campo'] = 'cnpj_cpf'; 
        $dados[$posicao]['formato'] = 'X';
        $dados[$posicao]['inicio'] = '15';
        $dados[$posicao]['fim'] = '16';
        
        # Classe do Consumo ou Tipo de Assinante  
        $dados[++$posicao]['qtd'] = 1;
        $dados[$posicao]['campo'] = 'tipo_assinante'; 
        $dados[$posicao]['formato'] = 'N';
        $dados[$posicao]['inicio'] = '17';
        $dados[$posicao]['fim'] = '17';   
        
        # Fase ou Tipo de Utilização
        $dados[++$posicao]['qtd'] = 1;
        $dados[$posicao]['campo'] = 'tipo_utilizacao'; 
        $dados[$posicao]['formato'] = 'N';
        $dados[$posicao]['inicio'] = '18';
        $dados[$posicao]['fim'] = '18';  
        
        # Grupo de Tensão
        $dados[++$posicao]['qtd'] = 2;
        $dados[$posicao]['campo'] = 'grupo_tensao'; 
        $dados[$posicao]['formato'] = 'N';
        $dados[$posicao]['inicio'] = '19';
        $dados[$posicao]['fim'] = '20';    
        
        # Data de Emissão
        $dados[++$posicao]['qtd'] = 8;
        $dados[$posicao]['campo'] = 'data_emissao'; 
        $dados[$posicao]['formato'] = 'N';
        $dados[$posicao]['inicio'] = '21';
        $dados[$posicao]['fim'] = '28';  
        
        # Modelo
        $dados[++$posicao]['qtd'] = 2;
        $dados[$posicao]['campo'] = 'modelo'; 
        $dados[$posicao]['formato'] = 'N';
        $dados[$posicao]['inicio'] = '29';
        $dados[$posicao]['fim'] = '30';  
        
        # Série
        $dados[++$posicao]['qtd'] = 3;
        $dados[$posicao]['campo'] = 'modelo'; 
        $dados[$posicao]['formato'] = 'X';
        $dados[$posicao]['inicio'] = '31';
        $dados[$posicao]['fim'] = '33';  
        
        # Número
        $dados[++$posicao]['qtd'] = 9;
        $dados[$posicao]['campo'] = 'numero'; 
        $dados[$posicao]['formato'] = 'N';
        $dados[$posicao]['inicio'] = '34';
        $dados[$posicao]['fim'] = '42';  
        
        # CFOP
        $dados[++$posicao]['qtd'] = 4;
        $dados[$posicao]['campo'] = 'cfop'; 
        $dados[$posicao]['formato'] = 'N';
        $dados[$posicao]['inicio'] = '43';
        $dados[$posicao]['fim'] = '46';  
        
        # Nº de ordem do Item
        $dados[++$posicao]['qtd'] = 3;
        $dados[$posicao]['campo'] = 'n_ordem_item'; 
        $dados[$posicao]['formato'] = 'N';
        $dados[$posicao]['inicio'] = '47';
        $dados[$posicao]['fim'] = '49';  
        
        # Código do item
        $dados[++$posicao]['qtd'] = 10;
        $dados[$posicao]['campo'] = 'codigo_item'; 
        $dados[$posicao]['formato'] = 'X';
        $dados[$posicao]['inicio'] = '50';
        $dados[$posicao]['fim'] = '59';  
        
        # Descrição do item
        $dados[++$posicao]['qtd'] = 40;
        $dados[$posicao]['campo'] = 'desc_item'; 
        $dados[$posicao]['formato'] = 'X';
        $dados[$posicao]['inicio'] = '60';
        $dados[$posicao]['fim'] = '99';  
        
        # Código de classificação do item
        $dados[++$posicao]['qtd'] = 4;
        $dados[$posicao]['campo'] = 'cod_class_item'; 
        $dados[$posicao]['formato'] = 'N';
        $dados[$posicao]['inicio'] = '100';
        $dados[$posicao]['fim'] = '103';  
        
        # Unidade
        $dados[++$posicao]['qtd'] = 6;
        $dados[$posicao]['campo'] = 'unidade'; 
        $dados[$posicao]['formato'] = 'X';
        $dados[$posicao]['inicio'] = '104';
        $dados[$posicao]['fim'] = '109';  
        
        # Quantidade contratada (com 3 decimais)
        $dados[++$posicao]['qtd'] = 12;
        $dados[$posicao]['campo'] = 'qtd_contr'; 
        $dados[$posicao]['formato'] = 'N';
        $dados[$posicao]['inicio'] = '110';
        $dados[$posicao]['fim'] = '121';  
        
        # Quantidade medida (com 3 decimais)
        $dados[++$posicao]['qtd'] = 12;
        $dados[$posicao]['campo'] = 'qtd_med'; 
        $dados[$posicao]['formato'] = 'N';
        $dados[$posicao]['inicio'] = '122';
        $dados[$posicao]['fim'] = '133';  
        
        # Total (com 2 decimais)
        $dados[++$posicao]['qtd'] = 11;
        $dados[$posicao]['campo'] = 'total'; 
        $dados[$posicao]['formato'] = 'N';
        $dados[$posicao]['inicio'] = '134';
        $dados[$posicao]['fim'] = '144';  
        
        # Desconto / Redutores (com 2 decimais)
        $dados[++$posicao]['qtd'] = 11;
        $dados[$posicao]['campo'] = 'desc_red'; 
        $dados[$posicao]['formato'] = 'N';
        $dados[$posicao]['inicio'] = '145';
        $dados[$posicao]['fim'] = '155';  
        
        # Acréscimos e Despesas Acessórias (com 2 decimais)
        $dados[++$posicao]['qtd'] = 11;
        $dados[$posicao]['campo'] = 'acre_des_ace'; 
        $dados[$posicao]['formato'] = 'N';
        $dados[$posicao]['inicio'] = '156';
        $dados[$posicao]['fim'] = '166';  
        
        # BC ICMS (com 2 decimais)
        $dados[++$posicao]['qtd'] = 11;
        $dados[$posicao]['campo'] = 'bc_icms'; 
        $dados[$posicao]['formato'] = 'N';
        $dados[$posicao]['inicio'] = '167';
        $dados[$posicao]['fim'] = '177';  
        
        # ICMS (com 2 decimais)
        $dados[++$posicao]['qtd'] = 11;
        $dados[$posicao]['campo'] = 'icms'; 
        $dados[$posicao]['formato'] = 'N';
        $dados[$posicao]['inicio'] = '178';
        $dados[$posicao]['fim'] = '188';  
        
        # Operações Isentas ou não tributadas (com 2 decimais)
        $dados[++$posicao]['qtd'] = 11;
        $dados[$posicao]['campo'] = 'oper_isent'; 
        $dados[$posicao]['formato'] = 'N';
        $dados[$posicao]['inicio'] = '189';
        $dados[$posicao]['fim'] = '199';  
        
        # Outros valores (com 2 decimais)
        $dados[++$posicao]['qtd'] = 11;
        $dados[$posicao]['campo'] = 'grupo_tensao'; 
        $dados[$posicao]['formato'] = 'N';
        $dados[$posicao]['inicio'] = '200';
        $dados[$posicao]['fim'] = '210';  
        
        # Alíquota do ICMS (com 2 decimais)
        $dados[++$posicao]['qtd'] = 4;
        $dados[$posicao]['campo'] = 'aliq_icms'; 
        $dados[$posicao]['formato'] = 'N';
        $dados[$posicao]['inicio'] = '211';
        $dados[$posicao]['fim'] = '214';  
        
        # Situação
        $dados[++$posicao]['qtd'] = 1;
        $dados[$posicao]['campo'] = 'situacao'; 
        $dados[$posicao]['formato'] = 'X';
        $dados[$posicao]['inicio'] = '215';
        $dados[$posicao]['fim'] = '215';  
        
        # Ano e Mês de referência de apuração
        $dados[++$posicao]['qtd'] = 4;
        $dados[$posicao]['campo'] = 'anomes_ref_apur'; 
        $dados[$posicao]['formato'] = 'X';
        $dados[$posicao]['inicio'] = '216';
        $dados[$posicao]['fim'] = '219';      
        
    }
    
    public function getEmpresasAutorizadas(){
        
        $empresas[] = 'TIM Celular S.A';
        
        return $empresas;
        
    }
    # Tipo 0
    public function header(){
        
        $posicao = 0;
        
        # Tipo de registro
        $dados[$posicao]['posicao'] = 0; 
        $dados[$posicao]['qtd'] = 1;
        $dados[$posicao]['campo'] = 'tipo'; 
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Tipo de Registros - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        
        # Identificação da empresa
        $dados[++$posicao]['posicao'] = 21; 
        $dados[$posicao]['qtd'] = 15;
        $dados[$posicao]['campo'] = 'empresa';
        $dados[$posicao]['formatacao'] = ''; 
        $dados[$posicao]['descricao'] = 'Identifica&ccedil;&atilde;o da empresa - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        
        return $dados;
    }
    
    # Tipo 1
    /**
      * FebrabanLayout::resumo()
      * 
      * Apresenta informações do cliente e dos recursos faturados na conta em questão
      * 
      * @return
      */
    public function resumo(){
        
        $posicao = 0;
        
        # Tipo de registro
        $dados[$posicao]['posicao'] = 0; 
        $dados[$posicao]['qtd'] = 1;
        $dados[$posicao]['campo'] = 'tipo'; 
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Tipo de Registros - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        
        # Data de vencimento
        $dados[++$posicao]['posicao'] = 38; 
        $dados[$posicao]['qtd'] = 8; 
        $dados[$posicao]['campo'] = 'dt_vencimento';
        $dados[$posicao]['formatacao'] = 'data';
        $dados[$posicao]['descricao'] = 'Data de vencimento - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Data de emissão                                
        $dados[++$posicao]['posicao'] = 46;
        $dados[$posicao]['qtd'] = 8;
        $dados[$posicao]['campo'] = 'dt_emissao';
        $dados[$posicao]['formatacao'] = 'data';
        $dados[$posicao]['descricao'] = 'Data de emiss&atilde;o - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Identificador único do recurso (NRC)                                        
        $dados[++$posicao]['posicao'] = 54;
        $dados[$posicao]['qtd'] = 25;
        $dados[$posicao]['campo'] = 'nrc';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Identificador &Uacute;nico do Recurso (NRC) - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Localidade
        $dados[++$posicao]['posicao'] = 84;
        $dados[$posicao]['qtd'] = 25;
        $dados[$posicao]['campo'] = 'localidade';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Nome da localidade - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # DDD                                
        $dados[++$posicao]['posicao'] = 109;
        $dados[$posicao]['qtd'] = 2;
        $dados[$posicao]['campo'] = 'ddd';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'DDD - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Nº do telefone                               
        $dados[++$posicao]['posicao'] = 111;
        $dados[$posicao]['qtd'] = 10;
        $dados[$posicao]['campo'] = 'telefone';
        $dados[$posicao]['formatacao'] = 'celular';
        $dados[$posicao]['descricao'] = 'Nº do telefone - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Tipo de serviço                
        $dados[++$posicao]['posicao'] = 121;
        $dados[$posicao]['qtd'] = 4;
        $dados[$posicao]['campo'] = 'tipo_servico';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Tipo de servi&ccedil;o - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Descrição do tipo de serviço                                
        $dados[++$posicao]['posicao'] = 125;
        $dados[$posicao]['qtd'] = 35;
        $dados[$posicao]['campo'] = 'desc_tipo_servico';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Descri&ccedil;&atilde;o do tipo de servi&ccedil;o - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        
        # Característica do recurso                                
        $dados[++$posicao]['posicao'] = 160;
        $dados[$posicao]['qtd'] = 15;
        $dados[$posicao]['campo'] = 'carac_recurso';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Caracter&iacute;stica do recurso  - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Início do período da assinatura                                
        $dados[++$posicao]['posicao'] = 186;
        $dados[$posicao]['qtd'] = 8;
        $dados[$posicao]['campo'] = 'ini_assinatura';
        $dados[$posicao]['formatacao'] = 'data';
        $dados[$posicao]['descricao'] = 'In&iacute;cio do per&iacute;odo da assinatura - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Fim do período da assinatura                                
        $dados[++$posicao]['posicao'] = 194;
        $dados[$posicao]['qtd'] = 8;
        $dados[$posicao]['campo'] = 'fim_assinatura';
        $dados[$posicao]['formatacao'] = 'data';
        $dados[$posicao]['descricao'] = 'Fim do per&iacute;odo da assinatura - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Início do período do serviço                                
        $dados[++$posicao]['posicao'] = 202;
        $dados[$posicao]['qtd'] = 8;
        $dados[$posicao]['campo'] = 'ini_servico';
        $dados[$posicao]['formatacao'] = 'data';
        $dados[$posicao]['descricao'] = 'In&iacute;cio do per&iacute;odo do servi&ccedil;o - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        
        # Fim do período do serviço                                
        $dados[++$posicao]['posicao'] = 210;
        $dados[$posicao]['qtd'] = 8;
        $dados[$posicao]['campo'] = 'fim_servico';
        $dados[$posicao]['formatacao'] = 'data';
        $dados[$posicao]['descricao'] = 'Fim do per&iacute;odo do servi&ccedil;o - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Unidade de consumo                                
        $dados[++$posicao]['posicao'] = 218;
        $dados[$posicao]['qtd'] = 5;
        $dados[$posicao]['campo'] = 'uni_consumo';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Unidade de consumo - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Sinal valor consumo                                
        $dados[++$posicao]['posicao'] = 230;
        $dados[$posicao]['qtd'] = 1;
        $dados[$posicao]['campo'] = 'sinal_valor_consumo';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Sinal valor consumo - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Valor consumo                                
        $dados[++$posicao]['posicao'] = 231;
        $dados[$posicao]['qtd'] = 13;
        $dados[$posicao]['campo'] = 'valor_consumo';
        $dados[$posicao]['formatacao'] = 'moeda';
        $dados[$posicao]['descricao'] = 'Valor consumo - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        
        # Sinal assinatura                                
        $dados[++$posicao]['posicao'] = 244;
        $dados[$posicao]['qtd'] = 1;
        $dados[$posicao]['campo'] = 'sinal_assinatura';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Sinal assinatura - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Valor assinatura                                
        $dados[++$posicao]['posicao'] = 245;
        $dados[$posicao]['qtd'] = 13;
        $dados[$posicao]['campo'] = 'valor_assinatura';
        $dados[$posicao]['formatacao'] = 'moeda';
        $dados[$posicao]['descricao'] = 'Valor assinatura - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Alíquota (Percentual)                         
        $dados[++$posicao]['posicao'] = 258;
        $dados[$posicao]['qtd'] = 2;
        $dados[$posicao]['campo'] = 'aliquota_porcentagem';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Al&iacute;quota (Percentual) - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Sinal ICMS                                
        $dados[++$posicao]['posicao'] = 260;
        $dados[$posicao]['qtd'] = 1;
        $dados[$posicao]['campo'] = 'sinal_icms';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Sinal ICMS - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Valor ICMS                                
        $dados[++$posicao]['posicao'] = 261;
        $dados[$posicao]['qtd'] = 13;
        $dados[$posicao]['campo'] = 'valor_icms';
        $dados[$posicao]['formatacao'] = 'moeda';
        $dados[$posicao]['descricao'] = 'Valor ICMS - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Sinal valor total de outros impostos                                
        $dados[++$posicao]['posicao'] = 274;
        $dados[$posicao]['qtd'] = 1;
        $dados[$posicao]['campo'] = 'sinal_valor_total_outros';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Sinal valor total de outros impostos - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Valor total de impostos                                
        $dados[++$posicao]['posicao'] = 275;
        $dados[$posicao]['qtd'] = 13;
        $dados[$posicao]['campo'] = 'valor_total_impostos';
        $dados[$posicao]['formatacao'] = 'moeda';
        $dados[$posicao]['descricao'] = 'Valor total de impostos - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Nº da nota fiscal                                
        $dados[++$posicao]['posicao'] = 288;
        $dados[$posicao]['qtd'] = 12;
        $dados[$posicao]['campo'] = 'nota_fiscal';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Nº da nota fiscal - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        
        # Sinal valor da conta                               
        $dados[++$posicao]['posicao'] = 300;
        $dados[$posicao]['qtd'] = 1;
        $dados[$posicao]['campo'] = 'sinal_valor_conta';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Sinal valor da conta - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
                                        
        # Valor da conta                                
        $dados[++$posicao]['posicao'] = 301;
        $dados[$posicao]['qtd'] = 13;
        $dados[$posicao]['campo'] = 'valor_conta';
        $dados[$posicao]['formatacao'] = 'moeda';
        $dados[$posicao]['descricao'] = 'Valor da conta - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        
        return $dados;
        
    }
    
    # Tipo 3
    /**
      * FebrabanLayout::bilhetacao()
      * 
      * Identifica o detalhamento faturado com valores devedores ou credores, 
      * exceto os descontos que serão apresentados no tipo de registro = 5 "Descontos"
      * 
      * @return
      */
    public function bilhetacao(){
        
        $posicao = 0;
        
        # Tipo de registro
        $dados[$posicao]['posicao'] = 0; 
        $dados[$posicao]['qtd'] = 1;
        $dados[$posicao]['campo'] = 'tipo'; 
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Tipo de Registros - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        
        # Data de vencimento
        $dados[++$posicao]['posicao'] = 13; 
        $dados[$posicao]['qtd'] = 8; 
        $dados[$posicao]['campo'] = 'dt_vencimento';
        $dados[$posicao]['formatacao'] = 'data';
        $dados[$posicao]['descricao'] = 'Data de vencimento - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Data de emissão                                
        $dados[++$posicao]['posicao'] = 21;
        $dados[$posicao]['qtd'] = 8;
        $dados[$posicao]['campo'] = 'dt_emissao';
        $dados[$posicao]['formatacao'] = 'data';
        $dados[$posicao]['descricao'] = 'Data de emiss&atilde;o - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Identificador único do recurso (NRC)                                        
        /*$dados[++$posicao]['posicao'] = 30;
        $dados[$posicao]['qtd'] = 25;
        $dados[$posicao]['campo'] = '';
        $dados[$posicao]['descricao'] = 'Identificador &Uacute;nico do Recurso (NRC) - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';  */                          
        # DDD                                
        $dados[++$posicao]['posicao'] = 59;
        $dados[$posicao]['qtd'] = 2;
        $dados[$posicao]['campo'] = 'ddd';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'DDD - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Nº do telefone                               
        $dados[++$posicao]['posicao'] = 61;
        $dados[$posicao]['qtd'] = 10;
        $dados[$posicao]['campo'] = 'telefone';
        $dados[$posicao]['formatacao'] = 'celular';
        $dados[$posicao]['descricao'] = 'Nº do telefone - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';                       
        # Data da ligação                                
        $dados[++$posicao]['posicao'] = 88;
        $dados[$posicao]['qtd'] = 8;
        $dados[$posicao]['campo'] = 'dt_ligacao';
        $dados[$posicao]['formatacao'] = 'data';
        $dados[$posicao]['descricao'] = 'Data da liga&ccedil;&atilde;o - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';                    
        # Nome da localidade chamada                              
        $dados[++$posicao]['posicao'] = 101;
        $dados[$posicao]['qtd'] = 25;
        $dados[$posicao]['campo'] = 'localidade';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Nome da localidade chamada - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';                              
        # UF do telefone chamado                                
        $dados[++$posicao]['posicao'] = 126;
        $dados[$posicao]['qtd'] = 2;
        $dados[$posicao]['campo'] = 'uf_localidade';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'UF do telefone chamado - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';                           
        # Código Nacional / Internacional                                
        $dados[++$posicao]['posicao'] = 128;
        $dados[$posicao]['qtd'] = 2;
        $dados[$posicao]['campo'] = 'cod_nac_int';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'C&oacute;digo Nacional / Internacional - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';                               
        # Código da operadora                                
        $dados[++$posicao]['posicao'] = 130;
        $dados[$posicao]['qtd'] = 2;
        $dados[$posicao]['campo'] = 'cod_operadora';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'C&oacute;digo da operadora - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';                              
        # Descri&ccedil;&atilde;o da operadora                                
        $dados[++$posicao]['posicao'] = 132;
        $dados[$posicao]['qtd'] = 20;
        $dados[$posicao]['campo'] = 'desc_operadora';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Descri&ccedil;&atilde;o da operadora - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';                              
        # DDD chamado                             
        $dados[++$posicao]['posicao'] = 155;
        $dados[$posicao]['qtd'] = 4;
        $dados[$posicao]['campo'] = 'ddd_destino';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'DDD chamado - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.'; 
        # Número do telefone chamado                             
        $dados[++$posicao]['posicao'] = 159;
        $dados[$posicao]['qtd'] = 10;
        $dados[$posicao]['campo'] = 'telefone_destino';
        $dados[$posicao]['formatacao'] = 'celular';
        $dados[$posicao]['descricao'] = 'N&uacute;mero do telefone chamado - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';                             
        # Duração da ligação                             
        $dados[++$posicao]['posicao'] = 171;
        $dados[$posicao]['qtd'] = 6;
        $dados[$posicao]['campo'] = 'duracao_ligacao';
        $dados[$posicao]['formatacao'] = 'moeda';
        $dados[$posicao]['descricao'] = 'Dura&ccedil;&atilde;o da liga&ccedil;&atilde;o - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';                              
        # Categoria                             
        $dados[++$posicao]['posicao'] = 177;
        $dados[$posicao]['qtd'] = 3;
        $dados[$posicao]['campo'] = 'categoria';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Categoria - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';                              
        # Descrição da categoria                             
        $dados[++$posicao]['posicao'] = 180;
        $dados[$posicao]['qtd'] = 50;
        $dados[$posicao]['campo'] = 'desc_categoria';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Descri&ccedil;&atilde;o da categoria - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';                                                      
        # Horário da ligação                            
        $dados[++$posicao]['posicao'] = 230;
        $dados[$posicao]['qtd'] = 6;
        $dados[$posicao]['campo'] = 'horario_ligacao';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Hor&aacute;rio da liga&ccedil;&atilde;o - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';                             
        # Tipo de chamada (TC)                           
        $dados[++$posicao]['posicao'] = 236;
        $dados[$posicao]['qtd'] = 1;
        $dados[$posicao]['campo'] = 'tipo_chamada';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Tipo de chamada (TC) - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';                          
        # Descrição do horário tarifário                          
        $dados[++$posicao]['posicao'] = 238;
        $dados[$posicao]['qtd'] = 25;
        $dados[$posicao]['campo'] = 'desc_horario_tarifario';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Descri&ccedil;&atilde;o do hor&aacute;rio tarif&aacute;rio - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Degrau da ligação                          
        $dados[++$posicao]['posicao'] = 263;
        $dados[$posicao]['qtd'] = 2;
        $dados[$posicao]['campo'] = 'degrau_ligacao';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Degrau da liga&ccedil;&atilde;o - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Sinal do valor da ligação                          
        $dados[++$posicao]['posicao'] = 265;
        $dados[$posicao]['qtd'] = 1;
        $dados[$posicao]['campo'] = 'sinal_valor_ligacao';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Sinal do valor da liga&ccedil;&atilde;o - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Alíquota ICMS                          
        $dados[++$posicao]['posicao'] = 266;
        $dados[$posicao]['qtd'] = 5;
        $dados[$posicao]['campo'] = 'aliquota_valor';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Al&iacute;quota ICMS - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';                              
        # Valor da ligação                         
        $dados[++$posicao]['posicao'] = 271;
        $dados[$posicao]['qtd'] = 13;
        $dados[$posicao]['campo'] = 'valor_ligacao';
        $dados[$posicao]['formatacao'] = 'moeda';
        $dados[$posicao]['descricao'] = 'Valor da liga&ccedil;&atilde;o com imposto - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        
        return $dados;
        
    }
    
    # Tipo 4
    /**
      * FebrabanLayout::servicos()
      * 
      * Identifica os serviços faturados
      * 
      * @return
      */
    public function servicos(){
        
        $posicao = 0;
        
        # Tipo de registro
        $dados[$posicao]['posicao'] = 0; 
        $dados[$posicao]['qtd'] = 1;
        $dados[$posicao]['campo'] = 'tipo'; 
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Tipo de Registros - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        
        # Data de vencimento
        $dados[++$posicao]['posicao'] = 13; 
        $dados[$posicao]['qtd'] = 8; 
        $dados[$posicao]['campo'] = 'dt_vencimento';
        $dados[$posicao]['formatacao'] = 'data';
        $dados[$posicao]['descricao'] = 'Data de vencimento - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
                                        
        # Data de emissão                                
        $dados[++$posicao]['posicao'] = 21;
        $dados[$posicao]['qtd'] = 8;
        $dados[$posicao]['campo'] = 'dt_emissao';
        $dados[$posicao]['formatacao'] = 'data';
        $dados[$posicao]['descricao'] = 'Data de emiss&atilde;o - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Identificador único do recurso (NRC)                                        
        $dados[++$posicao]['posicao'] = 29;
        $dados[$posicao]['qtd'] = 25;
        $dados[$posicao]['campo'] = 'nrc';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Identificador &Uacute;nico do Recurso (NRC) - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';                            
        # CNL do recurso em referência                               
        $dados[++$posicao]['posicao'] = 54;
        $dados[$posicao]['qtd'] = 5;
        $dados[$posicao]['campo'] = 'cnl_recurso';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'CNL do recurso em refer&ecirc;ncia - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # DDD                                
        $dados[++$posicao]['posicao'] = 59;
        $dados[$posicao]['qtd'] = 2;
        $dados[$posicao]['campo'] = 'ddd';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'DDD - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Nº do telefone                               
        $dados[++$posicao]['posicao'] = 61;
        $dados[$posicao]['qtd'] = 10;
        $dados[$posicao]['campo'] = 'telefone';
        $dados[$posicao]['formatacao'] = 'celular';
        $dados[$posicao]['descricao'] = 'Nº do telefone - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Características do recurso                              
        $dados[++$posicao]['posicao'] = 71;
        $dados[$posicao]['qtd'] = 15;
        $dados[$posicao]['campo'] = 'carac_recurso';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Caracter&iacute;sticas do recurso - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # data do serviço                              
        $dados[++$posicao]['posicao'] = 86;
        $dados[$posicao]['qtd'] = 8;
        $dados[$posicao]['campo'] = 'dt_servico';
        $dados[$posicao]['formatacao'] = 'data';
        $dados[$posicao]['descricao'] = 'data do servi&ccedil;o - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # CNL da localidade chamada                            
        $dados[++$posicao]['posicao'] = 94;
        $dados[$posicao]['qtd'] = 5;
        $dados[$posicao]['campo'] = 'cnl_localidade';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'CNL da localidade chamada - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Nome da localidade chamada                            
        $dados[++$posicao]['posicao'] = 99;
        $dados[$posicao]['qtd'] = 25;
        $dados[$posicao]['campo'] = 'localidade';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Nome da localidade chamada - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # UF do telefone chamado                            
        $dados[++$posicao]['posicao'] = 124;
        $dados[$posicao]['qtd'] = 2;
        $dados[$posicao]['campo'] = 'uf_localidade';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'UF do telefone chamado - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Código nacional / internacional                            
        $dados[++$posicao]['posicao'] = 126;
        $dados[$posicao]['qtd'] = 2;
        $dados[$posicao]['campo'] = 'cod_nac_int';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'C&oacute;digo nacional / internacional - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Código da operadora                            
        $dados[++$posicao]['posicao'] = 128;
        $dados[$posicao]['qtd'] = 2;
        $dados[$posicao]['campo'] = 'cod_operadora';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'C&oacute;digo da operadora - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Descri&ccedil;&atilde;o da operadora                            
        $dados[++$posicao]['posicao'] = 130;
        $dados[$posicao]['qtd'] = 20;
        $dados[$posicao]['campo'] = 'desc_operadora';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Descri&ccedil;&atilde;o da operadora - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Código do país chamado                            
        /*$dados[++$posicao]['posicao'] = 151;
        $dados[$posicao]['qtd'] = 3;
        $dados[$posicao]['campo'] = '';
        $dados[$posicao]['descricao'] = 'C&oacute;digo do pa&iacute;s chamado - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';*/
                                        
        # Área / DDD                            
        $dados[++$posicao]['posicao'] = 153;
        $dados[$posicao]['qtd'] = 3;
        $dados[$posicao]['campo'] = 'ddd_destino';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = '&Aacute;rea / DDD - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Número do telefone chamado                          
        $dados[++$posicao]['posicao'] = 157;
        $dados[$posicao]['qtd'] = 10;
        $dados[$posicao]['campo'] = 'telefone_destino';
        $dados[$posicao]['formatacao'] = 'celular';
        $dados[$posicao]['descricao'] = 'N&uacute;mero do telefone chamado - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Duração da ligação                          
        $dados[++$posicao]['posicao'] = 169;
        $dados[$posicao]['qtd'] = 6;
        $dados[$posicao]['campo'] = 'duracao_ligacao';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Dura&ccedil;&atilde;o da liga&ccedil;&atilde;o - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
                                        
        # Horário da ligação                          
        $dados[++$posicao]['posicao'] = 175;
        $dados[$posicao]['qtd'] = 6;
        $dados[$posicao]['campo'] = 'horario_ligacao';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Hor&aacute;rio da liga&ccedil;&atilde;o - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Grupo das categorias                         
        $dados[++$posicao]['posicao'] = 181;
        $dados[$posicao]['qtd'] = 3;
        $dados[$posicao]['campo'] = 'grupo_categoria';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Grupo das categorias - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
                                        
        # Descrição do grupo da categoria                         
        $dados[++$posicao]['posicao'] = 184;
        $dados[$posicao]['qtd'] = 30;
        $dados[$posicao]['campo'] = 'desc_grupo_categoria';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Descri&ccedil;&atilde;o do grupo da categoria - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Categoria                         
        $dados[++$posicao]['posicao'] = 214;
        $dados[$posicao]['qtd'] = 3;
        $dados[$posicao]['campo'] = 'categoria';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Categoria - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Descrição da categoria                         
        $dados[++$posicao]['posicao'] = 217;
        $dados[$posicao]['qtd'] = 40;
        $dados[$posicao]['campo'] = 'desc_categoria';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Descri&ccedil;&atilde;o da categoria - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Sinal do valor da ligação                         
        $dados[++$posicao]['posicao'] = 259;
        $dados[$posicao]['qtd'] = 1;
        $dados[$posicao]['campo'] = 'sinal_valor_ligacao';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Sinal do valor da liga&ccedil;&atilde;o - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Valor da ligação                         
        $dados[++$posicao]['posicao'] = 258;
        $dados[$posicao]['qtd'] = 13;
        $dados[$posicao]['campo'] = 'valor_ligacao';
        $dados[$posicao]['formatacao'] = 'moeda';
        $dados[$posicao]['descricao'] = 'Valor da liga&ccedil;&atilde;o - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        
        return $dados;
    
    }
    
    # Tipo 5
    /**
      * FebrabanLayout::descontos()
      * 
      * Identifica os descontos
      * 
      * @return
      */
    public function descontos(){
        
        $posicao = 0;
        
        # Tipo de registro
        $dados[$posicao]['posicao'] = 0; 
        $dados[$posicao]['qtd'] = 1;
        $dados[$posicao]['campo'] = 'tipo'; 
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Tipo de Registros - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        
        # Data de vencimento
        $dados[++$posicao]['posicao'] = 13; 
        $dados[$posicao]['qtd'] = 8; 
        $dados[$posicao]['campo'] = 'dt_vencimento';
        $dados[$posicao]['formatacao'] = 'data';
        $dados[$posicao]['descricao'] = 'Data de vencimento - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
                                        
        # Data de emissão                                
        $dados[++$posicao]['posicao'] = 21;
        $dados[$posicao]['qtd'] = 8;
        $dados[$posicao]['campo'] = 'dt_emissao';
        $dados[$posicao]['formatacao'] = 'data';
        $dados[$posicao]['descricao'] = 'Data de emiss&atilde;o - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Identificador único do recurso (NRC)                                        
        $dados[++$posicao]['posicao'] = 29;
        $dados[$posicao]['qtd'] = 25;
        $dados[$posicao]['campo'] = 'nrc';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Identificador &Uacute;nico do Recurso (NRC) - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # DDD                                
        $dados[++$posicao]['posicao'] = 28;
        $dados[$posicao]['qtd'] = 2;
        $dados[$posicao]['campo'] = 'ddd';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'DDD - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Nº do telefone                               
        $dados[++$posicao]['posicao'] = 86;
        $dados[$posicao]['qtd'] = 10;
        $dados[$posicao]['campo'] = 'telefone';
        $dados[$posicao]['formatacao'] = 'celular';
        $dados[$posicao]['descricao'] = 'Nº do telefone - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Grupo das categorias                         
        $dados[++$posicao]['posicao'] = 96;
        $dados[$posicao]['qtd'] = 3;
        $dados[$posicao]['campo'] = 'grupo_categoria';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Grupo das categorias - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
                                        
        # Descrição do grupo da categoria                         
        $dados[++$posicao]['posicao'] = 99;
        $dados[$posicao]['qtd'] = 30;
        $dados[$posicao]['campo'] = 'desc_grupo_categoria';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Descri&ccedil;&atilde;o do grupo da categoria - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Sinal do valor da ligação                         
        $dados[++$posicao]['posicao'] = 179;
        $dados[$posicao]['qtd'] = 1;
        $dados[$posicao]['campo'] = 'sinal_valor_ligacao';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Sinal do valor da liga&ccedil;&atilde;o - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Valor da ligação                         
        $dados[++$posicao]['posicao'] = 198;
        $dados[$posicao]['qtd'] = 13;
        $dados[$posicao]['campo'] = 'valor_ligacao';
        $dados[$posicao]['formatacao'] = 'moeda';
        $dados[$posicao]['descricao'] = 'Valor da liga&ccedil;&atilde;o - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
     
        return $dados;
                                        
    }
    
    # Tipo 9
    /**
      * FebrabanLayout::trailler()
      * 
      * Apresenta o fechamento da conta totalizando o valor a pagar da conta em questão
      * 
      * @return
      */
    public function trailler(){
        
        $posicao = 0;
        
        # Tipo de registro
        $dados[$posicao]['posicao'] = 0; 
        $dados[$posicao]['qtd'] = 1;
        $dados[$posicao]['campo'] = 'tipo'; 
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Tipo de Registros - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        
        # Data de vencimento
        $dados[++$posicao]['posicao'] = 53; 
        $dados[$posicao]['qtd'] = 8; 
        $dados[$posicao]['campo'] = 'dt_vencimento';
        $dados[$posicao]['formatacao'] = 'data';
        $dados[$posicao]['descricao'] = 'Data de vencimento - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';              
        # Data de emissão                                
        $dados[++$posicao]['posicao'] = 61;
        $dados[$posicao]['qtd'] = 8;
        $dados[$posicao]['campo'] = 'dt_emissao';
        $dados[$posicao]['formatacao'] = 'data';
        $dados[$posicao]['descricao'] = 'Data de emiss&atilde;o - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Quantidade de registros                                
        $dados[++$posicao]['posicao'] = 69;
        $dados[$posicao]['qtd'] = 12;
        $dados[$posicao]['campo'] = 'qtd_registros';
        $dados[$posicao]['formatacao'] = 'inteiro';
        $dados[$posicao]['descricao'] = 'Quantidade de registros - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Quantidade de linhas telefônicas (Recursos)                                
        $dados[++$posicao]['posicao'] = 81;
        $dados[$posicao]['qtd'] = 12;
        $dados[$posicao]['campo'] = 'qtd_linhas';
        $dados[$posicao]['formatacao'] = 'inteiro';
        $dados[$posicao]['descricao'] = 'Quantidade de linhas telef&ocirc;nicas (Recursos) - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Sinal do total                                
        $dados[++$posicao]['posicao'] = 93;
        $dados[$posicao]['qtd'] = 1;
        $dados[$posicao]['campo'] = 'sinal_total';
        $dados[$posicao]['formatacao'] = '';
        $dados[$posicao]['descricao'] = 'Sinal do total - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
        # Valor do total                                
        $dados[++$posicao]['posicao'] = 94;
        $dados[$posicao]['qtd'] = 13;
        $dados[$posicao]['campo'] = 'valor_total';
        $dados[$posicao]['formatacao'] = 'moeda';
        $dados[$posicao]['descricao'] = 'Valor do total - Na posi&ccedil;&atilde;o '.$dados[$posicao]['posicao'].
                                        ' com '.$dados[$posicao]['qtd'].' posi&ccedil;&otilde;es.';
     
        return $dados;
                                        
    }

}