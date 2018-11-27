<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
setlocale(LC_ALL, 'pt_BR.UTF-8');
class ArquivoRetorno{
	
    private $feedbackGravaArquivo = array();
    private $idTodosArquivosOk = array();
    
     /**
      * ArquivoRetorno::__construct()
      * 
      * @return
      */
     public function __construct()
    {
        #parent::Model();
        #$this->load->model('Financeiro_model','financeiro');
        #$this->SE =& get_instance();
        #$this->SE->load->library('session');        
        $this->CI =& get_instance();
        $this->CI->load->model('ArquivoCobranca_model','ArquivoCobranca');   
        $this->CI->load->model('Financeiro_model','financeiro');   
        $this->CI->load->library('Util', '', 'util');  
    }
    
    /**
     * ArquivoRetorno::validaArquivo()
     * 
     * Inicía o processo de validação do arquivo encaminhando para a sua devida configuração
     * 
     * @param mixed $arquivos Diretório do arquivo para validação
     * @return Os feedbacks e os ids dos arquivos gravados
     */
    public function validaArquivo($arquivos){
        
        #$this->CI->session->set_userdata('totalRetornos', count($arquivos));
        #$_SESSION['totalRetornos'] = count($arquivos);        
        
        #$processados = 1;
        foreach($arquivos as $arq){
            
            if(!preg_match('/Thumbs.db/', $arq)){
            
                if(preg_match('/daycoval/', $arq)){ # DAYCOVAL
                    
                    # Inicia a configuração do arquivo
                    $this->configuraArquivo(1, $arq);
                    
                }elseif(preg_match('/bb/', $arq)){ # BANCO DO BRASIL
                    
                    # Inicia a configuração do arquivo
                    $this->configuraArquivo(3, $arq);
                    
                }elseif(preg_match('/bra/', $arq)){ # BRADESCO
                    
                    # Inicia a configuração do arquivo
                    $this->configuraArquivo(4, $arq);
                    
                }elseif(preg_match('/cef/', $arq)){ # CAIXA ECONÔMICA
                    
                    # Inicia a configuração do arquivo
                    $this->configuraArquivo(5, $arq);
                    
                }elseif(preg_match('/hsbc/', $arq)){ # HSBC
                    #echo $arq; exit();
                    if(!preg_match('/\/[0-9]{10}\.(ret|RET)$/', $arq)){ # NÃO PEGAR OS ARQUIVOS DA ASSESSORIA
                    #if(strlen($arq) > 82){ # NÃO PEGAR OS ARQUIVOS DA ASSESSORIA
                        # Inicia a configuração do arquivo
                        $this->configuraArquivo(6, $arq);
                    }
                    
                }elseif(preg_match('/itau/', $arq)){ # ITAÚ
                    
                    if(!preg_match('/\/E[0-9]/', $arq)){ # NÃO PEGAR OS ARQUIVOS DE EXTRATO
                        
                        # Inicia a configuração do arquivo
                        $this->configuraArquivo(7, $arq);
                    }
                    
                }elseif(preg_match('/sant/', $arq)){ # SANTANDER
                    
                        # Inicia a configuração do arquivo
                        $this->configuraArquivo(8, $arq);
                    
                }else{
                    
                    echo 'BANCO NOVO. Informe ao administrador do sistema';
                    exit();
                    
                }
                
                #$this->CI->session->unset_userdata('retornoProcessado');            
                #$this->CI->session->set_userdata('retornoProcessado', $processados);
                            
                #echo 'aqui'; exit();            
                #echo $this->CI->session->userdata('retornoProcessado');             
                #$_SESSION['retornoProcessado'] = $processados;                        
               #$processados++; 
           
           }
        }
        
        $resultado['feedback'] = $this->feedbackGravaArquivo;
        $resultado['IdArquivos'] = $this->idTodosArquivosOk;
        return $resultado;
        
    }
    
