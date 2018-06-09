    <!-- INÍCIO Modal Valores -->
    <div class="modal fade" id="valores" tabindex="-1" role="dialog" aria-labelledby="valores" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="myModalLabel">Valores do contrato</h4>
                </div>
                <div class="modal-body row">
                <?php  
                $data = array('class'=>'pure-form','id'=>'frm-valores');
                echo form_open($pasta.'/'.$controller.'/salvarValores',$data); 
                    
                    echo '<div class="col-md-8">';       
                    echo form_label('Contrato', 'alt_nome');
            		$data = array('id'=>'nome_contrato_valor', 'name'=>'nome_contrato_valor', 'readonly'=>'readonly', 'class'=>'form-control');
            		echo form_input($data,'');
                    echo '</div>';
                    
                    echo '<div class="col-md-4">';
                    echo form_label('Data 1ª fatura <a id="limpa_data_pri_fatura" class="fa fa-eraser" aria-hidden="true"></a>', 'data_pri_fatura');
        			$data = array('name'=>'data_pri_fatura', 'value'=>'','id'=>'data_pri_fatura', 'class'=>'form-control');
        			echo form_input($data);
                    echo '</div>';
                    
                    echo '<div class="col-md-4">';
                    echo form_label('Data última fatura', 'data_ult_fatura');
        			$data = array('name'=>'data_ult_fatura', 'value'=>'','id'=>'data_ult_fatura','readonly'=>'readonly', 'class'=>'form-control');
        			echo form_input($data);
                    echo '</div>';
                    
                    echo '<div class="col-md-4">';
                    echo form_label('Dia vencimento', 'dia_vencimento');
        			$data = array('name'=>'dia_vencimento', 'value'=>'','id'=>'dia_vencimento', 'class'=>'doisDigitos form-control');
        			echo form_input($data);
                    echo '</div>';
                    
                    echo '<div class="col-md-4">';
                    echo form_label('Dia pro rata 1º m&ecirc;s', 'dia_pro_rata_pri_mes');
        			$data = array('name'=>'dia_pro_rata_pri_mes', 'value'=>'','id'=>'dia_pro_rata_pri_mes', 'class'=>'doisDigitos form-control');
        			echo form_input($data);
                    echo '</div>';
                    
                    echo '<div class="col-md-4">';
                    echo form_label('Valor do contrato', 'valor');
        			$data = array('name'=>'valor', 'value'=>'','id'=>'valor', 'class'=>'dinheiro form-control');
        			echo form_input($data);
                    echo '</div>';
                    
                    echo '<div class="col-md-4">';
                    echo form_label('Mensal. contratada S/I', 'mens_contratada_sem_imposto');
        			$data = array('name'=>'mens_contratada_sem_imposto', 'value'=>'','id'=>'mens_contratada_sem_imposto', 'class'=>'dinheiro form-control');
        			echo form_input($data);
                    echo '</div>';
                    
                    echo '<div class="col-md-4">';
                    echo form_label('1ª mensalidade', 'primeira_mensalidade');
        			$data = array('name'=>'primeira_mensalidade', 'value'=>'','id'=>'primeira_mensalidade', 'class'=>'dinheiro form-control');
        			echo form_input($data);
                    echo '</div>';
                    
                    $options = array('' => '');
                    foreach($permissor as $per){
                        $options[$per->cd_unidade] = htmlentities($per->nome);
                    }	
                    echo '<div class="col-md-4">';	
            		echo form_label('Faturado por', 'faturado_por');
            		echo form_dropdown('faturado_por', $options, $faturado_por, 'id="faturado_por" class="form-control"');
                    echo '</div>'; 
                    
                    echo '<div class="col-md-4">';
                    echo form_label('Multa %', 'multa_porc');
        			$data = array('name'=>'multa_porc', 'value'=>'','id'=>'multa_porc', 'class'=>'form-control porcentagem');
        			echo form_input($data);
                    echo '</div>';
                    
                    $options = array('' => '', 'NAO' => 'Não','SIM' => 'Sim');
                    echo '<div class="col-md-4">';	
            		echo form_label('Multa não dev. equip.', 'multa_nao_dev_equip');
            		echo form_dropdown('multa_nao_dev_equip', $options, $multa_nao_dev_equip, 'id="multa_nao_dev_equip" class="form-control"');
                    echo '</div>'; 
                    
                    $options = array('' => '');
                    foreach($regrasReajustes as $regraR){
                        $options[$regraR->id] = htmlentities($regraR->nome);
                    }
                    echo '<div class="col-md-6">';	
            		echo form_label('Regra de reajuste', 'idRegraReajuste');
            		echo form_dropdown('idRegraReajuste', $options, $idRegraReajuste, 'id="idRegraReajuste" class="form-control"');
                    echo '</div>';
                    
                    $options = array('' => '');
                    for($i=1; $i <= 12; $i++){
                        $options[$i] = $i;
                    }
                    echo '<div id="div_mes_reajuste" class="col-md-6">';	
            		echo form_label('Mês reajuste', 'mes_reajuste');
            		echo form_dropdown('mes_reajuste', $options, $mes_reajuste, 'id="mes_reajuste" class="form-control"');
                    echo '</div>';
                    
                    $options = array('' => '');
                    foreach($indiceReajuste as $indRea){
                        $options[$indRea->id] = $indRea->nome;
                    }
                    echo '<div class="col-md-4">';	
            		echo form_label('Indice de reajuste', 'idIndiceReajuste');
            		echo form_dropdown('idIndiceReajuste', $options, $idIndiceReajuste, 'id="idIndiceReajuste" class="form-control"');
                    echo '</div>';
                    
                    echo '<div class="col-md-4">';
                    echo form_label('Próximo reajuste', 'proximo_reajuste');
        			$data = array('name'=>'proximo_reajuste', 'value'=>'','id'=>'proximo_reajuste', 'class'=>'form-control data');
        			echo form_input($data);
                    echo '</div>';
                    
                    $options = array('' => '', 'SIM' => 'Sim', 'NAO' => 'Não');
                    echo '<div class="col-md-4">';	
            		echo form_label('Isenção ICMS', 'isencao_icms');
            		echo form_dropdown('isencao_icms', $options, $isencao_icms, 'id="isencao_icms" class="form-control"');
                    echo '</div>';
                    
                    $options = array('' => '', 'SIM' => 'Sim', 'NAO' => 'Não');
                    echo '<div class="col-md-4">';	
            		echo form_label('Faturado pelo SIGA', 'faturado_siga');
            		echo form_dropdown('faturado_siga', $options, $faturado_siga, 'id="faturado_siga" class="form-control"');
                    echo '</div>';
                    
                    $options = array('' => '');
                    foreach($operadorasPai as $operPai){
                        $options[$operPai->id] = $operPai->titulo;
                    }
                    echo '<div class="col-md-8">';	
            		echo form_label('GRUPO', 'grupo');
            		echo form_dropdown('grupo', $options, '', 'id="grupo" class="form-control"');
                    echo '</div>';
                    
                    echo '<div class="col-md-12">';	
                    $options = array('' => '');	
                    foreach($operadoras as $oper){
                        $options[$oper->id] = htmlentities($oper->id.' - '.$oper->titulo.' - '.$oper->razaoSocial.' - '.$oper->cnpj);
                    }		
            		echo form_label('Operadora', 'idOper');
            		echo form_dropdown('idOper', $options, $idOper, 'id="idOper" class="form-control"');
                    echo '</div>'; 
      
                ?>
                    <div class="col-md-12 text-center"><strong>Análise financeira do projeto</strong></div>
                    <div class="col-md-12">
                       <table class="table">
                            <tr>
                                <th colspan="2">CAPEX DO PROJETO</th>
                                <th colspan="1">Sem Impostos</th>
                                <th colspan="1">&nbsp</th>
                            </tr>
                            <tr>
                                <td colspan="2">Mão de obra da empreiteira</td>
                                <td colspan="1">
                                <?php
                                $data = array('name'=>'mao_obra_empreiteira', 'value'=>'','id'=>'mao_obra_empreiteira', 'class'=>'dinheiro form-control');
       			                echo form_input($data);
                                ?>
                                </td>
                                <td colspan="1">&nbsp</td>
                            </tr>
                            <tr>
                                <td colspan="2">Aquisição de equipamentos</td>
                                <td colspan="1">
                                <?php
                                $data = array('name'=>'aquisicao_equipamento', 'value'=>'','id'=>'aquisicao_equipamento', 'class'=>'dinheiro form-control');
  			                    echo form_input($data);
                                ?>
                                </td>
                                <td colspan="1">&nbsp</td>
                            </tr>
                            <tr>
                                <td colspan="2">Sub Total</td>
                                <td colspan="1">
                                <?php
                                $data = array('name'=>'sub_total', 'value'=>'','id'=>'sub_total', 'class'=>'dinheiro form-control');
  			                    echo form_input($data);
                                ?>
                                </td>
                                <td colspan="1">&nbsp</td>
                            </tr>
                            <tr>
                                <th colspan="2">RECEITA DO PROJETO</th>
                                <th colspan="1">Sem Impostos</th>
                                <th colspan="1"><!--Com Impostos--></th>
                            </tr>
                            <tr>
                                <td colspan="2">Taxa de instalação</td>
                                <td colspan="1">
                                <?php
                                $data = array('name'=>'taxa_inst_sem_imposto', 'value'=>'','id'=>'taxa_inst_sem_imposto', 'class'=>'dinheiro form-control');
  			                    echo form_input($data);
                                ?>
                                </td>
                                <td colspan="1">
                                <?php
                                #$data = array('name'=>'taxa_inst_com_imposto', 'value'=>'','id'=>'taxa_inst_com_imposto', 'class'=>'dinheiro form-control');
       			                #echo form_input($data);
                                ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">Valor de mensalidade</td>
                                <td colspan="1">
                                <?php
                                $data = array('name'=>'mens_atual_sem_imposto', 'value'=>'','id'=>'mens_atual_sem_imposto', 'class'=>'dinheiro form-control');
       			                echo form_input($data);
                                ?>
                                </td>
                                <td colspan="1">
                                <?php
                                #$data = array('name'=>'mens_atual_com_imposto', 'value'=>'','id'=>'mens_atual_com_imposto', 'class'=>'dinheiro form-control');
       			                #echo form_input($data);
                                ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">Receita Total</td>
                                <td colspan="1">
                                <?php
                                $data = array('name'=>'receita_total_sem_imposto', 'value'=>'','id'=>'receita_total_sem_imposto', 'class'=>'dinheiro form-control');
       			                echo form_input($data);
                                ?>
                                </td>
                                <td colspan="1">
                                <?php
                                #$data = array('name'=>'receita_total_com_imposto', 'value'=>'','id'=>'receita_total_com_imposto', 'class'=>'dinheiro form-control');
       			                #echo form_input($data);
                                ?>
                                </td>
                            </tr>
                        </table>
                    </div> 
                </div>
                
                <div class="modal-footer">
                    <input type="hidden" id="valor_id" name="valor_id" />
                    <input type="hidden" id="email" name="email" value="nao" />
                    <?php if(in_array($perAnaliseFinanceira, $this->session->userdata('permissoes'))){ ?>
                    <button type="submit" sendEmail="sim" class="envia btn btn-primary pull-left">Enviar análise por e-mail</button>
                    <a id="printFichaFin" href="#" target="_blank" class="btn btn-default pull-left">Imprimir</a>
                    <?php } ?>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" sendEmail="nao" class="envia btn btn-primary">Salvar</button>
                </div>
                <?php
                echo form_close();
                ?>
        </div>
      </div>
    </div>
    <!-- FIM Modal Valores -->
