<?php
echo link_tag(array('href' => 'assets/css/css_dashboard_call.css', 'rel' => 'stylesheet', 'type' => 'text/css'));
echo link_tag(array('href' => 'assets/componentes/datepicker/css/datepicker.css',
    'rel' => 'stylesheet', 'type' => 'text/css'));
echo "<script type='text/javascript' src='" .
 base_url('assets/componentes/datepicker/js/bootstrap-datepicker.js') . "'></script>";

//header('Content-Type: text/html; charset=UTF-8');

$this->session->set_userdata('current_menu_pbx', 0);
?>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <div class="col col-md-12 md-offset-3 col-sm-3 main">

        <div class="row-fluid col col-md-10">

            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a></li>
                <li class="active">Dashboard / Telefonia</li>
            </ol>
        </div>
    </div>