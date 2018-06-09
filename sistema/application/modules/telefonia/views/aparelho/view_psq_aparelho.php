<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
    <!-- INÍCIO Modal Apaga registro -->
    <div class="modal fade" id="apaga" tabindex="-1" role="dialog" aria-labelledby="apaga" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="myModalLabel">Deseja apagar a aparelho?</h4>
                </div>
                <div class="modal-body">
                    <?php              
                        $data = array('class'=>'pure-form','id'=>'apagaRegistro');
                        echo form_open('telefonia/apagaAparelho',$data);
                        
                            echo form_label('N&uacute;mero', 'apg_nome');
                    		$data = array('id'=>'apg_nome', 'name'=>'apg_nome', 'class'=>'form-control');
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
                    <li class="active">Aparelhos</li>
                </ol>
                <div id="divMain">
                    <?php
                        
                        $postLinha = (isset($postLinha))? $postLinha: false;
                        $postImei = (isset($postImei))? $postImei: false;
                        $postMarca = (isset($postMarca))? $postMarca: false;
                        $postModelo = (isset($postModelo))? $postModelo: false;
                        $postTipo = (isset($postTipo))? $postTipo: false;
                        $postStatus = (isset($postStatus))? $postStatus: false;
                        $pesquisa = (isset($pesquisa))? $pesquisa: false;
                        
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'pesquisar');
                    	echo form_open('telefonia/pesqAparelho',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
                            
                            $botaoCadastrar = (in_array(224, $this->session->userdata('permissoes')))? "<a href='".base_url('telefonia/fichaAparelho')."' class='linkDireita'>Cadastrar&nbsp<span class='glyphicon glyphicon-plus'></span></a>": '';
                            
                    		echo form_fieldset("Pesquisar aparelho".$botaoCadastrar, $attributes);
                    		  
                                echo '<div class="row">';
                                    
                                    echo '<div class="col-md-2">';
                                    #print_r(array_keys($listaBancos));
                                    echo form_label('Linha', 'linha');
                        			$data = array('name'=>'linha', 'value'=>$postLinha,'id'=>'linha', 'placeholder'=>'Digite a linha', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    #print_r(array_keys($listaBancos));
                                    echo form_label('Imei', 'imei');
                        			$data = array('name'=>'imei', 'value'=>$postImei,'id'=>'imei', 'placeholder'=>'Digite o imei', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    $options = array(''=>'');
                                    foreach($marcas as $mar){
                                        $options[$mar->cd_telefonia_marca] = $mar->nome;
                                    }		
                            		echo form_label('Marca', 'marca');
                            		echo form_dropdown('marca', $options, $postMarca, 'id="marca" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    #print_r(array_keys($listaBancos));
                                    echo form_label('Modelo', 'modelo');
                        			$data = array('name'=>'modelo', 'value'=>$postModelo,'id'=>'modelo', 'placeholder'=>'Digite o nome', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    $options = array(''=>'', 'CEL' => 'Celular', 'INT' => 'Interface');		
                            		echo form_label('Tipo', 'tipo');
                            		echo form_dropdown('tipo', $options, $postTipo, 'id="tipo" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    $options = array(''=>'', 'Ativo' => 'Ativo', 'Estoque' => 'Estoque', 'Avariado' => 'Avariado', 'Furtado' => 'Furtado', 'Baixa Estoque' => 'Baixa Estoque');		
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
                    
                    $colunas[] = anchor("telefonia/pesqAparelho/".(($postLinha == '')? '0': $postLinha)."/".(($postImei == '')? '0': $postImei)."/".(($postMarca == '')? '0': $postMarca)."/".(($postModelo == '')? '0': $postModelo)."/".(($postTipo == '')? '0': $postTipo)."/".(($postStatus == '')? '0': $postStatus)."/".$nome."/".(($sort_order == 'asc' && $sort_by == $nome) ? 'desc' : 'asc') ,$display.$icoAscDesc, array('class' => $class));
                    
                }
                $colunas[] = 'A&ccedil;&atilde;o';

                #$this->table->set_heading('Login', 'Nome', /*'E-mail',*/ 'Cidade', 'Departamento', 'Perfil', 'A&ccedil;&atilde;o');
                $this->table->set_heading($colunas);
                
                $contCell = 0;        
                foreach($dados as $da){
                    
                    #$cell[$contCell++] = array('data' => $da->cd_telefonia_aparelho);
                    #$cell2 = array('data' => $da->imei);
                    
                    /*if($da->imei != ''){
                        $modelo = '<a href="#" title="'.$da->imei.'">'.html_entity_decode($da->modelo).'</a>';
                    }else{
                        $modelo = html_entity_decode($da->modelo);
                    }*/
                    
                    if($da->tipo == 'Celular'){
                        $tipo = 'C-';
                    }else{
                        $tipo = 'I-';
                    }
                    
                    $marca = '<a href="#" title="'.$da->tipo.'">'.$tipo.$da->marca.'</a>';
                    
                    $cell[$contCell++] = array('data' => $marca);
                    $cell[$contCell++] = array('data' => htmlentities($da->modelo));
                    #$cell[$contCell++] = array('data' => $da->tipo);
                    $cell[$contCell++] = array('data' => ($da->imei)? htmlentities($da->imei): '');
                    $cell[$contCell++] = array('data' => htmlentities($da->nome_usuario));
                    $cell[$contCell++] = array('data' => $da->status);
                    
                    $botaoVisualizar = (in_array(243, $this->session->userdata('permissoes')))? '<a title="Visualizar" href="'.base_url('telefonia/visualizarAparelho/'.$da->cd_telefonia_aparelho).'" class="glyphicon glyphicon-search"></a>': '';
                    $botaoEditar = (in_array(224, $this->session->userdata('permissoes')))? '<a title="Editar" href="'.base_url('telefonia/fichaAparelho/'.$da->cd_telefonia_aparelho).'" class="glyphicon glyphicon glyphicon-pencil"></a>': '';
                    $botaoExcluir = (in_array(225, $this->session->userdata('permissoes')))? '<a title="Apagar" href="#" onclick="apagarRegistro('.$da->cd_telefonia_aparelho.',\''.$da->marca." - ".$da->modelo.'\')" data-toggle="modal"  data-target="#apaga" class="glyphicon glyphicon glyphicon glyphicon-remove"></a>': '';
                    
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