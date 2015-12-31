/**
 * Open a modal popup
 *
 * @param content
 * @param title
 */
function openResponseModal(content, title)
{
    $('.responseModal.modal-header').show();

    if (!title)
    {
        $('.responseModal.modal-header').hide();
    }

    $('.ajaxResponse').html(content);
    $('.ajaxResponseTitle').html(title);
    $('#responseModal').modal('show');
}

/**
 * Perform a more secure logout action
 */
function logoutSession()
{
    var sessionId = $('input[name="sessionId"]').val();
    $('body').append('<form action="login?action=logout" method="POST" id="logoutForm"><input type="hidden" name="logout" value="' + sessionId + '"></form>');
    $('#logoutForm').submit();
}

$(document).ready(function () {
   $('#logoutButton').click(logoutSession);
});