<?php if(!class_exists('Rain\RainTPL4')){exit;}?><div class="formInput" style="margin-top: 0px;">
    <?php $roles=$interface->getPossibleRoles();?>


    <?php if($roles){?>

        <form name="interface_save_form">
            <table class="formTable">
                <tbody>
                <tr>
                    <td class="formTitle">Role:</td>
                    <td>
                        <select name="InterfaceRole">
                            <?php if(($this->modifiers["in"]("client_cable_dhcp", $roles))){?>

                                <option value="client_cable_dhcp"<?php if($interface->getRole() == 'client_cable_dhcp'){?> selected<?php }?>>Connect using cable and dhcp</option>
                            <?php }?>


                            <?php if(($this->modifiers["in"]("client_cable_static", $roles))){?>

                                <option value="client_cable_static"<?php if($interface->getRole() == 'client_cable_static'){?> selected<?php }?>>Connect using cable and static address</option>
                            <?php }?>


                            <?php if(($this->modifiers["in"]("access_point", $roles))){?>

                                <option value="access_point"<?php if($interface->getRole() == 'access_point'){?> selected<?php }?>>Share network as router on lan/wlan</option>
                            <?php }?>


                            <?php if(($this->modifiers["in"]("monitor", $roles))){?>

                                
                                
                                <option value="monitor"<?php if($interface->getRole() == 'monitor'){?> selected<?php }?>>Monitor packets</option>
                            <?php }?>


                            <?php if(($this->modifiers["in"]("down", $roles))){?>

                                
                                <option value="down"<?php if($interface->getRole() == 'down'){?> selected<?php }?>>Don't use this interface</option>
                            <?php }?>

                        </select>
                    </td>
                </tr>

                
                <?php if(($this->modifiers["in"]("monitor", $roles))){?>

                    <?php extract($this->variables); require $this->checkTemplate("Admin/SummaryScreen/Interfaces/Forms/Modes/Monitor.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/ConfigureInterface/FormInput.tpl", 40, 1873);?>

                <?php }?>


                
                <?php if(($this->modifiers["in"]("client_cable_static", $roles))){?>

                    <?php extract($this->variables); require $this->checkTemplate("Admin/SummaryScreen/Interfaces/Forms/Modes/ClientCableStatic.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/ConfigureInterface/FormInput.tpl", 45, 2053);?>

                <?php }?>


                
                <?php if(($this->modifiers["in"]("access_point", $roles))){?>

                    <?php extract($this->variables); require $this->checkTemplate("Admin/SummaryScreen/Interfaces/Forms/Modes/AccessPoint.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/ConfigureInterface/FormInput.tpl", 50, 2236);?>

                <?php }?>


                
                <?php if(($this->modifiers["in"]("down", $roles))){?>

                    <?php extract($this->variables); require $this->checkTemplate("Admin/SummaryScreen/Interfaces/Forms/Modes/Down.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/ConfigureInterface/FormInput.tpl", 55, 2405);?>

                <?php }?>

                </tbody>
            </table>
        </form>

        <button class="btn <?php if($interface->isConnected()){?>btn-success<?php }else{?>btn-primary<?php }?> configureInterface" data-toggle="modal" data-target="#myModal">
            Commit changes
        </button>

        <a href="managePasswords,<?php print($interface->getName());?>">
            <button class="btn configureInterface interfaceButton">
                Manage passwords
            </button>
        </a>
    <?php }?>


<?php extract($this->variables); require $this->checkTemplate("Admin/SummaryScreen/Interfaces/Forms/ModalNotice.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/ConfigureInterface/FormInput.tpl", 72, 2961);?>