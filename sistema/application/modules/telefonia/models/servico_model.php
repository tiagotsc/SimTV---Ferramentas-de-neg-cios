<?php

/**
 * Dashboard_model
 * 
 * Classe que realiza o tratamento do módulo de serviço
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2015
 * @access public
 */
class Servico_model extends CI_Model{
	
	/**
	 * Servico_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
	}
    
    /**
    * Servico_model::servicos()
    * 
    * Função que pega todas as servicos  ativas
    * @return As servicos localizadas
    */
    public function servicos($cd = false){
        
        if($cd){
            $this->db->where('cd_telefonia_servico', $cd);
        }
        
        $this->db->where('status', 'A');
        $this->db->order_by('nome', 'asc'); 
        
        if($cd){
            
            return $this->db->get('adminti.telefonia_servico')->row();
            
        }else{
        
            return $this->db->get('adminti.telefonia_servico')->result();
        
        }
        
    }
    
    /**
    * Servico_model::insere()
    * 
    * Função que realiza a inserção dos dados da servico na base de dados
    * @return O número de linhas afetadas pela operação
    */
	public function insere(){
		
		$campo = array();
		$valor = array();
        
        #$campo[] = 'criador_usuario';
        #$valor[] = $this->session->userdata('cd');
		foreach($_POST as $c => $v){
			
            if($c <> 'cd_telefonia_servico'){
            
    			$valorFormatado = $this->input->post($c);
    			$valorFormatado = $this->util->formaValorBanco($valorFormatado);
    			
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
        
		$sql = "INSERT INTO adminti.telefonia_servico (".$campos.")\n VALUES(".$valores.");";
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
    * Servico_model::atualiza()
    * 
    * Função que realiza a atualização dos dados da servico na base de dados
    * @return O número de linhas afetadas pela operação
    */
	public function atualiza(){
        
        #$campoValor[] = 'atualizador_usuario = '.$this->session->userdata('cd');
        #$campoValor[] = "data_atualizacao_usuario = '".date('Y-m-d h:i:s')."'";
        
		foreach($_POST as $c => $v){
			
			if($c != 'cd_telefonia_servico'){
				$valorFormatado = $this->input->post($c);
				$valorFormatado = $this->util->formaValorBanco($valorFormatado);
			
				$campoValor[] = $c.' = '.$valorFormatado;
			
			}
		}
		
		$camposValores = implode(', ', $campoValor);
		
        $this->db->trans_begin();
        
		$sql = "UPDATE adminti.telefonia_servico SET ".$camposValores." WHERE cd_telefonia_servico = ".$this->input->post('cd_telefonia_servico').";";
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
    * Servico_model::dados()
    * 
    * Função que monta um array com todos os dados da servico
    * @param $cd Cd da servico para recuperação de dados
    * @return Retorna todos os dados da servico
    */
	public function dados($cd){
        
        $this->db->where('cd_telefonia_servico', $cd);
		$dados = $this->db->get('adminti.telefonia_servico')->result_array(); # TRANSFORMA O RESULTADO EM ARRAY
		
		return $dados[0];
	}
	
    /**
    * Servico_model::campos()
    * 
    * Função que pega os nomes de todos os campos existentes na tabela servico
    * @return Os campos da tabela servico
    */
	public function campos(){
		
		$campos = $this->db->get('adminti.telefonia_servico')->list_fields();
		
		return $campos;
		
	}
    
    /**
     * Servico_model::pesquisa()
     * 
     * lista as servicos existentes de acordo com os parâmetros informados
     * @param $nome da servico que se deseja encontrar
     * @param $pagina Página da paginação
     * @param $sort_by Campo que vai ser ordenado
     * @param $sort_order Tipo de ordeção do campo (Crescente ou decrescente)
     * @param $mostra_por_pagina Página corrente da paginação
     * 
     * @return A lista das servicos
     */
    public function pesquisa($nome = null, $status = null, $pagina = null, $mostra_por_pagina = null, $sort_by = null, $sort_order = null){
        
        // Verifica qual ordenação foi informada
        $sort_order = ($sort_order == 'desc') ? 'desc' : 'asc';
        // Campos da tabela que podem receber ordenação
		$sort_columns = array('cd_telefonia_servico', 'nome', 'qtd', 'valor', 'data_inicio', 'data_fim', 'status');
        // Verifica qual campo foi informado para ordenação
        $sort_by = (in_array($sort_by, $sort_columns)) ? $sort_by : 'nome';
                        
        $this->db->select("
                            cd_telefonia_servico,
                            nome,
                            qtd,
                            valor,
                            data_inicio,
                            data_fim,
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
        
        return $this->db->get('adminti.telefonia_servico', $mostra_por_pagina, $pagina)->result();
    }
    
    /**
     * Servico_model::pesquisaQtd()
     * 
     * Consulta a quantidade de servicos da pesquisa
     * 
     * @param $nome Nome da servico para filtrar a consulta
     * 
     * @param $status Status da servico para filtrar a consulta
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
        return $this->db->get('adminti.telefonia_servico')->result();
    }
    
    /**
     * Servico_model::delete()
     * 
     * Apaga a servico
     * 
     * @return Retorna o número de linhas afetadas
     */
    public function delete(){
        
        $sql = "DELETE FROM adminti.telefonia_servico WHERE cd_telefonia_servico = ".$this->input->post('apg_cd');
        $this->db->query($sql);
        return $this->db->affected_rows();
        
    }

}