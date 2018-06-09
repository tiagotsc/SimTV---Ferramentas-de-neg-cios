<?php

/**
 * Dashboard_model
 * 
 * Classe que realiza o tratamento do módulo de contrato
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2016
 * @access public
 */
class Backlog_model extends CI_Model{
    
    const tabela = 'tcom_analise_financ';
    
    /**
	 * Contrato_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
        
        $this->load->model('tcom-contrato/log_contrato_model','contratoLog');
        
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
     * @return A lista dos dados
     */
    public function pesquisa($parametros, $mostra_por_pagina, $sort_by, $sort_order, $pagina){
        
        $campoTbOrderBy = array(
                        'contrato'=>'tcom_contrato.numero',
                        'data_aprovacao'=>'tcom_viab_resp.data_aprovacao',
                        'prazo_ativacao'=>'tcom_viab_resp.prazo_ativacao',
                        'unidade'=>'tcom_viab.cd_unidade',
                        'tipo'=>'tcom_viab.idViabTipo',
                        'historico'=>'tcom_status_hist.nome',
                        'ativou'=>'tcom_viab_resp.ativou',
                        'dias_para_ativacao'=>'DATEDIFF(tcom_viab_resp.prazo_ativacao,tcom_viab_resp.data_aprovacao)',
                        'dias_de_atrasos'=>'DATEDIFF(CURDATE(), tcom_viab_resp.prazo_ativacao)'
                        );
        
        $this->db->select("
                            tcom_contrato.id,
                            tcom_viab_resp.id AS idViabResp,
                    		numero AS contrato,
                        	DATE_FORMAT(data_aprovacao, '%d/%m/%Y %H:%i:%s') AS data_aprovacao,
                        	DATE_FORMAT(prazo_ativacao, '%d/%m/%Y') AS prazo_ativacao,
                        	/*CASE 
                        		WHEN (SELECT COUNT(*) FROM ".BANCO_TELECOM.".tcom_viab_resp_hist WHERE idViabResp = tcom_viab_resp.id) > 0
                        			THEN 
                        				(
                        				SELECT 
                        					tcom_status_hist.nome
                        				FROM ".BANCO_TELECOM.".tcom_viab_resp_hist 
                        				INNER JOIN ".BANCO_TELECOM.".tcom_status_hist ON tcom_status_hist.id = idStatusHist
                        				WHERE idViabResp = tcom_viab_resp.id
                        				ORDER BY tcom_viab_resp_hist.data_cadastro DESC LIMIT 1
                        				) 
                        	ELSE '-' END AS status,*/
                            tcom_status_hist.nome AS historico,
                        	unidade.nome AS unidade,
                        	tcom_viab_tipo.nome AS tipo,
                        	aprovacao,
                        	ativou,
                        	CASE 
                        		WHEN ativou = 'N'
                        			THEN DATEDIFF(prazo_ativacao,data_aprovacao)
                        	ELSE '0' END AS dias_para_ativacao,
                        	CASE 
                        			WHEN ativou = 'N' AND prazo_ativacao < CURDATE()
                        				THEN DATEDIFF(CURDATE(), prazo_ativacao)
                        	ELSE '0' END AS dias_de_atrasos,
                        	CASE
                        		WHEN ativou = 'N' AND prazo_ativacao < CURDATE() AND idStatusHist != 8
                        			THEN 'ATRASADO'
                        		WHEN (ativou = 'N' AND prazo_ativacao > CURDATE() AND idStatusHist != 8) OR (ativou = 'N' AND idStatusHist != 8) OR  idStatusHist IS NULL
                        			THEN 'PENDENTE'
                        	ELSE 'CONCLUIDO' END AS andamento
                            ");

        if($sort_by != '1'){
            $campoTb = (isset($campoTbOrderBy[$sort_by]))? $campoTbOrderBy[$sort_by]: $sort_by;
            $this->db->order_by($campoTb, $sort_order);
        }

        if($parametros){
            $post = explode('|', $parametros);
            foreach($post as $campoValor){
                $res = explode('=', $campoValor);
                if($res[1] != ''){
                    
                    if(in_array($res[0], array('numero'))){
                        $this->db->like('tcom_contrato.'.$res[0], $res[1]);
                    }elseif(in_array($res[0], array('historico'))){
                        $this->db->where('tcom_viab_resp.idStatusHist', $res[1]);
                    }else{
                        $this->db->where('tcom_viab.'.$res[0], $res[1]);
                    }
                    
                }
            }
        } 
        
        $this->db->where('aprovacao', 'S');
        $this->db->where("ativou != 'S'");
        $this->db->where("(idStatusHist != '8' OR idStatusHist IS NULL)");
        $this->db->where("tcom_contrato.status = 'P'");
        
        $this->db->join(BANCO_TELECOM.'.tcom_viab', 'tcom_viab_resp.idViab = tcom_viab.id');
        $this->db->join('adminti.unidade', 'unidade.cd_unidade = tcom_viab.cd_unidade');
        $this->db->join(BANCO_TELECOM.'.tcom_viab_tipo', 'tcom_viab_tipo.id = tcom_viab.idViabTipo');
        $this->db->join(BANCO_TELECOM.'.tcom_contrato', 'tcom_contrato.id = CASE WHEN tcom_viab_resp.idContratoAtual IS NOT NULL THEN tcom_viab_resp.idContratoAtual ELSE tcom_viab_resp.idContrato END', 'left');
        $this->db->join(BANCO_TELECOM.'.tcom_status_hist', 'tcom_viab_resp.idStatusHist = tcom_status_hist.id', 'left');
        /*if($this->session->userdata('cd') == 6){
        $this->db->get(BANCO_TELECOM.'.tcom_viab_resp', $mostra_por_pagina, $pagina)->result();
        echo '<pre>'; echo $this->db->last_query(); exit();
        }*/
        $dados['id'] = 'id';
        $dados['dados'] = $this->db->get(BANCO_TELECOM.'.tcom_viab_resp', $mostra_por_pagina, $pagina)->result();           
        $dados['qtd'] = $this->qtdLinhas($parametros);
        $dados['camposLabel'] = array('dias_para_ativacao'=>'diasAtivacao', 'dias_de_atrasos'=>'diasAtrasos', 'data_aprovacao'=>'aprovacao', 'prazo_ativacao'=>'prazo');
        $dados['campos'] = array('id','contrato', 'data_aprovacao', 'prazo_ativacao', 'historico', 'unidade', 'tipo', /*'aprovacao', 'ativou',*/ 'dias_para_ativacao', 'dias_de_atrasos'/*, 'andamento'*/);

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
                    if(in_array($res[0], array('numero'))){
                        $this->db->like('tcom_contrato.'.$res[0], $res[1]);
                    }elseif(in_array($res[0], array('historico'))){
                        $this->db->where('tcom_viab_resp.idStatusHist', $res[1]);
                    }else{
                        $this->db->where('tcom_viab.'.$res[0], $res[1]);
                    }
                }
            }
        } 
        
        $this->db->where('aprovacao', 'S');
        $this->db->where("ativou != 'S'");
        $this->db->where("(idStatusHist != '8' OR idStatusHist IS NULL)");
        $this->db->where("tcom_contrato.status = 'P'");
         
        $this->db->join(BANCO_TELECOM.'.tcom_viab', 'tcom_viab_resp.idViab = tcom_viab.id');
        $this->db->join('adminti.unidade', 'unidade.cd_unidade = tcom_viab.cd_unidade');
        $this->db->join(BANCO_TELECOM.'.tcom_viab_tipo', 'tcom_viab_tipo.id = tcom_viab.idViabTipo');
        $this->db->join(BANCO_TELECOM.'.tcom_contrato', 'tcom_contrato.id = CASE WHEN tcom_viab_resp.idContratoAtual IS NOT NULL THEN tcom_viab_resp.idContratoAtual ELSE tcom_viab_resp.idContrato END', 'left');
        $this->db->join(BANCO_TELECOM.'.tcom_status_hist', 'tcom_viab_resp.idStatusHist = tcom_status_hist.id', 'left');
        
        return $this->db->get(BANCO_TELECOM.'.tcom_viab_resp')->num_rows(); 
        
    }
    
}