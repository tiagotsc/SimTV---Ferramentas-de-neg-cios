<?php

/**
 * Dashboard_model
 * 
 * Classe que realiza o tratamento do módulo de interface
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2016
 * @access public
 */
class Viabilidade_pend_model extends CI_Model{
	
    const tabela = 'tcom_viab_pend';
    
	/**
	 * Tinterface_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
	}
    
    public function pendenciasViabilidade($id = false){
        
        if($id){
            $this->db->where('idViab', $id);
        }else{
            return false;
        }
        
        $this->db->select("
      		                id,
                            idViab,
                        	status,
                        	pergunta,
                        	DATE_FORMAT(data_cadastro_pergunta,'%d/%m/%Y %H:%i:%s') AS data_cadastro_pergunta,
                        	usuPri.nome_usuario AS usuario_pergunta,
                            resposta,
                        	DATE_FORMAT(data_cadastro_resposta,'%d/%m/%Y %H:%i:%s') AS data_cadastro_resposta,
                        	usuSec.nome_usuario AS usuario_resposta
                            ");
        
        $this->db->join('adminti.usuario AS usuPri', 'usuPri.cd_usuario = cd_usuario_pergunta');
        $this->db->join('adminti.usuario AS usuSec', 'usuSec.cd_usuario = cd_usuario_resposta', 'left');
        $this->db->order_by('data_cadastro_pergunta', 'asc');
        return $this->db->get(BANCO_TELECOM.'.'.self::tabela)->result();

    }
    
    public function dadosPendencia($id){
        
        if($id){
            $this->db->where('id', $id);
        }
        
        $this->db->select("
      		                id,
                            idViab,
                        	status,
                        	pergunta,
                        	DATE_FORMAT(data_cadastro_pergunta,'%d/%m/%Y %H:%i:%s') AS data_cadastro_pergunta,
                        	usuPri.nome_usuario AS usuario_pergunta,
                            resposta,
                        	DATE_FORMAT(data_cadastro_resposta,'%d/%m/%Y %H:%i:%s') AS data_cadastro_resposta,
                        	usuSec.nome_usuario AS usuario_resposta
                            ");
        
        $this->db->join('adminti.usuario AS usuPri', 'usuPri.cd_usuario = cd_usuario_pergunta');
        $this->db->join('adminti.usuario AS usuSec', 'usuSec.cd_usuario = cd_usuario_resposta', 'left');
        $this->db->order_by('data_cadastro_pergunta', 'asc');
        return $this->db->get(BANCO_TELECOM.'.'.self::tabela)->row();
        
    }
    
    /**
     * 
     * lista os dados existentes de acordo com os parâmetros informados
     * @param $parametros Condições para filtro
     * @param $mostra_por_pagina Página corrente da paginação
     * @param $sort_by Campo que vai ser ordenado
     * @param $sort_order Tipo de ordeção do campo (Crescente ou decrescente)
     * @param $pagina Página da paginação
     * 
     * @return A lista das dados
     */
    public function pesquisa($parametros, $mostra_por_pagina, $sort_by, $sort_order, $pagina){
        
        $this->db->select("
                        	id,
                        	nome,
                        	CASE WHEN status = 'A' THEN 'Ativo' Else 'Inativo' END AS status
                            ");

        if($sort_by != '1'){
            $this->db->order_by($sort_by, $sort_order);
        }

        if($parametros){
            $post = explode('|', $parametros);
            foreach($post as $campoValor){
                $res = explode('=', $campoValor);
                
                if($res[1] != ''){
                    if(in_array($res[0], array('status'))){
                        $this->db->where(self::tabela.'.'.$res[0], $res[1]);
                    }else{
                        $this->db->like(self::tabela.'.'.$res[0], $res[1]);
                    }
                }
                
            }
        }  
        
        $dados['id'] = 'id';
        $dados['dados'] = $this->db->get(BANCO_TELECOM.'.'.self::tabela, $mostra_por_pagina, $pagina)->result();           
        $dados['qtd'] = $this->qtdLinhas($parametros);
        $dados['campos'] = array('id', 'nome', 'status');

        return $dados;
    }
    
    /**
     * 
     * Quantidade de linhas da consulta
     * @param $parametros Condições para filtro
     * 
     * @return A quantidade de linhas
     */
    public function qtdLinhas($parametros = null){
        
        if($parametros){
            $post = explode('|', $parametros);
            
            foreach($post as $campoValor){
                $res = explode('=', $campoValor);
                
                if($res[1] != ''){
                    if(in_array($res[0], array('status'))){
                        $this->db->where(self::tabela.'.'.$res[0], $res[1]);
                    }else{
                        $this->db->like(self::tabela.'.'.$res[0], $res[1]);
                    }
                }
            }
        }
        
        return $this->db->get(BANCO_TELECOM.'.'.self::tabela)->num_rows(); 
        
    }

}