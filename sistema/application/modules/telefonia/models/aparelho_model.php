<?php

/**
 * Dashboard_model
 * 
 * Classe que realiza o tratamento do módulo do aparelho
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2015
 * @access public
 */
class Aparelho_model extends CI_Model{
	
	/**
	 * Aparelho_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
	}
    
    /**
    * Aparelho_model::insere()
    * 
    * Função que realiza a inserção dos dados do aparelho na base de dados
    * @return O número de aparelhos afetadas pela operação
    */
	public function insere(){
		
		$campo = array();
		$valor = array();
        
        #$campo[] = 'criador_usuario';
        #$valor[] = $this->session->userdata('cd');
		foreach($_POST as $c => $v){
			
            if($c <> 'cd_telefonia_aparelho' and $c <> 'imei'){
            
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
        
		$sql = "INSERT INTO adminti.telefonia_aparelho (".$campos.")\n VALUES(".$valores.");";
        
		$this->db->query($sql);
        $cd = $this->db->insert_id();
        
        $_POST['cd_telefonia_aparelho'] = $cd;
        $this->gravaImei();
        
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
    * Aparelho_model::atualiza()
    * 
    * Função que realiza a atualização dos dados do aparelho na base de dados
    * @return O número de aparelhos afetadas pela operação
    */
	public function atualiza(){
        
        #$campoValor[] = 'atualizador_usuario = '.$this->session->userdata('cd');
        #$campoValor[] = "data_atualizacao_usuario = '".date('Y-m-d h:i:s')."'";
        
		foreach($_POST as $c => $v){
			
			if($c != 'cd_telefonia_aparelho' and $c != 'imei'){
				$valorFormatado = $this->util->removeAcentos($this->input->post($c));
				$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
			
				$campoValor[] = $c.' = '.$valorFormatado;
			
			}
		}
		
		$camposValores = implode(', ', $campoValor);
		
        $this->db->trans_begin();
        
		$sql = "UPDATE adminti.telefonia_aparelho SET ".$camposValores." WHERE cd_telefonia_aparelho = ".$this->input->post('cd_telefonia_aparelho').";";
		$this->db->query($sql);
        
        $this->gravaImei();
        
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
    * Aparelho_model::gravaImei()
    * 
    * Função que grava o mês o IMEI
    * @return 
    */
    public function gravaImei(){
 
        $sql = "DELETE FROM adminti.telefonia_imei WHERE cd_telefonia_aparelho = ".$this->input->post('cd_telefonia_aparelho');
        $this->db->query($sql);
            
        if($this->input->post('imei')){ 
            
            foreach($this->input->post('imei') as $valor){
                
                $imei = strtoupper($this->util->formaValorBanco($valor));
                $sql = "INSERT INTO adminti.telefonia_imei(imei, cd_telefonia_aparelho) ";
                $sql .= "VALUES(".$imei.",".$this->input->post('cd_telefonia_aparelho').");";
                $this->db->query($sql);
                
            }
            
        }
        
    }
    
    /**
    * Aparelho_model::imeiAparelho()
    * 
    * Função que pega o IMEI do aparelho
    * @param $cd Cd do aparelho para recuperação de dados
    * @return Retorna todos os IMEI do aparelho
    */
    public function imeiAparelho($cd){
        
        $this->db->where('cd_telefonia_aparelho', $cd);
		return $this->db->get('adminti.telefonia_imei')->result();
        
    }
	
    /**
    * Aparelho_model::dados()
    * 
    * Função que monta um array com todos os dados do aparelho
    * @param $cd Cd do aparelho para recuperação de dados
    * @return Retorna todos os dados do aparelho
    */
	public function dados($cd){
        
        $this->db->where('cd_telefonia_aparelho', $cd);
		$dados = $this->db->get('adminti.telefonia_aparelho')->result_array(); # TRANSFORMA O RESULTADO EM ARRAY
		
		return $dados[0];
	}
	
    /**
    * Aparelho_model::campos()
    * 
    * Função que pega os nomes de todos os campos existentes na tabela
    * @return Os campos da tabela linha
    */
	public function campos(){
		
		$campos = $this->db->get('adminti.telefonia_aparelho')->list_fields();
		
		return $campos;
		
	}
    
    /**
     * Aparelho_model::pesquisa()
     * 
     * lista os linhas existentes de acordo com os parâmetros informados
     * @param $ddd que se deseja encontrar
     * @param $numero número que se deseja encontrar
     * @param $operadora cd da operadora que se deseja encontrar
     * @param $plano cd da plano que se deseja encontrar
     * @param $status cd da status que se deseja encontrar
     * @param $pagina Página da paginação
     * @param $sort_by Campo que vai ser ordenado
     * @param $sort_order Tipo de ordeção do campo (Crescente ou decrescente)
     * @param $mostra_por_pagina Página corrente da paginação
     * 
     * @return A lista dos linhas
     */
    public function pesquisa($linha = null, $imei = null, $marca = null, $modelo = null, $tipo = null, $status = null, $pagina = null, $mostra_por_pagina = null, $sort_by = null, $sort_order = null){
        
        // Verifica qual ordenação foi informada
        $sort_order = ($sort_order == 'desc') ? 'desc' : 'asc';
        // Campos da tabela que podem receber ordenação 
		$sort_columns = array('taparelho.cd_telefonia_aparelho','imei','tmarca.cd_telefonia_marca', 'taparelho.modelo', 'taparelho.tipo', 'nome_usuario', 'taparelho.status');
        // Verifica qual campo foi informado para ordenação
        $sort_by = (in_array($sort_by, $sort_columns)) ? $sort_by : 'taparelho.modelo';
        
        $this->db->distinct();                
        $this->db->select("
                            taparelho.cd_telefonia_aparelho,
                            tlinha.numero,
                            '' AS imei,
                            tmarca.nome AS marca,
                            taparelho.modelo AS modelo,
                            CASE WHEN taparelho.tipo = 'CEL'
                                THEN 'Celular'
                            ELSE 'Interface' END AS tipo,
                            (SELECT GROUP_CONCAT(timei.imei) FROM adminti.telefonia_imei AS timei WHERE timei.cd_telefonia_aparelho = taparelho.cd_telefonia_aparelho) AS imei,
                            CASE WHEN nome_usuario IS NOT NULL
                                THEN nome_usuario
                            ELSE '-' END AS nome_usuario,
                            taparelho.status
                            ");       
        
        
        if($linha != '0'){ 
            #$this->db->where('taparelho.imei', $imei); 
            $condicao = "tlinha.numero LIKE '%".$linha."%'";
            $this->db->where($condicao);
        }
        
        if($imei != '0'){ 
            #$this->db->where('timei.imei', $imei); 
            #$condicao = "timei.imei LIKE '%".$imei."%'";
            $condicao = "taparelho.cd_telefonia_aparelho IN (SELECT cd_telefonia_aparelho FROM adminti.telefonia_imei WHERE imei LIKE '%".$imei."%')";
            $this->db->where($condicao);
        }
        
        if($marca != '0'){ 
            $this->db->where('tmarca.cd_telefonia_marca', $marca); 
            #$condicao = "nome LIKE '%";
            #$this->db->where($condicao);
        }
        
        if($modelo != '0'){ 
            $this->db->like('taparelho.modelo', $modelo); 
            #$condicao = "tlinha.numero = ".$numero;
            #$this->db->where($condicao);
        }
        
        if($tipo != '0'){ 
            $this->db->where('taparelho.tipo', $tipo);
            #$condicao = "taparelho.tipo = '".$tipo."'";
            #$this->db->where($condicao);
        }
        
        if($status != '0'){ 
            $this->db->where('taparelho.status', $status);
            #$condicao = "taparelho.status = '".$status."'";
            #$this->db->where($condicao);
        }
        
        $this->db->order_by($sort_by, $sort_order);  
        $this->db->join('adminti.telefonia_marca AS tmarca', 'tmarca.cd_telefonia_marca = taparelho.cd_telefonia_marca');
        #$this->db->join('adminti.telefonia_imei AS timei', 'timei.cd_telefonia_aparelho = taparelho.cd_telefonia_aparelho', 'left');
        $this->db->join('adminti.telefonia_emprestimo AS temprestimo', 'temprestimo.cd_telefonia_aparelho = taparelho.cd_telefonia_aparelho', 'left');
        $this->db->join('adminti.telefonia_emprestimo_linha AS temprestimo_linha', 'temprestimo_linha.cd_telefonia_emprestimo = temprestimo.cd_telefonia_emprestimo', 'left');
        $this->db->join('adminti.telefonia_linha AS tlinha', 'tlinha.cd_telefonia_linha = temprestimo_linha.cd_telefonia_linha', 'left');
        $this->db->join('adminti.usuario', 'adminti.usuario.cd_usuario = temprestimo.cd_usuario', 'left');
        #$this->db->where('temprestimo.cd_telefonia_emprestimo', 6);
        return $this->db->get('adminti.telefonia_aparelho AS taparelho', $mostra_por_pagina, $pagina)->result();
        #echo '<pre>'; print_r($this->db->last_query()); exit();
    }
    
    /**
     * Aparelho_model::pesquisaQtd()
     * 
     * Consulta a quantidade de linhas da pesquisa
     * @param $ddd que se deseja encontrar
     * @param $numero número que se deseja encontrar
     * @param $operadora cd da operadora que se deseja encontrar
     * @param $plano cd da plano que se deseja encontrar
     * @param $status cd da status que se deseja encontrar
     * @param $pagina Página da paginação
     * @param $sort_by Campo que vai ser ordenado
     * @param $sort_order Tipo de ordeção do campo (Crescente ou decrescente)
     * @param $mostra_por_pagina Página corrente da paginação
     * 
     * @return Retorna a quantidade
     */
    public function pesquisaQtd($linha = null, $imei = null, $marca = null, $modelo = null, $tipo = null, $status = null){
        
        if($linha != '0'){ 
            #$this->db->where('taparelho.imei', $imei); 
            $condicao = "tlinha.numero LIKE '%".$linha."%'";
            $this->db->where($condicao);
        }
        
        if($imei != '0'){ 
            #$this->db->where('timei.imei', $imei); 
            #$condicao = "timei.imei LIKE '%".$imei."%'";
            $condicao = "taparelho.cd_telefonia_aparelho IN (SELECT cd_telefonia_aparelho FROM adminti.telefonia_imei WHERE imei LIKE '%".$imei."%')";
            $this->db->where($condicao);
        }
        
        if($marca != '0'){ 
            $this->db->where('tmarca.cd_telefonia_marca', $marca); 
            #$condicao = "nome LIKE '%";
            #$this->db->where($condicao);
        }
        
        if($modelo != '0'){ 
            $this->db->like('taparelho.modelo', $modelo); 
            #$condicao = "tlinha.numero = ".$numero;
            #$this->db->where($condicao);
        }
        
        if($tipo != '0'){ 
            $this->db->where('taparelho.tipo', $tipo);
            #$condicao = "taparelho.tipo = '".$tipo."'";
            #$this->db->where($condicao);
        }
        
        if($status != '0'){ 
            $this->db->where('taparelho.status', $status);
            #$condicao = "taparelho.status = '".$status."'";
            #$this->db->where($condicao);
        }
        
        $this->db->select('count(*) as total');
        /*
        $this->db->join('adminti.telefonia_marca AS tmarca', 'tmarca.cd_telefonia_marca = taparelho.cd_telefonia_marca');
        $this->db->join('adminti.telefonia_aparelho_linha AS tap_li', 'tap_li.cd_telefonia_aparelho = taparelho.cd_telefonia_aparelho', 'left');
        $this->db->join('adminti.telefonia_linha AS tlinha', 'tlinha.cd_telefonia_linha = tap_li.cd_telefonia_linha', 'left');
        $this->db->join('adminti.telefonia_imei AS timei', 'timei.cd_telefonia_aparelho = taparelho.cd_telefonia_aparelho', 'left');
        */
        $this->db->join('adminti.telefonia_marca AS tmarca', 'tmarca.cd_telefonia_marca = taparelho.cd_telefonia_marca');
        #$this->db->join('adminti.telefonia_imei AS timei', 'timei.cd_telefonia_aparelho = taparelho.cd_telefonia_aparelho', 'left');
        $this->db->join('adminti.telefonia_emprestimo AS temprestimo', 'temprestimo.cd_telefonia_aparelho = taparelho.cd_telefonia_aparelho', 'left');
        $this->db->join('adminti.telefonia_emprestimo_linha AS temprestimo_linha', 'temprestimo_linha.cd_telefonia_emprestimo = temprestimo.cd_telefonia_emprestimo', 'left');
        $this->db->join('adminti.telefonia_linha AS tlinha', 'tlinha.cd_telefonia_linha = temprestimo_linha.cd_telefonia_linha', 'left');
        $this->db->join('adminti.usuario', 'adminti.usuario.cd_usuario = temprestimo.cd_usuario', 'left');
        return $this->db->get('adminti.telefonia_aparelho AS taparelho', $mostra_por_pagina, $pagina)->result();
    }
    
    public function aparelhos($associados = '', $aparelho = null){
        
        if($associados == 'nao'){
            /*$condicao = "AND cd_telefonia_aparelho NOT IN(
                        	SELECT DISTINCT cd_telefonia_aparelho FROM adminti.telefonia_emprestimo WHERE cd_telefonia_aparelho IS NOT NULL
                        )";*/
            $condicao = "AND adminti.telefonia_aparelho.status = 'Estoque'";
        }elseif($associados == 'sim'){
            /*$condicao = "AND cd_telefonia_aparelho IN(
                        	SELECT DISTINCT cd_telefonia_aparelho FROM adminti.telefonia_emprestimo WHERE cd_telefonia_aparelho IS NOT NULL
                        )";*/
            $condicao .= " AND adminti.telefonia_aparelho.status = 'Ativo'";
        }elseif($associados == 'assoEmpNao'){ # Não esta associado ao emprestimo
            
            $condicao = ($aparelho)? "AND adminti.telefonia_aparelho.status != 'Avariado' AND cd_telefonia_aparelho = ".$aparelho: "";
            /*$condicao .= " OR cd_telefonia_aparelho NOT IN (
                        	SELECT DISTINCT cd_telefonia_aparelho FROM adminti.telefonia_emprestimo WHERE cd_telefonia_aparelho IS NOT NULL
                        )";*/
            if($condicao != ''){            
                $condicao .= " OR adminti.telefonia_aparelho.status = 'Estoque'";
            }else{
                $condicao .= " AND adminti.telefonia_aparelho.status = 'Estoque'";
            }
        }elseif($associados == 'assoEmpSim'){ # Esta associado ao emprestimo
            
            $condicao = ($aparelho)? "AND cd_telefonia_aparelho = ".$aparelho: "";
            /*$condicao .= " OR cd_telefonia_aparelho IN (
                        	SELECT DISTINCT cd_telefonia_aparelho FROM adminti.telefonia_emprestimo WHERE cd_telefonia_aparelho IS NOT NULL
                        )";*/
            if($condicao != ''){            
                $condicao .= " OR adminti.telefonia_aparelho.status = 'Ativo'";
            }else{
                $condicao .= " AND adminti.telefonia_aparelho.status = 'Ativo'";
            }
        }else{
            $condicao = '';
        }
        
        $sql = "SELECT 
                	cd_telefonia_aparelho,
                    adminti.telefonia_marca.nome AS marca,
                	tipo,
                	(
                        SELECT 
                            GROUP_CONCAT(timei.imei) 
                        FROM adminti.telefonia_imei AS timei 
                        WHERE timei.cd_telefonia_aparelho = adminti.telefonia_aparelho.cd_telefonia_aparelho
                    ) AS imei,
                	nota_fiscal, 
                	modelo
                FROM adminti.telefonia_aparelho 
                INNER JOIN adminti.telefonia_marca ON adminti.telefonia_marca.cd_telefonia_marca = adminti.telefonia_aparelho.cd_telefonia_marca
                WHERE 1=1
                ".$condicao;
        #if($this->session->userdata('cd') != 6){        
        return $this->db->query($sql)->result();
        /*}else{
            $this->db->query($sql)->result();
            echo '<pre>'; print_r($this->db->last_query()); exit();
        }*/
        
    }
    
    /**
     * Aparelho_model::delete()
     * 
     * Apaga o aparelho
     * 
     * @return Retorna o número de linhas afetadas
     */
    public function delete(){
        
        $sql = "DELETE FROM adminti.telefonia_aparelho WHERE cd_telefonia_aparelho = ".$this->input->post('apg_cd');
        $this->db->query($sql);
        return $this->db->affected_rows();
        
    }

}