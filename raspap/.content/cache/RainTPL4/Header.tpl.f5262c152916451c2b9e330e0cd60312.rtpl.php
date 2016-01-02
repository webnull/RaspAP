<?php if(!class_exists('Rain\RainTPL4')){exit;}?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <base href="<?php print($baseURL);?>">

    <title>RaspAP - router from your computer</title>

    
    <script src="./bower_components/jquery/dist/jquery.min.js"></script>

    
    <link href="./bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    
    <link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">

    
    <link href="dist/css/timeline.css" rel="stylesheet">

    
    <link href="dist/css/sb-admin-2.css" rel="stylesheet">

    
    <link href="bower_components/morrisjs/morris.css" rel="stylesheet">

    
    <link href="bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    
    <link href="dist/css/custom.css" rel="stylesheet">

    <link rel="shortcut icon" type="image/png" href="../img/favicon.png">
    
    
    
    <script type="text/javascript" src="js/admin.js"></script>

    
    <?php if(isset($includedJS) && $includedJS){?>

        <?php $counter1=-1; $newVar=$includedJS; if(isset($newVar)&&(is_array($newVar)||$newVar instanceof Traversable)&& sizeof($newVar))foreach($newVar as $key1 => $path){ $counter1++; ?>

            <script type="text/javascript" src="<?php print($path);?>"></script>
        <?php }?>

    <?php }?>

</head>
<body>
    <input type="hidden" name="sessionId" value="<?php print($sessionId);?>">
    <div id="wrapper">
        
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="summary">RaspAP - router from your box</a>
            </div>
            

            <ul class="nav navbar-top-links navbar-right">
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false">
                    <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
                </a>
                <ul class="dropdown-menu dropdown-user">
                    <li>
                        <a id="logoutButton"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                    </li>
                </ul>
                
            </li>
            
            </ul>

            
            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li>
                            <a href="summary"><i class="fa fa-signal fa-fw"></i> Network interfaces</a>
                        </li>
                        <li>
                            <a href="dhcpConnectedDevices"><i class="fa fa-exchange fa-fw"></i> Connected devices</a>
                        </li>
                        <li>
                            <a href="anonymitySettings"><i class="fa fa-eye-slash fa-fw"></i> TOR</a>
                        </li>
                        <li>
                            <a href="diagnostic"><i class="fa fa-gears fa-fw"></i> Diagnostic</a>
                        </li>
                        
                    </ul>
                </div>
            </div>
        </nav>

        <div id="page-wrapper">

            
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">
                        <img class="logo" src="./img/raspAP-logo.png" width="45" height="45">RaspAP
                    </h1>
                </div>
            </div>