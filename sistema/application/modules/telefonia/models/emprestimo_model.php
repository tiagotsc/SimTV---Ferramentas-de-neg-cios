<?php

/**
 * Dashboard_model
 * 
 * Classe que realiza o tratamento do módulo do emprestimo
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2015
 * @access public
 */
class Emprestimo_model extends CI_Model{
	
	/**
	 * Emprestimo_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
        $this->load->library('Util', '', 'util');
	}
    
    /**
    * Emprestimo_model::insere()
    * 
    * Função que realiza a inserção dos dados do emprestimo na base de dados
    * @return O número de emprestimos afetadas pela operação
    */
	public function insere(){
		
		$campo = array();
		$valor = array();
        
        #$campo[] = 'criador_usuario';
        #$valor[] = $this->session->userdata('cd');
		foreach($_POST as $c => $v){
			
            if($c <> 'cd_telefonia_emprestimo' and $c <> 'cd_aparelho_original'){
            
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
        
		$sql = "INSERT INTO adminti.telefonia_emprestimo (".$campos.")\n VALUES(".$valores.");";
        
		$this->db->query($sql);
        $cd = $this->db->insert_id();
        
        $this->atualizaAparelhoAtual();
        
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
    * Emprestimo_model::atualiza()
    * 
    * Função que realiza a atualização dos dados do emprestimo na base de dados
    * @return O número de emprestimo afetadas pela operação
    */
	public function atualiza(){
        
        #$campoValor[] = 'atualizador_usuario = '.$this->session->userdata('cd');
        #$campoValor[] = "data_atualizacao_usuario = '".date('Y-m-d h:i:s')."'";
        
		foreach($_POST as $c => $v){
			
			if($c != 'cd_telefonia_emprestimo' and $c != 'cd_aparelho_original'){
				$valorFormatado = $this->util->removeAcentos($this->input->post($c));
				$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
			
				$campoValor[] = $c.' = '.$valorFormatado;
			
			}
		}
		
		$camposValores = implode(', ', $campoValor);
		
        $this->db->trans_begin();
        
		$sql = "UPDATE adminti.telefonia_emprestimo SET ".$camposValores." WHERE cd_telefonia_emprestimo = ".$this->input->post('cd_telefonia_emprestimo').";";
		$this->db->query($sql);
        
        $this->atualizaAparelhoAtual();
        $this->atualizaAparelhoAnterior();
        
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
    * Emprestimo_model::atualizaAparelhoAtual()
    * 
    * Função que atualiza o status do aparelho atual para "Ativo" no empréstimo corrente (Aparelho associado)
    * 
    */
    public function atualizaAparelhoAtual(){
        
        if($this->input->post('cd_telefonia_aparelho') != ''){
            
            $sql = "UPDATE adminti.telefonia_aparelho SET status = 'Ativo' WHERE cd_telefonia_aparelho = ".$this->input->post('cd_telefonia_aparelho');
            $this->db->query($sql);
            
        }
        
    }
    
    /**
    * Emprestimo_model::atualizaAparelhoAnterior()
    * 
    * Função que atualiza o status do aparelho anterior para "Estoque" no empréstimo corrente (Aparelho desassociado)
    * 
    */
    public function atualizaAparelhoAnterior(){
        
        if($this->input->post('cd_aparelho_original') != '' and $this->input->post('cd_aparelho_original') != $this->input->post('cd_telefonia_aparelho')){
            
            $sql = "UPDATE adminti.telefonia_aparelho SET status = 'Estoque' WHERE cd_telefonia_aparelho = ".$this->input->post('cd_aparelho_original');
            $this->db->query($sql);
            
        }
        
    }
	
    /**
    * Emprestimo_model::dados()
    * 
    * Função que monta um array com todos os dados do emprestimo
    * @param $cd Cd do emprestimo para recuperação de dados
    * @return Retorna todos os dados do emprestimo
    */
	public function dados($cd){
        
        $this->db->select("cd_telefonia_emprestimo,
                            cd_telefonia_aparelho,
                            cd_usuario,
                            DATE_FORMAT(data_inicio, '%d/%m/%Y') AS data_inicio, 
                            DATE_FORMAT(data_fim, '%d/%m/%Y') AS data_fim
                            ");
        $this->db->where('cd_telefonia_emprestimo', $cd);
		$dados = $this->db->get('adminti.telefonia_emprestimo')->result_array(); # TRANSFORMA O RESULTADO EM ARRAY
		
		return $dados[0];
	}
	
    /**
    * Emprestimo_model::campos()
    * 
    * Função que pega os nomes de todos os campos existentes na tabela emprestimo
    * @return Os campos da tabela emprestimo
    */
	public function campos(){
		
		$campos = $this->db->get('adminti.telefonia_emprestimo')->list_fields();
		
		return $campos;
		
	}
    
    /**
     * Emprestimo_model::pesquisa()
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
    public function pesquisa($linha = null, $imei = null, $user = null, $pagina = null, $mostra_por_pagina = null, $sort_by = null, $sort_order = null){
        
        // Verifica qual ordenação foi informada
        $sort_order = ($sort_order == 'desc') ? 'desc' : 'asc';
        // Campos da tabela que podem receber ordenação
		$sort_columns = array('temprestimo.cd_telefonia_emprestimo', /*'tddd.ddd', */'tlinha.numero', 'usuario.nome_usuario', 'temprestimo.data_inicio', 'temprestimo.data_termino', 'temprestimo.parcelas');
        // Verifica qual campo foi informado para ordenação
        $sort_by = (in_array($sort_by, $sort_columns)) ? $sort_by : 'tlinha.numero';
                        
        $this->db->select(" 
                            temprestimo.cd_telefonia_emprestimo,
                            toperadora.cd_telefonia_operadora,
                            tddd.ddd,
                            tlinha.numero AS linha,
                            (
                                SELECT 
                                GROUP_CONCAT(' ',taparelhoSec.imei)
                                FROM adminti.telefonia_imei AS taparelhoSec
                                WHERE taparelhoSec.cd_telefonia_aparelho = taparelho.cd_telefonia_aparelho
                            ) AS imei,
                            usuario.cd_usuario,
                            usuario.matricula_usuario,
                            usuario.nome_usuario,
                            usuario.email_usuario,
                            temprestimo.data_inicio,
                            temprestimo.data_fim,
                            temprestimo.data_criacao_termo,
                            temprestimo.aceite_termo,
                            DATE_FORMAT(temprestimo.data_aceite_termo, '%d/%m/%Y') AS data_aceite_termo,
                            SUBSTR(temprestimo.data_aceite_termo, 12, 8) AS hora_aceite,
                            '' AS parcelas,
                            '' AS fidelizado,
                            '' AS multa
                            ");       
        
        
        if($linha != '0'){
            $this->db->like('tlinha.numero', $linha);  
        }
        
        if($imei != '0'){
            $this->db->where("temprestimo.cd_telefonia_aparelho IN (
                            	SELECT 
                                    sec.cd_telefonia_aparelho 
                                FROM adminti.telefonia_imei AS sec 
                                WHERE sec.imei LIKE '%".$imei."%'
                            )");
        }
        
        if($user != '0'){
            #$this->db->like('usuario.nome_usuario', $user);
            $this->db->where("(usuario.nome_usuario LIKE '%".$user."%' OR usuario.matricula_usuario LIKE '".$user."')");
        }
        
        $this->db->order_by($sort_by, $sort_order);  
        $this->db->join('adminti.telefonia_aparelho AS taparelho', 'taparelho.cd_telefonia_aparelho = temprestimo.cd_telefonia_aparelho'); 
        $this->db->join('adminti.usuario AS usuario', 'usuario.cd_usuario = temprestimo.cd_usuario');
        $this->db->join('adminti.telefonia_emprestimo_linha AS temp_li', 'temp_li.cd_telefonia_emprestimo = temprestimo.cd_telefonia_emprestimo', 'left');
        $this->db->join('adminti.telefonia_linha AS tlinha', 'tlinha.cd_telefonia_linha = temp_li.cd_telefonia_linha', 'left');
        $this->db->join('adminti.telefonia_ddd AS tddd', 'tddd.cd_telefonia_ddd = tlinha.cd_telefonia_ddd', 'left');
        $this->db->join('adminti.telefonia_operadora AS toperadora', 'toperadora.cd_telefonia_operadora = tlinha.cd_telefonia_operadora', 'left');
        return $this->db->get('adminti.telefonia_emprestimo AS temprestimo', $mostra_por_pagina, $pagina)->result();
    }
    
    /**
     * Emprestimo_model::pesquisaQtd()
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
    public function pesquisaQtd($linha = null, $imei = null, $user = null){
        
        if($linha != '0'){
            $this->db->like('tlinha.numero', $linha); 
        }
        
        if($imei != '0'){
            $this->db->where("temprestimo.cd_telefonia_aparelho IN (
                            	SELECT 
                                    sec.cd_telefonia_aparelho 
                                FROM adminti.telefonia_imei AS sec 
                                WHERE sec.imei LIKE '%".$imei."%'
                            )");
        }
        
        if($user != '0'){
            $this->db->like('usuario.nome_usuario', $user); 
        }
        
        $this->db->select('count(*) as total');
        $this->db->join('adminti.telefonia_aparelho AS taparelho', 'taparelho.cd_telefonia_aparelho = temprestimo.cd_telefonia_aparelho'); 
        $this->db->join('adminti.usuario AS usuario', 'usuario.cd_usuario = temprestimo.cd_usuario');
        $this->db->join('adminti.telefonia_emprestimo_linha AS temp_li', 'temp_li.cd_telefonia_emprestimo = temprestimo.cd_telefonia_emprestimo', 'left');
        $this->db->join('adminti.telefonia_linha AS tlinha', 'tlinha.cd_telefonia_linha = temp_li.cd_telefonia_linha', 'left');
        $this->db->join('adminti.telefonia_ddd AS tddd', 'tddd.cd_telefonia_ddd = tlinha.cd_telefonia_ddd', 'left');
        $this->db->join('adminti.telefonia_operadora AS toperadora', 'toperadora.cd_telefonia_operadora = tlinha.cd_telefonia_operadora', 'left');
        return $this->db->get('adminti.telefonia_emprestimo AS temprestimo', $mostra_por_pagina, $pagina)->result();
    }
    
    /**
    * Emprestimo_model::dadosEmprestimos()
    * 
    * Função que pega os dados dos empréstimos
    * @return Os dados
    */
    public function dadosEmprestimos(){
        
        $this->db->select("tlinha.cd_telefonia_linha,
                            DATE_FORMAT(temprestimo.data_inicio, '%d/%m/%Y') AS data_inicio, 
                            DATE_FORMAT(temprestimo.data_fim, '%d/%m/%Y') AS data_fim,
                            data_criacao_termo,
                            /*PERIOD_DIFF(DATE_FORMAT(temprestimo.data_fim, '%Y%m'), DATE_FORMAT(temprestimo.data_inicio, '%Y%m')) AS parcelas_restantes,*/
                            TIMESTAMPDIFF(MONTH, temprestimo.data_inicio, temprestimo.data_fim)  AS parcelas_restantes,
                            taparelho.tipo,
                            tmarca.nome AS marca,
                            modelo,
                            matricula_usuario,
                            nome_usuario,
                            email_usuario,
                            login_usuario,
                            tcargo.nome AS cargo,
                            nome_departamento,
                            CASE WHEN status_usuario = 'A' THEN 'Ativo' ELSE 'Inativo' END AS status_usuario,
                            ddd,
                            numero,
                            (SELECT GROUP_CONCAT(imei) FROM adminti.telefonia_imei WHERE cd_telefonia_aparelho = taparelho.cd_telefonia_aparelho) AS imei");
        $this->db->where('temprestimo.cd_telefonia_emprestimo', $this->input->post('cd_emprestimo'));
        $this->db->where('tlinha.numero', $this->input->post('linha'));
        $this->db->join('adminti.telefonia_aparelho AS taparelho', 'taparelho.cd_telefonia_aparelho = temprestimo.cd_telefonia_aparelho'); 
        $this->db->join('adminti.telefonia_marca AS tmarca', 'tmarca.cd_telefonia_marca = taparelho.cd_telefonia_marca');
        $this->db->join('adminti.usuario AS usuario', 'usuario.cd_usuario = temprestimo.cd_usuario');
        $this->db->join('adminti.cargo AS tcargo', 'tcargo.cd_cargo = usuario.cd_cargo', 'left');
        $this->db->join('adminti.departamento AS tdepartamento', 'tdepartamento.cd_departamento = usuario.cd_departamento');
        $this->db->join('adminti.telefonia_emprestimo_linha AS temp_li', 'temp_li.cd_telefonia_emprestimo = temprestimo.cd_telefonia_emprestimo');
        $this->db->join('adminti.telefonia_linha AS tlinha', 'tlinha.cd_telefonia_linha = temp_li.cd_telefonia_linha');
        $this->db->join('adminti.telefonia_ddd AS tddd', 'tddd.cd_telefonia_ddd = tlinha.cd_telefonia_ddd');
        return $this->db->get('adminti.telefonia_emprestimo AS temprestimo')->result();
        
    }
    
    /**
    * Emprestimo_model::servicosLinha()
    * 
    * Função que pega os serviços da linha
    * @return Os dados
    */    
    public function servicosLinha(){
        
        $this->db->where('tls.cd_telefonia_linha', $this->input->post('cd_linha'));
        $this->db->where('tservico.status', 'A');
        $this->db->join('adminti.telefonia_servico AS tservico', 'tservico.cd_telefonia_servico = tls.cd_telefonia_servico');
        $this->db->order_by('nome', 'asc'); 
        return $this->db->get('adminti.telefonia_linha_servico AS tls')->result();
        
    }
    
    /**
    * Emprestimo_model::listaUsuario()
    * 
    * Função que pega os nomes de todos os campos existentes na tabela emprestimo
    * @return Os campos da tabela emprestimo
    */
    public function listaUsuarios($cond = 'todos'){
        
        if($cond == 'naoAssc'){
            $condicao = "AND cd_usuario NOT IN (
                    	SELECT DISTINCT cd_usuario FROM adminti.telefonia_emprestimo
                    )";
        }elseif($cond == 'simAss'){
            $condicao = "AND cd_usuario IN (
                    	SELECT DISTINCT cd_usuario FROM adminti.telefonia_emprestimo
                    )";
        }else{
            $condicao = "";
        }
        
        $sql = "SELECT 
                    cd_usuario,
                    matricula_usuario,
                    nome_usuario,
                    /*CONCAT(SUBSTRING_INDEX(nome_usuario,' ',1),' ', SUBSTRING_INDEX(nome_usuario,' ',-1)) AS nome_usuario,*/
                    login_usuario,
                    unidade.nome AS unidade 
                FROM adminti.usuario 
                LEFT JOIN adminti.unidade ON adminti.unidade.cd_unidade = adminti.usuario.cd_unidade
                WHERE status_usuario = 'A'
                AND cd_departamento IS NOT NULL
                ".$condicao."
                ORDER BY nome_usuario";
                
        return $this->db->query($sql)->result();
        
    }
    
    /**
    * Emprestimo_model::termo()
    * 
    * Função que pega os dados do termo
    * @return Dados dos termos
    */
    public function termo(){
        
        $this->db->select("cd_telefonia_emprestimo,
                            data_criacao_termo,
                            DATE_FORMAT(data_termo, '%d/%m/%Y') AS data_termo, 
                            aceite_termo,
                            DATE_FORMAT(data_aceite_termo, '%d/%m/%Y') AS data_aceite_termo
                            ");
        $this->db->where('cd_telefonia_emprestimo', $this->input->post('cd_emprestimo'));
        return $this->db->get('adminti.telefonia_emprestimo')->result();
        
    }
    
    public function salvaTermo(){
        
        $this->db->trans_begin();
        
        $sql = "UPDATE adminti.telefonia_emprestimo SET";
        $sql .= "\n data_termo = ".$valorFormatado = $this->util->formaValorBanco($this->input->post('data_termo'));
        
        if(!$this->input->post('data_criacao_termo')){
        $sql .= "\n, criador_termo = ".$this->session->userdata('cd');
        $sql .= "\n, data_criacao_termo = '".date('Y-m-d H:i:s')."'";
        }
        
        if($this->input->post('data_criacao_termo')){
            $sql .= "\n, alterador_termo = ".$this->session->userdata('cd');
            $sql .= "\n, data_alteracao_termo = '".date('Y-m-d H:i:s')."'";
        }
        
        if($this->input->post('reset_termo')){
            $sql .= "\n, aceite_termo = null";
            $sql .= "\n, data_aceite_termo = null";
        }
        
        $sql .= "\n WHERE cd_telefonia_emprestimo = ".$this->input->post('cd_emprestimo_termo');
        $this->db->query($sql);
        
        $sql = "\nDELETE FROM adminti.telefonia_emprestimo_acessorio WHERE cd_telefonia_emprestimo = ".$this->input->post('cd_emprestimo_termo');
        $this->db->query($sql);
        
        foreach($this->input->post('cd_acessorio') as $acessorio){
            $sql = "\nINSERT INTO adminti.telefonia_emprestimo_acessorio (cd_telefonia_emprestimo, cd_telefonia_acessorio) ";
            $sql .= "VALUES(".$this->input->post('cd_emprestimo_termo').", ".$acessorio.");";
            $this->db->query($sql);
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
    
    public function salvaRespostaTermo(){
        
        $sql = "UPDATE adminti.telefonia_emprestimo";
        $sql .= " SET aceite_termo = '".$this->input->post('res_concordo')."', data_aceite_termo = '".date('Y-m-d H:i:s')."'";
        $sql .= " WHERE cd_telefonia_emprestimo = ".$this->input->post('cd_emprestimo_res_termo').";";
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
    * Emprestimo_model::acessorios()
    * 
    * Função que pega todos os acessórios e inclusive já informa se determinado acessório 
    * esta contido no empréstimo quando o mesmo ($cd_emprestimo) é informado
    * @param $cd_emprestimo Serve para verificar quais acessórios estão presentes no empréstimo
    * @return Os acessórios inclusive a presença ou não no empréstimo quando informado
    */
    public function acessorios($cd_emprestimo = null){
        
        $condicao = ",
                    '' AS emprestimo";
        if($cd_emprestimo){
            $condicao = ",
                	(
                		SELECT 
                			tea.cd_telefonia_emprestimo_acessorio 
                		FROM adminti.telefonia_emprestimo_acessorio AS tea 
                		WHERE tea.cd_telefonia_acessorio = ta.cd_telefonia_acessorio
                		AND tea.cd_telefonia_emprestimo = ".$cd_emprestimo."
                	) AS emprestimo";
        }
        
        $sql = "SELECT 
                	DISTINCT
                	ta.cd_telefonia_acessorio,
                	nome
                    ".$condicao."
                FROM adminti.telefonia_acessorio AS ta
                WHERE 
                status = 'A'";
                
        return $this->db->query($sql)->result();
        
    }
    
    /**
    * Emprestimo_model::acessorios()
    * 
    * Função que pega os acessórios do termo
    * @param $cd_emprestimo para busca
    * @return Os acessórios do termo (empréstimo)
    */
    public function acessoriosTermo($cd_emprestimo = null){
                
        $this->db->select("ta.cd_telefonia_acessorio,
                	       ta.nome");
        $this->db->where('ta.status', 'A');
        if($cd_emprestimo){
            $this->db->where('tea.cd_telefonia_emprestimo', $cd_emprestimo);
        }
        $this->db->join('adminti.telefonia_acessorio AS ta', 'ta.cd_telefonia_acessorio = tea.cd_telefonia_acessorio');
        return $this->db->get('adminti.telefonia_emprestimo_acessorio AS tea')->result(); 
        
    }
    
    /**
     * Emprestimo_model::termoUsuario()
     * 
     * Pega o termo do usuário caso ele possua
     * 
     * @return Retorna os dados do termo
     */
    public function termoUsuario($cd_usuario){
        
        $this->db->select("temprestimo.cd_telefonia_emprestimo,
                            tlinha.cd_telefonia_operadora,
                            temprestimo.cd_usuario,
                            usuario.nome_usuario,
                            usuario.rg_usuario,
                            usuario.cpf_usuario,
                            tcargo.nome AS nome_cargo,
                            DATE_FORMAT(temprestimo.data_termo, '%d/%m/%Y') AS data_termo, 
                            temprestimo.aceite_termo,
                            DATE_FORMAT(temprestimo.data_aceite_termo, '%d/%m/%Y') AS data_aceite_termo,
                            SUBSTR(temprestimo.data_aceite_termo, 12, 8) AS hora_aceite,
                            tddd.ddd,
                            tlinha.cd_telefonia_linha,
                            tlinha.numero,
                            tmarca.nome AS marca,
                            taparelho.modelo,
                            (
                                SELECT 
                                    GROUP_CONCAT(timei.imei) 
                                FROM adminti.telefonia_imei AS timei 
                                WHERE timei.cd_telefonia_aparelho = taparelho.cd_telefonia_aparelho
                            ) AS imei
                            ");
        $this->db->where('data_criacao_termo IS NOT NULL');
        $this->db->where('temprestimo.cd_usuario', $cd_usuario);
        
        $this->db->join('adminti.telefonia_aparelho AS taparelho', 'taparelho.cd_telefonia_aparelho = temprestimo.cd_telefonia_aparelho'); 
        $this->db->join('adminti.telefonia_marca AS tmarca', 'tmarca.cd_telefonia_marca = taparelho.cd_telefonia_marca');
        $this->db->join('adminti.usuario AS usuario', 'usuario.cd_usuario = temprestimo.cd_usuario');
        $this->db->join('adminti.cargo AS tcargo', 'tcargo.cd_cargo = usuario.cd_cargo', 'left');
        #$this->db->join('adminti.departamento AS tdepartamento', 'tdepartamento.cd_departamento = usuario.cd_departamento');
        $this->db->join('adminti.telefonia_emprestimo_linha AS temp_li', 'temp_li.cd_telefonia_emprestimo = temprestimo.cd_telefonia_emprestimo');
        $this->db->join('adminti.telefonia_linha AS tlinha', 'tlinha.cd_telefonia_linha = temp_li.cd_telefonia_linha');
        $this->db->join('adminti.telefonia_operadora AS toperadora', 'toperadora.cd_telefonia_operadora = tlinha.cd_telefonia_operadora');
        $this->db->join('adminti.telefonia_ddd AS tddd', 'tddd.cd_telefonia_ddd = tlinha.cd_telefonia_ddd');
        #$this->db->join('adminti.telefonia_imei AS timei', 'timei.cd_telefonia_aparelho = taparelho.cd_telefonia_aparelho', 'left');
        
        return $this->db->get('adminti.telefonia_emprestimo AS temprestimo')->result();        
        
    }
    
    /**
     * Aparelho_model::pesqVisualizar()
     * 
     * Pega os dados da pesquisa
     * 
     * @return 
     */
    public function pesqVisualizar(){
        
        $sql = "SELECT
                	emprestimo.cd_telefonia_emprestimo,
                	emprestimo.data_inicio AS data_inicio_emprestimo,
                	emprestimo.data_fim AS data_fim_emprestimo,
                	data_termo,
                	aceite_termo,
                	data_aceite_termo,
                    usuario.cd_usuario,
                	matricula_usuario,
                	nome_usuario,
                	ddd,
                    cd_telefonia_operadora,
                	numero AS linha,
                    (
                		SELECT 
                			GROUP_CONCAT(imei) AS imei
                		FROM adminti.telefonia_imei AS timei
                		WHERE timei.cd_telefonia_aparelho = emprestimo.cd_telefonia_aparelho
                	) AS imei
                FROM adminti.telefonia_emprestimo AS emprestimo
                LEFT JOIN adminti.usuario AS usuario ON emprestimo.cd_usuario = usuario.cd_usuario
                LEFT JOIN adminti.telefonia_emprestimo_linha AS emp_linha ON emp_linha.cd_telefonia_emprestimo = emprestimo.cd_telefonia_emprestimo
                LEFT JOIN adminti.telefonia_linha AS tlinha ON tlinha.cd_telefonia_linha = emp_linha.cd_telefonia_linha
                LEFT JOIN adminti.telefonia_ddd AS tddd ON tddd.cd_telefonia_ddd = tlinha.cd_telefonia_ddd
                WHERE
                1=1 ";
        
        if($this->input->post('linha') != ''){        
            $sql .= "AND numero LIKE '%".$this->input->post('linha')."%' ";
        }
        
        if($this->input->post('imei') != ''){ 
            $sql .= "AND emprestimo.cd_telefonia_aparelho IN (
                    	SELECT timei.cd_telefonia_aparelho FROM adminti.telefonia_imei AS timei WHERE timei.imei LIKE '%".$this->input->post('imei')."%'
                    ) ";
        }
        
        if($this->input->post('user') != ''){ 
            $sql .= "AND (nome_usuario LIKE '%".$this->input->post('user')."%' OR matricula_usuario LIKE '%".$this->input->post('user')."%')";
        }
        
        $sql .= "LIMIT 10";
                
        return $this->db->query($sql)->result();                                
        
    }
    
    
    /**
     * Aparelho_model::associaLinhaEmprestimo()
     * 
     * Associa linha(s) ao empréstimo
     * 
     * @return 
     */
    public function associaLinhaEmprestimo($linhas){
        
        $this->db->trans_begin();

        $sql = "DELETE FROM adminti.telefonia_emprestimo_linha WHERE cd_telefonia_emprestimo = ".$this->input->post('cd_emprestimo');

        $this->db->query($sql);
        
        foreach($linhas as $li){
            
            $sql = "INSERT INTO adminti.telefonia_emprestimo_linha (cd_telefonia_linha, cd_telefonia_emprestimo) VALUES(".$li.", ".$this->input->post('cd_emprestimo').");";
          
            $this->db->query($sql);
            
        }
        
        $this->atualizaStatusLinha($linhas, 'A');

        if($this->db->trans_status() === TRUE){
            return $this->db->trans_commit();
        }else{
            return $this->db->trans_rollback();
        }
    }
    
    /**
     * Aparelho_model::atualizaStatusLinha()
     * 
     * Muda o status da linha dependendo da associação ou desassociação do empréstimo
     * 
     * @return 
     */
    public function atualizaStatusLinha($linhas, $status){
        
        #$this->db->trans_begin();
        
        if(is_array($linhas)){

            foreach($linhas as $li){
                
                $sql = "UPDATE adminti.telefonia_linha SET status = '".$status."' WHERE cd_telefonia_linha = ".$li;
              
                $this->db->query($sql);
                
            }
        
        }else{
            
            $sql = "UPDATE adminti.telefonia_linha SET status = '".$status."' WHERE cd_telefonia_linha = ".$linhas;
              
            $this->db->query($sql);
            
        }
        /*
        if($this->db->trans_status() === TRUE){
            return $this->db->trans_commit();
        }else{
            return $this->db->trans_rollback();
        }
        */
    }
    
    /**
     * Aparelho_model::atualizaStatusAparelho()
     * 
     * Muda status do aparelho dependendo da associação ou desassociação do empréstimo
     * 
     * @return 
     */
    public function atualizaStatusAparelho($aparelhos, $status){
        
        #$this->db->trans_begin();
        
        if(is_array($aparelhos)){

            foreach($aparelhos as $apa){
                
                $sql = "UPDATE adminti.telefonia_aparelho SET status = '".$status."' WHERE cd_telefonia_aparelho = ".$apa;
              
                $this->db->query($sql);
                
            }
        
        }else{
            
            $sql = "UPDATE adminti.telefonia_aparelho SET status = '".$status."' WHERE cd_telefonia_aparelho = ".$aparelhos;
              
            $this->db->query($sql);
            
        }
        /*
        if($this->db->trans_status() === TRUE){
            return $this->db->trans_commit();
        }else{
            return $this->db->trans_rollback();
        }
        */
    }
    
    /**
     * Aparelho_model::usuariosAssociados()
     * 
     * Usuários associados ao empréstimo
     * 
     * @return 
     */
    public function usuariosAssociados(){
        
        $sql = "SELECT
                	cd_usuario,
                	nome_usuario
                FROM adminti.usuario
                WHERE cd_usuario IN (
                SELECT DISTINCT cd_usuario FROM adminti.telefonia_emprestimo
                )
                AND cd_departamento = 35
                ORDER BY nome_usuario";
        return $this->db->query($sql)->result();
        
    }
    
    /**
     * Aparelho_model::linhasAssociadas()
     * 
     * Linhas associadas ao empréstimo
     * 
     * @return 
     */
    public function linhasAssociadas(){
        
        $sql = "SELECT
                	tlinha.cd_telefonia_linha,
                	numero
                FROM adminti.telefonia_linha AS tlinha
                INNER JOIN adminti.telefonia_emprestimo_linha AS temp_lin ON temp_lin.cd_telefonia_linha = tlinha.cd_telefonia_linha
                INNER JOIN adminti.telefonia_emprestimo AS temp ON temp.cd_telefonia_emprestimo = temp_lin.cd_telefonia_emprestimo
                INNER JOIN adminti.usuario AS tusuario ON tusuario.cd_usuario = temp.cd_usuario AND tusuario.cd_departamento = 35
                ORDER BY numero";
        return $this->db->query($sql)->result();
        
    }
    
    public function pegaCdLinha($cdEmprestimo){
        
        $sql = "SELECT cd_telefonia_linha FROM adminti.telefonia_emprestimo_linha WHERE cd_telefonia_emprestimo = ".$cdEmprestimo;
        return $this->db->query($sql)->result();
        
    }
    
    public function pegaCdAparelho($cdEmprestimo){
        
        $sql = "SELECT cd_telefonia_aparelho FROM adminti.telefonia_emprestimo WHERE cd_telefonia_emprestimo = ".$cdEmprestimo;
        return $this->db->query($sql)->result();
    }
    
    /**
     * Emprestimo_model::delete()
     * 
     * Apaga o emprestimo
     * 
     * @return Retorna o número de linhas afetadas
     */
    public function delete(){
        
        $cdLinha = $this->pegaCdLinha($this->input->post('apg_cd'));
        
        foreach($cdLinha as $linha){
            $this->atualizaStatusLinha($linha->cd_telefonia_linha, 'E');
        }
        
        $cdAparelho = $this->pegaCdAparelho($this->input->post('apg_cd'));
        
        foreach($cdAparelho as $aparelho){
            $this->atualizaStatusAparelho($aparelho->cd_telefonia_aparelho, 'Estoque');
        }
        
        $sql = "DELETE FROM adminti.telefonia_emprestimo WHERE cd_telefonia_emprestimo = ".$this->input->post('apg_cd');
        $this->db->query($sql);
        return $this->db->affected_rows();
        
    }

}