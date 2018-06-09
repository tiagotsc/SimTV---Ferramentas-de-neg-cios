<?php

/**
 * Dashboard_model
 * 
 * Classe que realiza o tratamento do m�dulo de centro de custo
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2016
 * @access public
 */
class CentroCusto_model extends CI_Model{
	
    const tabelaDb = 'adminti.centro_custo';
    const tabela = 'centro_custo';
    
	/**
	 * Tinterface_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
	}
    
    public function todosCentroCusto(){
        
        $this->db->select('cd_centro_custo,
                    	centro_custo.cd_departamento AS cd_departamento,
                    	centro_custo.cd_unidade AS cd_unidade,
                    	nome_departamento,
                    	nome AS unidade,
                    	codigo');
        $this->db->join('adminti.departamento', 'departamento.cd_departamento = centro_custo.cd_departamento'); 
        $this->db->join('adminti.unidade', 'unidade.cd_unidade = centro_custo.cd_unidade'); 
        
        $this->db->order_by('nome_departamento', 'asc');
        
        return $this->db->get(self::tabelaDb)->result();
        
    }
    
    public function interfaces($id = false){
        
        if($id){
            $this->db->where('id', $id);
        }
        
        $this->db->where('status', 'A');
        $this->db->order_by('nome', 'asc');
        return $this->db->get(self::tabelaDb)->result();
    }
    
    /**
     * 
     * lista os dados existentes de acordo com os par�metros informados
     * @param $parametros Condi��es para filtro
     * @param $mostra_por_pagina P�gina corrente da pagina��o
     * @param $sort_by Campo que vai ser ordenado
     * @param $sort_order Tipo de orde��o do campo (Crescente ou decrescente)
     * @param $pagina P�gina da pagina��o
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
        $dados['dados'] = $this->db->get(self::tabelaDb, $mostra_por_pagina, $pagina)->result();           
        $dados['qtd'] = $this->qtdLinhas($parametros);
        $dados['campos'] = array('id', 'nome', 'status');

        return $dados;
    }
    
    /**
     * 
     * Quantidade de linhas da consulta
     * @param $parametros Condi��es para filtro
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
        
        return $this->db->get(self::tabelaDb)->num_rows(); 
        
    }

}