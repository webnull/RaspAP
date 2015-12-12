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