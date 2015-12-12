<?php if(!class_exists('Rain\RainTPL4')){exit;}?><?php echo $this->modifiers["addJS"]("js/ManagementDashboard/TOR.js");?>

<?php extract($this->variables); require $this->checkTemplate("Layout/Header.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/TOR/Main.tpl", 2, 40);?>

<?php extract($this->variables); require $this->checkTemplate("Admin/ModalResponse.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/TOR/Main.tpl", 3, 70);?>



<?php if(isset($status) && is_string($status) && $status){?>

    <div class="alert alert-danger">
        <b>Configuration failed:</b> <?php print($status);?>

    </div>
<?php }?>



<?php if(isset($status) && $status === true){?>

    <script>
        window.location.href = 'anonymitySettings';
    </script>
<?php }?>


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
                                <?php $setupMode=$TOREntity->isEnabled();?>

                                <select name="action">
                                    <option value="disableTOR"<?php if(!$setupMode || $setupMode == 'disableTOR'){?> selected<?php }?>>Don't use TOR</option>
                                    <option value="setupRelay"<?php if($setupMode == 'relay'){?> selected<?php }?>>Relay or Exit Node</option>
                                    <option value="setupBridge"<?php if($setupMode == 'bridge'){?> selected<?php }?>>Bridge/Client only</option>
                                </select>
                            </td>
                        </tr>
                        <tr class="tor_setup_relay tor_setup">
                            <td class="formTitle" title="IPv4 address of this router accessible in internet, NOT A LOCAL ADDRESS">Global IPv4 Address:</td>
                            <td>
                                <input type="text" name="TOR_Address" value="<?php print($TORConfiguration->getAddress());?>">
                            </td>
                        </tr>
                        <tr class="tor_setup_relay tor_setup">
                            <td class="formTitle">TOR node nickname:</td>
                            <td>
                                <input type="text" name="TOR_Nickname" value="<?php print($TORConfiguration->getNickName());?>">
                            </td>
                        </tr>
                        <tr class="tor_setup_relay tor_setup">
                            <td class="formTitle">Bandwidth rate:</td>
                            <td>
                                <input type="number" name="TOR_Rate" value="<?php print($TORConfiguration->getRelayBandwidthRate());?>"> kb/s
                            </td>
                        </tr>
                        <tr class="tor_setup_relay tor_setup">
                            <td class="formTitle">Bandwidth burst rate:</td>
                            <td>
                                <input type="number" name="TOR_BurstRate" value="<?php print($TORConfiguration->getRelayBandwidthBurst());?>"> kb/s
                            </td>
                        </tr>
                        <tr class="tor_setup_relay tor_setup">
                            <td class="formTitle" title="You could allow TOR users to use your router as an exit node, this means they could operate using your public IP address on those ports. In some countries this could cause cops could look at you.">Allowed ports:</td>
                            <td>
                                <input type="text" name="TOR_ExitPolicy" value="<?php echo implode(',', $TORConfiguration->getAllowedPorts());?>">
                            </td>
                        </tr>
                        <tr class="tor_default">
                            <td class="formTitle" title="Enable local HTTP proxy for browsing internet using TOR">Local HTTP Proxy:</td>
                            <td>
                                <input type="checkbox" name="TOR_UserProxy" value="1"<?php if($TOREntity->isProxyEnabled()){?> checked<?php }?>>
                            </td>
                        </tr>
                        <tr class="tor_default">
                            <td class="formTitle" title="To make TOR proxy available on the network please enter a lan address of this router, eg. 192.168.1.1, IP address you could lookup on a interface in main view">Proxy bind address:</td>
                            <td>
                                <input type="text" name="TOR_ProxyBindAddress" value="<?php print($Privoxy->get('listen-address'));?>">
                                <select id="interfaceIP">
                                    <option value="localhost"></option>
                                    <?php $counter1=-1; $newVar=$interfaces->getInterfaces(); if(isset($newVar)&&(is_array($newVar)||$newVar instanceof Traversable)&& sizeof($newVar))foreach($newVar as $key1 => $interface){ $counter1++; ?>

                                        <?php if(!$interface->getIPAddress()){ continue; }?>

                                        <option value="<?php print($interface->getIPAddress());?>"><?php print($interface->getIPAddress());?> (<?php print($interface->getName());?>)</option>
                                    <?php }?>

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
<?php extract($this->variables); require $this->checkTemplate("Layout/Footer.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/TOR/Main.tpl", 96, 5434);?>