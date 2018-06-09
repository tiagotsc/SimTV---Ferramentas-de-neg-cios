<?php
// We change the headers of the page so that the browser will know what sort of file is dealing with. Also, we will tell the browser it has to treat the file as an attachment which cannot be cached.
/*
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=exceldata.xls");
header("Pragma: no-cache");
header("Expires: 0");
*/

/** Error reporting */
#error_reporting(E_ALL);
#ini_set('memory_limit','100M');
#ini_set('display_errors', TRUE);
#ini_set('display_startup_errors', TRUE);

function geraCampos(&$objPHPExcel, $campos){

    # Controla a posição das colunas
    $contCampo = 0;
    
    # Estiliza a primeira coluna em negrito
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
    
    if($campos <> ''){
        
        try{
        
            # Cria as colunas títulos do excel
            foreach($campos as $campo){ 
                
                # Estiliza a coluna em negrito
                $objPHPExcel->getActiveSheet()->getStyle($contCampo)->getFont()->setBold(true);
                
                # Cria a coluna
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($contCampo, 1,  $campo);
                
                # Próxima coluna
                $contCampo++;
            }
        
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        
    }
 
}

date_default_timezone_set('America/Sao_Paulo');

if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');


#require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';
require_once('assets/PHPExcel/Classes/PHPExcel.php');

PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Sistema Sim Tv")
							 ->setLastModifiedBy("Sistema Sim Tv")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");


geraCampos($objPHPExcel, $campos);

# Conteúdo a partir da segunda linha
$linha = 2;
/*if($this->session->userdata('cd') == 6){
    echo '<pre>';
    print_r($valores);
    exit();
}*/

$primeiraColuna = key($valores[0]);

if($valores <> ''){ 
#$objPHPExcel->setActiveSheetIndex(0);    
    try{
        $ultimaColunaGravada = '';
        # Controla o campo da linha
        $coluna = 0;
        $sheet = 0;
        foreach($valores as $valor){ # Alimenta as colunas com o conteúdo            
            
            if($valor[$primeiraColuna] != $ultimaColunaGravada){ 
                #echo "<script type='text/javascript'>alert(".$sheet.");</script>"; 
                $objPHPExcel->createSheet(); 
                $objPHPExcel->setActiveSheetIndex($sheet);
                geraCampos($objPHPExcel, $campos);
                $linha = 2;
                $sheet++;
                $objPHPExcel->getActiveSheet()->setTitle($valor[$primeiraColuna]);
            };            
                                  
            # Remove o negrito
            $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(false);
            
            # Remove o negrito
            $objPHPExcel->getActiveSheet()->getStyle('B2')->getFont()->setBold(false);
            foreach($campos as $campo){
            
                # Estiliza removendo o negrito
                $objPHPExcel->getActiveSheet()->getStyle($coluna)->getFont()->setBold(false);
                
                #if($this->session->userdata('cd') == 6){
                    # Exemplos: 10/06/2015 | 10/06/15 | 2015-06-10 | 15-06-10
                    if(preg_match('/^[0-9]{2,4}(-|\/)[0-9]{2}(-|\/)[0-9]{2,4}$/', trim($valor[$campo]))){ # Verifica se o conteúdo é uma data
                        # Adiciona o conteúdo - Data no formato Excel
                        # Se for data converte a string data para o formato data do Excel
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha,  PHPExcel_Shared_Date::stringToExcel(trim($valor[$campo]))); #echo trim($valor[$campo]); echo '<br>'; echo $ExcelDateValue; exit();
                        $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($coluna, $linha)->getNumberFormat()->setFormatCode('dd/mm/yyyy'); #PHPExcel_Style_NumberFormat::FORMAT_DATE_DMYSLASH
                    
                    }elseif(is_numeric(trim($valor[$campo]))){
                        $objPHPExcel->getActiveSheet()
                        ->getStyle($coluna, $linha)
                        ->getNumberFormat()
                        ->setFormatCode('#,##0.00');
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, trim($valor[$campo] ));
                        
                    }/*elseif(is_numeric(trim($valor[$campo])) and strlen(trim($valor[$campo])) >= 12){ # Se for um número muito grande converte pra string
                        
                        $type = PHPExcel_Cell_DataType::TYPE_STRING;
                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($coluna, $linha)->setValueExplicit(trim($valor[$campo]), $type);
                        
                    }*/else{ # Mantem o formato padrão
                        # Adiciona o conteúdo
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha,  trim($valor[$campo]));
                        
                    }
        
                #}else{
                
                    # Adiciona o conteúdo
                    #$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha,  trim($valor[$campo]));
                
                #}
                # Segue pra próxima coluna
                $coluna++;
            }
        
            # Retorno a primeira coluna    
            $coluna = 0;
        
            # Avança pra próxima linha
            $linha++;  
            
            $ultimaColunaGravada = $valor[$primeiraColuna];  
            
            // Rename worksheet
            #$objPHPExcel->getActiveSheet()->setTitle($sheet);
            
        }
    
    }catch( Exception $e ){
            
        log_message('error', $e->getMessage());
        
    }

}



// Set active sheet index to the first sheet, so Excel opens this as the first sheet


/*$objPHPExcel->createSheet();

// Llorem ipsum...
$sLloremIpsum = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Vivamus eget ante. Sed cursus nunc semper tortor. Aliquam luctus purus non elit. Fusce vel elit commodo sapien dignissim dignissim. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Curabitur accumsan magna sed massa. Nullam bibendum quam ac ipsum. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Proin augue. Praesent malesuada justo sed orci. Pellentesque lacus ligula, sodales quis, ultricies a, ultricies vitae, elit. Sed luctus consectetuer dolor. Vivamus vel sem ut nisi sodales accumsan. Nunc et felis. Suspendisse semper viverra odio. Morbi at odio. Integer a orci a purus venenatis molestie. Nam mattis. Praesent rhoncus, nisi vel mattis auctor, neque nisi faucibus sem, non dapibus elit pede ac nisl. Cras turpis.';

// Add some data to the second sheet, resembling some different data types
#echo date('H:i:s') , " Add some data" , EOL;
$objPHPExcel->setActiveSheetIndex(1);
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Terms and conditions');
$objPHPExcel->getActiveSheet()->setCellValue('A3', $sLloremIpsum);
$objPHPExcel->getActiveSheet()->setCellValue('A4', $sLloremIpsum);
$objPHPExcel->getActiveSheet()->setCellValue('A5', $sLloremIpsum);
$objPHPExcel->getActiveSheet()->setCellValue('A6', $sLloremIpsum);
$objPHPExcel->getActiveSheet()->setTitle('Terms and conditions');*/

try{

    // Redirect output to a client’s web browser (Excel2007)
    /*header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="relatorio.xlsx"');
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');
    
    // If you're serving to IE over SSL, then the following may be needed
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header ('Pragma: public'); // HTTP/1.0
    
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');*/
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    
    $dir = './files/telecom/faturamento/delin/'.substr($this->input->post('data'),3,4);
    if(!file_exists ( $dir )){
        mkdir($dir, 0777, true);
    }
    chmod ($dir, 0777); # ALTERA A PERMISSÃO DA PASTA OU ARQUIVO
    
    $objWriter->save($dir.'/'.$link);
    
    #redirect(base_url('tcom-faturamento/faturamento/realizarAcao'));
    redirect(base_url($modulo.'/'.$controller.'/realizarAcao'));
}catch( Exception $e ){
    
    log_message('error', $e->getMessage());
    
}

#exit;

?>