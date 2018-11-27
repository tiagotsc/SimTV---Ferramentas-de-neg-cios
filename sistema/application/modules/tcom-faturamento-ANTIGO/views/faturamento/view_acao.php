<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mtz.monthpicker.js") ?>"></script>
<script type="text/javascript" src="http://momentjs.com/downloads/moment-with-locales.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url("assets/css/tooltip.css") ?>" />
<style>
.mtz-monthpicker-year{
    width: 70px;
}
.mtz-monthpicker.mtz-monthpicker-year{
    color: black;
}
</style>
    <!-- INÍCIO Modal Baixar registro -->
    <div class="modal fade" id="baixar_arquivo" tabindex="-1" role="dialog" aria-labelledby="apaga" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Baixar arquivo</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                    <?php              
                        echo '<div class="col-md-6">';
                        $options = array(''=>'','delin'=>'Delin'/*,'faturamento'=>'Faturamento','reajuste'=>'Reajuste'*/);	
                		echo form_label('Tipo a&ccedil;&atilde;o', 'tipo_acao_baixar');
                		echo form_dropdown('tipo_acao_baixar', $options, $tipo_acao, 'id="tipo_acao_baixar" class="form-control"');
                        echo '</div>';
                        
                        echo '<div class="col-md-6">';
                        $options = array(''=>'');	
                        foreach($ano_acao as $ano){
                            $options[$ano->ano] = $ano->ano;
                        }
                		echo form_label('Ano', 'ano_baixar');
                		echo form_dropdown('ano_baixar', $options, $tipo_acao, 'id="ano_baixar" class="form-control"');
                        echo '</div>';
                    ?>
                    </div>
                    <hr />
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Arquivo</th>
                                        <th>Status</th>
                                        <th>Data/hora</th>
                                    </tr>
                                </thead>
                                <tbody id="tabelaArquivos">
                                    <tr>
                                        <td colspan="3">Selecione os parâmetros</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="apg_id" name="apg_id" />
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <!--<button type="submit" class="btn btn-primary">Sim</button>-->
            </div>
                    <?php
                    echo form_close();
                    ?>
        </div>
      </div>
    </div>
    <!-- FIM Modal Baixar registro de telecom -->
    
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
                            
                            $botaoCadastrar = '<a href="#" data-toggle="modal" data-target="#baixar_arquivo" class="linkDireita">Baixar arquivo da a&ccedil;&atilde;o&nbsp<span class="glyphicon glyphicon-floppy-save"></span></a>';
                            
                    		echo form_fieldset($titulo.$botaoCadastrar, $attributes);
                    		  
                                echo '<div class="row">';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array(''=>'','delin'=>'Delin','faturamento'=>'Faturamento','nota_fiscal'=>'Nota fiscal'/*,'reajuste'=>'Reajuste'*/);	
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
                    <div class="row">&nbsp</div>
                    <div class="well">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="todos" /></th>
                                    <th>Grupo Operadora</th>
                                    <th>Qtd. contratos</th>
                                    <th>Valor</th>
                                    <th>Gerado?</th>
                                    <th>Próximo</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <td colspan="2"><strong>Total:</strong></td>
                                    <td id="totalQtd">0</td>
                                    <td id="totalValor">R$ 0,00</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                            </tfoot>
                            <tbody id="tabelaConteudo">
                                <tr>
                                    <td colspan="6">Informe os parâmetros</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <input type="hidden" id="idSelecionados" name="idSelecionados" value="" />
                <input type="hidden" id="idGerados" name="idGerados" value="" />
                
<?php

                                echo '<div class="actions">';
                                echo form_submit("btn",'Gerar', 'id="btnTipoAcao" class="btn btn-primary pull-right"');
                                echo '</div>';
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
?>
            </div>
            
<script type="text/javascript">
//moment.locale('pt-BR');
//alert(moment().format('MMMM Do YYYY, h:mm:ss a'));