    /**
     * ArquivoRetorno::configuraArquivo()
     * 
     * Identifica de qual banco é o arquivo montando as informações do banco
     * Filtra os arquivos através da sua nomenclatura
     * Inicia o processo de gravação gravando do nome do arquivo
     * 
     * @param mixed $banco Id do banco para carregamento das informações
     * @param mixed $arquivo Diretório do arquivo
     * 
     * @return Grava o feedback no atributo $this->feedbackGravaArquivo
     */
    public function configuraArquivo($banco, $arquivo){
        
        #echo basename($arquivo); exit();
        
        # Verifica existência do arquivo
        $existenciaArquivo =  $this->CI->ArquivoCobranca->verificaExistenciaArquivo($banco, basename($arquivo));
        
        if($existenciaArquivo == 0){ # Arquivo não existe

            # PEGA OS DADOS DO BANCO
            $dadosBanco = $this->CI->financeiro->banco($banco);
               
            if($banco == 6){ # HSBC
                
                # CARREGA OS REGISTROS DE TELECOM
                $registrosTelecom = $this->CI->ArquivoCobranca->registrosTelecom();
                
                # CRIA UM ARRAY COM OS REGISTROS DE TELECOM
                foreach($registrosTelecom as $resTel){
                    $resTelecom[] = $resTel->cod_registro_telecom;
                }
                 
            }else{
                
                $resTelecom = null;
                
            }
                
            # NOME DO ARQUIVO
            $nomeArquivoOriginal = basename($arquivo);
            
            # ARQUIVOS QUE COMEÇAM COM "D" RECEBEM A SIGLA DO BANCO
            if(preg_match('/^D/', $nomeArquivoOriginal) or preg_match('/^1/', $nomeArquivoOriginal) or preg_match('/^6/', $nomeArquivoOriginal)){
                $nomeArquivo = $dadosBanco[0]->nome_diretorio_banco.$nomeArquivoOriginal;
            }else{
                $nomeArquivo = $nomeArquivoOriginal;
            }
           
            # TIPO DE BOLETO
            if(preg_match('/^CB/', $nomeArquivoOriginal)){
                
                $tipoBoleto = 'Boletos reemitidos';
                
                # Puxa os dados de configuração do arquivo
                $configArquivo = $this->dadosPosicoesBanco($nomeArquivoOriginal, $banco);
                
                # SIGLA EMPRESA
                if(preg_match('/^CB109/', $nomeArquivoOriginal)){
                    $empresa = 'MULT';
                }elseif(preg_match('/^CB644/', $nomeArquivoOriginal)){
                    $empresa = 'TVC';
                }elseif(preg_match('/^CB115/', $nomeArquivoOriginal)){
                    $empresa = 'CABLE';
                }else{
                    $empresa = 'TVC';
                }
             # TIPO DE BOLETO   
            }elseif(preg_match('/^C/', basename($arquivo))){
                
                $tipoBoleto = 'Boletos 1ª vencimento';
                
                # Puxa os dados de configuração do arquivo
                $configArquivo = $this->dadosPosicoesBanco($nomeArquivoOriginal, $banco);
                
                # SIGLA EMPRESA
                if(preg_match('/^C109/', $nomeArquivoOriginal)){
                    $empresa = 'MULT';
                }elseif(preg_match('/^C644/', $nomeArquivoOriginal)){
                    $empresa = 'TVC';
                }elseif(preg_match('/^C115/', $nomeArquivoOriginal)){
                    $empresa = 'CABLE';
                }else{
                    $empresa = 'SEM EMPRESA DEFINIDA';
                }
             # TIPO DE BOLETO   
            }elseif(preg_match('/^D/', $nomeArquivoOriginal)){
                
                $tipoBoleto = 'DCC';
                
                # Puxa os dados de configuração do arquivo
                $configArquivo = $this->dadosPosicoesBanco($nomeArquivoOriginal, $banco);
                
                # SIGLA EMPRESA
                if(preg_match('/^D109/', $nomeArquivoOriginal)){
                    $empresa = 'MULT';
                }elseif(preg_match('/^D644/', $nomeArquivoOriginal)){
                    $empresa = 'TVC';
                }elseif(preg_match('/^D115/', $nomeArquivoOriginal)){
                    $empresa = 'CABLE';
                }else{
                    $empresa = 'SEM EMPRESA DEFINIDA';
                }
             # TIPO DE BOLETO   
            }elseif(preg_match('/^V/', $nomeArquivoOriginal)){
                
                $tipoBoleto = 'Boletos emitidos';
                
                # Puxa os dados de configuração do arquivo
                $configArquivo = $this->dadosPosicoesBanco($nomeArquivoOriginal, $banco);
                
                # SIGLA EMPRESA
                $empresa = 'TVC';
             # TIPO DE BOLETO   
            }elseif(preg_match('/^[0-9]/', $nomeArquivoOriginal)){
                
                $tipoBoleto = 'Boletos emitidos';
                
                #if(in_array($banco, array(3,6))){ #BANCO DO BRASIL e HSBC
                    #$tipoBoleto = 'DCC';
                #}
                
                # Puxa os dados de configuração do arquivo
                $configArquivo = $this->dadosPosicoesBanco($nomeArquivoOriginal, $banco);
                
                # SIGLA EMPRESA
                if(preg_match('/^109/', $nomeArquivoOriginal)){
                    $empresa = 'MULT';
                }elseif(preg_match('/^644/', $nomeArquivoOriginal)){
                    $empresa = 'TVC';
                }elseif(preg_match('/^115/', $nomeArquivoOriginal)){
                    $empresa = 'CABLE';
                }else{
                    $empresa = 'SEM EMPRESA DEFINIDA';
                }
             # TIPO DE BOLETO   
            }else{
                
                $tipoBoleto = 'Boletos emitidos';
                $empresa = 'TVC';
                
            }
            
            # INICIA A TRANSAÇÃO NO BANCO
            $this->CI->ArquivoCobranca->iniciaTrasacao();
            # GRAVA O NOME DO ARQUIVO
            $idArquivo = $this->CI->ArquivoCobranca->gravaNomeArquivoRetorno($nomeArquivo, $tipoBoleto, $empresa, $dadosBanco[0]->cd_banco);
      
            
            if($idArquivo){
                
                # Registra os ID dos arquivos gravados
                $this->idTodosArquivosOk[] = $idArquivo;
                
                # REALIZA A GRAVAÇÃO DAS LINHAS ARQUIVO
                $gravaLinhasRetorno = $this->configuraLinha($idArquivo, $arquivo, $dadosBanco, $resTelecom, $configArquivo);
                
                if($gravaLinhasRetorno){
                    #$this->feedbackGravaArquivo[] = '<div class="alert alert-success">Arquivo <strong>'.$nomeArquivoOriginal.'</strong> <br>('.$arquivo.') processado com sucesso!</div>';
                    $this->feedbackGravaArquivo[] = '<div class="alert alert-success" role="alert"><strong>'.$nomeArquivoOriginal.'</strong></div>';
                }else{
                    $this->feedbackGravaArquivo[] = '<div class="alert alert-danger" role="alert">Erro ao gravar as linhas do arquivo <strong>'.$nomeArquivoOriginal.'</strong> <br>('.$arquivo.')!</div>';
                }
                
            }else{
                
                #$this->feedbackGravaArquivo[] = '<div class="alert alert-danger">Erro ao processar o arquivo <strong>'.$nomeArquivoOriginal.'</strong> <br>('.$arquivo.')!</div>';
                $this->feedbackGravaArquivo[] = '<div class="alert alert-danger" role="alert">Erro ao processar o arquivo <strong>'.$nomeArquivoOriginal.'</strong>!</div>';
                
            }
        
        }else{
        
            // Arquivo existe
            $this->feedbackGravaArquivo[] = '<div class="alert alert-warning" role="alert"><strong>'.basename($arquivo).' n&atilde;o processado (j&aacute; existe no nosso sistema).</strong></div>';
        
        }
        #@unlink($arquivo);
        
    }
    
