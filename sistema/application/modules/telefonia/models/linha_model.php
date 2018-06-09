<?php

/**
 * Dashboard_model
 * 
 * Classe que realiza o tratamento do módulo do linha
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2015
 * @access public
 */
class Linha_model extends CI_Model{
	
    private $inputsIgnorados = array('cd_telefonia_linha', 'cd_servico', 'qtd', 'valor', 'cd_servico','qtd_servico', 'valor_servico', 'inicio_servico','fim_servico');
    
	/**
	 * Linha_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
        $this->load->library('Util', '', 'util');
        $this->load->model('telefonia/servico_model','servico');
	}
    
    /**
    * Linha_model::insere()
    * 
    * Função que realiza a inserção dos dados do linha na base de dados
    * @return O número de linhas afetadas pela operação
    */
	public function insere(){
		
		$campo = array();
		$valor = array();
        
        #$campo[] = 'criador_usuario';
        #$valor[] = $this->session->userdata('cd');
		foreach($_POST as $c => $v){
			
            if(!in_array($c, $this->inputsIgnorados)){
            
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
        
		$sql = "INSERT INTO adminti.telefonia_linha (".$campos.")\n VALUES(".$valores.");";
        
		$this->db->query($sql);
        $cd = $this->db->insert_id();
        
        $_POST['cd_telefonia_linha'] = $cd;
        $this->associaServicos();
        
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
        
        #$campoValor[] = 'atualizador_usuario = '.$this->session->userdata('cd');
        #$campoValor[] = "data_atualizacao_usuario = '".date('Y-m-d h:i:s')."'";
        
		foreach($_POST as $c => $v){
			
			if(!in_array($c, $this->inputsIgnorados)){
				$valorFormatado = $this->util->removeAcentos($this->input->post($c));
				$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
			
				$campoValor[] = $c.' = '.$valorFormatado;
			
			}
		}
		
		$camposValores = implode(', ', $campoValor);
		
        $this->db->trans_begin();
        
		$sql = "UPDATE adminti.telefonia_linha SET ".$camposValores." WHERE cd_telefonia_linha = ".$this->input->post('cd_telefonia_linha').";";
		$this->db->query($sql);
        
        $this->associaServicos();
        
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
    * Linha_model::dados()
    * 
    * Função que monta um array com todos os dados do linha
    * @param $cd Cd do linha para recuperação de dados
    * @return Retorna todos os dados do linha
    */
	public function dados($cd){
        
        $this->db->where('cd_telefonia_linha', $cd);
		$dados = $this->db->get('adminti.telefonia_linha')->result_array(); # TRANSFORMA O RESULTADO EM ARRAY
		
		return $dados[0];
	}
	
    /**
    * Linha_model::campos()
    * 
    * Função que pega os nomes de todos os campos existentes na tabela linha
    * @return Os campos da tabela linha
    */
	public function campos(){
		
		$campos = $this->db->get('adminti.telefonia_operadora')->list_fields();
		
		return $campos;
		
	}
    
    /**
     * Linha_model::pesquisa()
     * 
     * lista os linhas existentes de acordo com os parâmetros informados
     * @param $ddd que se deseja encontrar
     * @param $identificacao identificação do chip
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
    public function pesquisa($ddd = null, $identificacao = null, $numero = null, $operadora = null, $plano = null, $status = null, $pagina = null, $mostra_por_pagina = null, $sort_by = null, $sort_order = null){
        
        // Verifica qual ordenação foi informada
        $sort_order = ($sort_order == 'desc') ? 'desc' : 'asc';
        // Campos da tabela que podem receber ordenação
		#$sort_columns = array('tlinha.cd_telefonia_linha','tddd.cd_telefonia_ddd','tlinha.numero','toperadora.nome','tplano.nome','tlinha.status');
        $sort_columns = array('tddd.ddd','tlinha.numero','toperadora.nome','tplano.nome','tlinha.status');
        // Verifica qual campo foi informado para ordenação
        $sort_by = (in_array($sort_by, $sort_columns)) ? $sort_by : 'tddd.ddd';
                        
        $this->db->select("
                            tlinha.cd_telefonia_linha,
                            ddd,
                            tlinha.numero AS linha,
                            toperadora.nome AS operadora,
                            tddd.ddd,
                            tplano.nome AS plano,
                            (
                                SELECT 
                                    CASE
                                        WHEN matricula_usuario IS NULL
                                            THEN CONCAT('S/M - ', nome_usuario)
                                    ELSE CONCAT(matricula_usuario,' - ',nome_usuario) END AS usuario
                                FROM adminti.usuario AS user
                                LEFT JOIN adminti.telefonia_emprestimo AS emp ON user.cd_usuario = emp.cd_usuario
                                INNER JOIN adminti.telefonia_emprestimo_linha AS empL ON empL.cd_telefonia_emprestimo = emp.cd_telefonia_emprestimo
                                WHERE empL.cd_telefonia_linha = tlinha.cd_telefonia_linha
                            ) AS usuario,
                            CASE 
                                WHEN tlinha.status = 'A'
                                    THEN 'Ativo'
                                WHEN tlinha.status = 'E'
                                    THEN 'Estoque'
                            ELSE 'Inativo' END AS status
                            ");       
        
        
        if($ddd != '0'){
            $this->db->where('tddd.cd_telefonia_ddd', $ddd); 
            #$condicao = "nome LIKE '%";
            #$this->db->where($condicao);
        }
        
        if($identificacao != '0'){
            $this->db->like('tlinha.identificacao', $identificacao);
            #$condicao = "tlinha.numero = ".$numero;
            #$this->db->where($condicao);
        }
        
        if($numero != '0'){
            $this->db->like('tlinha.numero', $numero);
            #$condicao = "tlinha.numero = ".$numero;
            #$this->db->where($condicao);
        }
        
        if($operadora != '0'){
            #$this->db->like('nome_perfil', $nome); 
            $condicao = "tlinha.cd_telefonia_operadora = ".$operadora;
            $this->db->where($condicao);
        }
        
        if($plano != '0'){
            #$this->db->like('nome_perfil', $nome); 
            $condicao = "tlinha.cd_telefonia_plano = ".$plano;
            $this->db->where($condicao);
        }
        
        if($status != '0'){
            #$this->db->like('nome_perfil', $nome); 
            $condicao = "tlinha.status = '".$status."'";
            $this->db->where($condicao);
        }
       
        $this->db->order_by($sort_by, $sort_order);  
        $this->db->join('adminti.telefonia_operadora AS toperadora', 'toperadora.cd_telefonia_operadora = tlinha.cd_telefonia_operadora'); 
        $this->db->join('adminti.telefonia_ddd AS tddd', 'tddd.cd_telefonia_ddd = tlinha.cd_telefonia_ddd');
        $this->db->join('adminti.telefonia_plano AS tplano', 'tplano.cd_telefonia_plano = tlinha.cd_telefonia_plano', 'left');
        return $this->db->get('adminti.telefonia_linha AS tlinha', $mostra_por_pagina, $pagina)->result();
        #echo '<pre>';
        #print_r($this->db->last_query()); exit();
    }
    
    /**
     * Linha_model::pesquisaQtd()
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
    public function pesquisaQtd($ddd = null, $identificacao = null, $numero, $operadora = null, $plano = null, $status = null){
        
        if($ddd != '0'){
            #$this->db->like('tddd.ddd', $ddd); 
            $condicao = "tddd.cd_telefonia_ddd = ".$ddd;
            $this->db->where($condicao);
        }
        
        if($identificacao != '0'){
            $this->db->like('tlinha.identificacao', $identificacao);
            #$condicao = "tlinha.numero = ".$numero;
            #$this->db->where($condicao);
        }
        
        if($numero != '0'){
            $this->db->like('tlinha.numero', $numero); 
            #$condicao = "tlinha.numero = ".$numero;
            #$this->db->where($condicao);
        }
        
        if($operadora != '0'){
            #$this->db->like('nome_perfil', $nome); 
            $condicao = "tlinha.cd_telefonia_operadora = ".$operadora;
            $this->db->where($condicao);
        }
        
        if($plano != '0'){
            #$this->db->like('nome_perfil', $nome); 
            $condicao = "tlinha.cd_telefonia_plano = ".$plano;
            $this->db->where($condicao);
        }
        
        if($status != '0'){
            #$this->db->like('nome_perfil', $nome); 
            $condicao = "tlinha.status = '".$status."'";
            $this->db->where($condicao);
        }
        
        $this->db->select('count(*) as total');
        $this->db->join('adminti.telefonia_operadora AS toperadora', 'toperadora.cd_telefonia_operadora = tlinha.cd_telefonia_operadora'); 
        $this->db->join('adminti.telefonia_ddd AS tddd', 'tddd.cd_telefonia_ddd = tlinha.cd_telefonia_ddd');
        return $this->db->get('adminti.telefonia_linha AS tlinha')->result();
    }
    
    /**
     * Linha_model::dddsAssociadosLinhas()
     * 
     * Pegas os DDDs que estão associados a alguma linha
     * 
     * @return Retorna as linhas encontradas
     */ 
    public function dddsAssociadosLinhas(){
        
        $sql = "SELECT
                *
                FROM adminti.telefonia_ddd
                WHERE 
                cd_telefonia_ddd IN (SELECT DISTINCT cd_telefonia_ddd FROM adminti.telefonia_linha/* WHERE status = 'A'*/)
                /*AND status = 'A'*/";
        return $this->db->query($sql)->result();
        
    }
    
    /**
     * Linha_model::linhasAssociadasDDD()
     * 
     * Pegas as linhas associadas ao DDD informado
     * 
     * @return Retorna as linhas encontradas
     */    
    public function linhasAssociadasDDD(){
        
        $condicao = ($this->input->post('linha'))? " AND tlinha.numero LIKE '".$this->input->post('linha')."%' ": '';
        
        $sql = "SELECT
                	tlinha.cd_telefonia_linha,
                	tddd.ddd,
                	tlinha.numero
       	        FROM adminti.telefonia_linha AS tlinha
                INNER JOIN adminti.telefonia_ddd AS tddd ON tddd.cd_telefonia_ddd = tlinha.cd_telefonia_ddd
                WHERE 
                tlinha.cd_telefonia_ddd = ".$this->input->post('cd_ddd')."
                ".$condicao."
                AND tlinha.cd_telefonia_linha NOT IN
                (SELECT cd_telefonia_linha FROM adminti.telefonia_emprestimo_linha)
                /*AND tlinha.status = 'A'*/";
                
        return $this->db->query($sql)->result();
        
    }
    
    /**
     * Linha_model::linhasAssociadasEmprestimo()
     * 
     * Pegas as linhas associadas ao aparelho informado
     * 
     * @return Retorna as linhas encontradas
     */  
    public function linhasAssociadasEmprestimo($cd){
        
        $sql = "SELECT 
                	tlinha.cd_telefonia_linha,
                	tddd.ddd,
                	tlinha.numero 
                FROM adminti.telefonia_emprestimo_linha AS temp_lin
                INNER JOIN adminti.telefonia_linha AS tlinha ON tlinha.cd_telefonia_linha = temp_lin.cd_telefonia_linha
                INNER JOIN adminti.telefonia_ddd AS tddd ON tddd.cd_telefonia_ddd = tlinha.cd_telefonia_ddd
                WHERE cd_telefonia_emprestimo = ".$cd;
                
        return $this->db->query($sql)->result();
        
    }
    
    /**
     * Linha_model::gravaNovoServico()
     * 
     * Grava novo serviço e adiciona na linha
     * 
     * @return Bool
     */
    /*public function gravaNovoServico(){
        
        $this->db->trans_begin();
        
        foreach($this->input->post('nome_servico') as $campo => $valor){
            
            if((trim($valor) != '' and trim($this->input->post('qtd_servico')[$campo]) != '') or trim($this->input->post('valor_servico')[$campo])){
                
                $nome = strtoupper($this->util->formaValorBanco($this->util->removeAcentos($valor)));
                $qtd = $this->input->post('qtd_servico')[$campo];
                $valor = $this->util->formaValorBanco($this->input->post('valor_servico')[$campo]);
                
                $sql = "INSERT INTO adminti.telefonia_servico(nome) VALUES(".$nome.");";
                
                $this->db->query($sql);
                $cd = $this->db->insert_id();
                
                if($cd){
                    
                    if($this->input->post('cd_linha')){
                  
                        $sql = "INSERT INTO adminti.telefonia_linha_servico (cd_telefonia_servico, cd_telefonia_linha, qtd, valor)";
                        $sql.= "\n VALUES(".$cd.", ".$this->input->post('cd_linha').",".$qtd.", ".$valor.");";
                        
                        $this->db->query($sql);
                    
                    }
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
            
            return true;
        }
    }*/
    public function gravaNovoServico(){
        
        $this->db->trans_begin();
        
        foreach($this->input->post('nome_servico') as $campo => $valor){
            
            if((trim($valor) != '' and trim($this->input->post('qtd_servico')[$campo]) != '') or trim($this->input->post('valor_servico')[$campo])){
                
                $nome = strtoupper($this->util->formaValorBanco($this->util->removeAcentos($valor)));
                $qtd = $this->input->post('qtd_servico')[$campo];
                $valor = $this->util->formaValorBanco($this->input->post('valor_servico')[$campo]);
                $inicio = $this->util->formaValorBanco($this->input->post('inicio_servico')[$campo]);
                $fim = $this->util->formaValorBanco($this->input->post('fim_servico')[$campo]);
                
                $sql = "INSERT INTO adminti.telefonia_servico(nome, qtd, valor, data_inicio, data_fim) VALUES(".$nome.", ".$qtd.", ".$valor.", ".$inicio.", ".$fim.");";
                
                $this->db->query($sql);
                $cd = $this->db->insert_id();
                
                if($cd){
                    
                    if($this->input->post('cd_linha')){
                  
                        $sql = "INSERT INTO adminti.telefonia_linha_servico (cd_telefonia_servico, cd_telefonia_linha)";
                        $sql.= "\n VALUES(".$cd.", ".$this->input->post('cd_linha').");";
                        
                        $this->db->query($sql);
                    
                    }
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
            
            return true;
        }
    }
    
    
    /**
     * Linha_model::associaServicos()
     * 
     * Associa o serviço a linha
     * 
     * @return Bool
     */
    public function associaServicos(){
        
        $sql = "DELETE FROM adminti.telefonia_linha_servico WHERE cd_telefonia_linha = ".$this->input->post('cd_telefonia_linha');
        $this->db->query($sql);
        
        if($this->input->post('cd_servico')){
        
            foreach($this->input->post('cd_servico') as $campo => $cd_servico){
            
                if($cd_servico != '' and trim($this->input->post('inicio_servico')[$campo]) != '' and trim($this->input->post('fim_servico')[$campo])){
                    
                    $inicio = $this->util->formaValorBanco($this->input->post('inicio_servico')[$campo]);
                    $fim = $this->util->formaValorBanco($this->input->post('fim_servico')[$campo]);
                    
                    $sql = "INSERT INTO adminti.telefonia_linha_servico (cd_telefonia_servico, cd_telefonia_linha, data_inicio, data_fim) "; 
                    $sql .= "VALUES(".$cd_servico.", ".$this->input->post('cd_telefonia_linha').", ".$inicio.", ".$fim.");";
                    
                    $this->db->query($sql);
                    
                }
                
            }
        
        }
        
    }
    
    /**
     * Linha_model::servicosLinha()
     * 
     * Pega os serviços da linha
     * 
     * @return Bool
     */
    public function servicosLinha($cd, $tipo = 'ATIVO'){
        
        if($tipo == 'ATIVO'){
            $this->db->where('linhaServico.data_inicio <= CURDATE()');
            $this->db->where('linhaServico.data_fim >= CURDATE()');
        }
        
        if($tipo == 'INATIVO'){
            $this->db->where('linhaServico.data_fim <= CURDATE()');
        }
        
        $this->db->select('cd_telefonia_linha_servico,
                            linhaServico.cd_telefonia_servico,
                            nome,
                            descricao,
                            servico.qtd,
                            servico.valor,
                            linhaServico.data_inicio,
                            linhaServico.data_fim');
        $this->db->where('cd_telefonia_linha', $cd);
        $this->db->join('adminti.telefonia_servico AS servico', 'servico.cd_telefonia_servico = linhaServico.cd_telefonia_servico');  
		$this->db->order_by('servico.nome', 'asc'); 
        return $this->db->get('adminti.telefonia_linha_servico AS linhaServico')->result();

    }
    
    /**
     * Linha_model::verificaServico()
     * 
     * Verifica se a linha possuí o serviço informado
     * 
     * @param $cdLinha Cd linha para consulta
     * @param $cdServico Cd servico para verificação
     * 
     * @return Retorna o número de linhas afetadas
     */
    public function verificaServico($cdLinha, $cdServico, $tipo = 'verificao', $formacao = ''){
        
        if(is_array($cdServico)){
            $this->db->where('telefonia_linha_servico.cd_telefonia_servico IN ('.implode(',', $cdServico).')');
        }else{
            $this->db->where('telefonia_linha_servico.cd_telefonia_servico', $cdServico);
        }
        
        $this->db->where('cd_telefonia_linha', $cdLinha);
        
        $this->db->from('adminti.telefonia_linha_servico');
        
        if($tipo == 'verificao'){
        
            return $this->db->count_all_results();
        
        }
        
        if($tipo == 'conteudo'){   
            #echo 1;
            #return $this->db->row();
            #echo '<pre>'; print_r($this->db->result()); exit();
            $this->db->join('adminti.telefonia_servico AS servico', 'servico.cd_telefonia_servico = telefonia_linha_servico.cd_telefonia_servico');
            #return $this->db->get()->row()->qtd;
            $resultado = $this->db->get()->row();
            if($resultado){
                return '<span title="'.$resultado->nome.'">'.trim($resultado->qtd.' '.$formacao).'</span>';
            }else{
                return '-';
            }
            
        }
    }
    
    /**
     * Linha_model::delete()
     * 
     * Apaga o linha
     * 
     * @return Retorna o número de linhas afetadas
     */
    public function delete(){
        
        $sql = "DELETE FROM adminti.telefonia_linha WHERE cd_telefonia_linha = ".$this->input->post('apg_cd');
        $this->db->query($sql);
        return $this->db->affected_rows();
        
    }
    
    public function salvaServicoMassa(){
        
        $this->db->trans_begin();
        
        $this->servicosMassaApaga();
        $this->servicoMassaAdicionar();
        
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return false;
        }
        else
        {
            $this->db->trans_commit();
            
            return count(preg_split('/\r\n|[\r\n]/', trim($this->input->post('linhas'))));
        }
        
    }
    
    public function servicosMassaApaga(){
        
        if($this->input->post('remover') and $this->input->post('linhas')){
        
            $linhas = implode(',', preg_split('/\r\n|[\r\n]/', str_replace("-", "", trim($this->input->post('linhas'))))); 
            
            $sql = "DELETE FROM adminti.telefonia_linha_servico 
                    WHERE cd_telefonia_servico = ".$this->input->post('remover')." AND cd_telefonia_linha IN(
                    	SELECT
                    	cd_telefonia_linha
                    	FROM adminti.telefonia_linha
                    	INNER JOIN adminti.telefonia_ddd ON telefonia_ddd.cd_telefonia_ddd = telefonia_linha.cd_telefonia_ddd
                    	WHERE CONCAT(ddd,numero) IN (
                    	".$linhas."
                    	)
                    )";
              
            $this->db->query($sql);
        
        }
        
    }
    
    public function servicoMassaAdicionar(){
        
        if($this->input->post('adicionar') and $this->input->post('linhas')){
        
            $addServico = $this->servico->servicos($this->input->post('adicionar'));

            $linhas = implode(',', preg_split('/\r\n|[\r\n]/', str_replace("-", "", trim($this->input->post('linhas'))))); 
            
            $sql = "INSERT INTO adminti.telefonia_linha_servico(cd_telefonia_linha, cd_telefonia_servico, qtd, valor, data_inicio, data_fim)
                    SELECT
                	   cd_telefonia_linha, 
                       ".$addServico->cd_telefonia_servico.",
                       '".$addServico->qtd."',
                       '".$addServico->valor."',
                       '".$addServico->data_inicio."',
                       '".$addServico->data_fim."'
                	FROM adminti.telefonia_linha
                	INNER JOIN adminti.telefonia_ddd ON telefonia_ddd.cd_telefonia_ddd = telefonia_linha.cd_telefonia_ddd
                	WHERE CONCAT(ddd,numero) IN (
                	".$linhas."
                	)";
                                 
            $this->db->query($sql);
        
        }
        
    }

}