// Verifica se data fornecida é anterior a data corrente
// alert(moment('2018-10-20').isBefore('<?php echo date('Y-m-d');?>'));

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

var linkAcaoDownload = '<?php echo base_url($linkAcaoDownload); ?>';

$("#dataDiv,#btnTipoAcao").hide();

$("#tipo_acao").change(function(){
    $("#totalQtd").html('0');
    $("#totalValor").html("R$ 0.00");
    $("#idSelecionados").val("");
    $("#idGerados").val("");
   if($(this).val() != '' && $(this).val() != 'faturamento'){
    $("#dataDiv").show();
   }else{
    $("#dataDiv").hide();
    $("#tabelaConteudo").html('<tr><td colspan="6">Informe os parâmetros</td></tr>');
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
    $("#btnTipoAcao").hide();
    $.ajax({
      type: "POST",
      url: '<?php echo base_url(); ?>tcom-faturamento/ajaxFaturamento/dadosTabelaDinamica',
      data: {
        tipo_acao: $("#tipo_acao").val(),
        data: $("#data").val()
      },
      dataType: "json",
      error: function(res) { //dump(res);
        $("#tabelaConteudo").html('<tr><td colspan="6">Erro...</td></tr>');
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
                //alert(this.proxima);
                
                //if(this.proxima != $("#data").val()){
                if(this.faturado > 0 && this.existe != '-'){ 
                    var checkbox = 'Faturado';
                }else{
                    var checkbox = '<input class="pegaCheckbox" id="grupo'+this.idPai+'" name="grupo['+this.idPai+']" value="'+this.idPai+'" onclick="calculaValores('+this.idPai+')" type="checkbox" />';
                }
                
                if(this.existe > 0){
                    var gerado = 'Sim';
                }else{
                    var gerado = 'Não';
                }
                
                if(this.proxima === null){
                    var prox = '-';
                }else{
                    var prox = this.proxima;
                    if($("#tipo_acao").val() == 'delin' && $("#data").val() != this.proxima && this.faturado == 0 && gerado != 'Sim'){
                        checkbox = '-';
                    }
                }
                
                if(this.arquivo === null || this.arquivo === undefined){
                    var arq = '';
                }else{
                    var arq = this.arquivo;
                }
                
                // Se não tiver operadora definida não habilita delin ou faturamento
                if(this.idPai == '0'){ 
                    checkbox = '-';
                }
                
                if(this.competencia !== null){
                    if($("#tipo_acao").val() == 'delin' && $("#data").val() != this.competencia && this.faturado == 0){ 
                        checkbox = '-';
                    }
                }
                
                var inputGerado = '<input type="hidden" id="gerado'+this.idPai+'" name="gerado['+this.idPai+']" value="'+gerado+'" />';
                var inputCompetencia = '<input type="hidden" id="competencia'+this.idPai+'" name="competencia['+this.idPai+']" value="'+prox+'" />';
                
                conteudo += '<tr>';
                conteudo += '<td>'+checkbox+inputGerado+inputCompetencia+'</td>';
                conteudo += '<td title="'+arq+'">'+this.grupo+'</td>';
                conteudo += '<td class="qtd'+this.idPai+'">'+this.qtdOper+'</td>';
                conteudo += '<td class="valor'+this.idPai+'">R$ '+mascaraValor(valor)+'</td>';
                conteudo += '<td>'+gerado+'</td>';
                conteudo += '<td>'+prox+'</td>';
                conteudo += '</tr>';
            
            });
        
        }else{
            conteudo += '<tr><td colspan="6">Nada encontrado.</td></tr>';
        }
        
        $("#tabelaConteudo").html(conteudo);
        
      }
    });
    
}

//alert(number_format( 4121234.50, 2, ',', '.' ));


