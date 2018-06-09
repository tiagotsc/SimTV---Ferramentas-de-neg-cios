<?php

/**
 * Dashboard_model
 * 
 * Classe que realiza o tratamento do módulo do crud
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2016
 * @access public
 */
class Crud_model extends CI_Model{
	
    private $tabela = false;
    private $campoId = false;
    private $textArea = array();
    private $camposIgnorados = array();
    private $chavePrimaria = false;
    private $listaCampos = false;
    private $listaCamposSelecionados = false;
    private $relacionamentos = array();
    private $conexao = false;
    
	/**
	 * Ura_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
        
        if(!$this->conexao){
            $this->conexao = $this->db;
                    
        }        
                
	}
    
    function getTipoCampos($tabelaDestino = false, $campoDestino = false)
    {
        if($tabelaDestino == false and $campoDestino == false){
            $dadosCampos = $this->conexao->query("SHOW COLUMNS FROM `".$this->tabela."`")->result();
        }else{     
            return $this->conexao->query("SHOW COLUMNS FROM `".$tabelaDestino."` WHERE Field = '".$campoDestino."'")->row();
        }
        
    	$db_field_types = array();

    	foreach($dadosCampos as $db_field_type)
    	{
    		$type = explode("(",$db_field_type->Type);
    		$db_type = $type[0];

    		if(isset($type[1]))
    		{
    			if(substr($type[1],-1) == ')')
    			{
    				$length = substr($type[1],0,-1);
    			}
    			else
    			{
    				list($length) = explode(" ",$type[1]);
    				$length = substr($length,0,-1);
    			}
    		}
    		else
    		{
    			$length = '';
    		}
    		$db_field_types[$db_field_type->Field]['db_max_length'] = $length;
    		$db_field_types[$db_field_type->Field]['db_type'] = $db_type;
    		$db_field_types[$db_field_type->Field]['db_null'] = $db_field_type->Null == 'YES' ? true : false;
    		$db_field_types[$db_field_type->Field]['db_extra'] = $db_field_type->Extra;
    	}
                
    	$results = $this->conexao->field_data($this->tabela); 
    	foreach($results as $num => $row)
    	{
    		$row = (array)$row;
    		$results[$num] = (object)( array_merge($row, $db_field_types[$row['name']])  );
    	}                                                             
                
    	return $results;
    }
    
    public function setConexao($conexao){
        
        $this->conexao = $this->load->database($conexao, TRUE);
        
    }
    
    /**
     * Seta a tabela que será usada
     **/
    public function setTabela($tabela){
        
        $this->tabela = $tabela;
        
    }
    
    public function setCampoId($campo){
        $this->campoId = $campo;
    }
    
    public function setTextArea($campos){
        $this->textArea = $campos;
    }
    
    public function setCamposIgnorados($campos){
        $this->camposIgnorados = $campos;
    }
    
    public function setRelacao($campoOrigem, $tabela, $campoDestino){
        
        $dadosTabelaDestino = $this->getTipoCampos($tabela, $campoDestino);
        $tipo = explode('(', $dadosTabelaDestino->Type);
                                                                                #Tipo do campo destino
        $this->relacionamentos[] = array($campoOrigem, $tabela, $campoDestino, $tipo[0]);
        
    }
    
    public function setListaCamposSelecionados($array = false)
    {
        
        if($array){
            $this->listaCamposSelecionados = $array;
        }
        
    }
    
    public function configCampos()
    {
        
        $tipos = $this->getTipoCampos();
            
            $campos = array();
            
            foreach($tipos as $t){
                
                if($t->primary_key == 1 and $t->db_extra == 'auto_increment'){
                    $this->chavePrimaria = $t->name;
                    $campos[$this->tabela.'.'.$t->name] = 'pk';
                }else{
                    $campos[$this->tabela.'.'.$t->name] = $t->type;
                }
                
            }
            
        if($this->listaCampos == false){
            $this->listaCampos = $campos;
        }
        
        # Remove a chave primária da lista
        #unset($this->listaCampos[$this->chavePrimaria]);
        
    }
    
