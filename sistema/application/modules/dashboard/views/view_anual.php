<?php
echo link_tag(array('href' => 'assets/js/c3js/c3.css','rel' => 'stylesheet','type' => 'text/css'));
echo "<script type='text/javascript' src='".base_url('assets/js/c3js/d3.v3.min.js')."'></script>";
echo "<script type='text/javascript' src='".base_url('assets/js/c3js/c3.js')."'></script>";
echo "<script type='text/javascript' src='".base_url('assets/js/jquery.blockui/jquery.block.ui.js')."'></script>";
#print_r($anosRetornos);
?>

            <!--<div class="col-md-9 col-sm-8"> USADO PARA SIDEBAR -->
            <div class="col-lg-12">
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a>
                    </li>
                    <li class="active">Dashboard</li>
                </ol>
                <div id="divMain">
                    <h3>Dashboard</h3>
                        <?php
                        $options = array('' => '');		
                		foreach($anoDashboard as $ano){
                			$options[$ano] = $ano;
                		}
                		
                		echo form_label('Selecione o ano:', 'ano_dashboard');
                		echo form_dropdown('ano_dashboard', $options, '', 'id="ano_dashboard" class="form-control"');
                        ?>
                       <h4 class="ocultar">- Cobran&ccedil;a e Faturamento</h4>
                        <div class="ocultar">
                        <?php
                        $options = array('' => '');		
                		foreach($banco as $ba){
                			$options[$ba->cd_banco] = html_entity_decode($ba->nome_banco);
                		}
                		
                		echo form_label('Filtre pelo banco:', 'cd_banco');
                		echo form_dropdown('cd_banco', $options, '', 'id="cd_banco" class="form-control"');
                        ?>
                        </div>
                        
                       <h5 class="ocultar tituloGrafico">Quantidade de t&iacute;tulos baixados e rejeitados
                            <a class="descritivo_manual" target="_blank" href="<?php echo base_url('manual/manual_grafico_qtd_baixados_rejeitados.pdf');?>">Descritivo desse gr&aacute;fico</a>
                       </h5>
                       <p class="ocultar">Descri&ccedil;&atilde;o: Quantidade de t&iacute;tulos que ser&atilde;o baixados e rejeitadas pelo siga.</p>
                       <div class="ocultar" id="chart1"></div>
                       
                       <h5 class="ocultar tituloGrafico" class="ocultar">Valor total dos t&iacute;tulos baixados e rejeitados
                            <a class="descritivo_manual" target="_blank" href="<?php echo base_url('manual/manual_grafico_valor_baixados_rejeitados.pdf');?>">Descritivo desse gr&aacute;fico</a>
                       </h5>
                       <p class="ocultar">Descri&ccedil;&atilde;o: Valor total dos t&iacute;tulos que ser&atilde;o baixados e rejeitados pelo siga, sendo que o valor dos rejeitados s&atilde;o t&iacute;tulo de t&iacute;tulos que foram pagos e que foram rejeitados pelo banco.</p>
                       <div class="ocultar" id="chart2"></div>
                       
                       <div id="aguarde" style="text-align: center; display: none"><img src="<?php echo base_url('assets/img/aguarde.gif');?>" /></div>        
                </div>
            </div>
    
<script type="text/javascript">

function dump(obj) {
    var out = '';
    for (var i in obj) {
        out += i + ": " + obj[i] + "\n";
    }
    alert(out);
}

/*var chart1 = c3.generate({
    bindto: '#chart1',
	data: {
		columns: [
			['Atendidos', 30, 200, 100, 400, 150, 250, 50, 100, 250, 123, 345, 467],
			['Desmarcados', 10, 300, 140, 450, 110, 270, 60, 160, 280, 789, 185, 345]
		]
	},
	axis: {
		x: {
			type: 'category',
			categories: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez']
		}
	}
});*/

$.fn.graficoLinha = function() {
    
    // GRÁFICO DE LINHA                        
    c3.generate({
        bindto: '#chart1',
        data: {
            //url: '<?php echo base_url('assets/c3_test.json')?>',
            url: '<?php echo base_url(); ?>ajax/graficoLinhaRetornoAno/'+$("#ano_dashboard").val()+'/'+$("#cd_banco").val(),
            mimeType: 'json',
            //onclick: function (d, element) { alert(d.value); },
            keys: {
                x: 'meses', // it's possible to specify 'x' when category axis
                value: ['Quantidade baixado', 'Quantidade rejeitado']
            }
        },
    	axis: {
    		x: {
    			type: 'category'
    		},
             y : {
            tick: {
                    format: d3.format(',')
                }
            }            
    	},
        tooltip: {
            format: {
                value: function (value, ratio, id) {
                    var format = id === 'Quantidade baixado' ? d3.format(',') : d3.format(',');
                    return format(value).replace(',', '.').replace(',', '.');
                }
    //            value: d3.format(',') // apply this format to both y and y2
            }
        }
    });
    
};

