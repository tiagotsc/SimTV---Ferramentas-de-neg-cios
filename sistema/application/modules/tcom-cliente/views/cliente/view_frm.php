<?php
#echo "<script type='text/javascript' src='".base_url('assets/js/jquery.blockui/jquery.block.ui.js')."'></script>";
?>

<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>

            <div class="col-md-10 col-sm-9">
            <!--<div class="col-lg-12">-->
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a>
                    </li>
                    <li class="active">Ficha node</li>
                </ol>
                <div id="divMain">
                    <div id="feedback" class="alert alert-warning" role="alert">
                        <strong>Pesquise o endere&ccedil;o correto (Rua, avenida, etc) no site dos Correios <a target="_blank" href="http://www.correios.com.br">Correios</a></strong>
                    </div>
                    <?php   
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'frm-salvar');
                    	echo form_open('tcom-node/node/salvar',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
             
                    		echo form_fieldset("Ficha node<a href='".base_url('tcom-node/node/pesq')."' class='linkDireita'><span class='glyphicon glyphicon-arrow-left'></span>&nbspVoltar Pesquisar</a>", $attributes);
                    		
                                echo '<div class="row">';
                                    echo '<div class="col-md-3">';
                                    $options = array('' => '');		
                            		foreach($permissor as $per){
                      		            
                                        if($per->permissor != ''){                                        
                          			       $options[$per->cd_unidade] = $per->permissor.' - '.htmlentities($per->nome);
                                        }                                      
                            		}	
                            		echo form_label('Permissor<span class="obrigatorio">*</span>', 'cd_unidade');
                            		echo form_dropdown('cd_unidade', $options, $cd_unidade, 'id="cd_unidade" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div id="node-nome" class="col-md-3">';
                                    $options = array('' => '');		
                            		echo form_label('Node<span class="obrigatorio">*</span>', 'node');
                            		echo form_dropdown('node', $options, '', 'id="node" class="insertNode form-control"');
                                    
                                    #echo form_label('Node2<span class="obrigatorio">*</span>', 'node');
                        			$data = array('name'=>'node', 'value'=>$node.'-'.$descricao,'id'=>'node', 'readonly' => 'readonly', 'placeholder'=>'Informe a dist&acirc;ncia', 'class'=>'updateNode form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                
                                    echo '<div class="col-md-4">';
                                    echo form_label('Dist&acirc;ncia<span class="obrigatorio">*</span>', 'distancia');
                        			$data = array('name'=>'distancia', 'value'=>$distancia,'id'=>'distancia', 'placeholder'=>'Informe a dist&acirc;ncia', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    echo form_label('CEP', 'cep');
                        			$data = array('name'=>'cep', 'value'=>$cep,'id'=>'cep', 'placeholder'=>'Informe o CEP', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-8">';
                                    echo form_label('Endere&ccedil;o', 'endereco');
                        			$data = array('name'=>'endereco', 'value'=>$endereco,'id'=>'endereco', 'placeholder'=>'Informe o endere&ccedil;o', 'class'=>'bloq form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    echo form_label('Cidade', 'cidade');
                        			$data = array('name'=>'cidade', 'value'=>$cidade,'id'=>'cidade', 'placeholder'=>'Informe a cidade', 'class'=>'bloq form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    echo '<div id="maskProtecaoUf"></div>';
                                    $options = array('' => '');		
                                    foreach($estado as $est){
                                        $options[$est->cd_estado] = $est->sigla_estado;
                                    }
                            		echo form_label('Estado', 'cd_estado');
                            		echo form_dropdown('cd_estado', $options, $cd_estado, 'readonly="readonly" id="cd_estado" class="bloq form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    echo form_label('Bairro', 'bairro');
                        			$data = array('name'=>'bairro', 'value'=>$bairro,'id'=>'bairro', 'placeholder'=>'Informe o bairro', 'class'=>'bloq form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    /*
                                    echo '<div class="col-md-2">';
                                    echo form_label('N&uacute;mero', 'numero');
                        			$data = array('name'=>'numero', 'value'=>$numero,'id'=>'numero', 'placeholder'=>'Informe o n&uacute;mero', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    */
                                    echo '<div class="col-md-8">';
                                    echo form_label('Complemento', 'complemento');
                        			$data = array('name'=>'complemento', 'value'=>$complemento,'id'=>'bairro', 'placeholder'=>'Informe o complemento', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';                                                                        
                                    
                                    echo '<div class="col-md-2">';
                                    echo form_label('Coordenada X', 'coordx');
                        			$data = array('name'=>'coordx', 'value'=>$coordx,'id'=>'coordx', 'placeholder'=>'Informe o coordenada', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    echo form_label('Coordenada Y', 'coordy');
                        			$data = array('name'=>'coordy', 'value'=>$coordy,'id'=>'coordy', 'placeholder'=>'Informe o coordenada', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';       
                                    
                                    echo '<div class="col-md-2">';
                                    $options = array('' => '', 'hub' => 'Hub', 'headend' => 'Headend');		
                            		echo form_label('Pop<span class="obrigatorio">*</span>', 'pop');
                            		echo form_dropdown('pop', $options, $pop, 'id="pop" class="form-control"');
                                    echo '</div>';  
                                    
                                    echo '<div class="col-md-2">';
                                    $options = array('' => '', 'sim' => 'Sim', 'nao' => 'N&atilde;o', 'parcial' => 'Parcial');		
                            		echo form_label('CM', 'cm');
                            		echo form_dropdown('cm', $options, $cm, 'id="cm" class="form-control"');
                                    echo '</div>';  
                                    
                                    echo '<div class="col-md-2">';
                                    $options = array('' => '', 'sim' => 'Sim', 'nao' => 'N&atilde;o');		
                            		echo form_label('TV', 'tv');
                            		echo form_dropdown('tv', $options, $tv, 'id="tv" class="form-control"');
                                    echo '</div>';      
                                    echo '<div class="col-md-6">';
                                        echo '<div><strong>C&eacute;lulas</strong></div>';
                                        echo '<div>';
                                                foreach($celulas as $cel){
                                                    
                                                    $checked = (in_array($cel, $celulasSelecionadas))? 'checked': '';
                                                    
                                                    echo '<label class="checkboxFloatLeft">'.$cel.'<input type="checkbox" name="celula[]" value="'.$cel.'" '.$checked.'> </label>';
                                                }
                                        echo '</div>';
                                    echo '</div>'; 
                       
                                    
                                echo '</div>';
                                                              
                                echo '<div class="actions">';
                                
                                echo form_hidden('id', $id);
                                
                                echo form_submit("btn","Salvar", 'class="btn btn-primary pull-right"');
                                echo '</div>';   
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
                        
                    ?>       
                </div>
            </div>
    
<script type="text/javascript">

// Busca o endere?o de acordo com o CEP
$("#cep").keyup(function(){
    
    
    if($(this).val().length == 9){ // CEP Completo
        $.ajax({
              type: "GET",
              //url: '<?php echo base_url(); ?>ajax/pegaEndereco',
              url: wsBuscaCEP+$(this).val().replace('-',''),              
              data: {
                cep: $(this).val().replace('-','')
              },
              dataType: "json",
              /*error: function(res) {
                $("#resMarcar").html('<span>Erro de execu??o</span>');
              },*/
              success: function(res) {
                $("#endereco").val(res.logradouro);
                $("#bairro").val(res.bairro);
                $("#cidade").val(res.cidade);
                
                $('#cd_estado option').each(function(){
                   var item = $(this).text();
                   if(item == res.estado){
                      $(this).prop('selected',true);
                   }
                });
                
                if(res == 0){ // N?o achou endere?o
                    //$('.bloq').prop('readonly', false);
                    //$("#maskProtecaoUf").css('display', 'none'); // Desabilita bloqueio de UF
                    $("#feedback").css('display', 'block');
                }else{ // Achou endere?o
                   $('.bloq').prop('readonly', true); 
                   //$("#maskProtecaoUf").css('display', 'block'); // Habilita bloqueio de UF
                   $("#feedback").css('display', 'none');
                }
                
              }
            }); 
    }else{ // CEP Incompleto
        $("#endereco").val('');
        $("#bairro").val('');
        $("#cidade").val('');
        $("#cd_estado option").prop("selected", false);
        $('.bloq').prop('readonly', true);
    }
    
}); 
/*
function dump(obj) {
    var out = '';
    for (var i in obj) {
        out += i + ": " + obj[i] + "\n";
    }
    alert(out);
}
*/



$(document).ready(function(){ 
    
    $('.bloq').prop('readonly', true);
    $('#cep').mask('00000-000');
    
    <?php if($id){ ?>
    $("#node-nome").show(); 
    $(".insertNode").hide().prop('disable', true);  
    $(".updateNode").show().prop('disable', false); 
     
    <?php }else{ ?>
    $("#node-nome").hide(); 
    $(".insertNode").show().prop('disable', false);    
    $(".updateNode").hide().prop('disable', true); 
    <?php } ?>
    
    $(".actions").click(function() {
        $('#aguarde').css({display:"block"});
    });
    
    $("#cd_unidade").change(function(){
        
        if($(this).val() != ''){
            
            $(".insertNode").show().prop('disable', false);    
            $(".updateNode").remove(); 
            
            var perm = $("#cd_unidade option:selected").text().split(" - ");
            $("#node-nome").show();
            $("#node").html('<option value="">AGUARDE...</option>');
                
            $.ajax({
              type: "POST",
              url: '<?php echo base_url(); ?>tcom-node/ajaxNode/carregaNodesSiga',
              data: {
                permissor: perm[0],
              },
              dataType: "json",
              /*error: function(res) {
                $("#resMarcar").html('<span>Erro de execu??o</span>');
              },*/
              success: function(res) {
                
                if(res.length > 0){
                
                content = '<option value=""></option>';
                
                $.each(res, function() {
                  
                  content += '<option value="'+ this.MANNRO +'-'+ this.MANDSC +'">'+ this.MANNRO +' - '+ this.MANDSC +'</option>';
                  
                });
                
                $("#node").html('');
                $("#node").append(content);
                
                }else{
                    $("#node").html('');
                    $("#node-nome").hide();
                }
                
              }
            });
            
        }else{
            
            $("#node-nome").hide();
            $("#node").html('');
            
        }
        
    });
    
    // Valida o formul?rio
	$("#frm-salvar").validate({
		debug: false,
		rules: {
            cd_unidade: {
                required: true
            },
			node: {
                required: true
            },
            distancia: {
                required: true
			},
            pop: {
                required: true
			}
		},
		messages: {
            cd_unidade: {
                required: "Selecione o permissor."
            },
			node: {
                required: "Selecione o node."
            },
            distancia: {
                required: "Informe a dist&acirc;ncia."
            },
            pop: {
                required: "Selecione o POP."
            }
	   }
   });
    
});


</script>