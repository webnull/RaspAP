<?php if(!class_exists('Rain\RainTPL4')){exit;}?><div class="col-md-6">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="accessPointIcon"></div>

            <h4>Wireless Access Point, <?php print($info["Standard"]);?>, <?php print($interface->getName());?></h4>
            Interface Name: <?php print($interface->getName());?><br />
            Mac Address: <?php print($info["MAC"]);?><br />
            Transmission power: <?php print($info["TransmissionPower"]);?> dBm<br />
            MTU: <?php print($info["MTU"]);?><br />

            <br />
            <?php extract($this->variables); require $this->checkTemplate("Admin/SummaryScreen/StatisticsBlock.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/SummaryScreen/Interfaces/AccessPoint.tpl", 13, 465);?>

            <?php extract($this->variables); require $this->checkTemplate("Admin/SummaryScreen/ConfigureButton.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/SummaryScreen/Interfaces/AccessPoint.tpl", 14, 529);?>

        </div>
    </div>
</div>