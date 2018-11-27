<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
error_reporting(0);
setlocale(LC_ALL, 'pt_BR.UTF-8');
class BibContrato{
    
    const idUsuRecebeDescoxaoPorStatus = 40;
    
    public function __construct(){
        $this->CI =& get_instance();
        $this->CI->load->model('tcom-contrato/contrato_model','contrato');
        $this->CI->load->model('base/dadosBanco_model','dadosBanco');
        $this->CI->load->model('base/log_model','logGeral');
        $this->CI->load->model('email/email_model','emailModel');
        $this->CI->load->library('Util', '', 'util');
    }
    
    public function emailDesconexao($idContrato){
        #$idContrato = 3247;
        $contrato = $this->CI->contrato->dadosContratoValores(md5($idContrato));
        $equipamentos = $this->CI->contrato->equipamentosAssociados($idContrato);
        $usuario = $this->CI->emailModel->usuarioEnviaEmail(self::idUsuRecebeDescoxaoPorStatus, $contrato->cd_unidade);
        
        $nomeDe = 'Sim TV - Ferramenta de negocios | Telecom - desconexão';
        $emailDe = 'naoresponda@simtv.com.br'; #SMTP SIMTV
        #$emailDe = 'sim-tv@bol.com.br';
        $titulo = "Desconexão - (".$contrato->numero.") - Por Mudança de status - Operação: ".utf8_decode($contrato->unidade);
        $para = 'equipe.sistemas@simtv.com.br';
        
        $msg = "<strong>:: CONCLUSÃO desconexão - (".$contrato->numero.") - Operação: ".utf8_decode($contrato->unidade)."</strong><br><br>";
        
        if($equipamentos){
            $msg .= "Equipamentos no local:<br>"; 
            foreach($equipamentos as $equi){
                $msg .= utf8_encode($equi->marca." ".$equi->modelo." - ".$equi->codigo." (id: ".$equi->identificacao.")<br>");
            }
            $msg .="<br>";
        }
        foreach($usuario as $usu){
            $this->CI->util->enviaEmail($nomeDe, $emailDe, $usu->email_usuario, $titulo, nl2br($msg), false);
            #$this->CI->util->enviaEmail($nomeDe, $emailDe, 'tiago.costa@simtv.com.br', $titulo, nl2br($msg), false);
        }
    }

}