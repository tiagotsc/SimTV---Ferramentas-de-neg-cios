            <!--<div class="col-md-9 col-sm-8"> USADO PARA SIDEBAR -->
            <div class="col-lg-12">
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a>
                    </li>
                    <li class="active">Impress&atilde;o t&iacute;tulo</li>
                </ol>
                <div id="divMain">
                <?php
                        $data = array('class'=>'pure-form','id'=>'frm', 'target' => '_blank');
                    	echo form_open($url,$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
                            
                    		echo form_fieldset("Impress&atilde;o t&iacute;tulo", $attributes);
                    		
                                echo '<div class="row">';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array('aberto' => 'Aberto', 'pago' => 'Pago');	
                            		echo form_label('T&iacute;tulo status<span class="obrigatorio">*</span>', 'status');
                            		echo form_dropdown('status', $options, false, 'id="status" class="form-control"');
                                    echo '</div>';
                                
                                    echo '<div class="col-md-3">';
                                    $options = array('' => '');	
                                    foreach($permissor as $per){
                                        if($per->permissor != ''){
                                            $options[$per->permissor] = $per->permissor.' - '.$per->nome;
                                        }
                                    }	
                            		echo form_label('Permissor<span class="obrigatorio">*</span>', 'permissor');
                            		echo form_dropdown('permissor', $options, false, 'id="permissor" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    echo form_label('T&iacute;tulo<span class="obrigatorio">*</span>', 'titulo');
                        			$data = array('name'=>'titulo', 'value'=>'','id'=>'titulo', 'placeholder'=>'Digite o t&iacute;tulo', 'class'=>'form-control dia required');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    echo form_label('E-mail', 'email');
                        			$data = array('name'=>'email', 'value'=>'','id'=>'email', 'placeholder'=>'Digite o e-mail', 'class'=>'form-control dia required');
                        			echo form_input($data);
                                    echo '</div>';
                                echo '</div>';    
                                                
                                echo form_hidden('senha', 'apiSimTv');                
                                echo '<div class="actions">';
                                echo form_submit("btn","Gerar / Enviar", 'class="btn btn-primary pull-right"');
                                echo '</div>';
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
                    
                    ?>      
                </div>
            </div>
            
<script type="text/javascript">
$(document).ready(function(){
    $("#status").change(function(){
       if($(this).val() == 'aberto'){
            $("#frm").attr('action', 'http://sistemas.simtv.com.br/ws/titulo/segundaViaPdf');
       }else{
            $("#frm").attr('action', 'http://sistemas.simtv.com.br/ws/titulo/segundaViaPdfPaga');
       } 
    });
});
</script>