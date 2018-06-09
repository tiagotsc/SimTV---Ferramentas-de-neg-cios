    <!-- INÍCIO Modal Crédito / Débito -->
    <div class="modal fade" id="cred_deb" tabindex="-1" role="dialog" aria-labelledby="cred_deb" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Nota de crédito / débito</h4>
                </div>
                <div class="modal-body row">
                <?php  
                $data = array('class'=>'pure-form','id'=>'frm-cred_deb');
                echo form_open($pasta.'/notaDebCred/salvar',$data); 
                    
                    echo '<div class="col-md-12">';       
                    echo form_label('Contrato', 'alt_nome');
            		$data = array('id'=>'nome_contrato', 'name'=>'nome_contrato', 'readonly'=>'readonly', 'class'=>'form-control');
            		echo form_input($data,'');
                    echo '</div>';
                    
                    echo '<div class="col-md-6">';
                    echo form_label('Valor', 'valor');
        			$data = array('name'=>'valor', 'value'=>'','id'=>'valorCont', 'class'=>'form-control dinheiro');
        			echo form_input($data);
                    echo '</div>';
                    
                    $options = array('C' => 'Crédito','D' => 'Débito');
                    echo '<div class="col-md-6">';	
            		echo form_label('Tipo', 'tipo');
            		echo form_dropdown('tipo', $options, $tipo, 'id="tipo" class="form-control"');
                    echo '</div>';
                    
                    $options = array('' => '');
                    foreach($notaMotivos as $notaMot){
                        $options[$notaMot->id] = htmlentities($notaMot->nome);
                    }
                    echo '<div class="col-md-9">';	
            		echo form_label('Motivo', 'idDebCredMotivo');
            		echo form_dropdown('idDebCredMotivo', $options, $motivo, 'id="idDebCredMotivo" class="form-control"');
                    echo '</div>';  
                    
                    $options = array('A' => 'Ativo','I' => 'Inativo');
                    echo '<div class="col-md-3">';	
            		echo form_label('Status', 'status');
            		echo form_dropdown('status', $options, $status, 'id="status" class="form-control"');
                    echo '</div>'; 
                    
                    echo '<div id="divCancelar" class="col-md-4">';
                        echo form_label('&nbsp', 'cancelar');
                        echo '<button id="cancelar" class="btn btn-primary">Cancelar</button>';
                    echo '</div>';
                    
                    echo '<div class="col-md-4 col-md-offset-4 text-right">';
                        echo form_label('&nbsp', 'salvar');
                        echo '<input type="submit" id="salvar" class="btn btn-primary" value="Salvar">';
                    echo '</div>';
      
                ?>
                    <div class="col-md-12 text-center"><strong>Histórico</strong></div>
                    <table id="notasContrato" class="table table-bordered">
                    <tr>
                        <th>Valor</th>
                        <th>Tipo</th>
                        <th>Status</th>
                        <th>Data</th>
                        <th>Usuário</th>
                        <th>Ação</th>
                    </tr>
                    <tr>
                        <td>500,00</td>
                        <td>C</td>
                        <td>A</td>
                        <td>26/05/17 15:00</td>
                        <td>Roberto de Sousa Lima dos Santos</td>
                        <td><a title="Editar" href="#" key="" class="DadosCredDeb glyphicon glyphicon glyphicon-pencil"></a></td>
                    </tr>
                    </table>
                    
                </div>
                
                <div class="modal-footer">
                    <input type="hidden" id="idContrato" name="idContrato" />
                    <input type="hidden" id="nota_id" name="nota_id" />
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                </div>
                <?php
                echo form_close();
                ?>
        </div>
      </div>
    </div>
    <!-- FIM Modal Valores -->
<script type="text/javascript">

$(".dinheiro").maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:',', affixesStay: false});

$(document).ready(function(){   
    
    $("#divCancelar").css('visibility','hidden');
    
    $("#cancelar").click(function(){
        $("#valorCont").val('');
        $("#idDebCredMotivo").val('');
        $("#tipo").val('C'); 
        $("#status").val('A'); 
        $("#nota_id").val(''); 
        $("#salvar").val('Salvar');
        $("#divCancelar").css('visibility','hidden');
    });
    
    $(".DadosCredDeb").click(function(){
       $("#nome_contrato").val($(this).attr('nome'));
       $("#idContrato").val($(this).attr('key'));
        $.ajax({
          type: "POST",
          url: '<?php echo base_url().$modulo; ?>/notaDebCred/ajaxDadosNotas',          
          data: {
            idContrato: $(this).attr('key')
          },
          dataType: "json",
          /*error: function(res) {
            $("#resMarcar").html('<span>Erro de execução</span>');
          },*/
          success: function(res) { 
            
            if(res.length > 0){
            
                content = '<tr>';
                content += '<th>Valor</th>';
                content += '<th>Tipo</th>';
                content += '<th>Status</th>';
                content += '<th>Data</th>';
                content += '<th>Usuário</th>';
                content += '<th>Ação</th>';
                content += '</tr>';
                
                $.each(res, function() {
                  
                    content += '<tr>';
                    content += '<td class="valor'+this.id+'">'+this.valor+'</td>';
                    content += '<td class="tipo'+this.id+'">'+this.tipo+'</td>';
                    content += '<td class="status'+this.id+'">'+this.status+'</td>';
                    content += '<td class="motivo'+this.id+'" idmotivo="'+this.idDebCredMotivo+'" title="'+this.nome+'"><a href="#">'+this.dataCadastro+'</a></td>';
                    content += '<td>'+this.nome_usuario+'</td>';
                    if(this.faturado == 'SIM'){
                        content += '<td><strong title="Faturado">F</strong></td>';
                    }else{
                    content += '<td><a title="Editar" href="#" key="'+this.id+'" class="dadosCredDeb"><span class="glyphicon glyphicon glyphicon-pencil"></span></a></th>';
                    }
                    content += '</tr>';
                  
                });
                
                $("#notasContrato").html('');
                $("#notasContrato").append(content);
                
                $(".dadosCredDeb").click(function(){

                    $("#divCancelar").css('visibility','visible');
                    
                    $("#valorCont").val($(".valor"+$(this).attr('key')).html());
                    $("#tipo").val($(".tipo"+$(this).attr('key')).html());
                    $("#idDebCredMotivo").val($(".motivo"+$(this).attr('key')).attr('idmotivo'));
                    $("#status").val($(".status"+$(this).attr('key')).html());
                    $("#nota_id").val($(this).attr('key'));
                    
                    $("#salvar").val('Alterar');

                });
            
            }else{
                $("#notasContrato").html('');
            }
            
          }
        }); 
    });
    
    // Valida o formulário
	$("#frm-cred_deb").validate({
		debug: false,
		rules: {
            valor:{
                required: true  
            },
			idDebCredMotivo: {
                required: true
            }
		},
		messages: {
            valor:{
                required: "Preencha o valor"  
            },
			idDebCredMotivo: {
                required: "Selecione o motivo"
            }
	   }
   });    
     
});

$("#limpa_data_pri_fatura").click(function(){
    $("#data_pri_fatura").val('');
});

$('#data_pri_fatura').monthpicker({
    monthNames: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
});


</script>