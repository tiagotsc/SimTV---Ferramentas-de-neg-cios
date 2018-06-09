<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
    <!-- INÍCIO Modal Apaga registro -->
    <div class="modal fade" id="apaga" tabindex="-1" role="dialog" aria-labelledby="apaga" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="myModalLabel">Deseja apagar a emprestimo?</h4>
                </div>
                <div class="modal-body">
                    <?php              
                        $data = array('class'=>'pure-form','id'=>'apagaRegistro');
                        echo form_open('telefonia/apagaEmprestimo',$data);
                        
                            echo form_label('Empr&eacute;stimo', 'apg_nome');
                    		$data = array('id'=>'apg_nome', 'name'=>'apg_nome', 'class'=>'form-control data');
                    		echo form_input($data,'');
                        
                    ?>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="apg_cd" name="apg_cd" />
                    <button type="button" class="btn btn-default" data-dismiss="modal">N&atilde;o</button>
                    <button type="submit" class="btn btn-primary">Sim</button>
            </div>
                    <?php
                    echo form_close();
                    ?>
        </div>
      </div>
    </div>
    <!-- FIM Modal Apaga registro -->
    
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
                        <input type="checkbox" checked="true" name="email" value="true" />
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
                    <li class="active">Empr&eacute;stimos</li>
                </ol>
                <div id="divMain">
                    <?php
                        
                        $postLinha = (isset($postLinha))? $postLinha: false;
                        $postImei = (isset($postImei))? $postImei: false;
                        $postUser = (isset($postUser))? $postUser: false;
                        $pesquisa = (isset($pesquisa))? $pesquisa: false;
                        
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'pesquisar');
                    	echo form_open('telefonia/pesqEmprestimo',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
                            
                            $botaoCadastrar = (in_array(221, $this->session->userdata('permissoes')))? "<a href='".base_url('telefonia/fichaEmprestimo')."' class='linkDireita'>Cadastrar&nbsp<span class='glyphicon glyphicon-plus'></span></a>": '';
                            
                    		echo form_fieldset("Pesquisar empr&eacute;stimo".$botaoCadastrar, $attributes);
                    		  
                                echo '<div class="row">';
                                    
                                    echo '<div class="col-md-3">';
                                    #print_r(array_keys($listaBancos));
                                    echo form_label('Linha', 'linha');
                        			$data = array('name'=>'linha', 'value'=>$postLinha,'id'=>'linha', 'placeholder'=>'Digite o n&uacute;mero', 'class'=>'form-control celular');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    #print_r(array_keys($listaBancos));
                                    echo form_label('Imei', 'imei');
                        			$data = array('name'=>'imei', 'value'=>$postImei,'id'=>'imei', 'placeholder'=>'Digite o IMEI', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-6">';
                                    #print_r(array_keys($listaBancos));
                                    echo form_label('Usu&aacute;rio ou matr&iacute;cula', 'user');
                        			$data = array('name'=>'user', 'value'=>$postUser,'id'=>'user', 'placeholder'=>'Digite o nome ou matr&iacute;cula', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
 
                                echo '</div>';                      
                                                                
                                echo '<div class="actions">';
                                echo form_submit("btn_cadastro","Pesquisar", 'class="btn btn-primary pull-right"');
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
                    <strong>Mostrando <?php echo ($qtdDadosCorrente)? $qtdDadosCorrente: 0; ?> de <?php echo ($qtdDados[0]->total)? $qtdDados[0]->total: 0; ?> registros localizados.</strong>
                </p>
                <?php 
                $colunas = array();
                
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
                    
                    $colunas[] = anchor("telefonia/pesqEmprestimo/".(($postLinha == '')? '0': $postLinha)."/".(($postUser == '')? '0': $postUser)."/".$nome."/".(($sort_order == 'asc' && $sort_by == $nome) ? 'desc' : 'asc') ,$display.$icoAscDesc, array('class' => $class));
                    
                }
                $colunas[] = 'A&ccedil;&atilde;o';

                #$this->table->set_heading('Login', 'Nome', /*'E-mail',*/ 'Cidade', 'Departamento', 'Perfil', 'A&ccedil;&atilde;o');
                $this->table->set_heading($colunas);
                
                $contCell = 0;            
                foreach($dados as $da){
                    
                    $corTermo = ($da->data_criacao_termo)? 'verde': '';
                    
                    if($da->aceite_termo == 'S'){
                        $iconeViewTermo = 'glyphicon glyphicon-thumbs-up';
                    }elseif($da->aceite_termo == 'N'){
                        $iconeViewTermo = 'glyphicon glyphicon-thumbs-down';
                    }else{
                        $iconeViewTermo = 'glyphicon glyphicon-list-alt';
                    }
            
                    #$cell[$contCell++] = array('data' => $da->cd_telefonia_emprestimo);
                    
                    $cell[$contCell++] = array('data' => ($da->ddd != '')? $da->ddd: '');
                    $cell[$contCell++] = array('data' => ($da->linha != '')? $da->linha: '');
                    $cell[$contCell++] = array('data' => ($da->imei != '')? $da->imei: '');
                    $cell[$contCell++] = array('data' => htmlentities($da->nome_usuario));
                    
                    $dddLinha = $da->ddd.' - '.$da->linha;
                    
                    if($da->aceite_termo != ''){
                        $botaoViewTermo = '<a title="Visualizar termo" href="#" data-toggle="modal" onclick="viewTermo('.$da->cd_telefonia_emprestimo.', '.$da->cd_telefonia_operadora.', '.$da->cd_usuario.')" data-target="#view_termo" class="glyphicon '.$iconeViewTermo.'"></a>';
                    }else{
                        $botaoViewTermo = '';
                    }

                    $botaoTermo = (in_array(233, $this->session->userdata('permissoes')))? '<a title="Gerar e enviar termo" href="#" onclick="termo('.$da->cd_telefonia_emprestimo.', \''.$da->email_usuario.'\')" data-toggle="modal"  data-target="#termo" class="'.$corTermo.' glyphicon glyphicon glyphicon-list-alt"></a>': '';
                    $botaoEditar = (in_array(227, $this->session->userdata('permissoes')))? '<a title="Editar" href="'.base_url('telefonia/fichaEmprestimo/'.$da->cd_telefonia_emprestimo).'" class="glyphicon glyphicon glyphicon-pencil"></a>': '';
                    $botaoExcluir = (in_array(228, $this->session->userdata('permissoes')))? '<a title="Apagar" href="#" onclick="apagarRegistro('.$da->cd_telefonia_emprestimo.',\''.'('.$da->ddd.') '.$da->linha." - ".$da->nome_usuario.'\')" data-toggle="modal"  data-target="#apaga" class="glyphicon glyphicon glyphicon glyphicon-remove"></a>': '';
                    
                    $cell[$contCell++] = array('data' => $botaoViewTermo.$botaoTermo.$botaoEditar.$botaoExcluir);
                        
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