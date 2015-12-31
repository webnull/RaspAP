<div class="formInput" style="margin-top: 0px;">
    {$roles=$interface->getPossibleRoles()}

    {if $roles}
        <form name="interface_save_form">
            <table class="formTable">
                <tbody>
                <tr>
                    <td class="formTitle">Role:</td>
                    <td>
                        <select name="InterfaceRole">
                            {if "client_cable_dhcp" in $roles}
                                <option value="client_cable_dhcp"{if $interface->getRole() == 'client_cable_dhcp'} selected{/if}>Connect using cable and dhcp</option>
                            {/if}

                            {if "client_cable_static" in $roles}
                                <option value="client_cable_static"{if $interface->getRole() == 'client_cable_static'} selected{/if}>Connect using cable and static address</option>
                            {/if}

                            {if "access_point" in $roles}
                                <option value="access_point"{if $interface->getRole() == 'access_point'} selected{/if}>Share network as router on lan/wlan</option>
                            {/if}

                            {if "monitor" in $roles}
                                <!-- ifconfig interface mode monitor -->
                                <!-- tcpdump ...-->
                                <option value="monitor"{if $interface->getRole() == 'monitor'} selected{/if}>Monitor packets</option>
                            {/if}

                            {if "down" in $roles}
                                <!-- ifconfig interface down -->
                                <option value="down"{if $interface->getRole() == 'down'} selected{/if}>Don't use this interface</option>
                            {/if}
                        </select>
                    </td>
                </tr>

                <!-- monitor -->
                {if "monitor" in $roles}
                    {include "Admin/SummaryScreen/Interfaces/Forms/Modes/Monitor.tpl"}
                {/if}

                <!-- client -->
                {if "client_cable_static" in $roles}
                    {include "Admin/SummaryScreen/Interfaces/Forms/Modes/ClientCableStatic.tpl"}
                {/if}

                <!-- access_point -->
                {if "access_point" in $roles}
                    {include "Admin/SummaryScreen/Interfaces/Forms/Modes/AccessPoint.tpl"}
                {/if}

                <!-- down -->
                {if "down" in $roles}
                    {include "Admin/SummaryScreen/Interfaces/Forms/Modes/Down.tpl"}
                {/if}
                </tbody>
            </table>
        </form>

        <button class="btn {if $interface->isConnected()}btn-success{else}btn-primary{/if} configureInterface" data-toggle="modal" data-target="#myModal">
            Commit changes
        </button>

        <a href="managePasswords,{$interface->getName()}">
            <button class="btn configureInterface interfaceButton">
                Manage passwords
            </button>
        </a>
    {/if}

{include "Admin/SummaryScreen/Interfaces/Forms/ModalNotice.tpl"}