<?php

/**
 * Relatorio_model
 * 
 * Classe que realiza consultas genéricas no banco
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2014
 * @access public
 */

class Relatorio_model extends CI_Model{
	
	/**
	 * Relatorio_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
        $this->load->library('Util', '', 'util');
	}
    
    /**
    * Relatorio_model::insere()
    * 
    * Função que realiza a inserção dos dados do relatório na base de dados
    * @return O número de linhas afetadas pela operação
    */
	public function insere(){
		
		$campo = array();
		$valor = array();
        
        $this->db->trans_begin();
        
        $nome = $this->util->removeAcentos($this->input->post('nome_relatorio'));
        $nome = ucfirst($this->util->formaValorBanco($nome));
        
        $sql = "INSERT INTO permissao (nome_permissao, pai_permissao, status_permissao) ";
        $sql .= "VALUES(".$nome.", 22, 'A');";
        
        $this->db->query($sql);
        $cd_permissao = $this->db->insert_id();
        
        if($this->input->post('cd_perfil')){
            
            foreach($this->input->post('cd_perfil') as $perfil){
                
                $sql = "INSERT INTO permissao_perfil (cd_permissao,cd_perfil) VALUES(".$cd_permissao.",".$perfil.")";
                $this->db->query($sql);
                
            }
            
        }
        
        $campo[] = 'criador_relatorio';
        $valor[] = $this->session->userdata('cd');
        $campo[] = 'cd_permissao';
        $valor[] = $cd_permissao;
		foreach($_POST as $c => $v){
			
            if($c <> 'cd_relatorio' and $c <> 'cd_parametro' and $c <> 'cd_permissao' and $c <> 'nome_relatorio_parametro' and $c <> 'cd_perfil'){
            
                if($c == 'query_relatorio'){
                    
                    $_POST['query_relatorio'] = addslashes($this->input->post('query_relatorio'));
                    $valorFormatado = "'".$this->input->post('query_relatorio')."'";
                    
                }else{
            
        			#$valorFormatado = $this->util->removeAcentos($this->input->post($c));
        			$valorFormatado = ucfirst($this->util->formaValorBanco($this->input->post($c)));
                    
    			}
                
    			$campo[] = $c;
    			$valor[] = $valorFormatado;
            
            }
            
		}
        
		$campos = implode(', ', $campo);
		$valores = implode(', ', $valor);
        
		$sql = "INSERT INTO relatorio (".$campos.")\n VALUES(".$valores.");";
        
        #echo '<pre>';
        #print_r($sql);
        #exit();
        
		$this->db->query($sql);
        $cd = $this->db->insert_id();
        
        if($cd){
        
            if($this->input->post('cd_parametro')){
        
                foreach($this->input->post('cd_parametro') as $par){
                    
                    $sql = "INSERT INTO relatorio_parametro (cd_parametro, nome_relatorio_parametro, cd_relatorio) VALUES (".$par.", '".addslashes(trim($_POST['nome_relatorio_parametro'][$par]))."', ".$cd.");";
                    $this->db->query($sql);
                    
                }
            
            }
        
        }
        
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
    * Relatorio_model::atualiza()
    * 
    * Função que realiza a atualização dos dados do relatório na base de dados
    * @return O número de linhas afetadas pela operação
    */
	public function atualiza(){

        $this->db->trans_begin();
        
        $nome = $this->util->removeAcentos(html_entity_decode($this->input->post('nome_relatorio')));
        $nome = ucfirst($this->util->formaValorBanco($nome));
        
        $sql = "UPDATE permissao SET nome_permissao =".$nome." WHERE cd_permissao = ".$this->input->post('cd_permissao');
        $this->db->query($sql);
        
        $sql = "DELETE FROM permissao_perfil WHERE cd_permissao = ".$this->input->post('cd_permissao');
        $this->db->query($sql);
        if($this->input->post('cd_perfil')){
            
            foreach($this->input->post('cd_perfil') as $perfil){
                
                $sql = "INSERT INTO permissao_perfil (cd_permissao,cd_perfil) VALUES(".$this->input->post('cd_permissao').",".$perfil.")";
                $this->db->query($sql);
                
            }
            
        }
        
        $campoValor[] = 'atualizador_relatorio = '.$this->session->userdata('cd');
        $campoValor[] = "data_atualizacao_relatorio = '".date('Y-m-d h:i:s')."'";
        
		foreach($_POST as $c => $v){
			
			if($c <> 'cd_relatorio' and $c <> 'cd_parametro' and $c <> 'nome_relatorio_parametro' and $c <> 'cd_perfil'){
				
                if($c == 'query_relatorio'){
                    
                    $_POST['query_relatorio'] = addslashes($this->input->post('query_relatorio'));
                    $valorFormatado = "'".$this->input->post('query_relatorio')."'";
                    
                }else{
            
        			$valorFormatado = $this->util->formaValorBanco($this->input->post($c));
                    
    			}
			
				#$campoValor[] = $c.' = '.ucfirst($this->util->removeAcentos(html_entity_decode($valorFormatado)));
                $campoValor[] = $c.' = '.ucfirst(html_entity_decode($valorFormatado));
			
			}

		}

		$camposValores = implode(', ', $campoValor);
        
		$sql = "UPDATE relatorio SET ".$camposValores." WHERE cd_relatorio = ".$this->input->post('cd_relatorio').";";

		$this->db->query($sql);
        
        
        $sql = "DELETE FROM relatorio_parametro WHERE cd_relatorio = ".$this->input->post('cd_relatorio');
        $this->db->query($sql);
        
        if($this->input->post('cd_parametro')){
        
            foreach($this->input->post('cd_parametro') as $par){
                    
                $sql = "INSERT INTO relatorio_parametro (cd_parametro, nome_relatorio_parametro, cd_relatorio) VALUES (".$par.", '".addslashes(trim($_POST['nome_relatorio_parametro'][$par]))."', ".$this->input->post('cd_relatorio').");";
                $this->db->query($sql);
                
            }
        
        }
        
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
        
		return $this->db->query($sql); # RETORNA O NÚMERO DE LINHAS AFETADAS
		
	}
    
    /**
    * Relatorio_model::dadosRelatorio()
    * 
    * Função que monta um array com todos os dados do relatório
    * @param $cd Cd do relatório para recuperação de dados
    * @return Retorna todos os dados do relatório
    */
	public function dadosRelatorio($cd){
	   
		$this->db->where('cd_relatorio', $cd);
		$usuario = $this->db->get('relatorio')->result_array(); # TRANSFORMA O RESULTADO EM ARRAY
		
		return $usuario[0];
	}
	
    /**
    * Relatorio_model::camposRelatorio()
    * 
    * Função que pega os nomes de todos os campos existentes na tabela relatório
    * @return Os campos da tabela relatório
    */
	public function camposRelatorio(){
		
		$campos = $this->db->get('relatorio')->list_fields();
		
		return $campos;
		
	}
    
    /**
    * Relatorio_model::parametrosRelatorio()
    * 
    * Função que pega os parâmetros do relatório
    * @return os parâmetros
    */
    public function relatorioParametro($cd){
        
        $this->db->where('cd_relatorio', $cd);
		return $this->db->get('relatorio_parametro')->result();
        
    }
    
    /**
     * Usuario_model::psqRelatorios()
     * 
     * lista os relatórios existentes de acordo com os parâmetros informados
     * @param $nome do relatório que se deseja encontrar
     * @param $pagina Página da paginação
     * @param $mostra_por_pagina Página corrente da paginação
     * 
     * @return A lista os relatórios
     */
    public function psqRelatorios($nome = null, $departamento = null, $status = null, $pagina = null, $mostra_por_pagina = null){
        
        $this->db->select("
                            cd_relatorio,
                            nome_relatorio,
                            cd_permissao,
                            CASE WHEN status_relatorio = 'A'
                                THEN 'Ativo'
                            ELSE 'Inativo' END AS status_relatorio,
                            nome_departamento
                            ");       
        
        
        if($nome != '0'){
            #$this->db->like('nome_perfil', $nome); 
            $condicao = "nome_relatorio LIKE '%".strtoupper($nome)."%'";
            $this->db->where($condicao);
        }
        
        if($status != '0'){
            #$this->db->like('nome_perfil', $nome); 
            $condicao = "status_relatorio = '".$status."'";
            $this->db->where($condicao);
        }
        
        if($departamento != '0'){
            #$this->db->like('nome_perfil', $nome); 
            $condicao = "relatorio.cd_departamento = ".$departamento;
            $this->db->where($condicao);
        }
        
        $this->db->join('departamento', 'departamento.cd_departamento = relatorio.cd_departamento');     
        $this->db->order_by("nome_relatorio", "asc"); 
        
        return $this->db->get('relatorio', $mostra_por_pagina, $pagina)->result();
    }
    
    /**
     * Relatorio_model::psqQtdRelatorios()
     * 
     * Consulta a quantidade de relatórios da pesquisa
     * 
     * @param $nome Nome do relatório para filtrar a consulta
     * 
     * @param $status Status do usuário para filtrar a consulta
     * 
     * @return Retorna a quantidade
     */
    public function psqQtdRelatorios($nome = null, $departamento = null, $status = null){
        
        if($nome != '0'){
            $condicao = "nome_relatorio LIKE '%".strtoupper($nome)."%'";
            $this->db->where($condicao);
        }
        
        if($status != '0'){
            #$this->db->like('nome_perfil', $nome); 
            $condicao = "status_relatorio = '".$status."'";
            $this->db->where($condicao);
        }
        
        if($departamento != '0'){
            #$this->db->like('nome_perfil', $nome); 
            $condicao = "relatorio.cd_departamento = ".$departamento;
            $this->db->where($condicao);
        }
        
        $this->db->select('count(*) as total');
        return $this->db->get('relatorio')->result();
    }
    
    /**
     * Relatorio_model::deleteRelatorio()
     * 
     * Apaga o relatório
     * 
     * @return Retorna o número de linhas afetadas
     */
    public function deleteRelatorio(){
        
        $this->db->trans_begin();
        
        #$sql = "DELETE FROM permissao_perfil WHERE cd_permissao = ".$this->input->post('apg_cd_permissao');
        #$this->db->query($sql);
        
        $sql = "DELETE FROM relatorio WHERE cd_relatorio = ".$this->input->post('apg_cd_relatorio');
        $this->db->query($sql);
        
        $sql = "DELETE FROM permissao WHERE cd_permissao = ".$this->input->post('apg_cd_permissao');
        $this->db->query($sql);
        
        
        #return $this->db->affected_rows();
        
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
     * Relatorio_model::departamentosRelatorios()
     * 
     * Pega os departamentos que possuem relatórios
     * 
     * @return
     */
    public function departamentosRelatorios(){
        
        $sql = 'SELECT 
                    DISTINCT
                    departamento.cd_departamento, 
                    nome_departamento 
                FROM adminti.departamento
                INNER JOIN sistema.relatorio ON departamento.cd_departamento = relatorio.cd_departamento
                WHERE cd_permissao IN ('.implode(',',$this->session->userdata('permissoes')).')
                ORDER BY nome_departamento';
        
        return $this->db->query($sql)->result();       
        
    }
    
    /**
     * Relatorio_model::relatoriosCategorias()
     * 
     * Pega os relatórios do departamento informado
     * 
     * @param mixed $cd_departamento Cd do departamento para filtrar a busca
     * @return
     */
    public function relatoriosCategorias($cd_departamento, $permissoes){
        
        $this->db->where('status_relatorio', 'A');
        $this->db->where('cd_departamento', $cd_departamento);
        $this->db->where('cd_permissao IN ('.implode(',',$permissoes).')');
        $this->db->order_by("nome_relatorio", "asc"); 
        
		return $this->db->get('relatorio')->result();
        
    }
    
    
    /**
     * Relatorio_model::parametrosDoRelatorio()
     * 
     * Pega os parâmetros do relatório
     * 
     * @param mixed $cd_relatorio Cd do relatório para filtrar a busca
     * @return
     */
    public function parametrosDoRelatorio($cd_relatorio){
        
        $sql = 'SELECT 
                    parametro.cd_parametro,
                    relatorio_parametro.cd_parametro,        
                    parametro.nome_parametro,
                    mascara_parametro,
                    campo_parametro,
                    variavel_parametro,
                    legenda_parametro,
                    tipo_parametro,
                    relatorio_parametro.nome_relatorio_parametro AS campo_personalizado                    
                FROM relatorio_parametro 
                INNER JOIN parametro ON parametro.cd_parametro = relatorio_parametro.cd_parametro
                WHERE cd_relatorio = '.$cd_relatorio;
                
        return $this->db->query($sql)->result();  
        
    }
    
    /**
     * Relatorio_model::dadosBancoRelatorio()
     * 
     * Pega os dados necessários para que seja possível rodar a query
     * 
     * @return
     */
    public function dadosBancoRelatorio(){
        
        $this->db->select('banco_relatorio, query_relatorio');
        $this->db->where('cd_relatorio', $this->input->post('cd_relatorio'));
        
		return $this->db->get('relatorio')->result();
        
    }
    
    public function rodaQuery($sql, $banco){
        
        switch($banco){
            
            case 'oracle': # Produção
                $conexao = $this->load->database('oracle', TRUE);
                
                #$conexao->query("ALTER SESSION SET NLS_DATE_FORMAT = 'DD-MM-YYYY HH24:MI:SS'");
                $conexao->query("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YYYY'"); # FORMATO DEFAULT DA SESSÃO DE EXECUÇÃO
                
                break;
            case 'siga_bcv': # Relatório
                $conexao = $this->load->database('siga_bcv', TRUE);
                
                #$conexao->query("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YYYY HH24:MI:SS'");
                $conexao->query("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YYYY'"); # FORMATO DEFAULT DA SESSÃO DE EXECUÇÃO
                
                break;   
            case 'ura': # Relatório
                $conexao = $this->load->database('ura', TRUE);
                $conexao->query("SET GLOBAL max_connect_errors=10000");
                
                break;   
            default: # Meu banco
                $conexao = $this->db;
            
            
        }
        
        return $conexao->query($sql)->result_array();
         
    }
    
    public function copiarRelatorio(){
        
        $parametros = $this->relatorioParametro($this->input->post('copy_cd_relatorio'));
        
        $dados = $this->dadosRelatorio($this->input->post('copy_cd_relatorio'));   
        
        $campo = array();
		$valor = array();
        
        $this->db->trans_begin();
        
        $sql = "INSERT INTO permissao (nome_permissao, pai_permissao, status_permissao) ";
        $sql .= "VALUES('".$dados['nome_relatorio']."_COPIA', 22, 'A');";
        
        $this->db->query($sql);
        $cd_permissao = $this->db->insert_id();
        
        $campo[] = 'criador_relatorio';
        $valor[] = $this->session->userdata('cd');
        $campo[] = 'cd_permissao';
        $valor[] = $cd_permissao;
		foreach($dados as $c => $v){
			
            if($c <> 'cd_relatorio' and $c <> 'criador_relatorio' and $c <> 'atualizador_relatorio' and $c <> 'data_criacao_relatorio' and $c <> 'data_atualizacao_relatorio' and $c <> 'cd_permissao'){
            
                if($c == 'nome_relatorio'){
                    $valorFormatado = ucfirst($this->util->formaValorBanco($v.'_COPIA'));
                }elseif($c == 'query_relatorio'){
                    $valorFormatado = $this->util->formaValorBanco(addslashes($v));
                }else{
                    $valorFormatado = ucfirst($this->util->formaValorBanco($v));
                }

    			$campo[] = $c;
    			$valor[] = $valorFormatado;
            
            }
            
		}
        
		$campos = implode(', ', $campo);
		$valores = implode(', ', $valor);
        
		$sql = "INSERT INTO relatorio (".$campos.")\n VALUES(".$valores.");";
        
		$this->db->query($sql);
        $cd = $this->db->insert_id();
        
        if($cd){
        
            if($parametros){

                foreach($parametros as $par){
                    
                    $sql = "INSERT INTO relatorio_parametro (cd_parametro, nome_relatorio_parametro, cd_relatorio) VALUES ('".$par->cd_parametro."', '".$par->nome_relatorio_parametro."', ".$cd.");";
                    $this->db->query($sql);
                    
                }
            
            }
        
        }
        
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
    
    public function perfilRelatorio($cd_relatorio){
        
        $sql = "SELECT 
                    DISTINCT cd_perfil 
                FROM relatorio
                INNER JOIN permissao_perfil ON relatorio.cd_permissao = permissao_perfil.cd_permissao
                WHERE cd_relatorio = ".$cd_relatorio;
                
        return $this->db->query($sql)->result();
        
    }
    
    public function registraAcessoRelatorio(){
        
        $sql = 'INSERT INTO relatorio_acesso (cd_relatorio, cd_usuario) VALUES('.$this->input->post('cd_relatorio').', '.$this->session->userdata('cd').')';
        return $this->db->query($sql);
        
    }

}