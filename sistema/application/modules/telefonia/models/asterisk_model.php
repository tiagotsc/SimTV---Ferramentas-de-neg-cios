<?php

/**
 * Dashboard_model
 * 
 * Classe que realiza o tratamento do módulo de asterisk
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2015
 * @access public
 */
class Asterisk_model extends CI_Model{
	
	/**
	 * Asterisk_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
        $this->load->model('logArquivo_model','logArquivo');
	}
    
    /**
    * Asterisk_model::operadoras()
    * 
    * Função que pega os dados do servidor asterisk para importação
    * @return As operadoras localizadas
    */
    public function importAsterisk(){
        
        $ultimaData = $this->logArquivo->dataUltimaAcao('SERVIDOR ASTERISK');
        #echo '<pre>'; print_r($ultimaData); exit();
        $sql = "SELECT 
                	calldate as fim,
                	src as origem,
                	dst as destino,
                	billsec as segundos,
                	CASE 
                	WHEN dst RLIKE '^[2-5][0-9]{7}$' OR dst RLIKE '^[0][2][1][2-5][0-9]{7}$'
                	THEN 'fixoLocal' 
                	WHEN dst RLIKE '^[6-9][0-9]{7,8}$' OR dst RLIKE '^[0][2][1][6-9][0-9]{7,8}$'
                	THEN 'celularLocal'
                	WHEN dst RLIKE '^[0][1-9]{2}[2-5][0-9]{7}$'
                	THEN 'fixoLDN'
                	WHEN dst RLIKE '^[0][1-9]{2}[6-9][0-9]{7,8}$'
                	THEN 'celularLDN'
                END as tipo
                FROM asteriskcdrdb.cdr
                WHERE 
                	disposition = 'ANSWERED' AND 
                  (
                		dst RLIKE '^[2-5][0-9]{7}$' OR 
                		dst RLIKE '^[6-9][0-9]{7,8}$' OR
                		dst RLIKE '^[0][1-9]{2}[2-5][0-9]{7}$' OR
                		dst RLIKE '^[0][1-9]{2}[6-9][0-9]{7,8}$'
                	)
                    AND calldate > '".$ultimaData[0]->data."';
                ";
         #echo '<pre>'; print_r($sql);  exit();      
        $conexao = $this->load->database('telefoniaProducao', TRUE);
        
        return $conexao->query($sql)->result();
        
    }

}