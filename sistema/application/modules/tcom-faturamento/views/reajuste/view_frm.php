<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.maskMoney.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mtz.monthpicker.js") ?>"></script>
<style>
.mtz-monthpicker-year{
    width: 70px;
}
.mtz-monthpicker.mtz-monthpicker-year{
    color: black;
}
</style>
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
                    <?php   

                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'frm-salvar');
                    	echo form_open($modulo.'/'.$controller.'/salvar',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
             
                    		echo form_fieldset("Ficha ".$assunto."<a href='".base_url($modulo.'/'.$controller.'/pesq')."' class='linkDireita'><span class='glyphicon glyphicon-arrow-left'></span>&nbspVoltar Pesquisar</a>", $attributes);

                              echo '<div class="row">';

                                    echo '<div class="col-md-3">';
                                    $options = array('' => '');	
                                    foreach($indices as $ind){
                                        $options[$ind->id] = $ind->nome;
                                    }
                            		echo form_label('Nome<span class="obrigatorio">*</span>', 'idindicereajuste');
                            		echo form_dropdown('idindicereajuste', $options, $idIndiceReajuste, 'id="idindicereajuste" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    echo form_label('M&ecirc;s / Ano<span class="obrigatorio">*</span>', 'mesano');
                        			$data = array('name'=>'mesano', 'value'=>implode('/',array_reverse(explode('-',substr($mesAno, 0, 7)))),'id'=>'mesano', 'placeholder'=>'Informe a porcetagem', 'maxlength' => '7', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                              
                                    echo '<div class="col-md-3">';
                                    echo form_label('&Iacute;ndice(%)<span class="obrigatorio">*</span>', 'indice');
                        			$data = array('name'=>'indice', 'value'=>$indice,'id'=>'indice', 'title'=>'Separe as casas decimais com .', 'placeholder'=>'Informe a porcetagem', 'maxlength' => '7', 'class'=>'porcentagem form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    echo form_label('Acomp. ano(%)<span class="obrigatorio">*</span>', 'acomano');
                        			$data = array('name'=>'acomano', 'value'=>$acomAno,'id'=>'acomano', 'title'=>'Separe as casas decimais com .', 'placeholder'=>'Informe a porcetagem', 'maxlength' => '7', 'class'=>'porcentagem form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    echo form_label('Acomp. doze meses(%)', 'acomdozemeses');
                        			$data = array('name'=>'acomdozemeses', 'value'=>$acomDozeMeses,'id'=>'acomdozemeses', 'title'=>'Separe as casas decimais com .', 'placeholder'=>'Informe a base de calculo', 'maxlength' => '7', 'class'=>'porcentagem form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array('A' => 'Ativo', 'I' => 'Inativo');		
                            		echo form_label('Status', 'status');
                            		echo form_dropdown('status', $options, $status, 'id="status" class="form-control"');
                                    echo '</div>';   
                                    
                                echo '</div>';
                                                              
                                echo '<div class="actions">';
                                
                                echo form_hidden('id', $id);
                                
                                echo form_submit("btn","Salvar", 'class="btn btn-primary pull-right"');
                                echo '</div>';   
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
                        
                    ?>       
                </div>
            </div>
    
<script type="text/javascript">

$('#mesano').monthpicker({
    monthNames: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
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

$(".porcentagem").keyup(function(){
    var remocao = $(this).val().replace(',','.');
    $(this).val(remocao);
})

$("#mesano").mask("00/0000");
//$(".porcentagem").mask("00,0000", {reverse: true});
//$(".porcentagem").maskMoney({suffix:'%', allowZero: false, allowNegative: true, thousands:'.', decimal:'.', affixesStay: true});

$(document).ready(function(){
    
    // Valida o formul�rio
	$("#frm-salvar").validate({
            debug: false,
            rules: {
                nome: {
                    required: true
                },
                mesano: {
                    required: true
                },
                indice: {
                    required: true
                },
                acomano: {
                    required: true
                },
                acomdozemeses: {
                    required: true
                }
            },
            messages: {
                nome: {
                    required: "Informe o nome"
                },
                mesano: {
                    required: "Informe mês / ano"
                },
                indice: {
                    required: "Informe o índice"
                },
                acomano: {
                    required: "Informe o acomp. ano"
                },
                acomdozemeses: {
                    required: "Informe o acomp. 12 meses"
                }
            }
        });
    
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