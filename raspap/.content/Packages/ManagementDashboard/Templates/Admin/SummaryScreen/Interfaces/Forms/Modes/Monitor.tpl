{$stack=$interface->getLinuxNetworkStack()}

<tr class="role_monitor role_all">
    <td class="formTitle">Filter:</td>
    <td><input type="text" name="Monitor_Filter" value="{$stack->getFilter()}" title="eg. dst portrange 80-81"></td>
</tr>

<tr class="role_monitor role_all">
    {$types=$stack->getPacketTypes()}
    <td class="formTitle">Packets:</td>
    <td>
        <input type="checkbox" name="Monitor_PacketType[]" value="UDP"{if "UDP" in $types} checked{/if}> UDP
        <input type="checkbox" name="Monitor_PacketType[]" value="TCP"{if "TCP" in $types} checked{/if}> TCP
        <input type="checkbox" name="Monitor_PacketType[]" value="ICMP"{if "ICMP" in $types} checked{/if}> ICMP
        <input type="checkbox" name="Monitor_PacketType[]" value="Broadcast"{if "Broadcast" in $types} checked{/if}> Broadcast
        <input type="checkbox" name="Monitor_PacketType[]" value="Multicast"{if "Multicast" in $types} checked{/if}> Multicast
        <input type="checkbox" name="Monitor_PacketType[]" value="ARP"{if "ARP" in $types} checked{/if}> ARP
    </td>
</tr>

<tr class="role_monitor role_all">
    <td class="formTitle">Packet maximum size:</td>
    <td><input type="number" name="Monitor_PacketSize" value="{$stack->getPacketSize()}"> bytes</td>
</tr>