<?php
class LogBeneficio{
    
    public function __construct(){
        
        $this->ci =& get_instance();
        $this->ci->load->library('beneficios/alelo', '', 'alelo');
        $this->ci->load->library('beneficios/VT', '','transporte');
        $this->ci->load->library('beneficios/FaltaLib', '', 'faltaLib');
        $this->ci->load->model('rh-usuario/faltas_model', 'faltas');
        $this->ci->load->model('rh-beneficio/beneficio_model', 'beneficio');
        
    }
    
    const logAlelo = 'log_compra_alelo';
    const logValeTransporte = 'log_compra_vale_transporte';
    
    function logCompraValeTransporte(){
        
        $i = 0;
        $dados;
        
        foreach($_POST['colaboradores'] as $colaborador){
            $dados[] = array(
                'cd_usuario_comprador' => $this->ci->session->userdata('cd'),
                'data_geracao_arquivo' => date('Y-d-m'),
                'matricula_usuario_solicitante' => $colaborador['matricula'],
                'dias_uteis_mes' => $_POST['dias'][$i],
                'dias_acrescimos' => ($colaborador['acrescimos'] == NULL?0:$colaborador['acrescimos']),
                'dias_descontos' => ($colaborador['descontos'] == NULL?0:$colaborador['descontos']),
                'valor_passagem' => $_POST['valorPassagem'][$i]
            );
            $i++;
        }
        
        return $dados;
    }
    
    function logCompraAlelo($tabelaLog){
        
        
        $nomeTabela = 'adminti.rh_faltas_VT';
        
        $colaboradores = $this->ci->beneficio->retornaBeneficioCompra($_POST['razaoSocial'], $_POST['opcBeneficio']);
        
        foreach($colaboradores as $colaborador){
            
            $dataFalta = $_POST['mesCompraBeneficio'].'-'.date('Y');
            $cd_usuario = $this->ci->beneficio->retornaIdUsuario($colaborador['matricula_usuario']);
            
            $diasExtras = $this->ci->faltas->consultaFaltaCadastro($cd_usuario,$dataFalta, $nomeTabela);
            
            if(empty($diasExtras)){
                
                $nomeTabela = 'adminti.rh_faltas';
                
                $diasExtras = $this->ci->faltas->consultaFaltaCadastro($cd_usuario, $dataFalta, $nomeTabela);
                
            }
            
            $diasCompraBeneficio = $this->ci->faltaLib->diasUteis($colaborador['cd_unidade'], $_POST['mesCompraBeneficio'], $colaborador['matricula_usuario']);
            
            $configuracaoBeneficio = [
                'confBeneficio' => $colaborador['conf_alelo'],
                'diasUteis' => $diasCompraBeneficio,
                'elegivelBeneficio' => $colaborador['elegivel_beneficio']
            ];
            
            $compra[] = [
                'cd_usuario_comprador' => $this->ci->session->userdata('cd'),
                'data_geracao_arquivo' => date('Y-m-d H-i-s'),
                'matricula_usuario_solicitante' => $colaborador['matricula_usuario'],
                'dias_uteis_mes' => $diasCompraBeneficio,
                'dias_acrescimos' => (is_null($diasExtras['qdt_acressimo']))?0:$diasExtras['qdt_acressimo'],
                'dias_descontos' => (is_null($diasExtras['qdt_descontos']))?0:$diasExtras['qdt_descontos'],
                'valor_comprado' => $this->ci->alelo->calculaBeneficio($configuracaoBeneficio),
            ];
            
        }
        
        $this->ci->beneficio->logBeneficio($compra,$tabelaLog);
    }
    
    
    
}