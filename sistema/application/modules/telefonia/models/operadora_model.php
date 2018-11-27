<?php

/**
 * Dashboard_model
 * 
 * Classe que realiza o tratamento do módulo de operadora
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2015
 * @access public
 */
class Operadora_model extends CI_Model{
	
	/**
	 * Operadora_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
	}
    
    /**
    * Operadora_model::operadoras()
    * 
    * Função que pega todas as operadoras  ativas
    * @return As operadoras localizadas
    */
    public function operadoras(){
        
        $this->db->where('status', 'A');
        $this->db->order_by('nome', 'asc'); 
        return $this->db->get('adminti.telefonia_operadora')->result();
        
    }
    
    /**
    * Operadora_model::insere()
    * 
    * Função que realiza a inserção dos dados da operadora na base de dados
    * @return O número de linhas afetadas pela operação
    */
	public function insere(){
		
		$campo = array();
		$valor = array();
        
        #$campo[] = 'criador_usuario';
        #$valor[] = $this->session->userdata('cd');
		foreach($_POST as $c => $v){
			
            if($c <> 'cd_telefonia_operadora'){
            
    			$valorFormatado = $this->util->removeAcentos($this->input->post($c));
    			$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
    			
    			$campo[] = $c;
    			$valor[] = $valorFormatado;
            
            }
            
		}
        
        # A senha inícial fica definida com o CPF
        #$campo[] = 'senha_usuario';
		#$valor[] = $this->util->formaValorBanco(md5(str_replace('-', '', str_replace('.', '',$this->input->post('cpf_funcionario')))));
		
		$campos = implode(', ', $campo);
		$valores = implode(', ', $valor);
		
        $this->db->trans_begin();
        
		$sql = "INSERT INTO adminti.telefonia_operadora (".$campos.")\n VALUES(".$valores.");";
		$this->db->query($sql);
        $cd = $this->db->insert_id();
        
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
    * Operadora_model::atualiza()
    * 
    * Função que realiza a atualização dos dados da operadora na base de dados
    * @return O número de linhas afetadas pela operação
    */
	public function atualiza(){
        
        #$campoValor[] = 'atualizador_usuario = '.$this->session->userdata('cd');
        #$campoValor[] = "data_atualizacao_usuario = '".date('Y-m-d h:i:s')."'";
        
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
        
		#return $this->db->query($sql); # RETORNA O NÚMERO DE LINHAS AFETADAS
		
	}
	
    /**
    * Operadora_model::dados()
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
    * Operadora_model::campos()
    * 
    * Função que pega os nomes de todos os campos existentes na tabela operadora
    * @return Os campos da tabela operadora
    */
	public function campos(){
		
		$campos = $this->db->get('adminti.telefonia_operadora')->list_fields();
		
		return $campos;
		
	}
    
    /**
     * Operadora_model::pesquisa()
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
    public function pesquisa($nome = null, $status = null, $pagina = null, $mostra_por_pagina = null, $sort_by = null, $sort_order = null){
        
        // Verifica qual ordenação foi informada
        $sort_order = ($sort_order == 'desc') ? 'desc' : 'asc';
        // Campos da tabela que podem receber ordenação
		$sort_columns = array('nome', 'status');
        // Verifica qual campo foi informado para ordenação
        $sort_by = (in_array($sort_by, $sort_columns)) ? $sort_by : 'nome';
                        
        $this->db->select("
                            cd_telefonia_operadora,
                            nome,
                            CASE WHEN status = 'A'
                                THEN 'Ativo'
                            ELSE 'Inativo' END AS status
                            ");       
        
        
        if($nome != '0'){
            $this->db->like('nome', $nome); 
            #$condicao = "nome LIKE '%";
            #$this->db->where($condicao);
        }
        
        if($status != '0'){
            #$this->db->like('nome_perfil', $nome); 
            $condicao = "status = '".$status."'";
            $this->db->where($condicao);
        }
        
        $this->db->order_by($sort_by, $sort_order);  
        
        return $this->db->get('adminti.telefonia_operadora', $mostra_por_pagina, $pagina)->result();
    }
    
    /**
     * Operadora_model::pesquisaQtd()
     * 
     * Consulta a quantidade de operadoras da pesquisa
     * 
     * @param $nome Nome da operadora para filtrar a consulta
     * 
     * @param $status Status da operadora para filtrar a consulta
     * 
     * @return Retorna a quantidade
     */
    public function pesquisaQtd($nome = null, $status = null){
        
        if($nome != '0'){
            #$condicao = "nome_usuario LIKE '%".strtoupper($nome)."%'";
            $this->db->like('nome', $nome);
            #$this->db->where($condicao);
        }
        
        if($status != '0'){
            #$this->db->like('nome_perfil', $nome); 
            $condicao = "status = '".$status."'";
            $this->db->where($condicao);
        }
        
        $this->db->select('count(*) as total');
        return $this->db->get('adminti.telefonia_operadora')->result();
    }
    
    /**
     * Operadora_model::delete()
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