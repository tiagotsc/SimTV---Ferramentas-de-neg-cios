<?php 
echo link_tag(array('href' => 'assets/css/tooltip.css','rel' => 'stylesheet','type' => 'text/css'));
?>

<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>

    <!-- INÍCIO Modal Pendência registro -->
    <div class="modal fade" id="pendencia" tabindex="-1" role="dialog" aria-labelledby="pendencia" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="myModalLabel">Responder pendência para realização de vistoria</h4>
                </div>
                <div class="modal-body">
                    <div id="feedbackGravacao"></div>
                    <div style="max-height: 300px;overflow: auto" class="row"><div id="todasPends" class="col-md-12"></div></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
                    <?php
                    echo form_close();
                    ?>
        </div>
      </div>
    </div>
    <!-- FIM Modal Apaga Pendência -->

    <!-- INÍCIO Modal Apaga registro -->
    <div class="modal fade" id="apaga" tabindex="-1" role="dialog" aria-labelledby="apaga" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="myModalLabel">Deseja apagar <?php echo $controller; ?>?</h4>
                </div>
                <div class="modal-body">
                    <?php              
                        $data = array('class'=>'pure-form','id'=>'apagaRegistro');
                        echo form_open($pasta.'/'.$controller.'/deleta',$data);
                        
                            echo form_label('Nome', 'apg_nome');
                    		$data = array('id'=>'apg_nome', 'name'=>'apg_nome', 'readonly'=>'readonly', 'class'=>'form-control');
                    		echo form_input($data,'');
                        
                    ?>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="apg_id" name="apg_id" />
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
    
            <div id="corpo" class="col-md-10 col-sm-9">
            <!--<div class="col-lg-12">-->
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a></li>
                    <li><a href="<?php echo base_url('telecom'); ?>"><?php echo ucfirst($assunto); ?></a></li>
                    <li class="active"><?php echo $titulo; ?></li>
                </ol>
                <div id="divMain">
                    <?php
                        
                        $controle = (isset($controle))? $controle: false;
                        $n_solicitacao = (isset($n_solicitacao))? $n_solicitacao: false;
                        $operadora = (isset($operadora))? $operadora: false;
                        $cd_unidade = (isset($cd_unidade))? $cd_unidade: false;
                        $idViabTipo = (isset($idViabTipo))? $idViabTipo: false;
                        $vistoriado = (isset($vistoriado))? $vistoriado: 'N';
                        $endereco = (isset($endereco))? $endereco: false;
                        
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'pesquisar');
                    	echo form_open($pasta.'/'.$controller.'/pesq',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
                            
                            $botaoCadastrar = (in_array($perEditarCadastrar, $this->session->userdata('permissoes')))? "<a href='".base_url($pasta.'/'.$controller.'/ficha')."' class='linkDireita'>Cadastrar&nbsp<span class='glyphicon glyphicon-plus'></span></a>": '';
                            
                    		echo form_fieldset($titulo.$botaoCadastrar, $attributes);
                    		  
                                echo '<div class="row">';
                                
                                    echo '<div class="col-md-4">';
                                    echo form_label('Controle', 'controle');
                        			$data = array('name'=>'controle', 'value'=>$controle,'id'=>'controle', 'placeholder'=>'Digite o controle', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    echo form_label('Número solicitação', 'n_solicitacao');
                        			$data = array('name'=>'n_solicitacao', 'value'=>$n_solicitacao,'id'=>'n_solicitacao', 'placeholder'=>'Digite o número da solictação', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    $options = array(''=>'');	
                                    foreach($tiposViab as $tipoV){
                                        $options[$tipoV->id] = htmlentities($tipoV->nome);
                                    }	
                            		echo form_label('Tipo', 'idViabTipo');
                            		echo form_dropdown('idViabTipo', $options, $idViabTipo, 'id="idViabTipo" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    $options = array('S'=>'Vistoriado','N'=>'Não vistoriado');		
                            		echo form_label('Vistoriado', 'vistoriado');
                            		echo form_dropdown('vistoriado', $options, $vistoriado, 'id="vistoriado" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    $options = array(''=>'');
                                    foreach($unidade as $uni){
                                        $options[$uni->cd_unidade] = htmlentities($uni->nome);
                                    }		
                            		echo form_label('Permissor', 'cd_unidade');
                            		echo form_dropdown('cd_unidade', $options, $cd_unidade, 'id="vistoriado" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    echo form_label('Endereço ponta B', 'endereco');
                        			$data = array('name'=>'endereco', 'value'=>$endereco,'id'=>'endereco', 'placeholder'=>'Digite o endereço', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                       
                                echo '</div>';                      
                                                                
                                echo '<div class="actions">';
                                echo form_submit("btn",'Pesquisar', 'class="btn btn-primary pull-right"');
                                echo '</div>';
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
                    
                    ?>    
                </div>
                
                <div class="row">&nbsp</div>
                <div class="well">
                    <div class="row">
                        <div class="col-md-2"> 
                        <span class="ok glyphicon glyphicon-asterisk" aria-hidden="true"></span> Conclu&iacute;do<br />
                        </div>
                        <div class="col-md-2"> 
                        <span class="pendente glyphicon glyphicon-asterisk" aria-hidden="true"></span> Pendente<br />
                        </div>
                        <div class="col-md-2"> 
                        <span class="atrasado glyphicon glyphicon-asterisk" aria-hidden="true"></span> Atrasado<br />
                        </div>
                    </div>
                <p>
                    <strong>Mostrando <?php echo ($qtdDadosCorrente)? $qtdDadosCorrente: 0; ?> de <?php echo ($qtdRegistos)? $qtdRegistos: 0; ?> registros localizados.</strong>
                </p>
                <?php 
    
                $colunas = array();
                $contCell = 0; 
                
                foreach($campos as $nome){     
                    
                    if($nome == $id){
                        $pk = $nome;
                    }
                    
                    if($nome != $id){
                    
                        if($sort_by == $nome){
                            $class = "sort_$sort_order";
                            
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
                        
                        $label = (isset($camposLabel[$nome]))? $camposLabel[$nome]: $nome;
                        
                        $colunas[] = anchor($pasta.'/'.$controller.'/'.$metodo.'/'.$post.'/'.$nome.'/'.(($sort_order == 'asc' && $sort_by == $nome) ? 'desc' : 'asc') ,$label.$icoAscDesc, array('class' => $class));
                        
                    }
                    
                }
                
                $colunas[] = 'A&ccedil;&atilde;o';
                
                $this->table->set_heading($colunas);
                
                foreach($dados as $da){
                    
                    if($da->situacao_atual == 'OK'){
                        $classSituacao = 'ok';
                    }elseif($da->situacao_atual == 'PENDENTE'){
                        $classSituacao = 'pendente';
                    }else{
                        $classSituacao = 'atrasado';
                    }
                    
                    $campo1 = removeAcentosSinaisEspacos(utf8_encode(strtolower($campos[1])));
                    $campo2 = removeAcentosSinaisEspacos(utf8_encode(strtolower($campos[2])));

                    $nome = $da->$campo1.' - '.$da->$campo2;
                    
                    foreach($campos as $campo => $valor){ 
                        $valor = removeAcentosSinaisEspacos(utf8_encode(strtolower($valor)));
                        if($campo != $id){
                            if(in_array($valor, array('dt_prazo','dt_solicitacao'))){
                                $cell[$contCell++] = array('data' => '<span title="'.htmlentities($da->dias_atraso).'">'.$this->util->formataData($da->$valor, 'BR').'</span>', 'class' => $classSituacao );
                            }elseif($valor == 'controle'){
                                
                                $iconePend = (in_array($perPendCadResp, $this->session->userdata('permissoes')) and $da->pendencia > 0)? '<span title="Existe '.$da->pendencia.' pendência" key="'.$da->id.'" qtd="'.$da->pendencia.'" data-toggle="modal" data-target="#pendencia" class="pendOpen cursorPointer icoPend'.$da->id.' glyphicon glyphicon-info-sign"></span>': '';
                                
                                $cell[$contCell++] = array('data' => ucfirst(htmlentities($da->$valor)).'&nbsp'.$iconePend, 'class' => $classSituacao );
                            }else{
                                $cell[$contCell++] = array('data' => ucfirst(htmlentities($da->$valor)), 'class' => $classSituacao );
                            }
                        }
                    
                    }
                    
                    $botaoImprimir = (in_array($perImprimir, $this->session->userdata('permissoes')))? '<a title="Imprimir" target="_blank" href="'.base_url($pasta.'/'.$controller.'/imprimirOrdem/'.$this->util->base64url_encode($da->$pk)).'" class="glyphicon glyphicon-print"></a>': '';
                    $botaoEditar = (in_array($perEditarCadastrar, $this->session->userdata('permissoes')))? '<a title="Editar" href="'.base_url($pasta.'/'.$controller.'/ficha/'.$da->$pk).'" class="glyphicon glyphicon-pencil"></a>': '';
                    $botaoExcluir = (in_array($perExcluir, $this->session->userdata('permissoes')))? '<a title="Apagar" href="#" data-toggle="modal" data-target="#apaga" key="'.$da->$pk.'" nome="'.$nome.'" class="del glyphicon glyphicon-remove"></a>': '';
                    $cell[$contCell++] = array('data' => $botaoImprimir.$botaoEditar.$botaoExcluir);
                        
                    $this->table->add_row($cell);
                    $contCell = 0; 
                    
                }
    
                $template = array('table_open' => '<table class="table zebra">');
            	$this->table->set_template($template);
            	echo $this->table->generate();
                echo "<ul class='pagination pagination-lg'>" . utf8_encode($paginacao) . "</ul>";
                ?>
                </div>
                
            </div>
            
<script type="text/javascript">

var pendenciaQtd = 0;

$(function() {
$( document ).tooltip({
  position: {
    my: "center bottom-20",
    at: "center top",
    using: function( position, feedback ) {
      $( this ).css( position );
      $( "<div>" )
        .addClass( "arrow" )
        .addClass( feedback.vertical )
        .addClass( feedback.horizontal )
        .appendTo( this );
    }
  }
});
});

$.fn.pegaPendencias = function(idViab) {
    
    $("#todasPends").html('');

    $.ajax({
      type: "POST",
      url: '<?php echo base_url('tcom-viabilidade-pend/ajaxViabilidadePend/pendenciasViabilidade'); ?>',          
      data: {
        idviab: idViab
      },
      dataType: "json",
      /*error: function(res) {
        $("#resMarcar").html('<span>Erro de execução</span>');
      },*/
      success: function(res) { 
        if(res){
            
            var classTable = '';
            var conteudo = '<table class="table">';
            
            var cont = 0;
            $.each(res, function() {
                
                if(cont % 2 == 0){ 
                    classTable = 'fundoCinzaClaro';
                }else{
                    classTable = '';
                }
                
                conteudo += '<tr class="'+classTable+'">';
                conteudo += '<td><strong>Autor Pergunta: </strong>'+this.usuario_pergunta+'</td>';
                conteudo += '<td><strong>Data / Hora: </strong>'+this.data_cadastro_pergunta+'</td>';
                conteudo += '</tr>';
                conteudo += '<tr class="'+classTable+'">';
                conteudo += '<td><strong>Status: </strong>'+this.status+'</td>';
                conteudo += '<td></td>';
                conteudo += '</tr>';
                conteudo += '<tr class="'+classTable+'">';
                conteudo += '<td colspan="2"><p class="text-left"><strong>Pergunta:</strong><br>'+this.pergunta+'</p></td>';
                conteudo += '</tr>';
                
                if(this.data_cadastro_resposta){
                    
                    conteudo += '<tr class="'+classTable+'">';
                    conteudo += '<td><strong>Autor Resposta: </strong>'+this.usuario_resposta+'</td>';
                    conteudo += '<td><strong>Data / Hora: </strong>'+this.data_cadastro_resposta+'</td>';
                    conteudo += '</tr>';
                    conteudo += '<tr class="'+classTable+'">';
                    conteudo += '<td colspan="2"><p class="text-left"><strong>Resposta:</strong><br>'+this.resposta+'</p></td>';
                    conteudo += '</tr>';
                    
                }else{
                    conteudo += '<tr class="'+classTable+'">';
                    conteudo += '<td colspan="2"><textarea id="resposta'+this.id+'" name="resposta'+this.id+'" class="form-control"></textarea></td>';
                    conteudo += '</tr>';
                    conteudo += '<tr class="'+classTable+'">';
                    conteudo += '<td><!--<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>--></td>';
                    conteudo += '<td><button type="button" style="float:right" id="btPend" idViab="'+this.idViab+'" key="'+this.id+'" class="btn btnPendResp btn-primary">Responder</button></td>';
                    conteudo += '</tr>';
                }
                
                cont++;
                
            });
            
            conteudo += '</table>';
            
            $("#todasPends").html(conteudo);
            
            $(this).montaBotaoSalvaResposta();
            
        }else{
            $("#todasPends").html('');
        }
        
      }
    }); 
    
}

$.fn.montaBotaoSalvaResposta = function(idViab) {
    $(".btnPendResp").click(function(){
        
        var idViab = $(this).attr('idviab');
        var idPend = $(this).attr('key');
        var resposta = $("#resposta"+idPend).val();       
                
        $.post( "<?php echo base_url('tcom-viabilidade-pend/ajaxViabilidadePend/salvarResposta'); ?>", {id: idPend, idviab: idViab, resposta: resposta }, function( data ) {
          
          if(data == 'OK'){
            if(pendenciaQtd == 1){ //alert(pendenciaQtd);
                $(".icoPend"+idViab).hide();
                pendenciaQtd = 0;
                //alert($(this).attr('qtd'));
            }
            var msg = '<div class="alert alert-success" role="alert"><strong>Respondida com sucesso!</strong></div>';
          }else if(data == 'ERRO'){
            var msg = '<div class="alert alert-warning" role="alert"><strong>Erro ao responder, caso o erro persista comunique o administrador.</strong></div>';
          }else{
            var msg = '<div class="alert alert-danger" role="alert"><strong>Você não tem permissão!</strong></div>';
          }
          
          $("#feedbackGravacao").html(msg);
          
          setInterval(function(){ $("#feedbackGravacao").html(''); }, 3000);
          $(this).pegaPendencias(idViab);
          
        }, "json");
        
    });
}

$(".pendOpen").click(function(){
   pendenciaQtd = $(this).attr('qtd');     
   $(this).pegaPendencias($(this).attr('key'));
    
});

$(".del").click(function(){
   $("#apg_id").val($(this).attr('key'));
   $("#apg_nome").val($(this).attr('nome'));
});

function apagarRegistro(id, nome){
    $("#apg_id").val(id);
    $("#apg_nome").val(nome);
}

</script>