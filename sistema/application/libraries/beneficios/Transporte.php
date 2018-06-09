<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
error_reporting(0);
setlocale(LC_ALL, 'pt_BR.UTF-8');
class Transporte{
    
    public function __construct(){
        $this->ci =& get_instance();
        $this->ci->load->model('rh-beneficio/beneficio_model', 'beneficio');
//        $this->load->helper('transporte');
    }
            
    
    function montaCompraBeneficio($colaboradores){ 
        
        
        $compra;
        $feriados = $this->ci->beneficio->retornaFeriadosUnidade($_POST['cd_unidade'],$_POST['mesCompraBeneficio']);
        $feriados['total'] = count($feriados);
        $datas['mes'] = $_POST['mesCompraBeneficio'];
        $datas['feriados'] = $feriados;
        $diasUteisMes = $this->diasUteis($datas);
        $diasCompraPassagem = '';
        
        
        foreach($colaboradores as $colaborador){
            $cd_usuario = $this->ci->beneficio->retornaIdUsuario($colaborador['matricula_usuario']);
            $ferias = $this->ci->beneficio->retornaPeriodoFeriasVale($cd_usuario,$_POST['mesCompraBeneficio']);
            if($ferias != NULL){
                
                $datas['ferias'] = $ferias;
                
                $diasCompraPassagem = $diasUteisMes - $this->diasUteisFerias($datas);
                
            }else{
                
                $diasCompraPassagem = $diasUteisMes;
            }
            $compra[] = array(
                'matricula_usuario' => $colaborador['matricula_usuario'],
                'nome_usuario' => $colaborador['nome_usuario'],
                'valor' => $colaborador['valor'],
                'diasUteis' => $diasCompraPassagem,
                'cpf' => $colaborador['cpf_usuario']
            );
            
        }
        return $compra;
    }

    function diasUteis($datas){

        $diasUteis = 0;
        $diasUteisFeriado = 0;
        $format = 'Y-m-d';
        $i = 0;
        // Obtém o número de dias no mês 
        $dias_no_mes = cal_days_in_month(CAL_GREGORIAN, $datas['mes'], date('Y'));


        for($dia = 1; $dia <= $dias_no_mes; $dia++){

            // Obtém o timestamp
            $timestamp = mktime(0, 0, 0, $datas['mes'], $dia, date('Y'));            
            $diaTeste  = date("N", $timestamp);
            
            if($diaTeste < 6){
                
                $diasUteis++;
                if(date($format,$timestamp) == $datas['feriados'][$i]['data']){
                    $diasUteisFeriado++;
                    $i++;
                }
            }else{
                if(date($format,$timestamp) == $datas['feriados'][$i]['data']){
                    $i++;
                }
            }

        }
        
        return $diasUteis - $diasUteisFeriado;

    }
    
    function diasUteisFerias($datas){
        
        $inicio = strtotime($datas['ferias']['inicio']);
        $fim = strtotime($datas['ferias']['fim']);
        $format = 'Y-m-d';
        $dia = 86400;// 1 dia em segundos
        $diasUteis = 0;
        $diasUteisFeriado = 0;
        $i = 0;
        

        $inicioArray = explode('-',$datas['ferias']['inicio']);
        $fimArray = explode('-',$datas['ferias']['fim']);

        if($inicioArray[1] != $fimArray[1]){
            
            if($datas['mes'] == $inicioArray[1]){
                $fim = date('Y').'-'.$datas['mes'].'-'.cal_days_in_month(CAL_GREGORIAN, $datas['mes'], date('Y'));
                $fim = strtotime($fim);
            }else{
                $inicio = date('Y').'-'.$datas['mes'].'-'.'01';
                $inicio = strtotime($inicio);
            }
            
        }
        
            while(date($format,$inicio) <= date($format,$fim)){
                $diaTeste = date('N',$inicio);
                if($diaTeste < 6){
                    $diasUteis++;
                    if(date($format,$inicio) == $datas['feriados'][$i]['data']){
                        $diasUteisFeriado++;
                        $i++;  
                    }
                }else{
                    if(date($format,$inicio) == $datas['feriados'][$i]['data']){
                        $i++;
                    }
                }

                $inicio += $dia;
            }
            
            return $diasUteis - $diasUteisFeriado;

    }

