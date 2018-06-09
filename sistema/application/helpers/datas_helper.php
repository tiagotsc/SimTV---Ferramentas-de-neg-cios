<?php 
/*
function enviaEmail($nomeDe = 'Sim TV - Ferramentas de Negócios', $emailDe = 'equipe.sistemas@simtv.com.br', $emailPara = '', $titulo = '', $texto = '', $anexo = false){
    
    $res =& get_instance();
    $res->load->library("My_phpmailer", '', 'phpMailer');
    
    $mail = $res->phpMailer->inicializar();
    
    $mail->From = $emailDe; // Remetente
    $mail->FromName = $nomeDe; // Remetente nome

    $mail->IsHTML(true);

    $mail->Subject = $titulo; // assunto
    $mail->Body = $texto; // Mensagem
    $mail->AddAddress($emailPara,''); // Email e nome do destino
    if($anexo){
        $mail->AddAttachment('./temp/'.$anexo.'.pdf', $anexo.'.pdf'  );
    }
    if($mail->Send()){
        if($anexo){
            apagaArquivo('./temp/'.$anexo.'.pdf');
        }
        return true;
    }else{
        #echo "Erro: " . $mail->ErrorInfo;
        return false;
    }
    
}
*/

function proximoDiaUtil($data){ # 2016-04-06 
    
    $diaSemana = date('N', strtotime(date($data)));
    
    if($diaSemana == 6){ # Sábado
        $data = date('Y-m-d', strtotime("+2 days",strtotime($data)));
    }
    
    if($diaSemana == 7){ # Domingo
        $data = date('Y-m-d', strtotime("+1 days",strtotime($data)));
    }
    
    return $data;
    
}