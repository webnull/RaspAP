{if !$hideDetailsButton}
    <a href="configureInterface,{$interface->getName()}">
        <input type="button" class="btn {if $interface->isConnected()}btn-success{else}btn-primary{/if} configureInterface" value="Configure">
    </a>
{/if}