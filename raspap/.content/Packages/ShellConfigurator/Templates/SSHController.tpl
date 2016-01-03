{"js/ManagementDashboard/SSH.js"|addJS}
{include "Layout/Header.tpl"}

{if $errorMessage}
    <div class="alert alert-danger">
        {$errorMessage}
    </div>
{/if}

{if $saved && !$errorMessage}
    <div class="alert alert-success">
        Saved.
    </div>
{/if}

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading"><i class="fa fa-plug fa-fw"></i> Configure Secure Shell Server</div>
            <div class="panel-body">
                    <div class="formInput" style="margin-top: 0px;">
                        <form name="interface_save_form" method="POST">
                            <input type="hidden" name="FormPosted" value="true">

                            <table class="formTable">
                                <tbody>
                                <tr>
                                    <td class="formTitle">Enable webshell using shellinaboxd:</td>
                                    <td>
                                        <input type="checkbox" name="SSH_WebShell" value="1" {if $ssh->isShellInABoxEnabled()} checked{/if}>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="formTitle">Port:</td>
                                    <td>
                                        <input type="number" name="SSH_WebShell_Port" value="{$ssh->getShellInABoxPort()}">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="formTitle">Restrict access only to local subnets:</td>
                                    <td>
                                        <input type="checkbox" name="SSH_WebShell_RestrictAccess" value="1" {if $ssh->isShellInABoxRestricted()} checked{/if}>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="2"></td>
                                </tr>

                                <tr>
                                    <td class="formTitle">Enable OpenSSH Server:</td>
                                    <td>
                                        <input type="checkbox" name="SSH_OpenSSH" value="1" {if $ssh->isOpenSSHEnabled()} checked{/if}>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="formTitle" title="When lan address specified eg. 192.168.0.1, then only lan interfaces would be able to connect to SSH server. This could protect from attackers from internet.">Listen address:</td>
                                    <td>
                                        <input type="text" name="SSH_OpenSSH_ListenAddress" value="{$ssh->getListenAddress()}">
                                        <select id="interfaceIP">
                                            <option value="0.0.0.0">any</option>
                                            {foreach from="$interfaces->getInterfaces()" as $interface}
                                                {if !$interface->getIPAddress()}{continue}{/if}
                                                <option value="{$interface->getIPAddress()}">{$interface->getIPAddress()} ({$interface->getName()})</option>
                                            {/foreach}
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="formTitle">Port:</td>
                                    <td>
                                        <input type="number" name="SSH_OpenSSH_Port" value="{$ssh->getPort()}">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="formTitle">Protocol:</td>
                                    <td>
                                        <input type="radio" name="SSH_OpenSSH_Protocol" value="1" {if $ssh->getProtocol() === 1} checked{/if}> 1 <br/><input type="radio" name="SSH_OpenSSH_Protocol" value="2" {if $ssh->getProtocol() === 2} checked{/if}> 2
                                    </td>
                                </tr>
                                <tr>
                                    <td class="formTitle">Forward X11:</td>
                                    <td>
                                        <input type="checkbox" name="SSH_OpenSSH_X11Forwarding" value="1" {if $ssh->getX11Forwarding()} checked{/if}>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="formTitle">Forward TCP:</td>
                                    <td>
                                        <input type="checkbox" name="SSH_OpenSSH_TCPForwarding" value="1" {if $ssh->getTCPForwarding()} checked{/if}>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="formTitle">Print last login info:</td>
                                    <td>
                                        <input type="checkbox" name="SSH_OpenSSH_LastLog" value="1" {if $ssh->getLastLogPrinting()} checked{/if}>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="formTitle">Permit tunneling:</td>
                                    <td>
                                        <input type="checkbox" name="SSH_OpenSSH_PermitTunnel" value="1" {if $ssh->getPermitTunnel()} checked{/if}>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="formTitle">Allow root login:</td>
                                    <td>
                                        <input type="checkbox" name="SSH_OpenSSH_RootLogin" value="1" {if $ssh->getPermitRootLogin()} checked{/if}>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="formTitle">Max sessions:</td>
                                    <td>
                                        <input type="number" name="SSH_OpenSSH_MaxSessions" value="{$ssh->getMaxSessionsCount()}">
                                    </td>
                                </tr>
                                </tbody>
                            </table>

                            <button class="btn btn-success configureInterface" data-toggle="modal" data-target="#myModal">
                                Commit changes
                            </button>

                            {if $ssh->isShellInABoxEnabled()}
                                <a href="configureSSH?action=openShell">
                                    <input type="button" class="btn btn-primary configureInterface" value="Open web shell" style="margin-right: 10px;">
                                </a>
                            {/if}
                        </form>
                    </div>
            </div><!-- /.panel-body -->
        </div><!-- /.panel-default -->
    </div><!-- /.col-lg-12 -->
</div><!-- /.row -->
{include "Layout/Footer.tpl"}