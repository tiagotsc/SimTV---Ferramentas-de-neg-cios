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
class EquipModelo_model extends CI_Model{
	
    const tabela = 'tcom_equip_modelo';
    
	/**
	 * Tinterface_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
	}
    
    public function modelos($id = false){
        
        if($id){
            $this->db->where('id', $id);
        }
        
        $this->db->where('status', 'A');
        $this->db->order_by('nome', 'asc');
        return $this->db->get(BANCO_TELECOM.'.'.self::tabela)->result();
    }
    
    public function codigosModelo($id = false){
        
        if($id){
            $this->db->where('idEquipModelo', $id);
        }
        
        $this->db->select("
                            tcom_equip_modelo_codigo.id,
                            tcom_equip_modelo_codigo.identificacao,
                            tcom_equip_modelo_codigo.codigo,
                            tcom_contrato_equip.idContrato");
        
        $this->db->join(BANCO_TELECOM.'.tcom_contrato_equip', 'tcom_contrato_equip.idEquipModCod = tcom_equip_modelo_codigo.id', 'left'); 
        return $this->db->get(BANCO_TELECOM.'.tcom_equip_modelo_codigo')->result();
        
    }
    
    public function equipaMarcaModelo(){
        
        $this->db->select("
                        	tcom_equip_modelo.id,
                            tcom_equip_marca.nome AS marca,
                        	tcom_equip_modelo.nome,
                        	tcom_equip_modelo.status
                            ");
                            
        $this->db->join(BANCO_TELECOM.'.tcom_equip_marca', 'tcom_equip_marca.id = tcom_equip_modelo.idEquipMarca');  
        
        return $this->db->get(BANCO_TELECOM.'.'.self::tabela)->result();
        
    }
    
    public function equipMarcaModeloCodigoDisponiveis(){
        
        $this->db->select("
                            tcom_equip_modelo_codigo.id,
                            tcom_equip_marca.nome AS marca,
                        	tcom_equip_modelo.nome AS modelo,
                            tcom_equip_modelo_codigo.identificacao,
                            tcom_equip_modelo_codigo.codigo");
                            
        $this->db->where("tcom_equip_modelo_codigo.id NOT IN (
                            SELECT idEquipModCod FROM ".BANCO_TELECOM.".tcom_contrato_equip
                        )");
        
        $this->db->join(BANCO_TELECOM.'.tcom_equip_modelo', 'tcom_equip_modelo.id = tcom_equip_modelo_codigo.idEquipModelo');
        $this->db->join(BANCO_TELECOM.'.tcom_equip_marca', 'tcom_equip_marca.id = tcom_equip_modelo.idEquipMarca');
        return $this->db->get(BANCO_TELECOM.'.tcom_equip_modelo_codigo')->result();
        
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
                        	tcom_equip_modelo.id,
                            tcom_equip_marca.nome AS marca,
                        	tcom_equip_modelo.nome,
                        	CASE WHEN tcom_equip_modelo.status = 'A' THEN 'Ativo' Else 'Inativo' END AS status
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
                    }elseif($res[0] == 'identificacao'){
                        $this->db->where("tcom_equip_modelo.id IN (
                                            SELECT 
                                                idEquipModelo 
                                            FROM ".BANCO_TELECOM.".tcom_equip_modelo_codigo 
                                            WHERE identificacao = '".$res[1]."'
                                        )");
                    }elseif($res[0] == 'contrato'){
                        if($res[1] == 'S'){
                            $this->db->where("tcom_equip_modelo.id IN (
                                                SELECT 
                                                	DISTINCT
                                                tcom_equip_modelo_codigo.idEquipModelo
                                                FROM ".BANCO_TELECOM.".tcom_equip_modelo_codigo
                                                INNER JOIN ".BANCO_TELECOM.".tcom_contrato_equip ON tcom_contrato_equip.idEquipModCod = tcom_equip_modelo_codigo.id
                                            )");
                        }
                        if($res[1] == 'N'){
                            $this->db->where("tcom_equip_modelo.id NOT IN (
                                                SELECT 
                                                	DISTINCT
                                                tcom_equip_modelo_codigo.idEquipModelo
                                                FROM ".BANCO_TELECOM.".tcom_equip_modelo_codigo
                                                INNER JOIN ".BANCO_TELECOM.".tcom_contrato_equip ON tcom_contrato_equip.idEquipModCod = tcom_equip_modelo_codigo.id
                                            )");
                        }
                    }else{
                        $this->db->like(self::tabela.'.'.$res[0], $res[1]);
                    }
                }
                
            }
        }
        
        $this->db->join(BANCO_TELECOM.'.tcom_equip_marca', 'tcom_equip_marca.id = tcom_equip_modelo.idEquipMarca');  
        
        $dados['id'] = 'id';
        $dados['dados'] = $this->db->get(BANCO_TELECOM.'.'.self::tabela, $mostra_por_pagina, $pagina)->result();           
        $dados['qtd'] = $this->qtdLinhas($parametros);
        $dados['campos'] = array('id','marca', 'nome', 'status');

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
                    }elseif($res[0] == 'identificacao'){
                        $this->db->where("tcom_equip_modelo.id IN (
                                            SELECT 
                                                idEquipModelo 
                                            FROM ".BANCO_TELECOM.".tcom_equip_modelo_codigo 
                                            WHERE identificacao = '".$res[1]."'
                                        )");
                    }elseif($res[0] == 'contrato'){
                        if($res[1] == 'S'){
                            $this->db->where("tcom_equip_modelo.id IN (
                                                SELECT 
                                                	DISTINCT
                                                tcom_equip_modelo_codigo.idEquipModelo
                                                FROM ".BANCO_TELECOM.".tcom_equip_modelo_codigo
                                                INNER JOIN ".BANCO_TELECOM.".tcom_contrato_equip ON tcom_contrato_equip.idEquipModCod = tcom_equip_modelo_codigo.id
                                            )");
                        }
                        if($res[1] == 'N'){
                            $this->db->where("tcom_equip_modelo.id NOT IN (
                                                SELECT 
                                                	DISTINCT
                                                tcom_equip_modelo_codigo.idEquipModelo
                                                FROM ".BANCO_TELECOM.".tcom_equip_modelo_codigo
                                                INNER JOIN ".BANCO_TELECOM.".tcom_contrato_equip ON tcom_contrato_equip.idEquipModCod = tcom_equip_modelo_codigo.id
                                            )");
                        }
                    }else{
                        $this->db->like(self::tabela.'.'.$res[0], $res[1]);
                    }
                }
                
            }
        }
        
        $this->db->join(BANCO_TELECOM.'.tcom_equip_marca', 'tcom_equip_marca.id = tcom_equip_modelo.idEquipMarca');
        
        return $this->db->get(BANCO_TELECOM.'.'.self::tabela)->num_rows(); 
        
    }
    
    public function salvaCodigos(){
        
        if($this->input->post('cod')){
            foreach($this->input->post('cod') as $key => $cod){
                if(trim($cod) != ''){
                    
                    $identificacao = strtoupper($this->util->removeAcentos(trim($this->input->post('ident')[$key])));
                    $codigo = strtoupper($this->util->removeAcentos(trim($cod)));
                    $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_equip_modelo_codigo(idEquipModelo, identificacao, codigo) VALUES(".$this->input->post('id').",'".$identificacao."','".$codigo."')";
                    $this->db->query($sql);
                }   
            }
        }
        
        if($this->input->post('cod-update')){
            foreach($this->input->post('cod-update') as $id => $cod){
                if(trim($cod) != ''){
                    $identificacao = strtoupper($this->util->removeAcentos(trim($this->input->post('ident-update')[$id])));
                    $codigo = strtoupper($this->util->removeAcentos(trim($cod)));
                    $sql = "UPDATE ".BANCO_TELECOM.".tcom_equip_modelo_codigo SET identificacao='".$identificacao."', codigo = '".$codigo."' WHERE id = ".$id;
                    $this->db->query($sql);
                }   
            }
        }
        
    }

}