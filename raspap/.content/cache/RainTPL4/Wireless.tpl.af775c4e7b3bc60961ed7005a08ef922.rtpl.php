<?php if(!class_exists('Rain\RainTPL4')){exit;}?><div class="col-md-6">
    <div class="panel panel-default">
        <div class="panel-body">
            <?php if($interface->isConnected()){?>

                <div class="wirelessInterfaceIcon"></div>
            <?php }else{?>

                <div class="wirelessInterfaceNotConnectedIcon"></div>
            <?php }?>


            <h4><?php print($interface->getType());?> interface</h4>
            <b>Interface Name:</b> <?php print($interface->getName());?><br />
            <b>IPv4:</b> <?php print(ab($info["IPv4"], "Not associated"));?><br />
            <b>Netmask:</b> <?php print(ab($info["Netmask"], "Not setup"));?><br />
            <b>IPv6:</b> <?php print(ab($info["IPv6"], "Not associated"));?><br />
            <b>Mac Address:</b> <?php print($info["MAC"]);?><br />

            <?php if($interface->isConnected()){?>

                <b>Network:</b> <i><?php print($info["ESSID"]);?></i><br />
                <b>Strength:</b> <?php print(ab($info["LinkQuality"], "?"));?> <?php if($info["SignalLevel"]){?>(<?php print($info["SignalLevel"]);?> dBm)<?php }?><br />
                <b>TX-Power:</b> <?php print($info["TransmissionPower"]);?><br />
            <?php }?>


            <br />
            <?php extract($this->variables); require $this->checkTemplate("Admin/SummaryScreen/StatisticsBlock.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/SummaryScreen/Interfaces/Wireless.tpl", 24, 1003);?>

            <?php extract($this->variables); require $this->checkTemplate("Admin/SummaryScreen/ConfigureButton.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/SummaryScreen/Interfaces/Wireless.tpl", 25, 1067);?>

        </div>
    </div>
</div>