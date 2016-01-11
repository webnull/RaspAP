{"js/ManagementDashboard/ManagePasswords.js"|addJS}
{include "Layout/Header.tpl"}
<script type="text/javascript">currentInterfaceName = '{$interface->getName()}';</script>

{if isset($redirect) && $redirect}
    <script>
        //window.location.href = 'managePasswords,' + currentInterfaceName;
    </script>
{/if}

{if $statusMessage}
    <div class="alert alert-danger">
        <b>Save action failed:</b> {$statusMessage}
    </div>
{/if}

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading"><i class="fa fa-signal fa-fw"></i> {$interface->getName()}</div>
            <div class="panel-body">
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Computer name</th>
                        <th>Mac Address</th>
                        <th>Password</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    {$i=0}
                    {foreach from="$psk->get()" as $mac => $details}
                        {$i=$i+1}
                        <tr>
                            <td><form action="managePasswords,{$interface->getName()}?action=addMachine" method="POST">{$i}</td>
                            <td><input type="text" name="PSK_Title" value="{$details.name}" style="width: 100%;"></td>
                            <td><input type="text" name="PSK_Mac" value="{$mac}" data-origina-mac="{$mac}" style="width: 100%;" onchange="PSKMacChanged(this);"></td>
                            <td><input type="text" name="PSK_Secr" value="{$details.password}" style="width: 100%;"></td>
                            <td>
                                <button class="btn btn-primary saveButton" type="submit">Save</button>
                                <button class="btn btn-primary" type="button" onclick="removeMac(this);">Delete</button>
                                </form>
                            </td>
                        </tr>
                    {/foreach}

                    <tr>
                        <td><form action="managePasswords,{$interface->getName()}?action=addMachine" method="POST"></td>
                        <td><input type="text" name="PSK_Title" style="width: 100%;"></td>
                        <td><input type="text" name="PSK_Mac" style="width: 100%;"></td>
                        <td><input type="text" name="PSK_Secr" style="width: 100%;"></td>
                        <td><button class="btn btn-primary" type="submit">Save</button></form></td>
                    </tr>
                    </tbody>
                </table>

                <a href="configureInterface,{$interface->getName()}">
                    <button class="btn btn-primary" type="submit">Back to {$interface->getName()}</button>
                </a>
            </div>
        </div>
    </div>
</div>