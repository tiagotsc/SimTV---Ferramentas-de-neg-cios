<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

Class Beneficio_model extends CI_Model{
    
    const tabelaPassagem = 'adminti.rh_passagem';
    const tabelaValeTransporte = 'adminti.rh_vale_transporte';
    const tabelaFaltas = 'adminti.rh_faltas';
    const tabelaUnidade = 'adminti.unidade';
    const tabelaContratoAlelo = 'adminti.rh_contrato_alelo';
    const tabelaUsuario = 'adminti.usuario';
    const tabelaFerias = 'adminti.ferias';
    const tabelaRazaoSocial = 'adminti.razao_social';
    const tabelaFeriado = 'adminti.feriado';
    const tabelaConfBeneficioAlelo = 'adminti.rh_beneficio_alelo_conf';
    const tabelaBeneficioSolicitacaoAlelo = 'adminti.rh_beneficio_solicitacao_alelo';
    const tabelaBeneficioValorAlelo = 'adminti.rh_beneficio_valor_alelo';
    const tabelaLogCompraValeTransporte = 'adminti.log_compra_vale_transporte';

    /**
     * Usuario_model::__construct()
     * 
     * @return
     */
    function __construct(){
        parent::__construct();

    }
    
    //------------------ Inicio Consultas ------------------
    
    
    public function retornaPassagem($id = NULL,$cd_usuario = NULL){
        
        $cd_unidade = $this->retornaUnidadeUsuario($cd_usuario);
        
        if($id == NULL){
            $this->db->select('id, passagens, valor');
            $this->db->where('status', 'A');
            $this->db->where('cd_unidade', $cd_unidade['cd_unidade']);
            return $this->db->get(self::tabelaPassagem)->result_array();
            
        }else{
            
            $this->db->select('valor');
            $this->db->where('id', $id);
            $this->db->where('status', 'A');
            return $this->db->get(self::tabelaPassagem)->result_array();
        }
        
        
    }
    
    public function retornaDiasExtras($cd_usuario,$dataFalta){
        
        $where = "cd_usuario = ".$cd_usuario." AND data_falta = '".$dataFalta."'";
        
        $this->db->select('qdt_acressimo, qdt_descontos');
        $this->db->where($where);
        return $this->db->get(self::tabelaFaltas)->row_array();
    }
    
    public function retornaUnidadeRazaoSocial($razaoSocial){
        
        $where = "razao_social = ".$razaoSocial." AND compraBeneficio = 'S'";
        
        $this->db->select('cd_unidade,nome');
        $this->db->where($where);
        return $this->db->get(self::tabelaUnidade)->result_array();
        
    }
    
    public function retornaContratoAlelo(){
        
        $where = "tipo_beneficio = ".$_POST['opcBeneficio']." AND razao_social = ".$_POST['razaoSocial'];
        
        $this->db->select('numero_contrato');
        $this->db->where($where);
        return $this->db->get(self::tabelaContratoAlelo)->row_array();
    }
    
    public function retornaValeTransporte($cd_usuario){
            
        $this->db->select('id_passagem, passagens, valor');
        $this->db->where('cd_usuario',$cd_usuario);
        $this->db->join(self::tabelaPassagem, self::tabelaValeTransporte.'.id_passagem = '.self::tabelaPassagem.'.id', 'left');
        return $this->db->get(self::tabelaValeTransporte)->row_array();

    }
    
    public function retornaIdUsuario($matricula){
        
        $this->db->select('cd_usuario');
        $this->db->where('matricula_usuario',$matricula);
        $va = $this->db->get(self::tabelaUsuario)->row_array();
        
        return $va['cd_usuario'];
        
    }
    
    public function informacoesValeTransporte($cd){
        
        $this->db->select('valor');
        $this->db->where('cd_usuario',$cd);
        $this->db->join(self::tabelaPassagem,self::tabelaPassagem.'.id = '.self::tabelaValeTransporte.'.id_passagem','left');
        return $this->db->get(self::tabelaValeTransporte)->row();
    }
    
    public function retornaFuncionarioValeTransporte(){
        $this->db->select('matricula_usuario');
        $this->db->join(self::tabelaUsuario,self::tabelaUsuario.'.cd_usuario = '.self::tabelaValeTransporte.'.cd_usuario','left');
        return $this->db->get(self::tabelaValeTransporte)->result_array();
    }
    
    public function retornaPeriodoFeriasVale($cd_usuario, $mes){
        
        $where = "cd_usuario = ". $cd_usuario ." AND (inicio LIKE '%-".$mes."-%' OR fim like '%-".$mes."-%')" ;
        
        $this->db->select('inicio, fim');
        $this->db->where($where);
        $this->db->where('status', 'A');
        return $this->db->get(self::tabelaFerias)->row_array();
        
    }
    
    public function infoVale($cd_unidade){
        $this->db->select('matricula_usuario, '.self::tabelaUsuario.'.cd_usuario, nome_usuario, valor, cpf_usuario');
        $this->db->join(self::tabelaUsuario,self::tabelaUsuario.'.cd_usuario = '.self::tabelaValeTransporte.'.cd_usuario','left');
        $this->db->join(self::tabelaPassagem,self::tabelaPassagem.'.id = '.self::tabelaValeTransporte.'.id_passagem','left');
        $this->db->where(self::tabelaUsuario.'.cd_unidade', $cd_unidade);
        $this->db->where(self::tabelaUsuario.'.status_usuario', 'A');
        $this->db->order_by('nome_usuario', 'asc');
        return $this->db->get(self::tabelaValeTransporte)->result_array();
        
    }
    
    public function infoValeT(){
        $this->db->select('matricula_usuario, nome_usuario, valor');
        $this->db->join(self::tabelaUsuario, self::tabelaUsuario.'.cd_usuario = adminti.'.self::tabelaValeTransporte.'.cd_usuario','left');
        $this->db->join(self::tabelaPassagem, self::tabelaPassagem.'.id = '.self::tabelaValeTransporte.'.id_passagem','left');
        return $this->db->get(self::tabelaValeTransporte)->result_array();
        
    }
    
    public function retornaUnidade(){
        $this->db->select('cd_unidade, sigla, nome');
        $this->db->where('permissor !=', 'NULL');
        return $this->db->get(self::tabelaUnidade)->result();
    }
    
    public function retornaRazaoSocial(){
        $this->db->select('cd_razao_social, nome');
        return $this->db->get(self::tabelaRazaoSocial)->result_array();
    }
    
    public function retornaFeriadosUnidade($cd_unidade, $mesCompraBeneficio){        
//        $where = "cd_unidade = ". $cd_unidade ." AND data LIKE '".$mesCompraBeneficio."-%' ";
        $where = "cd_unidade = ". $cd_unidade ." AND data LIKE '".date('Y')."-".$mesCompraBeneficio."-%' ";

        $this->db->select('data, descricao');
        $this->db->where($where);
        $this->db->order_by('data', "asc"); 
        return $this->db->get(self::tabelaFeriado)->result_array();
    }
    
    public function verificaGrupoFetranspor($cd_usuario){
        
        $this->db->select('matricula_fetranspor');
        $this->db->where('cd_usuario',$cd_usuario);
        return $this->db->get(self::tabelaUsuario)->row_array();
    }
    
    public function retornaUnidadeUsuario($cd_usuario){
        $this->db->select('cd_unidade');
        $this->db->where('cd_usuario',$cd_usuario);
        return $this->db->get(self::tabelaUsuario)->row_array();
        
    }
    
    public function retornaInformacaoVt($cd_usuario){
        
        $this->db->select( self::tabelaUsuario.'.numero_vt, id_passagem');
        $this->db->join( self::tabelaValeTransporte,self::tabelaValeTransporte.'.cd_usuario = '.self::tabelaUsuario.'.cd_usuario','left');
        $this->db->where( self::tabelaUsuario.'.cd_usuario',$cd_usuario);
        return $this->db->get( self::tabelaUsuario)->row_array();
        
    }
    
    //------------------ INICIO Alelo ------------------
    public function retornaConfiguracaoAlelo(){
        return $this->db->get(self::tabelaConfBeneficioAlelo)->result_array();
    }
    
    public function retornaNumeroCartao($cd_usuario){
        
        $this->db->select("cd_usuario,conf_alelo,data_cadastro");
        $this->db->where('cd_usuario',$cd_usuario);
        return $this->db->get(self::tabelaBeneficioSolicitacaoAlelo)->row_array();
        
    }
    
    public function retornaBeneficioCompra($razaoSocial, $opcBeneficio){
        
        $this->db->select(self::tabelaUsuario.'.cd_usuario, '.self::tabelaUsuario.'.nome_usuario, '.self::tabelaUsuario.'.data_nascimento, '.self::tabelaUsuario.'.sexo, '.self::tabelaUsuario.'.matricula_usuario, '.self::tabelaUsuario.'.cd_unidade, '.self::tabelaUsuario.'.cpf_usuario, '.self::tabelaUsuario.'.elegivel_beneficio, '.self::tabelaUnidade.'.razao_social, '.self::tabelaBeneficioSolicitacaoAlelo.'.conf_alelo');
        $this->db->join(self::tabelaBeneficioSolicitacaoAlelo, self::tabelaBeneficioSolicitacaoAlelo.'.cd_usuario = '.self::tabelaUsuario.'.cd_usuario','left');
        $this->db->join( self::tabelaUnidade, self::tabelaUnidade.'.cd_unidade = '.self::tabelaUsuario.'.cd_unidade','left');
        $this->db->where("unidade.razao_social = ".$razaoSocial);
        $this->db->where("(conf_alelo = 3 OR conf_alelo = ".$opcBeneficio.")");
        return $this->db->get(self::tabelaUsuario)->result_array();
        
    }
    
    public function retornaValorVa(){
        $this->db->select('valor');
        $where = ['status'=>'A','tipo'=>'VA'];
        $this->db->where($where);
        return $this->db->get(self::tabelaBeneficioValorAlelo)->row_array();
    }
    
    public function retornaValorVr(){
        $this->db->select('valor');
        $where = ['status'=>'A','tipo'=>'VR'];
        $this->db->where($where);
        return $this->db->get(self::tabelaBeneficioValorAlelo)->row_array();
    }
    
    public function testaElegibilidadeBeneficio($cd_usuario){
        
        $this->db->select('elegivel_beneficio, cpf_usuario');
        $this->db->where('cd_usuario',$cd_usuario);
        return $this->db->get(self::tabelaUsuario)->row_array();
    }
    
    public function retornaInformacoesAlelo($cd_usuario){
        $this->db->select( 'conf_alelo,usuario.numero_va,usuario.numero_vr');
        $this->db->join( self::tabelaUsuario, self::tabelaUsuario.'.cd_usuario = '.self::tabelaBeneficioSolicitacaoAlelo.'.cd_usuario','left');
        $this->db->where( self::tabelaBeneficioSolicitacaoAlelo.'.cd_usuario',$cd_usuario);
        return $this->db->get(self::tabelaBeneficioSolicitacaoAlelo)->row_array();
    }
    
    public function retornaValorBeneficio(){
        $this->db->select('tipo, valor');
        $this->db->where('status','A');
        return $this->db->get(self::tabelaBeneficioValorAlelo)->result_array();
    }
    
    //------------------ FIM Alelo ------------------
    
    
    
    
    //------------------ Fim Consultas ------------------
    
    
    
    //------------------ Inicio Alteracoes ------------------
    
    public function valeTransporte($beneficios){
        $condicao = $this->retornaValeTransporte($beneficios['cd_usuario']);
        
        if(empty($condicao)){
            $result = $this->cadastraValeTransporte($beneficios);
        }else{
            $result = $this->atualizaValeTransporte($beneficios);
        }
    }
    
    function cadastraValeTransporte($beneficios){
        
        $this->db->trans_begin();
        
        $this->db->insert(self::tabelaValeTransporte, $beneficios);
        
        if ($this->db->trans_status() === FALSE){
        
            $this->db->trans_rollback();
        }
        else{
            
            $this->db->trans_commit();
        }
        
    }
    
    function atualizaValeTransporte($beneficios){
        
        $this->db->trans_begin(); 
        
        $this->db->where('cd_usuario',$beneficios['cd_usuario']);
        $this->db->update(self::tabelaValeTransporte, $beneficios);
        
        if ($this->db->trans_status() === FALSE){
        
            $this->db->trans_rollback();
        }
        else{
            
            $this->db->trans_commit();
        }
    }
    
    public function cadastraValeEmLote($usuarios){
        
        $this->db->trans_begin();
        
        $this->db->insert_batch(self::tabelaValeTransporte, $usuarios);
        
        if ($this->db->trans_status() === FALSE){
        
            $this->db->trans_rollback();
            return false;
        }
        else{
            
            $this->db->trans_commit();
            return true;
        }
        
    }
    
    public function logBeneficio($log,$nomeTabela){
        
        $this->db->trans_begin();
        
        $this->db->insert_batch($nomeTabela, $log);
        
        if ($this->db->trans_status() === FALSE){
        
            $this->db->trans_rollback();
            return false;
        }
        else{
            
            $this->db->trans_commit();
            return true;
        }
    }
    
    public function deletaValeTransporte($id){
        
        $this->db->trans_begin();
        
        $this->db->where('cd_usuario',$id);
        $this->db->delete(self::tabelaValeTransporte);
        
        if ($this->db->trans_status() === FALSE){
        
            $this->db->trans_rollback();
            return false;
        }
        else{
            
            $this->db->trans_commit();
            return true;
        }
    }
    
    public function salvaSolicitacaoAlelo(){
        
        //Monta o campo para incercao na tabela           
            
        $infoBeneficios = [
            'cd_usuario' => $_POST['cd_usuario'],
            'conf_alelo' => $_POST['confBeneficio'],
            'data_cadastro' => date('Y-m-d')
        ];
        
        
        /*  define o tipo de operacao que sera realizada, caso seja uma atualizacao
         *  deleta as informacoes anteriores e insere as novas informacoes como um 
         *  'insert'
         */
        $dados = $this->retornaNumeroCartao($_POST['cd_usuario']);
        
        if(empty($dados)){
            $operacao = 'insert';
        }else{
            $operacao = 'update';
            if($_POST['confBeneficio'] <> $dados['conf_alelo']){
                $this->db->delete(self::tabelaBeneficioSolicitacaoAlelo,['cd_usuario'=>$_POST['cd_usuario']]);
                $operacao = 'insert';
            }
        }
        
        // insere as informacoes no banco
        if($operacao === 'update'){
            $this->db->where('cd_usuario',$_POST['cd_usuario']);
            $this->db->$operacao( self::tabelaBeneficioSolicitacaoAlelo,$infoBeneficios);
        }else{
            $this->db->$operacao( self::tabelaBeneficioSolicitacaoAlelo,$infoBeneficios);
        }
    }
    
    
    
    //------------------ Fim Alteracoes ------------------
    

}