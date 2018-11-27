<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
error_reporting(0);
setlocale(LC_ALL, 'pt_BR.UTF-8');
class Util{
	
    private $controlaClass = 0;
    private $nivel1 = 0;
    private $nivel2 = 0;
    private $nivel3 = 0;
    private $menuCompleto = '';
    private $menuCompletoLateral = '';
    private $positionMenu = 'right';
    private $arquivos = array();
    private $paiMenuLateralClicado = false;
    private $linkClicado = '';
    private $baseSistema = 'sistema/';
    
    public function __construct(){
        $this->CI =& get_instance();
        
        if($this->CI->session->userdata('indexHabilita') == 'SIM'){
            $this->linkClicado = substr($_SERVER['PHP_SELF'],1);
        }else{
            $this->baseSistema = '';
            $this->linkClicado = $_SERVER['REDIRECT_QUERY_STRING'];
        }
        
    }
    
    // Limpa o atributo de menu
    public function setMenuCompleto($valor){
        $this->menuCompleto = $valor;
    }
    
    // Limpa o atributo de menu
    public function setMenuCompletoLateral($valor){
        $this->menuCompletoLateral = $valor;
    }
    
    // Define a posição do menu
    public function setPositionMenu($valor){
        $this->positionMenu = $valor;
    }
    
    public function limpaArquivos(){
        $this->arquivos = array();
    }
    
	/**
	 * Util::formaValorBanco()
	 * 
     * Formata os dados para salvar no banco de dados
     * 
	 * @param mixed $valor Conteúdo para formação
	 * @return
	 */
	public function formaValorBanco($valor){
		
		#strtoupper();
		
		if(empty($valor) and $valor !== "0"){
			$valor = 'null';
		}elseif(preg_match('/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/', $valor)){ # DATA
        	$valor = "'".$this->formataData($valor, 'USA')."'";
        }elseif(preg_match('/^[0-9]{2}\/[0-9]{4}$/', $valor)){ # DATA (MÊS ANO)
        	$valor = "'".$this->formataData($valor, 'USA')."'";
        }elseif(preg_match('/^[0-9]+[.,]{1}[0-9]{2}$/',$valor)){ # NUMÉRICO (PONTO FLUTUANTE)
        	$valor = "'".preg_replace('/,/', '.', $valor)."'";
        }elseif(preg_match('/^[0-9]+$/', $valor)){ # INTEIRO
        	$valor = $valor;
        }else{ # STRING
            $valor = "'".$valor."'";
        }
		
		return $valor;
	}
	
	/**
	 * Util::removeAcentos()
	 * 
     * Remove os acentos da string
     * 
	 * @param mixed $string String para remoção de acentos
	 * @return
	 */
	public function removeAcentos($string) {
	
        $string = htmlentities($string, ENT_COMPAT, 'UTF-8');
        $string = preg_replace('/&([a-zA-Z])(uml|acute|grave|circ|tilde|cedil);/', '$1',$string);
    
		/*$string = preg_replace("/[ÁÀÂÃÄáàâãä]/", "a", $string);
		$string = preg_replace("/[ÉÈÊéèê]/", "e", $string);
		$string = preg_replace("/[ÍÌíì]/", "i", $string);
		$string = preg_replace("/[ÓÒÔÕÖóòôõö]/", "o", $string);
		$string = preg_replace("/[ÚÙÜúùü]/", "u", $string);
		$string = preg_replace("/[Çç]/", "c", $string);
		$string = preg_replace("/[][><}{;,!?*%~^`&#]/", "", $string);*/
		#$string = preg_replace("/[][><}{)(:;,!?*%~^`&#@]/", "", $string);
		#$string = preg_replace("/ /", "_", $string);
		#$string = strtolower($string);
		
		return $string;
		
	}
    
