<?php
echo link_tag(array('href' => 'assets/js/drag_drop/style.css','rel' => 'stylesheet','type' => 'text/css'));
#echo "<script type='text/javascript' src='".base_url('assets/js/jquery.blockui/jquery.block.ui.js')."'></script>";
?>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/drag_drop/fieldChooser.js") ?>"></script>

            <div class="col-md-10 col-sm-9">
            <!--<div class="col-lg-12">-->
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a></li>
                    <li><a href="<?php echo base_url('telefonia'); ?>">Telefonia</a></li>
                    <li class="active">Ficha aparelho</li>
                </ol>
                <div id="divMain">
                    <?php
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'frm-salvar');
                    	echo form_open('telefonia/salvaAparelho',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
             
                    		echo form_fieldset("Ficha Aparelho<a href='".base_url('telefonia/aparelhos')."' class='linkDireita'><span class='glyphicon glyphicon-arrow-left'></span>&nbspVoltar Pesquisar</a>", $attributes);
                    		
                                echo '<div class="row">';
                                
                                    echo '<div class="col-md-3">';
                                    $options = array('' => '');		
                                    foreach($marcas as $ma){
                                        $options[$ma->cd_telefonia_marca] = $ma->nome;
                                    }
                            		echo form_label('Marca<span class="obrigatorio">*</span>', 'cd_telefonia_marca');
                            		echo form_dropdown('cd_telefonia_marca', $options, $cd_telefonia_marca, $disabled.' id="cd_telefonia_marca" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    echo form_label('Modelo<span class="obrigatorio">*</span>', 'modelo');
                        			$data = array('name'=>'modelo', 'value'=>$modelo,'id'=>'modelo', $readonly => true, 'placeholder'=>'Digite o nome', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    $options = array('CEL' => 'Celular', 'INT' => 'Interface');		
                            		echo form_label('Tipo<span class="obrigatorio">*</span>', 'tipo');
                            		echo form_dropdown('tipo', $options, $tipo, $disabled.' id="tipo" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    echo form_label('Data in&iacute;cio', 'data_inicio');
                        			$data = array('name'=>'data_inicio', 'value'=>$this->util->formataData($data_inicio, 'BR'),'id'=>'data_inicio', $readonly => true, 'placeholder'=>'Informa a data', 'class'=>'form-control data');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    echo form_label('Data fim', 'data_fim');
                        			$data = array('name'=>'data_fim', 'value'=>$this->util->formataData($data_fim, 'BR'),'id'=>'data_fim', $readonly => true, 'placeholder'=>'Informa a data', 'class'=>'form-control data');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    echo form_label('Nota Fiscal', 'nota_fiscal');
                        			$data = array('name'=>'nota_fiscal', 'value'=>$nota_fiscal, $readonly => true, 'id'=>'nota_fiscal', 'placeholder'=>'Digite a nota fiscal', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    
                                    if($cd_telefonia_aparelho){
                                        if($status == 'Ativo' or $status == 'Avariado'){
                                            if($status == 'Avariado'){
                                                $options = array('Ativo' => 'Ativo', 'Estoque' => 'Estoque', 'Avariado' => 'Avariado', 'Furtado' => 'Furtado', 'Baixa Estoque' => 'Baixa Estoque');
                                            }else{
                                                $options = array('Ativo' => 'Ativo', 'Avariado' => 'Avariado', 'Furtado' => 'Furtado', 'Baixa Estoque' => 'Baixa Estoque');
                                            }
                                        }else{
                                            $options = array('Estoque' => 'Estoque', 'Avariado' => 'Avariado', 'Furtado' => 'Furtado', 'Baixa Estoque' => 'Baixa Estoque');
                                        }
                                        #$options = array('Estoque' => 'Estoque', 'Ativo' => 'Ativo', 'Avariado' => 'Avariado');
                                    }else{
                                        $options = array('Estoque' => 'Estoque');
                                    }
                                    	
                            		echo form_label('Status<span class="obrigatorio">*</span>', 'status');
                            		echo form_dropdown('status', $options, $status, $disabled.' id="status" class="form-control"');
                                    echo '</div>';
                                    
                                echo '</div>';
                                if(!$visualizar){
                                echo '<div class="row">';
                                echo '<div class="col-md-12"><a id="add-imei" href="#">Adicionar IMEI</a></div>';
                                echo '</div>';
                                }
                                echo '<div id="imeis">';
                                if($imeis){
                                    $cont = 0;
                                    foreach($imeis as $imei){
                                        echo '<div class="row">';
                                        echo '<div class="col-md-3">';
                                        echo form_label('IMEI', 'imei['.$cont.']');
                            			$data = array('name'=>'imei['.$cont.']', 'value'=>htmlentities($imei->imei),'id'=>'imei['.$cont.']', $readonly => true, 'placeholder'=>'Digite o IMEI', 'class'=>'form-control');
                            			echo form_input($data);
                                        echo '</div>';
                                        if(!$visualizar){
                                        echo '<div class="col-md-2">';
                                        echo '<a onclick="confirmacao(this)" href="#" class="glyphicon glyphicon glyphicon glyphicon-remove"></a>';
                                        echo '</div>';
                                        }
                                        echo '</div>';
                                    }  
                                }
                                echo '</div>';
                                
                                if(!$visualizar){                              
                                echo '<div class="actions">';
                                
                                echo form_hidden('cd_telefonia_aparelho', $cd_telefonia_aparelho);
                                
                                echo form_submit("btn_cadastro","Salvar", 'class="btn btn-primary pull-right"');
                                echo '</div>';  
                                } 
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
                    ?>      
                </div>
            </div>
    
<script type="text/javascript">

function confirmacao(elemento) {
    var r = confirm("<?php echo utf8_encode('Deseja remover esse IMEI?\nCaso sim, click no botão \'Salvar\' para confirmar a alteração.');?>");
    if (r == true) {
        $(elemento).parent().parent().remove();
    } 
}

$("#add-imei").click(function(){
    event.preventDefault();
    var cont = $('[name*="imei"]').length;
    
    if(cont == 2){
        alert('<?php echo utf8_encode("Limite máximo alcançado");?>');
        return false;
    }
    
   var inputIMEI = '<div class="row">';
   inputIMEI += '<div class="col-md-3">';
   inputIMEI += '<label>IMEI';
   inputIMEI += '<input type="text" id="imei['+cont+']" title="Digite o IMEI" name="imei['+cont+']" placeholder="Digite o IMEI" class="form-control dinamic" />';
   inputIMEI += '</label>';
   inputIMEI += '</div>';
   inputIMEI += '<div class="col-md-2">';
   inputIMEI += '<a onclick="$(this).parent().parent().remove();" href="#" class="glyphicon glyphicon glyphicon glyphicon-remove"></a>';
   inputIMEI += '</div>';
   inputIMEI += '</div>';
        
   $("#imeis").append(inputIMEI);
   
   $(".dinamic").css('font-weight', 'normal');
   
   $("#frm-salvar").validate();
   
});

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
    
    $(".data").mask("00/00/0000");
    $(".valor").mask("0.00##"/*, {reverse: true}*/);
    $(".celular").mask("#00000000");
    $(".qtd").mask("###0");
    
    $(".actions").click(function() {
        $('#aguarde').css({display:"block"});
    });
});


<?php if(!$visualizar){ ?>
/*
CONFIGURA O CALENDÁRIO DATEPICKER NO INPUT INFORMADO
*/
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
<?php } ?>

$(document).ready(function(){
    
    // Valida o formulário
	$("#frm-salvar").validate({
		debug: false,
		rules: {
            cd_telefonia_marca:{
                required: true  
            },
			modelo: {
                required: true
            }
		},
		messages: {
            cd_telefonia_marca:{
                required: "Selecione a marca"  
            },
			modelo: {
                required: "Informe o modelo"
            }
	   }
   });   
   
});
</script>