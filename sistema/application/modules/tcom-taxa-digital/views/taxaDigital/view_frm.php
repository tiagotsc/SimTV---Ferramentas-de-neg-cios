<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>

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
                                    echo form_label('Velocidade<span class="obrigatorio">*</span>', 'velocidade');
                        			$data = array('name'=>'velocidade', 'value'=>$velocidade,'id'=>'velocidade', 'placeholder'=>'Informe a velocidade', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                
                                    echo '<div class="col-md-2">';
                                    $options = array('' => '', 'Kbps' => 'Kbps', 'Mbps' => 'Mbps', 'Gbps' => 'Gbps');		
                            		echo form_label('Tipo<span class="obrigatorio">*</span>', 'tipo');
                            		echo form_dropdown('tipo', $options, $tipo, 'id="tipo" class="form-control"');
                                    echo '</div>';  
                                    
                                    echo '<div class="col-md-4">';
                                    echo form_label('Mnemonico', 'mnemonico');
                        			$data = array('name'=>'mnemonico', 'value'=>$mnemonico,'id'=>'mnemonico', 'placeholder'=>'Informe o mnemonico', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>'; 
                                    
                                    echo '<div class="col-md-2">';
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

$(document).ready(function(){ 

    // Valida o formulário
	$("#frm-salvar").validate({
		debug: false,
		rules: {
            velocidade: {
                required: true
            },
			tipo: {
                required: true
            }
		},
		messages: {
            velocidade: {
                required: "Informe a velocidade."
            },
			tipo: {
                required: "Selecione o tipo."
            }
	   }
   });
    
});


</script>