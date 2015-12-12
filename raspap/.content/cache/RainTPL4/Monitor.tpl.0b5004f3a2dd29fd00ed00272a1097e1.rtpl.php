<?php if(!class_exists('Rain\RainTPL4')){exit;}?><?php $stack=$interface->getLinuxNetworkStack();?>


<tr class="role_monitor role_all">
    <td class="formTitle">Filter:</td>
    <td><input type="text" name="Monitor_Filter" value="<?php print($stack->getFilter());?>" title="eg. dst portrange 80-81"></td>
</tr>

<tr class="role_monitor role_all">
    <?php $types=$stack->getPacketTypes();?>

    <td class="formTitle">Packets:</td>
    <td>
        <input type="checkbox" name="Monitor_PacketType[]" value="UDP"<?php if(($this->modifiers["in"]("UDP", $types))){?> checked<?php }?>> UDP
        <input type="checkbox" name="Monitor_PacketType[]" value="TCP"<?php if(($this->modifiers["in"]("TCP", $types))){?> checked<?php }?>> TCP
        <input type="checkbox" name="Monitor_PacketType[]" value="ICMP"<?php if(($this->modifiers["in"]("ICMP", $types))){?> checked<?php }?>> ICMP
        <input type="checkbox" name="Monitor_PacketType[]" value="Broadcast"<?php if(($this->modifiers["in"]("Broadcast", $types))){?> checked<?php }?>> Broadcast
        <input type="checkbox" name="Monitor_PacketType[]" value="Multicast"<?php if(($this->modifiers["in"]("Multicast", $types))){?> checked<?php }?>> Multicast
        <input type="checkbox" name="Monitor_PacketType[]" value="ARP"<?php if(($this->modifiers["in"]("ARP", $types))){?> checked<?php }?>> ARP
    </td>
</tr>

<tr class="role_monitor role_all">
    <td class="formTitle">Packet maximum size:</td>
    <td><input type="number" name="Monitor_PacketSize" value="<?php print($stack->getPacketSize());?>"> bytes</td>
</tr>