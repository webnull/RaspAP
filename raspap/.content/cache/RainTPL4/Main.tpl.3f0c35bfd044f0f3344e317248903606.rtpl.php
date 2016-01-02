<?php if(!class_exists('Rain\RainTPL4')){exit;}?><?php extract($this->variables); require $this->checkTemplate("Layout/Header.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/SummaryScreen/Main.tpl", 1, 0);?>

<?php $hideDetailsButton=false;?>


<?php if($daemonNotRunningAlert){?>

    <div class="alert alert-warning">
        Daemon is not running. Changes will not be applied until daemon will not be started.
    </div>
<?php }?>


<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading"><i class="fa fa-dashboard fa-fw"></i> Network interfaces</div>
            <div class="panel-body">
                <div class="row">
                    <?php $counter1=-1; $newVar=$interfaces; if(isset($newVar)&&(is_array($newVar)||$newVar instanceof Traversable)&& sizeof($newVar))foreach($newVar as $interfaceName => $interface){ $counter1++; ?>

                        <?php $info=$interface->getInfo();?>


                        <?php if($interface->isAccessPoint()){?>

                            <?php extract($this->variables); require $this->checkTemplate("Admin/SummaryScreen/Interfaces/AccessPoint.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/SummaryScreen/Main.tpl", 20, 717);?>


                        <?php }elseif($interface->getType() == 'Wired'){?>

                            <?php extract($this->variables); require $this->checkTemplate("Admin/SummaryScreen/Interfaces/Wired.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/SummaryScreen/Main.tpl", 23, 871);?>


                        <?php }elseif($interface->getType() == 'Wireless'){?>

                            <?php extract($this->variables); require $this->checkTemplate("Admin/SummaryScreen/Interfaces/Wireless.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/SummaryScreen/Main.tpl", 26, 1022);?>


                        <?php }elseif($interface->getType() == 'Loopback'){?>

                            <?php extract($this->variables); require $this->checkTemplate("Admin/SummaryScreen/Interfaces/Loopback.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/SummaryScreen/Main.tpl", 29, 1176);?>


                        <?php }elseif($interface->getType() == 'Bridge'){?>

                            <?php extract($this->variables); require $this->checkTemplate("Admin/SummaryScreen/Interfaces/Bridge.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/SummaryScreen/Main.tpl", 32, 1328);?>

                        <?php }?>

                    <?php }?>

                </div>
            </div>
        </div>
    </div>
</div>
<?php extract($this->variables); require $this->checkTemplate("Layout/Footer.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/SummaryScreen/Main.tpl", 40, 1518);?>