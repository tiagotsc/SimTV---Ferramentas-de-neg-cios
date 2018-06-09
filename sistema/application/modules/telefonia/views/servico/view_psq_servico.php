<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
    <!-- INÍCIO Modal Apaga registro de telecom -->
    <div class="modal fade" id="apaga" tabindex="-1" role="dialog" aria-labelledby="apaga" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="myModalLabel">Deseja apagar o servi&ccedil;o?</h4>
                </div>
                <div class="modal-body">
                    <?php              
                        $data = array('class'=>'pure-form','id'=>'apagaRegistro');
                        echo form_open('telefonia/apagaServico',$data);
                        
                            echo form_label('Nome', 'apg_nome');
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
    <!-- FIM Modal Apaga registro de telecom -->
            <div class="col-md-10 col-sm-9">
            <!--<div class="col-lg-12">-->
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a></li>
                    <li><a href="<?php echo base_url('telefonia'); ?>">Telefonia</a></li>
                    <li class="active">Servi&ccedil;os</li>
                </ol>
                <div id="divMain">
                    <?php
                        
                        $postNome = (isset($postNome))? $postNome: false;
                        $postStatus = (isset($postStatus))? $postStatus: false;
                        $pesquisa = (isset($pesquisa))? $pesquisa: false;
                        
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'pesquisar');
                    	echo form_open('telefonia/pesqServico',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
                            
                            $botaoCadastrar = (in_array(231, $this->session->userdata('permissoes')))? "<a href='".base_url('telefonia/fichaServico')."' class='linkDireita'>Cadastrar&nbsp<span class='glyphicon glyphicon-plus'></span></a>": '';
                            
                    		echo form_fieldset("Pesquisar servi&ccedil;o".$botaoCadastrar, $attributes);
                    		  
                                echo '<div class="row">';
                                
                                    echo '<div class="col-md-8">';
                                    #print_r(array_keys($listaBancos));
                                    echo form_label('Nome do servi&ccedil;o', 'nome');
                        			$data = array('name'=>'nome', 'value'=>$postNome,'id'=>'nome', 'placeholder'=>'Digite o nome', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    $options = array(''=>'', 'A' => 'Ativo', 'I' => 'Inativo');		
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
                    
                    $colunas[] = anchor("telefonia/pesqServico/".(($postNome == '')? '0': $postNome)."/".(($postStatus == '')? '0': $postStatus)."/".$nome."/".(($sort_order == 'asc' && $sort_by == $nome) ? 'desc' : 'asc') ,$display.$icoAscDesc, array('class' => $class));
                    
                }
                $colunas[] = 'A&ccedil;&atilde;o';

                #$this->table->set_heading('Login', 'Nome', /*'E-mail',*/ 'Cidade', 'Departamento', 'Perfil', 'A&ccedil;&atilde;o');
                $this->table->set_heading($colunas);
                
                $contCell = 0;            
                foreach($dados as $da){
                    
                    #$cell[$contCell++] = array('data' => $da->cd_telefonia_servico);
                    $cell[$contCell++] = array('data' => htmlentities($da->nome));
                    $cell[$contCell++] = array('data' => htmlentities($da->qtd));
                    $cell[$contCell++] = array('data' => htmlentities($da->valor));
                    $cell[$contCell++] = array('data' => $this->util->formataData($da->data_inicio,'BR'));
                    $cell[$contCell++] = array('data' => $this->util->formataData($da->data_fim,'BR'));
                    $cell[$contCell++] = array('data' => htmlentities($da->status));
                    
                    $botaoEditar = (in_array(231, $this->session->userdata('permissoes')))? '<a title="Editar" href="'.base_url('telefonia/fichaServico/'.$da->cd_telefonia_servico).'" class="glyphicon glyphicon glyphicon-pencil"></a>': '';
                    $botaoExcluir = (in_array(232, $this->session->userdata('permissoes')))? '<a title="Apagar" href="#" onclick="apagarRegistro('.$da->cd_telefonia_servico.',\''.$da->nome.'\')" data-toggle="modal"  data-target="#apaga" class="glyphicon glyphicon glyphicon glyphicon-remove"></a>': '';
                    
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
    
<script type="text/javascript">

function apagarRegistro(cd, nome){
    $("#apg_cd").val(cd);
    $("#apg_nome").val(nome);
}

$(document).ready(function(){
    
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