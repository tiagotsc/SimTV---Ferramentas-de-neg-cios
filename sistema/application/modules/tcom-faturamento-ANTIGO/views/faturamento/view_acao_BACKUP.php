<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mtz.monthpicker.js") ?>"></script>
<style>
.mtz-monthpicker-year{
    width: 70px;
}
.mtz-monthpicker.mtz-monthpicker-year{
    color: black;
}
</style>
    <!-- INÍCIO Modal Apaga registro -->
    <div class="modal fade" id="apaga" tabindex="-1" role="dialog" aria-labelledby="apaga" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="myModalLabel">Deseja apagar <?php echo $assunto; ?>?</h4>
                </div>
                <div class="modal-body">
                    <?php              
                        $data = array('class'=>'pure-form','id'=>'apagaRegistro');
                        echo form_open($pasta.'/'.$controller.'/deleta',$data);
                        
                            echo form_label('Nome', 'apg_nome');
                    		$data = array('id'=>'apg_nome', 'name'=>'apg_nome', 'readonly'=>'readonly', 'class'=>'form-control');
                    		echo form_input($data,'');
                        
                    ?>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="apg_id" name="apg_id" />
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
    
            <div id="corpo" class="col-md-10 col-sm-9">
            <!--<div class="col-lg-12">-->
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a></li>
                    <li><a href="<?php echo base_url('tcom'); ?>"><?php echo strtolower($assunto); ?></a></li>
                    <li class="active"><?php echo $titulo; ?></li>
                </ol>
                <div id="divMain">
                    <?php
                     
                        $nome = (isset($nome))? $nome: false;
                        $status = (isset($status))? $status: false;
                        
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'pesquisar');
                    	echo form_open($pasta.'/'.$controller.'/gerar',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
                            
                            $botaoCadastrar = (in_array($perEditarCadastrar, $this->session->userdata('permissoes')))? "<a href='".base_url($pasta.'/'.$controller.'/ficha')."' class='linkDireita'>Cadastrar&nbsp<span class='glyphicon glyphicon-plus'></span></a>": '';
                            
                    		echo form_fieldset($titulo.$botaoCadastrar, $attributes);
                    		  
                                echo '<div class="row">';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array(''=>'','delin'=>'Delin','faturamento'=>'Faturamento','reajuste'=>'Reajuste');	
                            		echo form_label('Tipo a&ccedil;&atilde;o', 'tipo_acao');
                            		echo form_dropdown('tipo_acao', $options, $tipo_acao, 'id="tipo_acao" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div id="dataDiv" class="col-md-6">';
                                    #print_r(array_keys($listaBancos));
                                    echo form_label('Data da competência <a id="limpaData" class="fa fa-eraser" aria-hidden="true"></a>', 'data');
                        			$data = array('name'=>'data', 'value'=>date('m/Y'),'id'=>'data', 'placeholder'=>'Digite a data', 'class'=>'data form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                       
                                echo '</div>';                      
                    
                    ?>    
                </div>
                
                <div class="row">&nbsp</div>
                <div class="well">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="todos" /></th>
                                <th>Grupo Operadora</th>
                                <th>Qtd. contratos</th>
                                <th>Valor</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <td colspan="2"><strong>Total:</strong></td>
                                <td id="totalQtd">0</td>
                                <td id="totalValor">R$ 0,00</td>
                            </tr>
                        </tfoot>
                        <tbody id="tabelaConteudo">
                            <tr>
                                <td colspan="4">Informe os parâmetros</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <input type="hidden" id="idSelecionados" name="idSelecionados" value="" />
<?php
                                echo '<div class="actions">';
                                echo form_submit("btn",'Gerar', 'id="btnTipoAcao" class="btn btn-primary pull-right"');
                                echo '</div>';
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
?>
            </div>
            
<script type="text/javascript">
//alert(number_format('1234.56', 2, ',', '.'));
/*$(".data").mask("00/0000");

$(".data").datepicker({
	dateFormat: 'mm/yy',
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
});*/

$("#dataDiv,#btnTipoAcao").hide();

$("#tipo_acao").change(function(){
    $("#totalQtd").html('0');
    $("#totalValor").html("R$ 0.00");
    $("#idSelecionados").val("");
   if($(this).val() != ''){
    $("#dataDiv").show();
   }else{
    $("#dataDiv").hide();
    $("#tabelaConteudo").html('<tr><td colspan="4">Informe os parâmetros</td></tr>');
   }
   $(this).btnSubmit();
});

$.fn.btnSubmit = function() {
    if($("#tipo_acao").val() != '' && $("#data").val() != ''){
        $("#btnTipoAcao").show();
        $(this).carregaTabela();
    }else{
        $("#btnTipoAcao").hide();
    }
}

$.fn.carregaTabela = function(){
    
    $.ajax({
      type: "POST",
      url: '<?php echo base_url(); ?>tcom-faturamento/ajaxFaturamento/dadosTabelaDinamica',
      data: {
        tipo_acao: $("#tipo_acao").val(),
        data: $("#data").val()
      },
      dataType: "json",
      error: function(res) {
        $("#tabelaConteudo").html('<tr><td colspan="4">Erro...</td></tr>');
      },
      success: function(res) {
        var conteudo = '';
        if(res.length > 0){
            
            $.each(res, function() {
                
                if(this.valor_cobrado === null){
                    var valor = '0,00';
                }else{
                    var valor = this.valor_cobrado;
                }
                
                conteudo += '<tr>';
                conteudo += '<td><input class="pegaCheckbox" id="grupo'+this.idPai+'" name="grupo" value="'+this.idPai+'" onclick="calculaValores('+this.idPai+')" type="checkbox" /></td>';
                conteudo += '<td>'+this.grupo+'</td>';
                conteudo += '<td class="qtd'+this.idPai+'">'+this.qtdOper+'</td>';
                conteudo += '<td class="valor'+this.idPai+'">R$ '+mascaraValor(valor)+'</td>';
                conteudo += '</tr>';
            
            });
        
        }else{
            conteudo += '<tr><td colspan="4">Nada encontrado.</td></tr>';
        }
        
        $("#tabelaConteudo").html(conteudo);
        
      }
    });
    
}

function calculaValores(id){
    
    var valorTotal = Number($("#totalValor").html().replace("R$ ", "").replace(".", "").replace(",", "."));
    var qtdTotal = parseInt($("#totalQtd").html());
    var pegaValor = Number($(".valor"+id).html().replace("R$ ", "").replace(".", "").replace(",", "."));
    var pegaQtd = parseInt($(".qtd"+id).html());
    
    if($("#grupo"+id).prop('checked') == true){
        $("#totalQtd").html(qtdTotal+pegaQtd);
        $("#totalValor").html("R$ "+ mascaraValor((valorTotal+pegaValor).toFixed(2)) );
    }else{
        $("#totalQtd").html(qtdTotal-pegaQtd);
        $("#totalValor").html("R$ "+ mascaraValor((valorTotal-pegaValor).toFixed(2)) );
    }
    
    var arr = [];
    
    $("input:checkbox[name=grupo]:checked").each(function(){
        arr.push($(this).val());
    });
    var selecionados = implode_js(',',arr);  
    $("#idSelecionados").val(selecionados);
    
}

function dump(obj) {
    var out = '';
    for (var i in obj) {
        out += i + ": " + obj[i] + "\n";
    }
    alert(out);
}


$("#todos").click(function(){
    
    $("#totalQtd").html(0);
    $("#totalValor").html("R$ 0");
    
    if($(this).prop('checked') == true){
        $('input:checkbox').prop('checked', true);
        
        var arr = [];
        $.each($(".pegaCheckbox"), function() {
            arr.push($(this).val());
            calculaValores(this.value);
        });
        
        var selecionados = implode_js(',',arr);  
        $("#idSelecionados").val(selecionados);
        
    }else{
        $('input:checkbox').prop('checked', false);
    }
    
})

$("#data,#tipo_acao").keyup(function(){

    if($("#tipo_acao").val() == ''){
        alert("Data e tipo de ação são obrigatórios");
    }
    
});

$("#limpaData").click(function(){
    $(".data").val('');
    $(this).btnSubmit();
});

$('.data').monthpicker({
    monthNames: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
});

$('.data').monthpicker().bind('monthpicker-click-month', function (e, month) {
    $("#totalQtd").html('0');
    $("#totalValor").html("R$ 0.00");
    $("#idSelecionados").val("");    
    $(this).btnSubmit();
})

</script>