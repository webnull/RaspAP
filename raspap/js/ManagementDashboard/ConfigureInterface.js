/**
 * Executes when "Role" <select> was changed
 */
function roleChanged()
{
    var value = $('select[name="InterfaceRole"]').val();
    $('.role_all').hide();
    $('.role_' + value).show();
}

/**
 * Executes when "Encryption" <select> was changed
 */
function AP_EncryptionChanged()
{
    var value = $('select[name="AP_Encryption"]').val();
    var option = $('option[value="' + value + '"]', $('select[name="AP_Encryption"]'));

    if (option.attr('data-passphrase'))
    {
        $('#wpa_passphrase_field').show();
    }
    else
    {
        $('#wpa_passphrase_field').hide();
    }
}

/**
 * Modal window events
 */
modalNotice = {
    'cancelButtonClicked': function () {
        $('#myModal').modal('hide');
    },

    'submitButtonClicked': function () {
        $.ajax({
            type: "POST",
            url: 'configureInterface,' + currentInterfaceName + '?action=commit&__returnType=json',
            data: $('form[name="interface_save_form"]').serialize(),
            success: function (data)
            {
                modalNotice.cancelButtonClicked();

                if (data.message)
                {
                    openResponseModal(data.message);
                }
            },
            dataType: 'json'
        });
    }
};



$(document).ready(function() {
    $('select[name="AP_Encryption"]').change(AP_EncryptionChanged); AP_EncryptionChanged();
    $('select[name="InterfaceRole"]').change(roleChanged); roleChanged();
    $('#cancelButton').click(modalNotice.cancelButtonClicked);
    $('#commitButton').click(modalNotice.submitButtonClicked);
});