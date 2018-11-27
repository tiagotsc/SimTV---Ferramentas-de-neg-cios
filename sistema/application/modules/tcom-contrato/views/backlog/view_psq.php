<?php 
echo link_tag(array('href' => 'assets/css/tooltip.css','rel' => 'stylesheet','type' => 'text/css'));
?>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.maskMoney.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.blockui/jquery.block.ui.js') ?>"></script>
    
            <!--<div id="corpo" class="col-md-10 col-sm-9">-->
            <div class="col-lg-12">
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a></li>
                    <li><a href="<?php echo base_url('tcom'); ?>"><?php echo strtolower($assunto); ?></a></li>
                    <li class="active"><?php echo $titulo; ?></li>
                </ol>
                <div id="divMain">
                    <?php
                        
                        $numero = (isset($numero))? $numero: false;
                        $historico = (isset($historico))? $historico: false;
                        $cd_unidade = (isset($cd_unidade))? $cd_unidade: false;
                        $idViabTipo = (isset($idViabTipo))? $idViabTipo: false;
                        
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'pesquisar');
                    	echo form_open($pasta.'/'.$controller.'/pesq',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
                            
                            $botaoVoltar = "<a href='".base_url($modulo.'/contrato/pesq')."' class='linkDireita'><span class='glyphicon glyphicon-arrow-left'></span>&nbspVoltar contratos</a>";
                            
                    		echo form_fieldset($titulo.$botaoVoltar, $attributes);
                    		  
                                echo '<div class="row">';
                                    
                                    echo '<div class="col-md-3">';
                                    #print_r(array_keys($listaBancos));
                                    echo form_label('Número do contrato', 'numero');
                        			$data = array('name'=>'numero', 'value'=>$numero,'id'=>'numero', 'placeholder'=>'Digite o número', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array('' => '');		
                            		foreach($historicoStatus as $histStatus){
                    			       $options[$histStatus->id] = htmlentities($histStatus->nome);                                     
                            		}	
                            		echo form_label('Status Histórico', 'historico');
                            		echo form_dropdown('historico', $options, $historico, 'id="status" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array('' => '');		
                            		foreach($permissor as $per){
                      		            
                                        if($per->permissor != ''){                                        
                          			       $options[$per->cd_unidade] = $per->permissor.' - '.htmlentities($per->nome);
                                        }                                      
                            		}	
                            		echo form_label('Permissor', 'cd_unidade');
                            		echo form_dropdown('cd_unidade', $options, $cd_unidade, 'id="cd_unidade" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array('' => '');		
                            		foreach($viabTipo as $viabT){                                
                    			       $options[$viabT->id] = htmlentities($viabT->nome);                                    
                            		}	
                            		echo form_label('Tipo de solicitação', 'idViabTipo');
                            		echo form_dropdown('idViabTipo', $options, $idViabTipo, 'id="idViabTipo" class="form-control"');
                                    echo '</div>';
                                    
                                       
                                echo '</div>';                      
                                                                
                                echo '<div class="actions">';
                                echo form_submit("btn",utf8_encode($titulo), 'class="btn btn-primary pull-right"');
                                echo '</div>';
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
                    
                    ?>    
                </div>
                
                <div class="row">&nbsp</div>
                <div class="well">
                    <div class="row">
                        <!--
                        <div class="col-md-2"> 
                        <span class="ok glyphicon glyphicon-asterisk" aria-hidden="true"></span> Conclu&iacute;do<br />
                        </div>
                        -->
                        <div class="col-md-2"> 
                        <span class="pendente glyphicon glyphicon-asterisk" aria-hidden="true"></span> Pendente<br />
                        </div>
                        <div class="col-md-2"> 
                        <span class="atrasado glyphicon glyphicon-asterisk" aria-hidden="true"></span> Atrasado<br />
                        </div>
                    </div>
                <p>
                    <strong>Mostrando <?php echo ($qtdDadosCorrente)? $qtdDadosCorrente: 0; ?> de <?php echo ($qtdRegistos)? $qtdRegistos: 0; ?> registros localizados.</strong>
                </p>
                <?php 
                $colunas = array();
                $contCell = 0; 
                
                foreach($campos as $nome){     
                    
                    if($nome == $id){
                        $pk = $nome;
                    }
                    
                    if($nome != $id){
                    
                        if($sort_by == $nome){
                            $class = "sort_$sort_order";
                            
                            if($sort_order == 'asc'){
                                // Crescente
                                $icoAscDesc = '&nbsp<span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span>';
                            }else{
                                // Descrecente
                                $icoAscDesc = '&nbsp<span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span>';
                            }
                            
                        }else{
                            $class = "";
                            $icoAscDesc = '';
                        }
                        
                        $label = (isset($camposLabel[$nome]))? $camposLabel[$nome]: $nome;
                        
                        $colunas[] = anchor($pasta.'/'.$controller.'/'.$metodo.'/'.$post.'/'.$nome.'/'.(($sort_order == 'asc' && $sort_by == $nome) ? 'desc' : 'asc') ,$label.$icoAscDesc, array('class' => $class));
                        
                    }
                    
                }
                
                $colunas[] = 'A&ccedil;&atilde;o';
                
                $this->table->set_heading($colunas);
                
                foreach($dados as $da){
                    
                    if($da->andamento == 'CONCLUIDO'){
                        $classSituacao = 'ok';
                    }elseif($da->andamento == 'PENDENTE'){
                        $classSituacao = 'pendente';
                    }else{
                        $classSituacao = 'atrasado';
                    }
                    
                    $campo1 = strtolower($campos[1]);
                    $campo2 = strtolower($campos[2]);

                    $nome = htmlentities($da->$campo1.' - '.$da->$campo2);
                    
                    foreach($campos as $campo => $valor){
                        $valor = strtolower($valor);
                        if($campo != $id){
                            
                            #if(in_array($valor, array('designacao'))){
                                #$cell[$contCell++] = array('data' => '<span title="'.htmlentities($da->$valor).'">'.htmlentities(substr($da->$valor,0, 8)).'</span>', 'class' => $classSituacao );
                            #}else{
                                $cell[$contCell++] = array('data' => ucfirst(htmlentities($da->$valor)), 'class' => $classSituacao );
                            #}
                            
                        }
                    
                    }
                    
                    $botaoHistorico = (in_array($perVisualizarHistorico, $this->session->userdata('permissoes')))? '<a title="Histórico" target="_blank" href="'.base_url('tcom-viabilidade-resp-hist/viabilidadeRespHist/listarHistorico/'.$da->idViabResp).'" class="historico glyphicon glyphicon-list"></a>': '';
                    $botaoImprimir = (in_array($perImprimir, $this->session->userdata('permissoes')))? '<a title="Imprimir" target="_blank" href="'.base_url($pasta.'/contrato/imprimir/'.$da->$pk).'" class="glyphicon glyphicon-print"></a>': '';

                    $cell[$contCell++] = array('data' => $botaoImprimir.$botaoHistorico);
                        
                    $this->table->add_row($cell);
                    $contCell = 0; 
                    
                }
    
                $template = array('table_open' => '<table class="table zebra">');
            	$this->table->set_template($template);
            	echo $this->table->generate(); 
                echo "<ul class='pagination pagination-lg'>" . utf8_encode($paginacao) . "</ul>";
                ?>
                </div>
                
            </div>
            
<script type="text/javascript">

$.fn.carregando = function(msg) {
    $(document).ajaxStart(
        $.blockUI({ 
        message:  '<h1>'+msg+'...</h1>',
        css: { 
        	border: 'none', 
        	padding: '15px', 
        	backgroundColor: '#000', 
        	'-webkit-border-radius': '10px', 
        	'-moz-border-radius': '10px', 
        	opacity: .5, 
        	color: '#fff',
            'z-index': '99999999999999' 
        	} 
        })
    ); 
    
    //setTimeout($.unblockUI, tempo);   
    //$(document).ajaxStart($.blockUI);
    
}


$(function() {
$( document ).tooltip({
  position: {
    my: "center bottom-20",
    at: "center top",
    using: function( position, feedback ) {
      $( this ).css( position );
      $( "<div>" )
        .addClass( "arrow" )
        .addClass( feedback.vertical )
        .addClass( feedback.horizontal )
        .appendTo( this );
    }
  }
});
});


$(".del").click(function(){
   $("#apg_id").val($(this).attr('key'));
   $("#apg_nome").val($(this).attr('nome'));
});

function apagarRegistro(id, nome){
    $("#apg_id").val(id);
    $("#apg_nome").val(nome);
}

$(document).ready(function(){
    
    // Valida o formulário
	$("#frm-anexar").validate({
		debug: false,
		rules: {
            anexo: {
                required: true
            }
		},
		messages: {
            anexo: {
                required: "Selecione um arquivo."
            }
	   }
   });
   
   // Valida o formulário
/*	$("#altStatus").validate({
		debug: false,
		rules: {
            alt_data_fim: {
                required: true
            }
		},
		messages: {
            alt_data_fim: {
                required: "Informe a data fim."
            }
	   }
   });*/
    
});

$(".data").datepicker({
	dateFormat: 'dd/mm/yy',
	dayNames: ['Domingo','Segunda','Ter&ccedil;a','Quarta','Quinta','Sexta','S&aacute;bado','Domingo'],
	dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
	dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','S&aacute;b','Dom'],
	monthNames: ['Janeiro','Fevereiro','Mar&ccedil;o','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
	monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
	nextText: 'Pr&oacute;ximo',
	prevText: 'Anterior',
    
    // Traz o calendário input datepicker para frente da modal
    beforeShow :  function ()  { 
        setTimeout ( function (){ 
            $ ( '.ui-datepicker' ). css ( 'z-index' ,  99999999999999 ); 
        },  0 ); 
    } 
});

</script>