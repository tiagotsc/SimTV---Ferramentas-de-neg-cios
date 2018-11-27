<?php
#error_reporting(0);
include_once(APPPATH.'modules/base/controllers/base.php');
#include_once(APPPATH.'controllers/base.php');
if(!defined('BASEPATH')) exit('No direct script access allowed');
#date_default_timezone_set('America/Sao_Paulo');
#setlocale(LC_ALL, 'pt_BR.UTF-8', 'Portuguese_Brazil.1252');
/**
* Classe respons�vel pela usu�rio
*/
class Faltas extends Base{
    
    const pastaView = 'faltas';
    
    
    public function __construct(){

        parent::__construct();

        $this->load->library('Crud', '', 'crud');
        $this->load->library('FaltaLib', '', 'faltaLib');
        $this->load->helper('transporte');
        $this->load->model('base/crud_model','crudModel');
        $this->load->model('administrador/permissaoPerfil_model','permissaoPerfil');
        $this->load->model('rh-usuario/faltas_model', 'faltas');
        $this->load->model('rh-beneficio/beneficio_model','beneficio');

    }
    
    public function cadastraFalta(){
        
        $dados['unidade'] = $this->carregaUnidade($this->beneficio->retornaUnidade());
        
        $this->layout->region('html_header', 'view_html_header');
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        
            $this->layout->region('corpo', self::pastaView.'/cadastro_faltas_view',$dados);
  
        $this->layout->region('rodape', 'view_rodape');
        $this->layout->region('html_footer', 'view_html_footer');
        
        // Ent�o chama o layout que ir� exibir as views parciais...
        $this->layout->show('layout');
    }
    
    
    public function salvaFalta(){
        
//        $vetorFaltas = $this->faltaLib->vetorFalta();
        
//        'data_falta' => date('Y-m-d',  strtotime($this->input->post('data_falta')))
        
        $opc = $this->faltas->salvaFalta($this->faltaLib->vetorFalta());
//        $opc = $this->faltas->salvaFalta($vetorFaltas);
        
        if($opc){
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success">Faltas cadastrada com sucesso</div>');
        }else{
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao savar faltas</div>');
        }
        redirect($_POST['paginaRetorno']);
    }
    
    function carregaUnidade($dados){
        
        $data;
        
        foreach($dados as $dado){
            if($dado->sigla != NULL){
                $data[] = (object) array(
                    'cd_unidade' => $dado->cd_unidade,
                    'nome' => $dado->nome
                );
            }
        }
        
        return $data;
        
    }
   
}