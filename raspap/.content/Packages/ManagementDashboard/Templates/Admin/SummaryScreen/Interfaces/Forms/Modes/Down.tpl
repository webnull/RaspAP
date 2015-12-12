{$stack=$interface->getLinuxNetworkStack()}

<tr class="role_down role_all">
    <td class="formTitle"></td>
    <td><input type="checkbox" name="Down_DontUse" value="1" {if !$stack->isUsed()} checked{/if}> Allow other network management applications to use this interface</td>
</tr>