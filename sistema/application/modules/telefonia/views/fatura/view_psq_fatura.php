<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
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
                    <li class="active">Operadoras</li>
                </ol>
                <div id="divMain">
                    <?php
                        
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'pesquisar');
                    	echo form_open('telefonia/pesqFatura',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
                            
                            $botaoProcessar = (in_array(203, $this->session->userdata('permissoes')))? "<a href='".base_url('telefonia/faturasArquivos')."' class='linkDireita'>Processar fatura&nbsp<span class='glyphicon glyphicon-plus'></span></a>": '';
                            
                    		echo form_fieldset("Pesquisar fatura".$botaoProcessar, $attributes);
                    		  
                                echo '<div class="row">';
                                    
                                    echo '<div class="col-md-2">';
                                    #print_r(array_keys($listaBancos));
                                    echo form_label('M&ecirc;s/ano', 'mes_ano');
                        			$data = array('name'=>'mes_ano', 'value'=>$this->input->post('mes_ano'),'id'=>'mes_ano', 'placeholder'=>'Digite o m&ecirc;s/ano', 'class'=>'form-control data');
                        			echo form_input($data);
                                    echo '</div>';
                                
                                    echo '<div class="col-md-2">';
                                    $options = array(''=>'');
                                    foreach($linhas as $lin){
                                        $options[$lin->cd_telefonia_linha] = $lin->numero;
                                    }		
                            		echo form_label('Linha', 'linha');
                            		echo form_dropdown('linha', $options, $postLinha, 'id="linha" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-8">';
                                    $options = array(''=>'');
                                    foreach($usuarios as $usu){
                                        $options[$usu->cd_usuario] = $usu->nome_usuario;
                                    }		
                            		echo form_label('Usu&aacute;rio', 'usuario');
                            		echo form_dropdown('usuario', $options, $postUsuario, 'id="usuario" class="form-control"');
                                    echo '</div>';
                                       
                                echo '</div>';                      
                                                                
                                echo '<div class="actions">';
                                echo form_submit("btn_cadastro",utf8_encode("Pesquisar na fatura"), 'class="btn btn-primary pull-right"');
                                echo '</div>';
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
                    
                    ?>        
                </div>
                
                <div class="row">&nbsp</div>
                <?php
                if($pesquisa == 'sim'){
                ?>
                <div class="well">
                <p>
                    <strong>Voc&ecirc; est&aacute; em <?php echo ($qtdDadosCorrente)? $qtdDadosCorrente: 0; ?> de <?php echo ($qtdDados[0]->total)? $qtdDados[0]->total: 0; ?> registros localizados.</strong>
                </p>
                <?php 
                $colunas = array();
                $contCell = 0; 
                foreach($campos as $nome => $display){
                    
                    if($sort_by == $nome){
                        $class = "sort_$sort_order";
                        $class = '';
                        
                        if($sort_order == 'asc'){
                            // Crescente
                            $icoAscDesc = '&nbsp<span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span>';
                        }else{
                            // Descrecente
                            $icoAscDesc = '&nbsp<span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span>';
                        }
                        
                    }else{
                        $class = "";
                        $icoAscDesc = '';
                    }
                    
                    $colunas[] = anchor("telefonia/pesqOperadora/".(($postNome == '')? '0': $postNome)."/".(($postStatus == '')? '0': $postStatus)."/".$nome."/".(($sort_order == 'asc' && $sort_by == $nome) ? 'desc' : 'asc') ,$display.$icoAscDesc, array('class' => $class));
                    
                }
                $colunas[] = 'A&ccedil;&atilde;o';

                #$this->table->set_heading('Login', 'Nome', /*'E-mail',*/ 'Cidade', 'Departamento', 'Perfil', 'A&ccedil;&atilde;o');
                $this->table->set_heading($colunas);
                            
                foreach($dados as $da){
                    
                    #$cell[$contCell++] = array('data' => $da->cd_telefonia_operadora);
                    $cell[$contCell++] = array('data' => html_entity_decode($da->nome));
                    $cell[$contCell++] = array('data' => html_entity_decode($da->status));
                    
                    $botaoEditar = (in_array(203, $this->session->userdata('permissoes')))? '<a title="Editar" href="'.base_url('telefonia/fichaOperadora/'.$da->cd_telefonia_operadora).'" class="glyphicon glyphicon glyphicon-pencil"></a>': '';
                    $botaoExcluir = (in_array(207, $this->session->userdata('permissoes')))? '<a title="Apagar" href="#" onclick="apagarRegistro('.$da->cd_telefonia_operadora.',\''.$da->nome.'\')" data-toggle="modal"  data-target="#apaga" class="glyphicon glyphicon glyphicon glyphicon-remove"></a>': '';
                    
                    $cell[$contCell++] = array('data' => $botaoEditar.$botaoExcluir);
                        
                    $this->table->add_row($cell);
                    $contCell = 0; 
                    
                }
                
            	$template = array('table_open' => '<table class="table zebra">');
            	$this->table->set_template($template);
            	echo $this->table->generate();
                echo "<ul class='pagination pagination-lg'>" . utf8_encode($paginacao) . "</ul>"; 
                ?>
                </div>
                <?php
                }
                ?>
                
            </div>

<script>
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
          .attr( "title", value + "  <?php echo utf8_encode('não localizado'); ?>" )
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
    $( "#linha, #usuario" ).combobox();
    $( "#toggle" ).click(function() {
      $( "#linha, #usuario" ).toggle();
    });
  });
  </script>    
<script type="text/javascript">

function apagarRegistro(cd, nome){
    $("#apg_cd").val(cd);
    $("#apg_nome").val(nome);
}

$(document).ready(function(){
    
    $(".data").mask("00/0000");
    
    $(".actions").click(function() {
        $('#aguarde').css({display:"block"});
    });
});


/*
CONFIGURA O CALENDÁRIO DATEPICKER NO INPUT INFORMADO
*/
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
});

$(document).ready(function(){
    
    // Valida o formulário
	$("#pesquisar").validate({
		debug: false,
		rules: {
			mes_ano: {
                required: true
            }
		},
		messages: {
			mes_ano: {
                required: "Informe uma data."
            }
	   }
   });   
   
});

</script>