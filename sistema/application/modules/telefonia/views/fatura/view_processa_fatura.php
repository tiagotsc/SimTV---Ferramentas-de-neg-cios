<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
            <div class="col-md-10 col-sm-9">
            <!--<div class="col-lg-12">-->
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a></li>
                    <li><a href="<?php echo base_url('telefonia'); ?>">Telefonia</a></li>
                    <li class="active">Faturas</li>
                </ol>
                <div id="divMain">
                    <?php
                        
                        $postOperadora = (isset($postOperadora))? $postOperadora: false;
                        
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'formulario');
                    	echo form_open_multipart('telefonia/processaFatura',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
                            
                    		echo form_fieldset("Processar fatura", $attributes);
                    		  
                                echo '<div class="row">';
                                
                                    echo '<div class="col-md-4">';
                                    $options[''] = '';
                                    $options['CALLCENTER - ATIVO'] = 'Call Center - Ativo';
                                    $options['CALLCENTER - RECEPTIVO - 0800'] = 'Call Center - Receptivo - 0800';
                                    $options['CALLCENTER - RECEPTIVO - 4004'] = 'Call Center - Receptivo - 4004';
                                    foreach($operadoras as $ope){
                                        $options[$ope->cd_telefonia_operadora] = $ope->nome;
                                    }		
                            		echo form_label('Selecione a operadora / Tipo', 'tipo');
                            		echo form_dropdown('tipo', $options, $postOperadora, 'id="tipo" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div id="file" class="col-md-8">';
                                    echo form_label('Selecione o arquivo', 'userfile');
                        			$data = array('name'=>'userfile','id'=>'userfile', 'placeholder'=>'Selecione o arquivo', 'class'=>'form-control');
                        			echo form_upload($data);
                                    echo '</div>';
                                       
                                echo '</div>';                      
                                                                
                                echo '<div class="actions">';
                                echo form_hidden('processar', 'sim');
                                echo form_submit("btn_processar",utf8_encode("Processar"), 'id="btn_processar" class="btn btn-primary pull-right"');
                                $data = array(
                                    'name' => 'button',
                                    'id' => 'button',
                                    'value' => 'true',
                                    'type' => 'button',
                                    'class'=> "btn btn-primary pull-right",
                                    'content' => 'Enviar'
                                );
                                
                                echo form_button($data);
                                echo '</div>';
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
                    
                    ?>        
                </div>
                <br />
                <div id="tempoReal"></div>
                <div id="aguarde" style="text-align: center; display: none"><img src="<?php echo base_url('assets/img/aguarde.gif');?>" /></div>
                <div class="row">&nbsp</div>
                <?php
                $this->table->set_heading('&Uacute;ltimos Arquivos processados', 'Fonte');
                
                foreach($ultimosArquivos as $uA){
                    
                    $cell1 = array('data' => $this->util->formataData($uA->data, 'BR'));
                    $cell2 = array('data' => $uA->fonte);
                    
                    $this->table->add_row($cell1, $cell2);
                    
                }
                
                $template = array('table_open' => '<table class="table zebra">');
            	$this->table->set_template($template);
            	echo $this->table->generate();
                ?>
                
            </div>
    
<script type="text/javascript">

$("#button").click(function(){

    $.post( '<?php echo base_url(); ?>telefonia/processaFatura', { tipo: $("#tipo").val(), processar: 'sim'} );
    
    var intervalo = window.setInterval(
        function(){ 
            $.post( '<?php echo base_url(); ?>ajax/feedbackTempoReal', function( data ) {
              //console.log( data.time ); // 2pm
              $("#tempoReal").html(data.feedback);
              
              if(data.feedbackStatus == 'finalizado'){
                $.post( '<?php echo base_url(); ?>ajax/feedbackTempoRealLimpa' );
                window.clearInterval(intervalo);
                //$(location).attr('href', '<?php echo base_url(); ?>telefonia/faturasArquivos');
              }
              
            }, "json");
        }    
    ,1000);


});

function apagarRegistro(cd, nome){
    $("#apg_cd").val(cd);
    $("#apg_nome").val(nome);
}

$(document).ready(function(){
    
    $("#btn_processar").css('display', 'none');
    $("#button").css('display', 'none');
       
// Valida o formulário
$("#formulario").validate({
	debug: false,
	rules: {
		tipo: {
            required: true
        },
        userfile: {
            required: true
        }
	},
	messages: {
		tipo: {
            required: "Selecione o tipo."
        },
        userfile: {
            required: "Selecione o arquivo"
        }
   }
});   
    
    $("#file").css('display', 'none');
    //$("#btn_processar").css('display', 'none');
    
    $(".data").mask("00/00/0000");
    
    /*$(".actions").click(function() {
        $('#aguarde').css({display:"block"});
    });*/
});


$("#tipo").change(function(){
    
    /*
    if($(this).val() != ''){
        $("#btn_processar").css('display', 'block');
    }else{
        $("#btn_processar").css('display', 'none');
    }
    */
    /*if($(this).val() != '' && $(this).val() != '1'){
        alert(1);
        $( "#userfile" ).rules( "add", {
          required: true
          messages: {
            required: "Selecione o arquivo"
          }
        });
    }else{
        $( "#userfile" ).rules( "remove" );
    }*/
    
    
    $("#formulario").validate();
    
    if(isNaN($(this).val())){ // Verifica se é string (isNaN())
        $("#file").css('display', 'block');
        $("#btn_processar").css('display', 'block');
        $("#button").css('display', 'none');
    }else{
        $("#userfile").val('');
        $("#file").css('display', 'none');
        $("#btn_processar").css('display', 'none');
        $("#button").css('display', 'block');
    }
    
    if($(this).val() == ''){
        $("#button").css('display', 'none');
    }
    
    /*
    if(parseInt($(this).val()) == 8){
        $("#userfile").val('');
        $("#file").css('display', 'none');
        $("#btn_processar").css('display', 'none');
        $("#button").css('display', 'block');
    }
    
    if(parseInt($(this).val()) != 8 && $(this).val() != ''){
        $("#file").css('display', 'block');
        $("#btn_processar").css('display', 'block');
        $("#button").css('display', 'none');
    }
    
    if($(this).val() == ''){
        $("#file").css('display', 'none');
        $("#btn_processar").css('display', 'none');
        $("#button").css('display', 'none');
    }*/
});

/*
CONFIGURA O CALENDÁRIO DATEPICKER NO INPUT INFORMADO
*/
$("#data,#data2").datepicker({
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