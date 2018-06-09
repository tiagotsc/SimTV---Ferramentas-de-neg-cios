<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
<?php
echo link_tag(array('href' => 'assets/js/dropzone/dropzone.css','rel' => 'stylesheet','type' => 'text/css')); 
echo "<script type='text/javascript' src='".base_url('assets/js/dropzone/dropzone.js')."'></script>";
?>
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
                        $data = array('class'=>'pure-form dropzone','id'=>'formulario');
                    	echo form_open_multipart('telefonia/processaFatura',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
                            
                    		echo form_fieldset("Processar fatura", $attributes);
                    		  
                                echo '<div class="row">';
                                
                                echo '<div class="fallback">
                                    <input name="file" type="file" multiple />
                                  </div>';
                                       
                                echo '</div>';                      
                                                                
                                echo '<div class="actions">';
                                
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
       
// Valida o formul�rio
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
    
    $("#formulario").validate();
    
    if(isNaN($(this).val())){ // Verifica se � string (isNaN())
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
    
});

/*
CONFIGURA O CALEND�RIO DATEPICKER NO INPUT INFORMADO
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
    
    // Traz o calend�rio input datepicker para frente da modal
    beforeShow :  function ()  { 
        setTimeout ( function (){ 
            $ ( '.ui-datepicker' ). css ( 'z-index' ,  99999999999999 ); 
        },  0 ); 
    } 
});

</script>