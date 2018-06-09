<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
 
include_once APPPATH.'/third_party/mpdf/mpdf.php';
 
class M_pdf {
 
    public $pdf;
 
    /*public function __construct(
                                $modo = 'en-x', 
                                $formato = 'A4', 
                                $font_size = '', 
                                $fonte = '', 
                                $margin_left = 20, 
                                $margin_right = 20, 
                                $margin_top = 10, 
                                $margin_bottom = 20, 
                                $margin_header = 5, 
                                $margin_footer = 10,
                                $orientacao = 'P'
                                )
    {
        $this->pdf = new mPDF(
                                $modo,$formato,
                                $font_size,
                                $fonte,
                                $margin_left,
                                $margin_right,
                                $margin_top,
                                $margin_bottom,
                                $margin_header,
                                $margin_footer,
                                $orientacao
                                );
    }*/
    public function __construct($parametros = "'en-x','A4','','',20,20,10,20,5,10,'P'")
    {
        $parametros = explode(',',$parametros);
        list($modo,
            $formato,
            $font_size,
            $fonte,
            $margin_left,
            $margin_right,
            $margin_top,
            $margin_bottom,
            $margin_header,
            $margin_footer,
            $orientacao
            ) = $parametros;                
        $this->pdf = new mPDF(
                                $modo,
                                $formato,
                                $font_size,
                                $fonte,
                                $margin_left,
                                $margin_right,
                                $margin_top,
                                $margin_bottom,
                                $margin_header,
                                $margin_footer,
                                $orientacao
                                );
    }
    
}