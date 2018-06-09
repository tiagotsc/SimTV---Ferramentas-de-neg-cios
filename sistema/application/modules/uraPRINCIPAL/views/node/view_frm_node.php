<?php
echo link_tag(array('href' => 'assets/js/drag_drop/style.css','rel' => 'stylesheet','type' => 'text/css'));

echo link_tag(array('href' => 'assets/js/jquery.ui.timepicker.addon/jquery-ui-timepicker-addon.min.css','rel' => 'stylesheet','type' => 'text/css'));
echo link_tag(array('href' => 'assets/js/jquery.ui.timepicker.addon/jquery-ui-timepicker-addon.css','rel' => 'stylesheet','type' => 'text/css'));

#echo "<script type='text/javascript' src='".base_url('assets/js/jquery.blockui/jquery.block.ui.js')."'></script>";
?>

<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.ui.timepicker.addon/jquery-ui-timepicker-addon.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.ui.timepicker.addon/jquery-ui-sliderAccess.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.ui.timepicker.addon/jquery-ui-timepicker-addon.js") ?>"></script>

<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/drag_drop/fieldChooser.js") ?>"></script>

            <!--<div class="col-md-9 col-sm-8"> USADO PARA SIDEBAR -->
            <div class="col-lg-12">
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a>
                    </li>
                    <li class="active">Ficha node</li>
                </ol>
                <div id="divMain">
                    <?php   #echo '<pre>'; print_r($permissor); exit();
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'salvar_node');
                    	echo form_open('ura/salvarNode',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
             
                    		echo form_fieldset("Ficha node<a href='".base_url('ura/node')."' class='linkDireita'><span class='glyphicon glyphicon-arrow-left'></span>&nbspVoltar Pesquisar</a>", $attributes);
                    		
                                echo '<div class="row">';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array('' => '');		
                            		foreach($permissor as $per){
                      		            
                                        if($per->permissor != ''){                                        
                          			       $options[$per->permissor] = $per->permissor.' - '.htmlentities($per->nome);
                                        }                                      
                            		}	
                            		echo form_label('Permissor<span class="obrigatorio">*</span>', 'cd_permissor');
                            		echo form_dropdown('cd_permissor', $options, '', 'id="cd_permissor" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div id="node" class="col-md-3">';
                                    $options = array('' => '');		
                            		/*foreach($permissor as $per){
                            			$options[$per->permissor] = $per->nome;
                            		}*/	
                            		echo form_label('Node<span class="obrigatorio">*</span>', 'dados_node');
                            		echo form_dropdown('dados_node', $options, '', 'id="dados_node" class="form-control"');
                                    echo '</div>';
                                
                                    echo '<div class="col-md-2">';
                                    echo form_label('Data fim<span class="obrigatorio">*</span>', 'data_fim');
                        			$data = array('name'=>'data_fim', 'value'=>$data_fim,'id'=>'data_fim', 'placeholder'=>'Informe a data', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    echo form_label('Hora fim<span class="obrigatorio">*</span>', 'hora_fim');
                        			$data = array('name'=>'hora_fim', 'value'=>$data_fim,'id'=>'hora_fim', 'placeholder'=>'Informe a hora', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    $options = array('Ativo' => 'Ativo', 'Inativo' => 'Inativo');		
                            		echo form_label('Status', 'status');
                            		echo form_dropdown('status', $options, $postStatus, 'id="status" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-12">';
                                    echo form_label('Observa&ccedil;&atilde;o', 'observacao');
                                      echo '<textarea id="observacao" name="observacao" rows="5" cols="10" class="form-control">'.utf8_decode($observacao).'</textarea>';
                                    echo '</div>'; 
                                    
                                echo '</div>';
                                                              
                                echo '<div class="actions">';
                                
                                #echo form_hidden('cd_usuario', $cd_usuario);
                                
                                echo form_submit("btn_cadastro","Salvar", 'class="btn btn-primary pull-right"');
                                echo '</div>';   
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
                        
                    ?>       
                </div>
            </div>
    
<script type="text/javascript">

function dump(obj) {
    var out = '';
    for (var i in obj) {
        out += i + ": " + obj[i] + "\n";
    }
    alert(out);
}

function marcaTodos(){
    
    if($('#todos').prop('checked') == true){
        $('input:checkbox').prop('checked', true);
    }else{
        $('input:checkbox').prop('checked', false);
    }
    
}

function marcaGrupo(classe, campo){
    
    if(campo.checked == true){
        $(classe).prop('checked', true);
    }else{
        $(classe).prop('checked', false);
    }

}

$(document).ready(function(){
    
    $(".matricula").mask("000000000000");
    $("#data_fim").mask("00/00/0000");
    $("#hora_fim").mask("00:00:00");
    $(".rg").mask("00.000.000.0");
    $(".cpf").mask("000.000.000-00");
    
    $("#node").hide();    
        
    $(".actions").click(function() {
        $('#aguarde').css({display:"block"});
    });
    
    $("#cd_permissor").change(function(){
        
        if($(this).val() != ''){
            
            $("#node").show();
            $("#dados_node").html('<option value="">AGUARDE...</option>');
                
            $.ajax({
              type: "POST",
              url: '<?php echo base_url(); ?>ajax/carregaNodes',
              data: {
                permissor: $("#cd_permissor").val(),
              },
              dataType: "json",
              /*error: function(res) {
                $("#resMarcar").html('<span>Erro de execução</span>');
              },*/
              success: function(res) {
                
                if(res.length > 0){
                
                content = '<option value=""></option>';
                
                $.each(res, function() {
                  
                  content += '<option value="'+ this.MANNRO +'-'+ this.MANDSC +'">'+ this.MANNRO +' - '+ this.MANDSC +'</option>';
                  
                });
                
                $("#dados_node").html('');
                $("#dados_node").append(content);
                
                }else{
                    $("#dados_node").html('');
                    $("#node").hide();
                }
                
              }
            });
            
        }else{
            
            $("#node").hide();
            $("#dados_node").html('');
            
        }
        
    })
    
});

$('#hora_fim').timepicker({
    timeText: 'Hor&aacute;rio',
    hourText: 'Hora',
    minuteText: 'Minuto',
    currentText: 'Atual',
    closeText: 'Fechar',
    timeFormat: 'HH:mm',
    // year, month, day and seconds are not important
    //minTime: new Date(0, 0, 0, 8, 0, 0),
    //maxTime: new Date(0, 0, 0, 15, 0, 0),
    // time entries start being generated at 6AM but the plugin 
    // shows only those within the [minTime, maxTime] interval
    startHour: 6,
    // the value of the first item in the dropdown, when the input
    // field is empty. This overrides the startHour and startMinute 
    // options
    //startTime: new Date(0, 0, 0, 8, 20, 0),
    // items in the dropdown are separated by at interval minutes
    interval: 30
});
/*
CONFIGURA O CALENDÁRIO DATEPICKER NO INPUT INFORMADO
*/
$("#data_fim").datepicker({
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

$(document).ready(function(){
    
    // Valida o formulário
	$("#salvar_node").validate({
		debug: false,
		rules: {
            cd_permissor: {
                required: true
            },
			dados_node: {
                required: true
            },
            data_fim: {
                required: true
			},
            hora_fim: {
                required: true
			}
		},
		messages: {
            cd_permissor: {
                required: "Selecione o permissor."
            },
			dados_node: {
                required: "Selecione o node."
            },
            data_fim: {
                required: "Informe a data fim."
            },
            hora_fim: {
                required: "Informe a hora fim."
            }
	   }
   });   
   
});
</script>