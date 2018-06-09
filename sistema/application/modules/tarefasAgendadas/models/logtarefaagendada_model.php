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
     * Grava o log da operação
     * 
     * @param $titulo Título da ação
     * @param $descricao Descrição da ação
     * @param $status Status da ação
     * 
	 * @return
	 */
    public function grava($titulo, $descricao, $status){
        
        $sql = "INSERT adminti.log_tarefa_agendada(tarefa, descricao, status) ";
        $sql .= "VALUES('".$titulo."','".$descricao."','".$status."')";
       
        $this->db->query($sql);
        
    }

}