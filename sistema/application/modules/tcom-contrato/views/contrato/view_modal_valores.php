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
                    echo form_label('Data 1ª fatura', 'data_pri_fatura');
        			$data = array('name'=>'data_pri_fatura', 'value'=>'','id'=>'data_pri_fatura', 'class'=>'data form-control');
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
                                <th colspan="1">Com Impostos</th>
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
                                $data = array('name'=>'taxa_inst_com_imposto', 'value'=>'','id'=>'taxa_inst_com_imposto', 'class'=>'dinheiro form-control');
       			                echo form_input($data);
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
                                $data = array('name'=>'mens_atual_com_imposto', 'value'=>'','id'=>'mens_atual_com_imposto', 'class'=>'dinheiro form-control');
       			                echo form_input($data);
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
                                $data = array('name'=>'receita_total_com_imposto', 'value'=>'','id'=>'receita_total_com_imposto', 'class'=>'dinheiro form-control');
       			                echo form_input($data);
                                ?>
                                </td>
                            </tr>
                        </table>
                    </div> 
                </div>
                
                <div class="modal-footer">
                    <input type="hidden" id="valor_id" name="valor_id" />
                    <input type="hidden" id="email" name="email" value="nao" />
                    <button type="submit" sendEmail="sim" class="envia btn btn-primary pull-left">Enviar análise por e-mail</button>
                    <a id="printFichaFin" href="#" target="_blank" class="btn btn-default pull-left">Imprimir</a>
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