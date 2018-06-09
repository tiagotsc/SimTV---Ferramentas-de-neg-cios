<?php
/**
* Classe que realiza todas as interações com a entidade agenda
*/
class PermissaoPerfil_model extends CI_Model{
	
	/**
	 * PermissaoPerfil_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
		$this->load->library('Util', '', 'util');
	}
    
    /**
     * PermissaoPerfil_model::inserePerfil()
     * 
     * Cadastra o perfil
     * 
     * @return Retorna o CD caso verdadeiro ou false
     */
    public function inserePerfil(){
        
        $valorFormatado = $this->util->removeAcentos($this->input->post('nome_perfil'));
        $valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
        
        $this->db->trans_begin();
        
        $sql = "INSERT INTO perfil(nome_perfil, status_perfil, criador_perfil) VALUES(".$valorFormatado.", '".$this->input->post('status_perfil')."',".$this->session->userdata('cd').");";
        $this->db->query($sql);
        $cd = $this->db->insert_id();
        
        if($cd){
        
            foreach($this->input->post('permissao') as $perm){
                
                $sql = "INSERT INTO permissao_perfil (cd_permissao, cd_perfil) VALUES (".$perm.", ".$cd.");";
                $this->db->query($sql);
                
            }
        
        }
            
         if($this->db->trans_status() === TRUE){
            $this->db->trans_commit();
            return $cd;
         }else{
            $this->db->trans_rollback();
            return false;
         } 
        
    }
    
    /**
     * PermissaoPerfil_model::atualizaPerfil()
     * 
     * Atualiza o perfil
     * 
     * @param mixed $cd Cd do perfil para atualização
     * @return Retorna o Cd caso verdadeiro ou false
     */
    public function atualizaPerfil($cd){
        
        $valorFormatado = $this->util->removeAcentos($this->input->post('nome_perfil'));
        $valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
        
        $this->db->trans_begin();
        
        $sql = "UPDATE perfil SET nome_perfil = ".$valorFormatado.", status_perfil = '".$this->input->post('status_perfil')."', atualizador_perfil = ".$this->session->userdata('cd').", data_atualizacao_perfil = '".date('Y-m-d h:i:s')."' WHERE cd_perfil = ".$cd;
        
        if($this->db->query($sql)){
            
            $sql = "DELETE FROM permissao_perfil WHERE cd_perfil = ".$cd;
            $this->db->query($sql);
            
            foreach($this->input->post('permissao') as $perm){
                
                $sql = "INSERT INTO permissao_perfil (cd_permissao, cd_perfil) VALUES (".$perm.", ".$cd.");";
                $this->db->query($sql);
                
            }
            
        }
        
        if($this->db->trans_status() === TRUE){
            $this->db->trans_commit();
            return $cd;
         }else{
            $this->db->trans_rollback();
            return false;
         } 
        
    }
    
	/**
	 * PermissaoPerfil_model::perfil()
	 * 
     * Pega os perfis ativos
     * 
	 * @return Retorna os ativos
	 */
	public function perfil(){
	   
        $this->db->where("status_perfil =  'A'");
        $this->db->order_by("nome_perfil", "asc"); 
		return $this->db->get('perfil')->result();
	}
    
    /**
     * PermissaoPerfil_model::permissoesDoPerfil()
     * 
     * Pega as permissões do perfil
     * 
     * @param mixed $cd
     * @return Retorna as permissões encontradas
     */
    public function permissoesDoPerfil($cd){
        
        $this->db->select('cd_permissao');
        $this->db->where("cd_perfil", $cd);
		return $this->db->get('permissao_perfil')->result_array();
        
    }
    
    /**
    * PermissaoPerfil_model::dadosPerfil()
    * 
    * Função que monta um array com todos os dados do perfil
    * @param $cd do perfil para recuperação dos dados
    * @return Retorna todos os dados do perfil
    */
	public function dadosPerfil($cd){
	
		$this->db->where('cd_perfil', $cd);
		$funcionario = $this->db->get('perfil')->result_array(); # TRANSFORMA O RESULTADO EM ARRAY
		
		return $funcionario[0];
	}
	
