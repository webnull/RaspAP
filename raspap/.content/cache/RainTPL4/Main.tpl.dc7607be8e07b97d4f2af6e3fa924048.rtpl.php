<?php if(!class_exists('Rain\RainTPL4')){exit;}?><?php extract($this->variables); require $this->checkTemplate("Layout/Header.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/Diagnostic/Main.tpl", 1, 0);?>

<?php extract($this->variables); require $this->checkTemplate("Admin/ModalResponse.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/Diagnostic/Main.tpl", 2, 30);?>


<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading"><i class="fa fa-gears fa-fw"></i> Diagnostic</div>
            <div class="panel-body">
                <div class="row">
                    <div class="row interfaceForm">
                        <?php $counter1=-1; $newVar=$commands; if(isset($newVar)&&(is_array($newVar)||$newVar instanceof Traversable)&& sizeof($newVar))foreach($newVar as $title => $command){ $counter1++; ?>

                            <a href="diagnostic?command=<?php print($title);?>">
                                <button type="button" class="btn btn-default" data-toggle="tooltip" data-placement="bottom" title="<?php print($command);?>" data-original-title="<?php print($command);?>" style="margin-bottom: 9px;"><?php print($title);?></button>
                            </a>
                        <?php }?>

                    </div>
                </div>

                <?php if($response){?>

                    <div class="col-lg-4" style="width: 100%; margin-top: 50px;">
                        <div class="panel panel-danger">
                            <div class="panel-heading">
                                <?php print($selectedCommand);?>

                            </div>
                            <div class="panel-body">
                                <pre><?php print($response);?></pre>
                            </div>
                        </div>
                    </div>
                <?php }?>

            </div>
        </div>
    </div>
<?php extract($this->variables); require $this->checkTemplate("Layout/Footer.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/Diagnostic/Main.tpl", 34, 1429);?>