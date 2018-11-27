<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
error_reporting(0);
setlocale(LC_ALL, 'pt_BR.UTF-8');

class alelo{
    
    const nomeTabela = 'adminti.rh_faltas_alelo';
    
    public function __construct(){
        $this->ci =& get_instance();
        $this->ci->load->model('rh-usuario/faltas_model', 'faltas');
        $this->ci->load->library('beneficios/FaltaLib', '', 'faltaLib');
        $this->ci->load->model('rh-beneficio/beneficio_model', 'beneficio');
    }
    
    //Funcoes Auxiliares
    
    
    function forcaDownloadArquivo($nomeArquivo){
        
        $this->ci->load->helper('download');

        // Baixa o arquivo

        $arquivo = './temp/valeTransporte/'.$nomeArquivo;

        $data = file_get_contents('./temp/valeTransporte/'.$nomeArquivo);
        $name = $nomeArquivo;

        force_download($name, $data);

        redirect(base_url('rh-beneficio/beneficio/compraValeTransporte'));

    }
    
    function geraArray(){
        
        $dados;
        $i = 0;
        
        if($_POST != NULL){
            while ($i < count($_POST['matricula'])){
        
                $dados['unidade'] = $_POST['regionalValue'];
                $dados['valorTotalArquivo'] += $_POST['total'][$i];
                
                $dados['matricula'][$i] = [
                    'matricula' => $_POST['matricula'][$i],
                    'nome' => $_POST['nome'][$i],
                    'dias' => $_POST['dias'][$i],
                    'acrescimos' => intval($_POST['acrescimos'][$i]),
                    'descontos' => intval($_POST['descontos'][$i]),
                    'data_nascimento' => $_POST['data_nascimento'][$i],
                    'sexo' => $_POST['sexo'][$i],
                    'valorBeneficio' => $_POST['total'][$i],
                    'cpf' => $_POST['cpf'][$i],
                    
                ];

                $i++;
            }
            return $dados;
        }else{
            echo 'Nenhuma informacao foi recebida';
            exit();
        }
        
    }
    
    // Inicio das funcoes para geracao do arquivo para compra dos beneficios Alelo
    
    
    /*
     * $configuracaoBeneficio é um array composto pela configuracao do beneficio¹,
     * a quantidade de dias que seram comprados o beneficio e o uma marcação para
     * determinar se o colaborador é ou nao elegivel ao beneficio de vale alimentacao.
     * Abaixo um exemplo do array:
     * 
     * $configuracaoBeneficio[
     *      confBeneficio => 2,
     *      diasUteis => 20,
     *      diasExtra => [
     *          id_falta => 255,
     *          qdt_acressimo => 2,
     *          qdt_descontos => 1
     *      ]
     *      elegivelBeneficio => S
     * ]
     * 
     * ¹ Por determinacao do RH, caso um funcionario ganhe acima de R$X, ele nao
     * tem direito ao valor do Vale Alimentacao, este ainda pode solicitar que
     * o valor recebido para almoco(Vale Refeicao) seja direcionado para o
     * Vale Alimentacao.
     */
    function calculaBeneficio($configuracaoBeneficio){
        
        
        $valorVa = intval(array_pop($this->ci->beneficio->retornaValorVa()));
        $valorVr = intval(array_pop($this->ci->beneficio->retornaValorVr()));
        $valorCompra = $valorVr * ( $configuracaoBeneficio['diasUteis'] + ( $configuracaoBeneficio['diasExtra']['qdt_acressimo'] - $configuracaoBeneficio['diasExtra']['qdt_descontos'] ) );
        
        switch ($configuracaoBeneficio['confBeneficio']){
            case "1":
            case "2":
                return ( $configuracaoBeneficio['elegivelBeneficio'] == 'S' )?( $valorCompra + $valorVa ):$valorCompra;
                break;
            case "3":
                return($_POST['opcBeneficio'] == 1)?$valorCompra:$valorVa;
                break;
        }
        
    }
    
