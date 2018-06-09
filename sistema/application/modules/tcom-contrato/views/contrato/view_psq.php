<?php 
echo link_tag(array('href' => 'assets/css/tooltip.css','rel' => 'stylesheet','type' => 'text/css'));
?>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.maskMoney.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.blockui/jquery.block.ui.js') ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mtz.monthpicker.js") ?>"></script>
<style>
.mtz-monthpicker-year{
    width: 70px;
}
.mtz-monthpicker.mtz-monthpicker-year{
    color: black;
}
</style>
<?php
$this->load->view('contrato/view_modal_valores');
?>
    
    <!-- INÍCIO Modal Histórico -->
    <div class="modal fade" id="historico" tabindex="-1" role="dialog" aria-labelledby="historico" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="myModalLabel">Histórico do contrato</h4>
                </div>
                <div class="modal-body">
                <?php              
                    echo form_label('Designação', 'alt_nome');
            		$data = array('id'=>'historico_nome', 'name'=>'historico_nome', 'readonly'=>'readonly', 'class'=>'form-control');
            		echo form_input($data,'');
                    echo '<div id="historicoLista"></div>';       
                ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                </div>
        </div>
      </div>
    </div>
    <!-- FIM Modal Histórico -->

    <!-- INÍCIO Modal Anexar arquivo -->
    <div class="modal fade" id="anexar" tabindex="-1" role="dialog" aria-labelledby="anexar" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="myModalLabel">Deseja anexar / alterar anexo do <?php echo $controller; ?>?</h4>
                </div>
                <div class="modal-body">
                <?php              
                    $data = array('class'=>'pure-form','id'=>'frm-anexar');
                    echo form_open_multipart($pasta.'/'.$controller.'/salvarAnexo',$data);
                    
                        echo form_label('Designação', 'alt_nome');
                		$data = array('id'=>'anexo_nome', 'name'=>'anexo_nome', 'readonly'=>'readonly', 'class'=>'form-control');
                		echo form_input($data,'');
                        
                        echo '<div id="anexado"></div>';

                        echo form_label('Novo Arquivo', 'anexo');
            			$data = array('name'=>'anexo','id'=>'anexo', 'placeholder'=>'Selecione o arquivo', 'class'=>'form-control');
            			echo form_upload($data, '');
                    
                ?>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="anexo_id" name="id" />
                    <input type="hidden" id="anexoOrigem" name="anexoOrigem" />
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Anexar</button>
                </div>
                <?php
                echo form_close();
                ?>
        </div>
      </div>
    </div>
    <!-- FIM Modal Anexar arquivo -->

    <!-- INÍCIO Modal Alterar status -->
    <div class="modal fade" id="alt-status" tabindex="-1" role="dialog" aria-labelledby="alt-status" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="myModalLabel">Deseja alterar o status ou vigência do <?php echo $controller; ?>?</h4>
                </div>
                <div class="modal-body row">
                <?php              
                    $data = array('class'=>'pure-form','id'=>'altStatus');
                    echo form_open($pasta.'/'.$controller.'/alterarStatusVigencia',$data);
                    
                        echo '<div class="col-md-12">';
                        echo form_label('Designação', 'alt_nome');
                		$data = array('id'=>'alt_nome', 'name'=>'alt_nome', 'readonly'=>'readonly', 'class'=>'form-control');
                		echo form_input($data,'');
                        echo '</div>';
                        
                        $options = array('12' => '12', '24' => '24', '36' => '36');	
                        echo '<div class="col-md-3">';	
                		echo form_label('Dur. meses', 'duracao_mes');
                		echo form_dropdown('duracao_mes', $options, 'A', 'id="duracao_mes" class="form-control"');
                        echo '</div>';
                        
                        echo '<div class="col-md-3">';
                        echo form_label('Calcular na:', 'calcular_hoje');
                        $data = array(
                            'name'        => 'calcular_hoje',
                            'id'          => 'calcular_hoje',
                            'value'       => 'S'
                            );
                        
                        echo form_checkbox($data);
                        echo 'Data de hoje';
                        echo '</div>';
                        
                        echo '<div class="col-md-3">';
                        echo form_label('Data início', 'alt_data_inicio');
            			$data = array('name'=>'alt_data_inicio', 'value'=>'','id'=>'alt_data_inicio', 'readonly'=>true, 'class'=>'form-control');
            			echo form_input($data);
                        echo '</div>';
                        
                        echo '<div class="col-md-3">';
                        echo form_label('Data fim', 'alt_data_fim');
            			$data = array('name'=>'alt_data_fim', 'value'=>'','id'=>'alt_data_fim', 'readonly'=>'readonly', 'placeholder'=>'Informe o data', 'class'=>'form-control');
            			echo form_input($data);
                        echo '</div>';
                        /*
                        echo '<div class="col-md-3">';
                        echo form_label('Data 1ª futura.', 'alt_data_pri_fat');
            			$data = array('name'=>'alt_data_pri_fat', 'value'=>'','id'=>'alt_data_pri_fat', 'placeholder'=>'Informe o data', 'class'=>'data form-control');
            			echo form_input($data);
                        echo '</div>';
                        */
                        $options = array('A' => 'Ativo', 'I' => 'Inativo', 'C' => 'Cancelado'/*, 'P' => 'Pendente'*/);	
                        echo '<div class="col-md-12">';	
                		echo form_label('Status', 'alt_status');
                		echo form_dropdown('alt_status', $options, 'A', 'id="alt_status" class="form-control"');
                        echo '</div>';
                        
                        echo '<div style="max-height: 300px;overflow: auto" class="col-md-12">';	
                        echo '<table id="equip-adicionados" class="table zebra">';
                        echo '<tr>';
                        echo '<th colspan="3">Equipamentos adicionados</th>';
                        echo '</tr>';
                        echo '<tr>';
                        echo '<td>Marca</td>';
                        echo '<td>Modelo</td>';
                        echo '<td>Código</td>';
                        #echo '<td>Ação</td>';
                        echo '</tr>';
                        echo '</table>';
                        echo '</div>';
                    
                ?>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="alt_id" name="alt_id" />
                    <input type="hidden" id="alt_backup_mes" name="alt_backup_mes" />
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Alterar</button>
                </div>
                <?php
                echo form_close();
                ?>
        </div>
      </div>
    </div>
    <!-- FIM Modal Alterar status -->

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
                    <li><a href="<?php echo base_url('tcom'); ?>"><?php echo strtolower($assunto); ?></a></li>
                    <li class="active"><?php echo $titulo; ?></li>
                </ol>
                <div id="divMain">
                    <?php
                        $designacao = (isset($designacao))? $designacao: false;
                        $cd_unidade = (isset($cd_unidade))? $cd_unidade: false;
                        $status = (isset($status))? $status: false;
                        
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'pesquisar');
                    	echo form_open($pasta.'/'.$controller.'/pesq',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
                            
                            $botaoBacklog = (in_array($perVisualizarHistorico, $this->session->userdata('permissoes')))? "<a href='".base_url('tcom-contrato/backLog/pesq')."' class='linkDireita'>BACKLOG&nbsp<span class='glyphicon glyphicon-search'></span></a>": '';
                            $botaoAtivacao = (in_array($perAtivacao, $this->session->userdata('permissoes')))? "<a href='".base_url('tcom-viabilidade-resp/ativacao/realizarAtivacao')."' class='linkDireita'>ATIVAÇÃO&nbsp<span class='glyphicon glyphicon-plus'></span></a>": '';
                            
                    		echo form_fieldset($titulo.$botaoBacklog.$botaoAtivacao, $attributes);
                    		  
                                echo '<div class="row">';
                                    
                                    echo '<div class="col-md-3">';
                                    #print_r(array_keys($listaBancos));
                                    echo form_label('Número do contrato', 'numero');
                        			$data = array('name'=>'numero', 'value'=>$numero,'id'=>'numero', 'placeholder'=>'Digite o número', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    #print_r(array_keys($listaBancos));
                                    echo form_label('Designação', 'designacao');
                        			$data = array('name'=>'designacao', 'value'=>$designacao,'id'=>'designacao', 'placeholder'=>'Digite a designação', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array('' => '');		
                            		foreach($permissor as $per){
                      		            
                                        if($per->permissor != ''){                                        
                          			       $options[$per->cd_unidade] = $per->permissor.' - '.htmlentities($per->nome);
                                        }                                      
                            		}	
                            		echo form_label('Permissor', 'cd_unidade');
                            		echo form_dropdown('cd_unidade', $options, $cd_unidade, 'id="cd_unidade" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array(''=>'', 'A' => 'Ativo', 'I' => 'Inativo', 'P' => 'Pendente', 'C' => 'Cancelado');		
                            		echo form_label('Status', 'status');
                            		echo form_dropdown('status', $options, $status, 'id="status" class="form-control"');
                                    echo '</div>';
                                       
                                echo '</div>';                      
                                                                
                                echo '<div class="actions">';
                                echo form_submit("btn",utf8_encode($titulo), 'class="btn btn-primary pull-right"');
                                echo '</div>';
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
                    
                    ?>    
                </div>
                
                <div class="row">&nbsp</div>
                <div class="well">
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
                    
                    $campo1 = strtolower($campos[1]);
                    $campo2 = strtolower($campos[2]);

                    $nome = htmlentities($da->$campo1.' - '.$da->$campo2);
                    
                    foreach($campos as $campo => $valor){
                        $valor = strtolower($valor);
                        if($campo != $id){
                            
                            #if(in_array($valor, array('designacao'))){
                                #$cell[$contCell++] = array('data' => '<span title="'.htmlentities($da->$valor).'">'.htmlentities(substr($da->$valor,0, 8)).'</span>', 'class' => $classSituacao );
                            #}else{
                                $cell[$contCell++] = array('data' => ucfirst(htmlentities($da->$valor)) );
                            #}
                            
                        }
                    
                    }
                    
                    $botaoAnexar = (in_array($perAnexar, $this->session->userdata('permissoes')))? '<a title="Anexar arquivo" href="#" data-toggle="modal" data-target="#anexar" key="'.$da->$pk.'" nome="'.$nome.'" anexo="'.$da->anexo.'" class="anexar glyphicon glyphicon-saved"></a>': '';
                    $botaoHistorico = (in_array($perVisualizarHistorico, $this->session->userdata('permissoes')))? '<a title="Histórico" href="#" data-toggle="modal" data-target="#historico" key="'.$da->$pk.'" nome="'.$nome.'" class="historico glyphicon glyphicon-list"></a>': '';
                    $botaoStatus = (/*$da->status != 'Pendente' and*/ in_array($perStatus, $this->session->userdata('permissoes')))? '<a title="Alterar status ou vigência" href="#" data-toggle="modal" data-target="#alt-status" key="'.$da->$pk.'" nome="'.$nome.'" dt-inicio="'.$da->data_inicio.'" dt-fim="'.$da->data_fim.'" mes="'.$da->duracao_mes.'" status="'.$da->status.'" class="status glyphicon glyphicon-share"></a>': '';
                    $botaoImprimir = (in_array($perImprimir, $this->session->userdata('permissoes')))? '<a title="Imprimir" target="_blank" href="'.base_url($pasta.'/'.$controller.'/imprimir/'.$da->$pk).'" class="glyphicon glyphicon-print"></a>': '';
                    $botaoEditar = (in_array($perEditarCadastrar, $this->session->userdata('permissoes')))? '<a title="Editar" href="'.base_url($pasta.'/'.$controller.'/ficha/'.$da->$pk).'" class="glyphicon glyphicon-pencil"></a>': '';
                    $botaoValor = (in_array($perValores, $this->session->userdata('permissoes')))? '<a title="Valor" href="#" data-toggle="modal" data-target="#valores" key="'.$da->$pk.'" unidade="'.$da->cd_unidade.'" nome="'.$da->numero.'" class="valoresDados glyphicon glyphicon-usd"></a>': '';
                    $botaoExcluir = (in_array($perExcluir, $this->session->userdata('permissoes')))? '<a title="Apagar" href="#" data-toggle="modal" data-target="#apaga" key="'.$da->$pk.'" nome="'.$nome.'" class="del glyphicon glyphicon-remove"></a>': '';
                    
                    $botaoAnexar = ''; $botaoStatus = '';
                    $cell[$contCell++] = array('data' => $botaoImprimir.$botaoHistorico.$botaoAnexar.$botaoStatus.$botaoEditar.$botaoValor.$botaoExcluir);
                        
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

$.fn.carregando = function(msg) {
    $(document).ajaxStart(
        $.blockUI({ 
        message:  '<h1>'+msg+'...</h1>',
        css: { 
        	border: 'none', 
        	padding: '15px', 
        	backgroundColor: '#000', 
        	'-webkit-border-radius': '10px', 
        	'-moz-border-radius': '10px', 
        	opacity: .5, 
        	color: '#fff',
            'z-index': '99999999999999' 
        	} 
        })
    ); 
    
    //setTimeout($.unblockUI, tempo);   
    //$(document).ajaxStart($.blockUI);
    
}

$(".envia").click(function(){
   
   $("#email").val($(this).attr('sendEmail'));
   
   if($(this).attr('sendEmail') == 'sim'){
    $(this).carregando('Salvando e enviando e-mail');
   }
    
});

$(".dinheiro").maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:',', affixesStay: false});

$(".valoresDados").click(function(){
    
   $("#printFichaFin").attr('href','<?php echo base_url("tcom-contrato/contrato/imprimirAnaliseFin"); ?>/'+$(this).attr('key')); 
   $("#nome_contrato_valor").val($(this).attr('nome'));
   $("#valor_id").val($(this).attr('key'));
   //alert($(this).attr('key'));
   $.ajax({
      type: "POST",
      url: '<?php echo base_url().$modulo; ?>/ajaxContrato/valoresContrato',
      data: {
        id: $(this).attr('key')
      },
      dataType: "json",
      error: function(res) {
        alert('erro');
      },
      success: function(res) {
        
        if(res != 0){
            $("#valor").val(res.valor);
            $("#data_pri_fatura").val(res.data_pri_fatura);
            $("#mens_contratada_sem_imposto").val(res.mens_contratada_sem_imposto);
            $("#mens_atual_sem_imposto").val(res.mens_atual_sem_imposto);
            $("#mens_atual_com_imposto").val(res.mens_atual_com_imposto);
            $("#taxa_inst_com_imposto").val(res.taxa_inst_com_imposto);
            $("#taxa_inst_sem_imposto").val(res.taxa_inst_sem_imposto);
            $("#primeira_mensalidade").val(res.primeira_mensalidade);
            $("#mao_obra_empreiteira").val(res.mao_obra_empreiteira);
            $("#aquisicao_equipamento").val(res.aquisicao_equipamento);
            $("#sub_total").val(res.sub_total);
            $("#receita_total_sem_imposto").val(res.receita_total_sem_imposto);
            $("#receita_total_com_imposto").val(res.receita_total_com_imposto);
            //$("#valor_id").val(res.idContrato);
            
            $("#multa_porc").val(res.multa_porc);
            $("#multa_nao_dev_equip").val(res.multa_nao_dev_equip);
            $("#dia_vencimento").val(res.dia_vencimento);
            $("#dia_pro_rata_pri_mes").val(res.dia_pro_rata_pri_mes);
            $("#data_ult_fatura").val(res.data_ult_fatura);
            $("#idIndiceReajuste").val(res.idIndiceReajuste); 
            $("#idRegraReajuste").val(res.idRegraReajuste); 
            $("#mes_reajuste").val(res.mes_reajuste); 
            $("#isencao_icms").val(res.isencao_icms);
            $("#faturado_siga").val(res.faturado_siga);
            $("#proximo_reajuste").val(res.proximo_reajuste); 
            $("#idOper").val(res.idOper);
            $("#grupo").val(res.idGrupo);
            
            //if(res.faturado_por !== null){
                $("#faturado_por").val(res.faturado_por);
            //}else{
                //$("#faturado_por").val($(".valoresDados[key='"+res.idContrato+"']").attr('unidade'));
            //}
        }else{
            $("#valor").val('');
            $("#mens_contratada_sem_imposto").val('');
            $("#mens_atual_sem_imposto").val('');
            $("#mens_atual_com_imposto").val('');
            $("#taxa_inst_com_imposto").val('');
            $("#taxa_inst_sem_imposto").val('');
            $("#primeira_mensalidade").val('');
            $("#mao_obra_empreiteira").val('');
            $("#aquisicao_equipamento").val('');
            $("#sub_total").val('');
            $("#receita_total_sem_imposto").val('');
            $("#receita_total_com_imposto").val('');
            //$("#valor_id").val('');
            $("#faturado_por").val('');
            $("#multa_porc").val('');
            $("#multa_nao_dev_equip").val('');
            $("#dia_vencimento").val('');
            $("#dia_pro_rata_pri_mes").val('');
            $("#data_ult_fatura").val('');
            $("#idIndiceReajuste").val('');
            $("#idRegraReajuste").val(''); 
            $("#mes_reajuste").val(''); 
            $("#isencao_icms").val('');     
            $("#faturado_siga").val('');  
            $("#proximo_reajuste").val(''); 
            $("#idOper").val('');
            $("#grupo").val(res.idGrupo);        
        }
        
      }
    });
    
});

$(".historico").click(function(){
   
   $("#historico_nome").val($(this).attr('nome'));
   
    $.ajax({
      type: "POST",
      url: '<?php echo base_url().$modulo; ?>/ajaxContrato/listaHistoricos',
      data: {
        id: $(this).attr('key')
      },
      dataType: "json",
      error: function(res) {
        alert('erro');
      },
      success: function(res) {
        
        var linkPrintResp = '<?php echo base_url('tcom-viabilidade-resp/viabilidadeResp/imprimir/'); ?>';
        
        if(res != 0){
            
            var table = '<table class="table">';
                table += '<tr>';
                table += '<th>Tipo</th>';
                table += '<th>Status</th>';
                table += '<th>Data</th>';
                table += '<th>Imprimir</th>';
                table += '</tr>';
            $.each(res, function() {
              table += '<tr>';
              table += '<td><a href="<?php echo base_url('tcom-viabilidade-resp-hist/viabilidadeRespHist/listarHistorico'); ?>/'+this.id+'" target="_blank">'+this.nome+'</a></td>';
              table += '<td>'+this.status+'</td>';
              table += '<td>'+this.data_cadastro+'</td>';
              table += '<th><a title="Imprimir" target="_blank" href="'+linkPrintResp+'/'+this.id+'" class="glyphicon glyphicon-print"></a></th>';
              table += '</tr>';
            });
            
            $("#historicoLista").html(table);
        
        }
        
      }
    });
   
});

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

$(".anexar").click(function(){
    
    var dir = '<?php echo base_url($dirDownload).'/';?>';
    
    $("#anexo_id").val($(this).attr('key'));
    $("#anexo_nome").val($(this).attr('nome'));
   
    if($(this).attr('anexo') != ''){
    
        var link = dir+$(this).attr('anexo');
        $("#anexado").html('<strong>Arquivo anexado</strong><br><a target="_BLANK" href="'+link+'">'+$(this).attr('anexo')+'</a>');
        $("#anexoOrigem").val($(this).attr('anexo'));
    
    }else{
    
        $("#anexado").html(''); 
        $("#anexoOrigem").val('');
    
    }
   
});

$(".status").click(function(event){
   $("#alt_id").val($(this).attr('key'));
   $("#alt_nome").val($(this).attr('nome'));
   $("#alt_data_inicio").val($(this).attr('dt-inicio'));
   $("#alt_data_fim").val($(this).attr('dt-fim'));
   $("#alt_data_pri_fat").val($(this).attr('dt-pri-fat'));
   $("#duracao_mes").val($(this).attr('mes'));
   $("#alt_backup_mes").val($(this).attr('mes'));
   
   if($(this).attr('status') == 'Ativo'){
    var status = 'A';
   }else if($(this).attr('status') == 'Inativo'){
    var status = 'I';
   }else if($(this).attr('status') == 'Cancelado'){
    var status = 'C';
   }else{
    var status = 'P';
   }
   
   $("#alt_status").val(status);
   
    $.ajax({
      type: "POST",
      url: '<?php echo base_url().$modulo; ?>/ajaxContrato/dadosContrato',
      data: {
        id: $(this).attr('key')
      },
      dataType: "json",
      error: function(res) {
        alert('erro');
      },
      success: function(res) {
        
        if(res != 0){
            
            var tr = '';
            $(".trDinamico").remove();
            $.each(res['equipamentos'], function() {
              tr += '<tr class="trDinamico">';
              tr += '<td>'+this.marca+'</td>';
              tr += '<td>'+this.modelo+'</td>';
              tr += '<td>'+this.codigo+'</td>';
              //tr += '<td></td>';
              tr += '</tr>';
            });
            
            $("#equip-adicionados").append(tr);
        
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

$(document).ready(function(){
    
    // Valida o formulário
	$("#frm-anexar").validate({
		debug: false,
		rules: {
            anexo: {
                required: true
            }
		},
		messages: {
            anexo: {
                required: "Selecione um arquivo."
            }
	   }
   });
   
   // Valida o formulário
/*	$("#altStatus").validate({
		debug: false,
		rules: {
            alt_data_fim: {
                required: true
            }
		},
		messages: {
            alt_data_fim: {
                required: "Informe a data fim."
            }
	   }
   });*/
    
});

$(".data").datepicker({
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

</script>