    function formataPassagem($passagem){
        
        $valTmp = explode(',',floatval($passagem));
//        imprimeVetor($valTmp);

        if ($valTmp[1] == NULL){

            return str_pad($valTmp[0],(strlen($valTmp[0])+ 2),'0',STR_PAD_RIGHT);
//            echo $valFrm = str_pad($valTmp[0],(strlen($valTmp[0])+ 2),'0',STR_PAD_RIGHT);exit();

        }else{

            return $valTmp[0].str_pad($valTmp[1],2,'0',STR_PAD_RIGHT);
//            echo $valFrm = $valTmp[0].str_pad($valTmp[1],2,'0',STR_PAD_RIGHT);exit();
            
        }

    }

    

    function forcaDownloadArquivo($nomeArquivo){
        
        $this->ci->load->helper('download');

        // Baixa o arquivo

        $arquivo = './temp/valeTransporte/'.$nomeArquivo;

        $data = file_get_contents('./temp/valeTransporte/'.$nomeArquivo);
        $name = $nomeArquivo;

        force_download($name, $data);

        redirect(base_url('rh-beneficio/beneficio/compraValeTransporte'));

    }
    
    function logCompraValeTransporte(){
        
        $totalRegistros = count($_POST['matricula']);

        $i = 0;
        $dados;
        
        while($i < $totalRegistros){
            $dados[] = array(
                'cd_usuario_comprador' => $_POST['cd_usuario'],
                'data_geracao_arquivo' => $_POST['data'],
                'matricula_usuario_solicitante' => $_POST['matricula'][$i],
                'dias_uteis_mes' => $_POST['dias'][$i],
                'dias_acrescimos' => ($_POST['acrescimos'][$i] == NULL?0:$_POST['acrescimos'][$i]),
                'dias_descontos' => ($_POST['descontos'][$i] == NULL?0:$_POST['descontos'][$i]),
                'valor_passagem' => $_POST['valorPassagem'][$i]
            );
            $i++;
        }
        
        return $dados;
    }
    
    function geraArray(){
        
        $dados;
        $i = 0;
        
        if($_POST != NULL){
            while ($i < count($_POST['matricula'])){
        
                $dados['unidade'] = $_POST['regionalValue'];
                $dados['valorTotalArquivo'] += $_POST['total'][$i];
                
                $dados['matricula'][$i] = array(
                    'matricula' => $_POST['matricula'][$i],
                    'nome' => $_POST['nome'][$i],
                    'dias' => $_POST['dias'][$i],
                    'acrescimos' => intval($_POST['acrescimos'][$i]),
                    'descontos' => intval($_POST['descontos'][$i]),
                    'valorPassagem' => $this->formataPassagem($_POST['valorPassagem'][$i]),
                    'valorTotalPassagem' => $_POST['total'][$i],
                    'cpf' => $_POST['cpf'][$i],
                );

                $i++;
            }
            return $dados;
        }else{
            echo 'deu ruim';
            exit();
        }
        
    }
    
    function testaUnidade(){
        $unidade = $_POST['regionalValue'];
        
        switch ($unidade){
            
            case 1:
                $this->geraArquivoAracaju();
                break;
            
            case 2:
                $this->geraArquivoFeiraSantana();
                break;
            
            case 4:
                $this->geraArquivoNiteroi();
                break;
            
            case 6:
                $this->geraArquivoSalvador();
                break;
            
            case 8:
                $this->geraArquivoCuiaba();
                break;
            
            case 10:
                $this->geraArquivoJuizFora();
                break;
            
            case 12:
                $this->geraArquivoRecife();
                break;
            
            case 14:
                $this->geraArquivoVoltaRedonda();
                break;
            
            case 15:
                $this->geraArquivoNiteroi();
                break;
            
            default :
                break;
        }
    }
    
    
    
