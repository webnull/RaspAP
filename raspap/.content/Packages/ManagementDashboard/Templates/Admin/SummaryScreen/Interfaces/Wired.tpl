<div class="col-md-6">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="wiredInterfaceIcon"></div>

            <h4>{$interface->getType()} interface</h4>
            <b>Interface Name:</b> <i>{$interface->getName()}</i><br />
            <b title="Local IPv4 address">IPv4:</b> {$info.IPv4|ab:"Not associated"}<br />
            <b>Netmask:</b> {$info.Netmask|ab:"Not setup"}<br />
            <b>IPv6:</b> {$info.IPv6|ab:"Not associated"}<br />
            <b>Mac Address:</b> {$info.MAC}<br />
            {if $interface->getBroadcastAddress()}<b>Broadcast</b>: {$interface->getBroadcastAddress()}<br />{/if}
            {if $interface->getGatewayAddress()}<b>Gateway</b>: {$interface->getGatewayAddress()}<br />{/if}

            {if $interface->isBridgeConnected()}
                <b>Bridge:</b> <span style="color: red;">connected</span><br/>
            {/if}

            <br />
            {include "Admin/SummaryScreen/StatisticsBlock.tpl"}
            {include "Admin/SummaryScreen/ConfigureButton.tpl"}
        </div><!-- /.panel-body -->
    </div><!-- /.panel-default -->
</div><!-- /.col-md-6 -->