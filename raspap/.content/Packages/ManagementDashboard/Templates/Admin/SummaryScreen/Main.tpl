{include "Layout/Header.tpl"}
{$hideDetailsButton=false}

{if $daemonNotRunningAlert}
    <div class="alert alert-warning">
        Daemon is not running. Changes will not be applied until daemon will not be started.
    </div>
{/if}

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading"><i class="fa fa-dashboard fa-fw"></i> Network interfaces</div>
            <div class="panel-body">
                <div class="row">
                    {foreach from="$interfaces" as $interfaceName => $interface}
                        {$info=$interface->getInfo()}

                        {if $interface->isAccessPoint()}
                            {include "Admin/SummaryScreen/Interfaces/AccessPoint.tpl"}

                        {elseif $interface->getType() == 'Wired'}
                            {include "Admin/SummaryScreen/Interfaces/Wired.tpl"}

                        {elseif $interface->getType() == 'Wireless'}
                            {include "Admin/SummaryScreen/Interfaces/Wireless.tpl"}

                        {elseif $interface->getType() == 'Loopback'}
                            {include "Admin/SummaryScreen/Interfaces/Loopback.tpl"}

                        {elseif $interface->getType() == 'Bridge'}
                            {include "Admin/SummaryScreen/Interfaces/Bridge.tpl"}
                        {/if}
                    {/foreach}
                </div>
            </div><!-- /.panel-body -->
        </div><!-- /.panel-default -->
    </div><!-- /.col-lg-12 -->
</div><!-- /.row -->
{include "Layout/Footer.tpl"}