    public function listar($parametros = null, $mostra_por_pagina = null, $sort_by = 1, $sort_order = null, $pagina = null){
        $this->configCampos();
        
        if($this->relacionamentos){
            foreach($this->relacionamentos as $dados){
                
                unset($this->listaCampos[$this->tabela.'.'.$dados[0]]);
                $this->listaCampos[$dados[1].'.'.$dados[2].' AS '.$dados[1].ucfirst($dados[2])] = $dados[3];
                $this->conexao->join($dados[1], $this->tabela.'.'.$dados[0].' = '.$dados[1].'.'.$dados[0], 'left');
                
            }
        }
        
        if($this->listaCamposSelecionados){
                
                $selecionados[array_search('pk', $this->listaCampos)] = 'pk';
                
            foreach($this->listaCamposSelecionados as $campo => $alias){
                
                if(is_numeric($campo)){
                    if($this->listaCampos[$alias]){
                        $tipo = $this->listaCampos[$alias];
                    }else{
                        $c = explode('.', $alias);
                        $tipo = $this->listaCampos[$alias.' AS '.$c[0].ucfirst($c[1])];
                    }
                    
                    $selecionados[$alias] = $tipo;
                    
                }else{
                    if($this->listaCampos[$campo]){
                        $tipo = $this->listaCampos[$campo];
                    }else{
                        $c = explode('.', $campo);
                        $tipo = $this->listaCampos[$campo.' AS '.$c[0].ucfirst($c[1])];
                    }
                    $selecionados[$campo.' AS '.$alias] = $tipo;
                }

            }
            
            $this->listaCampos = $selecionados;
            
        }
        
        $this->conexao->select(implode(',', array_keys($this->listaCampos)));

        if($sort_by != '1'){
            $this->conexao->order_by($sort_by, $sort_order);
        }
        
        if($_POST){
            array_pop($_POST);
            foreach($this->input->post() as $campo => $valor){ 
                if($valor != ''){
                    $this->conexao->like($campo, $valor);
                }
            }
        }
        
        if($parametros){
            $post = explode('|', $parametros);
            
            foreach($post as $campoValor){
                $res = explode('=', $campoValor);
                
                if($res[1] != ''){
                    $this->conexao->like($res[0], $res[1]);
                }
            }
        }
        
        $dados['tabela'] = $this->tabela;
        $dados['dados'] = $this->conexao->get($this->tabela, $mostra_por_pagina, $pagina)->result();            
        $dados['qtd'] = $this->qtdLinhas($parametros);
        $dados['campos'] = $this->listaCampos;

        return $dados;
        
    }
    
    public function qtdLinhas($parametros = null){
        
        if($_POST){

            foreach($this->input->post() as $campo => $valor){ 
                if($valor != ''){
                    $this->conexao->like($campo, $valor);
                }
            }
        }  
        
        if($parametros){
            $post = explode('|', $parametros);
            
            foreach($post as $campoValor){
                $res = explode('=', $campoValor);
                
                if($res[1] != ''){
                    $this->conexao->like($res[0], $res[1]);
                }
            }
        }
        
        return $this->conexao->get($this->tabela)->num_rows(); 
        
    }
    
    /**
    * Aparelho_model::dados()
    * 
    * Função que monta um array com todos os dados do aparelho
    * @param $cd Cd do aparelho para recuperação de dados
    * @return Retorna todos os dados do aparelho
    */
	public function dadosId($id){
        
        $this->db->where($this->campoId, $id);
		$dados = $this->db->get($this->tabela)->result_array(); # TRANSFORMA O RESULTADO EM ARRAY
		
		return $dados[0];
	}
	
    /**
    * Aparelho_model::campos()
    * 
    * Função que pega os nomes de todos os campos existentes na tabela
    * @return Os campos da tabela linha
    */
	public function camposTabela(){
		
		$campos = $this->db->get($this->tabela)->list_fields();
		
		return $campos;
		
	}
    
