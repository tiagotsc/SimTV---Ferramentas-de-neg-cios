<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
    <!-- INÍCIO Modal Apaga registro -->
    <div class="modal fade" id="apagaExistente" tabindex="-1" role="dialog" aria-labelledby="apagaExistente" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="myModalLabel">Deseja apagar?</h4>
                </div>
                <div class="modal-body">
                    <?php              
                        $data = array('class'=>'pure-form','id'=>'apagaRegistro');
                        echo form_open($modulo.'/'.$controller.'/deletaCodigoExistente',$data);
                        
                            echo form_label('Identificação', 'apg_nome');
                    		$data = array('id'=>'apg_nome', 'name'=>'apg_nome', 'readonly'=>'readonly', 'class'=>'form-control');
                    		echo form_input($data,'');
                        
                    ?>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="apg_id" name="apg_id" />
                    <input type="hidden" id="idModelo" name="idModelo" value="<?php echo $id; ?>" />
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
             
                    		echo form_fieldset("Ficha equipamento - ".$assunto."<a href='".base_url($modulo.'/'.$controller.'/pesq')."' class='linkDireita'><span class='glyphicon glyphicon-arrow-left'></span>&nbspVoltar Pesquisa modelo</a>", $attributes);
                    		
                                echo '<div class="row">';
                                    
                                    echo '<div class="col-md-4">';
                                    $options = array('' => '');		
                                    foreach($marcas as $ma){
                                        $options[$ma->id] = $ma->nome;
                                    }
                            		echo form_label('Marca', 'idEquipMarca');
                            		echo form_dropdown('idEquipMarca', $options, $idEquipMarca, 'id="idEquipMarca" class="form-control"');
                                    echo '</div>';
                                                                                                            
                                    echo '<div class="col-md-4">';
                                    echo form_label('modelo<span class="obrigatorio">*</span>', 'nome');
                        			$data = array('name'=>'nome', 'value'=>$nome,'id'=>'nome', 'placeholder'=>'Informe o nome', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';

                                    echo '<div class="col-md-4">';
                                    $options = array('A' => 'Ativo', 'I' => 'Inativo');		
                            		echo form_label('Status', 'status');
                            		echo form_dropdown('status', $options, $status, 'id="Status" class="form-control"');
                                    echo '</div>';      
                                    
                                    if($id){
                                    echo '<div class="col-md-12"><a id="add-cod" href="#"><spa class="glyphicon glyphicon-plus"></spa><strong>Adicionar código</strong></a></div>';
                                    echo '<div class="col-md-12">';
                                    echo '<table id="codigos" class="table zebra">';
                                    echo '<tr>';
                                    echo '<th>Identificação</th>';
                                    echo '<th>Código (Placomp)</th>';
                                    echo '<th>Ação</th>';
                                    echo '</tr>';
                                    foreach($codigos as $cod){
                                        echo '<tr>';
                                        echo '<td><input class="form-control" type="text" name="ident-update['.$cod->id.']" value="'.$cod->identificacao.'" /></td>';
                                        echo '<td><input class="form-control" type="text" name="cod-update['.$cod->id.']" value="'.$cod->codigo.'" /></td>';
                                        if($cod->idContrato != ''){
                                            $link = '<a href="'.base_url('tcom-contrato/contrato/imprimir/'.$cod->idContrato).'" target="_blank" class="glyphicon glyphicon-search"></a>';
                                        }else{
                                            $link = '<a key="'.$cod->id.'" value="'.$cod->identificacao.'" data-toggle="modal" data-target="#apagaExistente" class="apgExistente glyphicon glyphicon-remove"></a>';
                                        }
                                        echo '<td>'.$link.'</td>';
                                        echo '</tr>';
                                    }
                                    echo '</table>';
                                    echo '</div>';
                                    
                                    }
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

$(".apgExistente").click(function(){
    $("#apg_id").val($(this).attr('key'));
    $("#apg_nome").val($(this).attr('value'));
});

$("#add-cod").click(function(event){
    event.preventDefault();
    var cont = $(".qtdIdent").length;
    var tr = '<tr>';
        tr += '<td><input class="form-control qtdIdent" type="text" name="ident['+cont+']" /></td>';
        tr += '<td><input class="form-control" type="text" name="cod['+cont+']" /></td>';
        tr += '<td><a class="removeCodigo glyphicon glyphicon-minus"></a></td>';
        tr += '</tr>';
        $("#codigos").append(tr);
    cont++;    
    removeCodigo();
});

function removeCodigo() {
    $(".removeCodigo").click(function(event){
        event.preventDefault();
        $(this).parent().parent().remove();
    });
}

$(document).ready(function(){
    
    removeCodigo();
    
    // Valida o formulário
	$("#frm-salvar").validate({
		debug: false,
		rules: {
            idEquipMarca: {
                required: true
            },
            nome: {
                required: true
            }
		},
		messages: {
            idEquipMarca: {
                required: "Selecione a marca."
            },
            nome: {
                required: "Informe o modelo."
            }
	   }
   });
    
});


</script>