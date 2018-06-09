<?php
echo link_tag(array('href' => 'assets/js/drag_drop/style.css','rel' => 'stylesheet','type' => 'text/css'));
#echo "<script type='text/javascript' src='".base_url('assets/js/jquery.blockui/jquery.block.ui.js')."'></script>";
?>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/drag_drop/fieldChooser.js") ?>"></script>
<style>
.custom-combobox {
    position: relative;
    display: inline-block;
    width: 97%;
}
.custom-combobox-toggle {
    position: absolute;
    top: 0;
    bottom: 0;
    margin-left: -1px;
    padding: 0;
}
.custom-combobox-input {
    margin: 0;
    padding: 5px 10px;
    width: 96%;
    color: #696969;
    font-weight: normal;
}
</style>

            <div class="col-md-10 col-sm-9">
            <!--<div class="col-lg-12">-->
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a></li>
                    <li><a href="<?php echo base_url('telefonia'); ?>">Telefonia</a></li>
                    <li class="active">Ficha empr&eacute;stimo</li>
                </ol>
                <div id="divMain">
                    <?php
                        echo $observacao;
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'frm-salvar');
                    	echo form_open('telefonia/salvaEmprestimo',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
             
                    		echo form_fieldset("Ficha Empr&eacute;stimo<a href='".base_url('telefonia/emprestimos')."' class='linkDireita'><span class='glyphicon glyphicon-arrow-left'></span>&nbspVoltar Pesquisar</a>", $attributes);
                    		
                                echo '<div class="row">';
                                    
                                    echo '<div class="col-md-6">';
                                    $options = array('' => '');		
                                    foreach($aparelhos as $apa){
                                        $options[$apa->cd_telefonia_aparelho] = '('.$apa->cd_telefonia_aparelho.') '.$apa->imei.' - '.$apa->marca.' - '.$apa->modelo;
                                    }
                            		echo form_label('Aparelho<span class="obrigatorio">*</span>', 'cd_telefonia_aparelho');
                            		echo form_dropdown('cd_telefonia_aparelho', $options, $cd_telefonia_aparelho, 'id="cd_telefonia_aparelho" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-6 ui-widget">';
                                    $options = array('' => '');		
                                    foreach($usuarios as $usu){
                                        $options[$usu->cd_usuario] = htmlentities($usu->nome_usuario).' - '.$usu->matricula_usuario.' ('.htmlentities($usu->unidade).')';
                                    }
                            		echo form_label('Usu&aacute;rio<span class="obrigatorio">*</span>', 'cd_usuario');
                            		echo form_dropdown('cd_usuario', $options, $cd_usuario, 'id="cd_usuario" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    echo form_label('Data In&iacute;cio<span class="obrigatorio">*</span>', 'data_inicio');
                        			$data = array('name'=>'data_inicio', 'value'=>$data_inicio,'id'=>'data_inicio', 'placeholder'=>'Digite a data', 'class'=>'form-control data');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    echo form_label('Data Fim<span class="obrigatorio">*</span>', 'data_fim');
                        			$data = array('name'=>'data_fim', 'value'=>$data_fim,'id'=>'data_fim', 'placeholder'=>'Digite a data', 'class'=>'form-control data');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                echo '</div>';
                                                              
                                echo '<div class="actions">';
                                
                                echo form_hidden('cd_aparelho_original', $cd_telefonia_aparelho);
                                echo form_hidden('cd_telefonia_emprestimo', $cd_telefonia_emprestimo);
                                
                                echo form_submit("btn_cadastro","Salvar", 'id="btn_cadastro" class="btn btn-primary pull-right"');
                                echo '</div>';   
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
                    
                    if($cd_telefonia_emprestimo){    
                    ?> 
                    <div class="row text-center">
                        <div><strong>Linhas associadas ao empr&eacute;stimo</strong></div>
                        <div id="res_associacao"></div>
                    </div>
                    <div class="row" id="fieldChooser" tabIndex="1">
                        <div class="row">
                            <div class="col-md-6">
                            <?php
                            $options = array('' => '');	
                            foreach($ddds as $ddd){
                                $options[$ddd->cd_telefonia_ddd] = $ddd->ddd.' - '.htmlentities($ddd->estado).' - '.htmlentities($ddd->cidade_regiao);
                            }
                    		echo form_label('Selecione o DDD<span class="obrigatorio">*</span>', 'ddd');
                    		echo form_dropdown('ddd', $options, '', $disabled.' id="ddd" class="form-control"');
                            ?>
                            </div>
                            <div class="col-md-6">
                            <?php
                            echo form_label('Linha', 'linha');
                			$data = array('name'=>'linha', 'value'=>$nota_fiscal,'id'=>'linha', $readonly => true, 'placeholder'=>'Digite a linha', 'class'=>'form-control celular');
                			echo form_input($data);
                            ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                        Linhas dispon&iacute;veis
                        </div>
                        <div class="col-md-6">
                        Linhas associadas ao empr&eacute;stismo
                        </div>
                        <div id="sourceFields">
                            <!--
                            <div id="dado_1">First name</div>
                            <div id="dado_2">Last name</div>
                            <div id="dado_3">Home</div>
                            <div id="dado_4">Work</div>
                            <div id="dado_5">Direct</div>
                            <div id="dado_6">Cell</div>
                            <div id="dado_7">Fax</div>
                            <div id="dado_8">Work email</div>
                            <div id="dado_9">Personal email</div>
                            -->
                        </div>
                        <div style="height: 150px;" id="destinationFields">
                            <?php foreach($linhasEmprestimo as $lA){?>
                            <div id="dado_<?php echo $lA->cd_telefonia_linha; ?>"><?php echo $lA->ddd.' - '.$lA->numero; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php
                    }
                    ?>         
                </div>
            </div>
    
<script type="text/javascript">

function dump(obj) {
    var out = '';
    for (var i in obj) {
        out += i + ": " + obj[i] + "\n";
    }
    alert(out);
}

function marcaTodos(){
    
    if($('#todos').prop('checked') == true){
        $('input:checkbox').prop('checked', true);
    }else{
        $('input:checkbox').prop('checked', false);
    }
    
}

function marcaGrupo(classe, campo){
    
    if(campo.checked == true){
        $(classe).prop('checked', true);
    }else{
        $(classe).prop('checked', false);
    }

}

$(document).ready(function(){
    
    $(".data").mask("00/00/0000");
    $(".valor").mask("0.00##"/*, {reverse: true}*/);
    $(".real").mask("##0.00"/*, {reverse: true}*/);
    $(".celular").mask("000000000");
    $(".qtd").mask("###0");
    
    $(".actions").click(function() {
        $('#aguarde').css({display:"block"});
    });
});


/*
CONFIGURA O CALENDÁRIO DATEPICKER NO INPUT INFORMADO
*/
$("#data_inicio,#data_fim").datepicker({
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



$(document).ready(function(){

    $("#btn_cadastro").hide();
    
    $("#btn_cadastro").mouseover(function (){
        if($("#cd_telefonia_aparelho").val() == '' || $("#cd_usuario").val() == ''){
            $("#btn_cadastro").hide();
            return false;
        }
    })
    
    $("#cd_telefonia_aparelho").combobox({ 
        select: function (event, ui) { 
            if(this.value != '' && $("#cd_usuario").val() != ''){
                $("#btn_cadastro").show();
            }else{
                $("#btn_cadastro").hide();
            }
        } 
    });
    
    $("#cd_usuario").combobox({ 
        select: function (event, ui) { 
            if(this.value != '' && $("#cd_telefonia_aparelho").val() != ''){
                $("#btn_cadastro").show();
            }else{
                $("#btn_cadastro").hide();
            }
        } 
    });
    
    // Valida o formulário
	$("#frm-salvar").validate({
		debug: false,
		rules: {
            cd_telefonia_aparelho:{
                required: true  
            }/*,
			cd_usuario: {
                required: true,
            },
            cd_telefonia_oferta: {
                required: true
            },
            data_inicio: {
                required: true,
                minlength: 10
            },
            data_fim: {
                required: true,
                minlength: 10
            },
            valor_fidelizado: {
                required: true,
            },
            valor_multa: {
                required: true,
            },*/
		},
		messages: {
            cd_telefonia_aparelho:{
                required: "Selecione o aparelho"  
            }/*,
			cd_usuario: {
                required: "<?php echo utf8_encode('Selecione o usuário'); ?>",
            },
            cd_telefonia_oferta: {
                required: 'Selecione a oferta'
            },
            data_inicio: {
                required: 'Informe a data',
                minlength: 'Informe a data completa'
            },
            data_fim: {
                required: 'Informe a data',
                minlength: 'Informe a data completa'
            },
            valor_fidelizado: {
                required: 'Digite o valor',
            },
            valor_multa: {
                required: 'Digite o valor',
            },*/
	   }
   });   
   
});

  (function( $ ) {
    $.widget( "custom.combobox", {
      _create: function() {
        this.wrapper = $( "<span>" )
          .addClass( "custom-combobox" )
          .insertAfter( this.element );
 
        this.element.hide();
        this._createAutocomplete();
        this._createShowAllButton();
      },
 
      _createAutocomplete: function() {
        var selected = this.element.children( ":selected" ),
          value = selected.val() ? selected.text() : "";
 
        this.input = $( "<input>" )
          .appendTo( this.wrapper )
          .val( value )
          .attr( "title", "" )
          .addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left" )
          .autocomplete({
            delay: 0,
            minLength: 0,
            source: $.proxy( this, "_source" )
          })
          .tooltip({
            tooltipClass: "ui-state-highlight"
          });
 
        this._on( this.input, {
          autocompleteselect: function( event, ui ) {
            ui.item.option.selected = true;
            this._trigger( "select", event, {
              item: ui.item.option
            });
          },
 
          autocompletechange: "_removeIfInvalid"
        });
      },
 
      _createShowAllButton: function() {
        var input = this.input,
          wasOpen = false;
 
        $( "<a>" )
          .attr( "tabIndex", -1 )
          .attr( "title", "Abrir todos" )
          .tooltip()
          .appendTo( this.wrapper )
          .button({
            icons: {
              primary: "ui-icon-triangle-1-s"
            },
            text: false
          })
          .removeClass( "ui-corner-all" )
          .addClass( "custom-combobox-toggle ui-corner-right" )
          .mousedown(function() {
            wasOpen = input.autocomplete( "widget" ).is( ":visible" );
          })
          .click(function() {
            input.focus();
 
            // Close if already visible
            if ( wasOpen ) {
              return;
            }
 
            // Pass empty string as value to search for, displaying all results
            input.autocomplete( "search", "" );
          });
      },
 
      _source: function( request, response ) {
        var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
        response( this.element.children( "option" ).map(function() {
          var text = $( this ).text();
          if ( this.value && ( !request.term || matcher.test(text) ) )
            return {
              label: text,
              value: text,
              option: this
            };
        }) );
      },
 
      _removeIfInvalid: function( event, ui ) {
 
        // Selected an item, nothing to do
        if ( ui.item ) {
          return;
        }
 
        // Search for a match (case-insensitive)
        var value = this.input.val(),
          valueLowerCase = value.toLowerCase(),
          valid = false;
        this.element.children( "option" ).each(function() {
          if ( $( this ).text().toLowerCase() === valueLowerCase ) {
            this.selected = valid = true;
            return false;
          }
        });
 
        // Found a match, nothing to do
        if ( valid ) {
          return;
        }
 
        // Remove invalid value
        this.input
          .val( "" )
          .attr( "title", value + " <?php echo utf8_encode('não localizado'); ?>" )
          .tooltip( "open" );
        this.element.val( "" );
        this._delay(function() {
          this.input.tooltip( "close" ).attr( "title", "" );
        }, 2500 );
        this.input.autocomplete( "instance" ).term = "";
      },
 
      _destroy: function() {
        this.wrapper.remove();
        this.element.show();
      }
    });
  })( jQuery );
 
  $(function() {
    $( "#cd_usuario, #cd_telefonia_aparelho" ).combobox();
    $( "#toggle" ).click(function() {
      $( "#cd_usuario, #cd_telefonia_aparelho" ).toggle();
    });
  });
  
/*$(function() {
$( "#cd_usuario, #mySelect" ).combobox();
});    */

<?php if($cd_telefonia_emprestimo){ ?>
var $sourceFields = $("#sourceFields");
var $destinationFields = $("#destinationFields");
var $chooser = $("#fieldChooser").fieldChooser(sourceFields, destinationFields, false);

// atualizar dinamicamente
$(function(){
	$("#destinationFields").sortable({
		opacity: 0.6,
		cursor: 'move',
		update: function(){
            
            setTimeout(function() {
                var selecionados = $("#destinationFields").sortable('toArray');  
                
                <?php if($visualizar){ ?>
                $("#res_associacao").html('<div class="alert alert-danger" role="alert"><b>Opera&ccedil;&atilde;o n&atilde;o permitida!</b></div>');
                setTimeout(function() {
               	    location.reload();
                }, 2000);
                <?php }else{ ?>
                
                if(selecionados.length <= 2){           
                $.post('<?php echo base_url(); ?>ajax/associaLinhaEmprestimo', {'selecionados':selecionados, 'cd_emprestimo': <?php echo $cd_telefonia_emprestimo; ?>}, function(retorno){
        			$("#res_associacao").html('<div class="alert alert-success" role="alert"><b>'+retorno+'</b></div>');
        		}); 
                }else{
                    $("#res_associacao").html('<div class="alert alert-danger" role="alert"><b>M&aacute;ximo de duas linhas por empr&eacute;stimo!</b></div>');
                    setTimeout(function() {
                   	    location.reload();
                    }, 2000);
                }
                
                <?php } ?>
                
            }, 1);  
            
            setTimeout(function() {
           	    $("#res_associacao").html('');
            }, 3000);
		}
        
	});
    
    $("#sourceFields").sortable({
		opacity: 0.6,
		cursor: 'move',
		update: function(){
            
            setTimeout(function() {
                var selecionados = $("#sourceFields").sortable('toArray');  
                
                <?php if($visualizar){ ?>
                $("#res_associacao").html('<div class="alert alert-danger" role="alert"><b>Opera&ccedil;&atilde;o n&atilde;o permitida!</b></div>');
                setTimeout(function() {
               	    location.reload();
                }, 2000);
                <?php }else{ ?>
                          
                $.post('<?php echo base_url(); ?>ajax/desassociaLinhaEmprestimo', {'selecionados':selecionados}, function(retorno){
        			//$("#res_associacao").html(retorno);
        		}); 
                
                <?php } ?>
                
            }, 1);  
            
            /*setTimeout(function() {
           	    $("#res_associacao").html('');
            }, 3000);*/
		}
        
	});
});

$("#ddd").change(function(){
        
    if($(this).val() != ''){
        
        $(this).linhasDisponiveis($(this).val(), $("#linha").val());
        
    }else{
        $("#sourceFields").html('');
    }
    
});

$("#linha").keyup(function(){
    if($("#ddd").val() != ''){
        $(this).linhasDisponiveis($("#ddd").val(), $(this).val());
    }else{
        $("#sourceFields").html('');
    }
});

$.fn.linhasDisponiveis = function(ddd, linha) { 
    
    $.ajax({
      type: "POST",
      url: '<?php echo base_url(); ?>ajax/linhasAssociadasDDD',
      data: {
        cd_ddd: ddd,
        linha: linha
      },
      dataType: "json",
      error: function(res) {
        //$("#resQtdArquivosDia").html('<span>Erro de execução</span>');
        alert('erro');
      },
      success: function(res) {
        
       var conteudoHtml = '';
        if(res.length > 0){
        
            $.each(res, function() {
              
              conteudoHtml += '<div class="fc-field" id="dado_'+this.cd_telefonia_linha+'">'+this.ddd+' - '+this.numero+'</div>';
              
            });
            
            $("#sourceFields").html(conteudoHtml);
        
        }else{
            
            $("#sourceFields").html('');
            
        }
        
      }
    });
    
};

<?php } ?>

</script>