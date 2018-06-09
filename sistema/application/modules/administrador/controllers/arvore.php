<?php
#error_reporting(0);
include_once(APPPATH.'modules/base/controllers/base.php');
#include_once(APPPATH.'controllers/base.php');
if(!defined('BASEPATH')) exit('No direct script access allowed');
#date_default_timezone_set('America/Sao_Paulo');
#setlocale(LC_ALL, 'pt_BR.UTF-8', 'Portuguese_Brazil.1252');
/**
* Classe responsável pela perfil
*/
class Arvore extends Base
{
    
	/**
	 * Perfil::__construct()
	 * 
	 * @return
	 */
	public function __construct(){
       
		parent::__construct();
        
        $this->load->model('permissaoPerfil_model','permissaoPerfil');
        $this->load->model('permissao_model','permissao');
        $this->load->model('tree_model','tree');
        $this->load->library('perfiltree', '', 'perfiltree');

    }
    
    function getChildren()
    {
        $result = $this->tree->tree_all();
        
        $itemsByReference = array();
        
        // Build array of item references:
        foreach($result as $key => &$item) {
            $itemsByReference[$item['id']] = &$item;
            // Children array:
            $itemsByReference[$item['id']]['children'] = array();
            // Empty data class (so that json_encode adds "data: {}" )
            $itemsByReference[$item['id']]['data'] = new StdClass();
        }
        
        // Set items as children of the relevant parent item.
        foreach($result as $key => &$item)
            if($item['parent_id'] && isset($itemsByReference[$item['parent_id']]))
        	$itemsByReference [$item['parent_id']]['children'][] = &$item;
        
        // Remove items that were added to parents elsewhere:
        foreach($result as $key => &$item) {
            if($item['parent_id'] && isset($itemsByReference[$item['parent_id']]))
        	unset($result[$key]);
        }
        foreach ($result as $row) {
            $data[] = $row;
        }
        
        // Encode:
        echo json_encode($data);
    }
    
