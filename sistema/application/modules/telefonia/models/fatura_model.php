<?php

/**
 * Dashboard_model
 * 
 * Classe que realiza o tratamento do módulo de Fatura
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2015
 * @access public
 */
class Fatura_model extends CI_Model{
	
	/**
	 * Fatura_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();    
        
        $this->load->library('Util', '', 'util'); 
        $this->load->model('base/logArquivo_model','logArquivo');   
	}
    
    /**
    * Fatura_model::Faturas()
    * 
    * Função que pega todas as Faturas  ativas
    * @return As Faturas localizadas
    */
    public function Faturas(){
        
        $this->db->where('status', 'A');
        $this->db->order_by('nome', 'asc'); 
        return $this->db->get('adminti.telefonia_Fatura')->result();
        
    }
    
    /**
    * Fatura_model::iniciaTransacao()
    * 
    * Inicia a transação
    * 
    */
    public function iniciaTransacao(){
        
        $this->db->trans_begin();
        
    }
    
    /**
    * Fatura_model::finalizaTransacao()
    * 
    * Finaliza a transação
    * 
    */
    public function finalizaTransacao(){
        
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return false;
        }
        else
        {
            $this->db->trans_commit();
            
            return true;
        }        
        
    }
    
    /**
    * Fatura_model::inserirArquivoCallCenter()
    * 
    * Insere os arquivos do CallCenter 
    * 
    */
    public function inserirArquivoCallCenter($handle, $log){
        
        $this->db->trans_begin();
        
        $logArquivo = $this->logArquivo->grava($log);
        
        if($logArquivo){
            
            $linha = 1;
            foreach($handle as $ha){
                #echo md5($ha); echo '<br>';
                #echo $ha; echo '<br>';
                $dado = explode(';', $ha);
                #print_r( str_replace('/','-',$dado)); echo '<br>'; 
                
                $inicio = trim(str_replace('/','-',$dado[0]));
                $fim = trim(str_replace('/','-',$dado[1]));
                $origem = trim($dado[2]);
                $destino = trim($dado[3]);
                $duracao = trim($dado[4]);
                $segundos = trim($dado[5]);
                $tipo = trim($dado[6]);
                
                switch ($tipo) {
                    case 'Celular': # Celular local
                        $ti = "celular";
                        break;
                    case 'DDDCel': # Celular à dintância
                        $ti = "celularddd";
                        break;
                    case 'Fixo': # Fixo local
                        $ti = "fixo";
                        break;
                        
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
                
                if($this->input->post('tipo') == 'CALLCENTER - ATIVO'){
                    $fonte = 'callcenter';
                }elseif($this->input->post('tipo') == 'CALLCENTER - RECEPTIVO - 0800'){
                    $fonte = 'callcenter0800';
                }elseif($this->input->post('tipo') == 'CALLCENTER - RECEPTIVO - 4004'){
                    $fonte = 'callcenter4004';
                }
                
                $custo = str_replace(',', '.', $this->util->telefoniaBilhetacao($segundos,$ti,$fonte));
                
                #if($linha == 3){
                    
                    #exit();
                #}
                
                if($linha > 1){
                    
                    $sql = "INSERT INTO adminti.telefonia_chamadas(cd_log_arquivo, inicio, fim, origem, destino, duracao, segundos, custo, tipo) ";                    
                    $sql .="\n VALUES(".$logArquivo.", '".$inicio."', '".$fim."', '".$origem."', '".$destino."', '".$duracao."', ".$segundos.", '".$custo."', '".$tipo."');";
                    #echo $sql; exit();
                    $this->db->query($sql);                                       
                    #print_r($sql); exit();               
                }
                
                
                $linha++;
            }
            
        }
        
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return false;
        }
        else
        {
            $this->db->trans_commit();
            
            return $cd;
        }
        
    }
    
    /**
    * Fatura_model::gravaQuery()
    * 
    * Monta e grava a query
    * 
    */
    public function gravaQuery($campos, $valores){
        
        $sql = "INSERT INTO adminti.telefonia_febraban(".implode(',', $campos).") VALUES(".implode(',', $valores).");";
        $this->db->query($sql);
        
    }
    
    /**
    * Fatura_model::mesesDisponiveis()
    * 
    * Pega os meses disponíveis
    * 
    */
    public function mesesDisponiveis($fonte = 'ATIVO'){
        
        if($fonte == 'ATIVO'){
            $condiFonte = "AND fonte IN ('CALLCENTER - ATIVO', 'SERVIDOR ASTERISK') ";
        }elseif($fonte == '0800'){
            $condiFonte = "AND fonte IN ('CALLCENTER - RECEPTIVO - 0800') ";
        }elseif($fonte == '4004'){
            $condiFonte = "AND fonte IN ('CALLCENTER - RECEPTIVO - 4004') ";
        }else{
            $condiFonte = "";
        }
        
        $sql = "SELECT 
                	DISTINCT 
                	DATE_FORMAT(fim, '%m/%Y') AS data, 
                	DATE_FORMAT(fim, '%m-%Y') AS data_banco
                FROM adminti.telefonia_chamadas 
                INNER JOIN adminti.log_arquivo ON adminti.log_arquivo.cd_log_arquivo = adminti.telefonia_chamadas.cd_log_arquivo
                WHERE DATE_FORMAT(fim, '%Y-%m') < DATE_FORMAT(CURDATE(), '%Y-%m') 
                ".$condiFonte."
                ORDER BY fim DESC";
        return $this->db->query($sql)->result();  
        
    }
    
    /**
    * Fatura_model::datasLigacaoPresentes()
    * 
    * Pega as datas de ligação disponível
    * 
    */
    public function datasLigacaoPresentes(){
        
        $sql = "SELECT
                	DISTINCT
                	#SUBSTR(dt_ligacao,1,7) AS dt_banco,
                	DATE_FORMAT(dt_ligacao,'%m/%Y') AS data
                FROM adminti.telefonia_febraban
                WHERE dt_ligacao IS NOT NULL
                ORDER BY dt_ligacao";
        
    }

}