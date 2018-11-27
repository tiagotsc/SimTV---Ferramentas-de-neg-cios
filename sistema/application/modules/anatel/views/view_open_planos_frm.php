<?php
echo link_tag(array('href' => 'assets/js/drag_drop/style.css','rel' => 'stylesheet','type' => 'text/css'));
echo "<script type='text/javascript' src='".base_url('assets/js/jquery.blockui/jquery.block.ui.js')."'></script>";
?>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/drag_drop/fieldChooser.js") ?>"></script>  

            <!--<div class="col-md-9 col-sm-8"> USADO PARA SIDEBAR -->
            <div class="col-lg-12">
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a>
                    </li>
                    <li class="active">Anatel - Responder formul&aacute;rio</li>
                </ol>
                <div id="divMain">
                    <?php  
                        
                        if($unidadeUsuario == null){
                            echo '<div class="alert alert-danger" role="alert"><strong>Resposta bloqueada! Informe sua unidade ao administrador para que voc&ecirc; possa responder o formul&aacute;rio.</strong></div>';
                        }
                                     
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'salvar_frm');
                    	echo form_open('anatel/salvarRespForm',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
             
                    		echo form_fieldset("Anatel - Responder formul&aacute;rio", $attributes);
                    		
                                echo '<div class="row">';
                                    
                                    echo '<div class="col-md-4">';
                                    echo form_label('Formul&aacute;rio', 'tipo_frm');
                            		$data = array('id'=>'tipo_frm', 'name'=>'tipo_frm', 'class'=>'form-control', 'readonly' => 'readonly', 'title' => $dadosFrm[0]->tipo_frm_descricao);
                            		echo form_input($data,$dadosFrm[0]->tipo_frm);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    echo form_label('Indicador', 'indicador');
                            		$data = array('id'=>'indicador', 'name'=>'indicador', 'class'=>'form-control', 'readonly' => 'readonly', 'title' => $dadosFrm[0]->nome_indicador);
                            		echo form_input($data,$dadosFrm[0]->sigla_indicador);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    echo form_label('Departamento', 'departamento');
                            		#$data = array('id'=>'departamento', 'name'=>'departamento', 'class'=>'form-control', 'readonly' => 'readonly');
                            		#echo form_input($data,utf8_decode($dadosFrm[0]->nome_departamento));
                                    echo '<input type="text" name="departamento" class="form-control" readonly="readonly" value="'.utf8_decode($dadosFrm[0]->nome_departamento).'"/>';
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    echo form_label('Unidade', 'unidade');
                            		#$data = array('id'=>'unidade', 'name'=>'unidade', 'class'=>'form-control', 'readonly' => 'readonly', 'title' => $dadosFrm[0]->nome_indicador);
                            		#echo form_input($data,$dadosFrm[0]->nome_unidade);
                                    echo '<input type="text" id="unidade" name="unidade" class="form-control" readonly="readonly" title="'.utf8_decode($dadosFrm[0]->nome_indicador).'" value="'.utf8_decode($dadosFrm[0]->nome_unidade).'"/>';
                                    echo '<input type="hidden" name="cd_anatel_xml" value="'.$dadosFrm[0]->cd_anatel_xml.'" />';
                                    echo '</div>';
                                
                                echo '</div>';
                                
                                echo '<div class="row marginTopBottom"><div class="col-md-12 text-right"><a href="#" id="addPlano">Adicionar plano</a></div></div>';
                                
                                echo '<div id="todasQuestoes" class="row marginTopBottom">';
                                    
                                    $cont = 1;
                                    $contQuestoes = count($dadosFrm);                                                                    
                                    foreach($dadosFrm as $perg){
                                        
                                        if($contQuestoes == 3){
                                            if($cont == 1){
                                                $funcao = 'maior()';
                                            }elseif($cont == 2){
                                                $funcao = 'soma()';
                                            }else{
                                                $funcao = 'menor()';
                                            }
                                        }
                                        
                                        $auxiliaCampo = ($perg->grupo == '')? '[1]': '['.$perg->grupo.']';
                                        $gru = ($perg->grupo == '')? 1: $perg->grupo;
                                        
                                        #Tipo resposta
                                        switch($perg->tipo_resp){
                                            case 'I': # Inteiro
                                                $classTipoResp = 'numero';
                                            break;
                                            case 'M': # Float
                                                $classTipoResp = 'moeda';
                                            break;
                                            case 'P': # Float
                                                $classTipoResp = 'porcentagem';
                                            break;
                                            default: # String
                                                $classTipoResp = 'texto';
                                        }
                                        
                                        # Obrigatório
                                        $obrigatorio = ($perg->obrigatorio == 'S')? 'required': '';
                                        
                                        if(($perg->grupo != $ultimoGrupo) or ($perg->grupo == '' and $cont == 1) ){
                                            $linkRemove = ($perg->grupo != '' and $perg->grupo != 1)? '<a style="float:right" href="#" gru="'.$gru.'" class="glyphicon glyphicon-minus grupo"></a>': '';
                                            echo '<div class="col-md-12 grupo'.$gru.' grupoResposta"><span class="float-left"><strong>'.$gru.' - Grupo de respostas</strong></span>'.$linkRemove.'</div>';
                                        }
                                        
                                        if($perg->questao == 'Status'){
                                            $valor = 1;
                                            $readonly = 'readonly';
                                        }else{
                                            $valor = $perg->resposta;
                                            $readonly = '';
                                        }
                                        
                                        echo '<div class="col-md-12 grupo'.$gru.'">
                                                <div class="input-group marginTopBottom">
                                                    <span class="input-group-addon" id="basic-addon1"><strong>ID '.$perg->cd_anatel_quest.' - '.utf8_decode($perg->questao).':</strong></span>
                                                    <input '.$readonly.' type="text" class="questoes form-control '.$classTipoResp.' '.$obrigatorio.'" title="Responda" id="resp['.$perg->cd_anatel_quest.']" name="resp'.$auxiliaCampo.'['.$perg->cd_anatel_quest.']" value="'.$valor.'" aria-describedby="basic-addon1">
                                                </div>
                                            </div>';
                                            
                                        $ultimoGrupo = $perg->grupo;
                                        $cont++;
                                    }
                                
                                echo '</div>';
                                
                                echo '<div id="todasRegras" class="row marginTopBottom">';
                                
                                $cont = 1;
                                foreach($regrasFrm as $rF){
                                
                                    echo '<div id="meta_'.$rF->cd_anatel_meta.'" class="col-md-4">';
                                        echo '<div><strong>Justificativa '.$cont++.'</strong></div>';
                                        #echo '<div id="RegraRes_'.$rF->cd_anatel_meta.'" class="vermelho"></div>';
                            			$data = array('name'=>'ilustracao['.$rF->cd_anatel_meta.']', 'id'=>'ilustracao['.$rF->cd_anatel_meta.']', 'class'=>'form-control vermelho', 'readonly' => 'readonly');
                            			echo form_input($data, $rF->ilustracao);
                                        $options = array(''=>'');
                                        foreach($motivosJust as $mJ){
                                            $options[$mJ->cd_anatel_motivo_just] = $mJ->nome;
                                        }		
                                		echo form_label('Motivo<span class="obrigatorio">*</span>', 'motivo['.$rF->cd_anatel_meta.']');
                                		echo form_dropdown('motivo['.$rF->cd_anatel_meta.']', $options, $rF->cd_anatel_motivo_just, 'id="motivo['.$rF->cd_anatel_meta.']" class="form-control required" title="Selecione o motivo"');
                                        
                                        echo form_label('Diagn&oacute;stico<span class="obrigatorio">*</span>', 'diagnostico['.$rF->cd_anatel_meta.']');
                            			$data = array('name'=>'diagnostico['.$rF->cd_anatel_meta.']', 'id'=>'diagnostico['.$rF->cd_anatel_meta.']', 'class'=>'form-control required', 'title' => 'Informe o diagn&oacute;stico');
                            			echo form_textarea($data, $rF->diagnostico);
                                        
                                        echo form_label('A&ccedil;&otilde;es corretivas<span class="obrigatorio">*</span>', 'acao_corretiva['.$rF->cd_anatel_meta.']');
                            			$data = array('name'=>'acao_corretiva['.$rF->cd_anatel_meta.']', 'id'=>'acao_corretiva['.$rF->cd_anatel_meta.']', 'class'=>'form-control required', 'title' => 'Informe a a&ccedil;&otilde;es corretivas');
                            			echo form_textarea($data, $rF->acao_corretiva);
                                        
                                    echo '</div>';
                                    
                               }
                               
                                echo '</div>';
                      
                                echo '<div class="actions">';
                                echo form_hidden('cd_anatel_frm', $dadosFrm[0]->cd_anatel_frm);
                                if($unidadeUsuario){
                                echo form_hidden('cd_unidade', $unidadeUsuario);
                                echo form_submit("btn_responder","Responder", 'id="responder" class="btn btn-primary pull-right"');
                                }
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

$("#addPlano").click(function(event){
    event.preventDefault();

    var cont = $(".grupoResposta").length+ 1;
    if($(".grupo"+cont).length){
        cont = $(".grupoResposta").length + 2;
    }
    
    
    var conteudo = '<div class="col-md-12 grupo'+cont+' grupoResposta"><span class="text-left"><strong>'+cont+' - Grupo de respostas</strong></span><a style="float:right" href="#" gru="'+cont+'" class="glyphicon glyphicon-minus grupo"></a></div>';
        
        <?php 
        foreach($dadosFrm as $perg){
        
        #Tipo resposta
        switch($perg->tipo_resp){
            case 'I': # Inteiro
                $classTipoResp = 'numero';
            break;
            case 'M': # Float
                $classTipoResp = 'moeda';
            break;
            case 'P': # Float
                $classTipoResp = 'porcentagem';
            break;
            default: # String
                $classTipoResp = 'texto';
        }
        
        if($perg->questao == 'Status'){
            $valor = 1;
            $readonly = 'readonly';
        }else{
            $valor = $perg->resposta;
            $readonly = '';
        }
        
        # Obrigatório
        $obrigatorio = ($perg->obrigatorio == 'S')? 'required': ''; 
            if($perg->grupo == '' or $perg->grupo == 1){   
        ?>
        
        conteudo +='<div class="col-md-12 grupo'+cont+'">';
        conteudo +='<div class="input-group marginTopBottom">';
        conteudo +='<span class="input-group-addon" id="basic-addon1"><strong>ID <?php echo $perg->cd_anatel_quest; ?> - <?php echo utf8_decode($perg->questao); ?>:</strong></span>';
        conteudo +='<input type="text" <?php echo $readonly; ?> class="questoes form-control <?php echo $classTipoResp;?> <?php echo $obrigatorio;?>" title="Responda" id="resp[<?php echo $perg->cd_anatel_quest;?>]" name="resp['+cont+'][<?php echo $perg->cd_anatel_quest;?>]" value="<?php echo $valor;?>" aria-describedby="basic-addon1">';
        conteudo +='</div>';
        conteudo +='</div>';
        
        <?php 
            }
        } 
        ?>
    $("#todasQuestoes").append(conteudo);
    $(".numero").mask("000000");
    $(".moeda").mask("####0.0", {reverse: true});
    $(".porcentagem").mask("000.00", {reverse: true});
    
    $(".grupo").click(function(){
        $(".grupo"+$(this).attr('gru')).remove();
    })
    
})

$(".grupo").click(function(){
    $(".grupo"+$(this).attr('gru')).remove();
})

$(document).ready(function(){
    
    $("#responder").click(function () { if (!$("#salvar_frm").valid()) { $("#salvar_frm").validate(); } });

    $(".numero").mask("000000");
    $(".moeda").mask("####0.0", {reverse: true});
    $(".porcentagem").mask("000.00", {reverse: true});
    
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
	$("#salvar_frm").validate({
		debug: false,
		rules: {
			cd_tipo_frm_anatel: {
                required: true
            },
            cd_departamento: {
                required: true
            }
		},
		messages: {
			cd_tipo_frm_anatel: {
                required: "Selecione o tipo de formul&aacute;rio."
            },
            cd_departamento: {
                required: "Selecione o departamento."
            }
            
	   },
       submitHandler: function(form) {
            // do other things for a valid form
            form.submit();
       }       
   });   
   
});

</script>