function calculaValores(id){
    
    var valorTotal = Number($("#totalValor").html().replace("R$ ", "").replace(".", "").replace(".", "").replace(",", "."));
    var qtdTotal = parseInt($("#totalQtd").html());
    var pegaValor = Number($(".valor"+id).html().replace("R$ ", "").replace(".", "").replace(".", "").replace(",", "."));
    var pegaQtd = parseInt($(".qtd"+id).html());

    if($("#grupo"+id).prop('checked') == true){
        $("#totalQtd").html(qtdTotal+pegaQtd);
        $("#totalValor").html("R$ "+ mascaraValor((valorTotal+pegaValor).toFixed(2)) );
    }else{
        $("#totalQtd").html(qtdTotal-pegaQtd);
        $("#totalValor").html("R$ "+ mascaraValor((valorTotal-pegaValor).toFixed(2)) );
    }
    
    var arr = [];
    var arrGerado = [];
    
    $("input:checkbox[name^='grupo']:checked").each(function(){
        arr.push($(this).val());
        if($("#gerado"+$(this).val()).val() == 'Sim'){
            arrGerado.push($(this).val());
        }
    });
    var selecionados = implode_js(',',arr);  
    var gerados = implode_js(',',arrGerado);  
    $("#idSelecionados").val(selecionados);
    $("#idGerados").val(gerados);
    
    if(selecionados){
        $("#btnTipoAcao").show();
    }else{
        $("#btnTipoAcao").hide();
    }
    
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
        
        //var selecionados = implode_js(',',arr);  
        //$("#idSelecionados").val(selecionados);
        
    }else{
        $('input:checkbox').prop('checked', false);
        $("#idSelecionados").val('');
        $("#idGerados").val('');
    }
    
})


function dump(obj) {
    var out = '';
    for (var i in obj) {
        out += i + ": " + obj[i] + "\n";
    }
    alert(out);
}

$("#data,#tipo_acao").keyup(function(){

    if($("#tipo_acao").val() == ''){
        alert("Data e tipo de ação são obrigatórios");
    }
    
});

$("#limpaData").click(function(){
    $(".data").val('');
    $(this).btnSubmit();
    $("#tabelaConteudo").html('');
});

$('.data').monthpicker({
    monthNames: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
});

$('.data').monthpicker().bind('monthpicker-click-month', function (e, month) {
    $("#totalQtd").html('0');
    $("#totalValor").html("R$ 0.00");
    $("#idSelecionados").val(""); 
    $("#idGerados").val('');   
    $(this).btnSubmit();
})

$.fn.pegaArquivos = function() {
    if($("#tipo_acao_baixar").val() != '' && $("#ano_baixar").val() != ''){
        
        $.ajax({
          type: "POST",
          url: '<?php echo base_url(); ?>tcom-faturamento/ajaxFaturamento/listarArquivosAcao',
          data: {
            tipo_acao: $("#tipo_acao_baixar").val(),
            ano: $("#ano_baixar").val()
          },
          dataType: "json",
          error: function(res) {
            $("#tabelaArquivos").html('<tr><td colspan="3">Erro</td></tr>');
          },
          success: function(res) {
            var conteudo = '';
            if(res.length > 0){
                
                $.each(res, function() {
                    
                    conteudo += '<tr>';
                    conteudo += '<td>';
                    conteudo += '<a target="_blank" href="'+linkAcaoDownload+'/delin/'+$("#ano_baixar").val()+'/'+this.link+'">';
                    conteudo += this.arquivo;
                    conteudo += '</a>';
                    conteudo += '</td>';
                    conteudo += '<td>'+this.status+'</td>';
                    conteudo += '<td>'+this.data_cadastro+'</td>';
                    
                    conteudo += '</tr>';
                
                });
            
            }else{
                conteudo += '<tr><td colspan="3">Nada encontrado</td></tr>';
            }
            
            $("#tabelaArquivos").html(conteudo);
            
          }
        });
        
    }else{
        $("#tabelaArquivos").html('');
    }
}

$("#tipo_acao_baixar").change(function(){
   $(this).pegaArquivos(); 
});

$("#ano_baixar").change(function(){
   $(this).pegaArquivos(); 
})

</script>