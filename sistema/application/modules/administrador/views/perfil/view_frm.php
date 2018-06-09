<link rel="stylesheet" href="<?php echo base_url("assets/js/jstree/themes/default/style.min.css") ?>" />
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.shiftcheckbox.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jstree/jstree.min.js") ?>"></script>

            <!--<div class="col-md-9 col-sm-8"> USADO PARA SIDEBAR -->
            <div class="col-lg-12">
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a>
                    </li>
                    <li class="active">Criar/Editar perfil</li>
                </ol>
                <div class="row" id="divMain">
                    <?php
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'salvar_perfil');
                    	echo form_open('perfil/salvar',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
             
                    		echo form_fieldset("Cadastrar perfil<a href='".base_url('perfil/perfis')."' class='linkDireita'><span class='glyphicon glyphicon-arrow-left'></span>&nbspVoltar Pesquisa</a>", $attributes);
                    		
                                echo '<div class="row">';
                                
                                    echo '<div class="col-md-8">';
                                    echo form_label('Nome do perfil', 'nome_perfil');
                        			$data = array('name'=>'nome_perfil', 'value'=>$nome_perfil,'id'=>'nome_perfil', 'placeholder'=>'Digite o nome', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    $options = array('A' => 'Ativo', 'I' => 'Inativo');		
                            		echo form_label('Status<span class="obrigatorio">*</span>', 'status_perfil');
                            		echo form_dropdown('status_perfil', $options, $status_perfil, 'id="status_perfil" class="form-control"');
                                    echo '</div>';
                                
                                echo '</div>';
                                                                
                                echo '<div style="position:fixed; top: 80%; right: 7.7%;z-index: 99999999" class="actions">';
                                echo form_submit("btn_cadastro","Salvar", 'class="btn btn-primary pull-right"');
                                echo '</div>';

                    ?> 
                    <div>
                        <label for="buscar">Digite a permissão que desejar encontrar</label>
                        <input type="text" id="buscar" name="buscar" class="form-control" />
                    </div>
                    <div id="divLinkAchados">
                        <a id="linkAchados" tipo="prox" href="#" linkCorrent="0" linkFim=""><strong>PRÓXIMO</strong></a>
                    </div>
                    <div id="localizado">
                    
                    </div>
                    <p class="accordion-expand-holder">
                        <a class="accordion-expand-all" href="#">EXPANDIR TODOS</a>
                    </p>
                    <p>
                        <input id="todos" type="checkbox" /> Marcar / desmarcar todos
                    </p>
                    <div id="accordion" class="ui-accordion ui-widget ui-helper-reset">
                    <?php echo $permissoes; ?>
                    </div>
                    <?php 
                            echo form_hidden('cd_perfil', $cd_perfil);
                    		echo form_fieldset_close();
                    	echo form_close(); 
                    ?>
                </div>
            </div>
    
<script type="text/javascript">

var todasPos = [];

$("#linkAchados").click(function(event){ 
    event.preventDefault();
    var posCorrente = 0;

    if(parseInt($(this).attr("linkCorrent")) <= parseInt($(this).attr("linkFim"))){
        posCorrente = parseInt($(this).attr("linkCorrent"))+1;
    }
    
    $('body').scrollTop(todasPos[posCorrente]);
    $(this).attr("linkCorrent", posCorrente);
    
});

$("#todos").click(function(){
    if($(this).prop('checked') == true){
        $('input:checkbox').prop('checked', true);
    }else{
        $('input:checkbox').prop('checked', false);
    }
})

$("#buscar").keyup(function(){
    todasPos = [];
    $("#accordion li").css("background-color","#eeeeee"); 
    if($(this).val().length >= 3){ 
        $("#accordion li:contains("+$(this).val().toUpperCase()+")").css("background-color","#CD5C5C");
        
        $(".ui-accordion-content").hide();
        
        // Quantidade de permissões localizadas
        var qtdPerLoc = $("#accordion li:contains("+$(this).val().toUpperCase()+")").length;
        
        // Quantidade total de abas
        var todasAbas = $("div.ui-accordion-content").length;
        // Armazena a quantidade de abas que possuem determinada permissão
        var qtdAbaLoc = 0;
        for(i=0; i<todasAbas; i++){
            $(".ui-accordion-content").eq(i).has("li:contains("+$(this).val().toUpperCase()+")").show();
            if($(".ui-accordion-content").eq(i).has("li:contains("+$(this).val().toUpperCase()+")").length){
                qtdAbaLoc++;   
            }
        }
        

        if(qtdPerLoc > 0){
            for(i=0; i<qtdPerLoc; i++){
                var pos = $("#accordion li:contains("+$(this).val().toUpperCase()+")").eq(i).offset().top;
                    var current = pos - 250;
                    todasPos[i] = current;

            }
            $("#linkAchados").attr("linkFim", todasPos.length-1);
            
            $("#localizado").html('<strong>Localizamos '+qtdPerLoc+' permissões que estão distribuidas em '+qtdAbaLoc+' abas. Veja abaixo:</strong>')
            .addClass('alert alert-info');
            
            $("#divLinkAchados").show();  
        }else{
            $("#divLinkAchados").hide();
        }
        
        //alert(cont);
        /*var index = $("#accordion li:contains("+$(this).val()+")")
                    .closest("div.ui-accordion-content")
                    .index("div.ui-accordion-content");
        $(".ui-accordion-content").eq(index).show();
        alert(index);*/
    }
    
    if($(this).val().length == 0){
        $(".ui-accordion-content").hide();
        $("#accordion li").css("background-color","#eeeeee");   
        $("#localizado").html('').removeClass('alert alert-info');     
    }
});

$("#accordion input:checkbox").click(function(){
    
    var tipo = false;
    
    if($(this).attr('tipo') == 'menu'){
        tipo = $(this).attr('menu');
    }
    
    if($(this).attr('tipo') == 'modulo'){
        tipo = $(this).attr('modulo');
    }
    
    if($(this).attr('tipo') == 'sidebar'){
        tipo = $(this).attr('sidebar');
    }
    
    if($(this).attr('tipo') == 'pagina'){
        tipo = $(this).attr('pagina');
    }
    
    if(tipo != false){
        if(this.checked == true){
            $("input["+$(this).attr('tipo')+"='"+tipo+"']").prop('checked', true);
        }else{
            $("input["+$(this).attr('tipo')+"='"+tipo+"']").prop('checked', false);
        }
    }
    
});

var headers = $('#accordion .accordion-header');
var contentAreas = $('#accordion .ui-accordion-content ').hide();
var expandLink = $('.accordion-expand-all');

// add the accordion functionality
headers.click(function() {
    var panel = $(this).next();
    var isOpen = panel.is(':visible');
 
    // open or close as necessary
    panel[isOpen? 'slideUp': 'slideDown']()
        // trigger the correct custom event
        .trigger(isOpen? 'hide': 'show');

    // stop the link from causing a pagescroll
    return false;
});

// hook up the expand/collapse all
expandLink.click(function(event){
    
    event.preventDefault();
    
    var isAllOpen = $(this).data('isAllOpen');
    
    contentAreas[isAllOpen? 'hide': 'show']()
        .trigger(isAllOpen? 'hide': 'show');
});

// when panels open or close, check to see if they're all open
contentAreas.on({
    // whenever we open a panel, check to see if they're all open
    // if all open, swap the button to collapser
    show: function(){
        var isAllOpen = !contentAreas.is(':hidden');   
        if(isAllOpen){
            expandLink.text('RECOLHER TODOS')
                .data('isAllOpen', true);
        }
    },
    // whenever we close a panel, check to see if they're all open
    // if not all open, swap the button to expander
    hide: function(){
        var isAllOpen = !contentAreas.is(':hidden');
        if(!isAllOpen){
            expandLink.text('EXPANDIR TODOS')
            .data('isAllOpen', false);
        } 
    }
});

$('#accordion :checkbox').shiftcheckbox();

$(document).ready(function(){

    
});

function dump(obj) {
    var out = '';
    for (var i in obj) {
        out += i + ": " + obj[i] + "\n";
    }
    alert(out);
}

</script>