    /**
    * PermissaoPerfil_model::camposPerfil()
    * 
    * Função que pega os nomes de todos os campos existentes na tabela perfil
    * @return O número de linhas afetadas pela operação
    */
	public function camposPerfil(){
		
		$campos = $this->db->get('perfil')->list_fields();
		
		return $campos;
		
	}
    
    /**
     * Permissaoperfil_model::psqPerfis()
     * 
     * lista os perfis existentes de acordo com os parâmetros informados
     * @param $nome do perfil que se deseja encontrar
     * @param $status Status do perfil
     * @param $pagina Página da paginação
     * @param $mostra_por_pagina Página corrente da paginação
     * 
     * @return A lista perfis encontrados
     */
    public function psqPerfis($nome = null, $status = null, $pagina = null, $sort_by = null, $sort_order = null, $mostra_por_pagina = null){
        
        // Verifica qual ordenação foi informada
        $sort_order = ($sort_order == 'desc') ? 'desc' : 'asc';
        // Campos da tabela que podem receber ordenação
		$sort_columns = array('cd_perfil', 'nome_perfil', 'status_pefil');
        // Verifica qual campo foi informado para ordenação
        $sort_by = (in_array($sort_by, $sort_columns)) ? $sort_by : 'nome_perfil';
        
        $this->db->select("
                            cd_perfil,
                            nome_perfil,
                            CASE WHEN status_perfil = 'A'
                                THEN 'Ativo'
                            ELSE 'Inativo' END AS status_perfil
                            ");       
        
        
        if($nome != '0'){
            #$this->db->like('nome_perfil', $nome); 
            $condicao = "nome_perfil LIKE '%".strtoupper($nome)."%'";
            $this->db->where($condicao);
        }
        
        if($status != '0'){
            $this->db->where('status_perfil', $status);
        }
        
        #$this->db->join('banco', 'banco.cd_banco = arquivo_retorno.cd_banco');        
        $this->db->order_by($sort_by, $sort_order); 
        return $this->db->get('perfil', $mostra_por_pagina, $pagina)->result();
    }
    
    /**
     * Permissaoperfil_model::psqQtdPerfis()
     * 
     * Consulta a quantidade de perfis da pesquisa
     * 
     * @param $nome Nome do perfil para filtrar a consulta
     * @param $status Status do perfil para filtrar a consulta
     * 
     * @return Retorna a quantidade
     */
    public function psqQtdPerfis($nome = null, $status = null){
        
        if($nome != '0'){
            $condicao = "nome_perfil LIKE '%".strtoupper($nome)."%'";
            $this->db->where($condicao);
        }
        
        if($status <> '0'){
            $this->db->where('status_perfil', $status);
        }
        
        $this->db->select('count(*) as total');
        return $this->db->get('perfil')->result();
    }
    
    public function copyPerfil(){
        
        $perfil = $this->dadosPerfil($this->input->post('copy_cd_perfil'));
        $perfilPermissoes = $this->permissoesDoPerfil($this->input->post('copy_cd_perfil'));

        $this->db->trans_begin();
        
        $sql = "INSERT INTO perfil(nome_perfil, status_perfil, criador_perfil) VALUES('".$perfil['nome_perfil']."_COPIA', 'A',".$this->session->userdata('cd').");";
        $this->db->query($sql);
        $cd = $this->db->insert_id();
        
        if($cd){
        
            foreach($perfilPermissoes as $perm){
                
                $sql = "INSERT INTO permissao_perfil (cd_permissao, cd_perfil) VALUES (".$perm['cd_permissao'].", ".$cd.");";
                $this->db->query($sql);
                
            }
        
        }
            
        if($this->db->trans_status() === TRUE){
            $this->db->trans_commit();
            return $cd;
        }else{
            $this->db->trans_rollback();
            return false;
        }
        
    }
    
    /**
     * PermissaoPerfil_model::deletePerfil()
     * 
     * Apaga o perfil
     * 
     * @return O número de linhas afetadas
     */
    public function deletePerfil(){
        
        $sql = "DELETE FROM perfil WHERE cd_perfil = ".$this->input->post('apg_cd_perfil');
        $this->db->query($sql);
        return $this->db->affected_rows();
        
    }

}