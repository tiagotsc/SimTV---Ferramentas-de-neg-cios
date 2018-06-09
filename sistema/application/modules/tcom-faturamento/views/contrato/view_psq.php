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
$this->load->view('tcom-contrato/contrato/view_modal_valores');
$this->load->view('contrato/view_modal_cred_deb');
?>
    
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
                    	echo form_open($pasta.'/'.$controller.'/pesqContr',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
                            
                    		echo form_fieldset($titulo, $attributes);
                    		  
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
                    
                    $botaoCredDeb = (in_array($perNotaCredDeb, $this->session->userdata('permissoes')))? '<a title="Nota de crédito / Débito" href="#" data-toggle="modal" data-target="#cred_deb" key="'.$da->$pk.'" unidade="'.$da->cd_unidade.'" nome="'.$da->numero.'" class="DadosCredDeb glyphicon glyphicon-sort"></a>': '';
                    $botaoValor = (in_array($perValores, $this->session->userdata('permissoes')))? '<a title="Valor" href="#" data-toggle="modal" data-target="#valores" key="'.$da->$pk.'" unidade="'.$da->cd_unidade.'" nome="'.$da->numero.'" class="valoresDados glyphicon glyphicon-usd"></a>': '';
                    $botaoValor = '';
                    $cell[$contCell++] = array('data' => $botaoCredDeb.$botaoValor);
                        
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
      url: '<?php echo base_url().'tcom-contrato'; ?>/ajaxContrato/valoresContrato',
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
            $("#isencao_icms").val('');     
            $("#faturado_siga").val('');  
            $("#proximo_reajuste").val(''); 
            $("#idOper").val('');
            $("#grupo").val(res.idGrupo);        
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


$(".del").click(function(){
   $("#apg_id").val($(this).attr('key'));
   $("#apg_nome").val($(this).attr('nome'));
});

function apagarRegistro(id, nome){
    $("#apg_id").val(id);
    $("#apg_nome").val(nome);
}

$(document).ready(function(){
    
    
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