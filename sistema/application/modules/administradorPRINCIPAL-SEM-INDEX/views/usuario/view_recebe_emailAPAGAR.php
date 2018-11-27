<?php
echo "<script type='text/javascript' src='".base_url('assets/js/jquery.blockui/jquery.block.ui.js')."'></script>";
?>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.shiftcheckbox.js") ?>"></script>
<!--<script type="text/javascript" src="http://cdn.jsdelivr.net/qtip2/2.2.1/jquery.qtip.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url("assets/js/tooltip/css/tooltipster.css") ?>" />
<script type="text/javascript" src="<?php echo base_url("assets/js/tooltip/js/jquery.tooltipster.min.js") ?>"></script>-->
  <style>
  .ui-tooltip, .arrow:after {
    /*background: black;*/
    border: 2px solid;
  }
  .ui-tooltip {
    padding: 10px 20px;
    /*color: white;*/
    border-radius: 20px;
    font: bold 14px "Helvetica Neue", Sans-Serif;
    text-transform: uppercase;
    box-shadow: 0 0 7px black;
  }
  .arrow {
    width: 70px;
    height: 16px;
    overflow: hidden;
    position: absolute;
    left: 50%;
    margin-left: -35px;
    bottom: -16px;
  }
  .arrow.top {
    top: -16px;
    bottom: auto;
  }
  .arrow.left {
    left: 20%;
  }
  .arrow:after {
    content: "";
    position: absolute;
    left: 20px;
    top: -20px;
    width: 25px;
    height: 25px;
    box-shadow: 6px 5px 9px -9px black;
    -webkit-transform: rotate(45deg);
    -ms-transform: rotate(45deg);
    transform: rotate(45deg);
  }
  .arrow.top:after {
    bottom: -20px;
    top: auto;
  }
  </style>

            <!--<div class="col-md-9 col-sm-8"> USADO PARA SIDEBAR -->
            <div class="col-lg-12">
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a>
                    </li>
                    <li class="active">Recebe e-mail</li>
                </ol>
                <div id="divMain">
                    <?php
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'frm');
                    	echo form_open('usuario/salvarRecebeEmail',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
                    		echo form_fieldset("Quem recebe e-mail", $attributes);
                    		  
                                echo '<div class="row">';
                                    echo '<div class="alert alert-info" role="alert">
                                            <strong>Dicas e informa&ccedil;&otilde;es:</strong><br>
                                            - <u>Marcar / desmarcar grupo</u> -> Marque o checkbox inicial, pressione a tecla SHIFT e segure, marque o checkbox final.<br>
                                            &nbsp;&nbsp;Obs: S&oacute; funciona para a primeira fileira de checkbox.<br>
                                            <!--- <u>Checkbox de prioridade</u> -> No fluxo normal o sistema disparar&aacute; e-mail somente para os usu&aacute;rios da localidade da a&ccedil;&atilde;o.<br>
                                            &nbsp;&nbsp;Com a marca&ccedil;&atilde;o do checkbox prioridade ocorre o seguinte:<br>
                                            &nbsp;&nbsp;Independente da localidade da a&ccedil;&atilde;o o usu&aacute;rio que estiver com o checkbox prioridade marcado receber&aacute; a notifica&ccedil;&atilde;o por e-mail.-->
                                        </div>';
                                    echo '<div class="col-md-8">';
                                    $options = array(''=>'');	
                                    foreach($tiposEmail as $tEmail){
                                        $options[$tEmail->id] = $tEmail->nome;
                                    }	
                            		echo form_label('A&ccedil;&atilde;o que dispara e-mail', 'tipo_email');
                            		echo form_dropdown('tipo_email', $options, $this->session->flashdata('tipo_email'), 'id="tipo_email" class="form-control"');
                                    echo '</div>';
                                    echo '<div class="col-md-4">';
                                    $options = array(''=>'', 'todos' => 'TODOS');
                                    foreach($unidade as $uni){
                                        $options[$uni->cd_unidade] = $uni->nome;                        
                                    }		                  
                            		echo form_label('Habilitar / Desabilitar para permissor', 'permissor');                     
                            		echo form_dropdown('permissor', $options, false, 'id="permissor" class="form-control"');
                                    echo '</div>';
                                echo '</div>';
                                
                                echo '<div class="row">';
                                    echo '<div class="col-md-12"><strong>SELECIONE UM DOS FILTROS ABAIXO PARA FILTRAR E HABILITAR / DESABILITAR OS USU&Aacute;RIOS PARA <span id="infoInstrucao"><u>TODOS OS PERMISSORES</u></span></strong></div>';
                                    echo '<div class="col-md-3">';
                                    echo form_label('Nome ou e-mail', 'nome_email');
                        			$data = array('name'=>'nome_email', 'value'=>'','id'=>'nome_email', 'placeholder'=>'Digite o nome ou e-mail', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array(''=>'', 'todos' => 'TODOS');
                                    foreach($departamento as $dep){
                                        $options[$dep->cd_departamento] = $dep->nome_departamento;                        
                                    }		                  
                            		echo form_label('Departamento', 'departamento');                     
                            		echo form_dropdown('departamento', $options, false, 'id="departamento" class="form-control"');
                                    echo '</div>';   
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array(''=>'', 'todos' => 'TODOS');
                                    foreach($unidade as $uni){
                                        $options[$uni->cd_unidade] = $uni->nome;                        
                                    }		                  
                            		echo form_label('Unidade', 'unidade');                     
                            		echo form_dropdown('unidade', $options, false, 'id="unidade" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array(''=>'', 'todos' => 'TODOS');
                                    foreach($funcao as $fun){
                                        $options[$fun->cd_cargo] = $fun->nome;                        
                                    }		                  
                            		echo form_label('Cargo', 'funcao');                     
                            		echo form_dropdown('funcao', $options, false, 'id="funcao" class="form-control"');
                                    echo '</div>';                              
                                echo '</div>';                      
                                /*
                                $urlOrigem = explode('/',$_SERVER['HTTP_REFERER']); 
                                if(in_array(end($urlOrigem), $redirect)){
                                    echo form_hidden('redirect', $_SERVER['HTTP_REFERER']);
                                }
                                */                                
                                echo '<div class="row">&nbsp</div>';
                                echo '<div id="mostraTabela" class="row"></div>';                                         
                    
                    ?>        
                </div>
<?php
                                echo '<div class="actions btnFixo">';
                                echo form_submit("btn",utf8_encode("Salvar"), 'class="btn btn-primary pull-right"');
                                echo '</div>';     
                    		echo form_fieldset_close();
                    	echo form_close(); 
?>                                                
            </div>
    
<script type="text/javascript">

$.fn.carregando = function() {
    $(document).ajaxStart(
        $.blockUI({ 
        message:  '<h1>Carregando dados...</h1>',
        css: { 
        	border: 'none', 
        	padding: '15px', 
        	backgroundColor: '#000', 
        	'-webkit-border-radius': '10px', 
        	'-moz-border-radius': '10px', 
        	opacity: .5, 
        	color: '#fff' 
        	} 
        })
    ); 
    
    //setTimeout($.unblockUI, tempo);   
    //$(document).ajaxStart($.blockUI);
    
}

$("#tipo_email").change(function(){
    $(this).infoHabilitacao();
   if($(this).val() != '' && $("#permissor").val() != ''){
    $("#nome_email").val('');
    $("#departamento").val('');
    $("#unidade").val('');
    $("#funcao").val('');
    $("#mostraTabela").html('');
    $(this).parent().parent().next().show();
    $("#nome_email").show();
    $("#departamento").show();
    $("#unidade").show();
    $("#funcao").show();
    $(this).carregando();
    $(this).carregaUsuarios();
   }else{
    $(this).parent().parent().next().hide();
    $("#nome_email").val('');
    $("#departamento").val('');
    $("#unidade").val('');
    $("#funcao").val('');
    $("#mostraTabela").html('');
   }
   $(document).ajaxStop($.unblockUI);

});

$("#nome_email").keyup(function(){
    if($(this).val() != '' && $(this).val().length >= 3){
        $(this).carregaUsuarios();
    }else{
        $("#mostraTabela").html('');
    }
    
    if($(this).val().length == 0){
        $(this).carregaUsuarios();
    }
});

$("#departamento,#unidade,#funcao").change(function(){
    
    $(this).carregando();
    
    if($(this).val() != ''){
        $(this).carregaUsuarios();
    }else{
        $("#mostraTabela").html('');
        $(this).carregaUsuarios();
    }
    
    $(document).ajaxStop($.unblockUI);
});
/*
$(function() {
    $( document ).tooltip();
});
*/

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

$("#permissor").change(function(){
    
    if($(this).val() != '' && $("#tipo_email").val() != ''){
        $(this).parent().parent().next().show();
        $(this).carregando();
        $(this).carregaUsuarios();
        $(document).ajaxStop($.unblockUI);
    }else{
        $(this).parent().parent().next().hide();
        $("#mostraTabela").html('');
    }
    //$(this).infoHabilitacao();
    
});

$.fn.carregaUsuarios = function(){
    
    var tabela = '';
    if($("#tipo_email").val() != '' && $("#permissor").val() != ''){
        $.ajax({
          type: "POST",
          url: '<?php echo base_url(); ?>administrador/ajaxAdmin/usuariosRecebeEmail',
          data: {
            tipo_email: $("#tipo_email").val(),
            permissor: $("#permissor").val(),
            nome_email: $("#nome_email").val(),
            departamento: $("#departamento").val(),
            unidade: $("#unidade").val(),
            funcao: $("#funcao").val()
          },
          dataType: "json",
          /*error: function(res) {
            $("#resMarcar").html('<span>Erro de execução</span>');
          },*/
          success: function(res) {
            
            if(res.length != 0){
                
                var i = 0;
                tabela +='<table class="table">';
                tabela +='<thead>';
                tabela +='<tr><th colspan="5">Usu&aacute;rios encontrados</th></tr>';
                tabela +='<tr>';
                tabela += '<th><input id="todos" type="checkbox" /><span id="infoHab" class="glyphicon glyphicon-question-sign" aria-hidden="true"></span></th>';
                //tabela += '<th class="campoPrioridade" title="O usu&aacute;rio recebe um e-mail independente da localidadade da a&ccedil;&atilde;o">Prioridade<span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span></th>';
                tabela += '<th>Nome</th>';
                tabela += '<th>E-mail</th>';
                tabela += '<th>Cargo</th>';
                tabela +='</tr>';
                tabela +='</thead>';
                tabela +='<tbody>';
    
                $.each(res, function(){
                   
                   if(this.email_usuario === null){
                    var semEmail = 'alert alert-danger';
                    var email = '<input type="text" name="email['+this.cd_usuario+']" class="form-control" /><span><?php echo $dominioEmail;?></spa>';
                   }else{
                    var email = this.email_usuario;
                    var semEmail = '';
                   }
                   
                   if(this.cargo === null){
                    var cargo = '-';
                   }else{
                    var cargo = this.cargo;
                   }
    
                   if(this.recebe !== null){
                    var recebe = 'checked';
                   }else{
                    var recebe = '';
                   }
                   
                   if(this.prioridade !== null){
                    var prioridade = 'checked';
                   }else{
                    var prioridade = '';
                   }
    
                   tabela +='<tr class="'+semEmail+'">';
                   tabela +='<td>';
                   tabela +='<input type="hidden" name="todosUsuarios[]" value="'+this.cd_usuario+'"/>';
                   tabela +='<input class="marcados" '+recebe+' type="checkbox" name="marcados['+i+']" value="'+this.cd_usuario+'" />';
                   tabela +='</td>';
                   //tabela +='<td class="campoPrioridade"><input class="prioridade" '+prioridade+' type="checkbox" name="prioridade['+i+']" value="'+this.cd_usuario+'" /></td>';
                   tabela +='<td title="'+this.habilitados+'">'+this.nome_usuario+'</td>';
                   tabela +='<td class="semEmail">'+email+'</td>';
                   tabela +='<td>'+cargo+'</td>';
                   tabela +='</tr>';
                   i++;
                });
                
                tabela +='</tbody>';
                tabela +='</table>';
                $("#mostraTabela").html(tabela);
                $(this).infoHabilitacao();
    
            }else{
                
                $("#mostraTabela").html('<div class="col-md-12"><strong>Nada encontrado</strong></div>');
            }
            
            
            
            $("#todos").change(function(){ 
                if($(this).prop('checked') == true){
                    $("input.marcados:checkbox").prop('checked', true);
                }else{
                    $("input.marcados:checkbox").prop('checked', false);//.parent().next().children().prop('checked', false);
                } 
            });
            /*
            $(".marcados").change(function(){
                if($(this).prop('checked') == false){
                    $(this).parent().next().children().prop('checked', false);
                }
            });
            
            $(".prioridade").change(function(){
                if($(this).prop('checked') == true){
                    $(this).parent().prev().children().prop('checked', true);
                }
            });
            */
            $('#mostraTabela input.marcados:checkbox').shiftcheckbox();
          }
        });
    }
};

$.fn.infoHabilitacao = function(){
    if($('#permissor :selected').text() == 'TODOS'){
        var msg = '<?php echo utf8_encode('Receberá e-mail para a ação de todos os permissores');?>';
        var msg2 = '<u>TODOS OS PERMISSORES</u>';
    }else{
        var msg = '<?php echo utf8_encode('Receberá e-mail somente para ação de');?> '+$('#permissor :selected').text().toLowerCase();
        var msg2 = '<u>'+$('#permissor :selected').text().toUpperCase()+'</u>';
    }
    $("#infoHab").attr('title',msg);
    $("#infoInstrucao").html(msg2);
};

$(document).ready(function(){
    $(this).carregaUsuarios();
    // Div que contém os inputs de codição
    //$('[data-toggle="tooltip"]').tooltip(); 
    $(".row:eq(2)").hide();
});

</script>