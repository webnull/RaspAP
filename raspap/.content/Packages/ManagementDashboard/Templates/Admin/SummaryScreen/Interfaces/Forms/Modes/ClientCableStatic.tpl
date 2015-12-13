<tr class="role_client_cable_static role_all">
    <td class="formTitle">IP Address:</td>
    <td>
        <input type="text" name="CableStatic_IP" value="{$interface->getIPAddress()}">
    </td>
</tr>

<tr class="role_client_cable_static role_all">
    <td class="formTitle">Netmask:</td>
    <td>
        <input type="text" name="CableStatic_Netmask" value="{$interface->getNetmaskAddress()}">
    </td>
</tr>

<tr class="role_client_cable_static role_all">
    <td class="formTitle">Gateway:</td>
    <td>
        <input type="text" name="CableStatic_Gateway" value="{$interface->getGatewayAddress()}">
    </td>
</tr>

<tr class="role_client_cable_static role_all">
    <td class="formTitle">Broadcast:</td>
    <td>
        <input type="text" name="CableStatic_Broadcast" value="{$interface->getBroadcastAddress()}">
    </td>
</tr>