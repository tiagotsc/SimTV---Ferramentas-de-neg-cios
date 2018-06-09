<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
            <!--<div class="col-md-9 col-sm-8"> USADO PARA SIDEBAR -->
            <div class="col-lg-12">
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a>
                    </li>
                    <li class="active">Anatel - Calcular e Salvar IRS</li>
                </ol>
                <div id="divMain">
                    <?php
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'calcular_irs');
                    	echo form_open('anatel/salvarCalculoIRS',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
                            
                    		echo form_fieldset("Calcular e Salvar IRS", $attributes);
                    		
                                echo '<div class="row">';
                                    
                                    echo '<div class="col-md-2">';
                                    echo form_label('M&ecirc;s/Ano', 'mes_ano');
                            		$data = array('id'=>'mes_ano', 'name'=>'mes_ano', 'class'=>'form-control data');
                            		echo form_input($data,'');
                                    echo '</div>';
                                    
                                echo '</div>';    
                                                                
                                echo '<div class="actions">';
                                echo form_submit("btn_irs","Calcular e salvar", 'class="btn btn-primary pull-right"');
                                echo '</div>';
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
                    
                    ?>        
                </div>
                
            </div>
    
<script type="text/javascript">

$(document).ready(function(){
    
    $(".data").mask("00/0000");
    
    $(".actions").click(function() {
        $('#aguarde').css({display:"block"});
    });
    
});


/*
CONFIGURA O CALENDÁRIO DATEPICKER NO INPUT INFORMADO
*/
$(".data,#data2").datepicker({
	dateFormat: 'mm/yy',
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
	$("#calcular_irs").validate({
		debug: false,
		rules: {
            mes_ano: {
                required: true,
                minlength: 7
            }
            
		},
		messages: {
            mes_ano: {
                required: "Informe o m&ecirc;s e ano",
                minlength: "Informe no formato (MM/YYYY)"
            }
	   }
   });   
   
});

</script>