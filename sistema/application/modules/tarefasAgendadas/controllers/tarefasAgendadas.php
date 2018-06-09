<?php
#error_reporting(0);
if(!defined('BASEPATH')) exit('No direct script access allowed');
date_default_timezone_set('America/Sao_Paulo');
#setlocale(LC_ALL, 'pt_BR.UTF-8', 'Portuguese_Brazil.1252');
/**
* Classe responsável pelas tarefas agendadas
*/
class TarefasAgendadas extends MX_Controller
{
    
	/**
	 * TarefasAgendadas::__construct()
	 * 
	 * @return
	 */
	public function __construct(){
       
		parent::__construct();
        
        /*if(!$this->session->userdata('session_id') || !$this->session->userdata('logado')){
			redirect(base_url('home'));
		}*/
        
        # Configurações do sistema
        include_once('configSistema.php');
        
        $this->load->library('Util', '', 'util');
        $this->load->model('base/dadosBanco_model','dadosBanco');
        #$this->load->helper('url');
        $this->load->model('anatel/AnatelForm_model','anatelForm');
        $this->load->model('telefonia/asterisk_model','asterisk');
        $this->load->model('ura/ura_model','ura');
        $this->load->model('logarquivo_model','logArquivo'); 
        $this->load->model('logTarefaAgendada_model','logTarefaAgendada'); 
        #$this->load->library('email');
        
    }
    