    /**
	 * Util::formataPorParametro()
	 * 
     * Formata o conteúdo de acordo com o parâmetro informado
     * 
	 * @param mixed $conteudo Conteúdo para formatação
	 * @param mixed $tipo Tipo de conteudo
	 * @return
	 */
    public function formataPorParametro($conteudo, $tipo){
        
        switch($tipo){
            case 'moeda':
                $conteudo = ltrim($conteudo, '0');
                if(substr($conteudo, 0,1) == '.'){
                    $valor = '0'.$conteudo;
                }else{
                    $valor = $conteudo;
                }
            break;
            case 'celular':
                $valor = str_replace('-', '', $conteudo);
            break;
            case 'data': #20150919 PARA 2015-09-19
                $valor = preg_replace('/([0-9]{4})([0-9]{2})([0-9]{2})/', '${1}-${2}-${3}', $conteudo);
            break;
            case 'inteiro':
                $valor = (int)$conteudo;
            break;
            default:
                $valor = $conteudo;
        }
        
        return $this->formaValorBanco($valor);
        
    }
	
	/**
	 * Util::formataData()
	 * 
     * Formata a data para USA ou BR
     * 
	 * @param mixed $data Data para formatação
	 * @param mixed $tipo Tipo de formatação
	 * @return
	 */
	public function formataData($data, $tipo){
	
		if($tipo == 'USA'){
            
            if(strlen($data) == 19){
                
                $inicio = explode(' ', $data);
                $data = implode('-',array_reverse(explode('/', $inicio[0]))).' '.$inicio[1];
                
            }else{
        
                $data = implode('-',array_reverse(explode('/', $data)));
            
            }
			
		}else{
            
            if(strlen($data) == 19){
                
                $inicio = explode(' ', $data);
                $data = implode('/',array_reverse(explode('-', $inicio[0]))).' '.$inicio[1];
                
            }else{
        
                $data = implode('/',array_reverse(explode('-', $data)));
            
            }
		
		}
	
		return $data;
	
	}
    
    /**
     * Util::listaMesesAteAtual()
     * 
     * Cria uma lista de meses
     * 
     * @param string $atual Se atual lista até mês atual senão lista todos os meses
     * @return
     */
    public function listaMesesAteAtual($atual = 'nao'){
        
        $meses = array(
          '1' => 'JANEIRO',
          '2' => 'FEVEREIRO',
          '3' => 'MARÇO',
          '4' => 'ABRIL',
          '5' => 'MAIO',
          '6' => 'JUNHO',
          '7' => 'JULHO',
          '8' => 'AGOSTO',
          '9' => 'SETEMBRO',
          '10' => 'OUTUBRO',
          '11' => 'NOVEMBRO',
          '12' => 'DEZEMBRO'
       );
       
       if($atual == 'sim'){
        
           for($i=1; $i<date('m'); $i++){
                $lista[] = $meses[$i];
           }
           
           return $lista;
       
       }
       
       return $meses;
       
    }
    
    /**
     * Util::mesExtenso()
     * 
     * Pega o nome dos mês por extenso
     * 
     * @param string $mes Mês que se deseja pegar o nome por extenso
     * @return
     */
    public function mesExtenso($mes){
        
        $meses = array(
          '01' => 'JANEIRO',
          '02' => 'FEVEREIRO',
          '03' => 'MARÇO',
          '04' => 'ABRIL',
          '05' => 'MAIO',
          '06' => 'JUNHO',
          '07' => 'JULHO',
          '08' => 'AGOSTO',
          '09' => 'SETEMBRO',
          '10' => 'OUTUBRO',
          '11' => 'NOVEMBRO',
          '12' => 'DEZEMBRO'
       );
       
       return $meses[$mes];
        
    }
    
    public function mesSigla($mes){
        
        $meses = array(
          '01' => 'JAN',
          '02' => 'FEV',
          '03' => 'MAR',
          '04' => 'ABR',
          '05' => 'MAI',
          '06' => 'JUN',
          '07' => 'JUL',
          '08' => 'AGO',
          '09' => 'SET',
          '10' => 'OUT',
          '11' => 'NOV',
          '12' => 'DEZ'
       );
       
       return $meses[$mes];
        
    }
    
