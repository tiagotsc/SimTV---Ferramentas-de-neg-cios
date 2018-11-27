<?php

/**
 * Dashboard_model
 * 
 * Classe que realiza o tratamento do módulo de viabilidade
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2015
 * @access public
 */
class Viabilidade_model extends CI_Model{
	
	/**
	 * Viabilidade_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
	}
    
    /**
    * Viabilidade_model::insere()
    * 
    * Função que realiza a inserção dos dados da operadora na base de dados
    * @return O número de linhas afetadas pela operação
    */
	public function insere(){
		
		$campo = array();
		$valor = array();
        
		foreach($_POST as $c => $v){
			
            if($c <> 'cd_telefonia_operadora'){
            
    			$valorFormatado = $this->util->removeAcentos($this->input->post($c));
    			$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
    			
    			$campo[] = $c;
    			$valor[] = $valorFormatado;
            
            }
            
		}
		
		$campos = implode(', ', $campo);
		$valores = implode(', ', $valor);
		
        $this->db->trans_begin();
        
		$sql = "INSERT INTO adminti.telefonia_operadora (".$campos.")\n VALUES(".$valores.");";
		$this->db->query($sql);
        $id = $this->db->insert_id();
        
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return false;
        }
        else
        {
            $this->db->trans_commit();
            
            return $id;
        }
        
	}
	
    /**
    * Viabilidade_model::atualiza()
    * 
    * Função que realiza a atualização dos dados da operadora na base de dados
    * @return O número de linhas afetadas pela operação
    */
	public function atualiza(){
        
		foreach($_POST as $c => $v){
			
			if($c != 'cd_telefonia_operadora'){
				$valorFormatado = $this->util->removeAcentos($this->input->post($c));
				$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
			
				$campoValor[] = $c.' = '.$valorFormatado;
			
			}
		}
		
		$camposValores = implode(', ', $campoValor);
		
        $this->db->trans_begin();
        
		$sql = "UPDATE adminti.telefonia_operadora SET ".$camposValores." WHERE cd_telefonia_operadora = ".$this->input->post('cd_telefonia_operadora').";";
		$this->db->query($sql);
        
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
		
	}
	
    /**
    * Viabilidade_model::dados()
    * 
    * Função que monta um array com todos os dados da operadora
    * @param $cd Cd da operadora para recuperação de dados
    * @return Retorna todos os dados da operadora
    */
	public function dados($cd){
        
        $this->db->where('cd_telefonia_operadora', $cd);
		$dados = $this->db->get('adminti.telefonia_operadora')->result_array(); # TRANSFORMA O RESULTADO EM ARRAY
		
		return $dados[0];
	}
	
    /**
    * Viabilidade_model::campos()
    * 
    * Função que pega os nomes de todos os campos existentes na tabela operadora
    * @return Os campos da tabela operadora
    */
	public function campos(){
		
		$campos = $this->db->get('adminti.telefonia_operadora')->list_fields();
		
		return $campos;
		
	}
    
    /**
     * Viabilidade_model::pesquisa()
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
                            usuario.cd_usuario,
                            matricula_usuario,
                            login_usuario,
                            nome_usuario/*,
                            email_usuario,
                            CASE WHEN status_usuario = 'A'
                                THEN 'Ativo'
                            ELSE 'Inativo' END AS status_usuario,
                            nome_estado,
                            nome_departamento,
                            nome_perfil*/
                            ");

        if($sort_by != '1'){
            $this->db->order_by($sort_by, $sort_order);
        }
        /*
        if($_POST){
            array_pop($_POST);
            foreach($this->input->post() as $campo => $valor){ 
                if($valor != ''){
                    $this->db->like($campo, $valor);
                }
            }
        }
        */
        if($parametros){
            $post = explode('|', $parametros);
            foreach($post as $campoValor){
                $res = explode('=', $campoValor);
                if($res[1] != ''){
                    $this->db->like($res[0], $res[1]);
                }
            }
        }
        
        $this->db->join('adminti.departamento', 'adminti.departamento.cd_departamento = adminti.usuario.cd_departamento', 'left');      
        $this->db->join('adminti.estado', 'adminti.estado.cd_estado = adminti.usuario.cd_estado', 'left');    
        $this->db->join('sistema.config_usuario', 'usuario.cd_usuario = sistema.config_usuario.cd_usuario', 'left');
        $this->db->join('sistema.perfil', 'sistema.perfil.cd_perfil = sistema.config_usuario.cd_perfil', 'left'); 
        
        $dados['id'] = 'cd_usuario';
        $dados['tabela'] = 'usuario';
        $dados['dados'] = $this->db->get('adminti.usuario', $mostra_por_pagina, $pagina)->result();           
        $dados['qtd'] = $this->qtdLinhas($parametros);
        $dados['campos'] = array('cd_usuario', 'matricula_usuario', 'login_usuario', 'nome_usuario'/*, 
                            'email_usuario', 'status_usuario', 'nome_estado', 'nome_departamento', 'nome_perfil'*/);

        return $dados;
    }
    
    public function qtdLinhas($parametros = null){
        
        /*if($_POST){

            foreach($this->input->post() as $campo => $valor){ 
                if($valor != ''){
                    $this->conexao->like($campo, $valor);
                }
            }
        }  */
        
        if($parametros){
            $post = explode('|', $parametros);
            
            foreach($post as $campoValor){
                $res = explode('=', $campoValor);
                
                if($res[1] != ''){
                    $this->db->like($res[0], $res[1]);
                }
            }
        }
        
        return $this->db->get('adminti.usuario')->num_rows(); 
        
    }
    
    /**
     * Viabilidade_model::delete()
     * 
     * Apaga a operadora
     * 
     * @return Retorna o número de linhas afetadas
     */
    public function delete(){
        
        $sql = "DELETE FROM adminti.telefonia_operadora WHERE cd_telefonia_operadora = ".$this->input->post('apg_cd');
        $this->db->query($sql);
        return $this->db->affected_rows();
        
    }

}