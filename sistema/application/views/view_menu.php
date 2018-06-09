<?php
# Configurações do sistema
include_once('configSistema.php');
?>
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <!-- You'll want to use a responsive image option so this logo looks good on devices - I recommend using something like retina.js (do a quick Google search for it and you'll find it) -->
            <a class="navbar-brand" href="<?php echo base_url('home/inicio');?>">Ferramentas de neg&oacute;cio</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse navbar-ex1-collapse">
            <?php echo $menu; ?>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container -->
</nav>
<?php
if(isset($_SERVER['HTTP_REFERER']) or $this->session->userdata('logado') == true){
    #if($this->session->userdata('logado') == true){
        echo '<div id="usuario">'.$this->session->userdata('bem_vindo').'</div>'; 
    #}
}

if($menu_satva /*or $this->session->userdata('cd') == 6*/){    
?>
<div class="container">
    <div>
        <div>&nbsp;</div>
        <div class="alert alert-warning" role="alert">
            <strong>
            Responda o indicador da Anatel abaixo. Ele esta dispon&iacute;vel at&eacute; <b style="color: red;"><?php echo $this->session->userdata('SATVA_FIM'); ?></b>. Ap&oacute;s essa data os dados ser&atilde;o enviados para Anatel.<br />
            Observa&ccedil;&otilde;es:
            <br/>- Se o menu estiver com um ok (&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-ok statusOk" aria-hidden="true"></span>) ignore essa mensagem, pois j&aacute; foi respondido.
            <br/>- Se o menu estiver com um alerta (&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-exclamation-sign statusAlerta" aria-hidden="true"></span>). Acesse, pois voc&ecirc; precisa justificar.
            </strong>
        </div>
    </div>
    <div>
        <div class="abaIndicadores"><strong>SATVA</strong></div>
        <div class="menuIndicadores">
        <?php 
        echo $menu_satva;
        ?>
        <div style="clear: both;"></div>
        </div>
    </div>
</div>
<script type="text/javascript">
function openFormAnatel(cd_form, cd_unidade){
    //alert(cd_unidade);
    $(location).attr('href','<?php echo base_url('anatel/openForm');?>/'+cd_form+'/'+cd_unidade);
}
</script>
<?php
}
?>
<!-- ######################################################################################################## -->
<?php 
if(in_array($this->session->userdata('cd'), array(6/*,2771,3588,3648,3951,4256,4298,4345,4583*/))){ 
    #$this->load->view('chat'); 
    #echo $chatHtml;
}
?>

<script>
$(".icone-menu-focus").mouseover(function(){
    $(this).prev().show();
});
/*
$(".icone-menu-focus").click(function(){
    $(this).prev().show();
});*/

$(".icone-menu-focus").mouseleave(function(){
    $(this).prev().hide();
});
</script>