    public function montaCompraBeneficio($colaboradores){
        
        $compra;

        $diasCompraBeneficio = '';
        
        
        
        foreach($colaboradores as $colaborador){
            
            $dataFalta = $_POST['mesCompraBeneficio'].'-'.date('Y');
            $cd_usuario = $this->ci->beneficio->retornaIdUsuario($colaborador['matricula_usuario']);
            
            //nao mecher
            $diasExtras = $this->ci->faltas->consultaFaltaCadastro($cd_usuario,$dataFalta, self::nomeTabela);
            
            if(empty($diasExtras)){
                
                $nomeTabela = 'adminti.rh_faltas';
                
                $diasExtras = $this->ci->faltas->consultaFaltaCadastro($cd_usuario, $dataFalta, $nomeTabela);
                
            }
            
            //nao mecher
            
            $diasCompraBeneficio = $this->ci->faltaLib->diasUteis($colaborador['cd_unidade'], $_POST['mesCompraBeneficio'], $colaborador['matricula_usuario']);
            
            $configuracaoBeneficio = [
                'confBeneficio' => $colaborador['conf_alelo'],
                'diasUteis' => $diasCompraBeneficio,
                'diasExtra' => $diasExtras,
                'elegivelBeneficio' => $colaborador['elegivel_beneficio']
            ];
            
            $compra[] = [
                'matricula_usuario' => $colaborador['matricula_usuario'],
                'nome_usuario' => $colaborador['nome_usuario'],
                'data_nascimento' => date('d/m/Y', strtotime($colaborador['data_nascimento']) ),
                'sexo' => $colaborador['sexo'],
                'valor' => $this->calculaBeneficio($configuracaoBeneficio),
                'opcBeneficio' => $colaborador['conf_alelo'],
                'diasUteis' => $diasCompraBeneficio,
                'qdt_acressimo' => (is_null($diasExtras['qdt_acressimo']))?0:$diasExtras['qdt_acressimo'],
                'qdt_descontos' => (is_null($diasExtras['qdt_descontos']))?0:$diasExtras['qdt_descontos'],
                'elegivelBeneficio' => $colaborador['elegivel_beneficio'],
                'cpf' => $colaborador['cpf_usuario'],
                'unidade' => $colaborador['cd_unidade']
            ];
            
        }
        
        return $compra;
    }
    
    public function geraArquivo(){
        
        $sequencialRegistro = '00001'; // O sequencial conta o 'header' como primeiro valor do arquivo
        $tipoRegistro = '01'; // Valor passado pela documentacao do layout
        $constanteNomeArquivo = 'PEDIDO';
        $vercaoArquivo = '01.00';
        $cnpjEmpresa = '01673744000482';
        $quebra = "\r\n";
        $dataHora = date('Ymd_Hi',time());
        
        //
        $tvcidade = 482;
        $cableBahia = 115;
        $multicabo = 109;
        
        $numeroContrato = $this->ci->beneficio->retornaContratoAlelo();
        
        // gera nome do arquivo

        $nomeArquivo = $constanteNomeArquivo.'_'.  str_replace('.', '', $vercaoArquivo).'_'.$cnpjEmpresa.'_'.$dataHora.'.txt';


        // cria o arquivo

        $file = fopen('./temp/valeTransporte/'.$nomeArquivo, 'w');
            if($file == false){
                die('nao foi possivel criar o arquivo');
            }

        // adiciona o 'header'
//        $headers[] = "%NUMERO CONTRATO%";
//        $headers[] = "%".$numeroContrato['numero_contrato']."%";
//        $headers[] = "NOME DO USUARIO;CPF;DATA DE NASCIMENTO;CODIGO DE SEXO; VALOR ;TIPO DE LOCAL ENTREGA;LOCAL DE ENTREGA;MATRICULA";
//        
//        foreach($headers as $header){
//            fwrite($file,$header.$quebra);
//        }

        // adiciona as linhas referentes ao corpo e o fim do arquivo
        $linhas = $this->geraArquivoCompra();

        foreach ($linhas as $linha){

            fwrite($file, $linha.$quebra);

        }

        fclose($file);
        
        $this->forcaDownloadArquivo($nomeArquivo);
        
    }
    
    function geraArquivoCompra(){
        
        //variaveis auxiliares

        $passTot;
        $linhas;
        $sequencialPedido = 2;
        $registro;
        $matriculaFetranspor;
        $localEntrega;
        
        // constantes do layout

        $tipoRegistroItemPedido = '02';
        $tipoRegistroFimArquivo = '99';
        
        switch ($_POST['razaoSocial']){
            case 1:
                $localEntrega = 482;
                break;
            case 2:
                $localEntrega = 115;
                break;
            case 3:
                $localEntrega = 109;
                break;
        }
        
        // Gera os itens do pedido
        $infos = $this->montaCompraBeneficio($this->ci->beneficio->retornaBeneficioCompra($_POST['razaoSocial'], $_POST['opcBeneficio']));

        foreach($infos as $info){

            $linhas[] = "%;".$info['nome_usuario'].';'.$info['cpf'].';'.$info['data_nascimento'].';'.$info['sexo'].';'.$info['valor'].';'."FI".';'.$localEntrega.';'.$info['matricula_usuario'].";%";

            $sequencialPedido += 1;

        }

        return $linhas;
        
    }
}