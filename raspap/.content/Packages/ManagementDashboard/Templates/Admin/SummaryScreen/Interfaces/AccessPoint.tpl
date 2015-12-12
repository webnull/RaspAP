<div class="col-md-6">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="accessPointIcon"></div>

            <h4>Wireless Access Point, {$info.Standard}, {$interface->getName()}</h4>
            Interface Name: {$interface->getName()}<br />
            Mac Address: {$info.MAC}<br />
            Transmission power: {$info.TransmissionPower} dBm<br />
            MTU: {$info.MTU}<br />

            <br />
            {include "Admin/SummaryScreen/StatisticsBlock.tpl"}
            {include "Admin/SummaryScreen/ConfigureButton.tpl"}
        </div><!-- /.panel-body -->
    </div><!-- /.panel-default -->
</div><!-- /.col-md-6 -->