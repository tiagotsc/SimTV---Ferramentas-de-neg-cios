<?php #echo '<pre>'; print_r($dados); exit(); ?>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
    <!-- FIM Modal Apaga registro -->
    
    <!-- INÍCIO Modal Visualizar -->
    <div class="modal fade" id="visualizar" tabindex="-1" role="dialog" aria-labelledby="visualizar" aria-hidden="true">
        <div style="width: 1000px;" class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
                <h4 class="modal-title" id="myModalLabel">Dados empr&eacute;stimo</h4>
                </div>
                <div class="modal-body row">
                    <div class="col-md-12">
                        <div id="divLinha" class="col-md-12">
                            <strong>Linha: </strong>84894954645
                        </div>
                    </div>
                    <div class="row">
                        <div id="divVis1" class="col-md-5">
                        </div>
                        <div id="divVis2" class="col-md-7">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="apg_cd" name="apg_cd" />
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
        </div>
      </div>
    </div>
    <!-- FIM Modal Visualizar -->
    
    <!-- INÍCIO Modal Termo -->
    <div class="modal fade" id="termo" tabindex="-1" role="dialog" aria-labelledby="termo" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
                <h4 class="modal-title" id="myModalLabel">Dados termo</h4>
                </div>
                <div class="modal-body row">
                <?php              
                $data = array('class'=>'pure-form','id'=>'salvaTermo');
                echo form_open('telefonia/salvaTermo',$data);
                    #echo '<div class="row">';
                    echo '<div class="col-md-3">';
                    #print_r(array_keys($listaBancos));
                    echo form_label('Data do termo', 'data_termo');
        			$data = array('name'=>'data_termo', 'value'=>date('d/m/Y'),'id'=>'data_termo', 'placeholder'=>'Digite a data', 'class'=>'form-control data');
        			echo form_input($data);
                    echo '</div>';
                    #echo '</div>';
                    ?>
                    <?php
                    #echo '<div class="row">';
                    echo '<div class="col-md-9">';
                        echo '<label>Acess&oacute;rios</label>';
                        echo '<label><input type="checkbox" id="todos" onclick="marcaGrupo(\'.grupo\', this)" />Marcar / Desmarcar todos</label>';
                        echo '<div id="acessorios" class="overflow">';
                        echo '</div>';
                    echo '</div>';                                   
                    ?>
                </div>
                <div class="modal-footer">
                    <label class="text-left">
                        <input type="checkbox" name="email" value="true" />
                        &nbsp;Comunicar gera&ccedil;&atilde;o do termo por e-mail
                    </label>
                    <label class="text-left">
                        <input type="checkbox" name="reset_termo" value="S" />
                        &nbsp;Resetar resposta do termo
                    </label>
                    <input type="hidden" id="data_criacao_termo" name="data_criacao_termo" />
                    <input type="hidden" id="email_usuario" name="email_usuario" />
                    <input type="hidden" id="cd_emprestimo_termo" name="cd_emprestimo_termo" />
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                <?php
                echo form_close();
                ?>
            </div>
        </div>
      </div>
    </div>
    <!-- FIM Modal Termo -->
            <div class="col-md-10 col-sm-9">
            <!--<div class="col-lg-12">-->
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a></li>
                    <li><a href="<?php echo base_url('telefonia'); ?>">Telefonia</a></li>
                    <li class="active">Pesquisar</li>
                </ol>
                <div id="divMain">
                    <?php
                        
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'pesquisar');
                    	echo form_open('telefonia/pesquisar',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
                            
                    		echo form_fieldset("Pesquisar", $attributes);
                    		  
                                echo '<div class="row">';
                                    
                                    echo '<div class="col-md-4">';
                                    #print_r(array_keys($listaBancos));
                                    echo form_label('Linha', 'linha');
                        			$data = array('name'=>'linha', 'value'=>$postLinha,'id'=>'linha', 'placeholder'=>'Digite o n&uacute;mero', 'class'=>'form-control celular');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    #print_r(array_keys($listaBancos));
                                    echo form_label('IMEI', 'imei');
                        			$data = array('name'=>'imei', 'value'=>$postImei,'id'=>'imei', 'placeholder'=>'Digite o IMEI', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    #print_r(array_keys($listaBancos));
                                    echo form_label('Usu&aacute;rio ou Matr&iacute;cula', 'user');
                        			$data = array('name'=>'user', 'value'=>$postUser,'id'=>'user', 'placeholder'=>'Digite o nome ou matr&iacute;cula', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
 
                                echo '</div>';                      
                                                                
                                echo '<div class="actions">';
                                echo form_hidden('pesquisar', 'sim');
                                echo form_submit("btn_cadastro",utf8_encode("Pesquisar"), 'class="btn btn-primary pull-right"');
                                echo '</div>';
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
                    
                    ?>        
                </div>
                
                <div class="row">&nbsp</div>
                <?php 
                if($pesquisar == 'sim'){
                ?>
                <div class="well">
                <?php 
                $this->table->set_heading('DDD', 'Linha', 'Imei', 'Usu&aacute;rio', 'A&ccedil;&atilde;o');
                
                $contCell = 0;            
                foreach($dados as $da){
                    
                    $corTermo = ($da->data_criacao_termo)? 'verde': '';
                    
                    if($da->aceite_termo == 'S'){
                        $iconeViewTermo = 'glyphicon glyphicon-hand-right';
                    }elseif($da->aceite_termo == 'N'){
                        $iconeViewTermo = 'glyphicon glyphicon-thumbs-down';
                    }else{
                        $iconeViewTermo = 'glyphicon glyphicon-list-alt';
                    }
            
                    #$cell[$contCell++] = array('data' => $da->cd_telefonia_emprestimo);
                    $cell[$contCell++] = array('data' => $da->ddd);
                    $cell[$contCell++] = array('data' => $da->linha);
                    $cell[$contCell++] = array('data' => $da->imei);
                    $cell[$contCell++] = array('data' => $da->matricula_usuario.' - '.html_entity_decode($da->nome_usuario));
                    
                    $dddLinha = $da->ddd.' - '.$da->linha;
                    
                    if($da->aceite_termo != ''){
                        $botaoViewTermo = '<a title="Visualizar termo" href="#" data-toggle="modal" onclick="viewTermo('.$da->cd_telefonia_emprestimo.', '.$da->cd_telefonia_operadora.', '.$da->cd_usuario.')" data-target="#view_termo" class="glyphicon '.$iconeViewTermo.'"></a>';
                    }else{
                        $botaoViewTermo = '';
                    }
                    
                    $botaoVisualizar = (in_array(229, $this->session->userdata('permissoes')))? '<a title="Visualzar" href="#" onclick="visualizar('.$da->cd_telefonia_emprestimo.', \''.$dddLinha.'\')" data-toggle="modal"  data-target="#visualizar" class="glyphicon glyphicon glyphicon-search"></a>': '';
                    
                    $cell[$contCell++] = array('data' => $botaoViewTermo.$botaoVisualizar);
                        
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

function apagarRegistro(cd, nome){
    $("#apg_cd").val(cd);
    $("#apg_nome").val(nome);
}

function visualizar(cd, ddLinha){
        //Verificar se o campo o nome nao e vazio
		//if($(this).val() != ""){
            $("#divLinha").html('<strong>Linha: </strong>'+ddLinha);
            
            $.ajax({
              type: "POST",
              url: '<?php echo base_url(); ?>ajax/dadosEmprestimos',
              data: {
                cd_emprestimo: cd,
                linha: ddLinha.split(' - ')[1]
              },
              dataType: "json",
              error: function(res) {
 	            dump(res);
              },
              success: function(res) {
                
                var html1 = '';
                var html2 = '';
                if(res.length > 0){
                    
                    html1 += '<div class="col-md-12">';
                    html1 += '<table class="table table-bordered">';
                    html1 += '<thead>';
                    html1 += '<tr>';
                    html1 += '<th colspan="2">Informa&ccedil;&otilde;es do empr&eacute;stimo</th>';
                    html1 += '</tr>';
                    html1 += '</thead>';
                    html1 += '<tbody>';
                    html1 += '<tr>';
                    html1 += '<td><strong>Data in&iacute;cio</strong></td>';
                    html1 += '<td>'+res[0]['data_inicio']+'</td>';
                    html1 += '</tr>';
                    html1 += '<tr>';
                    html1 += '<td><strong>Data fim</strong></td>';
                    html1 += '<td>'+res[0]['data_fim']+'</td>';
                    html1 += '</tr>';
                    html1 += '<tr>';
                    html1 += '<td><strong>Parcelas restantes</strong></td>';
                    html1 += '<td>'+res[0]['parcelas_restantes']+'</td>';
                    html1 += '</tr>';
                    html1 += '</tbody>';
                    html1 += '</table>';
                    html1 += '</div>';
                    
                    html1 += '<div class="col-md-12">';
                    html1 += '<table class="table table-bordered">';
                    html1 += '<thead>';
                    html1 += '<tr>';
                    html1 += '<th colspan="2">Informa&ccedil;&otilde;es do usu&aacute;rio</th>';
                    html1 += '</tr>';
                    html1 += '</thead>';
                    html1 += '<tbody>';
                    html1 += '<tr>';
                    html1 += '<td><strong>Matrícula</strong></td>';
                    html1 += '<td>'+res[0]['matricula_usuario']+'</td>';
                    html1 += '</tr>';
                    html1 += '<tr>';
                    html1 += '<td><strong>Nome</strong></td>';
                    html1 += '<td>'+res[0]['nome_usuario']+'</td>';
                    html1 += '</tr>';
                    html1 += '<tr>';
                    html1 += '<td><strong>Cargo</strong></td>';
                    html1 += '<td>'+res[0]['cargo']+'</td>';
                    html1 += '</tr>';
                    html1 += '<tr>';
                    html1 += '<td><strong>Departamento</strong></td>';
                    html1 += '<td>'+res[0]['nome_departamento']+'</td>';
                    html1 += '</tr>';
                    html1 += '<tr>';
                    html1 += '<td><strong>Status</strong></td>';
                    html1 += '<td>'+res[0]['status_usuario']+'</td>';
                    html1 += '</tr>';
                    html1 += '</tbody>';
                    html1 += '</table>';
                    html1 += '</div>';
                    $("#divVis1").html(html1);
                    
                    html2 += '<div class="col-md-12">';
                    html2 += '<table class="table table-bordered">';
                    html2 += '<thead>';
                    html2 += '<tr>';
                    html2 += '<th colspan="2">Informa&ccedil;&otilde;es do aparelho</th>';
                    html2 += '</tr>';
                    html2 += '</thead>';
                    html2 += '<tbody>';
                    html2 += '<tr>';
                    html2 += '<td><strong>Marca</strong></td>';
                    html2 += '<td>'+res[0]['marca']+'</td>';
                    html2 += '</tr>';
                    html2 += '<tr>';
                    html2 += '<td><strong>Modelo</strong></td>';
                    html2 += '<td>'+res[0]['modelo']+'</td>';
                    html2 += '</tr>';
                    html2 += '<tr>';
                    html2 += '<td><strong>IMEI</strong></td>';
                    html2 += '<td>'+res[0]['imei']+'</td>';
                    html2 += '</tr>';
                    html2 += '</tbody>';
                    html2 += '</table>';
                    html2 += '</div>';
                    $("#divVis2").html(html2);

                    $.post("<?php echo base_url(); ?>ajax/servicosLinha", { cd_linha: res[0]['cd_telefonia_linha'] }, function( data ) {
                    
                        if(data.length > 0){
                    
                            servico = '<div class="col-md-12">';
                            servico += '<table class="table table-bordered">';
                            servico += '<thead>';
                            servico += '<tr>';
                            servico += '<th colspan="5">Servi&ccedil;os</th>';
                            servico += '</tr>';
                            servico += '<tr>';
                            servico += '<th>Nome</th>';
                            servico += '<th>Quantidade</th>';
                            servico += '<th>Valor (R$)</th>';
                            servico += '<th>In&iacute;cio</th>';
                            servico += '<th>Fim</th>';
                            servico += '</tr>';
                            servico += '</thead>';
                            servico += '<tbody>';
                            
                            $.each(data, function() {
                            
                                servico += '<tr>';
                                servico += '<td>'+this.nome+'</td>';
                                servico += '<td>'+this.qtd+'</td>';
                                servico += '<td>'+this.valor+'</td>';
                                servico += '<td>12/10/2015</td>';
                                servico += '<td>20/10/2015</td>';
                                servico += '</tr>';
                            
                            });
                            
                            servico += '</tbody>';
                            servico += '</table>';
                            servico += '</div>';
                            
                            servico += '</div>';
                            
                            $("#divVis2").append(servico);
                        
                        }
                        
                    }, "json");
                }else{
                    conteudoHtml += '<div class="col-md-12"><div class="alert alert-warning" role="alert"><strong>Nenhum arquivo foi validado nesse dia.</strong></div></div>';
                }
    
                
              }
            });  
    
}

function termo(cd, email){
    
    $("#cd_emprestimo_termo").val(cd);
    $("#email_usuario").val(email);
    
    $.ajax({
      type: "POST",
      url: '<?php echo base_url(); ?>ajax/dadosTermo',
      data: {
        cd_emprestimo: cd
      },
      dataType: "json",
      error: function(res) {
        dump(res);
      },
      success: function(res) {
        
        if(res.length > 0){
            if(res[0]['data_termo']){
                $("#data_termo").val(res[0]['data_termo']);
                $("#data_criacao_termo").val(res[0]['data_criacao_termo']);
            }else{
                $("#data_termo").val('<?php echo date('d/m/Y'); ?>');
                $("#data_criacao_termo").val('');
            } 
        }else{
            $("#data_termo").val('<?php echo date('d/m/Y'); ?>');
            $("#data_criacao_termo").val('');
        }
        
      }
    });
    
    $.ajax({
      type: "POST",
      url: '<?php echo base_url(); ?>ajax/acessorios',
      data: {
        cd_emprestimo: cd
      },
      dataType: "json",
      error: function(res) {
        dump(res);
      },
      success: function(res) {
        
        if(res.length > 0){
            
            var checkbox = '';
            var checked = '';
            $.each(res, function() {
            
                if(this.emprestimo > 0){
                    checked = 'checked';
                }else{
                    checked = '';
                }
                
                checkbox += '<label class="marginLeft">';
                checkbox += '<input type="checkbox" class="grupo" '+checked+' name="cd_acessorio[]" value="'+this.cd_telefonia_acessorio+'" />&nbsp';
                checkbox += this.nome;
                checkbox += '</label>';
            
            });
            $("#acessorios").html(checkbox);
        }else{
            $("#acessorios").html('');
        }
        
      }
    });
    
}

$(document).ready(function(){
    
    $(".data").mask("00/00/0000");
    $(".celular").mask("000000000");
    
    $(".actions").click(function() {
        $('#aguarde').css({display:"block"});
    });
});


/*
CONFIGURA O CALENDÁRIO DATEPICKER NO INPUT INFORMADO
*/
$("#data,#data2, .data").datepicker({
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
   
   $("#salvaTermo").validate({
		debug: false,
		rules: {
			data_termo: {
                required: true
            },
            'cd_acessorio[]': {
                required: true
            }
		},
		messages: {
			data_termo: {
                required: "Informe uma data."
            },
            'cd_acessorio[]': {
                required: "<?php echo utf8_encode('Marque pelo menos 1 acessório.'); ?>"
            }
	   }
   });    
   
});

</script>