<?php

/**
 * Dashboard_model
 * 
 * Classe que realiza o tratamento do módulo de edificação
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2015
 * @access public
 */
class Edificacao_model extends CI_Model{
	
	/**
	 * Edificacao_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
	}
    
    /**
     * Edificacao_model::pesquisa()
     * 
     * lista as operadoras existentes de acordo com os parâmetros informados
     * @param $nome da operadora que se deseja encontrar
     * @param $pagina Página da paginação
     * @param $sort_by Campo que vai ser ordenado
     * @param $sort_order Tipo de ordeção do campo (Crescente ou decrescente)
     * @param $mostra_por_pagina Página corrente da paginação
     * 
     * @return A lista das operadoras
     */
    public function pesquisa($parametros, $mostra_por_pagina, $sort_by, $sort_order, $pagina){
        
        $this->db->select("
                        	id,
                        	controle,
                            contrato,
                            unidade.nome AS permissor,
                            inicio,
                            previsao,
                            CASE WHEN concluido = 'SIM' THEN 'Sim' ELSE 'Nao' END AS concluido,
                            conclusao,
                            CASE 
                            	WHEN concluido = 'SIM' AND conclusao IS NOT NULL
                            		THEN 'OK'
                            	WHEN (concluido = 'NAO' AND previsao >= CURDATE()) OR (concluido = 'NAO' AND previsao IS NULL)
                            		THEN 'PENDENTE'
                           	ELSE 'ATRASADO' END AS situacao_atual
                            ");

        if($sort_by != '1'){
            $this->db->order_by($sort_by, $sort_order);
        }

        if($parametros){
            $post = explode('|', $parametros);
            foreach($post as $campoValor){
                $res = explode('=', $campoValor);
                if($res[1] != ''){
                    
                    if(in_array($res[0], array('cd_unidade'))){
                        $this->db->where('tcom_edificacao.'.$res[0], $res[1]);
                    }else{
                        $this->db->like('tcom_edificacao.'.$res[0], $res[1]);
                    }
                }
            }
        }
        
        $this->db->join('adminti.unidade', 'unidade.cd_unidade = tcom_edificacao.cd_unidade', 'left');   
        #$this->db->join('adminti.estado', 'estado.cd_estado = tcom_edificacao.cd_estado', 'left'); 
        #$this->db->join('sistema.tcom_node', 'tcom_node.id = tcom_edificacao.idNode', 'left');    
        
        $dados['id'] = 'id';
        $dados['tabela'] = utf8_encode('edificação');
        $dados['dados'] = $this->db->get(BANCO_TELECOM.'.tcom_edificacao', $mostra_por_pagina, $pagina)->result();           
        $dados['qtd'] = $this->qtdLinhas($parametros);
        $dados['campos'] = array('id', 'Controle', 'Contrato', 'Permissor', 'Inicio', 'Previsao', 'Concluido', 'Conclusao');

        return $dados;
    }
    
    public function qtdLinhas($parametros = null){
        
        if($parametros){
            $post = explode('|', $parametros);
            
            foreach($post as $campoValor){
                $res = explode('=', $campoValor);
                
                if($res[1] != ''){
                    
                    if(in_array($res[0], array('cd_unidade'))){
                        $this->db->where('tcom_edificacao.'.$res[0], $res[1]);
                    }else{
                        $this->db->like('tcom_edificacao.'.$res[0], $res[1]);
                    }
                }
            }
        }
        
        return $this->db->get(BANCO_TELECOM.'.tcom_edificacao')->num_rows(); 
        
    }
    
    public function permissorTemNode(){
        
        $sql = "SELECT 
                	DISTINCT
                	unidade.* 
                FROM adminti.unidade 
                INNER JOIN ".BANCO_TELECOM.".tcom_node ON tcom_node.cd_unidade = unidade.cd_unidade";
                
        $sql = "SELECT 
                	* 
                FROM adminti.unidade
                WHERE cd_unidade NOT IN (15,16)
                ORDER BY permissor";
        return $this->db->query($sql)->result();
        
    }
    
    public function dadosNovoAssinante($permissor, $assinante){
        # BASE
        $conexao = $this->load->database('oracle', TRUE);
        
        $sql = "SELECT 
                 c.percod AS permissor,
                 c.cpcod AS cod_assinante,
                 TRIM(c.CPNOMAPE) AS nome,
                 TRIM(c.CPTEL) AS telefone,
                 TRIM(c.CPTELCEL) AS celular,
                 TRIM(c.CPNODEND) AS node,
                 TRIM(m.MANDSC)   AS dsc_node,
                 TRIM(c.CPCOBLOG) AS endereco,
                 TRIM(c.CPNUMEND) AS numero_endereco,
                 TRIM(c.CPREVCOM) AS complemento,
                 TRIM(c.cpcobbai) AS bairro,
                 TRIM(c.CPCOBCEP) AS cep,
                    --REGEXP_REPLACE(TRIM(CPCOBCEP),'([0-9]{5})([0-9]{3})','\1-\2') AS cep,
                 TRIM(c.CPCOBCID) AS cidade,
                 TRIM(c.CPCOBUF) AS uf,
                 TRIM(c.CPCOBREF) AS referencia
                FROM cadpro C,manzan M 
                WHERE 
                c.percod=".$permissor." AND c.CPCOD=".$assinante."
                and c.CPNODEND=m.mannro";
                
        return $conexao->query($sql)->row();
        
    }
    
    public function proximoControle($mes){
        
        #$this->db->like('controle', $mes, 'after'); 
        #$qtd = $this->db->count_all_results(BANCO_TELECOM.'.tcom_edificacao');
        
        $sql = "SELECT
                    MAX(CAST(substr(controle,7) AS UNSIGNED)) AS ultimo
                FROM ".BANCO_TELECOM.".tcom_edificacao
                WHERE controle LIKE '".$mes."%'
                ORDER BY CAST(substr(controle,7) AS UNSIGNED)";
        $qtd = $this->db->query($sql)->row()->ultimo;
        return $qtd + 1;
        
    }
    
    public function existeEndereco(){
        
        $this->db->select('id, contrato');
        $this->db->where('endereco',$this->input->post('endereco'));
        $this->db->where('cidade',$this->input->post('cidade'));
        $this->db->where('cd_estado',$this->input->post('cd_estado'));
        $this->db->where('bairro',$this->input->post('bairro'));
        $this->db->where('numero',$this->input->post('numero'));
        
        if($this->input->post('id')){
            $this->db->where('id !=',$this->input->post('id'));
        }
        
        #return $this->db->count_all_results('sistema.tcom_edificacao');
        return $this->db->get(BANCO_TELECOM.'.tcom_edificacao')->result();
        
    }
    
    public function dadosAssinanteMudancaEndereco($permissor, $assinante){
        # OC
        $conexao = $this->load->database('oracle', TRUE);
        
        $sql = "SELECT 
                 a.PERCOD AS permissor,
                 a.abocod AS cod_assinante,
                 TRIM(a.ABONOMAPE) AS nome,
                 TRIM(a.abotel) AS telefone,
                 TRIM(a.abocel) AS celular,
                 a.SECNRO AS node,
                 TRIM(m.MANDSC)   AS dsc_node,
                 TRIM(a.ABOCOBDIR) AS endereco,
                 a.ABOCALNRO AS numero_endereco,
                 TRIM(a.ABOCOBCOMP) AS complemento,
                 TRIM(a.ABOCOBBAR) AS bairro,
                 TRIM(a.ABOCOBZIP) AS cep,
                    --REGEXP_REPLACE(TRIM(ABOCOBZIP),'([0-9]{5})([0-9]{3})','\1-\2') AS cep,
                 TRIM(a.ABOCOBCIU) AS cidade,
                 a.ABOCOBUF AS uf,
                 TRIM(a.ABOCOBAMPNRO) AS referencia
                FROM abonad A,manzan M
                WHERE a.percod =".$permissor."  and a.ABOCOD=".$assinante."
                and a.SECNRO=m.mannro";
        
        return $conexao->query($sql)->row();
        #echo '<pre>'; print_r($conexao->query($sql)->row()); exit();
        
    }
    
    public function dadosEdificacao($id){
        
        $sql = "SELECT 
                	ted.controle,
                    tunidade.cd_unidade,
                	tunidade.nome AS unidade,
                	ted.inicio,
                	ted.previsao,
                	ted.contrato,
                	CASE WHEN ted.origem = 'NOVO' THEN 'Novo Assinante' ELSE 'Mudança de endereço' END AS origem,
                	ted.nome,
                	ted.telefone,
                	ted.celular,
                	tnode.descricao,
                	ted.cep,
                	ted.endereco,
                	ted.cidade,
                	testado.sigla_estado,
                	ted.bairro,
                	ted.numero,
                	ted.complemento,
                	ted.referencia,
                	ted.concluido,
                	ted.observacao
                FROM ".BANCO_TELECOM.".tcom_edificacao AS ted
                LEFT JOIN adminti.unidade AS tunidade ON tunidade.cd_unidade = ted.cd_unidade
                LEFT JOIN ".BANCO_TELECOM.".tcom_node AS tnode ON tnode.id = ted.idNode
                LEFT JOIN adminti.estado AS testado ON testado.cd_estado = ted.cd_estado
                WHERE ted.id = ".$id;
                
        return $this->db->query($sql)->row();
        
    }
    
    public function usuarioEnvioEmail($departamento, $unidade){
        
        $sql = "SELECT 
                		DISTINCT
                		nome_usuario, 
                		email_usuario
                FROM adminti.usuario
                INNER JOIN sistema.config_usuario ON config_usuario.cd_usuario = usuario.cd_usuario
                WHERE 
                	cd_departamento = ".$departamento." 
                	AND cd_unidade = ".$unidade."
                	AND status_config_usuario = 'A'
                	AND status_usuario = 'A'
                	AND email_usuario IS NOT NULL";
                    
        return $this->db->query($sql)->result();
        
    }
    
    public function aval(){
        return $this->db->get(BANCO_TELECOM.'.tcom_aval')->result();
    }

}