    //------------------------ Niteroi ------------------------
    
    function geraArquivoNiteroi(){
        
        $sequencialRegistro = '00001'; // O sequencial conta o 'header' como primeiro valor do arquivo
        $tipoRegistro = '01'; // Valor passado pela documentacao do layout
        $constanteNomeArquivo = 'PEDIDO';
        $vercaoArquivo = '01.00';
        $cnpjEmpresa = '01673744000482';
        $quebra = "\r\n";
        $dataHora = date('Ymd_Hi',time());


        // gera nome do arquivo

        $nomeArquivo = $constanteNomeArquivo.'_'.  str_replace('.', '', $vercaoArquivo).'_'.$cnpjEmpresa.'_'.$dataHora.'.txt';


        // cria o arquivo

        $file = fopen('./temp/valeTransporte/'.$nomeArquivo, 'w');
            if($file == false){
                die('nao foi possivel criar o arquivo');
            }

        // adiciona o 'header'

        $header = $sequencialRegistro.$tipoRegistro.$constanteNomeArquivo.$vercaoArquivo.$cnpjEmpresa;

        fwrite($file,$header.$quebra);

        // adiciona as linhas referentes ao corpo e o fim do arquivo
        $linhas = $this->geraRegistroNiteroi();

        foreach ($linhas as $linha){

            fwrite($file, $linha.$quebra);

        }

        fclose($file);
        
        $this->forcaDownloadArquivo($nomeArquivo);
        
    }
    
    function geraRegistroNiteroi(){

        //variaveis auxiliares

        $passTot;
        $linhas;
        $sequencialPedido = 2;
        $registro;
        $matriculaFetranspor;
                
        // constantes do layout

        $tipoRegistroItemPedido = '02';
        $tipoRegistroFimArquivo = '99';

        // Gera os itens do pedido
        $matriculas = $this->geraArray($_POST);
//        imprimeVetor($matriculas[matricula][0]);
        
//        $matriculas = $this->ci->beneficio->retornaFuncionarioValeTransporte();

        foreach($matriculas['matricula'] as $matricula){
//            echo 1;
//            imprimeVetor($matricula);
            $cd_usuario = $this->ci->beneficio->retornaIdUsuario($matricula['matricula']);
            $matriculaFetranspor = $this->ci->beneficio->verificaGrupoFetranspor($cd_usuario);
//            echo $matricula['matricula'];exit();
            
            if($matriculaFetranspor['matricula_fetranspor'] == NULL){
                $registro = $matricula['matricula'];
            }else{
                $registro = $matriculaFetranspor['matricula_fetranspor'];
            }

//            $sequencialItem = str_pad($sequencialPedido,5,0,STR_PAD_LEFT);
//            $matriForm = str_pad($registro,15,' ',STR_PAD_RIGHT);
//            $passFrm = str_pad($this->calculaValorPassagem($matricula['matricula_usuario']),8,'0',STR_PAD_LEFT);
            
            $sequencialItem = str_pad($sequencialPedido,5,0,STR_PAD_LEFT);
            $matriForm = str_pad($registro,15,' ',STR_PAD_RIGHT);
            $passFrm = str_pad($this->formataPassagem($matricula['valorTotalPassagem']),8,'0',STR_PAD_LEFT);
            
            $passTot = $passTot + $passFrm;

            $linhas[] = $sequencialItem.$tipoRegistroItemPedido.$matriForm.$passFrm;


            $sequencialPedido += 1;

        }
//        imprimeVetor($linhas);

        // Gera o fim do arquivo

        $sequencialItem = str_pad($sequencialPedido,5,0,STR_PAD_LEFT);
        $passTotFrm = str_pad($passTot,10,0,STR_PAD_LEFT);

        $linhas['fimArquivo'] = $sequencialItem.$tipoRegistroFimArquivo.$passTotFrm;

        return $linhas;
    }
    
    //------------------------ Volta Redonda ------------------------
    