    /**
    * Linha_model::insere()
    * 
    * Função que realiza a inserção dos dados do linha na base de dados
    * @return O número de linhas afetadas pela operação
    */
	public function insereMysql(){
		
		$campo = array();
		$valor = array();
        
        #$campo[] = 'criador_usuario';
        #$valor[] = $this->session->userdata('cd');
		foreach($_POST as $c => $v){
			
            if($c != $this->campoId){
                
                if(!in_array($c, $this->textArea)){
                    if(!in_array($c, $this->camposIgnorados)){
            			$valorFormatado = $this->util->removeAcentos(addslashes($this->input->post($c)));
            			$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
            			
            			$campo[] = $c;
            			$valor[] = $valorFormatado;
                    }
                }else{
                    $campo[] = $c;
        			$valor[] = $this->util->formaValorBanco(addslashes(trim($this->input->post($c))));
                }
            
            }
            
		}
        
        # A senha inícial fica definida com o CPF
        #$campo[] = 'senha_usuario';
		#$valor[] = $this->util->formaValorBanco(md5(str_replace('-', '', str_replace('.', '',$this->input->post('cpf_funcionario')))));
		
		$campos = implode(', ', $campo);
		$valores = implode(', ', $valor);
		
        $this->db->trans_begin();
        
		$sql = "INSERT INTO ".$this->tabela." (".$campos.")\n VALUES(".$valores.");";
        #echo '<pre>'; print_r($sql); exit();
        
        #if($this->session->userdata('cd') == 6){
            #echo '<pre>'; print_r($sql); exit();
        #}
        
		$this->db->query($sql);
        $cd = $this->db->insert_id();
        
        $_POST['id'] = $cd;
        
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
    * Linha_model::atualiza()
    * 
    * Função que realiza a atualização dos dados do linha na base de dados
    * @return O número de linhas afetadas pela operação
    */
	public function atualiza(){
        
		foreach($_POST as $c => $v){
			
			if($c != $this->campoId){
			 
                if(!in_array($c, $this->textArea)){
                    if(!in_array($c, $this->camposIgnorados)){
        				$valorFormatado = $this->util->removeAcentos(addslashes($this->input->post($c)));
        				$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
        			
        				$campoValor[] = $c.' = '.$valorFormatado;
                    }
                }else{
                    $campoValor[] = $c.' = '.$this->util->formaValorBanco(addslashes(trim($this->input->post($c))));
                }
                
			
			}
		}
		
		$camposValores = implode(', ', $campoValor);
		
        $this->db->trans_begin();
        
		$sql = "UPDATE ".$this->tabela." SET ".$camposValores." WHERE ".$this->campoId." = ".$this->input->post($this->campoId).";";
        
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
     * Crud_model::dadosIDSimples()
     * 
     * Formato simplificado de pegar os dados de um ID
     * @param $tabela Tabela que deseja consultar
     * @param $campoId Campo ID da tabela
     * @param $id ID que deseja pegar os dados
     * 
     * @return Retorna os dados da ID
     */
    public function dadosIDSimples($tabela = false, $campoId = false, $id = false){
        
        $this->setTabela($tabela);
        $this->setCampoId($campoId);
        if($tabela and $campoId and $id){
            return $this->dadosId($id);
        }else{
            return false;
        }
    }
    
    /**
     * Crud_model::camposTbSimples()
     * 
     * Formato simplificado de pegar os campos da tabela
     * @param $tabela Tabela que deseja consultar
     * 
     * @return Retorna os dados da ID
     */
    public function camposTbSimples($tabela){
        
        $this->setTabela($tabela);
        
        if($tabela){
            return $this->camposTabela();
        }else{
            return false;
        }
        
    }
    
    /**
     * Node_model::delete()
     * 
     * Apaga a operadora
     * 
     * @return Retorna o número de linhas afetadas
     */
    public function delete(){
        
        $sql = "DELETE FROM ".$this->tabela." WHERE ".$this->campoId." = ".$this->input->post('apg_id');
        $this->db->query($sql);
        return $this->db->affected_rows();
        
    }

}