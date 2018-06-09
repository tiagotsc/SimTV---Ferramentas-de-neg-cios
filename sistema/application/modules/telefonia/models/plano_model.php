<?php

/**
 * Dashboard_model
 * 
 * Classe que realiza o tratamento do módulo do plano
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2015
 * @access public
 */
class Plano_model extends CI_Model{
	
	/**
	 * Plano_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
	}
    
    /**
    * Plano_model::planos()
    * 
    * Função que pega todas as planos ativas
    * @return As planos localizadas
    */
    public function planos(){
        
        $this->db->where('status', 'A');
        $this->db->order_by('nome', 'asc'); 
        return $this->db->get('adminti.telefonia_plano')->result();
        
    }
    
    /**
    * Plano_model::insere()
    * 
    * Função que realiza a inserção dos dados do plano na base de dados
    * @return O número de linhas afetadas pela operação
    */
	public function insere(){
		
		$campo = array();
		$valor = array();
        
        #$campo[] = 'criador_usuario';
        #$valor[] = $this->session->userdata('cd');
		foreach($_POST as $c => $v){
			
            if($c <> 'cd_telefonia_plano' and $c <> 'nome_tarifa' and $c <> 'valor_tarifa' and $c <> 'inicio_tarifa' and $c <> 'fim_tarifa'){
            
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
        
		$sql = "INSERT INTO adminti.telefonia_plano (".$campos.")\n VALUES(".$valores.");";
        
		$this->db->query($sql);
        $cd = $this->db->insert_id();
        
        $_POST['cd_telefonia_plano'] = $cd;
        
        $this->inserirTarifas();
        
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
    * Plano_model::atualiza()
    * 
    * Função que realiza a atualização dos dados do plano na base de dados
    * @return O número de linhas afetadas pela operação
    */
	public function atualiza(){
        
        #$campoValor[] = 'atualizador_usuario = '.$this->session->userdata('cd');
        #$campoValor[] = "data_atualizacao_usuario = '".date('Y-m-d h:i:s')."'";
        
		foreach($_POST as $c => $v){
			
			if($c != 'cd_telefonia_plano'  and $c != 'nome_tarifa' and $c != 'valor_tarifa' and $c != 'inicio_tarifa' and $c != 'fim_tarifa'){
				$valorFormatado = $this->util->removeAcentos($this->input->post($c));
				$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
			
				$campoValor[] = $c.' = '.$valorFormatado;
			
			}
		}
		
		$camposValores = implode(', ', $campoValor);
		
        $this->db->trans_begin();
        
		$sql = "UPDATE adminti.telefonia_plano SET ".$camposValores." WHERE cd_telefonia_plano = ".$this->input->post('cd_telefonia_plano').";";
		
        $this->db->query($sql);
        
        $this->inserirTarifas();
        
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
    * Plano_model::inserirTarifas()
    * 
    * Função que inseri as tarifas
    * @return Retorna true ou false
    */
    public function inserirTarifas(){
        
        if($this->input->post('nome_tarifa')){
            #echo '<pre>'; print_r($_POST); 
            #echo '<br><br><br><br>';
            
            $sql = "DELETE FROM adminti.telefonia_tarifa WHERE cd_telefonia_plano = ".$this->input->post('cd_telefonia_plano');
            $this->db->query($sql);
            
            foreach($this->input->post('nome_tarifa') as $campo => $dado){
                #echo $campo.' - '.$dado.' - '.$this->input->post('valor_tarifa')[$campo].' - '.$this->input->post('inicio_tarifa')[$campo].' - '.$this->input->post('fim_tarifa')[$campo].'<br>';
                
                $nome = strtoupper($this->util->removeAcentos($dado));
                $valor = $this->input->post('valor_tarifa')[$campo];
                $inicio = $this->util->formaValorBanco($this->input->post('inicio_tarifa')[$campo]);
                $fim = $this->util->formaValorBanco($this->input->post('fim_tarifa')[$campo]);
                
                $sql = "INSERT INTO adminti.telefonia_tarifa(nome, valor, data_inicio, data_fim, cd_telefonia_plano) ";
                $sql .= "\n VALUES('".$nome."','".$valor."',".$inicio.",".$fim.", ".$this->input->post('cd_telefonia_plano').");";
                #echo $sql.'<br>';
                $this->db->query($sql);
            }
            
            #exit();
        }
        
    }
    
    /**
    * Plano_model::tarifasPlano()
    * 
    * Pega as tarifas do plano
    * @return Retorna true ou false
    */
    public function tarifasPlano($cd){
        
        $this->db->select("cd_telefonia_tarifa, nome, valor, DATE_FORMAT(data_inicio, '%d/%m/%Y') AS data_inicio, DATE_FORMAT(data_fim, '%d/%m/%Y') AS data_fim");
        $this->db->where('cd_telefonia_plano', $cd);
        $this->db->order_by('nome', 'asc'); 
		return $dados = $this->db->get('adminti.telefonia_tarifa')->result();
        
    }
	
    /**
    * Plano_model::dados()
    * 
    * Função que monta um array com todos os dados do plano
    * @param $cd Cd do plano para recuperação de dados
    * @return Retorna todos os dados do plano
    */
	public function dados($cd){
        
        $this->db->where('cd_telefonia_plano', $cd);
		$dados = $this->db->get('adminti.telefonia_plano')->result_array(); # TRANSFORMA O RESULTADO EM ARRAY
		
		return $dados[0];
	}
	
    /**
    * Plano_model::campos()
    * 
    * Função que pega os nomes de todos os campos existentes na tabela plano
    * @return Os campos da tabela plano
    */
	public function campos(){
		
		$campos = $this->db->get('adminti.telefonia_operadora')->list_fields();
		
		return $campos;
		
	}
    
    /**
     * Plano_model::pesquisa()
     * 
     * lista os planos existentes de acordo com os parâmetros informados
     * @param $nome da operadora que se deseja encontrar
     * @param $operadora cd da operadora que se deseja encontrar
     * @param $status cd do status que se deseja encontrar
     * @param $pagina Página da paginação
     * @param $sort_by Campo que vai ser ordenado
     * @param $sort_order Tipo de ordeção do campo (Crescente ou decrescente)
     * @param $mostra_por_pagina Página corrente da paginação
     * 
     * @return A lista dos planos
     */
    public function pesquisa($nome = null, $operadora, $status = null, $pagina = null, $mostra_por_pagina = null, $sort_by = null, $sort_order = null){
        
        // Verifica qual ordenação foi informada
        $sort_order = ($sort_order == 'desc') ? 'desc' : 'asc';
        // Campos da tabela que podem receber ordenação
		$sort_columns = array('tplano.nome', 'tplano.cd_telefonia_operadora', 'tplano.status');
        // Verifica qual campo foi informado para ordenação
        $sort_by = (in_array($sort_by, $sort_columns)) ? $sort_by : 'nome';
                        
        $this->db->select("
                            tplano.cd_telefonia_plano,
                            toperadora.nome AS operadora,
                            tplano.nome,
                            CASE WHEN tplano.status = 'A'
                                THEN 'Ativo'
                            ELSE 'Inativo' END AS status
                            ");       
        
        
        if($nome != '0'){
            $this->db->like('tplano.nome', $nome); 
            #$condicao = "nome LIKE '%";
            #$this->db->where($condicao);
        }
        
        if($operadora != '0'){
            #$this->db->like('nome_perfil', $nome); 
            $condicao = "tplano.cd_telefonia_operadora = ".$operadora;
            $this->db->where($condicao);
        }
        
        if($status != '0'){
            #$this->db->like('nome_perfil', $nome); 
            $condicao = "tplano.status = '".$status."'";
            $this->db->where($condicao);
        }
        
        $this->db->order_by($sort_by, $sort_order);  
        $this->db->join('adminti.telefonia_operadora AS toperadora', 'toperadora.cd_telefonia_operadora = tplano.cd_telefonia_operadora'); 
        return $this->db->get('adminti.telefonia_plano AS tplano', $mostra_por_pagina, $pagina)->result();
    }
    
    /**
     * Plano_model::pesquisaQtd()
     * 
     * Consulta a quantidade de planos da pesquisa
     * 
     * @param $nome Nome do plano para filtrar a consulta
     * @param $operadora Operadora do plano para filtrar a consulta
     * @param $status Status do plano para filtrar a consulta
     * 
     * @return Retorna a quantidade
     */
    public function pesquisaQtd($nome = null, $operadora = null, $status = null){
        
        if($nome != '0'){
            #$condicao = "nome_usuario LIKE '%".strtoupper($nome)."%'";
            $this->db->like('tplano.nome', $nome);
            #$this->db->where($condicao);
        }
        
        if($operadora != '0'){
            #$this->db->like('nome_perfil', $nome); 
            $condicao = "tplano.cd_telefonia_operadora = ".$operadora;
            $this->db->where($condicao);
        }
        
        if($status != '0'){
            #$this->db->like('nome_perfil', $nome); 
            $condicao = "tplano.status = '".$status."'";
            $this->db->where($condicao);
        }
        
        $this->db->select('count(*) as total');
        $this->db->join('adminti.telefonia_operadora AS toperadora', 'toperadora.cd_telefonia_operadora = tplano.cd_telefonia_operadora'); 
        return $this->db->get('adminti.telefonia_plano AS tplano')->result();
    }
    
    /**
     * Plano_model::delete()
     * 
     * Apaga o plano
     * 
     * @return Retorna o número de linhas afetadas
     */
    public function delete(){
        
        $sql = "DELETE FROM adminti.telefonia_plano WHERE cd_telefonia_plano = ".$this->input->post('apg_cd');
        $this->db->query($sql);
        return $this->db->affected_rows();
        
    }

}