<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
    <!-- INÍCIO Modal Apaga registro -->
    <div class="modal fade" id="apaga" tabindex="-1" role="dialog" aria-labelledby="apaga" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="myModalLabel">Deseja apagar a linha?</h4>
                </div>
                <div class="modal-body">
                    <?php              
                        $data = array('class'=>'pure-form','id'=>'apagaRegistro');
                        echo form_open('telefonia/apagaLinha',$data);
                        
                            echo form_label('N&uacute;mero', 'apg_nome');
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
            <div class="col-md-10 col-sm-9">
            <!--<div class="col-lg-12">-->
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a></li>
                    <li><a href="<?php echo base_url('telefonia'); ?>">Telefonia</a></li>
                    <li class="active">Linhas</li>
                </ol>
                <div id="divMain">
                    <?php
                        
                        $postDDD = (isset($postDDD))? $postDDD: false;
                        $postOperadora = (isset($postOperadora))? $postOperadora: false;
                        $postPlano = (isset($postPlano))? $postPlano: false;
                        $postStatus = (isset($postStatus))? $postStatus: false;
                        $pesquisa = (isset($pesquisa))? $pesquisa: false;
                        
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'pesquisar');
                    	echo form_open('telefonia/pesqLinha',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
                            
                            $botaoAlteraoMassa = (in_array(251, $this->session->userdata('permissoes')))? "<a href='".base_url('telefonia/servicoMassa')."' class='linkDireita'>Servi&ccedil;o em massa&nbsp<span class='glyphicon glyphicon-share-alt'></span></a>": '';
                            $botaoCadastrar = (in_array(221, $this->session->userdata('permissoes')))? "<a href='".base_url('telefonia/fichaLinha')."' class='linkDireita'>Cadastrar&nbsp<span class='glyphicon glyphicon-plus'></span></a>": '';
                            
                    		echo form_fieldset("Pesquisar linha".$botaoCadastrar.$botaoAlteraoMassa, $attributes);
                    		  
                                echo '<div class="row">';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array(''=>'');
                                    foreach($ddds as $ddd){
                                        $options[$ddd->cd_telefonia_ddd] = $ddd->descricao;
                                    }		
                            		echo form_label('DDD', 'ddd');
                            		echo form_dropdown('ddd', $options, $postDDD, 'id="ddd" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    #print_r(array_keys($listaBancos));
                                    echo form_label('Identifica&ccedil;&atilde;o', 'identificacao');
                        			$data = array('name'=>'identificacao', 'value'=>$this->input->post('identificacao'),'id'=>'identificacao', 'placeholder'=>'Digite o nome', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    #print_r(array_keys($listaBancos));
                                    echo form_label('N&uacute;mero', 'numero');
                        			$data = array('name'=>'numero', 'value'=>$this->input->post('numero'),'id'=>'numero', 'placeholder'=>'Digite o nome', 'class'=>'form-control celular');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    $options = array(''=>'');
                                    foreach($operadoras as $ope){
                                        $options[$ope->cd_telefonia_operadora] = $ope->nome;
                                    }		
                            		echo form_label('Operadora', 'operadora');
                            		echo form_dropdown('operadora', $options, $postOperadora, 'id="operadora" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array(''=>'');
                                    foreach($planos as $pla){
                                        $options[$pla->cd_telefonia_plano] = $pla->nome;
                                    }		
                            		echo form_label('Plano', 'plano');
                            		echo form_dropdown('plano', $options, $postPlano, 'id="plano" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    $options = array(''=>'', 'A' => 'Ativo', 'I' => 'Inativo', 'E' => 'Estoque');		
                            		echo form_label('Status', 'status');
                            		echo form_dropdown('status', $options, $postStatus, 'id="status" class="form-control"');
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
                    
                    if(in_array($display, array('Dados','TZ 41','SMS','Pct. Min.'))){
                        $colunas[] = $display;
                    }else{
                        $colunas[] = anchor("telefonia/pesqLinha/".(($postDDD == '')? '0': $postDDD)."/".(($postIdentificacao == '')? '0': $postIdentificacao)."/".(($postNumero == '')? '0': $postNumero)."/".(($postOperadora == '')? '0': $postOperadora)."/".(($postPlano == '')? '0': $postPlano)."/".(($postStatus == '')? '0': $postStatus)."/".$nome."/".(($sort_order == 'asc' && $sort_by == $nome) ? 'desc' : 'asc') ,$display.$icoAscDesc, array('class' => $class));
                    }
                }
                $colunas[] = 'A&ccedil;&atilde;o';

                #$this->table->set_heading('Login', 'Nome', /*'E-mail',*/ 'Cidade', 'Departamento', 'Perfil', 'A&ccedil;&atilde;o');
                $this->table->set_heading($colunas);
                
                $contCell = 0;            
                foreach($dados as $da){
                    
                    #$cell[$contCell++] = array('data' => $da->cd_telefonia_linha);
                    $cell[$contCell++] = array('data' => $da->ddd);
                    
                    if($da->usuario != ''){
                        $nLinha = '<a href="#" title="'.$da->usuario.'">'.$da->linha.'</a>';
                    }else{
                        $nLinha = $da->linha;
                    }
                    
                    $cell[$contCell++] = array('data' => $nLinha);
                    #$cell[$contCell++] = array('data' => html_entity_decode($da->operadora));
                    #$cell[$contCell++] = array('data' => ($da->plano)? $da->plano: ' ');
                    
                    $cell[$contCell++] = array('data' => $this->linha->verificaServico($da->cd_telefonia_linha, array(5,6), 'conteudo', 'GB')); # Dados
                    $cell[$contCell++] = array('data' => ($this->linha->verificaServico($da->cd_telefonia_linha, 3)>0)?'<span class="glyphicon glyphicon-ok"></span>':'-'); # Tarifa Zero 41
                    $cell[$contCell++] = array('data' => ($this->linha->verificaServico($da->cd_telefonia_linha, 16)>0)?'<span class="glyphicon glyphicon-ok"></span>':'-'); # Tim Torpedo Originado
                    $cell[$contCell++] = array('data' => $this->linha->verificaServico($da->cd_telefonia_linha, array(7,8,9,10,11,12,20,21,22,23,24,25,26,27), 'conteudo', '')); # Pacote Minutos
                    
                    $cell[$contCell++] = array('data' => $da->status);
                    
                    $botaoVisualizar = (in_array(238, $this->session->userdata('permissoes')))? '<a title="Visualizar" href="'.base_url('telefonia/visualizarLinha/'.$da->cd_telefonia_linha).'" class="glyphicon glyphicon-search"></a>': '';
                    $botaoEditar = (in_array(221, $this->session->userdata('permissoes')))? '<a title="Editar" href="'.base_url('telefonia/fichaLinha/'.$da->cd_telefonia_linha).'" class="glyphicon glyphicon-pencil"></a>': '';
                    $botaoExcluir = (in_array(222, $this->session->userdata('permissoes')))? '<a title="Apagar" href="#" onclick="apagarRegistro('.$da->cd_telefonia_linha.',\''.$da->ddd." - ".$da->linha.'\')" data-toggle="modal"  data-target="#apaga" class="glyphicon glyphicon glyphicon glyphicon-remove"></a>': '';
                    
                    $cell[$contCell++] = array('data' => $botaoVisualizar.$botaoEditar.$botaoExcluir);
                    
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

function apagarRegistro(cd, nome){
    $("#apg_cd").val(cd);
    $("#apg_nome").val(nome);
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