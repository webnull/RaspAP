<?php if(!class_exists('Rain\RainTPL4')){exit;}?><div class="col-md-6">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="wiredInterfaceIcon"></div>

            <h4><?php print($interface->getType());?> interface</h4>
            <b>Interface Name:</b> <i><?php print($interface->getName());?></i><br />
            <b title="Local IPv4 address">IPv4:</b> <?php print(ab($info["IPv4"], "Not associated"));?><br />
            <b>Netmask:</b> <?php print(ab($info["Netmask"], "Not setup"));?><br />
            <b>IPv6:</b> <?php print(ab($info["IPv6"], "Not associated"));?><br />
            <b>Mac Address:</b> <?php print($info["MAC"]);?><br />

            <br />
            <?php extract($this->variables); require $this->checkTemplate("Admin/SummaryScreen/StatisticsBlock.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/SummaryScreen/Interfaces/Wired.tpl", 14, 575);?>

            <?php extract($this->variables); require $this->checkTemplate("Admin/SummaryScreen/ConfigureButton.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/SummaryScreen/Interfaces/Wired.tpl", 15, 639);?>

        </div>
    </div>
</div>