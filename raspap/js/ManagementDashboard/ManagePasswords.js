/**
 * Remove MAC Address from the list
 *
 * @param e
 */
function removeMac(e)
{
    var mac = jQuery(e).parent().parent().find('input[name="PSK_Mac"]').val();

    $.ajax({
        type: "POST",
        url: 'managePasswords,' + currentInterfaceName + '?action=removeMachine&__returnType=json',
        data: 'PSK_Mac=' + mac,
        success: function (data)
        {
            if (data.statusMessage)
            {
                alert(data.statusMessage);
            }
            else
            {
                jQuery(e).parent().parent().remove();
            }
        },
        dataType: 'json'
    });
}

/**
 * Executes when MAC Address field was changed
 */
function PSKMacChanged(e)
{
    // data-origina-mac
    var mac = jQuery(e).parent().parent().find('input[name="PSK_Mac"]');
    var saveButton = jQuery(e).parent().parent().find('.saveButton');

    if (mac.attr('data-origina-mac') && mac.val() != mac.attr('data-origina-mac'))
    {
        saveButton.html('Duplicate');
    }
    else
    {
        saveButton.html('Save');
    }
}