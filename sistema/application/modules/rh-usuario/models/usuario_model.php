<?php

/**
 * Dashboard_model
 * 
 * Classe que realiza o tratamento do usu�rio
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2014
 * @access public
 */
class Usuario_model extends CI_Model{
    
    const tabelaDb = 'adminti.usuario';
    const tabela = 'usuario';
	
    /**
     * Usuario_model::__construct()
     * 
     * @return
     */
    function __construct(){
            parent::__construct();

    }
    
    /**
    * Usuario_model::insere()
    * 
    * Fun��o que realiza a inser��o dos dados do usu�rio na base de dados
    * @return O n�mero de linhas afetadas pela opera��o
    */
    public function insere(){

        $campo = array();
        $valor = array();
        $sistemas = array();

        $campo[] = 'criador_usuario';
        $valor[] = $this->session->userdata('cd');
        
        foreach($_POST as $c => $v){

            if($c <> 'cd_usuario' and $c <> 'cd_perfil' and $c <> 'status_usuario' and $c <> 'sistemas' and $c <> 'beneficios'){

                        $valorFormatado = $this->util->removeAcentos($this->input->post($c));
                        $valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));

                        $campo[] = $c;
                        $valor[] = $valorFormatado;
            }                
        }

    # A senha in�cial fica definida com o CPF
            #$campo[] = 'senha_usuario';
            #$valor[] = $this->util->formaValorBanco(md5(str_replace('-', '', str_replace('.', '',$this->input->post('cpf_funcionario')))));

        $campos = implode(', ', $campo);
        $valores = implode(', ', $valor);

        $this->db->trans_begin();

        $sql = "INSERT INTO adminti.usuario (".$campos.")\n VALUES(".$valores.");";
        $this->db->query($sql);
        $cd = $this->db->insert_id();
        
        $this->salvaSolicitacaoSistema($cd);
//        $this->salvaSolicitacaoBeneficio($cd);
        
        
        # Registra Perfil
        $perfil = $this->util->formaValorBanco($this->input->post('cd_perfil'));
        $sql = "INSERT INTO sistema.config_usuario (cd_usuario, cd_perfil, status_config_usuario)\n VALUES(".$cd.",".$perfil.",'".$this->input->post('status_usuario')."');";
        $this->db->query($sql);

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
    * Usuario_model::atualiza()
    * 
    * Fun��o que realiza a atualiza��o dos dados do usu�rio na base de dados
    * @return O n�mero de linhas afetadas pela opera��o
    */
    public function atualiza(){

        $campoValor[] = 'atualizador_usuario = '.$this->session->userdata('cd');
        $campoValor[] = "data_atualizacao_usuario = '".date('Y-m-d h:i:s')."'";
        $sistemas = array();
        $execao = [
            'cd_usuario',
            'cd_perfil',
            'sistemas',
            'beneficios',
            'valeTransporte',
            'valor_passagem',
            'id_passagem',
            'confBeneficio',
            'dirCartao',
            ];

        foreach($_POST as $c => $v){

                if(!in_array($c, $execao)){
                        $valorFormatado = $this->util->removeAcentos($this->input->post($c));
                        $valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));

                        $campoValor[] = $c.' = '.$valorFormatado;

                }
        }

        $camposValores = implode(', ', $campoValor);

        $this->db->trans_begin();

        $sql = "UPDATE adminti.usuario SET ".$camposValores." WHERE cd_usuario = ".$this->input->post('cd_usuario').";";
        $this->db->query($sql);
        
        $this->salvaSolicitacaoSistema();
