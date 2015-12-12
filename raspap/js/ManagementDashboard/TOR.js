/**
 * Executes when "Role" <select> was changed
 */
function roleChanged()
{
    var value = $('select[name="action"]').val();

    $('#torSetupForm').attr('action', 'anonymitySettings?action=' + value);
    $('.tor_setup').hide();
    $('.tor_default').show();

    if (value === 'disableTOR')
    {
        $('.tor_default').hide();
    }

    $('.tor_setup_' + value.replace('setup', '').toLowerCase()).show();
}

/**
 * Insert a predefined IP address from a <select> to <input name="TOR_ProxyBindAddress"> when <select> changed
 */
function interfaceIPChanged()
{
    var value = $('#interfaceIP').val();
    var input = $('input[name="TOR_ProxyBindAddress"]');
    var split = input.val().split(':');
    var port = split[1];

    if (!port)
    {
        port = '8118';
    }

    input.val(value + ':' + port);
}

/**
 * Executes when clicking "Save" on TOR configuration form
 */
function TORFormSubmitted()
{
    openResponseModal('Changes will be applied by background daemon up to few minutes, please be patient as the TOR client is taking some time to connect to the network');

    $('#modalHideButton').html('Confirm save');
    $('#modalHideButton').click(function (e) {
        e.preventDefault();
        $('#torSetupForm').submit();
        return false;
    });
}

$(document).ready(function() {
    $('select[name="action"]').change(roleChanged); roleChanged();
    $('#interfaceIP').change(interfaceIPChanged);
    $('#TORFormSubmit').click(TORFormSubmitted);
});