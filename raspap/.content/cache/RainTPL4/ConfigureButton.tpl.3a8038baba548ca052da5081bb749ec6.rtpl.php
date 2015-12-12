<?php if(!class_exists('Rain\RainTPL4')){exit;}?><?php if(!$hideDetailsButton){?>

    <a href="configureInterface,<?php print($interface->getName());?>">
        <input type="button" class="btn <?php if($interface->isConnected()){?>btn-success<?php }else{?>btn-primary<?php }?> configureInterface" value="Configure">
    </a>
<?php }?>