//        $this->salvaSolicitacaoBeneficio();

        # Registra Perfil
        $perfil = $this->util->formaValorBanco($this->input->post('cd_perfil'));
        $sql = "UPDATE sistema.config_usuario SET cd_perfil = ".$perfil.", status_config_usuario = '".$this->input->post('status_usuario')."' WHERE cd_usuario = ".$this->input->post('cd_usuario');
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

                #return $this->db->query($sql); # RETORNA O N�MERO DE LINHAS AFETADAS

    }
	
    /**
    * Usuario_model::dadosUsuario()
    * 
    * Fun��o que monta um array com todos os dados do usu�rio
    * @param $cd Cd do usu�rio para recupera��o de dados
    * @return Retorna todos os dados do usu�rio
    */
    public function dadosUsuario($id){

        $this->db->select('usuario.cd_usuario, login_usuario, matricula_usuario, nome_usuario, rg_usuario, cpf_usuario, email_usuario, nome_usuario, cd_cargo, cd_departamento, cd_perfil, status_config_usuario, cd_estado, cd_unidade, tipo_usuario, index_php_usuario, tipo_funcionario_usuario, elegivel_beneficio');
        $this->db->where('usuario.cd_usuario', $id);
        $this->db->join('sistema.config_usuario', 'usuario.cd_usuario = sistema.config_usuario.cd_usuario', 'left');
        $usuario = $this->db->get('adminti.usuario')->result_array(); # TRANSFORMA O RESULTADO EM ARRAY

        return $usuario[0];
    }
	
    /**
    * Usuario_model::camposUsuario()
    * 
    * Fun��o que pega os nomes de todos os campos existentes na tabela usu�rio
    * @return Os campos da tabela usu�rio
    */
	public function camposUsuario(){
		
        $this->db->select('usuario.cd_usuario, login_usuario, matricula_usuario, nome_usuario, email_usuario, nome_usuario, rg_usuario, cpf_usuario, cd_departamento, cd_perfil, status_config_usuario, cd_estado, cd_unidade, tipo_usuario');
        $this->db->join('sistema.config_usuario', 'usuario.cd_usuario = sistema.config_usuario.cd_usuario', 'left');
        $campos = $this->db->get('adminti.usuario')->list_fields();

        return $campos;
		
	}
    
    
    /**
     * Usuario_model::deleteUsuario()
     * 
     * Apaga o usu�rio
     * 
     * @return Retorna o n�mero de linhas afetadas
     */
    public function deleteUsuario(){
        
        
        
        $sql = "DELETE FROM adminti.usuario WHERE cd_usuario = ".$this->input->post('apg_cd_usuario');
        $this->db->query($sql);
        return $this->db->affected_rows();
        
    }
    
    /**
     * Usuario_model::autenticaUsuario()
     * 
     * Autentica o usu�rio
     * 
     * @return Retorna os dados do usu�rio caso ele exista
     */
    public function autenticaUsuario(){
        
        $this->db->select('usuario.cd_usuario, login_usuario, matricula_usuario, nome_usuario, email_usuario, cd_departamento, cd_perfil, status_usuario AS status_pai, status_config_usuario AS status_filho, status_chat_usuario, index_php_usuario, data_chat_usuario, CURRENT_TIMESTAMP() AS data_hora_atual');
        $this->db->where('login_usuario', $this->input->post('login'));
        $this->db->where('status_usuario', 'A');
        $this->db->join('sistema.config_usuario', 'usuario.cd_usuario = sistema.config_usuario.cd_usuario', 'left');
        return $this->db->get('adminti.usuario')->result();
        
    }
    
    /**
     * Usuario_model::atualizaDataHoraAcesso()
     * 
     * Atualiza acesso e status do usu�rio no sistema
     * 
     * @return 
     */
    public function atualizaDataHoraAcesso($logado = 'S', $cd_usuario){
        
        if($logado == 'S'){
            $chatStatus = ', data_chat_usuario = CURRENT_TIMESTAMP';
        }else{
            $chatStatus = '';
        }
        
        $sql = "UPDATE adminti.usuario SET logado_usuario = '".$logado."', data_logado_usuario = CURRENT_TIMESTAMP".$chatStatus." WHERE cd_usuario = ".$cd_usuario.";";
		$this->db->query($sql);
        
    }
    
    
    //nova estrutura
    
    public function pesquisa($parametros, $mostra_por_pagina, $sort_by, $sort_order, $pagina){
        
        $this->db->select("
                            usuario.cd_usuario,
                            matricula_usuario,
                            login_usuario,
                            nome_usuario,
                            email_usuario,
                            CASE WHEN status_usuario = 'A'
                                THEN 'Ativo'
                            ELSE 'Inativo' END AS status_usuario,
                            nome_estado,
                            nome_departamento,
                            nome_perfil
                            ");

        if($sort_by != '1'){
            $this->db->order_by($sort_by, $sort_order);
        }

        if($parametros){
            $post = explode('|', $parametros);
            foreach($post as $campoValor){
                $res = explode('=', $campoValor);
                
                if($res[1] != ''){
                    if(in_array($res[0], array('status'))){
                        $this->db->where(self::tabela.'.'.$res[0], $res[1]);
                    }else{
                        $this->db->like(self::tabela.'.'.$res[0], $res[1]);
                    }
                }
                
            }
        }
        
        $this->db->join('adminti.departamento', 'adminti.departamento.cd_departamento = adminti.usuario.cd_departamento', 'left');      
        $this->db->join('adminti.estado', 'adminti.estado.cd_estado = adminti.usuario.cd_estado', 'left');
        $this->db->join('sistema.config_usuario', 'usuario.cd_usuario = sistema.config_usuario.cd_usuario', 'left');
        $this->db->join('sistema.perfil', 'sistema.perfil.cd_perfil = sistema.config_usuario.cd_perfil', 'left');
        $this->db->order_by($sort_by, $sort_order);
        
        
        $dados['id'] = 'cd_usuario';
        $dados['dados'] = $this->db->get(self::tabelaDb, $mostra_por_pagina, $pagina)->result();           
        $dados['qtd'] = $this->qtdLinhas($parametros);
        
        $dados['camposLabel'] = array('matricula_usuario' => 'Matricula' , 'nome_usuario' => 'Nome', 'email_usuario' => 'E-mail', 'nome_estado' => 'Regional', 'nome_departamento' => 'Departamento');
        $dados['campos'] = array('cd_usuario', 'matricula_usuario', 'nome_usuario', 'email_usuario', 'nome_estado', 'nome_departamento');

        return $dados;
    }
    
    public function qtdLinhas($parametros = null){
        
        if($parametros){
            $post = explode('|', $parametros);
            
            foreach($post as $campoValor){
                $res = explode('=', $campoValor);
                
                if($res[1] != ''){
                    if(in_array($res[0], array('status'))){
                        $this->db->where(self::tabela.'.'.$res[0], $res[1]);
                    }else{
                        $this->db->like(self::tabela.'.'.$res[0], $res[1]);
                    }
                }
            }
        }
        
        return $this->db->get(self::tabelaDb)->num_rows(); 
        
    }
    
//------------------------ Novos Metodos ------------------------
    
    public function dadosEmail($id)
    {
        $this->db->select('nome_usuario, nome_departamento, nome, nome_estado, nome_perfil');
        $this->db->where('usuario.cd_usuario', $id);
        $this->db->join('sistema.config_usuario', 'usuario.cd_usuario = sistema.config_usuario.cd_usuario', 'left');
        $this->db->join('adminti.cargo','adminti.cargo.cd_cargo = adminti.usuario.cd_cargo');
        $this->db->join('adminti.departamento', 'adminti.departamento.cd_departamento = adminti.usuario.cd_departamento', 'left');
        $this->db->join('adminti.estado', 'adminti.estado.cd_estado = adminti.usuario.cd_estado', 'left');
        $this->db->join('sistema.perfil', 'sistema.perfil.cd_perfil = sistema.config_usuario.cd_perfil', 'left');
        

        $dados = $this->db->get('adminti.usuario')->row();
        
        return $dados;
    }
    
    
    public function retornaSistemas($cd = NULL)
    {
        if($cd == NULL)
        {
            $this->db->select('id, nome_sistema');
            $this->db->where('status','A');

            $sistemas = $this->db->get('adminti.acesso_sistema')->result_array();

            return array_column($sistemas,'id', 'nome_sistema');
        }
        else
        {
            $this->db->select('nome_sistema');
            $this->db->where('cd_usuario',$cd);
            $this->db->join('adminti.acesso_sistema', 'adminti.acesso_sistema.id = adminti.solicitacao_sistema.id_sistema', 'left');

            $sistemas = $this->db->get('adminti.solicitacao_sistema')->result_array();

            return $sistemas;
        }
    }
    
    public function retornaSolicitacoesSistema($id)
    {
        $this->db->select('id_sistema');
        $this->db->where('cd_usuario',$id);
        
        $result = $this->db->get('adminti.solicitacao_sistema')->result_array();
        
        return array_column($result, 'id_sistema');
        
    }
    
    public function retornaBeneficios($cd = NULL){
        if($cd == NULL)
        {
            $this->db->select('id, nome_beneficio');
            $this->db->where('status','A');

            $sistemas = $this->db->get('adminti.beneficio_disponivel')->result_array();

            return array_column($sistemas,'id', 'nome_beneficio');
        }
        else
        {
            $this->db->select('nome_beneficio');
            $this->db->where('cd_usuario',$cd);
            $this->db->join('adminti.beneficio_disponivel', 'adminti.beneficio_disponivel.id = adminti.beneficio_funcionario.id_beneficio', 'left');

            $sistemas = $this->db->get('adminti.beneficio_funcionario')->result_array();

            return $sistemas;
        }
    }
    
    public function retornaBeneficiosSolicitados($id)
    {
        $this->db->select('id_beneficios');
        $this->db->where('cd_usuario',$id);
        
        $result = $this->db->get('adminti.beneficio_funcionario')->result_array();
        
        return array_column($result, 'id_sistema');
        
    }
    
    
    public function geraMatricula()
    {  
            $this->db->select_max('matricula_usuario');
            $this->db->where('tipo_funcionario_usuario', $_POST['tipo_funcionario_usuario']);
            $te = $this->db->get('adminti.usuario')->row_array();
            return (1 + $te['matricula_usuario']);
    }
    
    public function retornaPerfil($permissaoExpessifica = NULL){
	
        if($permissaoExpessifica != NULL){
        
            $this->db->where("status_perfil =  'A'");
            $this->db->where("cd_perfil", $permissaoExpessifica);
            $this->db->order_by("nome_perfil", "asc");
            return $this->db->get('perfil')->result();
            
        }else{
            
            $this->db->where("status_perfil =  'A'");
            $this->db->order_by("nome_perfil", "asc");
            return $this->db->get('perfil')->result();
        }
    }
    
    public function salvaSolicitacaoSistema($cd_usuario = NULL){
        
        if($cd_usuario <> NULL){
            
            if($_POST['sistemas'] <> NULL){
                foreach ($_POST['sistemas'] as $c => $v){

                    $sistemas[] = array(
                            'cd_usuario' => $cd_usuario,
                            'id_sistema' => $v
                    );
                }
                $this->db->insert_batch('adminti.solicitacao_sistema', $sistemas);
            }
            
            
        }else{
            
            $this->db->where('cd_usuario',$this->input->post('cd_usuario'));
            $this->db->delete('adminti.solicitacao_sistema');
            
            if($_POST['sistemas'] <> NULL){
                foreach ($_POST['sistemas'] as $c => $v){
                    $sistemas[] = array(
                            'cd_usuario' => $this->input->post('cd_usuario'),
                            'id_sistema' => $v
                    );
                }
                $this->db->insert_batch('adminti.solicitacao_sistema', $sistemas);
            }
        }
    }
    
//    public function 
//    
//    ($cd_usuario = NULL){
//    
//        if($cd_usuario<>NULL){
//            
//            if($_POST['beneficios']<>NULL){
//                foreach ($_POST['beneficios'] as $c => $v){
//                    $beneficios[] = array(
//                        'cd_usuario' => $cd_usuario,
//                        'id_beneficio' => $v
//                    );
//                }
//               $this->db->insert_batch('adminti.beneficio_funcionario', $beneficios);
//            }
//        }else{
//
//            $this->db->where('cd_usuario',$this->input->post('cd_usuario'));
//            $this->db->delete('adminti.beneficio_funcionario');
//
//            if($_POST['beneficio'] <> NULL){
//                foreach ($_POST['beneficio'] as $c => $v){
//                    $beneficios[] = array(
//                        'cd_usuario' => $this->input->post('cd_usuario'),
//                        'id_beneficio' => $v
//                    );
//                }
//                $this->db->insert_batch('adminti.beneficio_funcionario', $beneficios);
//            }
//        }
//    }
    
    
    public function testaImp(){
        
        $conexao = $this->load->database('impTest',TRUE);
        
        $sql = "SELECT user,SUM(pages) as pages, date "
                . "from jobs_log where `date` like '%-06-%' GROUP BY `user`;";
        
        
       return $conexao->query($sql)->result();
        
//        $this->db->insert_batch('adminti.impressoes',$data);
                
    }

}