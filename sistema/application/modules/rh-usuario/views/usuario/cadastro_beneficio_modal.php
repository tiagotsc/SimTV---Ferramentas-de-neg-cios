<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="modal fade" id="editaBeneficios" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span  class ="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
<!--                <h4 class="modal-title">Cadastro de beneficios</h4>-->
            </div>
            <div class="modal-body">
                <div class="row">
                    <!--- CORPO--->

                    <?php
                        
                        if($vale_transporte == NULL){
                            
                            $data = array('class'=>'pure-form','id'=>'registraVale');
                            echo '<div id="" class="col-md-12">';
                                echo form_fieldset("Cadastro vale transporte");
                                        echo form_open('rh-beneficio/beneficio/cadastraBeneficio',$data);

                                            echo form_hidden('cd_usuario', $cd_usuario);

                                            echo '<div class="col-md-4">';
                                                echo form_label('Numero do cartao','numeroCartao');
                                                $op = array('name'=>'numero_vale_transporte', 'value'=> $vale_transporte['numero_vale_transporte'], 'placeholder'=>'Numero do cartao', 'class'=>'form-control valeTransporte');
                                                echo form_input($op);
                                            echo '</div>';

                                            echo '<div class="col-md-3">';
                                                $opc = array(''=>'');
                                                foreach($passagens as $passagem){
                                                    $opc[$passagem['id']] = ($passagem['passagens'] === null)?'Bilhete Unico':$passagem['passagens'] . ' passagens';
                                                }
                                                echo form_label('Passagens','passagens');
                                                echo form_dropdown('id_passagem', $opc, $vale_transporte['id_passagem'], 'class="form-control numeroPassagem"');
                                            echo '</div>';

                                            echo '<div class="col-md-3">';
                                                echo form_label('Valor','valor');
                                                echo'<input name="valorPassagem" id="valorPassagem" class="form-control" readonly>';
                                            echo '</div>';
                                            
                                            echo '<a href="#"><span style="margin-top:33px" class="glyphicon glyphicon-floppy-disk pull-right salvaVale"></span></a>';
                                            
                                        echo form_close();
                                echo form_fieldset_close();
                            echo "</div>";
                            
                        }else{
                            echo '<div ="row">';
                                echo '<div class="col-md-12" id="editarValeTransporte">';
                                    echo form_fieldset('Edicao&nbsp<a href="#"><span id="btn_cadastro" style="color: blue" class="glyphicon glyphicon-floppy-disk salvaVale"></span></a>');
                                        $data = array('id'=>'registraVale');
                                        echo form_open('rh-beneficio/beneficio/cadastraBeneficio',$data);

                                            echo form_hidden('cd_usuario', $cd_usuario);

                                                echo '<div class="col-md-4">';
                                                    echo form_label('Numero do cartao','numeroCartao');
                                                    $op = array('name'=>'numero_vale_transporte', 'value'=> $vale_transporte['numero_vale_transporte'], 'placeholder'=>'Numero do cartao', 'class'=>'form-control valeTransporte');
                                                    echo form_input($op);
                                                echo '</div>';

                                                echo '<div class="col-md-4">';
                                                    $opc = array(''=>'');
                                                    foreach($passagens as $passagem){
                                                        $opc[$passagem['id']] = ($passagem['passagens'] === null)?'Bilhete Unico':$passagem['passagens'] . ' passagens';
                                                    }
                                                    echo form_label('Passagens','passagens');
                                                    echo form_dropdown('id_passagem', $opc, $vale_transporte['id_passagem'], 'class="form-control numeroPassagem"');
                                                echo '</div>';

                                                echo '<div class="col-md-2">';
                                                    echo form_label('Valor','valor');
                                                    echo'<input name="valorPassagem" id="valorPassagem" value="'.$vale_transporte['valor'].'" class="form-control" readonly>';
                                                echo '</div>';

                                                echo '<div class="col-md-1">';
                                                    echo '<a href="#"><span id="btn_deleta_vale" style="margin-top:33px; color:black;" class="glyphicon glyphicon-trash pull-right"></span></a>';
                                                echo '</div>';
                                        echo form_close();
                                    echo form_fieldset_close();
                                echo '</div>';
                            echo '</div>';
                            
                            echo '<div class="col-md-12 marginTop25">';
                                echo form_fieldset('Vale transporte&nbsp<a href="#"><span id="btn_editar" class="glyphicon glyphicon-pencil"></span></a>');
                                    echo form_open();
                                        
                                        echo form_hidden('cd_usuario', $cd_usuario);

                                        echo '<div class="col-md-4">';
                                            $opcaoNumero = array('name'=>'numero_vale_transporte', 'value'=> $vale_transporte['numero_vale_transporte'], 'class'=>'form-control valeTransporte', 'readonly' => 'readonly');
                                            echo form_label('Numero do cartao','numeroCartao');
//                                            echo '<input name="valor_passagem" value="pao" class="form-control" readonly>';
                                            echo form_input($opcaoNumero);
                                        echo '</div>';
                                        
                                        echo '<div class="col-md-4">';
                                            $opcaoPassagem = array('name' => 'valor_passagem', 'value' => $vale_transporte['passagens'].' passagens', 'class'=>'form-control', 'readonly' => 'readonly');
                                            echo form_label('Passagens','passagens');
//                                            echo '<input name="valor_passagem" value="pao" class="form-control" readonly>';
                                            echo form_input($opcaoPassagem);
                                        echo '</div>';
                                    echo form_close();
                                echo form_fieldset_close();
                            echo '</div>';
                            
                        }
                    ?>

                    <!--- CORPO--->
                </div>    
            </div>
            
            <div class="modal-footer">
                <!--<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>-->
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    
    $(document).ready(function(){
        $('.valeTransporte').mask('00.00.00000000-0');
        $("#editarValeTransporte").hide();
    });
    
    $("#btn_editar").click(function (){
        event.preventDefault();
        $("#editarValeTransporte").toggle("slow");
    });
    
    $(".salvaVale").click(function (){
        event.preventDefault();
        $("#registraVale").attr('action','/sistema/rh-beneficio/beneficio/cadastraBeneficio');
        $("#registraVale").submit();
    });
    
    $("#btn_deleta_vale").click(function (){
        event.preventDefault();
        $("#registraVale").attr('action','/sistema/rh-beneficio/beneficio/deletaBeneficio');
        $("#registraVale").submit();
    });
    
    $('.numeroPassagem').change(function(){
        $.ajax({
            type:"POST",
            url: '<?php echo base_url(); ?>rh-beneficio/ajaxBeneficio/passagem',
            data: {
                id_passagem: $(this).val(),
            },
            dataType: "json",
            
            success: function (res){
                
                var input = res['passagem'][0].valor;
                $('#valorPassagem').val(input);
            }
        });
    });
    
    
    
    
    
    
</script>