    /**
    * Função que formata o valor numérico (Moeda)
    * @return Retorna o valor formatado
    *  
    * @param mixed $valor
    * @return Retorna o valor formatado
    */
    public function formataValor($valor){
 		$valor = array (substr($valor,0,strlen($valor)-2), substr($valor,strlen($valor)-2,2));
		$valor = ($valor[0] * 1).".".$valor[1];
		return $valor;
		//return $valor;
 	}
    
    /**
     * Util::montaMenu()
     * 
     * @param mixed $menus Todos os menus
     * @param mixed $paisMenu Somente os pais
     * @return
     */
    public function montaMenu($menus, $paisMenu){

        foreach($paisMenu as $pM){
            $pais[] = $pM['pai_menu'];
        }

        $this->paisMenu = $pais;

        foreach($menus as $me){
            
            $menuItens[$me->pai_menu][$me->cd_menu] = array('link' => $me->link_menu,'nome' => $me->nome_menu);
            
        }
        #echo'<pre>'; print_r($menuItens); exit();
        return $this->loopMenu($menuItens);
        #exit();
    }
    
    /**
     * Util::loopMenu()
     * 
     * Auxilia na montagem do menu
     * 
     * @param mixed $menuTotal
     * @param integer $idPai
     * @param string $filho
     * @return
     */
    public function loopMenu(array $menuTotal , $idPai = 0, $filho = 'nao'){
        
        if($filho == 'nao'){ # Se não é filho define class pai
            $classUl = 'class="nav navbar-nav navbar-'.$this->positionMenu.'"';
        }else{ # Se é filho define classe filho
            $classUl = 'class="dropdown-menu"';
        }
        
        $this->menuCompleto .= '<ul '.$classUl.'>';
   
        foreach( $menuTotal[$idPai] as $idMenu => $menuItem){
            
            if(in_array($idMenu, $this->paisMenu)){ # É filho então configura filho
                $classLi = 'class="dropdown"';
                $link = '#';
                $classLinkPai = 'class="dropdown-toggle" data-toggle="dropdown"';
                $auxLinkPai = '<b class="caret"></b>';
            }else{ # Configura pai
                $classLi = '';

                $link = base_url($this->CI->session->userdata('indexPHP').$menuItem['link']);
                
                $classLinkPai = '';
                $auxLinkPai = '';
            }
           
            if($this->baseSistema.$this->CI->session->userdata('indexPHP').$menuItem['link'] == $this->linkClicado){
                $ativo = 'class="active"';
            }else{
                $ativo = '';
            }
          
            $this->menuCompleto .= '<li '.$ativo.' '.$classLi.'>';
            
            $this->menuCompleto .= '<a href="'.$link.'" '.$classLinkPai.'>'.utf8_decode($menuItem['nome']).$auxLinkPai.'</a>';
                        
            if( isset( $menuTotal[$idMenu] ) ) $this->loopMenu($menuTotal,$idMenu, 'sim');
            
            $this->menuCompleto .= '</li>';
            
        }
        
        $this->menuCompleto .= '</ul>';
        
        return $this->menuCompleto;
        
    }
    
    /**
     * Util::montaMenu()
     * 
     * @param mixed $menus Todos os menus
     * @param mixed $paisMenu Somente os pais
     * @return
     */
    public function montaMenuLateral($menus, $paisMenu){
        
        
        foreach($paisMenu as $pM){
            $pais[] = $pM['pai'];
        }

        $this->paisMenuLateral = $pais;

        foreach($menus as $me){
            
            $menuItens[$me->pai][$me->cd_menu_lateral] = array('link' => $me->link,'nome' => $me->nome);
            
        }

        foreach($this->paisMenuLateral as $paiClicado){
            if(in_array(str_replace("sistema/index.php/", "", $this->linkClicado), array_column($menuItens[$paiClicado], 'link'))){
                $this->paiMenuLateralClicado = $paiClicado;
            }
        }
        
        return $this->loopMenuLateral($menuItens);
        
    }
    
