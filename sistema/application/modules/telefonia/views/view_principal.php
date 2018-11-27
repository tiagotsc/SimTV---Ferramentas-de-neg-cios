	<style>
/*		* {
			font-family: Arial;
			font-size: 12px;
		}
		table {
			border-collapse: collapse;
		}
		td, th {
			border: 1px solid #666666;
			padding:  4px;
		}
		div {
			margin: 4px;
		}
		.sort_asc:after {
			content: "?";
		}
		.sort_desc:after {
			content: "?";
		}
*/
	</style>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>

            <div class="col-md-9 col-sm-8">
            <!--<div class="col-lg-12">-->
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a>
                    </li>
                    <li class="active">Telefonia</li>
                </ol>
                <div id="divMain">
                    <div class="row">
                    <?php
                    echo $this->session->flashdata('statusOperacao');
                    $termo = (isset($termo))? $termo: false;
                    if($termo){
                        #echo '<pre>'; print_r($termo); exit();
                        $this->table->set_heading('Cd', 'Data do Termo', 'Linha', 'Aparelho', 'A&ccedil;&atilde;o');
                        
                        foreach($termo as $te){
                            
                            if($te->aceite_termo == 'S'){
                                $icone = 'glyphicon glyphicon-thumbs-up';
                            }elseif($te->aceite_termo == 'N'){
                                $icone = 'glyphicon glyphicon-thumbs-down';
                            }else{
                                $icone = 'glyphicon glyphicon-list-alt';
                            }
                            
                            $cell1 = array('data' => $te->cd_telefonia_emprestimo);
                            $cell2 = array('data' => $te->data_termo);
                            $cell3 = array('data' => $te->ddd.' - '.$te->numero);
                            $cell4 = array('data' => $te->marca.' - '.$te->modelo);
                            $cell5 = array('data' => '<a title="Visualizar termo" href="#" data-toggle="modal" onclick="viewTermo('.$te->cd_telefonia_emprestimo.', '.$te->cd_telefonia_operadora.', '.$te->cd_usuario.')" data-target="#view_termo" class="glyphicon '.$icone.'"></a>');
                            $this->table->add_row($cell1, $cell2, $cell3, $cell4, $cell5);
                        }
                        
                        $template = array('table_open' => '<table class="table zebra">');
                    	$this->table->set_template($template);
                    	echo $this->table->generate();
                        
                    #echo '<pre>';
                    #print_r($termo);
                    #exit();
                    }
                    
                    ?>
                       <!-- <p>Gerenciamento de telef&ocirc;nia</p>
                        <p>Selecione a op&ccedil;&atilde;o ao lado para o que deseja</p>    -->
                    </div>  
                </div>
                
            </div>
    
