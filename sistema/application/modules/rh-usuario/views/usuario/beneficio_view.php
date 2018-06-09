<?php

    echo '<div class="col-md-2">';
        echo form_label('Vale Transporte', 'vale_transporte_checkbox');
        $opc = ['checked'=>($VT['id_passagem']!=null?'TRUE':''),'name'=>'valeTransporte', 'class'=>'beneficios_opc', 'key'=>'transporte', 'style'=>'margin-left:50px', 'id'=>'vale_transporte_checkbox', 'value'=>'accept'];
        echo form_checkbox($opc);
    echo '</div>';
    
    echo '<div id="confBeneficio" class="col-md-3">';
        echo form_label('Opc. Alelo');
        $opc2 = [''=>''];
        if($elegivel_beneficio == 'N'){
            $opc2 = $opc2 + [$confAlelo[0]['id']=>$confAlelo[0]['descricao']];
        }else{
            foreach($confAlelo as $conf){
                $opc2 = $opc2 + [$conf['id']=>$conf['descricao']];
            }
        }
        echo form_dropdown('confBeneficio',$opc2,$cartoes['conf_alelo'],'id="opcAlelo" class="form-control"');
    echo '</div>';
    
    echo '<input name="cd_usuario" type="hidden" id="cd_usuario" value='.$cd_usuario.'>';

    echo '<div class="col-md-12 marginTop25" id="vale_transporte">';
        echo form_fieldset('Vale Transporte');
            echo form_open();
                echo '<div class="col-md-3">';
                    $opcaoNumero = ['name'=>'numero_vt', 'value'=> ($VT['numero_vt']!=null?$cartoes['numero_vt']:''), 'id'=>'num_transporte','class'=>'form-control valeTransporte'];
                    echo form_label('Numero do cartao','numeroCartao');
                    echo form_input($opcaoNumero);
                echo '</div>';

                echo '<div class="col-md-3">';
                    $opc = array(''=>'');
                    foreach($passagens as $passagem){
                        $opc[$passagem['id']] = ($passagem['passagens'] === null)?'Bilhete Unico':$passagem['passagens'] . ' passagens';
                    }
                    echo form_label('Passagens','passagens');
                    echo form_dropdown('id_passagem', $opc, $VT['id_passagem'], 'id="id_transporte" class="form-control numeroPassagem"');
                echo '</div>';
        echo form_fieldset_close();
    echo '</div>';
    
    echo '<div class="col-md-12 marginTop25" id="vale_alimentacao">';
        echo form_fieldset('Vale Alimentacao');
                echo '<div class="col-md-3">';
                    $opcaoNumero = array('name'=>'numero_va', 'value'=> ($cartoes['numero_va']!=null?$cartoes['numero_va']:''), 'id' => 'num_alimentacao', 'class'=>'form-control vale');
                    echo form_label('Numero do cartao','numeroCartao');
                    echo form_input($opcaoNumero);
                echo '</div>';
        echo form_fieldset_close();
    echo '</div>';
    
    echo '<div class="col-md-12 marginTop25" id="vale_refeicao">';
        echo form_fieldset('Vale Refeicao');
                echo '<div class="col-md-3">';
                    $opcaoNumero = array('name'=>'numero_vr', 'value'=> ($cartoes['numero_vr']!=null?$cartoes['numero_vr']:''), 'id'=>'num_refeicao', 'class'=>'form-control vale');
                    echo form_label('Numero do cartao','numeroCartao');
                    echo form_input($opcaoNumero);
                echo '</div>';
        echo form_fieldset_close();
    echo '</div>';
    echo form_close();
    
    echo '<div class="row"></div>';
    echo '<br><br>';

    
?>

<script type="text/javascript" src="<?php echo base_url("assets/js/rh-beneficio/compraAlelo.js") ?>"></script>