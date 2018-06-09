<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>

            <!--<div class="col-md-9 col-sm-8"> USADO PARA SIDEBAR -->
            <div class="col-lg-12">
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a>
                    </li>
                    <li class="active">Ficha relat&oacute;rio</li>
                </ol>
                <div id="divMain">
                    <?php
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'salvar_relatorio');
                    	echo form_open('relatorio/salvar',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
             
                    		echo form_fieldset("Ficha relat&oacute;rio<a href='".base_url('relatorio/gerenciar')."' class='linkDireita'><span class='glyphicon glyphicon-arrow-left'></span>&nbspVoltar Pesquisar</a>", $attributes);
                    		
                                echo '<div class="row">';
                                
                                    echo '<div class="col-md-3">';
                                    echo form_label('Nome<span class="obrigatorio">*</span>', 'nome_relatorio');
                        			#$data = array('name'=>'nome_relatorio', 'value'=>html_entity_decode($nome_relatorio),'id'=>'nome_relatorio', 'title'=>'Nome que ser&aacute; exibido na lista de relat&oacute;rios', 'placeholder'=>'D&ecirc; um nome para o relat&oacute;rio', 'class'=>'form-control', 'maxlength'=>'100');
                        			#echo form_input($data);
                                    echo '<input type="text" id="nome_relatorio" name="nome_relatorio" value="'.utf8_decode($nome_relatorio).'" title="Nome que será exibido na lista do relatório" placeholder="Dê um nome ao relatório" class="form-control" maxlength="100" />';
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array('' => '');		
                            		foreach($departamento as $dep){
                            			$options[$dep->cd_departamento] = htmlentities($dep->nome_departamento);
                            		}	
                            		echo form_label('Departamento<span class="obrigatorio">*</span>', 'cd_departamento');
                            		echo form_dropdown('cd_departamento', $options, $cd_departamento, 'id="cd_departamento" title="Departamento que o relat&oacute;rio ficar&aacute; associado." class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array(''=>'', 'mysql' => 'mysql', 'oracle'=>'oracle', 'siga_bcv'=>'siga bcv', 'ura' => 'telefonia ura');		
                            		echo form_label('Banco conex&atilde;o<span class="obrigatorio">*</span>', 'banco_relatorio');
                            		echo form_dropdown('banco_relatorio', $options, $banco_relatorio, 'id="banco_relatorio" title="Banco de dados que o relat&oacute;rio ser&aacute; rodado." class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array('A' => 'Ativo', 'I' => 'Inativo');		
                            		echo form_label('Status<span class="obrigatorio">*</span>', 'status_relatorio');
                            		echo form_dropdown('status_relatorio', $options, $status_relatorio, 'id="status_relatorio" title="Disponibilidade do relat&oacute;rio" class="form-control"');
                                    echo '</div>';
                                    
                                echo '</div>';
                                
                                echo '<div class="row">';
                                    #echo mb_convert_encoding( $descricao_relatorio , 'UTF-8' );
                                    #echo mb_convert_encoding($descricao_relatorio, "ISO-8859-1", "UTF-8");
                                    #echo mb_convert_encoding(html_entity_decode($descricao_relatorio), "ISO-8859-1", "UTF-8");
                                    #echo htmlentities($descricao_relatorio, ENT_COMPAT, 'UTF-8');
                                    #echo convert_accented_characters($descricao_relatorio);
                                    echo '<div class="col-md-6">';
                                    echo form_label('Descri&ccedil;&atilde;o do relat&oacute;rio<span class="obrigatorio">*</span>', 'descricao_relatorio');
                        			#$data = array('name'=>'descricao_relatorio', 'value'=>html_entity_decode($descricao_relatorio),'id'=>'descricao_relatorio', 'title'=>'Descri&ccedil;&atilde;o que ser&aacute; exibida na lista de relat&oacute;rios', 'placeholder'=>'Descreva o relat&oacute;rio', 'class'=>'form-control', 'maxlength'=>'200');
                        			#echo form_input($data);
                                    echo '<input type="text" id="descricao_relatorio" name="descricao_relatorio" value="'.utf8_decode($descricao_relatorio).'" title="Descri&ccedil;&atilde;o que ser&aacute; exibida na lista de relat&oacute;rios" placeholder="Descreva o relat&oacute;rio" class="form-control" maxlength="200" />';
                                    echo '</div>';
                                    
                                    #echo '<label>Descrição do relatório<span class="obrigatorio">*</span>';
                                    #echo '<input type="text" id="descricao_relatorio" name="descricao_relatorio" class="form-control" title="Descrição que será exibida na lista de relatórios" placeholder="Descreva o relatório" maxlength="200" value="'.$descricao_relatorio.'"></label>';
                                    
                                    echo '<div class="col-md-6">';
                                    
                                    echo form_label('Descri&ccedil;&atilde;o dos par&acirc;metros', 'descricao_parametro_relatorio');
                        			#$data = array('name'=>'descricao_parametro_relatorio', 'value'=>html_entity_decode($descricao_parametro_relatorio),'id'=>'descricao_parametro_relatorio', 'title'=>'Descri&ccedil;&atilde;o que ser&aacute; exibida na janela de preechimento de par&acirc;metros', 'placeholder'=>'Descreva como o usu&aacute;rio deve preencher os par&acirc;metros', 'class'=>'form-control', 'maxlength'=>'70');
                        			#echo form_input($data);
                                    echo '<input type="text" id="descricao_parametro_relatorio" name="descricao_parametro_relatorio" value="'.utf8_decode($descricao_parametro_relatorio).'" title="Descri&ccedil;&atilde;o que ser&aacute; exibida na janela de preechimento de par&acirc;metros" placeholder="Descreva como o usu&aacute;rio deve preencher os par&acirc;metros" class="form-control" maxlength="70" />';
                                    echo '</div>';

                                echo '</div>';
                                    
                                echo '<div class="row">';
                                
                                    echo '<div class="col-md-5">';
                                    $options = array();
                                    foreach($parametros as $par){
                            			$options[$par->cd_parametro] = $par->legenda_parametro.'&nbsp&nbsp - &nbsp&nbsp'.$par->variavel_parametro;
                            		}
                                    
                                    echo form_label('Selecione o(s) par&acirc;metro(s) <a href="#" onclick="$(\'#cd_parametro\').val(\'\').prop(\'selected\', true);$(\'#nome_parametros\').html(\'\');">Limpar par&acirc;metros</a>', 'cd_parametro');
                                    echo form_dropdown('cd_parametro[]', $options, $rel_param, 'id="cd_parametro" title="Par&acirc;metros que precisar&atilde;o ser preenchido para gerar o relat&oacute;rio" class="form-control" style="height:200px"');
                                    echo '</div>';
                                
                                    echo '<div class="col-md-3">';
                                        echo '<div id="nome_parametros">';
                                        
                                        if($nome_parametros){
                                            $i = 1;
                                            foreach($nome_parametros as $nomePar){
                                                
                                                echo form_label('D&ecirc; um nome para o '.$i.'&ordf; par&acirc;metro<span class="obrigatorio"> *</span>', 'nome_relatorio_parametro['.$nomePar->cd_parametro.']');
                                    			#$data = array('name'=>'nome_relatorio_parametro['.$nomePar->cd_parametro.']', 'value'=>html_entity_decode($nomePar->campo_personalizado),'id'=>'nome_relatorio_parametro['.$nomePar->cd_parametro.']', 'placeholder'=>'D&ecirc; um nome para o campo.', 'title'=>'Preencha o nome do campo', 'class'=>'required form-control', 'maxlength'=>'200');
                                    			#echo form_input($data);
                                                echo '<input type="text" id="nome_relatorio_parametro['.$nomePar->cd_parametro.']" name="nome_relatorio_parametro['.$nomePar->cd_parametro.']" value="'.utf8_decode($nomePar->campo_personalizado).'" title="Preencha o nome do campo" placeholder="D&ecirc; um nome para o campo." class="required form-control" maxlength="200" />';
                                                $i++;
                                            }
                                            
                                        }

                                        echo '</div>';
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                        echo '<strong>Perfis que ter&atilde;o acesso:</strong>';
                                        echo '<div id="lista_perfis_relatorio">';
                                            foreach($perfis as $perfil){
                                                
                                                if(in_array($perfil->cd_perfil, $perfilRelatorio)){
                                                    $checked = true;
                                                }else{
                                                    $checked = false;
                                                }
                                                
                                                $data = array(
                                                    'name'        => 'cd_perfil[]',
                                                    'id'          => 'cd_perfil[]',
                                                    'value'       => $perfil->cd_perfil,
                                                    'checked'     => $checked,
                                                    'style'       => 'margin:10px',
                                                    #'class'       => 'form-control'
                                                    );
                                                echo '<label>';
                                                echo form_checkbox($data);
                                                echo $perfil->nome_perfil.'</label>';
                                            
                                            }
                                        echo '</div>';
                                    
                                    echo '</div>';
                                    
                                echo '</div>';
                                    
                                echo '<div class="row">';    
                                
                                    echo '<div class="col-md-12">';
                                    echo form_label('Query do relat&oacute;rio<span class="obrigatorio">*</span>', 'query_relatorio');
                        			$data = array('name'=>'query_relatorio', 'value'=>$query_relatorio,'id'=>'query_relatorio', 'placeholder'=>'Coloque a query aqui e configure os par&acirc;metros, caso necess&aacute;rio', 'class'=>'form-control', 'style'=>'height:400px');
                        			echo form_textarea($data);
                                    echo '</div>';
                                
                                echo '<div>';
                                
                                echo '<div class="row"></div>';
                                                              
                                echo '<div class="actions">';
                                
                                echo form_hidden('cd_relatorio', $cd_relatorio);
                                
                                echo form_hidden('cd_permissao', $cd_permissao);
                                
                                echo form_submit("btn_cadastro","Salvar", 'class="btn btn-primary pull-right"');
                                echo '</div>';   
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
                        
                        
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
    
    $("#cd_parametro").click(function(){
        
        var conteudo = '';
        var i = 0;
        $("#cd_parametro option:selected ").each(function(){
            /*var v = $(this).attr("value"); // first select's value
            $('#bar option').each(function(){
              if ($(this).attr("value") == v) { 
                $(this).attr("selected",true); // select if same value
              }
            });*/
            
            //nome_parametros
            //alert($(this).text());
            //dump(this);
            var valor = '';
            
            if(typeof $('input[name="nome_relatorio_parametro['+$(this).val()+']"]').val() != 'undefined'){
                valor = $('input[name="nome_relatorio_parametro['+$(this).val()+']"]').val();
            }else{
                valor = '';
            }
            
            i++;
            
            conteudo += '<label for="nome_relatorio_parametro['+$(this).val()+']">';
            //conteudo += 'Dê um nome para '+$(this).text();
            conteudo += 'D&ecirc; um nome para o '+i+'&ordf; par&acirc;metro';
            conteudo += '<span class="obrigatorio"> *</span></label>';
            conteudo += '<input maxlength="100" type="text" placeholder="D&ecirc; um nome para o campo." title="Preencha o nome do campo" class="required form-control" id="nome_relatorio_parametro['+$(this).val()+']" name="nome_relatorio_parametro['+$(this).val()+']" value="'+valor+'" />';
            
            
        });
        
        $('#salvar_relatorio').validate();
        
        $("#nome_parametros").html(conteudo);
        
    });    
    
    $(".data").mask("00/00/0000");
    
    $(".actions").click(function() {
        $('#aguarde').css({display:"block"});
    });
});


/*
CONFIGURA O CALENDÁRIO DATEPICKER NO INPUT INFORMADO
*/
$("#data,#data2").datepicker({
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
    
    // Valida o formulário
	$("#salvar_relatorio").validate({
		debug: false,
		rules: {
			nome_relatorio: {
                required: true
            },
            cd_departamento: {
                required: true
			},
            banco_relatorio: {
                required: true
			},
            status_relatorio: {
                required: true
			},
            descricao_relatorio: {
                required: true
			},
            /*descricao_parametro_relatorio: {
                required: true
			},*/
            /*cd_parametro: {
                required: true
            },*/
            query_relatorio: {
                required: true
            }
		},
		messages: {
			nome_relatorio: {
                required: "Informe um nome para o relat&oacute;rio."
            },
            cd_departamento: {
                required: "Selecione o departamento."
            },
            banco_relatorio: {
                required: "Selecione o banco."
            },
            status_relatorio: {
                required: "Selecione o status."
            },
            descricao_relatorio: {
                required: "Descreva o relat&oacute;rio."
            },
            /*descricao_parametro_relatorio: {
                required: "Descreva os parâmetros do relatório."
            },*/
            /*cd_parametro: {
                required: "Selecione pelo menos um parâmetro."
            },*/
            query_relatorio: {
                required: "Coloque uma query para gerar o relat&oacute;rio."
            }
	   }
   });   
   
});

</script>