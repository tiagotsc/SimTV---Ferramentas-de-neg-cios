<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class My_PHPMailer {
    
    //MY_ para informar ao framework de que se trata de uma classe customizada, ou seja, não faz parte do framework. Pode ser alterada no arquivo config.php em application/config/
    public function My_PHPMailer() {
        require_once(APPPATH.'/third_party/PHPMailer/PHPMailerAutoload.php');
    }
    
    public function inicializar(){
        
        $mail = new PHPMailer();
        #$mail->SMTPDebug = true;
        $mail->IsSMTP(); 
        $mail->SMTPAuth = true;
        
        $mail->Host = 'mail.simtv.com.br';# Host
        $mail->Port = '587'; // seleciona a porta de envio
        $mail->SMTPSecure = 'tls'; // seleciona a porta de envio	
        $mail->Username = 'naoresponda@simtv.com.br'; //Email para smtp autenticado
        $mail->Password = 'Na0R&$P0nda'; // Senha
        
        #$mail->ConfirmReadingTo = 'ti@simtv.com.br'; Confirmação de leitura
        
        /*
        $mail->Host = 'smtps.bol.com.br';# Host
        $mail->Port = '587'; // seleciona a porta de envio
        $mail->SMTPSecure = 'tls'; // seleciona a porta de envio	
        $mail->Username = 'sim-tv@bol.com.br'; //Email para smtp autenticado
        $mail->Password = 'qaz12345'; // Senha
        */
        
        return $mail;
        
    }
    
    public function testeEnvio(){
        
        $mail = $this->inicializar(); 
        
        $mail->SetFrom('naoresponda@simtv.com.br', 'SIM TV'); //Quem está enviando o e-mail.
        #$mail->AddReplyTo("response@email.com","Nome Completo"); //Para que a resposta será enviada.
        $mail->Subject = "Assunto"; //Assunto do e-mail.
        $mail->Body = "Corpo do e-mail em HTML.\nLegal";
        #$mail->AltBody = "Corpo em texto puro.";
        $destino = "tiago.costa@simtv.com.br";
        $mail->AddAddress($destino, "Tiago Silva Costa");
         
        /*Também é possível adicionar anexos.*/
        #$mail->AddAttachment("images/phpmailer.gif");
        #$mail->AddAttachment("images/phpmailer_mini.gif");
     
        if(!$mail->Send()) {
            echo "ocorreu um erro durante o envio: " . $mail->ErrorInfo;
        } else {
            echo "Mensagem enviada com sucesso!";
        }
        
        exit();
        
    }
    
}
?>