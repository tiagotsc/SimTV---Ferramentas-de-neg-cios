<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>

            <!--<div class="col-md-10 col-sm-9">-->
            <div class="col-lg-12">
                
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
                                echo '<div class="col-md-12">';
                                    
                                    $optionsDp = array('' => '');		
                            		foreach($departamento as $dep){
                            			$optionsDp[$dep->cd_departamento] = htmlentities($dep->nome_departamento);
                            		}
                                    
                                    $optionsUni = array('' => '');	
                                    foreach($unidade as $un){
                                        $optionsUni[$un->cd_unidade] = htmlentities($un->nome);
                                    }
                                
                                    $this->table->set_heading('Departamento', 'Unidade', 'Código', 'A&ccedil;&atilde;o');
                                    #$this->table->set_heading($colunas);
                                    
                                    $contCell = 0; 
                                    $contInput = 1;         
                                    foreach($dados as $da){
	
                                		$depInput = form_label('Departamento<span class="obrigatorio">*</span>', 'cd_departamento['.$contInput.']');
                                		$depInput = form_dropdown('cd_departamento['.$contInput.']', $optionsDp, $da->cd_departamento, 'id="cd_departamento['.$contInput.']" depControl="'.$contInput.'" class="dep form-control"');
                                        
                                        $uniInput = form_label('Unidade', 'cd_unidade['.$contInput.']');
                          		        $uniInput = form_dropdown('cd_unidade['.$contInput.']', $optionsUni, $da->cd_unidade, 'id="cd_unidade['.$contInput.']" uniControl="'.$contInput.'" class="uni uniClass'.$contInput.' form-control"');
                                        
                                        $data = array('name'=>'codigo['.$contInput.']', 'value'=>$da->codigo,'id'=>'codigo['.$contInput.']', 'class'=>'form-control');
                                        $codeInput = form_input($data);
                                        
                                        $cell[$contCell++] = array('data' => $depInput);
                                        $cell[$contCell++] = array('data' => $uniInput);
                                        $cell[$contCell++] = array('data' => $codeInput);
                                        
                                        $botaoEditar = (in_array(130, $this->session->userdata('permissoes')))? '<a title="Editar" href="'.base_url('anatel/fichaForm/'.$frmsT->cd_anatel_frm).'" class="glyphicon glyphicon glyphicon-pencil"></a>': '';
                                        $botaoExcluir = (in_array(131, $this->session->userdata('permissoes')))? '<a title="Apagar" href="#" onclick="apagarRegistro('.$frmsT->cd_anatel_frm.',\''.$frmsT->nome.'\')" data-toggle="modal"  data-target="#apaga" class="glyphicon glyphicon glyphicon glyphicon-remove"></a>': '';
                                        
                                        $cell[$contCell++] = array('data' => $botaoEditar.$botaoExcluir);
                                            
                                        $this->table->add_row($cell);
                                        
                                        $contCell = 0;
                                        $contInput++;
                                        
                                    }
                                    
                                	$template = array('table_open' => '<table class="table zebra">');
                                	$this->table->set_template($template);
                                	echo $this->table->generate();                                
                         
                                echo '<div class="actions">';
                                
                                echo form_submit("btn","Salvar", 'class="btn btn-primary pull-right"');
                                echo '</div>';
                                
                                echo '</div>';
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
            nome: {
                required: true
            }
		},
		messages: {
            nome: {
                required: "Informe o nome."
            }
	   }
   });
    
});


$(".dep").change(function(){
    
    if($(this).val() != ''){
        //alert($(this).attr('depControl'));
        //alert($("#cd_unidade["+$(this).attr('depControl')+"]").val());
        alert($('.uniClass'+$(this).attr('depControl')).val());
    }
    
})

</script>