    /**
     * Util::loopMenu()
     * 
     * Auxilia na montagem do menu
     * 
     * @param mixed $menuTotal
     * @param integer $idPai
     * @param string $filho
     * @return
     */
    public function loopMenuLateral(array $menuTotal , $idPai = 0, $filho = 'nao'){
        
        if($filho == 'nao'){ # Se não é filho define class pai
            $classUl = 'class="nav navbar-nav navbar-'.$this->positionMenu.'"';
        }else{ # Se é filho define classe filho
            $classUl = 'class="dropdown-menu"';
        }
        
        $this->menuCompletoLateral .= '<ul '.$classUl.'>';

        foreach( $menuTotal[$idPai] as $idMenu => $menuItem){
            
            if(in_array($idMenu, $this->paisMenuLateral)){ # É filho então configura filho
                if($idMenu == $this->paiMenuLateralClicado){
                    $classLi = 'dropdown open';
                }else{
                    $classLi = 'dropdown';
                }
                $link = '#';
                $classLinkPai = 'class="dropdown-toggle" data-toggle="dropdown"';
                $auxLinkPai = '<b class="caret"></b>';
            }else{ # Configura pai
                $classLi = '';
                $link = base_url($this->CI->session->userdata('indexPHP').$menuItem['link']);
                $classLinkPai = '';
                $auxLinkPai = '';
            }

            if($this->baseSistema.$this->CI->session->userdata('indexPHP').$menuItem['link'] == $this->linkClicado){
                $ativo = 'active';
            }else{
                $ativo = '';
            }
          
            $this->menuCompletoLateral .= '<li class="'.$ativo.' '.$classLi.'">';
            
            $this->menuCompletoLateral .= '<a href="'.$link.'" '.$classLinkPai.'>'.utf8_decode($menuItem['nome']).$auxLinkPai.'</a>';

            if( isset( $menuTotal[$idMenu] ) ) $this->loopMenuLateral($menuTotal,$idMenu, 'sim');
            
            $this->menuCompletoLateral .= '</li>';
            
        }
        
        $this->menuCompletoLateral .= '</ul>';
        
        return $this->menuCompletoLateral;
        
    }
    
    /**
     * Util::montaPermissao()
     * 
     * Monta a árvore de permissões
     * 
     * @param mixed $permissoes Todas permissões
     * @param mixed $paiPermissoes Pai das permissões
     * @param bool $permissoesUsuario Permissões que o usuário pussui
     * @return
     */
    public function montaPermissao($permissoes, $paiPermissoes, $permissoesUsuario = false){
        
        foreach($paiPermissoes as $paiP){
            
            $perm[] = $paiP['pai_permissao'];
            
        }
        
        $this->paiPermissao = $perm;
        
        foreach($permissoes as $permi){
            
            $permItem[$permi->pai_permissao][$permi->cd_permissao] = array('nome'=>$permi->nome_permissao);
            
        }
        
        return $this->loopPermissoes($permItem, 0, 'nao', $permissoesUsuario);
    
    }
    
