<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
error_reporting(0);
class Termo{
    
    public function __construct(){
        #$this->CI =& get_instance();
        #$this->CI->load->helper('form');
    }
    
    /**
     * Anatel::SatvaAtributo()
     * 
     * Possui os valores fixos de alguns atributos do tipo de xml
     * 
     * @return Os atributos
     */
    public function telefoniaDeclaracao($operadora = 8){
        
        # TIM
        $dados[8] = "Declaro ter recebido da SIM nesta data, para o uso exclusivo das minhas atividades, o Kit de Telefonia Móvel Corporativa, abaixo descrito. 
                    <br><br>
                    Encontro-me ciente que eventuais prejuízos causados à empresa, decorrentes do mau uso ou conservação do kit, bem como dano total, parcial, roubo ou furto, cabendo-me ressarcir a companhia. Em caso de roubo ou furto, deverá ser apresentado um Boletim de Ocorrência Policial justificando o ocorrido. 
                    <br><br>
                    Comprometo-me a guardar o bem emprestado e zelar pela adequada conservação do mesmo e devolvê-lo ao final do contrato em perfeito estado de conservação e funcionamento, salvo o desgaste decorrente do uso cotidiano. 
                    <br><br><br>                                                                            
                    Modelo: #MODELO</span>
                    <br><br>  
                    Nº LINHA TIM: #LINHA<br>
                    Serial do aparelho: #IMEI<br><br>
                    <!--Serial do Chip: #SERIAL_CHIP-->
                    Serviços:<br><br>
                    #SERVICOS<br>
                    Acessórios:<br>
                    #ACESSORIO
                    <br><br>  
                    Nome: #NOME<br> 
                    Cargo: #CARGO<br> 
                    RG: #RG<br>
                    CPF: #CPF 
                    <br><br><br><br>  
                    			
                    			
                    Niter&oacute;i, #DATA.";
        
        return $dados[$operadora];

    }
    
    # TIM
    public function telefoniaRegulamento($operadora = 8){
        
        $dados[8] = "<strong>REGULAMENTO PARA UTILIZAÇÃO DO CELULAR CORPORATIVO</strong>
                    <br><br>
                    <strong>1 - OBJETIVO:</strong>
                    <br><br>
                    Estabelecer critérios e procedimentos para a utilização do Celular Corporativo da empresa, por seus Colaboradores, visando sua otimização e minimização das despesas inerentes.
                    <br><br>
                    <strong>2 - RESPONSABILIDADES:</strong>
                    <br><br>
                    Todo usuário será responsável financeiramente pelos danos ocasionados nos celulares, advindos da inobservância das cautelas mínimas necessárias, bem como das normas contidas neste Regulamento.
                    <br><br>
                    <strong>3 - DISPOSIÇÕES GERAIS:</strong>
                    <br><br>
                    3.1 - Os celulares de propriedade da empresa, são de uso EXCLUSIVO para execução de trabalhos pertinentes às atividades da empresa, ficando expressamente proibida a sua utilização para outros fins, bem como o seu empréstimo para outros funcionários e/ou quaisquer terceiros, sem a prévia autorização formal do Gerente Operacional;
                    <br><br>
                    3.2 – Os funcionários que estão destinados ao uso do Celular Corporativo são os funcionários que exerçam funções em que estejam fora das operações por pelo menos 80% do seu tempo de trabalho ou que exerçam funções críticas para o andamento do negócio da empresa.
                    <br><br>
                    3.3 – Consumo<br>
                    #SERVICOS_DESC
                    <br><br>
                    Exemplo: Disca a Operadora (041) + DDD + Nº do celular.
                    <br><br><br><br>
                            
                            
                    Niter&oacute;i, #DATA.
                            ";
                            
        return $dados[$operadora];                            
        
    }

}