    function geraArquivoVoltaRedonda(){
        
        $quebra = "\r\n";
        $regional = "VOLTA_REDONDA";
        $vercaoArquivo = '0200';
        $dataHora = date('Ymd_Hi',time());


        // gera nome do arquivo

        $nomeArquivo = $regional.'_'.$vercaoArquivo.'_'.$dataHora.'.txt';


        // cria o arquivo

        $file = fopen('./temp/valeTransporte/'.$nomeArquivo, 'w');
            if($file == false){
                die('nao foi possivel criar o arquivo');
            }

        // adiciona o 'header'

        fwrite($file,$vercaoArquivo.$quebra);

        // adiciona as linhas referentes ao corpo e o fim do arquivo
        $linhas = $this->geraRegistroVoltaRedonda();

        foreach ($linhas as $linha){

            fwrite($file, $linha.$quebra);

        }

        fclose($file);
        
        $this->forcaDownloadArquivo($nomeArquivo);
        
    }
    
    function geraRegistroVoltaRedonda(){
        
        $linhas;
        
        $usuarios = $this->geraArray($_POST);
        
        foreach($usuarios['matricula'] as $usuario){
            $cpf = str_replace(array('.','-'),"", $usuario['cpf']);
            $qdtDias = $usuario['dias'];
            $valorDiario = $usuario['valorPassagem'];
            $nomeUsuario = $usuario['nome'];
            
            $linhas[] = $cpf."|".$qdtDias."|".$valorDiario."|".$nomeUsuario;
        }
        
        return $linhas;
    }
    
    //------------------------ Cuiaba ------------------------
    
    function geraArquivoCuiaba(){
        
        $quebra = "\r\n";
        $regional = "CUIABA";
        $vercaoArquivo = '0200';
        $dataHora = date('Ymd_Hi',time());


        // gera nome do arquivo

        $nomeArquivo = $regional.'_'.$vercaoArquivo.'_'.$dataHora.'.txt';


        // cria o arquivo

        $file = fopen('./temp/valeTransporte/'.$nomeArquivo, 'w');
            if($file == false){
                die('nao foi possivel criar o arquivo');
            }

        // adiciona o 'header'

        fwrite($file,$vercaoArquivo.$quebra);

        // adiciona as linhas referentes ao corpo e o fim do arquivo
        $linhas = $this->geraRegistroCuiaba();

        foreach ($linhas as $linha){

            fwrite($file, $linha.$quebra);

        }

        fclose($file);
        
        $this->forcaDownloadArquivo($nomeArquivo);
        
    }
    
    function geraRegistroCuiaba(){
        
        $linhas;
        
        $usuarios = $this->geraArray($_POST);
        
        foreach($usuarios['matricula'] as $usuario){
            $cpf = str_replace(array('.','-'),"", $usuario['cpf']);
            $qdtDias = $usuario['dias'];
            $valorDiario = $usuario['valorPassagem'];
            $nomeUsuario = $usuario['nome'];
            
            $linhas[] = $cpf."|".$qdtDias."|".$valorDiario."|".$nomeUsuario;
        }
        
        return $linhas;
    }
    
    //------------------------ Juiz de Fora ------------------------
    
    function geraArquivoJuizFora(){
        
        $quebra = "\r\n";
        $regional = "JUIZ_FORA";
        $vercaoArquivo = '0100';
        $dataHora = date('Ymd_Hi',time());
        $dataHoraHeader = date('d/m/Y');
        $codigoPostServico = '0000000004';
        
        $header = $vercaoArquivo.$dataHoraHeader.$codigoPostServico;


        // gera nome do arquivo

        $nomeArquivo = $regional.'_'.$vercaoArquivo.'_'.$dataHora.'.txt';


        // cria o arquivo

        $file = fopen('./temp/valeTransporte/'.$nomeArquivo, 'w');
            if($file == false){
                die('nao foi possivel criar o arquivo');
            }

        // adiciona o 'header'

        fwrite($file,$header.$quebra);

        // adiciona as linhas referentes ao corpo e o fim do arquivo
        $linhas = $this->geraRegistroJuizFora();

        foreach ($linhas as $linha){

            fwrite($file, $linha.$quebra);

        }

        fclose($file);
        
        $this->forcaDownloadArquivo($nomeArquivo);
        
    }
    