    /**
     * Util::loopPermissoes()
     * 
     * Auxilida a montagem das permissões
     * 
     * @param mixed $permissoesTotal
     * @param integer $idPai
     * @param string $filho
     * @param bool $permissoesUsuario
     * @return
     */
    public function loopPermissoes(array $permissoesTotal , $idPai = 0, $filho = 'nao', $permissoesUsuario = false){
        
        $this->permissoesCompleto .= '<ul id="idPermissoes">';
   
        foreach( $permissoesTotal[$idPai] as $idPermissao => $permissaoItem){
            
            $divInicio = '';
            $divFim = '';
            
            //if(in_array($idPermissao, $this->paiPermissao)){ # Se não é filho define class pai
            if(preg_match('/^MENU/', $permissaoItem['nome'])){
                $this->nivel1++;
                $this->controlaClass++;
            
                #$sequencia = $this->controlaClass;
            
                $classUl = 'class="classItem'.$this->controlaClass.'"';
                $marcaTodos = 'onclick="marcaGrupo(\'.classItem'.$this->controlaClass.'\', this)"';
                
            }else{ # Se é filho define classe filho
            
                if(preg_match('/^MÓDULO/', $permissaoItem['nome'])){
                    
                    #$this->nivel1++;
                    
                    $classUl = 'class="classItem'.$this->controlaClass.' classNivel'.$this->nivel1.' classNivel'.$this->nivel2.'"';
                    $marcaTodos = 'onclick="marcaGrupo(\'.classNivel'.$this->nivel1.'\', this)"';
                    
                }elseif(preg_match('/^PÁGINA/', $permissaoItem['nome'])){

                    $this->nivel2++;

                    $divInicio = '<div><a href="#div'.$this->nivel2.'" onclick="mostrarOcultar(this, \'.div'.$this->nivel2.'\')">Mostrar</a></div><div class="todasDivs div'.$this->nivel2.'">';
                    $divFim = '</div>';
                    
                    $classUl = 'class="classItem'.$this->controlaClass.' classNivel'.$this->nivel1.' classNivel'.$this->nivel2.'"';
                    $marcaTodos = 'onclick="marcaGrupo(\'.classNivel'.$this->nivel2.'\', this)"';
                    
                }else{
            
                    $classUl = 'class="classItem'.$this->controlaClass.' classNivel'.$this->nivel1.' classNivel'.$this->nivel2.'"';
                    $marcaTodos = '';
                
                }
            }
            
            if(in_array($idPermissao, $permissoesUsuario)){
                $marcado = 'checked';
            }else{
                $marcado = ''; 
            }
            
            $this->permissoesCompleto .= $divInicio;
            $this->permissoesCompleto .= '<li>';
            
            $this->permissoesCompleto .= '<label>';
            $this->permissoesCompleto .= '<input '.$marcado.' type="checkbox" '.$marcaTodos.' '.$classUl.' name="permissao[]" value="'.$idPermissao.'" />&nbsp';
            $this->permissoesCompleto .= htmlentities($permissaoItem['nome']);
            $this->permissoesCompleto .= '</label>';

            if( isset( $permissoesTotal[$idPermissao] ) ) $this->loopPermissoes($permissoesTotal,$idPermissao, 'sim', $permissoesUsuario);
            
            $this->permissoesCompleto .= '</li>';
            $this->permissoesCompleto .= $divFim;
            
        }
        
        $this->permissoesCompleto .= '</ul>';
        
        #$this->controlaClass++;
        
        return $this->permissoesCompleto;
        
    }
    
    /**
     * Util::medidasInformatica()
     * 
     * Converte mega para kb
     * 
     * @param $valor Valor pra conversão
     * @param $de Formato atual
     * @param $para Formato que se quer chegar
     * 
     */
    public function medidasInformatica($valor, $de, $para){
        $kb = 1024; # = 1 MEGA
        if($de == 'MEGA' and $para == 'KB'){
            $result = $valor * $kb;
        }
        return $result;
    }
    
    /**
     * Util::buscaArquivosDiretorios()
     * 
     * Busca os arquivos no diretório
     * 
     * @param mixed $diretorio Diretório em que será extraido os arquivos 
     * 
     */
    public function buscaArquivosDiretorios($diretorio = null){
        
        #error_reporting(E_ALL);
        #ini_set('display_errors', TRUE);
        #ini_set('display_startup_errors', TRUE);
        
        #Abre o diretório
        $ponteiro  = opendir($diretorio);
        
        #echo '<pre>'; print_r($ponteiro); exit();
        // monta os vetores com os itens encontrados na pasta (Pastas encontradas)
        #while ($nome_itens = readdir($ponteiro)) {
        while (false !== ($nome_itens = readdir($ponteiro))){
            $itens[] = $nome_itens;
        }
        
        # Lopping nas pastas encontradas
        foreach($itens as $listar){
        
            # Remove os pontos de diretório
            if ($listar!="." && $listar!=".."){

                # Se é pasta
                if (is_dir($diretorio.'/'.$listar)) { 
                    
                    # Armazena as pastas
                    $this->dirPastas[] = $diretorio.'/'.$listar;
                    
                    /*
                    Chama as função "buscaArquivosDiretorios" novamente 
                    para destrinchar o diretório até encontrar os arquivos
                    */
                    $this->buscaArquivosDiretorios($diretorio.'/'.$listar);

                }else{
                    #echo filesize($diretorio.'/'.$listar); echo '<br>';
                    # Armazena os arquivos
                    $this->arquivos[] = $diretorio.'/'.$listar;
                    
                }
            }
        } 
        
        return $this->arquivos;     
        
    }
    
