{include "Layout/Header.tpl"}

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading"><i class="fa fa-exchange fa-fw"></i> Connected devices</div>
            <div class="panel-body">
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>MAC</th>
                        <th>Name</th>
                        <th>DHCP Lease Started</th>
                        <th>DHCP Lease Ends</th>
                    </tr>
                    </thead>

                    <tbody>
                        {if !$leases->getLeases()}
                            <tr>
                                <td colspan="4">
                                    No DHCP clients present
                                </td>
                            </tr>
                        {/if}

                        {foreach from="$leases->getLeases()" as $address => $lease}
                            <tr>
                                <td>
                                    {$lease.hardware.2}
                                </td>
                                <td>
                                    {$address} {if isset($lease.name)}{$lease.name}{/if}
                                </td>
                                <td>
                                    {$lease.starts_ipv4}
                                </td>
                                <td>
                                    {$lease.ends_ipv4}
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div><!-- /.panel-body -->
        </div><!-- /.panel-default -->
    </div><!-- /.col-lg-12 -->
</div><!-- /.row -->
{include "Layout/Footer.tpl"}