    /**
     * ArquivoRetorno::configuraLinha()
     * 
     * Abre o arquivo e grava as linhas
     * 
     * @param mixed $idArquivo Id do arquivo para ser gravado nas linhas
     * @param mixed $arquivo Arquivo para ser aberto
     * @param mixed $dadosBanco Dados do banco caso seja necessário
     * @param mixed $registrosTelecom Registros de telecom para serem verificados
     * @return 
     */
    public function configuraLinha($idArquivo, $arquivo, $dadosBanco, $registrosTelecom = null, $configArquivo){
        
        $handle = file($arquivo);
	    $num_linhas = count($handle);
        
        # Armazena o tipo de arquivo
        $this->tipoArquivo = '';
        $cont = 1;    
        foreach($handle as $han){
            
            $boleto = '';
            $agencia = '';
            $conta = '';
            $valorPago = '';
            
            $codInscricao = '';
            $numeroInscricao = '';
            $nossoNumero = '';
            $dataOcorrencia = '';
            $dataVencimento = '';
            $valorTitulo = '';
            $nossoNumCorresp = '';
            $codBanco = '';
            $codOcorrencia = '';
            $permissor = '';
            $cdOcorrencia = '';
            
            if($cont == 1){ # Header
                
                $dataArquivo = substr($han, $configArquivo['dataArquivo'],$configArquivo['qtdDataArquivo']);
                $this->tipoArquivo = substr($han, 81,17);
                
                # Grava as linhas válidas do arquivo
                $this->CI->ArquivoCobranca->gravaLinhasRetorno(
                                                                $idArquivo, 
                                                                $han, 
                                                                1, 
                                                                $boleto, 
                                                                $agencia, 
                                                                $conta, 
                                                                $valorPago, 
                                                                $codInscricao,
                                                                $numeroInscricao,
                                                                $nossoNumero,
                                                                $dataOcorrencia,
                                                                $dataVencimento,
                                                                $valorTitulo,
                                                                $nossoNumCorresp,
                                                                $codBanco,
                                                                $codOcorrencia,
                                                                $permissor,
                                                                $cdOcorrencia,
                                                                $cont);
            } # HEADER
            
            if($cont > 1 and $cont < $num_linhas){ # Linhas de registros
                #echo $han; echo '<br>';
                #echo intval(substr($han,$inicio,$qtd)); echo '<br>';
                                              #linha, número título, id do arquivo
                #$boleto = intval(substr($han,$inicio,$qtd));
                
                $boleto = intval(substr(intval(substr($han,$configArquivo['inicioBoleto'],$configArquivo['qtdInicioBoleto'])), 2));
                $permissor = substr(intval(substr($han,$configArquivo['inicioBoleto'],$configArquivo['qtdInicioBoleto'])), 0, 2);
                $codOcorrencia = substr($han,$configArquivo['inicioCodOcorrencia'],$configArquivo['qtdInicioCodOcorrencia']);
                
                if($dadosBanco[0]->cd_banco <> 1){ # Se diferente de Daycoval
                    $agencia = substr($han,$configArquivo['inicioAgencia'],$configArquivo['qtdInicioAgencia']);
                    $conta = trim(ltrim(substr($han,$configArquivo['inicioConta'],$configArquivo['qtdInicioConta']), '0'));
                }
                #$agencia = $configArquivo['agencia'];
                
                /*if($dadosBanco[0]->cd_banco <> 1){ # Se diferente de Daycoval
                    $conta = trim(ltrim(substr($han,$configArquivo['inicioConta'],$configArquivo['qtdInicioConta']), '0'));
                }*/
                
                if($dadosBanco[0]->cd_banco == 1){
                    $agencia = $configArquivo['agencia'];
                    $conta = $configArquivo['conta'];
                }
                
                #$conta = $configArquivo['conta'];
                $valorPago = $this->CI->util->formataValor(substr($han,$configArquivo['inicioValorPago'],$configArquivo['qtdInicioValorPago']));
                
                if($this->tipoArquivo <> 'DEBITO AUTOMATICO'){
                
                    $codInscricao = substr($han,$configArquivo['codInscricao'],$configArquivo['qtdCodInscricao']);
                    $numeroInscricao = substr($han,$configArquivo['numeroInscricao'],$configArquivo['qtdNumeroInscricao']);
                    $nossoNumero = intval(substr($han,$configArquivo['nossoNumero'],$configArquivo['qtdNossoNumero']));
                    $dataOcorrencia = substr($han,$configArquivo['dataOcorrencia'],$configArquivo['qtdDataVencimento']);
                    $dataVencimento = substr($han,$configArquivo['dataVencimento'],$configArquivo['qtdDataVencimento']);
                    $valorTitulo = $this->CI->util->formataValor(substr($han,$configArquivo['valorTitulo'],$configArquivo['qtdValorTitulo']));
                    
                    if($dadosBanco[0]->cd_banco == 1){ # Se for Daycoval
                        #$nossoNumCorresp = intval(substr($han,$configArquivo['nossoNumCorresp'],$configArquivo['qtdNossoNumCorresp']));
                        $nossoNumCorresp = trim(ltrim(substr($han,$configArquivo['nossoNumCorresp'],$configArquivo['qtdNossoNumCorresp']), '0'));
                    }
                    
                    $codBanco = substr($han,$configArquivo['codigoBanco'],$configArquivo['qtdCodigoBanco']);
                    
                    # Cd da ocorrência
                    $cdOcorrencia = $this->CI->ArquivoCobranca->cdOcorrenciaRetorno($codOcorrencia, $dadosBanco[0]->cd_banco, 'NORMAL');
                    #echo 'aqui-'.$cdOcorrencia.'|';
                
                }else{
                    
                    $agencia = $configArquivo['agencia'];
                    $conta = $configArquivo['conta'];
                    
                    $codInscricao = '';
                    $numeroInscricao = '';
                    $nossoNumero = '';
                    $dataOcorrencia = '';
                    $dataVencimento = '';
                    $valorTitulo = '';
                    $nossoNumCorresp = '';
                    $codBanco = '';
                    #$codOcorrencia = substr($han,$configArquivo['inicioCodOcorrencia'],$configArquivo['qtdInicioCodOcorrencia']);
                    #$permissor = '';
                    $cdOcorrencia = $this->CI->ArquivoCobranca->cdOcorrenciaRetorno($codOcorrencia, $dadosBanco[0]->cd_banco, 'DEBAUTO');
                    #echo 'aqui2'.$codOcorrencia.'|';
                }
                
                # SE NÃO EXISTIR LINHA "J" (CORRETO)
                if(substr($han, 0, 1) <> 'J'){             
    
                    if($registrosTelecom){
                        
                        # SE O NÚMERO NA POSIÇÃO 63 NÃO ESTIVER PRESENTE NO ARRAY DE REGISTROS DE TELECOM (CORRETO)
                        if(!in_array(substr($han, 62, 3), $registrosTelecom)){
                            
                            if(!empty($boleto)  and $boleto <> '0'){ # Registro com o "Nosso número"
                                
                                # Grava as linhas válidas do arquivo
                                $this->CI->ArquivoCobranca->gravaLinhasRetorno(
                                                                                $idArquivo, 
                                                                                $han, 
                                                                                2, 
                                                                                $boleto, 
                                                                                $agencia, 
                                                                                $conta, 
                                                                                $valorPago, 
                                                                                $codInscricao,
                                                                                $numeroInscricao,
                                                                                $nossoNumero,
                                                                                $dataOcorrencia,
                                                                                $dataVencimento,
                                                                                $valorTitulo,
                                                                                $nossoNumCorresp,
                                                                                $codBanco,
                                                                                $codOcorrencia,
                                                                                $permissor,
                                                                                $cdOcorrencia,
                                                                                $cont);
                            }else{ # Registro sem o nosso "Número"
                            
                                # Grava as linhas válidas do arquivo
                                /*$this->CI->ArquivoCobranca->gravaLinhasRetorno(
                                                                                $idArquivo, 
                                                                                $han, 
                                                                                3, 
                                                                                $boleto, 
                                                                                $agencia, 
                                                                                $conta, 
                                                                                $valorPago, 
                                                                                $codInscricao,
                                                                                $numeroInscricao,
                                                                                $nossoNumero,
                                                                                $dataOcorrencia,
                                                                                $dataVencimento,
                                                                                $valorTitulo,
                                                                                $nossoNumCorresp,
                                                                                $codBanco,
                                                                                $codOcorrencia,
                                                                                $permissor,
                                                                                $cdOcorrencia,
                                                                                $cont);*/
                                                                                
                                $tipoErro = 'SEM NOSSO NÚMERO';
                                                                                
                                $this->CI->ArquivoCobranca->gravaLinhasRemovidasRetorno($idArquivo, $han, $tipoErro, $cont);                                                    
                            } 
                             
                        }else{ # GRAVA A LINHA DE REGISTROS DE TELECOM NA TABELA DE LINHAS EXCLUIDAS
                            
                            $tipoErro = 'REGISTRO TELECOM';
                            
                            # Grava as linhas inválidas do arquivo 
                            $this->CI->ArquivoCobranca->gravaLinhasRemovidasRetorno($idArquivo, $han, $tipoErro, $cont);
                            
                        }
                        
                    }else{
                    
                        if(!empty($boleto) and $boleto <> '0'){ # Registro com o "Nosso número"
                        
                            # Grava as linhas válidas do arquivo
                            $this->CI->ArquivoCobranca->gravaLinhasRetorno(
                                                                            $idArquivo, 
                                                                            $han, 
                                                                            2, 
                                                                            $boleto, 
                                                                            $agencia, 
                                                                            $conta, 
                                                                            $valorPago, 
                                                                            $codInscricao,
                                                                            $numeroInscricao,
                                                                            $nossoNumero,
                                                                            $dataOcorrencia,
                                                                            $dataVencimento,
                                                                            $valorTitulo,
                                                                            $nossoNumCorresp,
                                                                            $codBanco,
                                                                            $codOcorrencia,
                                                                            $permissor,
                                                                            $cdOcorrencia,
                                                                            $cont);
                        }else{ # Registro sem o nosso "Número"
                            $boleto = '';
                            # Grava as linhas válidas do arquivo
                            /*$this->CI->ArquivoCobranca->gravaLinhasRetorno(
                                                                            $idArquivo, 
                                                                            $han, 
                                                                            3, 
                                                                            $boleto, 
                                                                            $agencia, 
                                                                            $conta, 
                                                                            $valorPago, 
                                                                            $codInscricao,
                                                                            $numeroInscricao,
                                                                            $nossoNumero,
                                                                            $dataOcorrencia,
                                                                            $dataVencimento,
                                                                            $valorTitulo,
                                                                            $nossoNumCorresp,
                                                                            $codBanco,
                                                                            $codOcorrencia,
                                                                            $permissor,
                                                                            $cdOcorrencia,
                                                                            $cont);*/
                                                                            
                            $tipoErro = 'SEM NOSSO NÚMERO';                                                
                                                                            
                            $this->CI->ArquivoCobranca->gravaLinhasRemovidasRetorno($idArquivo, $han, $tipoErro, $cont);
                        }  
                    
                    }
                
                }else{# GRAVA A LINHA "J" NA TABELA DE LINHAS EXCLUIDAS
                    
                    $tipoErro = 'LINHA J';
                    
                    # Grava as linhas válidas do arquivo
                    $this->CI->ArquivoCobranca->gravaLinhasRemovidasRetorno($idArquivo, $han, $tipoErro, $cont);
                    
                }
                                          
            }
            
            if($cont == $num_linhas){ # Footer
            
                $boleto = '';
                $agencia = '';
                $conta = '';
                $valorPago = '';
                
                $codInscricao = '';
                $numeroInscricao = '';
                $nossoNumero = '';
                $dataOcorrencia = '';
                $dataVencimento = '';
                $valorTitulo = '';
                $nossoNumCorresp = '';
                $codBanco = '';
                $codOcorrencia = '';
                $permissor = '';
                $cdOcorrencia = '';
                
                # Grava as linhas válidas do arquivo                                
                $this->CI->ArquivoCobranca->gravaLinhasRetorno(
                                                                $idArquivo, 
                                                                $han, 
                                                                4, 
                                                                $boleto, 
                                                                $agencia, 
                                                                $conta, 
                                                                $valorPago, 
                                                                $codInscricao,
                                                                $numeroInscricao,
                                                                $nossoNumero,
                                                                $dataOcorrencia,
                                                                $dataVencimento,
                                                                $valorTitulo,
                                                                $nossoNumCorresp,
                                                                $codBanco,
                                                                $codOcorrencia,
                                                                $permissor,
                                                                $cdOcorrencia,
                                                                $cont);
            }
            
            $cont++;
            
        } // Fecha foreach
        
        # Limpa o tipo arquivo
        $this->tipoArquivo = '';

        # Registra a data do arquivo
        $this->CI->ArquivoCobranca->registraDadosHeaderArquivo($idArquivo, $dataArquivo);
        
        # FINALIZA A TRANSAÇÃO NO BANCO DE DADOS
        if($this->CI->ArquivoCobranca->finalizaTransacao()){
            return true;
        }else{
            return false;
        }

    }
    