$.fn.graficoBarraColada = function() {
    
    // GRÁFICO BARRA COLADA            
    chart2 = c3.generate({
        bindto: '#chart2',
        data: {
            //url: '<?php echo base_url('assets/c3_test.json')?>',
            url: '<?php echo base_url(); ?>ajax/graficoBarraColadaRetornoAno/'+$("#ano_dashboard").val()+'/'+$("#cd_banco").val(),
            mimeType: 'json',
            type: 'bar',
            x : 'meses',
            columns: ['meses','Valor baixado', 'Valor rejeitado'],
            groups: [
                ['Valor baixado', 'Valor rejeitado']
            ]
        },
        axis: {
            x: {
                type: 'category' // this needed to load string x value
            },
             y : {
            tick: {
                    format: d3.format(",")
                }
            }
        },
        tooltip: {
            format: {
                value: function (value, ratio, id) {
                    var format = id === 'Valor baixado' ? d3.format(',') : d3.format(',');
                    return format(value).replace(',', '.').replace(',', '.');
                }
    //            value: d3.format(',') // apply this format to both y and y2
            }
        }
    });    
    
};

$.fn.carregando = function(tempo) {
    
    $.blockUI({ 
    message:  '<h1>Gerando dashboard...</h1>',
    css: { 
    	border: 'none', 
    	padding: '15px', 
    	backgroundColor: '#000', 
    	'-webkit-border-radius': '10px', 
    	'-moz-border-radius': '10px', 
    	opacity: .5, 
    	color: '#fff' 
    	} 
    }); 
    
    setTimeout($.unblockUI, tempo);    
    
}

$(document).ready(function(){
    
    $(".ocultar").css('display', 'none');
    
    $("#ano_dashboard").change(function(){         
        
        $("#cd_banco").val('');
        
        if($(this).val() != ""){
            
            $(".ocultar").css('display', 'block');
            
            $(this).graficoLinha();
            $(this).graficoBarraColada();
            
            $(this).carregando(800);
            
            /*
            // GRAFICO DE BARRA
            c3.generate({
                bindto: '#chart2',
                data: {
                  url: '<?php echo base_url(); ?>ajax/graficoBarra/'+$(this).val(),
                    mimeType: 'json'
                  ,
                  type: 'bar',
                  onclick: function (d, element) { console.log("onclick", d, element); },
                  onmouseover: function (d) { console.log("onmouseover", d); },
                  onmouseout: function (d) { console.log("onmouseout", d); }
                },
                axis: {
                  x: {
            			type: 'category',
            			categories: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez']
            		}
                },
                bar: {
                  width: {
                    ratio: 0.3
                  }
                }
              }); 
              */    
                                              
        }else{
            $(".ocultar").css('display', 'none');
        }
        
    });
    
    $("#cd_banco").change(function(){
    
        if($(this).val() != ""){
                     
            /*
            // GRÁFICO DE LINHA                        
            c3.generate({
                bindto: '#chart1',
                data: {
                    //url: '<?php echo base_url('assets/c3_test.json')?>',
                    url: '<?php echo base_url(); ?>ajax/graficoLinhaRetornoAno/'+$("#ano_dashboard").val()+'/'+$(this).val(),
                    mimeType: 'json',
                    keys: {
                        x: 'meses', // it's possible to specify 'x' when category axis
                        value: ['Quantidade baixado', 'Quantidade rejeitado']
                    }
                },
            	axis: {
            		x: {
            			type: 'category'
            		}
            	}
            });
            
            // GRÁFICO BARRA COLADA            
            chart2 = c3.generate({
                bindto: '#chart2',
                data: {
                    //url: '<?php echo base_url('assets/c3_test.json')?>',
                    url: '<?php echo base_url(); ?>ajax/graficoBarraColadaRetornoAno/'+$("#ano_dashboard").val()+'/'+$(this).val(),
                    mimeType: 'json',
                    type: 'bar',
                    x : 'meses',
                    columns: ['meses','Valor baixado', 'Valor rejeitado'],
                    groups: [
                        ['Valor baixado', 'Valor rejeitado']
                    ]
                },
                axis: {
                    x: {
                        type: 'category' // this needed to load string x value
                    }
                }
            });
            */
            
            $(this).graficoLinha();
            $(this).graficoBarraColada();
            
            $(this).carregando(800); 
            
        }else{
            
            /*
            // GRÁFICO DE LINHA                        
            c3.generate({
                bindto: '#chart1',
                data: {
                    //url: '<?php echo base_url('assets/c3_test.json')?>',
                    url: '<?php echo base_url(); ?>ajax/graficoLinhaRetornoAno/'+$("#ano_dashboard").val()+'/'+$(this).val(),
                    mimeType: 'json',
                    keys: {
                        x: 'meses', // it's possible to specify 'x' when category axis
                        value: ['Quantidade baixado', 'Quantidade rejeitado']
                    }
                },
            	axis: {
            		x: {
            			type: 'category'
            		}
            	}
            });
            
            // GRÁFICO BARRA COLADA            
            chart2 = c3.generate({
                bindto: '#chart2',
                data: {
                    //url: '<?php echo base_url('assets/c3_test.json')?>',
                    url: '<?php echo base_url(); ?>ajax/graficoBarraColadaRetornoAno/'+$("#ano_dashboard").val()+'/'+$(this).val(),
                    mimeType: 'json',
                    type: 'bar',
                    x : 'meses',
                    columns: ['meses','Valor baixado', 'Valor rejeitado'],
                    groups: [
                        ['Valor baixado', 'Valor rejeitado']
                    ]
                },
                axis: {
                    x: {
                        type: 'category' // this needed to load string x value
                    }
                }
            });
            */
            
            $(this).graficoLinha();
            $(this).graficoBarraColada();
            
            $(this).carregando(800);
            
        }
    
    });
    
});

</script>    
