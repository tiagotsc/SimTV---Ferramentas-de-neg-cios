<?php

/**
 * Log_model
 * 
 * Classe que realiza o tratamento do módulo de Log
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2015
 * @access public
 */
class Log_model extends CI_Model{
	
	/**
	 * Log_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();       
	}
    
    /**
    * Log_model::grava()
    * 
    * Função que pega todas as Logs  ativas
    * @return As Logs localizadas
    */
    public function grava($dadosLog){
        
        if(isset($dadosLog['idAcao'])){
            $valorIdAcao = $dadosLog['idAcao'];
        }else{
            $valorIdAcao = 'null';
        }
        
        $sql = "INSERT INTO adminti.log(cd_usuario, aplicacao, modulo, funcao, descricao, acao, idAcao) ";
        $sql .= "\n VALUES(".$dadosLog['usuario'].",'".$dadosLog['aplicacao']."', '".$dadosLog['modulo']."','".$dadosLog['funcao']."','".$dadosLog['descricao']."', '".$dadosLog['acao']."', ".$valorIdAcao.");";
        #echo '<pre>'; print_r($dadosLog); exit();
        $this->db->query($sql);
        $cd = $this->db->insert_id();
        
        return $cd;

    }

}