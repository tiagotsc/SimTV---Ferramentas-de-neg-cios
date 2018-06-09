<?php
echo "<script type='text/javascript' src='".base_url('assets/js/jquery.blockui/jquery.block.ui.js')."'></script>";
?>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
            <!--<div class="col-md-9 col-sm-8"> USADO PARA SIDEBAR -->
            <div class="col-lg-12">
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a>
                    </li>
                    <li class="active">Anatel - Cobra resposta do formul&aacute;rio</li>
                </ol>
                <div id="divMain">
                    <?php
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'gerar_xml');
                    	echo form_open('anatel/enviarEmail',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
                            
                    		echo form_fieldset("Enviar e-mail cobrando unidades", $attributes);
                    		
                                echo '<div class="row">';
                                    
                                    echo '<div class="col-md-2">';
                                    echo form_label('M&ecirc;s/Ano', 'mes_ano');
                            		$data = array('id'=>'mes_ano', 'name'=>'mes_ano', 'class'=>'form-control data');
                            		echo form_input($data,'');
                                    echo '</div>';
                                    
                                echo '</div>';
                                /*
                                echo '<div class="row">';    
                                
                                    echo '<div class="col-md-12">';
                                    echo form_label('Mensagem<span class="obrigatorio">*</span>', 'msg');
                        			$data = array('name'=>'msg', 'value'=>$query_relatorio,'id'=>'msg', 'class'=>'form-control');
                        			echo form_textarea($data);
                                    echo '</div>';
                                
                                echo '<div>';  
                                */
                                echo '<div class="row">'; 
                                echo '<div id="unidadesUsuarios" class="col-md-12">';
                                echo '</div>';
                                echo '</div>';  
                                                                
                                echo '<div class="actions">';
                                echo form_submit("btn_xml","Envia e-mail", 'class="btn btn-primary pull-right"');
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

$("#mes_ano").keyup(function(){
    
    if($(this).val() != "" && $(this).val().length == 7){
        
        $(this).listaUnidadesUsuarios();      
        
    }else{
        
        $("#unidadesUsuarios").html('');
        
    }
    
});

$.fn.listaUnidadesUsuarios = function() {

    $.ajax({
      type: "POST",
      url: '<?php echo base_url(); ?>ajax/anatelUnidadesNaoResponderam',
      data: {
        mes_ano: $("#mes_ano").val()
      },
      dataType: "json",
      error: function(res) {
        //$("#resQtdArquivosDia").html('<span>Erro de execução</span>');
        alert('erro');
      },
      success: function(res) {
        
        var conteudo = '';
        if(res.length > 0){
            $(this).carregando(1000);
            conteudo += '<table class="table zebra">';
            conteudo += '<thead>';
            conteudo += '<tr>';
            conteudo += '<th>Sistema</th>';
            conteudo += '<th>Indicador</th>';
            conteudo += '<th>Departamento</th>';
            conteudo += '<th>Unidade</th>';
            conteudo += '<th>Nome</th>';
            conteudo += '<th>E-mail</th>';
            conteudo += '</tr>';
            conteudo += '</thead>';
            conteudo += '<tbody>';
            $.each(res, function() {
              
              $("#cd_anatel_xml").append('<option value="'+this.cd_anatel_xml+'">'+this.nome+'</option>');
              conteudo += '<tr>';
              conteudo += '<td>'+this.tipo_sistema+'</td>';
              conteudo += '<td>'+this.indicador+'</td>';
              conteudo += '<td>'+this.nome_departamento+'</td>';
              conteudo += '<td>'+this.unidade+'</td>';
              conteudo += '<td>'+this.nome_usuario+'</td>';
              conteudo += '<td>'+this.email_usuario+'<input type="hidden" name="email[]" value="'+this.email_usuario+'" /></td>';
              conteudo += '</tr>';
              
            });
            conteudo += '</tbody>';
            conteudo += '</table>';
            
        }else{
            
            conteudo = '';
            
        }
        
        $("#unidadesUsuarios").html(conteudo);
        
      }
      
    });

};

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
    },
    onSelect: function (dateText, inst){
        
        if($("#mes_ano").val().length == 7){
            
            $(this).listaUnidadesUsuarios();
         
        }else{
            $("#unidadesUsuarios").html('');
        }
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
			cd_anatel_xml: {
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
			cd_anatel_xml: {
                required: "Selecione o tipo de XML."
            },
            mes_ano: {
                required: "Informe o m&ecirc;s e ano",
                minlength: "Informe a data no formato (MM/YYYY)"
            }
	   }
   });   
   
});

$.fn.carregando = function(tempo) {
    
    $.blockUI({ 
    message:  '<h1>Carregando...</h1>',
    css: { 
    	border: 'none', 
    	padding: '15px', 
    	backgroundColor: '#000', 
    	'-webkit-border-radius': '10px', 
    	'-moz-border-radius': '10px', 
    	opacity: .5, 
    	color: '#fff' 
    	} 
    }); 
    
    setTimeout($.unblockUI, tempo);    
    
}

</script>