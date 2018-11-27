<?php

/**
 * Dashboard_model
 * 
 * Classe que realiza o tratamento do m�dulo de LogArquivo
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2015
 * @access public
 */
class LogTarefaAgendada_model extends CI_Model{
	
	/**
	 * LogTarefaAgendada_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();       
	}
    
    /**
	 * LogTarefaAgendada_model::log()
	 * 
     * Grava o log da opera��o
     * 
     * @param $titulo T�tulo da a��o
     * @param $descricao Descri��o da a��o
     * @param $status Status da a��o
     * 
	 * @return
	 */
    public function grava($titulo, $descricao, $status){
        
        $sql = "INSERT adminti.log_tarefa_agendada(tarefa, descricao, status) ";
        $sql .= "VALUES('".$titulo."','".$descricao."','".$status."')";
       
        $this->db->query($sql);
        
    }

}