    /**
     * Util::uploadArquivo()
     * 
     * @return
     */
    function uploadArquivo($campo = 'userfile', $config = false){
        
        $this->CI->load->helper('file');
        
        if($config == false){                
                        
    		$config['upload_path'] = './temp';
            $config['allowed_types'] = 'txt';
    		#$config['allowed_types'] = '*';
    		#$config['max_size'] = '0';
    		#$config['max_width'] = '0';
    		#$config['max_height'] = '0';
    		#$config['encrypt_name'] = true;
        
        }                        
        
		$this->CI->load->library('upload',$config);
        
		if(!$this->CI->upload->do_upload($campo)){
			#$error = array('error' => $this->CI->upload->display_errors());
			#print_r($error);
			#exit();
            echo 'aqui'; exit();
            $dados['status'] = false;
            $dados['descricao'] = $this->CI->upload->display_errors();
            
            return $dados;
            
		}else{
			#$data = array('upload_data' => $this->upload->data());
			#return $data['upload_data']['file_name'];
            
            $dados['status'] = true;
            $dados['descricao'] = 'OK';
            $dados['arquivo'] = $this->CI->upload->data();
            
            return $dados;
		}
        
	} // Fecha uploadArquivo()
    
    /**
     * Util::apagaArquivo()
     * 
     * Apaga arquivo
     * 
     * @return
     */
    public function apagaArquivo($arquivo){
        
        if(@unlink($arquivo)){
            return true;
        }else{
            $this->apagaArquivo($arquivo);
            return false; 
        }
        
    }
    
    /**
     * Util::removeDiretorio()
     * 
     * Apaga diretório
     * 
     * @return
     */
    function removeDiretorio($dir, $DeleteMe = false) {
        if(!$dh = @opendir($dir)) return;
        while (false !== ($obj = readdir($dh))) {
            if($obj=='.' || $obj=='..') continue;
            if (!@unlink($dir.'/'.$obj)) $this->remove_dir($dir.'/'.$obj, true);
        }
 
        closedir($dh);
        if ($DeleteMe){
            @rmdir($dir);
        }
    
    }
    
    /**
     * Util::telefoniaBilhetacao()
     * 
     * Apaga diretório
     * 
     * @return
     */
    public function telefoniaBilhetacao($segundos, $tipoLigacao, $fonte){
        
        # CallCenter Ativo
        $tarifas['callcenter']['fixo'] = 0.0402;
        $tarifas['callcenter']['fixoddd'] = 0.1296;
        $tarifas['callcenter']['celular'] = 0.4486;
        $tarifas['callcenter']['celularddd'] = 0.4486;
        
        # Holding Ativo
        $tarifas['holding']['fixo'] = 0.0402;
        $tarifas['holding']['fixoddd'] = 0.1296;
        $tarifas['holding']['celular'] = 0.4486;
        $tarifas['holding']['celularddd'] = 0.4486;
        
        # CallCenter Receptivo - 0800
        $tarifas['callcenter0800']['fixo'] = 0.1782;
        $tarifas['callcenter0800']['fixoddd'] = 0.2673;
        $tarifas['callcenter0800']['celular'] = 0.3301;
        $tarifas['callcenter0800']['celularddd'] = 0.3301;
        
        # CallCenter Receptivo - 4004
        $tarifas['callcenter4004']['fixo'] = 0.07;
        $tarifas['callcenter4004']['fixoddd'] = 0.07;
        $tarifas['callcenter4004']['celular'] = 0.07;
        $tarifas['callcenter4004']['celularddd'] = 0.07;
        
        $tarifaFonte = $tarifas[$fonte][$tipoLigacao];
        
        #if($fonte == 'callcenter'){
        
            if($segundos < 3){
                return 0;
            }elseif(($segundos/60)<=0.5){
                return $tarifaFonte * 0.5;
            }elseif(($segundos/60)>0.5){
                return $tarifaFonte * 0.5 + (ceil(($segundos-30)/6)) * ($tarifaFonte / 10);
            }else{
                return 0;
            }
        
        #}
        /*
        switch ($tipoLigacao) {
            case 'Celular': # Celular local
                echo "i equals 0";
                break;
            case 'DDDCel': # Celular à dintância
                echo "i equals 1";
                break;
            case 'Fixo': # Fixo local
                echo "i equals 2";
                break;
            default: # Fixo à distância
               echo "i is not equal to 0, 1 or 2";
        }
        */
    }
    
