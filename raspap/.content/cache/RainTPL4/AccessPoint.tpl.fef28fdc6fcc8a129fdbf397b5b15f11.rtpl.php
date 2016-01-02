<?php if(!class_exists('Rain\RainTPL4')){exit;}?><tr class="role_access_point role_all">
    <td class="formTitle">SSID:</td>
    <td><input type="text" name="AP_ESSID" value="<?php print($interface->getHostAPD()->getName());?>"></td>
</tr>

<tr class="role_access_point role_all">
    <?php $mode=$interface->getHostAPD()->getMode();?>

    <?php $supportedModes=$interface->getHostAPD()->getSupportedModes();?>

    <td class="formTitle">Mode:</td>
    <td>
        <select name="AP_Mode">
            <?php if(($this->modifiers["in"]('b', $supportedModes))){?><option value="b"<?php if($mode == 'b'){?> selected<?php }?>>B</option><?php }?>

            <?php if(($this->modifiers["in"]('g', $supportedModes))){?><option value="g"<?php if($mode == 'g'){?> selected<?php }?>>G</option><?php }?>

            <?php if(($this->modifiers["in"]('g/n', $supportedModes))){?><option value="g/n"<?php if($mode == 'g/n' || $mode == 'n'){?> selected<?php }?>>G/N</option><?php }?>

            <?php if(($this->modifiers["in"]('a', $supportedModes))){?><option value="a"<?php if($mode == 'a'){?> selected<?php }?>>A</option><?php }?>

        </select>
    </td>
</tr>

<tr class="role_access_point role_all">
    <?php $channel=$interface->getHostAPD()->getChannel();?>

    <td class="formTitle">Channel:</td>
    <td>
        <select name="AP_Channel">
            <option value="0"<?php if($channel === 0){?> selected<?php }?>>Auto</option>

            <?php $counter1=-1; $newVar=range(1,14); if(isset($newVar)&&(is_array($newVar)||$newVar instanceof Traversable)&& sizeof($newVar))foreach($newVar as $key => $number){ $counter1++; ?>

                <option value="<?php print($number);?>"<?php if($channel === $number){?> selected<?php }?>><?php print($number);?></option>
            <?php }?>

        </select>
    </td>
</tr>

<tr class="role_access_point role_all">
    <td class="formTitle" alt="Create a single subnet for multiple interfaces (bridge mode). For example wireless and cable connected users could see each other.">Link with interfaces:</td>
    <td>
        <?php $counter1=-1; $newVar=$interfaces; if(isset($newVar)&&(is_array($newVar)||$newVar instanceof Traversable)&& sizeof($newVar))foreach($newVar as $key1 => $w){ $counter1++; ?>

            <?php if($w->getName() == $interface->getName() || !$w->canBeUsedInBridge()){ continue; }?>

            <input type="checkbox" name="AP_BridgeInterfaces[]" value="<?php print($w->getName());?>" <?php if($interface->getHostAPD()->isInBridgeWith($w)){?> checked <?php }?>> <?php print($w->getName());?><br>
        <?php }?>

    </td>
</tr>

<tr class="role_access_point role_all">
    <?php $encryption=$interface->getHostAPD()->getEncryptionType();?>

    <td class="formTitle">Encryption:</td>
    <td>
        <select name="AP_Encryption">
            <option data-passphrase="1" value="WPA"<?php if($encryption == 'WPA'){?> selected<?php }?>>WPA</option>
            <option value="WPA2PerUserKey"<?php if($encryption == 'WPA2PerUserKey'){?> selected<?php }?>>WPA2 Per-user key</option>
            <option data-passphrase="1" value="WPA2SharedKey"<?php if($encryption == 'WPA2SharedKey'){?> selected<?php }?>>WPA2 Shared key</option>
            <option data-passphrase="1" value="WEP"<?php if($encryption == 'WEP'){?> selected<?php }?>>WEP</option>
            <option value="Open"<?php if($encryption == 'Open'){?> selected<?php }?>>No encryption</option>
        </select>
    </td>
</tr>

<tr class="role_access_point role_all" id="wpa_passphrase_field">
    <td class="formTitle">Passphrase:</td>
    <td>
        <input type="text" name="AP_Passphrase" value="<?php print($interface->getPassword());?>">
    </td>
</tr>

<tr class="role_access_point role_all">
    <?php $cipher=$interface->getHostAPD()->getKeyCipher();?>

    <td class="formTitle">Key ciphers:</td>
    <td>
        <select name="AP_Pairwise">
            <option value="TKIP"<?php if($cipher == 'TKIP'){?> selected<?php }?>>TKIP</option>
            <option value="CCMP"<?php if($cipher == 'CCMP'){?> selected<?php }?>>CCMP</option>
            <option value="CCMP TKIP"<?php if($cipher == 'CCMP TKIP'){?> selected<?php }?>>CCMP + TKIP</option>
        </select>
    </td>
</tr>

<tr class="role_access_point role_all">
    <td class="formTitle">Hidden network:</td>
    <td>
        <input type="radio" name="AP_Hidden" value="0"<?php if(!$interface->getHostAPD()->isHidden()){?> checked<?php }?>> No
        <input type="radio" name="AP_Hidden" value="1"<?php if($interface->getHostAPD()->isHidden()){?> checked<?php }?>> Yes
    </td>
</tr>

<tr class="role_access_point role_all">
    <td class="formTitle">Country code:</td>
    <td>
       <select name="AP_CountryCode">
           
           <option value=""<?php if($interface->getHostAPD()->getCountryCode() == ''){?> selected<?php }?>></option>
           <option value="PL"<?php if($interface->getHostAPD()->getCountryCode() == 'PL'){?> selected<?php }?>>Poland</option>
           <option value="US"<?php if($interface->getHostAPD()->getCountryCode() == 'US'){?> selected<?php }?>>United States</option>
           <option value="BR"<?php if($interface->getHostAPD()->getCountryCode() == 'BR'){?> selected<?php }?>>Brazil</option>
           <option value="GR"<?php if($interface->getHostAPD()->getCountryCode() == 'GR'){?> selected<?php }?>>Greece</option>
           <option value="FR"<?php if($interface->getHostAPD()->getCountryCode() == 'FR'){?> selected<?php }?>>France</option>
       </select>
    </td>
</tr>

<tr class="role_access_point role_all">
    <td class="formTitle">DFS:</td>
    <td>
        <input type="radio" name="AP_DFS" value="0"<?php if(!$interface->getHostAPD()->getDFSValue()){?> checked<?php }?>> No
        <input type="radio" name="AP_DFS" value="1"<?php if($interface->getHostAPD()->getDFSValue()){?> checked<?php }?>> Yes
    </td>
</tr>

<tr class="role_access_point role_all">
    <td class="formTitle">Frame Protection:</td>
    <td>
        <input type="radio" name="AP_FrameProtection" value="0"<?php if(!$interface->getHostAPD()->getFrameProtection()){?> checked<?php }?>> No
        <input type="radio" name="AP_FrameProtection" value="1"<?php if($interface->getHostAPD()->getFrameProtection()){?> checked<?php }?>> Yes
    </td>
</tr>