    function geraRegistroJuizFora(){
        
        $usuarios = $this->geraArray($_POST);
        
        foreach($usuarios['matricula'] as $usuario){
            $cpf = str_replace(array('.','-'),"", $usuario['cpf']);
            $valorTotalPassagem = str_replace('.','',$usuario['valorTotalPassagem']);
            
            $linhas[] = 'CPF'.str_pad($cpf,25,' ',STR_PAD_RIGHT).str_pad($valorTotalPassagem,10,' ',STR_PAD_LEFT);
        }
        
        return $linhas;
    }
    
    //------------------------ RECIFE ------------------------
    
    function geraArquivoRecife(){
        
        $quebra = "\r\n";
        $regional = "RECIFE";
        $vercaoArquivo = '0200';
        $dataHora = date('Ymd_Hi',time());


        // gera nome do arquivo

        $nomeArquivo = $regional.'_'.$vercaoArquivo.'_'.$dataHora.'.txt';


        // cria o arquivo

        $file = fopen('./temp/valeTransporte/'.$nomeArquivo, 'w');
            if($file == false){
                die('nao foi possivel criar o arquivo');
            }

        // adiciona o 'header'

        fwrite($file,$vercaoArquivo.$quebra);

        // adiciona as linhas referentes ao corpo e o fim do arquivo
        $linhas = $this->geraRegistroRecife();

        foreach ($linhas as $linha){

            fwrite($file, $linha.$quebra);

        }

        fclose($file);
        
        $this->forcaDownloadArquivo($nomeArquivo);
        
    }
    
    function geraRegistroRecife(){
        
        $linhas;
        
        $usuarios = $this->geraArray($_POST);
        
        foreach($usuarios['matricula'] as $usuario){
            $cpf = str_replace(array('.','-'),"", $usuario['cpf']);
            $qdtDias = $usuario['dias'];
            $valorDiario = $usuario['valorPassagem'];
            $nomeUsuario = $usuario['nome'];
            
            $linhas[] = $cpf."|".$qdtDias."|".$valorDiario."|".$nomeUsuario;
        }
        
        return $linhas;
    }
    
    //------------------------ ARACAJU ------------------------
    
    function geraArquivoAracaju(){
        
        $quebra = "\r\n";
        $regional = "ARACAJU";
        $vercaoArquivo = '0200';
        $dataHora = date('Ymd_Hi',time());


        // gera nome do arquivo

        $nomeArquivo = $regional.'_'.$vercaoArquivo.'_'.$dataHora.'.txt';


        // cria o arquivo

        $file = fopen('./temp/valeTransporte/'.$nomeArquivo, 'w');
            if($file == false){
                die('nao foi possivel criar o arquivo');
            }

        // adiciona o 'header'

        fwrite($file,$vercaoArquivo.$quebra);

        // adiciona as linhas referentes ao corpo e o fim do arquivo
        $linhas = $this->geraRegistroAracaju();

        foreach ($linhas as $linha){

            fwrite($file, $linha.$quebra);

        }

        fclose($file);
        
        $this->forcaDownloadArquivo($nomeArquivo);
        
    }
    
    function geraRegistroAracaju(){
        
        $linhas;
        
        $usuarios = $this->geraArray($_POST);
        
        foreach($usuarios['matricula'] as $usuario){
            $cpf = str_replace(array('.','-'),"", $usuario['cpf']);
            $qdtDias = $usuario['dias'];
            $valorDiario = $usuario['valorPassagem'];
            $nomeUsuario = $usuario['nome'];
            
            $linhas[] = $cpf."|".$qdtDias."|".$valorDiario."|".$nomeUsuario;
        }
        
        return $linhas;
    }
    
    //------------------------ SALVADOR ------------------------
    
