<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>

<!-- INÍCIO Modal Apaga registro de telecom -->
<div class="modal fade" id="apaga" tabindex="-1" role="dialog" aria-labelledby="apaga" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            <h4 class="modal-title" id="myModalLabel">Deseja apagar o registro?</h4>
            </div>
            <div class="modal-body">
                <?php              
                    $data = array('class'=>'pure-form','id'=>'apagaRegistro');
                    echo form_open('cobrancaFaturamento/apagaRegistroTelecom',$data);
                    
                        echo form_label('Registro de telecom:', 'excluir_codigo_registro');
                		$data = array('id'=>'excluir_codigo_registro', 'name'=>'excluir_codigo_registro', 'class'=>'form-control data');
                		echo form_input($data,'');
                    
                ?>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="excluir_cd_registro" name="excluir_cd_registro" />
                <button type="button" class="btn btn-default" data-dismiss="modal">N&atilde;o</button>
                <button type="submit" class="btn btn-primary">Sim</button>
            </div>
                <?php
                echo form_close();
                ?>
        </div>
  </div>
</div>
<!-- FIM Modal Apaga registro de telecom -->

            <!--<div class="col-md-9 col-sm-8"> USADO PARA SIDEBAR -->
            <div class="col-lg-12">
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a>
                    </li>
                    <li class="active">Registros de telecom</li>
                </ol>
                <div id="divMain">
            
                    <?php    
                    
                    if(in_array(13, $this->session->userdata('permissoes'))){
                                    
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'salvaRegistros');
                    	echo form_open('cobrancaFaturamento/salvaRegistrosTelecom',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
             
                    		echo form_fieldset("Registros de telecom", $attributes);
                    		
                                #print_r(array_keys($listaBancos));
                                echo form_label('C&oacute;digo do registro<span class="obrigatorio">*</span>', 'cod_registro_telecom');
                    			$data = array('name'=>'cod_registro_telecom', 'value'=>$cod_registro_telecom,'id'=>'cod_registro_telecom', 'placeholder'=>'Digite o c&oacute;digo', 'class'=>'form-control', 'maxlength'=>'3');
                    			echo form_input($data);
                                
                                $options = array('' => '');		
                        		foreach($status as $sta){
                        			$options[$sta->sigla_status] = $sta->nome_status;
                        		}	
                        		echo form_label('Status<span class="obrigatorio">*</span>', 'status_registro_telecom');
                        		echo form_dropdown('status_registro_telecom', $options, $status_registro_telecom, 'id="status_registro_telecom" class="form-control"');
                                
                                echo form_hidden('cd_registro_telecom',$cd_registro_telecom);
                                
                                echo '<div class="actions">';
                                if($cd_registro_telecom){
                                    echo '<a href="'.base_url('cobrancaFaturamento/registrosTelecom').'" class="btn btn-primary pull-left">Novo n&uacute;mero</a>';
                    			}
                                echo form_submit("btn_cadastro",$botao.utf8_encode(" código"), 'class="btn btn-primary pull-right"');
                                echo '</div>';
                                                            
                    		echo form_fieldset_close();
                    	echo form_close();
                     
                     } // Fecha permissão
                                                                
                    ?>     
                           <div id="aguarde" style="text-align: center; display: none"><img src="<?php echo base_url('assets/img/aguarde.gif');?>" /></div>        
                    <div class="tabela-div">
                        <?php
                    	$this->table->set_heading('C&oacute;digo', 'Status', 'A&ccedil;&atilde;o');
                        
                        foreach($registros as $resg){
                            
                            $cell1 = array('data' => $resg->cod_registro_telecom);
                            $cell2 = array('data' => $resg->nome_status);
                            
                            $botaoEditar = (in_array(13, $this->session->userdata('permissoes')))? '<a title="Editar" href="'.base_url('cobrancaFaturamento/registrosTelecom/'.$resg->cd_registro_telecom).'" class="glyphicon glyphicon glyphicon-pencil"></a>' : '';
                            $botaoExcluir = (in_array(14, $this->session->userdata('permissoes')))? '<a title="Apagar" href="#" onclick="apagarRegistro('.$resg->cod_registro_telecom.','.$resg->cd_registro_telecom.')" data-toggle="modal"  data-target="#apaga" class="glyphicon glyphicon glyphicon glyphicon-remove"></a>' : '';
                            
                            $cell3 = array('data' => $botaoEditar.$botaoExcluir);
                                
                            $this->table->add_row($cell1, $cell2, $cell3);
                        }
                        
                    	$template = array('table_open' => '<table class="table table-bordered">');
                    	$this->table->set_template($template);
                    	echo $this->table->generate();
                    	?>
                    </div>
                </div>
            </div>
    
<script type="text/javascript">

function apagarRegistro(cod, cd){
    $("#excluir_codigo_registro").val(cod);
    $("#excluir_cd_registro").val(cd);
}

$(document).ready(function() {
    
    $('#cod_registro_telecom').mask('000');
    
   //$(".actions").click(function() {
      //$('#aguarde').css({display:"block"});
   //});
   
   // Valida o formulário do paciente
	$("#salvaRegistros").validate({
		debug: false,
		rules: {
			cod_registro_telecom: {
                required: true,
                minlength: 3
            },
            status_registro_telecom: "required"
		},
		messages: {
			cod_registro_telecom: {
                required: "Digite o n&uacute;mero.",
                minlength: "O n&uacute;mero deve conter 3 digitos"
            },
            status_registro_telecom: "Selecione o status."
	   },
        submitHandler: function(form) {
            $('#aguarde').css({display:"block"});
            form.submit();
        }       
   });
   
});

</script>