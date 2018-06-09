<?php

/**
 * Dashboard_model
 * 
 * Classe que realiza o tratamento do módulo de node
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2015
 * @access public
 */
class Node_model extends CI_Model{
	
	/**
	 * Node_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
	}
    
    private $camposRestritos = array('id', 'celula');
    
    /**
    * Node_model::insere()
    * 
    * Função que realiza a inserção dos dados da operadora na base de dados
    * @return O número de linhas afetadas pela operação
    */
	public function insere(){
		
        $node = explode('-' ,$this->input->post('node'));
        $existe = $this->verificaNode(trim($node[0]), trim($node[1]));
        
        if($existe){
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-warning"><strong>O node informado j&aacute; foi cadastrado no sistema!</strong></div>');
            redirect(base_url('telecom/node/fichaNode'));
            exit();
        }
        
		$campo = array();
		$valor = array();

		foreach($_POST as $c => $v){
			
            if(!in_array($c, $this->camposRestritos)){
                
                if($c == 'node'){
                    
                    $dado = explode('-', $v);
                    $campo[] = 'node';
        			$valor[] = "'".trim($dado[0])."'";
                    $campo[] = 'descricao';
        			$valor[] = "'".trim($dado[1])."'";
                    
                }else{
            
        			$valorFormatado = trim($this->util->removeAcentos($this->input->post($c)));
        			$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
        			
        			$campo[] = $c;
        			$valor[] = $valorFormatado;
                
                }
            }
            
		}

		$campos = implode(', ', $campo);
		$valores = implode(', ', $valor);
		
        $this->db->trans_begin();
        
		$sql = "INSERT INTO sistema.tcom_node (".$campos.")\n VALUES(".$valores.");";
		$this->db->query($sql);
        $cd = $this->db->insert_id();
        
        $this->gravaCelula($cd);
        
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return false;
        }
        else
        {
            $this->db->trans_commit();
            
            return $cd;
        }
        
	}
	
    /**
    * Node_model::atualiza()
    * 
    * Função que realiza a atualização dos dados da operadora na base de dados
    * @return O número de linhas afetadas pela operação
    */
	public function atualiza(){

		foreach($_POST as $c => $v){
			
			if(!in_array($c, $this->camposRestritos)){
			     
                if($c == 'node'){
                    
                    $valor = explode('-', $v);
                    $campoValor[] = "node = '".trim($valor[0])."'";
                    $campoValor[] = "descricao = '".trim($valor[1])."'";
                    
                }else{
             
    				$valorFormatado = trim($this->util->removeAcentos($this->input->post($c)));
    				$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
    			
    				$campoValor[] = $c.' = '.$valorFormatado;
                
                }
			
			}
		}
		
		$camposValores = implode(', ', $campoValor);
		
        $this->db->trans_begin();
     
		$sql = "UPDATE sistema.tcom_node SET ".$camposValores." WHERE id = ".$this->input->post('id').";";
		$this->db->query($sql);
        
        $this->gravaCelula($this->input->post('id'));
        
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return false;
        }
        else
        {
            $this->db->trans_commit();
            return true;
        }
        
		#return $this->db->query($sql); # RETORNA O NÚMERO DE LINHAS AFETADAS
		
	}
    
    public function nodes($unidade = false, $node = false, $descricao = false){
        
        if($unidade){
            $this->db->where('tcom_node.cd_unidade', $unidade);
        }
        
        if($node){
            $this->db->where('node', $node);
        }
        
        if($descricao){
            $this->db->where('descricao', $descricao);
        }
        
        $this->db->join('adminti.unidade', 'unidade.cd_unidade = tcom_node.cd_unidade');
        $this->db->order_by('unidade.permissor', 'asc');
        return $this->db->get('sistema.tcom_node')->result();
    }
    
    public function verificaNode($node, $descricao){
        
        $this->db->where('node', $node);
        $this->db->where('descricao', $descricao);
        return $this->db->get('sistema.tcom_node')->num_rows();
        
    }
    
    public function gravaCelula($id){
        
        $sql = "Delete FROM sistema.tcom_nodeCelula WHERE idNode = ".$id;
        $this->db->query($sql);
        
        if($this->input->post('celula')){
            
            foreach($this->input->post('celula') as $celula){
            
                $sql = "INSERT INTO sistema.tcom_nodeCelula (idNode, tipo) VALUES(".$id.", '".$celula."')";
                $this->db->query($sql);
            
            }
            
        }
        
    }
    
    /**
    * Node_model::dados()
    * 
    * Função que monta um array com todos os dados da operadora
    * @param $cd Cd da operadora para recuperação de dados
    * @return Retorna todos os dados da operadora
    */
	public function celulasNode($id){
        
        $this->db->where('idNode', $id);
		$dados = $this->db->get('sistema.tcom_nodeCelula')->result_array(); # TRANSFORMA O RESULTADO EM ARRAY
		
		return array_column($dados, 'tipo');
	}
    
    /**
     * Node_model::pesquisa()
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
                        	node,
                            descricao,
                        	distancia,
                        	unidade.nome AS permissor,
                        	bairro,
                            nome_estado AS estado,
                        	pop,
                        	cm,
                        	tv,
                        	CONCAT(coordx,',',coordy) AS utm
                            ");

        if($sort_by != '1'){
            $this->db->order_by($sort_by, $sort_order);
        }

        if($parametros){
            $post = explode('|', $parametros);
            foreach($post as $campoValor){
                $res = explode('=', $campoValor);
                if($res[1] != ''){
                    if(in_array($res[0], array('cd_unidade', 'bairro'))){
                        $this->db->where('tcom_node.'.$res[0], $res[1]);
                    }else{
                        $this->db->like('tcom_node.'.$res[0], $res[1]);
                    }
                }
            }
        }
        
        $this->db->join('adminti.unidade', 'unidade.cd_unidade = tcom_node.cd_unidade');   
        $this->db->join('adminti.estado', 'estado.cd_estado = tcom_node.cd_estado', 'left');    
        
        $dados['id'] = 'id';
        $dados['tabela'] = 'node';
        $dados['dados'] = $this->db->get('sistema.tcom_node', $mostra_por_pagina, $pagina)->result();           
        $dados['qtd'] = $this->qtdLinhas($parametros);
        $dados['campos'] = array('id', 'Node', 'Descricao', 'Distancia', 'Permissor', 'Bairro', 'Pop', 'Cm', 'Tv', 'Utm');

        return $dados;
    }
    
    public function qtdLinhas($parametros = null){
        
        if($parametros){
            $post = explode('|', $parametros);
            
            foreach($post as $campoValor){
                $res = explode('=', $campoValor);
                
                if($res[1] != ''){
                    if(in_array($res[0], array('cd_unidade', 'bairro'))){
                        $this->db->where('tcom_node.'.$res[0], $res[1]);
                    }else{
                        $this->db->like('tcom_node.'.$res[0], $res[1]);
                    }
                }
            }
        }
        
        return $this->db->get('sistema.tcom_node')->num_rows(); 
        
    }
    
    public function campoDistinctNode($campo){
        
        $this->db->distinct();
        $this->db->select($campo);
        $this->db->order_by('bairro', 'asc');
        return $this->db->get('sistema.tcom_node')->result();
        
    }
    
    /**
     * Node_model::delete()
     * 
     * Apaga a operadora
     * 
     * @return Retorna o número de linhas afetadas
     */
    /*public function delete(){
        
        $sql = "DELETE FROM adminti.telefonia_operadora WHERE cd_telefonia_operadora = ".$this->input->post('apg_cd');
        $this->db->query($sql);
        return $this->db->affected_rows();
        
    }*/

}