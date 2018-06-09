<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
error_reporting(0);
class Xml{
    
    public function __construct(){
        $this->CI =& get_instance();
        $this->CI->load->helper('form');
    }
    
	/**
	 * Xml::satva()
	 * 
     * Gera o xml do sistema SATVA
     * 
	 * @param $dados Dados para geração do xml
	 * @return
	 */
	public function satva($dados){
	   
        $atributo = $this->SatvaAtributo();
        
        $tag = $this->SatvaTag();
        
		$dom = new DomDocument('1.0', 'ISO-8859-1');
        
        // Elemento Raiz ROOT
        $root = $dom->appendChild($dom->createElement('root'));
        
        ##### Elemento UPLOAD - Inicio #####
        $upload = $dom->createElement($tag['pai']);
        // Adiciona o UPLOAD dentro do ROOT
        $root->appendChild($upload);
        
        // Adiciona atributo TIPO
        $tipo = $dom->createAttribute($tag['tipo']);
        $tipo->appendChild($dom->createTextNode($atributo['tipo']));
        $upload->appendChild($tipo);
        
        // Adiciona atributo AnoReferecia
        $anoRef = $dom->createAttribute($tag['ano']);
        $anoRef->appendChild($dom->createTextNode($dados['itens'][0]->ano));
        $upload->appendChild($anoRef);
        
        // Adiciona atributo MesReferecia
        $mesRef = $dom->createAttribute($tag['mes']);
        $mesRef->appendChild($dom->createTextNode((int)$dados['itens'][0]->mes));
        $upload->appendChild($mesRef);
        
        // Adiciona atributo IdServico
        $idServico = $dom->createAttribute($tag['servico']);
        $idServico->appendChild($dom->createTextNode($dados['itens'][0]->id_servico));
        $upload->appendChild($idServico);
        
        // Adiciona atributo IdOperadora
        $IdOperadora = $dom->createAttribute($tag['operadora']);
        $IdOperadora->appendChild($dom->createTextNode($dados['itens'][0]->id_operadora));
        $upload->appendChild($IdOperadora);
        ##### Elemento UPLOAD - Fim #####
        
        foreach($dados['ids'] as $id){
            
            ##### Elemento IDENTIFICACAO - Início #####
            $identificacao = $dom->createElement($tag['id']);
            // Adiciona o IDENTIFICACAO dentro do UPLOAD
            $upload->appendChild($identificacao);
        
            // Adiciona atributo IdCepAps
            $IdCepAps = $dom->createAttribute($tag['cep']);
            $IdCepAps->appendChild($dom->createTextNode($id->id_cep_aps));
            $identificacao->appendChild($IdCepAps);
            
            if($tag['plano']){ # Banda Larga ou Planos oferecidos
            
                // Adiciona atributo PossuiBandaLarga
                $PossuiProduto = $dom->createAttribute($tag['plano']);
                $PossuiProduto->appendChild($dom->createTextNode('S'));
                $identificacao->appendChild($PossuiProduto);
                ##### Elemento IDENTIFICACAO - Fim #####
            
            }
            
            foreach($dados['itens'] as $item){
            
                if($id->id_cep_aps == $item->id_cep_aps){
                    
                    $valor = $this->organizaDados($item);
                    
                    ##### Elemento PLANO - Início #####
                    $filho = $dom->createElement($tag['filho']);
                    // Adiciona o IDENTIFICACAO dentro do UPLOAD
                    $identificacao->appendChild($filho);
                    
                    // Adiciona atributo 1
                    if($valor['atr1'] !== false){ 
                        $atr1 = $dom->createAttribute($tag['filho-atr1']);
                        $atr1->appendChild($dom->createTextNode($valor['atr1']));
                        $filho->appendChild($atr1);
                    }
                    
                    // Adiciona atributo 2
                    if($valor['atr2'] !== false){ 
                        $atr1 = $dom->createAttribute($tag['filho-atr2']);
                        $atr1->appendChild($dom->createTextNode($valor['atr2']));
                        $filho->appendChild($atr1);
                    }
                    
                    // Adiciona atributo 3
                    if($valor['atr3'] !== false){
                        
                        # Tratamento caso seja valoradesao
                        #$valor['atr3'] = ($tag['filho-atr3'] == 'valoradesao')? '': $tag['filho-atr3']; 
                    
                        $atr1 = $dom->createAttribute($tag['filho-atr3']);
                        $atr1->appendChild($dom->createTextNode($valor['atr3']));
                        $filho->appendChild($atr1);
                    }
                    
                    // Adiciona atributo 4
                    if($valor['atr4'] !== false){
                        $atr1 = $dom->createAttribute($tag['filho-atr4']);
                        $atr1->appendChild($dom->createTextNode($valor['atr4']));
                        $filho->appendChild($atr1);
                    }
                    
                    // Adiciona atributo 5
                    if($valor['atr5'] !== false){
                        $atr1 = $dom->createAttribute($tag['filho-atr5']);
                        $atr1->appendChild($dom->createTextNode($valor['atr5']));
                        $filho->appendChild($atr1);
                    }
                    
                    // Adiciona atributo 6
                    if($valor['atr6'] !== false){
                        $atr1 = $dom->createAttribute($tag['filho-atr6']);
                        $atr1->appendChild($dom->createTextNode($valor['atr6']));
                        $filho->appendChild($atr1);
                    }
                    
                    // Adiciona atributo 7
                    if($valor['atr7'] !== false){
                        $atr1 = $dom->createAttribute($tag['filho-atr7']);
                        $atr1->appendChild($dom->createTextNode($valor['atr7']));
                        $filho->appendChild($atr1);
                    }
                    
                    // Adiciona atributo 8
                    if($valor['atr8'] !== false){
                        $atr1 = $dom->createAttribute($tag['filho-atr8']);
                        $atr1->appendChild($dom->createTextNode($valor['atr8']));
                        $filho->appendChild($atr1);
                    }
                    
                    // Adiciona atributo 9
                    if($valor['atr9'] !== false){
                        $atr1 = $dom->createAttribute($tag['filho-atr9']);
                        $atr1->appendChild($dom->createTextNode($valor['atr9']));
                        $filho->appendChild($atr1);
                    }
                    
                } # If id = id
            } # Foreach $item
        
        }
        /*
        ** insert more nodes
        */
        $dom->formatOutput = true; // set the formatOutput attribute of domDocument to true
    
        // SALVA XML EM PASTA DO SERVIDOR
        /*$test1 = $dom->saveXML(); // put string in test1
        $dom->save('xml/anatel/test1.xml'); // save as file*/
        
        // Força do download do XML
        #header('Content-disposition: attachment; filename=indicador_'.$dados['itens'][0]->id_operadora.'_'.date('d-m-Y__H-i-s').'.xml');
        header('Content-disposition: attachment; filename='.$dados['itens'][0]->tipo_xml.$dados['itens'][0]->nome_operadora.'_'.$dados['itens'][0]->mes.'_'.$dados['itens'][0]->ano.'_'.date('s').'.xml');
        header ("Content-Type:text/xml"); 
        //output the XML data
        echo $dom->saveXML();
         // if you want to directly download then set expires time
        header("Expires: 0");
        
	}
    