    /**
	 * TarefasAgendadas::satvaPlanosOferecidos()
	 * 
     * Importa os planos oferecidos
     * 
	 * @return
	 */
    function satvaPlanosOferecidos()
    {   
        
        $this->logTarefaAgendada->grava('SATVA - Planos oferecidos', 'Importação de dados', 'Iniciado');
        
        if($this->SatvaVerifica(24)){
            
            $this->logTarefaAgendada->grava('SATVA - Planos oferecidos', 'Importação de dados', 'Já rodou');
            
            exit(); # Importação já foi rodada
        }
        
        $dados = $this->anatelForm->importaPlanosOferecidos();
        $cont = 1;
        $this->db->trans_begin();
        foreach($dados as $item){
           
           if($item->COD_PERMISSOR != $ultimoPermissor){
                $cont = 1;
           }
           
           $unidade = $this->dadosBanco->unidade($item->COD_PERMISSOR);
           $unidade = $unidade[0]->cd_unidade;
           if(trim($item->PERMISSOR) == 'Várzea Grande'){
                $unidade = 7; 
           }
           if(trim($item->PERMISSOR) == 'SIM Cuiabá'){
                $unidade = 8; 
           }
           $plano = trim($item->PACOTE);
           
           $sql = "INSERT anatel_res(cd_anatel_frm, cd_anatel_quest, cd_unidade, resposta, grupo) ";
           $sql .= "VALUES(24, 54, ".$unidade.", '".$plano."', ".$cont.")";
           
           $this->db->query($sql);
           
           $canais = 'null';
           
           $sql = "INSERT anatel_res(cd_anatel_frm, cd_anatel_quest, cd_unidade, resposta, grupo) ";
           $sql .= "VALUES(24, 55, ".$unidade.", ".$canais.", ".$cont.")";
           
           $this->db->query($sql);
           
           $adesao = "'0.00'";
           
           $sql = "INSERT anatel_res(cd_anatel_frm, cd_anatel_quest, cd_unidade, resposta, grupo) ";
           $sql .= "VALUES(24, 56, ".$unidade.", ".$adesao.", ".$cont.")";
           
           $this->db->query($sql);
           
           $instalacao = str_replace(',','.',trim($item->VALOR_INSTALACAO));
           $sql = "INSERT anatel_res(cd_anatel_frm, cd_anatel_quest, cd_unidade, resposta, grupo) ";
           $sql .= "VALUES(24, 57, ".$unidade.", '".$instalacao."', ".$cont.")";
           
           $this->db->query($sql);
           
           #$mensalidade = trim($item->PRECO);
           $sql = "INSERT anatel_res(cd_anatel_frm, cd_anatel_quest, cd_unidade, resposta, grupo) ";
           $sql .= "VALUES(24, 58, ".$unidade.", null, ".$cont.")";
           
           $this->db->query($sql);
           
           $status = '1';
           $sql = "INSERT anatel_res(cd_anatel_frm, cd_anatel_quest, cd_unidade, resposta, grupo) ";
           $sql .= "VALUES(24, 59, ".$unidade.", '".$status."', ".$cont.")";
           
           $this->db->query($sql);
           
           if($unidade == 8){ # Se Cuiabá realiza mais uma inserção com Varzea Grande e vice versa
                
               $unidade = 7; # Prepara inserção Várzea Grande
            
               $plano = trim($item->PACOTE);
               
               $sql = "INSERT anatel_res(cd_anatel_frm, cd_anatel_quest, cd_unidade, resposta, grupo) ";
               $sql .= "VALUES(24, 54, ".$unidade.", '".$plano."', ".$cont.")";
               
               $this->db->query($sql);
               
               $canais = 'null';
               
               $sql = "INSERT anatel_res(cd_anatel_frm, cd_anatel_quest, cd_unidade, resposta, grupo) ";
               $sql .= "VALUES(24, 55, ".$unidade.", ".$canais.", ".$cont.")";
               
               $this->db->query($sql);
               
               $adesao = "'0.00'";
               
               $sql = "INSERT anatel_res(cd_anatel_frm, cd_anatel_quest, cd_unidade, resposta, grupo) ";
               $sql .= "VALUES(24, 56, ".$unidade.", ".$adesao.", ".$cont.")";
               
               $this->db->query($sql);
               
               $instalacao = str_replace(',','.',trim($item->VALOR_INSTALACAO));
               $sql = "INSERT anatel_res(cd_anatel_frm, cd_anatel_quest, cd_unidade, resposta, grupo) ";
               $sql .= "VALUES(24, 57, ".$unidade.", '".$instalacao."', ".$cont.")";
               
               $this->db->query($sql);
               
               #$mensalidade = trim($item->PRECO);
               $sql = "INSERT anatel_res(cd_anatel_frm, cd_anatel_quest, cd_unidade, resposta, grupo) ";
               $sql .= "VALUES(24, 58, ".$unidade.", null, ".$cont.")";
               
               $this->db->query($sql);
               
               $status = '1';
               $sql = "INSERT anatel_res(cd_anatel_frm, cd_anatel_quest, cd_unidade, resposta, grupo) ";
               $sql .= "VALUES(24, 59, ".$unidade.", '".$status."', ".$cont.")";
               
               $this->db->query($sql);
               
           }
           
           $ultimoPermissor = $item->COD_PERMISSOR;
           $cont++; 
        }
        
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            #$this->util->enviaEmail('SATVA - Planos oferecidos', 'Erro - Importação de dados');
            $this->logTarefaAgendada->grava('SATVA - Planos oferecidos', 'Importação de dados', 'Erro');
            return false;
        }
        else
        {
            $this->db->trans_commit();
            #$this->util->enviaEmail('SATVA - Planos oferecidos', 'Ok - Importação de dados');
            $this->logTarefaAgendada->grava('SATVA - Planos oferecidos', 'Importação de dados', 'Ok');
            return true;
        }

    }
    
    /**
	 * TarefasAgendadas::satvaImportIREDEC()
	 * 
     * Importa os dados do IREDEC
     * 
	 * @return
	 */
    public function satvaImportIREDEC(){
        
        $this->logTarefaAgendada->grava('SATVA - IREDEC', 'Importação de dados', 'Iniciado');
        
        if($this->SatvaVerifica(12)){
            
            $this->logTarefaAgendada->grava('SATVA - IREDEC', 'Importação de dados', 'Já rodou');
            
            exit(); # Importação já foi rodada
        }
        
        $dados = $this->anatelForm->importaIREDEC();
        $regra = $this->anatelForm->regrasMeta(12);
        $this->db->trans_begin();
        foreach($dados as $item){
           
           $unidade = $this->dadosBanco->unidade($item->COD_OPERADORA);
           $unidade = $unidade[0]->cd_unidade;
           
           # Questão ID 33
           $sql = "INSERT INTO anatel_res(cd_anatel_frm, cd_anatel_quest, resposta, grupo, cd_unidade, data_cadastro) VALUES(12, 33, '".$item->NUMERO_ATEND_ERROS."', 1, ".$unidade.", '".$item->DATA_CADASTRO."');";
           $this->db->query($sql);
           # Questão ID 34
           $sql = "INSERT INTO anatel_res(cd_anatel_frm, cd_anatel_quest, resposta, grupo, cd_unidade, data_cadastro) VALUES(12, 34, '".$item->NUMERO_DOC_EMITIDOS."', 1, ".$unidade.", '".$item->DATA_CADASTRO."');";
           $this->db->query($sql);
            
           $calculoPorcertagem = $this->util->satvaCalculoPorcentagem($item->NUMERO_ATEND_ERROS,($item->NUMERO_ATEND_ERROS+$item->NUMERO_DOC_EMITIDOS));
           # Verifica meta porcentagem
           if(str_replace(',','.',$calculoPorcertagem) > $regra[0]->numero){
                $ilustracao = $calculoPorcertagem.'% '.$regra[0]->comparador.' Meta '.(int)$regra[0]->numero.'%';
                $sql = "INSERT INTO anatel_meta_res (cd_anatel_meta, cd_anatel_frm, cd_unidade, ilustracao, resultado, data_cadastro) VALUES(22, 12, ".$unidade.", '".$ilustracao."', '".$calculoPorcertagem."', '".$item->DATA_CADASTRO."');";
                $this->db->query($sql);
           }
           
        }
        
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            #$this->util->enviaEmail('SATVA - IREDEC', 'Erro - Importação de dados');
            $this->logTarefaAgendada->grava('SATVA - IREDEC', 'Importação de dados', 'Erro');
            return false;
        }
        else
        {
            $this->db->trans_commit();
            #$this->util->enviaEmail('SATVA - IREDEC', 'Ok - Importação de dados');
            $this->logTarefaAgendada->grava('SATVA - IREDEC', 'Importação de dados', 'Ok');
            return true;
        }
        
    }
    
    public function satvaImportICCO(){
        
        $this->logTarefaAgendada->grava('SATVA - ICCO', 'Importação de dados', 'Iniciado');
        
        if($this->SatvaVerifica(3)){
            
            $this->logTarefaAgendada->grava('SATVA - ICCO', 'Importação de dados', 'Já rodou');
            
            exit(); # Importação já foi rodada
        }
        
        $dados = $this->anatelForm->importaICCO();
        $regra = $this->anatelForm->regrasMeta(3);
        $this->db->trans_begin();
        foreach($dados as $item){
           
           $unidade = $this->dadosBanco->unidade($item->COD_OPERADORA);
           $unidade = $unidade[0]->cd_unidade;
           
           # Questão ID 7
           $sql = "INSERT INTO anatel_res(cd_anatel_frm, cd_anatel_quest, resposta, grupo, cd_unidade, data_cadastro) VALUES(3, 7, '".$item->DENTRO_PRAZO."', 1, ".$unidade.", '".$item->DATA_CADASTRO."');";
           $this->db->query($sql);
           # Questão ID 8
           $sql = "INSERT INTO anatel_res(cd_anatel_frm, cd_anatel_quest, resposta, grupo, cd_unidade, data_cadastro) VALUES(3, 8, '".$item->TOTAL."', 1, ".$unidade.", '".$item->DATA_CADASTRO."');";
           $this->db->query($sql);
           
           # Questão ID 9
           $sql = "INSERT INTO anatel_res(cd_anatel_frm, cd_anatel_quest, resposta, grupo, cd_unidade, data_cadastro) VALUES(3, 9, '".$item->FORA_PRAZO."', 1, ".$unidade.", '".$item->DATA_CADASTRO."');";
           $this->db->query($sql);
            
           $calculoPorcertagem = $this->util->satvaCalculoPorcentagem($item->DENTRO_PRAZO,($item->DENTRO_PRAZO+$item->FORA_PRAZO));
            # Verifica meta porcentagem
            if(str_replace(',','.',$calculoPorcertagem) > $regra[0]->numero){
                $ilustracao = $calculoPorcertagem.'% '.$regra[0]->comparador.' Meta '.(int)$regra[0]->numero.'%';
                $sql = "INSERT INTO anatel_meta_res (cd_anatel_meta, cd_anatel_frm, cd_unidade, ilustracao, resultado, data_cadastro) VALUES(12, 5, ".$unidade.", '".$ilustracao."', '".$calculoPorcertagem."', '".$item->DATA_CADASTRO."');";
                $this->db->query($sql);
            }
            
            # verifica meta numérica
            if($item->FORA_PRAZO > $regra[1]->numero){
                $ilustracao = $item->FORA_PRAZO.' '.$regra[1]->comparador.' Meta '.(int)$regra[1]->numero;
                $sql = "INSERT INTO anatel_meta_res (cd_anatel_meta, cd_anatel_frm, cd_unidade, ilustracao, resultado, data_cadastro) VALUES(13, 5, ".$unidade.", '".$ilustracao."', '".$item->FORA_PRAZO."', '".$item->DATA_CADASTRO."');";
                $this->db->query($sql);
            }
           
        }
        
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            #$this->util->enviaEmail('SATVA - IREDEC', 'Erro - Importação de dados');
            $this->logTarefaAgendada->grava('SATVA - ICCO', 'Importação de dados', 'Erro');
            return false;
        }
        else
        {
            $this->db->trans_commit();
            #$this->util->enviaEmail('SATVA - IREDEC', 'Ok - Importação de dados');
            $this->logTarefaAgendada->grava('SATVA - ICCO', 'Importação de dados', 'Ok');
            return true;
        }
        
    }
    
    /**
	 * TarefasAgendadas::satvaImportIREDEC()
	 * 
     * Importa os dados do IREDEC
     * 
	 * @return
	 */
    public function satvaImportBaseAssinante(){
        
        $this->logTarefaAgendada->grava('SATVA - Base Assinante', 'Importação de dados', 'Iniciado');
        
        if($this->SatvaVerifica(25)){
            
            $this->logTarefaAgendada->grava('SATVA - Base Assinante', 'Importação de dados', 'Já rodou');
            
            exit(); # Importação já foi rodada
        }
        
        $dados = $this->anatelForm->importaBase();
        $this->db->trans_begin();
        foreach($dados as $item){
            
            $unidade = $this->dadosBanco->unidade($item->COD_OPERADORA);
            $unidade = $unidade[0]->cd_unidade;
            
            # Questão ID 61
            $sql = "INSERT INTO anatel_res(cd_anatel_frm, cd_anatel_quest, resposta, grupo, cd_unidade, data_cadastro) VALUES(25, 61, '".$item->BASE_TV."', 1, ".$unidade.", '".$item->DATA_CADASTRO."');";
            $this->db->query($sql);
            
        }
        
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            #$this->util->enviaEmail('SATVA - IREDEC', 'Erro - Importação de dados');
            $this->logTarefaAgendada->grava('SATVA - Base Assinante', 'Importação de dados', 'Erro');
            return false;
        }
        else
        {
            $this->db->trans_commit();
            #$this->util->enviaEmail('SATVA - IREDEC', 'Ok - Importação de dados');
            $this->logTarefaAgendada->grava('SATVA - Base Assinante', 'Importação de dados', 'Ok');
            return true;
        }
        
    }
    
    /**
	 * TarefasAgendadas::satvaBandaLarga()
	 * 
     * Importa os dados de banda larga
     * 
	 * @return
	 */
    function satvaImportBandaLarga()
    {
        
        if($this->SatvaVerifica(18)){
            exit(); # Importação já foi rodada
        }
        
        $dados = $this->anatelForm->importaBandaLarga();
        $cont = 1;
        $this->db->trans_begin();
        foreach($dados as $item){
            
            $unidade = $this->dadosBanco->unidade($item->PERCOD);
            $unidade = $unidade[0]->cd_unidade;
            
            if($cont == 1){
                $velocidade = $this->util->medidasInformatica($item->IPAQDSC, 'MEGA', 'KB');
           
                $sql = "INSERT anatel_res(cd_anatel_frm, cd_anatel_quest, cd_unidade, resposta, grupo) ";
                $sql .= "VALUES(18, 44, ".$unidade.", '".$velocidade."', 1)";
               
                $this->db->query($sql);
                
                $valor = $item->POLPRC;
           
                $sql = "INSERT anatel_res(cd_anatel_frm, cd_anatel_quest, cd_unidade, resposta, grupo) ";
                $sql .= "VALUES(18, 45, ".$unidade.", '".$valor."', 1)";
               
                $this->db->query($sql);
                
                if($item->PERCOD == 91){ # Se é Cuiabá cria Varzea Grande
                    
                    $sql = "INSERT anatel_res(cd_anatel_frm, cd_anatel_quest, cd_unidade, resposta, grupo) ";
                    $sql .= "VALUES(18, 44, 7, '".$velocidade."', 1)";
                   
                    $this->db->query($sql);
                    
                    $valor = $item->POLPRC;
               
                    $sql = "INSERT anatel_res(cd_anatel_frm, cd_anatel_quest, cd_unidade, resposta, grupo) ";
                    $sql .= "VALUES(18, 45, 7, '".$valor."', 1)";
                   
                    $this->db->query($sql);
                    
                }
                
            }
            
            if($cont == 2){
                
                $velocidade = $this->util->medidasInformatica($item->IPAQDSC, 'MEGA', 'KB');
           
                $sql = "INSERT anatel_res(cd_anatel_frm, cd_anatel_quest, cd_unidade, resposta, grupo) ";
                $sql .= "VALUES(18, 46, ".$unidade.", '".$velocidade."', 1)";
               
                $this->db->query($sql);
                
                $valor = $item->POLPRC;
           
                $sql = "INSERT anatel_res(cd_anatel_frm, cd_anatel_quest, cd_unidade, resposta, grupo) ";
                $sql .= "VALUES(18, 47, ".$unidade.", '".$valor."', 1)";
               
                $this->db->query($sql);
                
                $qtdAssinantes = $this->anatelForm->importaBase($item->PERCOD)[0]->BANDA_LARGA;
                $sql = "INSERT anatel_res(cd_anatel_frm, cd_anatel_quest, cd_unidade, resposta, grupo) ";
                $sql .= "VALUES(18, 60, ".$unidade.", ".$qtdAssinantes.", 1)";
               
                $this->db->query($sql);
                
                if($item->PERCOD == 91){ # Se é Cuiabá cria Varzea Grande
                    
                    $sql = "INSERT anatel_res(cd_anatel_frm, cd_anatel_quest, cd_unidade, resposta, grupo) ";
                    $sql .= "VALUES(18, 46, 7, '".$velocidade."', 1)";
                   
                    $this->db->query($sql);
                    
                    $valor = $item->POLPRC;
               
                    $sql = "INSERT anatel_res(cd_anatel_frm, cd_anatel_quest, cd_unidade, resposta, grupo) ";
                    $sql .= "VALUES(18, 47, 7, '".$valor."', 1)";
                   
                    $this->db->query($sql);
                    
                    $qtdAssinantes = $this->anatelForm->importaBase(99)[0]->BANDA_LARGA;
                    $sql = "INSERT anatel_res(cd_anatel_frm, cd_anatel_quest, cd_unidade, resposta, grupo) ";
                    $sql .= "VALUES(18, 60, 7, ".$qtdAssinantes.", 1)";
                   
                    $this->db->query($sql);
                    
                }
                
                $cont = 0;
            }
            
            $cont++;
        }
        
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            #$this->enviaEmail('SATVA - Banda Larga', 'Erro - Importação de dados');
            $this->logTarefaAgendada->grava('SATVA - Banda Larga', 'Importação de dados', 'Erro');
            return false;
        }
        else
        {
            $this->db->trans_commit();
            #$this->enviaEmail('SATVA - Banda Larga', 'Ok - Importação de dados');
            $this->logTarefaAgendada->grava('SATVA - Banda Larga', 'Importação de dados', 'Ok');
            return true;
        }
        
    }
    
    /*public function enviaEmail($titulo, $texto){
        
        $this->email->initialize(); // Aqui carrega todo config criado anteriormente
        $this->email->from('equipe.sistemas@simtv.com.br', utf8_encode('Sim TV - Ferramentas de Negócios'));
        $this->email->to('ti@simtv.com.br'); 
        #$this->email->cc('outro@outro-site.com'); 
        #$this->email->bcc('fulano@qualquer-site.com'); 
        
        $this->email->subject(utf8_encode($titulo));
        $this->email->message(utf8_encode($texto));	
        
        $this->email->send();
        echo $this->email->print_debugger();
    }*/
    
    /**
	 * TarefasAgendadas::SatvaVerifica()
	 * 
     * Verifica se o indicador informado já foi importado no mês corrente
     * 
     * @param Id do indicador para se verificado
     * 
	 * @return Count com a quantidade de registros casa exista
	 */
    public function SatvaVerifica($indicador){
        
        $sql = "SELECT 
                	COUNT(*) AS valor 
                FROM anatel_res 
                WHERE 
                SUBSTR(data_cadastro, 1, 7) = SUBSTR(CURDATE(), 1, 7)
                AND cd_anatel_frm = ".$indicador;
                
        return $this->db->query($sql)->row()->valor;
        
    }
    
    /**
	 * TarefasAgendadas::satvaImportaSIGA()
	 * 
     * Importa os dados das ocorrências do Siga
     * 
	 * @return
	 */
    public function satvaImportaSIGA(){
        
        $this->logTarefaAgendada->grava('SATVA - SIGA', 'Importação de dados', 'Iniciado');
        
        if($this->SatvaVerifica(16)){
            
            $this->logTarefaAgendada->grava('SATVA - SIGA', 'Importação de dados', 'Já rodou');
            
            exit(); # Importação já foi rodada
        }
        
        $dados = $this->anatelForm->importaSIGA();
        
        $this->db->trans_begin();
        
        foreach($dados as $item){
            
            $unidade = $this->dadosBanco->unidade($item->PERMISSOR);
            $unidade = $unidade[0]->cd_unidade;
            
            $sql = "INSERT INTO anatel_res(cd_anatel_frm, cd_anatel_quest, data_cadastro, cd_unidade, resposta) VALUES(16, 42,'".date('Y-m-d')."',".$unidade.",'".$item->QTD."');";
            $this->db->query($sql);
            
        }
        
        # Pega as unidades pendentes
        $unidadesPendentes = $this->anatelForm->sigaUnidadesPendentes();
        
        # Grava as unidades pendentes com o valor zero
        foreach($unidadesPendentes as $uniPend){
            $sql = "INSERT INTO anatel_res(cd_anatel_frm, cd_anatel_quest, data_cadastro, cd_unidade, resposta) VALUES(16, 42,'".date('Y-m-d')."',".$uniPend->cd_unidade.",'0');";
            $this->db->query($sql);
        }
        
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            $this->logTarefaAgendada->grava('SATVA - SIGA', 'Importação de dados', 'Erro');
            return false;
        }
        else
        {
            $this->db->trans_commit();
            $this->logTarefaAgendada->grava('SATVA - SIGA', 'Importação de dados', 'Ok');
            return true;
        }
                
    }
    
    /**
	 * TarefasAgendadas::satvaImportaIAP()
	 * 
     * Importa os dados do IAP
     * 
	 * @return
	 */
    public function satvaImportaIAP(){
        
        $this->logTarefaAgendada->grava('SATVA - IAP', 'Importação de dados', 'Iniciado');
        
        if($this->SatvaVerifica(5)){
            
            $this->logTarefaAgendada->grava('SATVA - IAP', 'Importação de dados', 'Já rodou');
            
            exit(); # Importação já foi rodada
        }
        
        $dados = $this->anatelForm->importaIAP();
        $regra = $this->anatelForm->regrasMeta(5);
        
        $this->db->trans_begin();
        foreach($dados AS $item){
            
            $unidade = $this->dadosBanco->unidade($item->permissor);
            $unidade = $unidade[0]->cd_unidade;
            
            if($item->cod_unidade == 8){ # Cuiabá
                $unidade = 8;
            }
            
            # Questão ID 13
            $sql = "INSERT INTO anatel_res(cd_anatel_frm, cd_anatel_quest, resposta, grupo, cd_unidade, data_cadastro) VALUES(5, 13, '".$item->tot_atend_prazo."', 1, ".$unidade.", '".$item->data_cadastro."');";
            $this->db->query($sql);
            # Questão ID 14
            #$sql = "INSERT INTO anatel_res(cd_anatel_frm, cd_anatel_quest, resposta, grupo, cd_unidade, data_cadastro) VALUES(5, 14, '".$item->tot_atend_mes."', 1, ".$unidade.", '".$item->data_cadastro."');";
            $sql = "INSERT INTO anatel_res(cd_anatel_frm, cd_anatel_quest, resposta, grupo, cd_unidade, data_cadastro) VALUES(5, 14, '".($item->tot_atend_prazo+$item->tot_atend_superior)."', 1, ".$unidade.", '".$item->data_cadastro."');";
            $this->db->query($sql);
            # Questão ID 15
            $sql = "INSERT INTO anatel_res(cd_anatel_frm, cd_anatel_quest, resposta, grupo, cd_unidade, data_cadastro) VALUES(5, 15, '".$item->tot_atend_superior."', 1, ".$unidade.", '".$item->data_cadastro."');";
            $this->db->query($sql);
            
            $calculoPorcertagem = $this->util->satvaCalculoPorcentagem($item->tot_atend_prazo,($item->tot_atend_prazo+$item->tot_atend_superior));
            # Verifica meta porcentagem
            if(str_replace(',','.',$calculoPorcertagem) < $regra[0]->numero){
                $ilustracao = $item->res_porc.'% '.$regra[0]->comparador.' Meta '.(int)$regra[0]->numero.'%';
                $sql = "INSERT INTO anatel_meta_res (cd_anatel_meta, cd_anatel_frm, cd_unidade, ilustracao, resultado, data_cadastro) VALUES(12, 5, ".$unidade.", '".$ilustracao."', '".$item->res_porc."', '".$item->data_cadastro."');";
                $this->db->query($sql);
            }
            
            # verifica meta numérica
            if($item->tot_atend_superior > $regra[1]->numero){
                $ilustracao = $item->tot_atend_superior.' '.$regra[1]->comparador.' Meta '.(int)$regra[1]->numero;
                $sql = "INSERT INTO anatel_meta_res (cd_anatel_meta, cd_anatel_frm, cd_unidade, ilustracao, resultado, data_cadastro) VALUES(13, 5, ".$unidade.", '".$ilustracao."', '".$item->tot_atend_superior."', '".$item->data_cadastro."');";
                $this->db->query($sql);
            }
            
            if($unidade == 8){ # Cuiabá (Cria uma cópia do conteúdo para Várzea Grande)
                
                $unidade = 7; # Várzea Grande
                
                # Questão ID 13
                $sql = "INSERT INTO anatel_res(cd_anatel_frm, cd_anatel_quest, resposta, grupo, cd_unidade, data_cadastro) VALUES(5, 13, '0', 1, ".$unidade.", '".$item->data_cadastro."');";
                $this->db->query($sql);
                # Questão ID 14
                $sql = "INSERT INTO anatel_res(cd_anatel_frm, cd_anatel_quest, resposta, grupo, cd_unidade, data_cadastro) VALUES(5, 14, '0', 1, ".$unidade.", '".$item->data_cadastro."');";
                $this->db->query($sql);
                # Questão ID 15
                $sql = "INSERT INTO anatel_res(cd_anatel_frm, cd_anatel_quest, resposta, grupo, cd_unidade, data_cadastro) VALUES(5, 15, '0', 1, ".$unidade.", '".$item->data_cadastro."');";
                $this->db->query($sql);
                
            }
            
        }
        
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            $this->logTarefaAgendada->grava('SATVA - IAP', 'Importação de dados', 'Erro');
            return false;
        }
        else
        {
            $this->db->trans_commit();
            $this->logTarefaAgendada->grava('SATVA - IAP', 'Importação de dados', 'Ok');
            return true;
        }

    }
    
    public function importAsterisk(){
        
        $this->logTarefaAgendada->grava('ASTERISK - Import', 'Importação dos dados', 'Iniciado');
        
        $this->db->trans_begin();
        
        $log['nome'] = '';
        $log['localizacao'] = '';
        $log['md5file'] = '';
        $log['fonte'] = 'SERVIDOR ASTERISK';
        
        $logArquivo = $this->logArquivo->grava($log);
        
        $dados = $this->asterisk->importAsterisk();
        #echo '<pre>'; print_r($dados); exit();
        if($logArquivo){
            
            foreach($dados as $da){
                #echo md5($ha); echo '<br>';
                #echo $ha; echo '<br>';
                #print_r( str_replace('/','-',$dado)); echo '<br>'; 
                
                $fim = $da->fim;
                $origem = $da->origem;
                $destino = $da->destino;
                $segundos = $da->segundos;
                $tipo = $da->tipo;
                
                switch ($tipo) {
                    case 'celularLocal': # Celular local
                        $ti = "celular";
                        break;
                    case 'celularLDN': # Celular à dintância
                        $ti = "celularddd";
                        break;
                    case 'fixoLocal': # Fixo local
                        $ti = "fixo";
                        break;
                    default: # Fixo à distância
                        $ti = "fixoddd";
                }
                
                $custo = str_replace(',', '.', $this->util->telefoniaBilhetacao($segundos,$ti,'holding'));
                
                #if($linha == 3){
                    
                    #exit();
                #}
                
                    
                $sql = "INSERT INTO adminti.telefonia_chamadas(cd_log_arquivo, fim, origem, destino, segundos, custo, tipo) ";                    
                $sql .="\n VALUES(".$logArquivo.", '".$fim."', '".$origem."', '".$destino."', ".$segundos.", '".$custo."', '".$tipo."');";
                #echo $sql; exit();
                $this->db->query($sql);                                       
                #print_r($sql); exit();               
                
            }
            
        }
        
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            $this->logTarefaAgendada->grava('ASTERISK - Import', 'Importação do Asterisk', 'Erro');
            return false;
        }
        else
        {
            $this->db->trans_commit();
            $this->logTarefaAgendada->grava('ASTERISK - Import', 'Importação do Asterisk', 'Ok');
            return true;
        }
    }
    
    public function importCallCenterAtivo(){
        
        $this->logTarefaAgendada->grava('CallCenter Ativo - Import', 'Importação dos dados', 'Iniciado');
        
        $this->db->trans_begin();
        
        $log['nome'] = 'CallCenter';
        $log['localizacao'] = '';
        $log['md5file'] = '';
        $log['fonte'] = 'CALLCENTER - ATIVO';
        
        $logArquivo = $this->logArquivo->grava($log);
        
        $dados = $this->ura->importCallCenter('Ativo');
        #echo '<pre>'; print_r($dados); exit();
        if($logArquivo){
            
            foreach($dados as $da){
                #echo md5($ha); echo '<br>';
                #echo $ha; echo '<br>';
                #print_r( str_replace('/','-',$dado)); echo '<br>'; 
                
                $fim = $da->fim;
                $origem = $da->origem;
                $destino = $da->destino;
                $segundos = $da->segundos;
                $tipo = $da->tipo;
                
                switch ($tipo) {
                    case 'celularLocal': # Celular local
                        $ti = "celular";
                        break;
                    case 'celularLDN': # Celular à dintância
                        $ti = "celularddd";
                        break;
                    case 'fixoLocal': # Fixo local
                        $ti = "fixo";
                        break;
                    default: # Fixo à distância
                        $ti = "fixoddd";
                }
                
                $custo = str_replace(',', '.', $this->util->telefoniaBilhetacao($segundos,$ti,'callcenter'));
                
                #if($linha == 3){
                    
                    #exit();
                #}
                
                    
                $sql = "INSERT INTO adminti.telefonia_chamadas(cd_log_arquivo, fim, origem, destino, segundos, custo, tipo) ";                    
                $sql .="\n VALUES(".$logArquivo.", '".$fim."', '".$origem."', '".$destino."', ".$segundos.", '".$custo."', '".$tipo."');";
                #echo $sql; exit();
                $this->db->query($sql);                                       
                #print_r($sql); exit();               
                
            }
            
        }
        
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            $this->logTarefaAgendada->grava('CallCenter Ativo - Import', 'Importação do CallCenter Ativo', 'Erro');
            return false;
        }
        else
        {
            $this->db->trans_commit();
            $this->logTarefaAgendada->grava('CallCenter Ativo - Import', 'Importação do CallCenter Ativo', 'Ok');
            return true;
        }
    }
    
    public function importCallCenterReceptivo0800(){
        
        $this->logTarefaAgendada->grava('CallCenter Receptivo 0800 - Import', 'Importação dos dados', 'Iniciado');
        
        $this->db->trans_begin();
        
        $log['nome'] = 'CallCenter';
        $log['localizacao'] = '';
        $log['md5file'] = '';
        $log['fonte'] = 'CALLCENTER - ATIVO';
        
        $logArquivo = $this->logArquivo->grava($log);
        
        $dados = $this->ura->importCallCenter('0800');
        #echo '<pre>'; print_r($dados); exit();
        if($logArquivo){
            
            foreach($dados as $da){
                #echo md5($ha); echo '<br>';
                #echo $ha; echo '<br>';
                #print_r( str_replace('/','-',$dado)); echo '<br>'; 
                
                $fim = $da->fim;
                $origem = $da->origem;
                $destino = $da->destino;
                $segundos = $da->segundos;
                $tipo = $da->tipo;
                
                switch ($tipo) {
                    case 'celularLocal': # Celular local
                        $ti = "celular";
                        break;
                    case 'celularLDN': # Celular à dintância
                        $ti = "celularddd";
                        break;
                    case 'fixoLocal': # Fixo local
                        $ti = "fixo";
                        break;
                    default: # Fixo à distância
                        $ti = "fixoddd";
                }
                
                $custo = str_replace(',', '.', $this->util->telefoniaBilhetacao($segundos,$ti,'callcenter'));
                
                #if($linha == 3){
                    
                    #exit();
                #}
                
                    
                $sql = "INSERT INTO adminti.telefonia_chamadas(cd_log_arquivo, fim, origem, destino, segundos, custo, tipo) ";                    
                $sql .="\n VALUES(".$logArquivo.", '".$fim."', '".$origem."', '".$destino."', ".$segundos.", '".$custo."', '".$tipo."');";
                #echo $sql; exit();
                $this->db->query($sql);                                       
                #print_r($sql); exit();               
                
            }
            
        }
        
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            $this->logTarefaAgendada->grava('CallCenter Receptivo 0800 - Import', 'Importação do CallCenter Receptivo 0800', 'Erro');
            return false;
        }
        else
        {
            $this->db->trans_commit();
            $this->logTarefaAgendada->grava('CallCenter Receptivo 0800 - Import', 'Importação do CallCenter Receptivo 0800', 'Ok');
            return true;
        }
    }
    
    public function importCallCenterReceptivo4000(){
        
        $this->logTarefaAgendada->grava('CallCenter Receptivo 4000 - Import', 'Importação dos dados', 'Iniciado');
        
        $this->db->trans_begin();
        
        $log['nome'] = 'CallCenter';
        $log['localizacao'] = '';
        $log['md5file'] = '';
        $log['fonte'] = 'CALLCENTER - ATIVO';
        
        $logArquivo = $this->logArquivo->grava($log);
        
        $dados = $this->ura->importCallCenter('4000');
        #echo '<pre>'; print_r($dados); exit();
        if($logArquivo){
            
            foreach($dados as $da){
                #echo md5($ha); echo '<br>';
                #echo $ha; echo '<br>';
                #print_r( str_replace('/','-',$dado)); echo '<br>'; 
                
                $fim = $da->fim;
                $origem = $da->origem;
                $destino = $da->destino;
                $segundos = $da->segundos;
                $tipo = $da->tipo;
                
                switch ($tipo) {
                    case 'celularLocal': # Celular local
                        $ti = "celular";
                        break;
                    case 'celularLDN': # Celular à dintância
                        $ti = "celularddd";
                        break;
                    case 'fixoLocal': # Fixo local
                        $ti = "fixo";
                        break;
                    default: # Fixo à distância
                        $ti = "fixoddd";
                }
                
                $custo = str_replace(',', '.', $this->util->telefoniaBilhetacao($segundos,$ti,'callcenter'));
                
                #if($linha == 3){
                    
                    #exit();
                #}
                
                    
                $sql = "INSERT INTO adminti.telefonia_chamadas(cd_log_arquivo, fim, origem, destino, segundos, custo, tipo) ";                    
                $sql .="\n VALUES(".$logArquivo.", '".$fim."', '".$origem."', '".$destino."', ".$segundos.", '".$custo."', '".$tipo."');";
                #echo $sql; exit();
                $this->db->query($sql);                                       
                #print_r($sql); exit();               
                
            }
            
        }
        
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            $this->logTarefaAgendada->grava('CallCenter Receptivo 0800 - Import', 'Importação do CallCenter Receptivo 4000', 'Erro');
            return false;
        }
        else
        {
            $this->db->trans_commit();
            $this->logTarefaAgendada->grava('CallCenter Receptivo 0800 - Import', 'Importação do CallCenter Receptivo 4000', 'Ok');
            return true;
        }
    }
    
    public function informativoFerias(){
        
        $this->load->model('administrador/ferias_model','ferias');
        
        $dados['feriasEntra'] = $this->ferias->feriasEntra();
        $dados['feriasVolta'] = $this->ferias->feriasVolta();
        $msg = $this->load->view('ferias/view_entra_sai_ferias', $dados, true);
        
        if($dados['feriasEntra'] or $dados['feriasVolta']){
        
            $usuario = $this->ferias->usuarioEnviaEmail(3);
            
            $nomeDe = utf8_decode('Sim TV - Ferramenta de negocios | Informe férias');
            $emailDe = 'naoresponda@simtv.com.br';
            $titulo = utf8_decode('Informe de férias');
            $para = 'equipe.sistemas@simtv.com.br';
            #$msg = '<strong>Vistoria - Reenvio</strong> - Segue em anexo<br><br>'.$titulo;
            #$this->util->enviaEmail($nomeDe, $emailDe, 'tiago.costa@simtv.com.br', $titulo, $msg, false);
            foreach($usuario as $usu){
                
                $this->util->enviaEmail($nomeDe, $emailDe, $usu->email_usuario, $titulo, $msg, false);
                #$this->util->enviaEmail($nomeDe, $emailDe, 'tiago.costa@simtv.com.br', $titulo, $msg, false);
            
            }
            
        }
        
        $this->logTarefaAgendada->grava('Informativo de Férias', 'Rotina de verificação', 'Rodou');
        
    }
                
}
