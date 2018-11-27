<?php

/**
 * Dashboard_model
 * 
 * Classe que realiza o tratamento do módulo de ura
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2015
 * @access public
 */
class Ura_model extends CI_Model{
	
    const banco = 'telefonia';
    
	/**
	 * Ura_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
	}
    
    function nodes($permissor = false){
        
        $conexao = $this->load->database('oracle', TRUE);
        /*$sql = "select 
                    distinct 
                    TRIM(Z.GRPPERCOD) AS GRPPERCOD,TRIM(M.MANNRO) AS MANNRO,TRIM(M.MANDSC) AS MANDSC
                from manzan M, ZOGRCA Z 
                where M.SECNRO=Z.SECNRO and M.MANNRO=Z.MANNRO ";
                
        if($permissor != false){
            $sql .= "and Z.GRPPERCOD = ".$permissor;
        }*/
        
        $sql = "select 
                    distinct
                	p.percod AS GRPPERCOD,
                	M.MANNRO AS MANNRO,
                	M.MANDSC AS MANDSC
                from manzan M,PERESTCIU P 
                where M.ESTACOD=P.ESTACOD and P.DISTCOD = M.DISTCOD ";
                
        if($permissor != false){
        $sql .= "AND p.percod = ".$permissor;
        }
        
                
        return $conexao->query($sql)->result();
        
    }
    
    /**
    * Ura_model::insere()
    * 
    * Função que realiza a inserção dos dados da ura na base de dados
    * @return O número de linhas afetadas pela operação
    */
	public function insereNode(){
		
        if($this->input->post('dados_node') == 'todos'){
            return $this->insereTodosNode();
        }else{
            return $this->insereUmNode();
        }

	}
    
    public function insereUmNode(){
        
        $dadosNode = explode('-',$this->input->post('dados_node'));
        
        $permissor = $this->input->post('cd_permissor');
        
        $nodeNum = $dadosNode[0];
        $nodeDes = $dadosNode[1];
        
        $dataFim = $this->util->formataData($this->input->post('data_fim'), 'USA').' '.$this->input->post('hora_fim');
        $tipo = $this->util->formaValorBanco($this->input->post('tipo'));
        $status = $this->input->post('status');
        
        $conexao = $this->load->database('ura', TRUE);
        $conexao->trans_begin();
        
		$sql = "INSERT INTO ".self::banco.".outageNode (percod, nodeNro, nodeDsc, dataInicio, dataFim, tipo, origem, status)\n ";
        $sql .= "VALUES('".$permissor."', '".trim($nodeNum)."', '".trim(utf8_decode($nodeDes))."', '".date('Y-m-d h:i:s')."', '".$dataFim."', ".$tipo.", '".$this->session->userdata('nome')."', '".$status."');";
        $conexao->query($sql);
        $id = $conexao->insert_id();
        
        if($this->input->post('observacao')){
            $sql = "INSERT INTO ".self::banco.".outageNodeObs(idOutageNode,observacao, autor)";
            $sql .= " VALUES(".$id.",'".addslashes($this->input->post('observacao'))."','".$this->session->userdata('nome')."')";
            $conexao->query($sql);
        }
        
        if ($conexao->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return false;
        }
        else
        {
            $conexao->trans_commit();
            
            return true;
        }
        
    }
    
    public function insereTodosNode(){
         
        $todos = explode(',',$this->input->post('todos_node'));
        
        $permissor = $this->input->post('cd_permissor');
        
        $dataFim = $this->util->formataData($this->input->post('data_fim'), 'USA').' '.$this->input->post('hora_fim');
        $tipo = $this->util->formaValorBanco($this->input->post('tipo'));
        $status = $this->input->post('status');
        
        $conexao = $this->load->database('ura', TRUE);
        $conexao->trans_begin();
        
        foreach($todos as $to){
            
            $dadosNode = explode('-',$to);
            $nodeNum = $dadosNode[0];
            $nodeDes = $dadosNode[1];
        
    		$sql = "INSERT INTO ".self::banco.".outageNode (percod, nodeNro, nodeDsc, dataInicio, dataFim, tipo, origem, status)\n ";
            $sql .= "VALUES('".$permissor."', '".trim($nodeNum)."', '".trim(utf8_decode($nodeDes))."', '".date('Y-m-d H:i:s')."', '".$dataFim."', ".$tipo.", '".$this->session->userdata('nome')."', '".$status."');";
    		
            $conexao->query($sql);
            $id = $conexao->insert_id();
            
            if($this->input->post('observacao')){
                $sql = "INSERT INTO ".self::banco.".outageNodeObs(idOutageNode,observacao, autor)";
                $sql .= " VALUES(".$id.",'".addslashes($this->input->post('observacao'))."','".$this->session->userdata('nome')."')";
                $conexao->query($sql);
            }
        
        }
        
        if ($conexao->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return false;
        }
        else
        {
            $conexao->trans_commit();
            
            return true;
        }
        
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
        
        $conexao = $this->load->database('ura', TRUE);
        
        $conexao->select("
                        	id,
                            percod, 
                            nodeDsc,
                            nodeNro, 
                            dataInicio, 
                            dataFim, 
                            origem,
                            tipo, 
                            CASE WHEN CURRENT_TIMESTAMP() >= dataInicio AND CURRENT_TIMESTAMP() <= dataFim
                            	THEN 'andamento'
                             WHEN CURRENT_TIMESTAMP() < dataInicio
                            	THEN 'futuro'
                             WHEN CURRENT_TIMESTAMP() > dataFim
                            	THEN 'passou'
                            ELSE 'sem definicao' END AS dataStatus,
                            status
                            ");

        if($sort_by != '1'){
            $conexao->order_by($sort_by, $sort_order);
        }

        if($parametros){
            $post = explode('|', $parametros);
            foreach($post as $campoValor){
                $res = explode('=', $campoValor);
                if($res[1] != ''){
                    if(in_array($res[0], array('percod','nodeDesc','tipo','status'))){
                        $conexao->where(self::banco.'.outageNode'.'.'.$res[0], $res[1]);
                    }else{
                        $conexao->like(self::banco.'.outageNode'.'.'.$res[0], $res[1]);
                    }
                }
            }
        } 
        
        $dados['id'] = 'id';
        $dados['dados'] = $conexao->get(self::banco.'.outageNode', $mostra_por_pagina, $pagina)->result();           
        $dados['qtd'] = $this->qtdLinhas($parametros);
        $dados['camposLabel'] = array('percod'=>'Permissor', 'nodeDsc'=>'N&uacute;mero', 'tipo' => 'Descri&ccedil;&atilde;o', 'dataInicio' => 'Data In&iacute;cio', 'dataFim' => 'Data Fim');
        $dados['campos'] = array('id', 'percod', 'nodeDsc', 'tipo', 'dataInicio', 'dataFim', 'origem', 'status');

        return $dados;
    }
    
    public function qtdLinhas($parametros = null){
        
        $conexao = $this->load->database('ura', TRUE);
        
        if($parametros){
            $post = explode('|', $parametros);
            foreach($post as $campoValor){
                $res = explode('=', $campoValor);
                if($res[1] != ''){
                    if(in_array($res[0], array('percod','nodeDsc','tipo','status'))){
                        $conexao->where(self::banco.'.outageNode'.'.'.$res[0], $res[1]);
                    }else{
                        $conexao->like(self::banco.'.outageNode'.'.'.$res[0], $res[1]);
                    }
                }
            }
        }

        return $conexao->get(self::banco.'.outageNode')->num_rows(); 
        
    }
    
    public function nodesCadastrados($status = ''){
        
        $conexao = $this->load->database('ura', TRUE);
        $conexao->query("SET GLOBAL max_connect_errors=10000");
        
        $sql = "SELECT
                	tabela.*,
                	outageNodeObs.observacao,
                	DATE_FORMAT(outageNodeObs.data_cadastro, '%d/%m/%Y %H:%i') AS dtObs
                FROM (
                	SELECT 
                		outageNode.id, 
                		CASE 
                		 WHEN percod = '52'
                		 THEN '52 - Aracaju'
                		 WHEN percod = '53'
                		 THEN '53 - Feira de Santana'
                		 WHEN percod = '73'
                		 THEN '73 - Jaboatão'
                		 WHEN percod = '61'
                		 THEN '61 - Niterói'
                		 WHEN percod = '74'
                		 THEN '74 - Paulista'
                		 WHEN percod = '51'
                		 THEN '51 - Salvador'
                		 WHEN percod = '99'
                		 THEN '99 - Várzea Grande'
                		 WHEN percod = '91'
                		 THEN '91 - Cuiabá'
                		 WHEN percod = '82'
                		 THEN '82 - Gravataí'
                		 WHEN percod = '64'
                		 THEN '64 - Juiz de Fora'
                		 WHEN percod = '72'
                		 THEN '72 - Olinda'
                		 WHEN percod = '71'
                		 THEN '71 - Recife'
                		 WHEN percod = '62'
                		 THEN '62 - São Gonçalo'
                		 WHEN percod = '63'
                		 THEN '63 - Volta Redonda'
                		 ELSE '' END AS permissor, 
                		nodeNro,
                        nodeDsc, 
                		tipo, 
                		DATE_FORMAT(dataInicio, '%d/%m/%Y %H:%i') AS dataInicio, 
                		DATE_FORMAT(dataFim, '%d/%m/%Y %H:%i') AS dataFim, 
                		origem, 
                		CASE WHEN CURRENT_TIMESTAMP() >= dataInicio AND CURRENT_TIMESTAMP() <= dataFim
                		 THEN 'Funcionando'
                		 WHEN CURRENT_TIMESTAMP() < dataInicio
                		 THEN 'Futuramente'
                		 WHEN CURRENT_TIMESTAMP() > dataFim
                		 THEN 'Expirou'
                		 ELSE 'Nada definido' END AS dataStatus, 
                		status, 
                		/*(
                		 SELECT 
                		 observacao 
                		 FROM telefonia.outageNodeObs 
                		 WHERE idOutageNode = outageNode.id
                		 ORDER BY data_cadastro DESC LIMIT 1
                		 ) observacao*/
                		MAX(outageNodeObs.id) AS idObs
                	FROM (".self::banco.".outageNode)
                	LEFT JOIN ".self::banco.".outageNodeObs ON idOutageNode = outageNode.id
                	WHERE 1=1 AND status =  'Ativo'
                	GROUP BY percod, nodeNro, nodeDsc, dataInicio, dataFim, status
                	#ORDER BY outageNodeObs.data_cadastro desc
                ) AS tabela 
                LEFT JOIN ".self::banco.".outageNodeObs ON outageNodeObs.id = idObs
                ORDER BY idObs DESC";
        
        return $conexao->query($sql)->result();
        
    }
    
    public function pegaTodasObs($id){
        
        $conexao = $this->load->database('ura', TRUE);
        
        $conexao->select("
                            observacao,
                            autor,
                            DATE_FORMAT(data_cadastro,'%d/%m/%Y %H:%i:%s') AS data_cadastro                          
                            ");   
        
        $conexao->where('idOutageNode', $id);
        $conexao->order_by('outageNodeObs.data_cadastro', 'desc');
        return $conexao->get(self::banco.'.outageNodeObs')->result();
        #$conexao->get(self::banco.'.outageNodeObs')->result();
        #echo '<pre>'; print_r($conexao->last_query()); exit();
        
    }
    
    public function tipos(){
        
        $conexao = $this->load->database('ura', TRUE);
        
        $conexao->select("
                            id,
                            nome                          
                            ");   
        
        $conexao->where('status', 'A');
        $conexao->order_by('nome', 'asc');
        return $conexao->get(self::banco.'.evento_tipo')->result();
        #$conexao->get(self::banco.'.outageNodeObs')->result();
        #echo '<pre>'; print_r($conexao->last_query()); exit();
        
    }
    
    
    public function alteraStatusNode(){
        
        $dataFim = false;
        $horaFim = false;
        
        $conexao = $this->load->database('ura', TRUE);
        
        $conexao->trans_begin();
        
        if(strlen($this->input->post('alt_data_fim'))== 10){
            $dataFim = $this->util->formataData($this->input->post('alt_data_fim'), 'USA');
        }
        
        if(strlen($this->input->post('alt_hora_fim')) == 5){
            $horaFim = $this->input->post('alt_hora_fim');
        }
        
        $tipo = $this->util->formaValorBanco($this->input->post('tipo'));
        
		$sql = "UPDATE 
                    ".self::banco.".outageNode SET 
                    status = '".$this->input->post('status')."', tipo = ".$tipo; 
        
        if($dataFim and $horaFim){
            $sql .= ", dataFim = '".$dataFim." ".$horaFim."'";
        }
                    
        $sql .= " WHERE id = ".$this->input->post('alt_cd_node').";";
        
		$conexao->query($sql);
        
        if($this->input->post('observacao')){
            $sql = "INSERT INTO ".self::banco.".outageNodeObs(idOutageNode,observacao, autor)";
            $sql .= " VALUES(".$this->input->post('alt_cd_node').",'".addslashes($this->input->post('observacao'))."','".$this->session->userdata('nome')."')";
            $conexao->query($sql);
        }
        
        if ($conexao->trans_status() === FALSE)
        {
            $conexao->trans_rollback();
            return false;
        }
        else
        {
            $conexao->trans_commit();
            return true;
        }
        
    }
    
    public function alteraStatusNodeTodos(){
        
        $dataFim = false;
        $horaFim = false;
        
        $idSelecionados = explode(',',$this->input->post('idSelecionados'));
        
        $conexao = $this->load->database('ura', TRUE);
        
        $conexao->trans_begin();
        
        if(strlen($this->input->post('alt_data_fim_todos'))== 10){
            $dataFim = $this->util->formataData($this->input->post('alt_data_fim_todos'), 'USA');
        }
        
        if(strlen($this->input->post('alt_hora_fim_todos')) == 5){
            $horaFim = $this->input->post('alt_hora_fim_todos');
        }
        
        $tipo = $this->util->formaValorBanco($this->input->post('tipo'));
        
        foreach($idSelecionados as $idSelec){
        
    		$sql = "UPDATE 
                        ".self::banco.".outageNode SET 
                        status = '".$this->input->post('status_todos')."', tipo = ".$tipo; 
            
            if($dataFim and $horaFim){
                $sql .= ", dataFim = '".$dataFim." ".$horaFim."'";
            }
                      
            $sql .= " WHERE id = ".$idSelec.";";
            
    		$conexao->query($sql);
            
            if($this->input->post('observacao')){
                $sql = "INSERT INTO ".self::banco.".outageNodeObs(idOutageNode,observacao, autor)";
                $sql .= " VALUES(".$idSelec.",'".addslashes($this->input->post('observacao'))."','".$this->session->userdata('nome')."')";
                $conexao->query($sql);
            }
        
        }
        
        
        
        if ($conexao->trans_status() === FALSE)
        {
            $conexao->trans_rollback();
            return false;
        }
        else
        {
            $conexao->trans_commit();
            return true;
        }
        
    }
    
    public function importCallCenter($tipoFonte){
        
        $conexao = $this->load->database('ura', TRUE);
        
        $sql = "SELECT * FROM ".self::banco.".outageNode LIMIT 10";
        
        return $conexao->query($sql)->result();
        
    }
    
    /**
     * Ura_model::delete()
     * 
     * Apaga a ura
     * 
     * @return Retorna o número de linhas afetadas
     */
    public function delete(){
        
        $sql = "DELETE FROM adminti.telefonia_ura WHERE cd_telefonia_ura = ".$this->input->post('apg_cd');
        $this->db->query($sql);
        return $this->db->affected_rows();
        
    }

}