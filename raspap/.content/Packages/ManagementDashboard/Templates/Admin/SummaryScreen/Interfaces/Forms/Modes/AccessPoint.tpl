<tr class="role_access_point role_all">
    <td class="formTitle">SSID:</td>
    <td><input type="text" name="AP_ESSID" value="{$interface->getHostAPD()->getName()}"></td>
</tr>

<tr class="role_access_point role_all">
    {$mode=$interface->getHostAPD()->getMode()}
    {$supportedModes=$interface->getHostAPD()->getSupportedModes()}
    <td class="formTitle">Mode:</td>
    <td>
        <select name="AP_Mode">
            {if 'b' in $supportedModes}<option value="b"{if $mode == 'b'} selected{/if}>B</option>{/if}
            {if 'g' in $supportedModes}<option value="g"{if $mode == 'g'} selected{/if}>G</option>{/if}
            {if 'g/n' in $supportedModes}<option value="g/n"{if $mode == 'g/n' || $mode == 'n'} selected{/if}>G/N</option>{/if}
            {if 'a' in $supportedModes}<option value="a"{if $mode == 'a'} selected{/if}>A</option>{/if}
        </select>
    </td>
</tr>

<tr class="role_access_point role_all">
    {$channel=$interface->getHostAPD()->getChannel()}
    <td class="formTitle">Channel:</td>
    <td>
        <select name="AP_Channel">
            <option value="0"{if $channel === 0} selected{/if}>Auto</option>

            {foreach from="range(1,14)" as $key => $number}
                <option value="{$number}"{if $channel === $number} selected{/if}>{$number}</option>
            {/foreach}
        </select>
    </td>
</tr>

<tr class="role_access_point role_all">
    {$encryption=$interface->getHostAPD()->getEncryptionType()}
    <td class="formTitle">Encryption:</td>
    <td>
        <select name="AP_Encryption">
            <option data-passphrase="1" value="WPA"{if $encryption == 'WPA'} selected{/if}>WPA</option>
            <option value="WPA2PerUserKey"{if $encryption == 'WPA2PerUserKey'} selected{/if}>WPA2 Per-user key</option>
            <option data-passphrase="1" value="WPA2SharedKey"{if $encryption == 'WPA2SharedKey'} selected{/if}>WPA2 Shared key</option>
            <option data-passphrase="1" value="WEP"{if $encryption == 'WEP'} selected{/if}>WEP</option>
            <option value="Open"{if $encryption == 'Open'} selected{/if}>No encryption</option>
        </select>
    </td>
</tr>

<tr class="role_access_point role_all" id="wpa_passphrase_field">
    <td class="formTitle">Passphrase:</td>
    <td>
        <input type="text" name="AP_Passphrase" value="{$interface->getHostAPD()->getPassphrase()}">
    </td>
</tr>

<tr class="role_access_point role_all">
    {$cipher=$interface->getHostAPD()->getKeyCipher()}
    <td class="formTitle">Key ciphers:</td>
    <td>
        <select name="AP_Pairwise">
            <option value="TKIP"{if $cipher == 'TKIP'} selected{/if}>TKIP</option>
            <option value="CCMP"{if $cipher == 'CCMP'} selected{/if}>CCMP</option>
            <option value="CCMP TKIP"{if $cipher == 'CCMP TKIP'} selected{/if}>CCMP + TKIP</option>
        </select>
    </td>
</tr>

<tr class="role_access_point role_all">
    <td class="formTitle">Hidden network:</td>
    <td>
        <input type="radio" name="AP_Hidden" value="0"{if !$interface->getHostAPD()->isHidden()} checked{/if}> No
        <input type="radio" name="AP_Hidden" value="1"{if $interface->getHostAPD()->isHidden()} checked{/if}> Yes
    </td>
</tr>

<tr class="role_access_point role_all">
    <td class="formTitle">Country code:</td>
    <td>
       <select name="AP_CountryCode">
           <!-- @todo Implement this list on server side https://en.wikipedia.org/wiki/ISO_3166-1 -->
           <option value=""{if $interface->getHostAPD()->getCountryCode() == ''} selected{/if}></option>
           <option value="PL"{if $interface->getHostAPD()->getCountryCode() == 'PL'} selected{/if}>Poland</option>
           <option value="US"{if $interface->getHostAPD()->getCountryCode() == 'US'} selected{/if}>United States</option>
           <option value="BR"{if $interface->getHostAPD()->getCountryCode() == 'BR'} selected{/if}>Brazil</option>
           <option value="GR"{if $interface->getHostAPD()->getCountryCode() == 'GR'} selected{/if}>Greece</option>
           <option value="FR"{if $interface->getHostAPD()->getCountryCode() == 'FR'} selected{/if}>France</option>
       </select>
    </td>
</tr>

<tr class="role_access_point role_all">
    <td class="formTitle">DFS:</td>
    <td>
        <input type="radio" name="AP_DFS" value="0"{if !$interface->getHostAPD()->getDFSValue()} checked{/if}> No
        <input type="radio" name="AP_DFS" value="1"{if $interface->getHostAPD()->getDFSValue()} checked{/if}> Yes
    </td>
</tr>

<tr class="role_access_point role_all">
    <td class="formTitle">Frame Protection:</td>
    <td>
        <input type="radio" name="AP_FrameProtection" value="0"{if !$interface->getHostAPD()->getFrameProtection()} checked{/if}> No
        <input type="radio" name="AP_FrameProtection" value="1"{if $interface->getHostAPD()->getFrameProtection()} checked{/if}> Yes
    </td>
</tr>