    /**
     * Anatel::organizaDados()
     * 
     * Formata e organiza os dos recebidos
     * 
     * @param $dados Dados que vão ser organizados
     * 
     * @return Os dados formatados
     */
    public function organizaDados($dados){
        
        switch($this->CI->input->post('tipo_xml')){
            
            case 1: #Planos oferecidos
                $resposta = explode(',', $dados->resposta); 
                $res['atr1'] = ($resposta[0] == '')? '': $resposta[0]; # Nome do plano
                $res['atr2'] = ($resposta[1] == '')? '': $resposta[1]; # Qtd. canais
                $res['atr3'] = ($resposta[2] == '')? '0,00': number_format($resposta[2], 2, ',', ''); # Valor adesão
                $res['atr4'] = ($resposta[3] == '')? '': number_format($resposta[3], 2, ',', ''); # Valor instalação
                $res['atr5'] = ($resposta[4] == '')? '': number_format($resposta[4], 2, ',', ''); # Valor mensalidade
                $res['atr6'] = ($resposta[5] == '')? '': $resposta[5]; # Status
                $res['atr7'] = false;
                $res['atr8'] = false;
                $res['atr9'] = false;
            break;
            case 3: # Banda larga
                $resposta = explode(',', $dados->resposta);
                $res['atr1'] = ($dados->nome_servico == '')? '': $dados->nome_servico; # Nome do serviço
                $res['atr2'] = ($dados->nome_pacote == '')? '': $dados->nome_pacote; # Nome do pacote
                $res['atr3'] = ($dados->tecnologia == '')? '': $dados->tecnologia; # Tecnologia
                $res['atr4'] = ($dados->inicio_servico == '')? '': $dados->inicio_servico; # Data início atividade
                $res['atr5'] = ($resposta[0] == '')? '': $resposta[0].'kbps'; # Velocidade do menor pacote
                $res['atr6'] = ($resposta[1] == '')? '': str_replace(".", ",", $resposta[1]); # Mensalidade do menor pacote
                $res['atr7'] = ($resposta[2] == '')? '': $resposta[2].'kbps'; # Velocidade do maior pacote
                $res['atr8'] = ($resposta[3] == '')? '': str_replace(".", ",", $resposta[3]); # Mensalidade do maior pacote
                $res['atr9'] = ($resposta[4] == '')? '0': $resposta[4]; # Quantidade de assinantes
            break;
            case 5: # Indicadores de qualidae
                $res['atr1'] = $dados->sigla; # Sigla
                $res['atr2'] = ($dados->resposta == '')? '': $dados->resposta; # Valor
                $res['atr3'] = false;
                $res['atr4'] = false;
                $res['atr5'] = false;
                $res['atr6'] = false;
                $res['atr7'] = false;
                $res['atr8'] = false;
                $res['atr9'] = false;
            break;
            
        }
        return $res;
    }
    