    /**
     * ArquivoRetorno::criaArquivosValidados()
     * 
     * Cria os arquivos validados e hospeda no diretório na rede
     * 
     * @param mixed $idArquivos
     * @return
     */
    public function criaArquivosValidados($idArquivos){
        
        if($idArquivos){
        
            #$dir = PASTA_SISTEMA.'RETORNO_RESULTADO/';
            $dir = PASTA_REDE_SISTEMA.'faturamento/retorno_resultado/'; 

            foreach($idArquivos as $idArquivo){
                
                # PEGA OS DADOS DO BANCO
                $dadosArquivo = $this->CI->ArquivoCobranca->dadosArquivoRetorno($idArquivo);
                
                # PEGA O CONTEÚDO DO ARQUIVO
                $conteudoArquivo = $this->CI->ArquivoCobranca->conteudoArquivoRetorno($idArquivo);
                
                if(count($conteudoArquivo) > 2){
                    
                    $pasta = $dadosArquivo[0]->nome_diretorio_banco;
        
                    if(!is_dir($dir.$pasta)){
                        #echo $dir.$pasta; exit();
                        mkdir($dir.$pasta, 777);
                    }
                    
                    $arquivo = $dadosArquivo[0]->nome_arquivo_retorno;
            		
            		$in = fopen($dir.$pasta.'/'.$arquivo,"w+");
            		
                    #echo $dir.$pasta.'/'.$arquivo; echo '<br>';
                    foreach($conteudoArquivo as $contArq){
                        fwrite( $in, trim($contArq->linha_arquivo_retorno)); 
                        fwrite( $in, "\r\n" ); # Quebra linha 
                    }
                    
                    fclose($in);
                    
                    $novosArquivos[] = '<div class="alert alert-success" role="alert"><strong>'.$dir.$pasta.'/'.$arquivo.'</strong></div>';
                    
                    
                }else{
                
                    $arquivo = $dadosArquivo[0]->nome_arquivo_retorno;
                    $novosArquivos[] = '<div class="alert alert-warning" role="alert"><strong>'.$arquivo.' (Arquivo sem linha - N&atilde;o criado)</strong></div>';
                }
            }
            
            return $novosArquivos;
        
        }
        
    }
    
