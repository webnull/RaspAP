{"js/ManagementDashboard/TOR.js"|addJS}
{include "Layout/Header.tpl"}
{include "Admin/ModalResponse.tpl"}

{*} Display error message if any {/*}
{if isset($status) && is_string($status) && $status}
    <div class="alert alert-danger">
        <b>Configuration failed:</b> {$status}
    </div>
{/if}

{*} Refresh page after save {/*}
{if isset($status) && $status === true}
    <script>
        window.location.href = 'anonymitySettings';
    </script>
{/if}

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading"><i class="fa fa-eye-slash fa-fw"></i> The Onion Router setup</div>
            <div class="panel-body">
                    <form action="anonymitySettings" method="POST" id="torSetupForm">
                    <table class="formTable">
                        <tbody>
                        <tr>
                            <td class="formTitle">Use as:</td>
                            <td>
                                {$setupMode=$TOREntity->isEnabled()}
                                <select name="action">
                                    <option value="disableTOR"{if !$setupMode || $setupMode == 'disableTOR'} selected{/if}>Don't use TOR</option>
                                    <option value="setupRelay"{if $setupMode == 'relay'} selected{/if}>Relay or Exit Node</option>
                                    <option value="setupBridge"{if $setupMode == 'bridge'} selected{/if}>Bridge/Client only</option>
                                </select>
                            </td>
                        </tr>
                        <tr class="tor_setup_relay tor_setup">
                            <td class="formTitle" title="IPv4 address of this router accessible in internet, NOT A LOCAL ADDRESS">Global IPv4 Address:</td>
                            <td>
                                <input type="text" name="TOR_Address" value="{$TORConfiguration->getAddress()}">
                            </td>
                        </tr>
                        <tr class="tor_setup_relay tor_setup">
                            <td class="formTitle">TOR node nickname:</td>
                            <td>
                                <input type="text" name="TOR_Nickname" value="{$TORConfiguration->getNickName()}">
                            </td>
                        </tr>
                        <tr class="tor_setup_relay tor_setup">
                            <td class="formTitle">Bandwidth rate:</td>
                            <td>
                                <input type="number" name="TOR_Rate" value="{$TORConfiguration->getRelayBandwidthRate()}"> kb/s
                            </td>
                        </tr>
                        <tr class="tor_setup_relay tor_setup">
                            <td class="formTitle">Bandwidth burst rate:</td>
                            <td>
                                <input type="number" name="TOR_BurstRate" value="{$TORConfiguration->getRelayBandwidthBurst()}"> kb/s
                            </td>
                        </tr>
                        <tr class="tor_setup_relay tor_setup">
                            <td class="formTitle" title="You could allow TOR users to use your router as an exit node, this means they could operate using your public IP address on those ports. In some countries this could cause cops could look at you.">Allowed ports:</td>
                            <td>
                                <input type="text" name="TOR_ExitPolicy" value="{implode(',', $TORConfiguration->getAllowedPorts())}">
                            </td>
                        </tr>
                        <tr class="tor_default">
                            <td class="formTitle" title="Enable local HTTP proxy for browsing internet using TOR">Local HTTP Proxy:</td>
                            <td>
                                <input type="checkbox" name="TOR_UserProxy" value="1"{if $TOREntity->isProxyEnabled()} checked{/if}>
                            </td>
                        </tr>
                        <tr class="tor_default">
                            <td class="formTitle" title="To make TOR proxy available on the network please enter a lan address of this router, eg. 192.168.1.1, IP address you could lookup on a interface in main view">Proxy bind address:</td>
                            <td>
                                <input type="text" name="TOR_ProxyBindAddress" value="{$Privoxy->get('listen-address')}">
                                <select id="interfaceIP">
                                    <option value="localhost"></option>
                                    {foreach from="$interfaces->getInterfaces()" as $interface}
                                        {if !$interface->getIPAddress()}{continue}{/if}
                                        <option value="{$interface->getIPAddress()}">{$interface->getIPAddress()} ({$interface->getName()})</option>
                                    {/foreach}
                                </select>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <button type="button" class="btn btn-primary" id="TORFormSubmit" style="float: right;">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
{include "Layout/Footer.tpl"}