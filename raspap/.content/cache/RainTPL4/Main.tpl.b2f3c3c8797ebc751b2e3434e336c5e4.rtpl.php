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
                <?php if($interface->isBridgeConnected()){?>

                    <strong>Note:</strong> <i>This interface is already active through a bridge connection with other interface, probably also DHCP server is working on this interface.<br/>
                    If you want to have an internet connection on this interface it's suggested to leave this interface configured as is (Don't use this interface option with checkox checked - allow other network management applications to use this interface)</i><br/><br/>
                <?php }?>


                <?php $hideDetailsButton=true;?>

                <?php $info=$interface->getInfo();?>


                <?php if($interface->isAccessPoint()){?>

                    <?php extract($this->variables); require $this->checkTemplate("Admin/SummaryScreen/Interfaces/AccessPoint.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/ConfigureInterface/Main.tpl", 26, 1306);?>


                <?php }elseif($interface->getType() == 'Wired'){?>

                    <?php extract($this->variables); require $this->checkTemplate("Admin/SummaryScreen/Interfaces/Wired.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/ConfigureInterface/Main.tpl", 29, 1444);?>


                <?php }elseif($interface->getType() == 'Wireless'){?>

                    <?php extract($this->variables); require $this->checkTemplate("Admin/SummaryScreen/Interfaces/Wireless.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/ConfigureInterface/Main.tpl", 32, 1579);?>


                <?php }elseif($interface->getType() == 'Loopback'){?>

                    <?php extract($this->variables); require $this->checkTemplate("Admin/SummaryScreen/Interfaces/Loopback.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/ConfigureInterface/Main.tpl", 35, 1717);?>

                <?php }?>


                <div class="row interfaceForm">
                    <?php extract($this->variables); require $this->checkTemplate("Admin/ConfigureInterface/FormInput.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/ConfigureInterface/Main.tpl", 39, 1864);?>

                </div>
            </div>
        </div>
    </div>
</div>
<?php extract($this->variables); require $this->checkTemplate("Layout/Footer.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/ConfigureInterface/Main.tpl", 45, 1990);?>