<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
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
                                
                                    echo '<div class="col-md-4">';
                                    echo form_label('Nome<span class="obrigatorio">*</span>', 'nome');
                        			$data = array('name'=>'nome', 'value'=>htmlentities($nome),'id'=>'nome', 'placeholder'=>'Informe o nome', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    /*
                                    echo '<div class="col-md-4">';
                                    $options = array(''=>'', 'S' => 'Sim', 'N' => 'Nao');		
                            		echo form_label('Status final?<span class="obrigatorio">*</span><span id="infoHab" title="Se definido como \'SIM\' o processo ser&aacute; finalizado, caso contr&aacute;rio ficar&aacute; aberto para novas intera&ccedil;&otilde;es." class="glyphicon glyphicon-question-sign"></span>', 'final');
                            		echo form_dropdown('final', $options, $final, 'id="final" class="form-control"');
                                    echo '</div>';
                                    */
                                    
                                    if($final != 'S'){
                                    echo '<div class="col-md-4">';
                                    $options = array('A' => 'Ativo', 'I' => 'Inativo');		
                            		echo form_label('Status', 'tv');
                            		echo form_dropdown('Status', $options, $status, 'id="Status" class="form-control"');
                                    echo '</div>';   
                                    }   
                                    
                                    
                                    
                                echo '</div>';
                                                              
                                echo '<div class="actions">';
                                
                                if($id != '' and in_array($perDefRecebeEmail, $this->session->userdata('permissoes'))){
                                    $this->session->set_flashdata('tipo_email', $idEmailEnvia);
                                    $this->session->set_flashdata('permissor', 'todos');
                                    echo '<a class="btn btn-primary" href="'.base_url('email/recebeEmail').'" role="button">Definir quem recebe e-mail para esse status</a>';

                                }
                                
                                echo form_hidden('id', $id);
                                
                                echo form_submit("btn","Salvar", 'class="btn btn-primary pull-right"');
                                echo '</div>';   
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
                        
                    ?>       
                </div>
            </div>
    
<script type="text/javascript">

$(document).ready(function(){
    
    $("#infoHab").hide();
    
    // Valida o formul�rio
	$("#frm-salvar").validate({
            debug: false,
            rules: {
                nome: {
                    required: true
                },
                final: {
                    required: true
                }
            },
            messages: {
                nome: {
                    required: "Informe o nome."
                },
                final: {
                    required: "Selecione status."
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


</script>