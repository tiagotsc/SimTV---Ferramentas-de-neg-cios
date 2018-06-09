<?php

/**
 * Dashboard_model
 * 
 * Classe que realiza o tratamento do módulo de LogArquivo
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2015
 * @access public
 */
class LogArquivo_model extends CI_Model{
	
	/**
	 * LogArquivo_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();       
	}
    
    /**
    * LogArquivo_model::LogArquivos()
    * 
    * Função que pega todas as LogArquivos  ativas
    * @return As LogArquivos localizadas
    */
    public function grava($dadosLog){
        
        $sql = "INSERT INTO adminti.log_arquivo(nome, localizacao, md5file, fonte) ";
        $sql .= "\n VALUES('".$dadosLog['nome']."','".$dadosLog['localizacao']."','".$dadosLog['md5file']."','".$dadosLog['fonte']."');";
        #echo '<pre>'; print_r($dadosLog); exit();
        $this->db->query($sql);
        $cd = $this->db->insert_id();
        
        return $cd;

    }
    
    public function existenciaArquivo($md5file){
        
        $this->db->where('md5file', $md5file);
        #$this->db->order_by('nome', 'asc');
        return $this->db->get('adminti.log_arquivo')->result();
        
    }
    
    public function dataUltimaAcao($parametro = false){
        
        if($parametro){
        
            $sql = "SELECT MAX(data) AS data FROM adminti.log_arquivo WHERE fonte = '".$parametro."' ORDER BY data DESC";
        
        }else{
            $sql = "SELECT MAX(data) AS data, fonte FROM adminti.log_arquivo WHERE data >= ADDDATE(CURDATE(), INTERVAL -10 DAY) GROUP BY fonte  ORDER BY data DESC";
        }
        #return $this->db->query($sql)->result(); 
        $conexao = $this->load->database('adminti', TRUE);
        
        return $conexao->query($sql)->result();
        
    }

}