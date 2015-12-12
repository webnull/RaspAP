<?php if(!class_exists('Rain\RainTPL4')){exit;}?><?php echo $this->modifiers["addJS"]("js/ManagementDashboard/ConfigureInterface.js");?>

<?php extract($this->variables); require $this->checkTemplate("Layout/Header.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/ConfigureInterface/Main.tpl", 2, 55);?>

<?php extract($this->variables); require $this->checkTemplate("Admin/ModalResponse.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/ConfigureInterface/Main.tpl", 3, 85);?>

<script type="text/javascript">currentInterfaceName = '<?php print($interface->getName());?>';</script>

<?php if($interface->getFailMessage()){?>

    <div class="alert alert-danger">
        <b>Interface configuration failed:</b> <?php print($interface->getFailMessage());?>

    </div>
<?php }?>


<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading"><i class="fa fa-signal fa-fw"></i> <?php print($interface->getName());?></div>
            <div class="panel-body">
                <?php $hideDetailsButton=true;?>

                <?php $info=$interface->getInfo();?>


                <?php if($interface->isAccessPoint()){?>

                    <?php extract($this->variables); require $this->checkTemplate("Admin/SummaryScreen/Interfaces/AccessPoint.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/ConfigureInterface/Main.tpl", 21, 766);?>


                <?php }elseif($interface->getType() == 'Wired'){?>

                    <?php extract($this->variables); require $this->checkTemplate("Admin/SummaryScreen/Interfaces/Wired.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/ConfigureInterface/Main.tpl", 24, 904);?>


                <?php }elseif($interface->getType() == 'Wireless'){?>

                    <?php extract($this->variables); require $this->checkTemplate("Admin/SummaryScreen/Interfaces/Wireless.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/ConfigureInterface/Main.tpl", 27, 1039);?>


                <?php }elseif($interface->getType() == 'Loopback'){?>

                    <?php extract($this->variables); require $this->checkTemplate("Admin/SummaryScreen/Interfaces/Loopback.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/ConfigureInterface/Main.tpl", 30, 1177);?>

                <?php }?>


                <div class="row interfaceForm">
                    <?php extract($this->variables); require $this->checkTemplate("Admin/ConfigureInterface/FormInput.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/ConfigureInterface/Main.tpl", 34, 1324);?>

                </div>
            </div>
        </div>
    </div>
</div>
<?php extract($this->variables); require $this->checkTemplate("Layout/Footer.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/ConfigureInterface/Main.tpl", 40, 1450);?>