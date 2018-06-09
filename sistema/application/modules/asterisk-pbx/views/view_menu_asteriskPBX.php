<?php
$current = $this->session->userdata('current_menu_pbx');
?>

<div class="col-md-2 col-sm-3 sidebar">
    <nav class="navbar navbar-default sidebar" role="navigation">
        <div class="container-fluid">
            <div class="collapse navbar-collapse" id="bs-sidebar-navbar-collapse-1">
                <ul id= "menu" class="nav navbar-nav panel panel-primary">
                    <div class="panel-heading text-center">
                        <h1 style="font-size:14px; font-family: Verdana" class="panel-title">- LIGA&Ccedil&OtildeES -</h1>
                    </div>
                    <li class="divider"></li>
                        <?php $active[$current] = "class=active"; ?>
                    <li <?php echo $active[1] ?>><a href="#"<?php $active[1] ?> class="dropdown dropdown-toggle" data-toggle="dropdown">Ativo - Chamadas &nbsp <span class="caret"></span><span class="pull-right hidden-xs showopacity"></span></a>
                        <ul class="dropdown-menu forAnimate" role="menu">
                            <li <?php echo $active[2] ?>><a href=<?php echo base_url('dashboard/telefonia/ativoChamadasIntelig') ?><?php $active[2] ?>>INTELIG</a></li>
                            <li class="divider"></li>
                            <li <?php echo $active[3] ?>><a href=<?php echo base_url('dashboard/telefonia/ativoChamadasGSM') ?><?php $active[3] ?>>Gateway GSM</a></li>
                        </ul>
                    </li>
                    <li class="divider"></li>
                    <li <?php echo $active[4] ?>><a href="#"<?php $active[4] ?> class="dropdown dropdown-toggle" data-toggle="dropdown">Receptivo - 4004 &nbsp&nbsp <span class="caret"></span><span class="pull-right hidden-xs showopacity"></span></a>
                        <ul class="dropdown-menu forAnimate" role="menu">
                            <li <?php echo $active[5] ?>><a href=<?php echo base_url('dashboard/telefonia/qmt8668') ?><?php $active[5] ?>>4003-8668</a></li>
                            <li class="divider"></li>
                            <li <?php echo $active[6] ?>><a href=<?php echo base_url('dashboard/telefonia/qmt9988') ?><?php $active[6] ?>>4003-9988</a></li>
                        </ul>
                    </li>
                    <li class="divider"></li>
                    <li <?php echo $active[7] ?>><a href=<?php echo base_url('dashboard/telefonia/tridigito') ?><?php $active[7] ?>>Receptivo - Trid&iacutegito<span class="pull-right hidden-xs showopacity"></span></a></li>
                    <li class="divider"></li>
                    <li <?php echo $active[8] ?>><a href="#"<?php $active[8] ?> class="dropdown dropdown-toggle" data-toggle="dropdown">Comparativo &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<span class="caret"></span><span class="pull-right hidden-xs showopacity"></span></a>
                        <ul class="dropdown-menu forAnimate" role="menu">
                            <li <?php echo $active[9] ?>><a href=<?php echo base_url('dashboard/telefonia/comparativoPorPeriodo') ?><?php $active[9] ?>>Por per&iacuteodo</a></li>                            
                            <li class="divider"></li>
                            <!--<li <?php echo $active[10] ?>><a href=<?php echo base_url('dashboard/telefonia/comparativoMensal') ?><?php $active[10] ?>>Mensal</a></li>-->
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>

<style>
    body,html{
        height: 100%;
    }

    nav.sidebar, .main{
        -webkit-transition: margin 200ms ease-out;
        -moz-transition: margin 200ms ease-out;
        -o-transition: margin 200ms ease-out;
        transition: margin 200ms ease-out;
    }

    .main{
        padding: 10px 10px 0 10px;
    }

    @media (min-width: 765px) {

        .main{
            position: absolute;
            width: calc(100% - 40px); 
            margin-left: 40px;
            float: right;
        }

        nav.sidebar:hover + .main{
            margin-left: 200px;
        }

        nav.sidebar.navbar.sidebar>.container .navbar-brand, .navbar>.container-fluid .navbar-brand {
            margin-left: 0px;
        }

        nav.sidebar .navbar-brand, nav.sidebar .navbar-header{
            text-align: center;
            width: 100%;
            margin-left: 0px;
        }

        nav.sidebar a{
            padding-right: 13px;
        }

        nav.sidebar .navbar-nav > li:first-child{
            border-top: 1px #e5e5e5 solid;
        }

        nav.sidebar .navbar-nav > li{
            border-bottom: 1px #e5e5e5 solid;
        }

        nav.sidebar .navbar-nav .open .dropdown-menu {
            position: static;
            float: none;
            width: auto;
            margin-top: 0;
            background-color: transparent;
            border: 0;
            -webkit-box-shadow: none;
            box-shadow: none;
        }

        nav.sidebar .navbar-collapse, nav.sidebar .container-fluid{
            padding: 0 0px 0 0px;
        }

        .navbar-inverse .navbar-nav .open .dropdown-menu>li>a {
            color: #000;
        }

        nav.sidebar{
            width: 180px;
            height: 100%;
            margin-left: -160px;
            float: left;
            margin-bottom: 0px;
        }

        nav.sidebar li {
            width: 100%;
        }

        nav.sidebar:hover{
            margin-left: 0px;
        }

        .forAnimate{
            opacity: 0;
        }
    }

    @media (min-width: 1330px) {

        .main{
            width: calc(100% - 180px);
            margin-left: 180px;
        }

        nav.sidebar{
            margin-left: 0px;
            float: left;
        }

        nav.sidebar .forAnimate{
            opacity: 1;
        }
    }

    nav.sidebar .navbar-nav .open .dropdown-menu>li>a:hover, nav.sidebar .navbar-nav .open .dropdown-menu>li>a:focus {
        /*color: #CCC;*/
        color: black;
        font-style: italic;
        background-color: transparent;
    }

    nav:hover .forAnimate{
        opacity: 1;
    }
    section{
        padding-left: 15px;
    }
</style>