<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Sistema Tiago</title>

    <!-- Bootstrap core CSS 
    <link href="css/bootstrap.css" rel="stylesheet">
    
     JavaScript 
    <script src="js/jquery-1.10.2.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/modern-business.js"></script>-->
    
    <!-- Add custom CSS here 
    <link href="css/modern-business.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet">-->
    
<?php

# Bootstrap core CSS
echo link_tag(array('href' => 'assets/css/bootstrap.css','rel' => 'stylesheet','type' => 'text/css'));  
echo link_tag(array('href' => 'assets/css/modern-business.css','rel' => 'stylesheet','type' => 'text/css'));
echo link_tag(array('href' => 'assets/font-awesome/css/font-awesome.min.css','rel' => 'stylesheet','type' => 'text/css'));
echo link_tag(array('href' => 'assets/css/personalizado.css','rel' => 'stylesheet','type' => 'text/css')); 

# JavaScript
echo "<script type='text/javascript' src='".base_url('assets/js/jquery-1.10.2.js')."'></script>";
echo "<script type='text/javascript' src='".base_url('assets/js/bootstrap.js')."'></script>";  
echo "<script type='text/javascript' src='".base_url('assets/js/modern-business.js')."'></script>";

?>    

</head>

<body>

    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.html">Sistema de negócios</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <!--<div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="about.html">About</a>
                    </li>
                    <li><a href="services.html">Services</a>
                    </li>
                    <li><a href="contact.php">Contact</a>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Portfolio <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="portfolio-1-col.html">1 Column Portfolio</a>
                            </li>
                            <li><a href="portfolio-2-col.html">2 Column Portfolio</a>
                            </li>
                            <li><a href="portfolio-3-col.html">3 Column Portfolio</a>
                            </li>
                            <li><a href="portfolio-4-col.html">4 Column Portfolio</a>
                            </li>
                            <li><a href="portfolio-item.html">Single Portfolio Item</a>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Blog <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="blog-home-1.html">Blog Home 1</a>
                            </li>
                            <li><a href="blog-home-2.html">Blog Home 2</a>
                            </li>
                            <li><a href="blog-post.html">Blog Post</a>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Other Pages <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li class="active"><a href="full-width.html">Full Width Page</a>
                            </li>
                            <li><a href="sidebar.html">Sidebar Page</a>
                            </li>
                            <li><a href="faq.html">FAQ</a>
                            </li>
                            <li><a href="404.html">404</a>
                            </li>
                            <li><a href="pricing.html">Pricing Table</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>-->
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>

    <div class="container">

        <div class="row">

            <div class="col-lg-12">
                <ol class="breadcrumb">
                    <li>
                        <a href="index.html">Home</a>
                    </li>
                    <li class="active">
                        Gerênciar arquivo de retorno
                    </li>
                </ol>
                
            </div>

        </div>
        
        <div class="span7" id="divMain">
            <?php
            echo $this->session->flashdata('statusOperacao');
            $data = array('class'=>'pure-form','id'=>'ger_arquivo');
        	echo form_open_multipart('home/salvaArquivo',$data);
                $attributes = array('id' => 'address_info', 'class' => 'address_info');
 
        		echo form_fieldset("Gerenciar arquivo de retorno", $attributes);
        		
                    $options = array('' => '');		
            		foreach($bancoArquivo as $ba){
            			$options[$ba->cd_banco_arquivo] = $ba->nome_banco_arquivo;
            		}
            		
            		echo form_label('Banco', 'cd_banco_arquivo');
            		echo form_dropdown('cd_banco_arquivo', $options, '', 'id="cd_banco_arquivo" class="form-control"');
                    
        			echo form_label('Nome do arquivo', 'nome_arquivo');
        			$data = array('name'=>'nome_arquivo','id'=>'nome_arquivo', 'placeholder'=>'Digite um nome para salvar o arquivo', 'class'=>'form-control');
        			echo form_input($data);
                    
                    echo form_label('Arquivo', 'userfile');
        			$data = array('name'=>'userfile','id'=>'userfile', 'placeholder'=>'Selecione o arquivo', 'class'=>'form-control');
        			echo form_upload($data);
        			
                    echo '<div class="actions">';
        			echo form_submit("btn_cadastro","Registrar arquivo", 'class="btn btn-primary pull-right"');
                    echo '</div>';
        		echo form_fieldset_close();
        	echo form_close();
            
                                   
            ?>
        </div>

        <div class="row">

            <div style="margin-top:20px">
                <?php 
                #echo '<pre>'; print_r($arquivosRegistrados);  
                    $this->table->set_heading('Nome arquivo','Banco', 'Data/hora', 'Ação');
                    
                    foreach($arquivosRegistrados as $aR){
                    
                        $cell1 = array('data' => $aR->nome_arquivo);
                        $cell2 = array('data' => $aR->nome_banco_arquivo);
                        $cell3 = array('data' => $this->util->formataData(substr($aR->data_arquivo,0, 10), 'BR').' - '.substr($aR->data_arquivo,11, 10));
                        $cell4 = array('data' => '<a title="Pesquisar boleto no arquivo" href="'.base_url('home/pesquisarBoleto/'.$aR->cd_arquivo).'">Pesquisar boleto</a>');
                    
                        $this->table->add_row($cell1, $cell2, $cell3, $cell4);
                    
                    }
                    
                    $template = array('table_open' => '<table class="table table-bordered">');
                        
                	$this->table->set_template($template);
                	echo $this->table->generate();
                ?>
            </div>
            

        </div>

<!--    </div>
-->    
    <!-- /.container -->
<!--
    <div class="container">

        <hr>

        <footer>
            <div class="row">
                <div class="col-lg-12">
                    <p>Copyright &copy; Company 2013</p>
                </div>
            </div>
        </footer>

    </div>
-->    
    <!-- /.container -->

</body>

</html>
