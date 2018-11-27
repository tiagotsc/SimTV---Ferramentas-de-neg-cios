<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>

 <!-- INÍCIO Modal Baixar relatório -->
<div class="modal fade" id="modalPai" tabindex="-1" role="dialog" aria-labelledby="modalPai" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">
                <?php              
                    $data = array('class'=>'pure-form','id'=>'relatorios');
                    echo form_open($this->session->userdata('indexPHP').'relatorio/baixarRelatorio',$data);
                ?>
                <strong id="inf_parametros"></strong><br />
                <div id="conteudoModal"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                <button type="submit" class="btn btn-primary">Gerar relat&oacute;rio</button>
        </div>
                <?php
                echo form_close();
                ?>
    </div>
  </div>
</div>
<!-- FIM Modal Relatório Arquivos Retorno -->

            <!--<div class="col-md-9 col-sm-8"> USADO PARA SIDEBAR -->
            <div class="col-lg-12">
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a>
                    </li>
                    <li class="active">Relat&oacute;rios</li>
                </ol>
                <div id="divMain">
                    <h3>Relat&oacute;rios</h3>
                    <div id="todosRelatorios">
                    
                        <?php /*error_reporting(0);*/ #echo '<pre>'; print_r($relatorios); exit();
                        foreach($departamentos as $der){ 
                        ?>
                          <h3><?php echo htmlentities($der->nome_departamento);?></h3>
                          <div>
                            <div>
                                <ul class="list-group">
                            <?php 
                                #$todosRelatorios = $relatorios[$der->cd_departamento];
                                
                                #if($todosRelatorios){                                
                                                                
                                    foreach($relatorios[$der->cd_departamento] as $relat){
                                    
                                        $botaoEditar = (in_array(34, $this->session->userdata('permissoes')))? '<a title="Editar" href="'.base_url('relatorio/ficha/'.$relat->cd_relatorio).'" class="glyphicon glyphicon glyphicon-pencil"></a>': '';
                            ?>
                                    <li class="list-group-item">
                                        <a data-toggle="modal" data-target="#modalPai" href="#" onclick="$(this).alimentaModal(<?php echo $relat->cd_relatorio; ?>,'<?php echo htmlentities($relat->nome_relatorio); ?>','<?php echo htmlentities($relat->descricao_parametro_relatorio); ?>')">
                                            <strong><?php echo htmlentities($relat->nome_relatorio); ?></strong>
                                        </a>
                                        &nbsp&nbsp&nbsp
                                        <?php echo $botaoEditar; ?>
                                        <p class="spanRelatorio">Descri&ccedil;&atilde;o: <?php echo htmlentities($relat->descricao_relatorio); ?></p>
                                    </li>
                            
                                <?php
                                    } //Foreach  
                                #} // Fecha If 
                                 
                                ?>
                                </ul>
                            </div>
                          </div>
                         <?php                          
                         } // Foreach
                         ?>
                    </div>
                           <div id="aguarde" style="text-align: center; display: none"><img src="<?php echo base_url('assets/img/aguarde.gif');?>" /></div>        
                </div>
            </div>
    
<script type="text/javascript">

//attach a jQuery live event to the button
$.fn.alimentaModal = function(cd_relatorio, nome_relatorio, desc_parametros) {
    
    $("#myModalLabel").html(nome_relatorio);
    $("#inf_parametros").html(desc_parametros);
    
    $.getJSON('<?php echo base_url().$this->session->userdata('indexPHP'); ?>ajax/parametrosRelatorio/'+cd_relatorio, function(data) {
        
        var campos = '';
        var todosCampos = new Array();
        var todasMascaras = new Array();
        var todosTipoParametro = new Array();
        
        var i = 0;
        $.each( data, function( key, campo ){
            
            todosTipoParametro[i] = campo.tipo_parametro;
            todosCampos[i] = '#'+campo.campo_parametro;
            todasMascaras[i] = campo.mascara_parametro;
            
            campos += '<label>'+campo.campo_personalizado+'<input maxlength="30" type="text" id="'+campo.campo_parametro+'" name="'+campo.campo_parametro+'" class="form-control" /></label>';
            
            //$("#"+campo.campo_parametro).mask(campo.mascara_parametro);
            
            i++;
            
        });
        
        campos += '<input type="hidden" name="cd_relatorio" value="'+cd_relatorio+'" />';
        
        //var juntaTodosCampos = todosCampos.join(',');
        
        $("#conteudoModal").html(campos);
        
        for (i = 0; i < todosCampos.length; i++) { 
            
            if(todasMascaras[i] != ''){
            
                if(todosTipoParametro[i] != 'LETRA' && todosTipoParametro[i] != 'MOEDA'){
                    $(todosCampos[i]).mask(todasMascaras[i]);
                }
            
            }
            
            if(todosCampos[i].search("data") >= 0){
                //alert(todosCampos[i]);
                
                $(todosCampos[i]).datepicker({
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
                
            }
            
        }
        /*
        if(juntaTodosCampos.search("data") > 0){
            
            //$(juntaTodosCampos).mask("00/00/0000");
            
            $(juntaTodosCampos).datepicker({
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
        
        }    
        */
    })
};


function dump(obj) {
    var out = '';
    for (var i in obj) {
        out += i + ": " + obj[i] + "\n";
    }
    alert(out);
}


$(document).ready(function(){

    $(function() {
        $( "#todosRelatorios" ).accordion({
            collapsible: true, // Habilita a opção de expandir e ocultar ao clicar
            heightStyle: "content",
            active: false
        });
    });
    
    //$(".data").mask("00/00/0000");
    
    $(".actions").click(function() {
        $('#aguarde').css({display:"block"});
    });
});

/*
CONFIGURA O CALENDÁRIO DATEPICKER NO INPUT INFORMADO
*/
/*
$("#data1,#data2").datepicker({
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
*/
$(document).ready(function(){
    
    // Valida o formulário
	$("#relatorios").validate({
		debug: false,
		rules: {
			data: {
                required: true
            }
		},
		messages: {
			data: {
                required: "Informe uma data."
            }
	   }
   });   
   
});

</script>