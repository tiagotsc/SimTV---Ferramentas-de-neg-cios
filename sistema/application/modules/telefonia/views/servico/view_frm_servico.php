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
                    <li class="active">Ficha servi&ccedil;o</li>
                </ol>
                <div id="divMain">
                    <?php
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'frm-salvar');
                    	echo form_open('telefonia/salvaServico',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
             
                    		echo form_fieldset("Ficha servi&ccedil;os<a href='".base_url('telefonia/servicos')."' class='linkDireita'><span class='glyphicon glyphicon-arrow-left'></span>&nbspVoltar Pesquisar</a>", $attributes);
                    		
                                echo '<div class="row">';
                                
                                    echo '<div class="col-md-4">';
                                    echo form_label('Nome<span class="obrigatorio">*</span>', 'nome');
                        			#$data = array('name'=>'nome', 'value'=>$nome,'id'=>'nome', 'placeholder'=>'Digite o nome', 'class'=>'form-control');
                        			#echo form_input($data);
                                    echo '<input type="text" id="nome" name="nome" value="'.utf8_decode($nome).'" placeholder="Digite o nome" class="form-control" />';
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    echo form_label('Quantidade<span class="obrigatorio">*</span>', 'nome');
                        			$data = array('name'=>'qtd', 'value'=>$qtd,'id'=>'qtd', 'placeholder'=>'Digite a qtd.', 'class'=>'form-control qtd');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    echo form_label('Valor<span class="obrigatorio">*</span>', 'valor');
                        			$data = array('name'=>'valor', 'value'=>$valor,'id'=>'valor', 'placeholder'=>'Digite o valor', 'class'=>'form-control valor');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    echo form_label('Data In&iacute;cio<span class="obrigatorio">*</span>', 'data_inicio');
                        			$data = array('name'=>'data_inicio', 'value'=>$this->util->formataData($data_inicio, 'BR'),'id'=>'data_inicio', 'placeholder'=>'Digite a data', 'class'=>'form-control data');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    echo form_label('Data Fim<span class="obrigatorio">*</span>', 'data_fim');
                        			$data = array('name'=>'data_fim', 'value'=>$this->util->formataData($data_fim,'BR'),'id'=>'data_fim', 'placeholder'=>'Digite a data', 'class'=>'form-control data');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-10">';
                                    echo form_label('Descri&ccedil;&atilde;o<span class="obrigatorio">*</span>', 'descricao');
                        			#$data = array('name'=>'descricao', 'value'=>$descricao,'id'=>'descricao', 'placeholder'=>'Descreva o servi&ccedil;o', 'class'=>'form-control');
                        			#echo form_input($data);
                                    echo '<input type="text" id="descricao" name="descricao" value="'.utf8_decode($descricao).'" placeholder="Descreva o serviço" class="form-control" />';
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    $options = array('A' => 'Ativo', 'I' => 'Inativo');		
                            		echo form_label('Status<span class="obrigatorio">*</span>', 'status');
                            		echo form_dropdown('status', $options, $status, 'id="status" class="form-control"');
                                    echo '</div>';
                                    
                                echo '</div>';
                                                              
                                echo '<div class="actions">';
                                
                                echo form_hidden('cd_telefonia_servico', $cd_telefonia_servico);
                                
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
    
    $(".data").mask("00/00/0000");
    $(".valor").mask("0.00##"/*, {reverse: true}*/);
    $(".qtd").mask("###0");
    
    $(".actions").click(function() {
        $('#aguarde').css({display:"block"});
    });
});


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

$(document).ready(function(){
    
    // Valida o formulário
	$("#frm-salvar").validate({
		debug: false,
		rules: {
			nome: {
                required: true,
                minlength: 2
            }
		},
		messages: {
			nome: {
                required: "Digite o nome.",
                minlength: "Digite o nome completo"
            }
	   }
   });   
   
});
</script>