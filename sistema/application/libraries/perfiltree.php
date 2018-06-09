<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
error_reporting(0);
setlocale(LC_ALL, 'pt_BR.UTF-8');
class Perfiltree{
    
    private $contClassMenu = 0;
    private $contClassModulo = 0;
    private $contClassSidebar = 0;
    private $contClassPagina = 0;
    private $cont = 0;
    
    public function __construct(){
        $this->CI =& get_instance();
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
    public function montaPermissaoAccordion($permissoes, $paiPermissoes, $permissoesUsuario = false){
        
        foreach($paiPermissoes as $paiP){
            
            $perm[] = $paiP['pai_permissao'];
            
        }
   
        $this->paiPermissao = $perm;
        
        foreach($permissoes as $permi){
            
            $permItem[$permi->pai_permissao][$permi->cd_permissao] = array('nome'=>$permi->nome_permissao);
            
        } 
        return $this->loopPermissoesAccordion($permItem, 0, 'nao', $permissoesUsuario);
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
    public function loopPermissoesAccordion(array $permissoesTotal , $idPai = 0, $filho = 'nao', $permissoesUsuario = false){
        
        $this->permissoesCompleto .= '';
        $this->cont++;

        foreach( $permissoesTotal[$idPai] as $idPermissao => $permissaoItem){
            
            if(in_array($idPermissao, $permissoesUsuario)){
                $marcado = 'checked';
            }else{
                $marcado = ''; 
            }
            
            $nomeCompleto = strtoupper($this->CI->util->removeAcentos($permissaoItem['nome']));

            //# Se não é filho define class pai
            if(preg_match('/^MENU/', $permissaoItem['nome'])){
                
                if($this->cont != 1 and $idPai == 0){
                $this->permissoesCompleto .= '</div>';
                }
                
                $this->contClassMenu++;
                
                $this->permissoesCompleto .= '<h3 class="accordion-header ui-accordion-header ui-helper-reset ui-state-default ui-accordion-icons ui-corner-all">';
                $this->permissoesCompleto .= '<span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-e"></span>';
                $this->permissoesCompleto .= $nomeCompleto.'</h3>';                
                $this->permissoesCompleto .= '<div class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom">';
                
                $this->permissoesCompleto .= '<h5>';
                $this->permissoesCompleto .= '<input tipo="menu" '.$marcado.' menu="'.$this->contClassMenu.'" type="checkbox" class="menu'.$this->contClassMenu.'" name="permissao[]" value="'.$idPermissao.'" />&nbsp';
                $this->permissoesCompleto .= $nomeCompleto.'</h5>';                
            }else{ # Se é filho define classe filho
                
                if(preg_match('/^MÓDULO/', $permissaoItem['nome'])){
                    $this->contClassModulo++;
                    $tipo="modulo";
                }elseif(preg_match('/^SIDEBAR/', $permissaoItem['nome'])){
                    $this->contClassSidebar++;
                    $this->contClassPagina++;
                    $tipo="sidebar";
                }elseif(preg_match('/^PÁGINA/', $permissaoItem['nome'])){
                    $this->contClassPagina++;
                    $tipo="pagina";
                }else{
                    $tipo="funcao";
                }
                $this->permissoesCompleto .= '<ul class="listaSemEstilo">';
                $this->permissoesCompleto .= '<li>';
                $this->permissoesCompleto .= '<h5>';
                $this->permissoesCompleto .= '<input '.$marcado.' tipo="'.$tipo.'" menu="'.$this->contClassMenu.'" modulo="'.$this->contClassModulo.'" sidebar="'.$this->contClassSidebar.'" pagina="'.$this->contClassPagina.'" type="checkbox" name="permissao[]" value="'.$idPermissao.'" />&nbsp';
                $this->permissoesCompleto .= $nomeCompleto.'</h5>';
                $this->permissoesCompleto .= '</li>';
            }

            if( isset( $permissoesTotal[$idPermissao] ) ) $this->loopPermissoesAccordion($permissoesTotal,$idPermissao, 'sim', $permissoesUsuario);
            $this->permissoesCompleto .= '</ul>';
            
        }

        return $this->permissoesCompleto;
        
    }
    
 }