    /**
     * Anatel::SatvaTag()
     * 
     * Possui as tags do tipo de xml
     * 
     * @return As tags
     */
    public function SatvaTag(){
        
        $dados['pai']          = 'UploadSATVA';
        $dados['tipo']         = 'Tipo';
        $dados['ano']          = 'AnoReferencia';
        $dados['mes']          = 'MesReferencia';
        $dados['servico']      = 'IdServico';
        $dados['operadora']    = 'IdOperadora';
        $dados['id']           = 'Identificacao';
        $dados['cep']          = 'IdCepAps';
        
        switch($this->CI->input->post('tipo_xml')){
            case 1: # Planos oferecidos
                $dados['plano']        = 'PossuiPlano';
                $dados['filho']        = 'Plano';
                $dados['filho-atr1']   = 'nome';
                $dados['filho-atr2']   = 'qtdcanais';
                $dados['filho-atr3']   = 'valoradesao';
                $dados['filho-atr4']   = 'valorinstalacao';
                $dados['filho-atr5']   = 'valormensalidade';
                $dados['filho-atr6']   = 'status';
            break;
            case 3: # Banda larga
                $dados['plano']        = 'PossuiBandaLarga';
                $dados['filho']        = 'BandaLarga';
                $dados['filho-atr1']   = 'Nomeservico';
                $dados['filho-atr2']   = 'Nomepacote';
                $dados['filho-atr3']   = 'Tecnologia';
                $dados['filho-atr4']   = 'Datainicioatividade';
                $dados['filho-atr5']   = 'Velocidademenorpacote';
                $dados['filho-atr6']   = 'Mensalidademenorpacote';
                $dados['filho-atr7']   = 'Velocidademaiorpacote';
                $dados['filho-atr8']   = 'Mensalidademaiorpacote';
                $dados['filho-atr9']   = 'numeroassinantes';
            break;
            case 5: # Indicadores de qualidade
            #case 0:
            #case 2:
                $dados['filho']        = 'Indicador';
                $dados['filho-atr1']   = 'sigla';
                $dados['filho-atr2']   = 'valor';
            break;
        }
        return $dados;
    }
    
    /**
     * Anatel::SatvaAtributo()
     * 
     * Possui os valores fixos de alguns atributos do tipo de xml
     * 
     * @return Os atributos
     */
    public function SatvaAtributo(){
        switch($this->CI->input->post('tipo_xml')){
            case 1: # Planos oferecidos
                $dado['tipo']       = 'Plano';
            break;
            case 3: # Banda larga
                $dado['tipo']       = 'BandaLarga';
            break;
            case 5: # Indicadores de qualidade
                $dado['tipo']       = 'Qualidade';
            break;
        }
        return $dado;
    }

}