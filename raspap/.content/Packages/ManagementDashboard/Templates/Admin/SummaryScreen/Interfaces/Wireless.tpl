<div class="col-md-6">
    <div class="panel panel-default">
        <div class="panel-body">
            {if $interface->isConnected()}
                <div class="wirelessInterfaceIcon"></div>
            {else}
                <div class="wirelessInterfaceNotConnectedIcon"></div>
            {/if}

            <h4>{$interface->getType()} interface</h4>
            <b>Interface Name:</b> {$interface->getName()}<br />
            <b>IPv4:</b> {$info.IPv4|ab:"Not associated"}<br />
            <b>Netmask:</b> {$info.Netmask|ab:"Not setup"}<br />
            <b>IPv6:</b> {$info.IPv6|ab:"Not associated"}<br />
            <b>Mac Address:</b> {$info.MAC}<br />

            {if $interface->isConnected()}
                <b>Network:</b> <i>{$info.ESSID}</i><br />
                <b>Strength:</b> {$info.LinkQuality|ab:"?"} {if $info.SignalLevel}({$info.SignalLevel} dBm){/if}<br />
                <b>TX-Power:</b> {$info.TransmissionPower}<br />
            {/if}

            <br />
            {include "Admin/SummaryScreen/StatisticsBlock.tpl"}
            {include "Admin/SummaryScreen/ConfigureButton.tpl"}
        </div><!-- /.panel-body -->
    </div><!-- /.panel-default -->
</div><!-- /.col-md-6 -->