    public function telefoniaPeriodo($mesAno){
        
        $sep = explode('-', $mesAno);
        
        $periodo['01'] = $sep[1].'-01-03 '.$sep[1].'-02-03';
        $periodo['02'] = $sep[1].'-02-03 '.$sep[1].'-03-03';
        $periodo['03'] = $sep[1].'-03-03 '.$sep[1].'-04-03';
        $periodo['04'] = $sep[1].'-04-03 '.$sep[1].'-05-03';
        $periodo['05'] = $sep[1].'-05-03 '.$sep[1].'-06-03';
        $periodo['06'] = $sep[1].'-06-03 '.$sep[1].'-07-03';
        $periodo['07'] = $sep[1].'-07-03 '.$sep[1].'-08-03';
        $periodo['08'] = $sep[1].'-08-03 '.$sep[1].'-09-03';
        $periodo['09'] = $sep[1].'-09-03 '.$sep[1].'-10-03';
        $periodo['10'] = $sep[1].'-10-03 '.$sep[1].'-11-03';
        $periodo['11'] = $sep[1].'-11-03 '.$sep[1].'-12-03';
        $periodo['12'] = $sep[1].'-12-03 '.($sep[1]+1).'-01-03';
        
        return $periodo[$sep[0]];
        
    }
    
    /**
     * Util::getDataURI()
     * 
     * Cria URI para o link informado, ou seja, encripta o link na base64
     * 
     * @return O link encriptado
     */
    function getDataURI($image, $mime = '') {
    	#return 'data: '.(function_exists('mime_content_type') ? mime_content_type($image) : $mime).';base64,'.base64_encode(file_get_contents($image));
        return 'data: '.mime_content_type($image).';base64,'.base64_encode(file_get_contents($image));
    }
    
    /**
     * Util::enviaEmail()
     * 
     * Realiza o envio de e-mail
     * 
     * @return O link encriptado
     */
    public function enviaEmail($nomeDe = 'Sim TV - Ferramentas de Negócios', $emailDe = 'equipe.sistemas@simtv.com.br', $emailPara = '', $titulo = '', $texto = '', $anexo = false){
        
        $this->CI->load->library("My_phpmailer", '', 'phpMailer');
        
        $mail = $this->CI->phpMailer->inicializar();
        
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
            return true;
        }else{
            #echo "Erro: " . $mail->ErrorInfo;
            return false;
        }
        
    }
    
    /**
     * Util::satvaCalculoPorcentagem()
     * 
     * Satva - Calculo de porcentagem da meta
     * 
     * @return O link encriptado
     */
    public function satvaCalculoPorcentagem($res1, $res2){
        return round(($res1 / $res2) * 100,2);
    }
    
    /**
     * Util::satvaCalculoFormula()
     * 
     * Satva - Calculo da meta
     * 
     * @return O link encriptado
     */
    public function satvaCalculoFormula($res1, $res2){
        return round((($res1 - $res2) * 100) / $res1,2);
    }
    
    public function satvaCalculoNumerica(){
        
    }
    
    function base64url_encode($data) { 
      return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); 
    } 
    
    function base64url_decode($data) { 
      return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT)); 
    }

}