<script type="text/javascript">

$("#idOper").change(function(){
    if($(this).val() != ''){ 
        $.ajax({
          type: "POST",
          url: '<?php echo base_url('tcom-operadora'); ?>/ajaxOper/dadosFaturamento',          
          data: {
            id: $(this).val()
          },
          dataType: "json",
          /*error: function(res) {
            $("#resMarcar").html('<span>Erro de execução</span>');
          },*/
          success: function(res) { 
            
            if(res){
                
                $("#idRegraReajuste").val('');
                $("#mes_reajuste").val('');
                $("#idIndiceReajuste").val('');
                $("#multa_porc").val('');
                $("#multa_nao_dev_equip").val('');
                
                if(res.idRegraReajuste === undefined){
                   res.idRegraReajuste = ''; 
                }
                if(res.mes_reajuste === undefined){
                   res.mes_reajuste = ''; 
                }
                if(res.idIndiceReajuste === undefined){
                   res.idIndiceReajuste = ''; 
                }
                if(res.multa_porc === undefined){
                   res.multa_porc = ''; 
                }
                if(res.multa_nao_dev_equip === undefined){
                   res.multa_nao_dev_equip = '';
                }
                $("#idRegraReajuste").val(res.idRegraReajuste);
                $("#mes_reajuste").val(res.mes_reajuste);
                $("#idIndiceReajuste").val(res.idIndiceReajuste);
                $("#multa_porc").val(res.multa_porc);
                $("#multa_nao_dev_equip").val(res.multa_nao_dev_equip);
            }else{
                $("#idRegraReajuste").val('');
                $("#mes_reajuste").val('');
                $("#idIndiceReajuste").val('');
                $("#multa_porc").val('');
                $("#multa_nao_dev_equip").val('');
            }
            
          }
        }); 
    }
});

