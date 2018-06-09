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
                                        
                                        $auxiliaCampo = ($perg->cd_anatel_xml == 1)? '['.$perg->grupo.']': '';
                                        
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
                                        
                                        if($perg->grupo != $ultimoGrupo and $cont > 1){
                                            echo '<div class="col-md-12 grupoResposta"><span class="float-left"><strong>'.$perg->grupo.' - Grupo de respostas</strong></span> <!--<span class="float-right" class="glyphicon glyphicon-remove" aria-hidden="true"></span>--><div style="clear:both"></div></div>';
                                        }
                                        
                                        echo '<div class="col-md-12">
                                                <div class="input-group marginTopBottom">
                                                    <span class="input-group-addon" id="basic-addon1"><strong>ID '.$perg->cd_anatel_quest.' - '.utf8_decode($perg->questao).':</strong></span>
                                                    <input type="text" class="questoes form-control '.$classTipoResp.' '.$obrigatorio.'" title="Responda" id="resp['.$perg->cd_anatel_quest.']" name="resp'.$auxiliaCampo.'['.$perg->cd_anatel_quest.']" value="'.$perg->resposta.'" aria-describedby="basic-addon1">
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

$(document).ready(function(){

<?php if($contQuestoes == 3){ ?>    
    var questao1 = '';
    var questao2 = '';
    var questao3 = '';
    /*$.each( $('[name="^resp"]'), function( key, value ) {
      alert( key + ": " + value );
    });*/
    var cont = 1;
    $('.questoes').each(function() { 
        
        if(cont == 1){ // Maior
            questao1 = $(this);
            $(this).keyup(function(){
                /*if($(this).val() <=  parseInt(questao3.val())){
                    alert('<?php echo utf8_encode('Atenção!\nEssa resposta tem que ser maior que a 3ª questão.'); ?>');
                }*/
                
                if($(this).val() != '' && questao3.val() != ''){
                    var soma = parseInt($(this).val())+parseInt(questao3.val());
                    questao2.val(soma);
                }
            });
        }
        
        if(cont == 2){ // Totalizador
            questao2 = $(this);
            <?php if(!in_array($dadosFrm[0]->cd_anatel_frm, array(6,10,11))){ ?>
            questao2.attr('readonly', true);  
            <?php } ?>
            questao2.prop('title', '<?php echo utf8_encode('Campo preenchido automaticamente com a resposta da 1ª e 3ª questão.'); ?>');          
            $(this).keyup(function(){
                if($(this).val() != (parseInt(questao1.val()+parseInt(questao3.val())))){
                    alert('<?php echo utf8_encode('Esse valor tem que ser a soma da 1ª e 2ª questão.'); ?>');
                }
            });
        }
        
        if(cont == 3){ // Menor
            questao3 = $(this);
            $(this).keyup(function(){
                /*if($(this).val() >=  parseInt(questao1.val())){
                    alert('<?php echo utf8_encode('Atenção!\nEssa resposta tem que ser menor que a 1ª questão.'); ?>');
                }*/
                
                if($(this).val() != '' && questao1.val() != ''){
                    var soma = parseInt($(this).val())+parseInt(questao1.val());
                    questao2.val(soma);
                }
            });
        }
        cont++;
    });
    
    $("#responder").mousemove(function(){
        /*if(parseInt(questao1.val()) < parseInt(questao3.val())){
            alert('<?php echo utf8_encode('A resposta da 1ª questão tem que ser maior que a 3ª questão.'); ?>');
            questao1.val('');
            questao2.val('');
        }*/
    });
    
    $("#responder").click(function () { if (!$("#salvar_frm").valid()) { $("#salvar_frm").validate();; } });
    
<?php } ?>
        
    $(".numero").mask("000000");
    $(".moeda").mask("####0.0", {reverse: true});
    $(".porcentagem").mask("000.00", {reverse: true});
    
    <?php 
    foreach($regrasFrm as $rF){ 
        $display = ($rF->ilustracao != '')? 'block': 'none';
    ?>
    $('#meta_<?php echo $rF->cd_anatel_meta; ?>').css('display', '<?php echo $display;?>');
    
        var calculo = 0;
        var numero = 0;
        var RegraDesc = '';
        var RegraRes = '';
    
        <?php if($rF->regra == 'P'){ ?>
        
        $.fn.funcaoRegraP = function() { 
            
            if($('[name="resp[<?php echo $rF->pergunta1; ?>]"]').val() != '' && $('[name="resp[<?php echo $rF->pergunta1; ?>]"]').val() != '0' && $('[name="resp[<?php echo $rF->pergunta2; ?>]"]').val() != '' && $('[name="resp[<?php echo $rF->pergunta2; ?>]"]').val() != '0'){
                
                calculo = ($('[name="resp[<?php echo $rF->pergunta1; ?>]"]').val() <?php echo $rF->operador; ?> $('[name="resp[<?php echo $rF->pergunta2; ?>]"]').val())*100;
                //alert(parseInt(calculo.toFixed(2)));
                //alert(isNaN(calculo.toFixed(2)));
                numero = <?php echo $rF->numero; ?>;
                RegraDesc = '';
                RegraRes = '';
                
                if(calculo.toFixed(2) <?php echo $rF->comparador; ?> numero){
                    RegraDesc = '';
                    RegraRes = '';
                    $('#meta_<?php echo $rF->cd_anatel_meta; ?>').css('display', 'none');
                    $('[name="motivo[<?php echo $rF->cd_anatel_meta; ?>]"]').val('');
                    $('[name="diagnostico[<?php echo $rF->cd_anatel_meta; ?>]"]').val('');
                    $('[name="acao_corretiva[<?php echo $rF->cd_anatel_meta; ?>]"]').val('');
                }else{
                    if(isNaN(calculo.toFixed(2)) == false){
                        RegraDesc = '((ID <?php echo $rF->pergunta1; ?> - '+$('[name="resp[<?php echo $rF->pergunta1; ?>]"]').val()+') / (ID <?php echo $rF->pergunta2; ?> - '+$('[name="resp[<?php echo $rF->pergunta2; ?>]"]').val()+') * 100)';
                        RegraRes = calculo.toFixed(2)+'% <?php echo $rF->comparador; ?> Meta '+numero+'%';
                        $('#meta_<?php echo $rF->cd_anatel_meta; ?>').css('display', 'block');  
                    }
                }
                $('[name="ilustracao[<?php echo $rF->cd_anatel_meta; ?>]"]').attr( "title", RegraDesc );
                $('[name="ilustracao[<?php echo $rF->cd_anatel_meta; ?>]"]').val(RegraRes);
                
            }else{
                
                $('#meta_<?php echo $rF->cd_anatel_meta; ?>').css('display', 'none');
                $('[name="motivo[<?php echo $rF->cd_anatel_meta; ?>]"]').val('');
                $('[name="diagnostico[<?php echo $rF->cd_anatel_meta; ?>]"]').val('');
                $('[name="acao_corretiva[<?php echo $rF->cd_anatel_meta; ?>]"]').val('');
                $('[name="ilustracao[<?php echo $rF->cd_anatel_meta; ?>]"]').removeAttr("title");
                $('[name="ilustracao[<?php echo $rF->cd_anatel_meta; ?>]"]').val('');
                
            }
            
        };
        
        $('[name="resp[<?php echo $rF->pergunta1; ?>]"]').keyup(function() {
            
            $(this).funcaoRegraP();
            
        });
        
        $('[name="resp[<?php echo $rF->pergunta2; ?>]"]').keyup(function() {
            
            $(this).funcaoRegraP();
            
        });
        <?php } // if($rF->regra == 'P'){ ?>
    
        <?php if($rF->regra == 'N'){ ?>
            $('[name="resp[<?php echo $rF->pergunta1; ?>]"]').keyup(function() {
                
                if($(this).val() != ''){
                    
                    $(this).funcaoRegraP();
                    
                    numero = <?php echo $rF->numero; ?>;
                    
                    if($(this).val() <?php echo $rF->operador; ?> <?php echo $rF->numero; ?>){
                        
                        RegraDesc = '';
                        RegraRes = '';
                        $('#meta_<?php echo $rF->cd_anatel_meta; ?>').css('display', 'none');
                        $('[name="motivo[<?php echo $rF->cd_anatel_meta; ?>]"]').val('');
                        $('[name="diagnostico[<?php echo $rF->cd_anatel_meta; ?>]"]').val('');
                        $('[name="acao_corretiva[<?php echo $rF->cd_anatel_meta; ?>]"]').val('');
                        $('[name="ilustracao[<?php echo $rF->cd_anatel_meta; ?>]"]').removeAttr("title");
                        $('[name="ilustracao[<?php echo $rF->cd_anatel_meta; ?>]"]').val('');
                        
                    }else{
                        
                        RegraDesc = '(ID <?php echo $rF->pergunta1; ?> - '+$(this).val()+')';
                        RegraRes = $(this).val()+' <?php echo $rF->comparador; ?> Meta '+numero;
                        $('#meta_<?php echo $rF->cd_anatel_meta; ?>').css('display', 'block'); 
                        
                    }
                    
                    $('[name="ilustracao[<?php echo $rF->cd_anatel_meta; ?>]"]').attr( "title", RegraDesc );
                    $('[name="ilustracao[<?php echo $rF->cd_anatel_meta; ?>]"]').val(RegraRes);
                    
                }else{
                    
                    $('#meta_<?php echo $rF->cd_anatel_meta; ?>').css('display', 'none');
                    $('[name="motivo[<?php echo $rF->cd_anatel_meta; ?>]"]').val('');
                    $('[name="diagnostico[<?php echo $rF->cd_anatel_meta; ?>]"]').val('');
                    $('[name="acao_corretiva[<?php echo $rF->cd_anatel_meta; ?>]"]').val('');
                    $('[name="ilustracao[<?php echo $rF->cd_anatel_meta; ?>]"]').removeAttr("title");
                    $('[name="ilustracao[<?php echo $rF->cd_anatel_meta; ?>]"]').val('');
                    
                }
                
            });
        <?php } ?>
        
        <?php if($rF->regra == 'F'){ ?>
        
        $.fn.funcaoRegraF = function() {
            
            if($('[name="resp[<?php echo $rF->pergunta1; ?>]"]').val() != '' && $('[name="resp[<?php echo $rF->pergunta1; ?>]"]').val() != '0' && $('[name="resp[<?php echo $rF->pergunta2; ?>]"]').val() != '' && $('[name="resp[<?php echo $rF->pergunta2; ?>]"]').val() != '0'){
                
                //calculo = ($(this).val() <?php echo $rF->operador; ?> $('[name="resp[<?php echo $rF->pergunta2; ?>]"]').val())*100;
                calculo = (($('[name="resp[<?php echo $rF->pergunta1; ?>]"]').val() <?php echo $rF->operador; ?> $('[name="resp[<?php echo $rF->pergunta2; ?>]"]').val())*100)/$('[name="resp[<?php echo $rF->pergunta1; ?>]"]').val();
                numero = <?php echo $rF->numero; ?>;
                RegraDesc = '';
                RegraRes = '';
                
                if(calculo.toFixed(2) <?php echo $rF->comparador; ?> numero){
                    RegraRes = '';
                    RegraDesc = '';
                    $('#meta_<?php echo $rF->cd_anatel_meta; ?>').css('display', 'none');
                    $('[name="motivo[<?php echo $rF->cd_anatel_meta; ?>]"]').val('');
                    $('[name="diagnostico[<?php echo $rF->cd_anatel_meta; ?>]"]').val('');
                    $('[name="acao_corretiva[<?php echo $rF->cd_anatel_meta; ?>]"]').val('');
                }else{
                    RegraDesc = '((  (ID <?php echo $rF->pergunta1; ?> - '+$('[name="resp[<?php echo $rF->pergunta1; ?>]"]').val()+') - (ID <?php echo $rF->pergunta2; ?> - '+$('[name="resp[<?php echo $rF->pergunta2; ?>]"]').val()+')  ) * 100) /  (ID <?php echo $rF->pergunta1; ?> - '+$('[name="resp[<?php echo $rF->pergunta1; ?>]"]').val()+')';
                    RegraRes = calculo.toFixed(2)+' <?php echo $rF->comparador; ?> Meta '+numero+'%';
                    
                    $('#meta_<?php echo $rF->cd_anatel_meta; ?>').css('display', 'block');  
                }
                $('[name="ilustracao[<?php echo $rF->cd_anatel_meta; ?>]"]').attr( "title", RegraDesc );
                $('[name="ilustracao[<?php echo $rF->cd_anatel_meta; ?>]"]').val(RegraRes);
                
            }else{
                
                $('#meta_<?php echo $rF->cd_anatel_meta; ?>').css('display', 'none');
                $('[name="motivo[<?php echo $rF->cd_anatel_meta; ?>]"]').val('');
                $('[name="diagnostico[<?php echo $rF->cd_anatel_meta; ?>]"]').val('');
                $('[name="acao_corretiva[<?php echo $rF->cd_anatel_meta; ?>]"]').val('');
                $('[name="ilustracao[<?php echo $rF->cd_anatel_meta; ?>]"]').removeAttr("title");
                $('[name="ilustracao[<?php echo $rF->cd_anatel_meta; ?>]"]').val('');
                
            }            
            
        };
        
        $('[name="resp[<?php echo $rF->pergunta1; ?>]"]').keyup(function() {
            
            $(this).funcaoRegraF();
            
        });
        
        $('[name="resp[<?php echo $rF->pergunta2; ?>]"]').keyup(function() {
            
            $(this).funcaoRegraF();
            
        });
        <?php } // if($rF->regra == 'F'){ ?>
        
    <?php 
    } // foreach($regrasFrm as $rF){ 
    ?>
    
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