    function geraArquivoSalvador(){
        
        $quebra = "\r\n";
        $regional = "SALVADOR";
        $vercaoArquivo = '0200';
        $dataHora = date('Ymd_Hi',time());


        // gera nome do arquivo

        $nomeArquivo = $regional.'_'.$vercaoArquivo.'_'.$dataHora.'.txt';


        // cria o arquivo

        $file = fopen('./temp/valeTransporte/'.$nomeArquivo, 'w');
            if($file == false){
                die('nao foi possivel criar o arquivo');
            }

        // Nao possui 'header'


        // adiciona as linhas referentes ao corpo e o fim do arquivo
        $linhas = $this->geraRegistroSalvador();

        foreach ($linhas as $linha){

            fwrite($file, $linha.$quebra);

        }

        fclose($file);
        
        $this->forcaDownloadArquivo($nomeArquivo);
        
    }
    
    function geraRegistroSalvador(){
        
        $linhas;
        
        $usuarios = $this->geraArray($_POST);
        
        foreach($usuarios['matricula'] as $usuario){
            $matriculaUsuario = str_pad($usuario['matricula'],15,' ',STR_PAD_RIGHT);
            $valorCarga = str_pad(str_replace('.','',$usuario['valorTotalPassagem']),5,' ',STR_PAD_RIGHT);
            
            $linhas[] = $matriculaUsuario.$valorCarga;
        }
        
        return $linhas;
    }
    
    //-------------------- FEIRA DE SANTANA --------------------
    
    function geraArquivoFeiraSantana(){
        
        $quebra = "\r\n";
        $regional = "FEIRA_SANTANA";
        $vercaoArquivo = '0200';
        $dataHora = date('Ymd_Hi',time());


        // gera nome do arquivo

        $nomeArquivo = $regional.'_'.$vercaoArquivo.'_'.$dataHora.'.txt';


        // cria o arquivo

        $file = fopen('./temp/valeTransporte/'.$nomeArquivo, 'w');
            if($file == false){
                die('nao foi possivel criar o arquivo');
            }

        // Nao possui 'header'


        // adiciona as linhas referentes ao corpo e o fim do arquivo
        $linhas = $this->geraRegistroFeiraSantana();

        foreach ($linhas as $linha){

            fwrite($file, $linha.$quebra);

        }

        fclose($file);
        
        $this->forcaDownloadArquivo($nomeArquivo);
        
    }
    
    function geraRegistroFeiraSantana(){
        
        $linhas;
        
        $usuarios = $this->geraArray($_POST);
        
        foreach($usuarios['matricula'] as $usuario){
            $matriculaUsuario = str_pad($usuario['matricula'],15,' ',STR_PAD_RIGHT);
            $qdtDias = $usuario['dias'];
            
            $linhas[] = $matriculaUsuario.$qdtDias;
        }
        
        return $linhas;
    }
}


/*padrao para gerar arquivo
 * 
 *  //informacoes do arquivo

        $sequencialRegistro = '00001'; // O sequencial conta o 'header' como primeiro valor do arquivo
        $tipoRegistro = '01'; // Valor passado pela documentacao do layout
        $constanteNomeArquivo = 'PEDIDO';
        $vercaoArquivo = '01.00';
        $cnpjEmpresa = '01673744000482';
        $quebra = "\r\n";
        $dataHora = date('Ymd_Hi',time());


        // gera nome do arquivo

        $nomeArquivo = $constanteNomeArquivo.'_'.  str_replace('.', '', $vercaoArquivo).'_'.$cnpjEmpresa.'_'.$dataHora.'.txt';


        // cria o arquivo

        $file = fopen('./temp/valeTransporte/'.$nomeArquivo, 'w');
            if($file == false){
                die('nao foi possivel criar o arquivo');
            }

        // adiciona o 'header'

        $header = $sequencialRegistro.$tipoRegistro.$constanteNomeArquivo.$vercaoArquivo.$cnpjEmpresa;

        fwrite($file,$header.$quebra);

        // adiciona as linhas referentes ao corpo e o fim do arquivo
        $linhas = $this->geraRegistro();

        foreach ($linhas as $linha){

            fwrite($file, $linha.$quebra);

        }

        fclose($file);
 * 
 */