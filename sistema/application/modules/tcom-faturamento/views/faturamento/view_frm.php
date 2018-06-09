<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.maskMoney.min.js") ?>"></script>
<?php
echo link_tag(array('href' => 'assets/css/tooltip.css','rel' => 'stylesheet','type' => 'text/css'));
?>

            <div class="col-md-10 col-sm-9">
            <!--<div class="col-lg-12">-->
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a>
                    </li>
                    <li class="active">Ficha <?php echo $assunto; ?></li>
                </ol>
                <div id="divMain">
                    <div class="row">
                        <div class="col-md-12">
                        <h3>Ficha financeira - <?php echo $grupo->titulo; ?>
                            <a href='<?php echo base_url($modulo.'/'.$controller.'/pesq'); ?>' class='linkDireita'>
                                <span class='glyphicon glyphicon-arrow-left'></span>&nbspVoltar
                            </a>
                        </h3>
                        </div>
                    </div>
                <hr />
                    <?php   

                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'frm-salvar');
                    	echo form_open($modulo.'/'.$controller.'/salvar',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
             
                    		echo form_fieldset("T&iacute;tulos em aberto", $attributes);
                    		
                                $colunas = array('<input type="checkbox" id="todos"/>','Compet&ecirc;ncia', 'CNPJ', 'Operadora', 'Nota fiscal','Tipo','Data venc','Valor','Status');
                
                                $this->table->set_heading($colunas);
                                
                                $contCell = 0; 

                                foreach($abertos as $ab){
                                    
                                    $class = ($ab->situacao =='ATRASADO')?'atrasado':'';
                                    
                                    $cell[$contCell++] = array('data' => '<input type="checkbox" name="titulo[]" value="'.$ab->id.'" />');
                                    $cell[$contCell++] = array('data' => $ab->competencia, 'class' => $class);
                                    $cell[$contCell++] = array('data' => $ab->cnpj, 'class' => $class);
                                    $cell[$contCell++] = array('data' => $ab->titulo, 'class' => $class);
                                    $cell[$contCell++] = array('data' => ($ab->nota_fiscal)?$ab->nota_fiscal:'', 'class' => $class);
                                    $cell[$contCell++] = array('data' => $ab->tipo, 'class' => $class);
                                    $cell[$contCell++] = array('data' => ($ab->data_venc)?$ab->data_venc:'', 'class' => $class);
                                    $cell[$contCell++] = array('data' => ($ab->valor_cobrado)?$ab->valor_cobrado:'', 'class' => $class);
                                    $cell[$contCell++] = array('data' => $ab->status, 'class' => $class);

                                    #$botaoEditar = (in_array($perEditarCadastrar, $this->session->userdata('permissoes')))? '<a title="Editar" href="'.base_url($pasta.'/'.$controller.'/ficha/'.$da->$pk.'/'.$da->comp_banco).'" class="glyphicon glyphicon-pencil"></a>': '';
                                    #$botaoExcluir = (in_array($perExcluir, $this->session->userdata('permissoes')))? '<a title="Apagar" href="#" data-toggle="modal" data-target="#apaga" key="'.$da->$pk.'" nome="'.$nome.'" class="del glyphicon glyphicon-remove"></a>': '';
                                    #$cell[$contCell++] = array('data' => $botaoEditar.$botaoExcluir);
                                        
                                    $this->table->add_row($cell);
                                    $contCell = 0; 
                                    
                                }
                                
                                $template = array('table_open' => '<table class="table zebra">');
                                $this->table->set_template($template);
                                echo $this->table->generate();
                                                              
                                echo '<div class="actions">';
                                
                                echo form_hidden('id', $id);
                                
                                #echo form_submit("btn","Salvar", 'class="btn btn-primary pull-right"');
                                echo '</div>';   
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
                        
                        $data = array('class'=>'pure-form','id'=>'frm-salvar');
                    	echo form_open($modulo.'/'.$controller.'/salvar',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
             
                    		echo form_fieldset("T&iacute;tulos pagos", $attributes);
                    		
                                $colunas = array('Compet&ecirc;ncia', 'CNPJ', 'Operadora','Nota fiscal','Tipo','Data venc.','Data pg.','Valor cob.', 'Valor pg.','Status');
                
                                $this->table->set_heading($colunas);
                                
                                $contCell = 0; 
                                foreach($pagos as $pg){

                                    $cell[$contCell++] = array('data' => $pg->competencia);
                                    $cell[$contCell++] = array('data' => $pg->cnpj);
                                    $cell[$contCell++] = array('data' => $pg->titulo);
                                    $cell[$contCell++] = array('data' => ($pg->nota_fiscal)?$pg->nota_fiscal:'');
                                    $cell[$contCell++] = array('data' => $pg->tipo);
                                    $cell[$contCell++] = array('data' => ($pg->data_venc)?$pg->data_venc:'');
                                    $cell[$contCell++] = array('data' => ($pg->data_pagamento)?$pg->data_pagamento:'');
                                    $cell[$contCell++] = array('data' => ($pg->valor_cobrado)?$pg->valor_cobrado:'', 'class' => $class);
                                    $cell[$contCell++] = array('data' => ($pg->valor_pago)?$pg->valor_pago:'', 'class' => $class);
                                    $cell[$contCell++] = array('data' => $pg->status);

                                    #$botaoEditar = (in_array($perEditarCadastrar, $this->session->userdata('permissoes')))? '<a title="Editar" href="'.base_url($pasta.'/'.$controller.'/ficha/'.$da->$pk.'/'.$da->comp_banco).'" class="glyphicon glyphicon-pencil"></a>': '';
                                    #$botaoExcluir = (in_array($perExcluir, $this->session->userdata('permissoes')))? '<a title="Apagar" href="#" data-toggle="modal" data-target="#apaga" key="'.$da->$pk.'" nome="'.$nome.'" class="del glyphicon glyphicon-remove"></a>': '';
                                    #$cell[$contCell++] = array('data' => $botaoEditar.$botaoExcluir);
                                        
                                    $this->table->add_row($cell);
                                    $contCell = 0; 
                                    
                                }
                                
                                $template = array('table_open' => '<table class="table zebra">');
                                $this->table->set_template($template);
                                echo $this->table->generate();
                                                              
                                echo '<div class="actions">';
                                
                                echo form_hidden('id', $id);
                                
                                #echo form_submit("btn","Salvar", 'class="btn btn-primary pull-right"');
                                echo '</div>';   
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
                        
                    ?>       
                </div>
            </div>
    
<script type="text/javascript">

$("#todos").click(function(){
    
    if($(this).prop('checked') == true){
        $('input:checkbox').prop('checked', true);
    }else{
        $('input:checkbox').prop('checked', false);
    }
    
})

//////////////////////////////////////

$(".porcentagem").maskMoney({suffix:'%', allowNegative: true, thousands:'.', decimal:'.', affixesStay: true});

$(document).ready(function(){
    
    $("#infoHab").hide();
    
    // Valida o formul�rio
	$("#frm-salvar").validate({
            debug: false,
            rules: {
                nome: {
                    required: true
                },
                cd_unidade: {
                    required: true
                },
                efetiva: {
                    required: true
                },
                /*base_calculo: {
                    required: true
                },*/
                data_inicio: {
                    required: true
                },
                data_fim: {
                    required: true
                }
            },
            messages: {
                nome: {
                    required: "Informe o nome."
                },
                cd_unidade: {
                    required: "Selecione a opera&ccedil;&atilde;o."
                },
                efetiva: {
                    required: "Informe a porcetagem."
                },
                /*base_calculo: {
                    required: "Informe a base de c&aacute;lculo."
                },*/
                data_inicio: {
                    required: "Informe a data in&iacute;cio."
                },
                data_fim: {
                    required: "Informe a data fim."
                }
            }
        });
    
});

$("#final").change(function(){
   
   if($(this).val() != ''){
    if($(this).val() == 'S'){
        $("#infoHab").show();
    }else{
        $("#infoHab").hide();
    }
   }else{
    $("#infoHab").hide();
   }
    
});

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