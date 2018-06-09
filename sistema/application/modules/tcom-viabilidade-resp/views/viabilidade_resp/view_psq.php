<?php
echo link_tag(array('href' => 'assets/css/tooltip.css','rel' => 'stylesheet','type' => 'text/css'));
?>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
    <!-- INÍCIO Modal Apaga registro -->
    <div class="modal fade" id="apaga" tabindex="-1" role="dialog" aria-labelledby="apaga" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="myModalLabel">Deseja apagar <?php echo $assunto; ?>?</h4>
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
                    <li><a href="<?php echo base_url('tcom'); ?>"><?php echo strtolower($assunto); ?></a></li>
                    <li class="active"><?php echo $titulo; ?></li>
                </ol>
                <div id="divMain">
                    <?php
                        
                        $controle = (isset($controle))? $controle: false;
                        $n_solicitacao = (isset($n_solicitacao))? $n_solicitacao: false;
                        $numero = (isset($numero))? $numero: false;
                        $designacao = (isset($designacao))? $designacao: false;
                        $status = (isset($status))? $status: false;
                        $aprovacao = (isset($aprovacao))? $aprovacao: false;
                        $andamento = (isset($andamento))? $andamento: false;
                        $endereco = (isset($endereco))? $endereco: false;
                        
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'pesquisar');
                    	echo form_open($pasta.'/'.$controller.'/pesq',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
                            
                            $botaoAtivacao = (in_array($perAtivacao, $this->session->userdata('permissoes')))? "<a href='".base_url($pasta.'/ativacao/realizarAtivacao')."' class='linkDireita'>ATIVAÇÃO&nbsp<span class='glyphicon glyphicon-plus'></span></a>": '';
                            $botaoCadastrar = (in_array($perEditarCadastrar, $this->session->userdata('permissoes')))? "<a href='".base_url($pasta.'/'.$controller.'/ficha')."' class='linkDireita'>NOVO&nbsp<span class='glyphicon glyphicon-plus'></span></a>": '';
                            
                    		echo form_fieldset($titulo.$botaoCadastrar.$botaoAtivacao, $attributes);
                    		  
                                echo '<div class="row">';
                                
                                    echo '<div class="col-md-3">';
                                    #print_r(array_keys($listaBancos));
                                    echo form_label('Controle', 'controle');
                        			$data = array('name'=>'controle', 'value'=>$controle,'id'=>'controle', 'placeholder'=>'Digite o controle', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    #print_r(array_keys($listaBancos));
                                    echo form_label('Número do pedido', 'n_solicitacao');
                        			$data = array('name'=>'n_solicitacao', 'value'=>$n_solicitacao,'id'=>'n_solicitacao', 'placeholder'=>'Digite o número do pedido', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    #print_r(array_keys($listaBancos));
                                    echo form_label('Número do contrato', 'numero');
                        			$data = array('name'=>'numero', 'value'=>$numero,'id'=>'numero', 'placeholder'=>'Digite a número', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    #print_r(array_keys($listaBancos));
                                    echo form_label('Designação', 'designacao');
                        			$data = array('name'=>'designacao', 'value'=>$designacao,'id'=>'designacao', 'placeholder'=>'Digite a designação', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array(''=>'', 'S' => 'Sim', 'N' => 'Não');		
                            		echo form_label('Viável', 'viavel');
                            		echo form_dropdown('viavel', $options, $status, 'id="viavel" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array(''=>'', 'S' => 'Sim', 'N' => 'Não', 'P' => 'Pendente');		
                            		echo form_label('Aprovado', 'aprovacao');
                            		echo form_dropdown('aprovacao', $options, $aprovacao, 'id="aprovacao" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array(''=>'', 'C' => 'Concluído', 'P' => 'Pendente', 'A'=>'Atrasado');		
                            		echo form_label('Andamento', 'andamento');
                            		echo form_dropdown('andamento', $options, $andamento, 'id="andamento" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array(''=>'');
                                    foreach($unidade as $uni){
                                        $options[$uni->cd_unidade] = htmlentities($uni->nome);
                                    }		
                            		echo form_label('Permissor', 'cd_unidade');
                            		echo form_dropdown('cd_unidade', $options, $cd_unidade, 'id="vistoriado" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-5">';
                                    #print_r(array_keys($listaBancos));
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
                    
                    $campo1 = strtolower($campos[1]);
                    $campo2 = strtolower($campos[2]);

                    $nome = $da->$campo1.' - '.$da->$campo2;
                    
                    foreach($campos as $campo => $valor){
                        $valor = strtolower($valor);
                        
                        if($campo != $id){                        
                                                
                            if(in_array($valor, array('prazo'))){
                                $title = 'title="'.htmlentities($da->dias_atraso).'"';
                                #$title = '';
                                $cell[$contCell++] = array('data' => '<span '.$title.'>'.$this->util->formataData($da->$valor, 'BR').'</span>', 'class' => $classSituacao );
                            }else{
                                
                                if($valor == 'controle'){
                                    $cell[$contCell++] = array('data' => '<span title="'.htmlentities($da->status).'">'.ucfirst(htmlentities($da->$valor)).'</span>', 'class' => $classSituacao );
                                }elseif(in_array($valor, array('n_solicitacao','numero','designacao','status'))){
                                    $cell[$contCell++] = array('data' => '<span title="'.htmlentities($da->$valor).'">'.htmlentities(substr($da->$valor,0, 8)).'</span>', 'class' => $classSituacao );
                                }else{
                                    $cell[$contCell++] = array('data' => ucfirst(htmlentities($da->$valor)), 'class' => $classSituacao );
                                }
                                
                            }
                        
                        }                                                
                    
                    }
                    
                    $botaoAprovacao = ($da->viavel == 'Sim' and in_array($perAprovacao, $this->session->userdata('permissoes')))? '<a title="Definir aprovação" href="'.base_url($pasta.'/'.$controller.'/aprovacao/'.$da->$pk).'" class="glyphicon glyphicon-check"></a>': '';
                    $botaoImprimir = (in_array($perImprimir, $this->session->userdata('permissoes')))? '<a target="_blank" title="Imprimir" href="'.base_url($pasta.'/'.$controller.'/imprimir/'.$da->$pk).'" class="glyphicon glyphicon-print"></a>': '';
                    $botaoHistorico = ($da->viavel == 'Sim' and in_array($perVisualizarHistorico, $this->session->userdata('permissoes')))? '<a title="Histórico" target="_BLANK" href="'.base_url('tcom-viabilidade-resp-hist/viabilidadeRespHist/listarHistorico/'.$da->$pk).'" class="glyphicon glyphicon-list"></a>': '';
                    $botaoEditar = (!$da->aprovacao and in_array($perEditarCadastrar, $this->session->userdata('permissoes')))? '<a title="Editar" href="'.base_url($pasta.'/'.$controller.'/ficha/'.$da->$pk).'" class="glyphicon glyphicon-pencil"></a>': '';
                    $botaoExcluir = (in_array($perExcluir, $this->session->userdata('permissoes')))? '<a title="Apagar" href="#" data-toggle="modal" data-target="#apaga" key="'.$da->$pk.'" nome="'.$nome.'" class="del glyphicon glyphicon-remove"></a>': '';
                    $cell[$contCell++] = array('data' => $botaoAprovacao.$botaoImprimir.$botaoHistorico.$botaoEditar.$botaoExcluir);
                        
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

$(".del").click(function(){
   $("#apg_id").val($(this).attr('key'));
   $("#apg_nome").val($(this).attr('nome'));
});

function apagarRegistro(id, nome){
    $("#apg_id").val(id);
    $("#apg_nome").val(nome);
}

</script>