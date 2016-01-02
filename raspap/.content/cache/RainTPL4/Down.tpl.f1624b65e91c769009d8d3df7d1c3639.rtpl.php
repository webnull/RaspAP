<?php if(!class_exists('Rain\RainTPL4')){exit;}?><?php $stack=$interface->getLinuxNetworkStack();?>


<tr class="role_down role_all">
    <td class="formTitle"></td>
    <td><input type="checkbox" name="Down_DontUse" value="1" <?php if(!$stack->isUsed()){?> checked<?php }?>> Allow other network management applications to use this interface</td>
</tr>