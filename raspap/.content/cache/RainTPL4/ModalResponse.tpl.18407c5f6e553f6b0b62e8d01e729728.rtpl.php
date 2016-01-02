<?php if(!class_exists('Rain\RainTPL4')){exit;}?>

<div class="modal fade" id="responseModal" tabindex="-1" role="dialog" aria-labelledby="responseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header responseModal">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title ajaxResponseTitle" id="responseModalLabel"></h4>
            </div>
            <div class="modal-body ajaxResponse">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" onclick="$('#responseModal').modal('hide');" id="modalHideButton">Close</button>
            </div>
        </div>
        
    </div>
    
</div>
