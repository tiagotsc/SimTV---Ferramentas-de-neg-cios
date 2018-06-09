<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Crud{
    
    private $controller = false;
    private $metodoController = false;
    private $metodo = false;
    private $parMetodo = array();
    private $tabela = false;
        
    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->model('base/crud_model','crudModel');
        $this->ci->load->library('table');
        #$this->ci->load->library('Util', '', 'util'); 
        #$ci->load->library('crud','','crud');
    }
    
    public function __call( $name, $arguments ){
        echo '"'.$name.'" m&eacute;todo n&atilde;o definido!'; exit();
    }
    
    public function setConexao($conexao){
        
        $this->ci->crudModel->setConexao($conexao);
        
    }
    
    public function setTabela( $tabela )
	{
	   $this->tabela = $tabela;
		$this->ci->crudModel->setTabela($tabela);
	}
    
    public function setRelacao($campoOrigem, $tabela, $campoDestino){
        
        $this->ci->crudModel->setRelacao($campoOrigem, $tabela, $campoDestino);
        
    }
    
    public function setListaCamposSel($campos){ 
        $this->ci->crudModel->setListaCamposSelecionados($campos);
    }
    /*
    public function listaCampos($array = false)
    {
        $this->ci->crudModel->listaCampos($array);
    }
    */
    public function ini(){

        foreach($this->ci->uri->segments as $pos => $uri){
        
            switch($pos){
                case 1:
                    $this->pasta = $uri; # 1ª é o pasta
                    break;
                case 2:
                    $this->controller = $uri; # 2ª Controller
                    break;
                case 3:
                    $this->metodo = $uri; # 3ª é o método do controller
                    break;
                default:
                    $this->parMetodo[] = $uri; # Do 4ª em diante são os parâmetros do método
            }
        
        }
        #echo '<pre>'; print_r($this->ci->uri->segments); exit();
        # Se nenhum método é especificado chama o método padrão que é listar 
        #OBS.: O método do controle precisa ter o mesmo nome da tabela para cair nesse caso.
        /*if($this->metodo == false){
            if($this->tabela == false){
                $this->ci->crudModel->setTabela($this->metodoController);
            }
            $this->metodo = 'listar';
            return $this->listar();
        }else{
            $metodo = (string)$this->metodo;
            return $this->$metodo();
        }*/
        
    }
    
    public function getParMetodo(){
        
        if($_POST){
            foreach($_POST as $campo => $valor){
                if($campo != 'btn'){
                    if($valor != ''){
                        $campos[] = $campo.'='.$valor;
                    }
                }
            }
            if(isset($campos) and $campos){
                $this->parMetodo[0] = $this->base64url_encode(implode('|',$campos));
            }
        }
        
        $post = (isset($this->parMetodo[0]))? $this->base64url_decode($this->parMetodo[0]): 0;
        
        $sort_by = (isset($this->parMetodo[1]))? $this->parMetodo[1]: 1;
        $sort_order = (isset($this->parMetodo[2]))? $this->parMetodo[2]: 'asc';
        $pagina = (isset($this->parMetodo[3]))? $this->parMetodo[3]: null;
        
        return array($post, $sort_by, $sort_order, $pagina);
        
        #return $this->parMetodo;
        
    }
    
    public function listar(){

        $mostra_por_pagina = 30;
        if($_POST){
            foreach($_POST as $campo => $valor){
                if($campo != 'btn'){
                    if($valor != ''){
                        $campos[] = $campo.'='.$valor;
                    }
                }
            }
            if($campos){
                $this->parMetodo[0] = $this->base64url_encode(implode('|',$campos));
            }
        }
        
        
        
        $post = (isset($this->parMetodo[0]))? $this->base64url_decode($this->parMetodo[0]): 0;
        $sort_by = (isset($this->parMetodo[1]))? $this->parMetodo[1]: 1;
        $sort_order = (isset($this->parMetodo[2]))? $this->parMetodo[2]: 'asc';
        $pagina = (isset($this->parMetodo[3]))? $this->parMetodo[3]: null;

        $resultado = $this->ci->crudModel->listar($post, $mostra_por_pagina, $sort_by, $sort_order, $pagina);
        
        $postEncode = $this->base64url_encode($post);
        
        $this->ci->load->library('pagination');
        $config['base_url'] = base_url($this->pasta.'/'.$this->controller.'/'.$this->metodo.'/'.$postEncode.'/'.$sort_by.'/'.$sort_order); 
		$config['total_rows'] = $resultado['qtd'];
		$config['per_page'] = $mostra_por_pagina;
		$config['uri_segment'] = 7;
        $config['first_link'] = '&lsaquo; Primeiro';
        $config['last_link'] = '&Uacute;ltimo &rsaquo;';
        $config['full_tag_open'] = '<li>';
        $config['full_tag_close'] = '</li>';
        $config['first_tag_open']	= '';
       	$config['first_tag_close']	= '';
        $config['last_tag_open']		= '';
	    $config['last_tag_close']		= '';
	    $config['first_url']			= ''; // Alternative URL for the First Page.
	    $config['cur_tag_open']		= '<a id="paginacaoAtiva" class="active"><strong>';
	    $config['cur_tag_close']		= '</strong></a>';
	    $config['next_tag_open']		= '';
        $config['next_tag_close']		= '';
	    $config['prev_tag_open']		= '';
	    $config['prev_tag_close']		= '';
	    $config['num_tag_open']		= '';
		$this->ci->pagination->initialize($config);
        
        $qtdRegistros = ($resultado['qtd'] < $mostra_por_pagina)? $resultado['qtd']: $mostra_por_pagina;
        #$dados['qtdDadosCorrente'] = ($pagina == null)? $qtdRegistros: $mostra_por_pagina + $pagina;
        
        if($pagina == null){
            $dados['qtdDadosCorrente'] = $qtdRegistros;
        }elseif(($mostra_por_pagina + $pagina) > $resultado['qtd']){
            $restante = $resultado['qtd'] - $pagina;
            $dados['qtdDadosCorrente'] = $pagina + $restante;
        }else{
            $dados['qtdDadosCorrente'] = $mostra_por_pagina + $pagina;
        }
        
        $dados['sort_by'] = $sort_by;
		$dados['sort_order'] = $sort_order;
        $dados['titulo'] = 'Listar '.$resultado['tabela'];
        $dados['qtdRegistos'] = $resultado['qtd'];
        $dados['pasta'] = $this->pasta;
        $dados['controller'] = $this->controller;
        $dados['metodo'] = $this->metodo;
        $dados['parMetodo'] = $this->parMetodo;
        
        if(isset($post)){
            $campoValor = explode('|', $post);
            foreach($campoValor as $caVa){
                $res = explode('=', $caVa);
                $dados[$res[0]] = $res[1];
                #echo '<pre>'; print_r($res); exit();
            }
        }
        
        #echo '<pre>'; print_r($post); exit();
        $dados['post'] = $postEncode;
        $dados['view'] = 'listar';
        $dados['paginacao'] = $this->ci->pagination->create_links();
        $dados['campos'] = $resultado['campos'];
        $dados['dados'] = $resultado['dados'];
        
        return $dados;
        
    }
    
    public function listarManual($resultado, $mostra_por_pagina, $postEncode = null, $sort_by, $sort_order, $pagina = null){
        
        $this->ci->load->library('pagination');
        $config['base_url'] = base_url($this->pasta.'/'.$this->controller.'/'.$this->metodo.'/'.$postEncode.'/'.$sort_by.'/'.$sort_order); 
		$config['total_rows'] = $resultado['qtd'];
		$config['per_page'] = $mostra_por_pagina;
		$config['uri_segment'] = 7;
        $config['first_link'] = '&lsaquo; Primeiro';
        $config['last_link'] = '&Uacute;ltimo &rsaquo;';
        $config['full_tag_open'] = '<li>';
        $config['full_tag_close'] = '</li>';
        $config['first_tag_open']	= '';
       	$config['first_tag_close']	= '';
        $config['last_tag_open']		= '';
	    $config['last_tag_close']		= '';
	    $config['first_url']			= ''; // Alternative URL for the First Page.
	    $config['cur_tag_open']		= '<a id="paginacaoAtiva" class="active"><strong>';
	    $config['cur_tag_close']		= '</strong></a>';
	    $config['next_tag_open']		= '';
        $config['next_tag_close']		= '';
	    $config['prev_tag_open']		= '';
	    $config['prev_tag_close']		= '';
	    $config['num_tag_open']		= '';
		$this->ci->pagination->initialize($config);
        
        $qtdRegistros = ($resultado['qtd'] < $mostra_por_pagina)? $resultado['qtd']: $mostra_por_pagina;
        #$dados['qtdDadosCorrente'] = ($pagina == null)? $qtdRegistros: $mostra_por_pagina + $pagina;
        
        if($pagina == null){
            $dados['qtdDadosCorrente'] = $qtdRegistros;
        }elseif(($mostra_por_pagina + $pagina) > $resultado['qtd']){ 
            $restante = $resultado['qtd'] - $pagina;
            $dados['qtdDadosCorrente'] = $pagina + $restante;
        }else{ 
            $dados['qtdDadosCorrente'] = $mostra_por_pagina + $pagina;
        }
       
        $dados['sort_by'] = $sort_by;
		$dados['sort_order'] = $sort_order;
        $dados['titulo'] = 'Listar '.utf8_decode($resultado['tabela']);
        $dados['id'] = $resultado['id'];
        $dados['qtdRegistos'] = $resultado['qtd'];
        $dados['camposLabel'] = (isset($resultado['camposLabel']))? $resultado['camposLabel'] : false;
        $dados['campos'] = $resultado['campos'];
        $dados['dados'] = $resultado['dados'];
        $dados['pasta'] = $this->pasta;
        $dados['controller'] = $this->controller;
        $dados['metodo'] = $this->metodo;
        $dados['parMetodo'] = $this->parMetodo;
        
        if($postEncode != null){ 
            $post = $this->base64url_decode($postEncode);            
            $campoValor = explode('|', $post);
            foreach($campoValor as $caVa){
                $res = explode('=', $caVa);
                $dados[$res[0]] = $res[1];
                #echo '<pre>'; print_r($res); exit();
            }
        }
        
        #echo '<pre>'; print_r($post); exit();
        $dados['post'] = $postEncode;
        $dados['view'] = 'listar';
        $dados['paginacao'] = $this->ci->pagination->create_links();
        
        return $dados;
        
    }
    
    public function cadastrar(){
        
        echo 'M&eacute;todo cadastrar';
        exit();
        
    }
    
    public function editar(){
        
        echo 'M&eacute;todo editar';
        exit();
        
    }
    
    public function apagar(){
        
        echo 'M&eacute;todo apagar';
        exit();
        
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
    
    function base64url_encode($data) { 
      return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); 
    } 
    
    function base64url_decode($data) { 
      return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT)); 
    } 

}