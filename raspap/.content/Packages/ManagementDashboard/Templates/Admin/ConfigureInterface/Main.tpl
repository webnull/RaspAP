{"js/ManagementDashboard/ConfigureInterface.js"|addJS}
{include "Layout/Header.tpl"}
{include "Admin/ModalResponse.tpl"}
<script type="text/javascript">currentInterfaceName = '{$interface->getName()}';</script>

{if $interface->getFailMessage()}
    <div class="alert alert-danger">
        <b>Interface configuration failed:</b> {$interface->getFailMessage()}
    </div>
{/if}

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading"><i class="fa fa-signal fa-fw"></i> {$interface->getName()}</div>
            <div class="panel-body">
                {$hideDetailsButton=true}
                {$info=$interface->getInfo()}

                {if $interface->isAccessPoint()}
                    {include "Admin/SummaryScreen/Interfaces/AccessPoint.tpl"}

                {elseif $interface->getType() == 'Wired'}
                    {include "Admin/SummaryScreen/Interfaces/Wired.tpl"}

                {elseif $interface->getType() == 'Wireless'}
                    {include "Admin/SummaryScreen/Interfaces/Wireless.tpl"}

                {elseif $interface->getType() == 'Loopback'}
                    {include "Admin/SummaryScreen/Interfaces/Loopback.tpl"}
                {/if}

                <div class="row interfaceForm">
                    {include "Admin/ConfigureInterface/FormInput.tpl"}
                </div>
            </div><!-- /.panel-body -->
        </div><!-- /.panel-default -->
    </div><!-- /.col-lg-12 -->
</div><!-- /.row -->
{include "Layout/Footer.tpl"}