    public function ficha(){
        
        $paiPermissoes = $this->dadosBanco->paiPermissao();
        $permissoes = $this->dadosBanco->permissoes();
        
        $dados['permissoes'] = $this->perfiltree->montaPermissao($permissoes, $paiPermissoes, $permissoesDoPerfil);
        
        $this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        
        if(in_array(18, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'perfil/view_frm', $dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    public function fichaTqTree($cd = false){
        
        if($cd){
            
            $dados = $this->permissaoPerfil->dadosPerfil($cd);
            
            $campos = array_keys($dados);
            
            foreach($campos as $campo){
			 
                #Data
                #if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $dados[$campo])){
                    #$dados[$campo] = $this->util->formataData($dados[$campo],'BR');
                #}
             
				$dados[$campo] = $dados[$campo]; # ALIMENTA OS CAMPOS COM OS DADOS
			}
            
        }else{
            
            $campos = $this->permissaoPerfil->camposPerfil();
            
            foreach($campos as $campo){
                $dados[$campo] = '';
            }
        
        }
        
        $this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        
        if(in_array(18, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'perfil/view_tqtree_frm', $dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    function getTqTree($cd)
    {
        
        $permissoesPerfil = $this->permissaoPerfil->permissoesDoPerfil($cd);
        $permissoes = array_column($permissoesPerfil, 'cd_permissao');
        
        #$result = $this->tree->tree_all();
        $result = $this->tree->tqTree_all();
        
        $itemsByReference = array();
        
        // Build array of item references:
        foreach($result as $key => &$item) {
            
           
            #echo $item['id']; exit();
            $itemsByReference[$item['id']] = &$item;
            
            if(in_array($item['id'], $permissoes)){
                $itemsByReference[$item['id']]['permitido'] = 'S';
            }else{
                $itemsByReference[$item['id']]['permitido'] = 'N';
            }
            
            // Children array:
            $itemsByReference[$item['id']]['children'] = array();
            
            // Empty data class (so that json_encode adds "data: {}" )
            $itemsByReference[$item['id']]['data'] = new StdClass();
        }

        // Set items as children of the relevant parent item.
        foreach($result as $key => &$item)
            if($item['parent_id'] && isset($itemsByReference[$item['parent_id']]))
        	$itemsByReference [$item['parent_id']]['children'][] = &$item;
        
        // Remove items that were added to parents elsewhere:
        foreach($result as $key => &$item) {
            if($item['parent_id'] && isset($itemsByReference[$item['parent_id']]))
        	unset($result[$key]);
        }
        foreach ($result as $row) {
            $data[] = $row;
        }
        #echo '<pre>'; print_r($itemsByReference); exit();
        // Encode:
        echo json_encode($data);
    }
    
    
    function getAccordion($cd)
    {
        
        $permissoesPerfil = $this->permissaoPerfil->permissoesDoPerfil($cd);
        $permissoes = array_column($permissoesPerfil, 'cd_permissao');
        
        #$result = $this->tree->tree_all();
        $result = $this->tree->tqTree_all();
        
        $itemsByReference = array();
        
        // Build array of item references:
        foreach($result as $key => &$item) {
            
            #echo $item['id']; exit();
            $itemsByReference[$item['id']] = &$item;
            
            if(preg_match('/^MENU/', $item['name'])){
                
                $itemsByReference[$item['id']]['permitido'] = 'S';
                
            }
            
            if(in_array($item['id'], $permissoes)){
                $itemsByReference[$item['id']]['permitido'] = 'S';
            }else{
                $itemsByReference[$item['id']]['permitido'] = 'N';
            }
            
            // Children array:
            $itemsByReference[$item['id']]['children'] = array();
            
            // Empty data class (so that json_encode adds "data: {}" )
            $itemsByReference[$item['id']]['data'] = new StdClass();
        }
        #echo '<pre>'; print_r($itemsByReference); exit();
        // Set items as children of the relevant parent item.
        foreach($result as $key => &$item)
            if($item['parent_id'] && isset($itemsByReference[$item['parent_id']]))
        	$itemsByReference [$item['parent_id']]['children'][] = &$item;
        
        // Remove items that were added to parents elsewhere:
        foreach($result as $key => &$item) {
            if($item['parent_id'] && isset($itemsByReference[$item['parent_id']]))
        	unset($result[$key]);
        }
        foreach ($result as $row) {
            $data[] = $row;
        }
        #echo '<pre>'; print_r($itemsByReference); exit();
        // Encode:
        echo json_encode($data);
    }
    
    public function fichaAccordion($cd = false){
        
        if($cd){
            
            $dados = $this->permissaoPerfil->dadosPerfil($cd);
            
            $campos = array_keys($dados);
            
            foreach($campos as $campo){
			 
                #Data
                #if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $dados[$campo])){
                    #$dados[$campo] = $this->util->formataData($dados[$campo],'BR');
                #}
             
				$dados[$campo] = $dados[$campo]; # ALIMENTA OS CAMPOS COM OS DADOS
			}
            
            $permissoesPerfil = $this->permissaoPerfil->permissoesDoPerfil($cd);
            
            foreach($permissoesPerfil as $perPer){
                $permissoesDoPerfil[] = $perPer['cd_permissao'];
            }
            
        }else{
            
            $campos = $this->permissaoPerfil->camposPerfil();
            
            foreach($campos as $campo){
                $dados[$campo] = '';
            }
            
            $permissoesDoPerfil = false;
        
        }
        
        $paiPermissoes = $this->dadosBanco->paiPermissao();
        $permissoes = $this->dadosBanco->permissoes();
        
        $dados['permissoes'] = $this->perfiltree->montaPermissaoAccordion($permissoes, $paiPermissoes, $permissoesDoPerfil);
        #echo '<pre>'; print_r($dados['permissoes']); exit();
        $this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        
        if(in_array(18, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'perfil/view_accordion_frm', $dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    public function salvar(){
        
        echo '<pre>'; print_r($_POST); exit();
        
    }
    
}