$(document).ready(function(){   
$('.porcentagem').mask('000');
$('.doisDigitos').mask('00');
});

$("#limpa_data_pri_fatura").click(function(){
    $("#data_pri_fatura").val('');
});

$('#data_pri_fatura').monthpicker({
    monthNames: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
});

$("#grupo").change(function(){
   if($(this).val() != ''){
    $(this).carregaOperadora($(this).val());
   }else{
    
   } 
});

$.fn.carregaOperadora = function(varPai) {
    if(varPai != ''){
        $.ajax({
          type: "POST",
          url: '<?php echo base_url().$modulo; ?>/ajaxContrato/operadorasFaturamento',          
          data: {
            pai: varPai
          },
          dataType: "json",
          /*error: function(res) {
            $("#resMarcar").html('<span>Erro de execução</span>');
          },*/
          success: function(res) { 
            
            if(res.length > 0){
            
                content = '<option value=""></option>';
                
                $.each(res, function() {
                  
                  content += '<option value="'+ this.id +'">'+this.id+' - '+ this.titulo +' - '+ this.razaoSocial +' - '+this.cnpj+'</option>';
                  
                });
                
                $("#idOper").html('');
                $("#idOper").append(content);
            
            }else{
                $("#idOper").html('');
            }
            
            $("#idOper").val('<?php echo $idOper; ?>').trigger('change');
            
          }
        }); 
    }
}

</script>