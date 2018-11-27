<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
            <!--<div class="col-md-9 col-sm-8"> USADO PARA SIDEBAR -->
            <div class="col-lg-12">
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a>
                    </li>
                    <li class="active">Anatel - Gerar XML</li>
                </ol>
                <div id="divMain">
                    <?php
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'gerar_xml');
                    	echo form_open('anatel/geraXml',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
                            
                    		echo form_fieldset("Gerar XML", $attributes);
                    		
                                echo '<div class="row">';
                                    
                                    echo '<div class="col-md-4">';
                                    $options = array('' => '');	
                                    foreach($tipos_frm_anatel as $tFa){
                                        $options[$tFa->cd_anatel_tipo_frm] = $tFa->nome;
                                    }
                            		echo form_label('Tipo Sistema<span class="obrigatorio">*</span>', 'tipo_sistema');
                            		echo form_dropdown('tipo_sistema', $options, $this->input->post('tipo_sistema'), 'id="tipo_sistema" class="form-control"');
                                    echo '</div>';
                                
                                    echo '<div class="col-md-4">';
                                    $options = array('' => '');	

                            		echo form_label('Tipo de XML<span class="obrigatorio">*</span>', 'tipo_xml');
                            		echo form_dropdown('tipo_xml', $options, $this->input->post('tipo_xml'), 'id="tipo_xml" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    $options = array('' => '');	
                                    foreach($operadoras as $operadora){
                                        $options[$operadora->id_operadora] = $operadora->id_operadora.' - '.$operadora->empresa;
                                    }
                            		echo form_label('ID operadora<span class="obrigatorio">*</span>', 'operadora');
                            		echo form_dropdown('operadora', $options, $this->input->post('operadora'), 'id="operadora" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    echo form_label('M&ecirc;s/Ano', 'mes_ano');
                            		$data = array('id'=>'mes_ano', 'name'=>'mes_ano', 'value' => $this->input->post('mes_ano'), 'class'=>'form-control data');
                            		echo form_input($data,'');
                                    echo '</div>';
                                    
                                echo '</div>';    
                                                                
                                echo '<div class="actions">';
                                echo form_submit("btn_xml","Gerar XML", 'class="btn btn-primary pull-right"');
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
    
    // Completa formula da equação
    $("#tipo_sistema").change(function(){
        
        if($(this).val() != ''){
            
            $.ajax({
              type: "POST",
              url: '<?php echo base_url(); ?>ajax/tipoXmlAnatelFrm',
              data: {
                tipo_frm: $(this).val()
              },
              dataType: "json",
              error: function(res) {
 	            //$("#resQtdArquivosDia").html('<span>Erro de execução</span>');
                alert('erro');
              },
              success: function(res) {
                //$("#tipo_xml option[value='']").html('Carregando...'); // Coloca 'Carregando' no 1ª option 
                $("#tipo_xml option:first").html('Carregando...'); // Coloca 'Carregando' no 1ª option 
                if(res.length > 0){
                
                    $.each(res, function() {
                      
                      $("#tipo_xml").append('<option value="'+this.cd_anatel_xml+'">'+this.nome+'</option>');
                      
                    });
                    
                }
                
                //$("#tipo_xml option[value='']").html(''); // Remove 'Carregando' do 1ª option
                $("#tipo_xml option:first").html(''); // Coloca 'Carregando' no 1ª option 
                
              }
              
              
            });
            
        }else{
            $('#tipo_xml').children('option:not(:first)').remove(); // Remove todos os options exceto o 1ª
        }
            
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
	$("#gerar_xml").validate({
		debug: false,
		rules: {
            tipo_sistema: {
                required: true
            },
			tipo_xml: {
                required: true
            },
            operadora: {
                required: true
            },
            mes_ano: {
                required: true,
                minlength: 7
            }
            
		},
		messages: {
            tipo_sistema: {
                required: "Selecione o tipo de sistema."
            },
			tipo_xml: {
                required: "Selecione o tipo de XML."
            },
            operadora: {
                required: "Selecione o ID da operadora."
            },
            mes_ano: {
                required: "Informe o m&ecirc;s e ano",
                minlength: "Informe no formato (MM/YYYY)"
            }
	   }
   });   
   
});

</script>