    /**
     * ArquivoRetorno::dadosPosicoesBanco()
     * 
     * Responsável por retornar as configurações de posições das informações para cada arquivo de acordo com o tipo de banco
     * 
     * @param mixed $inicioNomeArquivo Informa a nomenclatura inicial do arquivo para filtrar a configuração
     * @param mixed $banco Informa a qual banco pertence o arquivo para realizar a filtragem
     * @return a configuração do arquivo / banco
     */
    public function dadosPosicoesBanco($inicioNomeArquivo, $banco){
        
        if(preg_match('/^V/', $inicioNomeArquivo) and $banco == 1) # DAYCOVAL
        {
            $dados['agencia'] = '01'; #Agência
            #$dados['conta'] = '713783-6'; #Conta
            $dados['conta'] = '7137836'; #Conta
            
            $dados['inicioBoleto'] = 37; # Posição do boleto
            $dados['qtdInicioBoleto'] = 25; # Qtd. de posições do boleto
            
            $dados['inicioValorPago'] = 253; # Posição do valor pago
            $dados['qtdInicioValorPago'] = 13; # Qtd. posição do valor pago
            
            $dados['inicioAgencia'] = 168; # Posição da agência
            $dados['qtdInicioAgencia'] = 4; # Qtd. posição agência
            
            $dados['inicioConta'] = ''; # Posição da conta corrente
            $dados['qtdInicioConta'] = ''; # Qtd. posição conta corrente
            
            $dados['dataArquivo'] = 94; # Data do arquivo
            $dados['qtdDataArquivo'] = 6; # Qtd. data do arquivo
            
            $dados['inicioCodOcorrencia'] = 108; # Posição da ocorrência (Código que informa se o boleto foi pago ou não)
            $dados['qtdInicioCodOcorrencia'] = 2; # Qtd. posição da ocorrência
            #$dados['codigosBaixa'] = array('06'); # Códigos que informa que os boletos foram baixados com sucesso
            
            $dados['codInscricao'] = 1; # Identifica se o assinante é físico ou juridico
            $dados['qtdCodInscricao'] = 2; # Qtd. posição código inscrição
            
            $dados['numeroInscricao'] = 3; # Número da inscrição (CPF ou CNPJ)
            $dados['qtdNumeroInscricao'] = 14; # Qtd. posição número inscrição
            
            $dados['nossoNumero'] = 85; # Identifica o título no banco | liga o arquivo de remessa (Com DV)
            $dados['qtdNossoNumero'] = 9; # Qtd. posição nosso número
            
            $dados['dataOcorrencia'] = 110; # Data da ocorrência
            $dados['qtdDataOcorrencia'] = 6; # Qtd. data ocorrência
            
            $dados['dataVencimento'] = 146; # Data de vencimento do título
            $dados['qtdDataVencimento'] = 6; # Qtd. data de vencimento do título
            
            $dados['valorTitulo'] = 152; # Valor do título
            $dados['qtdValorTitulo'] = 13; # Qtd. posição valor do título
            
            $dados['nossoNumCorresp'] = 94; # Só se for cobrado em correspondente
            $dados['qtdNossoNumCorresp'] = 13; # Qtd. posição Nosso num corresp
            
            $dados['codigoBanco'] = 165; # Código do banco da onde o boleto foi pago
            $dados['qtdCodigoBanco'] = 3; # Qtd. posição código banco
            
        }
        elseif((preg_match('/^[0-9]/', $inicioNomeArquivo) or preg_match('/^D[0-9]/', $inicioNomeArquivo)) and $banco == 3) # BANCO DO BRASIL (DÉBITO AUTOMÁTICO)
        {
            if(preg_match('/^1[0]{1}9/', $inicioNomeArquivo) or preg_match('/^D1[0]{1}9/', $inicioNomeArquivo))
            {
                #$dados['agencia'] = '3336-7'; #Agência
                $dados['agencia'] = '33367'; #Agência
                #$dados['conta'] = '21019-6'; #Conta
                $dados['conta'] = '210196'; #Conta    
            }elseif(preg_match('/^115/', $inicioNomeArquivo) or preg_match('/^D115/', $inicioNomeArquivo))
            {
                #$dados['agencia'] = '3336-7'; #Agência
                $dados['agencia'] = '33367'; #Agência
                #$dados['conta'] = '30878-1'; #Conta
                $dados['conta'] = '308781'; #Conta
            }else{
                #$dados['agencia'] = '3336-7'; #Agência
                $dados['agencia'] = '33367'; #Agência
                #$dados['conta'] = '185002-4'; #Conta
                $dados['conta'] = '1850024'; #Conta
            }
            
            $dados['dataArquivo'] = 65; # Data do arquivo
            $dados['qtdDataArquivo'] = 8; # Qtd. data do arquivo
            
            $dados['inicioBoleto'] = 69; # Posição do boleto
            $dados['qtdInicioBoleto'] = 11; # Qtd. de posições do boleto
            
            $dados['inicioValorPago'] = 52; # Posição do valor pago
            $dados['qtdInicioValorPago'] = 15; # Qtd. posição do valor pago
            
            $dados['inicioAgencia'] = 26; # Posição da agência
            $dados['qtdInicioAgencia'] = 4; # Qtd. posição agência
            
            $dados['inicioConta'] = 30; # Posição da conta corrente
            $dados['qtdInicioConta'] = 14; # Qtd. posição conta corrente
            
            $dados['inicioCodOcorrencia'] = 67; # Posição da ocorrência (Código que informa se o boleto foi pago ou não)
            $dados['qtdInicioCodOcorrencia'] = 2; # Qtd. posição da ocorrência
            #$dados['codigosBaixa'] = array('00', '31'); # Códigos que informa que os boletos foram baixados com sucesso
            
        }
        elseif(preg_match('/^CB/', $inicioNomeArquivo) and $banco == 4) # BRADESCO
        {
            if(preg_match('/^CB1[0]{1}9/', $inicioNomeArquivo))
            {
                #$dados['agencia'] = '3391-0'; #Agência
                $dados['agencia'] = '3391'; #Agência
                #$dados['conta'] = '59000-2'; #Conta  
                $dados['conta'] = '590002'; #Conta  
            }elseif(preg_match('/^CB115/', $inicioNomeArquivo))
            {
                #$dados['agencia'] = '3391-0'; #Agência
                $dados['agencia'] = '3391'; #Agência
                #$dados['conta'] = '62000-9'; #Conta
                $dados['conta'] = '620009'; #Conta
            }else{
                #$dados['agencia'] = '3391-0'; #Agência
                $dados['agencia'] = '3391'; #Agência
                #$dados['conta'] = '57500-3'; #Conta
                $dados['conta'] = '575003'; #Conta
            }
            
            $dados['dataArquivo'] = 94; # Data do arquivo
            $dados['qtdDataArquivo'] = 6; # Qtd. data do arquivo
            
            $dados['inicioBoleto'] = 70; # Posição do boleto
            $dados['qtdInicioBoleto'] = 11; # Qtd. de posições do boleto
            
            $dados['inicioValorPago'] = 253; # Posição do valor pago
            $dados['qtdInicioValorPago'] = 13; # Qtd. posição do valor pago
            
            $dados['inicioAgencia'] = 24; # Posição da agência
            $dados['qtdInicioAgencia'] = 5; # Qtd. posição agência
            
            $dados['inicioConta'] = 31; # Posição da conta corrente
            $dados['qtdInicioConta'] = 6; # Qtd. posição conta corrente
            
            $dados['inicioCodOcorrencia'] = 108; # Posição da ocorrência (Código que informa se o boleto foi pago ou não)
            $dados['qtdInicioCodOcorrencia'] = 2; # Qtd. posição da ocorrência  
            #$dados['codigosBaixa'] = array(); # Códigos que informa que os boletos foram baixados com sucesso 
            
            $dados['codInscricao'] = 1; # Identifica se o assinante é físico ou juridico
            $dados['qtdCodInscricao'] = 2; # Qtd. posição código inscrição
            
            $dados['numeroInscricao'] = 3; # Número da inscrição (CPF ou CNPJ)
            $dados['qtdNumeroInscricao'] = 14; # Qtd. posição número inscrição
            
            $dados['nossoNumero'] = 70; # Identifica o título no banco | liga o arquivo de remessa (Com DV)
            $dados['qtdNossoNumero'] = 12; # Qtd. posição nosso número
            
            $dados['dataOcorrencia'] = 110; # Data da ocorrência
            $dados['qtdDataOcorrencia'] = 6; # Qtd. data ocorrência
            
            $dados['dataVencimento'] = 146; # Data de vencimento do título
            $dados['qtdDataVencimento'] = 6; # Qtd. data de vencimento do título
            
            $dados['valorTitulo'] = 152; # Valor do título
            $dados['qtdValorTitulo'] = 13; # Qtd. posição valor do título
            
            $dados['nossoNumCorresp'] = ''; # Só se for cobrado em correspondente
            $dados['qtdNossoNumCorresp'] = ''; # Qtd. posição Nosso num corresp
            
            $dados['codigoBanco'] = 165; # Código do banco da onde o boleto foi pago
            $dados['qtdCodigoBanco'] = 3; # Qtd. posição código banco
        }
        elseif(preg_match('/^C/', $inicioNomeArquivo) and $banco == 4) # BRADESCO
        {
            if(preg_match('/^C1[0]{1}9/', $inicioNomeArquivo))
            {
                #$dados['agencia'] = '3391-0'; #Agência
                $dados['agencia'] = '3391'; #Agência
                #$dados['conta'] = '59000-2'; #Conta  
                $dados['conta'] = '590002'; #Conta
            }elseif(preg_match('/^C115/', $inicioNomeArquivo))
            {
                #$dados['agencia'] = '3391-0'; #Agência
                $dados['agencia'] = '3391'; #Agência
                #$dados['conta'] = '62000-9'; #Conta
                $dados['conta'] = '620009'; #Conta
            }else{
                #$dados['agencia'] = '3391-0'; #Agência
                $dados['agencia'] = '3391'; #Agência
                #$dados['conta'] = '57500-3'; #Conta
                $dados['conta'] = '575003'; #Conta
            }
            
            $dados['dataArquivo'] = 94; # Data do arquivo
            $dados['qtdDataArquivo'] = 6; # Qtd. data do arquivo
            
            $dados['inicioBoleto'] = 70; # Posição do boleto
            $dados['qtdInicioBoleto'] = 11; # Qtd. de posições do boleto
            
            $dados['inicioValorPago'] = 253; # Posição do valor pago
            $dados['qtdInicioValorPago'] = 13; # Qtd. posição do valor pago
            
            $dados['inicioAgencia'] = 24; # Posição da agência
            $dados['qtdInicioAgencia'] = 5; # Qtd. posição agência
            
            $dados['inicioConta'] = 31; # Posição da conta corrente
            $dados['qtdInicioConta'] = 6; # Qtd. posição conta corrente
            
            $dados['inicioCodOcorrencia'] = 108; # Posição da ocorrência (Código que informa se o boleto foi pago ou não)
            $dados['qtdInicioCodOcorrencia'] = 2; # Qtd. posição da ocorrência
            #$dados['codigosBaixa'] = array(); # Códigos que informa que os boletos foram baixados com sucesso
            
            $dados['codInscricao'] = 1; # Identifica se o assinante é físico ou juridico
            $dados['qtdCodInscricao'] = 2; # Qtd. posição código inscrição
            
            $dados['numeroInscricao'] = 3; # Número da inscrição (CPF ou CNPJ)
            $dados['qtdNumeroInscricao'] = 14; # Qtd. posição número inscrição
            
            $dados['nossoNumero'] = 70; # Identifica o título no banco | liga o arquivo de remessa (Com DV)
            $dados['qtdNossoNumero'] = 12; # Qtd. posição nosso número
            
            $dados['dataOcorrencia'] = 110; # Data da ocorrência
            $dados['qtdDataOcorrencia'] = 6; # Qtd. data ocorrência
            
            $dados['dataVencimento'] = 146; # Data de vencimento do título
            $dados['qtdDataVencimento'] = 6; # Qtd. data de vencimento do título
            
            $dados['valorTitulo'] = 152; # Valor do título
            $dados['qtdValorTitulo'] = 13; # Qtd. posição valor do título
            
            $dados['nossoNumCorresp'] = ''; # Só se for cobrado em correspondente
            $dados['qtdNossoNumCorresp'] = ''; # Qtd. posição Nosso num corresp
            
            $dados['codigoBanco'] = 165; # Código do banco da onde o boleto foi pago
            $dados['qtdCodigoBanco'] = 3; # Qtd. posição código banco
        }
        elseif(preg_match('/^D/', $inicioNomeArquivo) and $banco == 4) # BRADESCO (DÉBITO AUTOMÁTICO)
        {
            if(preg_match('/^D1[0]{1}9/', $inicioNomeArquivo))
            {
                #$dados['agencia'] = '3391-0'; #Agência
                $dados['agencia'] = '3391'; #Agência
                #$dados['conta'] = '59000-2'; #Conta 
                $dados['conta'] = '590002'; #Conta  
            }elseif(preg_match('/^D115/', $inicioNomeArquivo))
            {
                #$dados['agencia'] = '3391-0'; #Agência
                $dados['agencia'] = '3391'; #Agência
                #$dados['conta'] = '62000-9'; #Conta
                $dados['conta'] = '620009'; #Conta
            }else{
                #$dados['agencia'] = '3391-0'; #Agência
                $dados['agencia'] = '3391'; #Agência
                #$dados['conta'] = '57500-3'; #Conta
                $dados['conta'] = '575003'; #Conta
            }
            
            $dados['dataArquivo'] = 65; # Data do arquivo
            $dados['qtdDataArquivo'] = 8; # Qtd. data do arquivo
            
            $dados['inicioBoleto'] = 69; # Posição do boleto
            $dados['qtdInicioBoleto'] = 11; # Qtd. de posições do boleto
            
            $dados['inicioValorPago'] = 52; # Posição do valor pago
            $dados['qtdInicioValorPago'] = 15; # Qtd. posição do valor pago
            
            $dados['inicioAgencia'] = 26; # Posição da agência
            $dados['qtdInicioAgencia'] = 4; # Qtd. posição agência
            
            $dados['inicioConta'] = 30; # Posição da conta corrente
            $dados['qtdInicioConta'] = 14; # Qtd. posição conta corrente
            
            $dados['inicioCodOcorrencia'] = 67; # Posição da ocorrência (Código que informa se o boleto foi pago ou não)
            $dados['qtdInicioCodOcorrencia'] = 2; # Qtd. posição da ocorrência
            #$dados['codigosBaixa'] = array('00', '31'); # Códigos que informa que os boletos foram baixados com sucesso

        }
        elseif(preg_match('/^D/', $inicioNomeArquivo) and $banco == 5) # CAIXA ECONÔMICA FEDERAL (DÉBITO AUTOMÁTICO)
        {
            if(preg_match('/^D1[0]{1}9/', $inicioNomeArquivo))
            {
                $dados['agencia'] = '2295'; #Agência
                #$dados['conta'] = '03000000430-3'; #Conta  
                $dados['conta'] = '030000004303'; #Conta 
            }elseif(preg_match('/^D115/', $inicioNomeArquivo))
            {
                $dados['agencia'] = '1360'; #Agência
                #$dados['conta'] = '00300000057-0'; #Conta
                $dados['conta'] = '003000000570'; #Conta
            }else{
                $dados['agencia'] = '1360'; #Agência
                #$dados['conta'] = '00300000055-3'; #Conta
                $dados['conta'] = '003000000553'; #Conta
            }
            
            $dados['dataArquivo'] = 65; # Data do arquivo
            $dados['qtdDataArquivo'] = 8; # Qtd. data do arquivo
            
            $dados['inicioBoleto'] = 69; # Posição do boleto
            $dados['qtdInicioBoleto'] = 11; # Qtd. de posições do boleto
            
            $dados['inicioValorPago'] = 52; # Posição do valor pago
            $dados['qtdInicioValorPago'] = 15; # Qtd. posição do valor pago
            
            $dados['inicioAgencia'] = 26; # Posição da agência
            $dados['qtdInicioAgencia'] = 4; # Qtd. posição agência
            
            $dados['inicioConta'] = 30; # Posição da conta corrente
            $dados['qtdInicioConta'] = 14; # Qtd. posição conta corrente
            
            $dados['inicioCodOcorrencia'] = 67; # Posição da ocorrência (Código que informa se o boleto foi pago ou não)
            $dados['qtdInicioCodOcorrencia'] = 2; # Qtd. posição da ocorrência
            #$dados['codigosBaixa'] = array('00', '31'); # Códigos que informa que os boletos foram baixados com sucesso
            
        }
        elseif(preg_match('/^D/', $inicioNomeArquivo) and $banco == 6) # HSBC (DÉBITO AUTOMÁTICO)
        {
            if(preg_match('/^D115/', $inicioNomeArquivo))
            {
                $dados['agencia'] = '0516'; #Agência
                #$dados['conta'] = '13174-81'; #Conta
                $dados['conta'] = '1317481'; #Conta
            }else{
                $dados['agencia'] = '0516'; #Agência
                #$dados['conta'] = '13176-35'; #Conta
                $dados['conta'] = '1317635'; #Conta
            }
            
            $dados['dataArquivo'] = 65; # Data do arquivo
            $dados['qtdDataArquivo'] = 8; # Qtd. data do arquivo
            
            $dados['inicioBoleto'] = 69; # Posição do boleto
            $dados['qtdInicioBoleto'] = 11; # Qtd. de posições do boleto
            
            $dados['inicioValorPago'] = 52; # Posição do valor pago
            $dados['qtdInicioValorPago'] = 15; # Qtd. posição do valor pago
            
            $dados['inicioAgencia'] = 26; # Posição da agência
            $dados['qtdInicioAgencia'] = 4; # Qtd. posição agência
            
            $dados['inicioConta'] = 30; # Posição da conta corrente
            $dados['qtdInicioConta'] = 14; # Qtd. posição conta corrente
            
            $dados['inicioCodOcorrencia'] = 67; # Posição da ocorrência (Código que informa se o boleto foi pago ou não)
            $dados['qtdInicioCodOcorrencia'] = 2; # Qtd. posição da ocorrência
            #$dados['codigosBaixa'] = array('00', '31'); # Códigos que informa que os boletos foram baixados com sucesso
        
        }
        elseif(preg_match('/^[0-9]/', $inicioNomeArquivo) and $banco == 6) # HSBC
        {
            if(preg_match('/^115/', $inicioNomeArquivo))
            {
                $dados['agencia'] = '0516'; #Agência
                #$dados['conta'] = '13174-81'; #Conta
                $dados['conta'] = '1317481'; #Conta
            }else{
                $dados['agencia'] = '0516'; #Agência
                #$dados['conta'] = '13176-35'; #Conta
                $dados['conta'] = '1317635'; #Conta
            }
            
            $dados['dataArquivo'] = 94; # Data do arquivo
            $dados['qtdDataArquivo'] = 6; # Qtd. data do arquivo
            
            $dados['inicioBoleto'] = 37; # Posição do boleto
            $dados['qtdInicioBoleto'] = 11; # Qtd. de posições do boleto
            
            $dados['inicioValorPago'] = 253; # Posição do valor pago
            $dados['qtdInicioValorPago'] = 13; # Qtd. posição do valor pago
            
            $dados['inicioAgencia'] = 24; # Posição da agência
            $dados['qtdInicioAgencia'] = 4; # Qtd. posição agência
            
            $dados['inicioConta'] = 28; # Posição da conta corrente
            $dados['qtdInicioConta'] = 7; # Qtd. posição conta corrente
            
            $dados['inicioCodOcorrencia'] = 108; # Posição da ocorrência (Código que informa se o boleto foi pago ou não)
            $dados['qtdInicioCodOcorrencia'] = 2; # Qtd. posição da ocorrência
            #$dados['codigosBaixa'] = array(); # Códigos que informa que os boletos foram baixados com sucesso
            
            $dados['codInscricao'] = 1; # Identifica se o assinante é físico ou juridico
            $dados['qtdCodInscricao'] = 2; # Qtd. posição código inscrição
            
            $dados['numeroInscricao'] = 3; # Número da inscrição (CPF ou CNPJ)
            $dados['qtdNumeroInscricao'] = 14; # Qtd. posição número inscrição
            
            $dados['nossoNumero'] = 62; # Identifica o título no banco | liga o arquivo de remessa (Com DV)
            $dados['qtdNossoNumero'] = 11; # Qtd. posição nosso número
            
            $dados['dataOcorrencia'] = 110; # Data da ocorrência
            $dados['qtdDataOcorrencia'] = 6; # Qtd. data ocorrência
            
            $dados['dataVencimento'] = 146; # Data de vencimento do título
            $dados['qtdDataVencimento'] = 6; # Qtd. data de vencimento do título
            
            $dados['valorTitulo'] = 152; # Valor do título
            $dados['qtdValorTitulo'] = 13; # Qtd. posição valor do título
            
            $dados['nossoNumCorresp'] = ''; # Só se for cobrado em correspondente
            $dados['qtdNossoNumCorresp'] = ''; # Qtd. posição Nosso num corresp
            
            $dados['codigoBanco'] = 165; # Código do banco da onde o boleto foi pago
            $dados['qtdCodigoBanco'] = 3; # Qtd. posição código banco
        }
        elseif(preg_match('/^D/', $inicioNomeArquivo) and $banco == 7) # ITAÚ (DÉBITO AUTOMÁTICO)
        {
            if(preg_match('/^D115/', $inicioNomeArquivo))
            {
                $dados['agencia'] = '1546'; #Agência
                #$dados['conta'] = '45231-7'; #Conta
                $dados['conta'] = '452317'; #Conta
            }else{
                $dados['agencia'] = '1546'; #Agência
                #$dados['conta'] = '45230-9'; #Conta
                $dados['conta'] = '452309'; #Conta
            }
            
            $dados['dataArquivo'] = 65; # Data do arquivo
            $dados['qtdDataArquivo'] = 8; # Qtd. data do arquivo
            
            $dados['inicioBoleto'] = 69; # Posição do boleto
            $dados['qtdInicioBoleto'] = 11; # Qtd. de posições do boleto
            
            $dados['inicioValorPago'] = 52; # Posição do valor pago
            $dados['qtdInicioValorPago'] = 15; # Qtd. posição do valor pago
            
            $dados['inicioAgencia'] = 26; # Posição da agência
            $dados['qtdInicioAgencia'] = 4; # Qtd. posição agência
            
            $dados['inicioConta'] = 38; # Posição da conta corrente
            $dados['qtdInicioConta'] = 5; # Qtd. posição conta corrente
            
            $dados['inicioCodOcorrencia'] = 67; # Posição da ocorrência (Código que informa se o boleto foi pago ou não)
            $dados['qtdInicioCodOcorrencia'] = 2; # Qtd. posição da ocorrência
            #$dados['codigosBaixa'] = array('00', '31'); # Códigos que informa que os boletos foram baixados com sucesso
            
        }
        elseif(preg_match('/^D/', $inicioNomeArquivo) and $banco == 8) # SANTANDER (DÉBITO AUTOMÁTICO)
        {
            if(preg_match('/^D115/', $inicioNomeArquivo))
            {
                $dados['agencia'] = '3689'; #Agência
                #$dados['conta'] = '13000912-4'; #Conta
                $dados['conta'] = '130009124'; #Conta
            }else{
                $dados['agencia'] = '3689'; #Agência
                #$dados['conta'] = '13000232-3'; #Conta
                $dados['conta'] = '130002323'; #Conta
            }
            
            $dados['dataArquivo'] = 65; # Data do arquivo
            $dados['qtdDataArquivo'] = 8; # Qtd. data do arquivo
            
            $dados['inicioBoleto'] = 69; # Posição do boleto
            $dados['qtdInicioBoleto'] = 11; # Qtd. de posições do boleto
            
            $dados['inicioValorPago'] = 52; # Posição do valor pago
            $dados['qtdInicioValorPago'] = 15; # Qtd. posição do valor pago
            
            $dados['inicioAgencia'] = 26; # Posição da agência
            $dados['qtdInicioAgencia'] = 4; # Qtd. posição agência
            
            $dados['inicioConta'] = 30; # Posição da conta corrente
            $dados['qtdInicioConta'] = 14; # Qtd. posição conta corrente
            
            $dados['inicioCodOcorrencia'] = 67; # Posição da ocorrência (Código que informa se o boleto foi pago ou não)
            $dados['qtdInicioCodOcorrencia'] = 2; # Qtd. posição da ocorrência
            #$dados['codigosBaixa'] = array('00', '31'); # Códigos que informa que os boletos foram baixados com sucesso
            
        }
        
        return $dados;
        
    }
    
	/**
	 * ArquivoRetorno::formaValorBanco()
	 * 
	 * @param mixed $valor
	 * @return
	 */
	/*public function formaValorBanco($valor){
		
		if(empty($valor)){
			$valor = 'null';
		}elseif(preg_match('/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/', $valor)){ # DATA
			$valor = "'".$this->formataData($valor, 'USA')."'";
		 }elseif(preg_match('/^[0-9]+[.,]{1}[0-9]{2}$/',$valor)){ # NUMÉRICO (PONTO FLUTUANTE)
			$valor = "'".preg_replace('/,/', '.', $valor)."'";
		 }elseif(preg_match('/^[0-9]+$/', $valor)){ # INTEIRO
			$valor = $valor;
		 }else{ # STRING
			 $valor = "'".$valor."'";
		 }
		
		return $valor;
	}*/

}