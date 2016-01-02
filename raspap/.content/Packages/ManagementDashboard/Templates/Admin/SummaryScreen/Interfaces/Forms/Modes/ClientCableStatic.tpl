<tr class="role_client_cable_static role_all">
    <td class="formTitle">IP Address:</td>
    <td>
        <input type="text" name="CableStatic_IP" value="{$interface->getLinuxNetworkStack()->getIPAddress()}">
    </td>
</tr>

<tr class="role_client_cable_static role_all">
    <td class="formTitle">Netmask:</td>
    <td>
        <input type="text" name="CableStatic_Netmask" value="{$interface->getLinuxNetworkStack()->getNetmaskAddress()}">
    </td>
</tr>

<tr class="role_client_cable_static role_all">
    <td class="formTitle">Gateway:</td>
    <td>
        <input type="text" name="CableStatic_Gateway" value="{$interface->getLinuxNetworkStack()->getGatewayAddress()}">
    </td>
</tr>

<tr class="role_client_cable_static role_all">
    <td class="formTitle">Broadcast:</td>
    <td>
        <input type="text" name="CableStatic_Broadcast" value="{$interface->getLinuxNetworkStack()->getBroadcastAddress()}">
    </td>
</tr>