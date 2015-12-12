<?php if(!class_exists('Rain\RainTPL4')){exit;}?><div class="col-md-6">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="bridgeInterfaceIcon"></div>

            <h4>Local bridge interface</h4>
            <b>Interface Name:</b> <?php print($interface->getName());?><br />

            <br />
            <?php extract($this->variables); require $this->checkTemplate("Admin/SummaryScreen/StatisticsBlock.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/SummaryScreen/Interfaces/Bridge.tpl", 10, 288);?>

        </div>
    </div>
</div>