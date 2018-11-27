<?php

/**
 * Dashboard_model
 * 
 * Classe que realiza o tratamento do m�dulo de interface
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2016
 * @access public
 */
class reajuste_model extends CI_Model{
	
    const tabela = 'tcom_indice_reajuste_hist';
    
	/**
	 * Tinterface_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
        
		parent::__construct();
	}
    
    public function anualAplicaAcomulativo(){
        
        /*$sql = "SELECT 
                    timestampdiff(month,data_inicio,data_fim) AS meses,
                    tcom_contrato.data_inicio,
                    tcom_contrato.data_fim,
                    tcom_contrato.numero,
                    tcom_contrato_valor.mens_atual_sem_imposto
                    #tcom_contrato.* 
                FROM tcom_contrato 
                INNER JOIN tcom_contrato_valor ON tcom_contrato_valor.idContrato = tcom_contrato.id
                WHERE 
                timestampdiff(month,data_inicio,data_fim) >= 12 
                AND data_fim > CURDATE()
                AND SUBSTR(data_inicio,6,2) = '08'";*/
                
        $this->db->select("tcom_contrato.id,
                    timestampdiff(month,data_inicio,data_fim) AS meses,
                    tcom_contrato.data_inicio,
                    tcom_contrato.data_fim,
                    tcom_contrato.numero,
                    tcom_contrato_valor.mens_atual_sem_imposto
                    #tcom_contrato.*");
                    
        $this->db->where("timestampdiff(month,data_inicio,data_fim) >= 12");
        $this->db->where("data_fim > CURDATE()");
        $this->db->where("SUBSTR(data_inicio,6,2) = '08'");
        
        $this->db->join(BANCO_TELECOM.'.tcom_contrato_valor', 'tcom_contrato_valor.idContrato = tcom_contrato.id');
        return $this->db->get(BANCO_TELECOM.'.tcom_contrato')->result();
        
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
                        	tcom_indice_reajuste_hist.id,
                       	    DATE_FORMAT(mesAno,'%m/%Y') AS mesano,
                            nome,
                            indice,
                            acomAno AS acomano,
                            acomDozeMeses AS acomdozemeses,
                            CASE WHEN tcom_indice_reajuste_hist.status = 'A' THEN 'Ativo' Else 'Inativo' END AS status
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
        
        $this->db->join(BANCO_TELECOM.'.tcom_indice_reajuste', 'tcom_indice_reajuste.id = idIndiceReajuste'); 
        #$this->db->join('adminti.estado AS estado1', 'estado1.cd_estado = tcom_imposto.cd_estado_origem'); 
        #$this->db->join('adminti.estado AS estado2', 'estado2.cd_estado = tcom_imposto.cd_estado'); 
        
        $dados['id'] = 'id';
        $dados['dados'] = $this->db->get(BANCO_TELECOM.'.'.self::tabela, $mostra_por_pagina, $pagina)->result();        
        $dados['qtd'] = $this->qtdLinhas($parametros);
        #$dados['camposLabel'] = array('efetiva' => 'Efet.%', 'uf' => 'UF orig./dest.', 'base_calculo' => 'Base cal.%', 'reducao' => 'Red.%');
        $dados['campos'] = array('id', 'mesAno', 'nome','indice', 'acomAno', 'acomDozeMeses','status');
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
        
        #$this->db->join(BANCO_TELECOM.'.tcom_servico', 'tcom_servico.id = idServico');
        #$this->db->join('adminti.estado AS estado1', 'estado1.cd_estado = tcom_imposto.cd_estado_origem'); 
        #$this->db->join('adminti.estado AS estado2', 'estado2.cd_estado = tcom_imposto.cd_estado');  
        
        return $this->db->get(BANCO_TELECOM.'.'.self::tabela)->num_rows(); 
        
    }
    
    public function indices($id){
        if($id){
            $this->db->where('id', $id);
        }
        $this->db->where('status', 'A');
        $this->db->order_by('nome', 'asc');
        if($id){
            return $this->db->get(BANCO_TELECOM.'.tcom_indice_reajuste')->row();
        }else{
            return $this->db->get(BANCO_TELECOM.'.tcom_indice_reajuste')->result();
        }
    }
    
}