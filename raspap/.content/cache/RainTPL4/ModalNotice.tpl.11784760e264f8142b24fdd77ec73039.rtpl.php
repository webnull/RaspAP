<?php if(!class_exists('Rain\RainTPL4')){exit;}?>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Commiting changes notice</h4>
            </div>
            <div class="modal-body">
                Please note that after applying changes interface could not get up.
                If you are connected to this machine through this interface then please consider fact that
                access to this machine could be cut off. Make sure you have a fallback interface configured, eg. a ethernet interface
                or a monitor and keyboard
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="cancelButton">Cancel</button>
                <button type="button" class="btn btn-primary" id="commitButton">Commit</button>
            </div>
        </div>
        
    </div>
    
</div>
