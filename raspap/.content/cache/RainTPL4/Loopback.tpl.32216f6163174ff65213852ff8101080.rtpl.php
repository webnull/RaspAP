<?php if(!class_exists('Rain\RainTPL4')){exit;}?><div class="col-md-6">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="loopbackInterfaceIcon"></div>

            <h4>Local Loopback interface</h4>
            <b>Interface Name:</b> <?php print($interface->getName());?><br />

            <br />
            <?php extract($this->variables); require $this->checkTemplate("Admin/SummaryScreen/StatisticsBlock.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/SummaryScreen/Interfaces/Loopback.tpl", 10, 292);?>

        </div>
    </div>
</div>