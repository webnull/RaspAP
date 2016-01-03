/**
 * Insert a predefined IP address from a <select> to <input name="SSH_OpenSSH_ListenAddress"> when <select> changed
 */
function interfaceIPChanged()
{
    var value = $('#interfaceIP').val();
    var input = $('input[name="SSH_OpenSSH_ListenAddress"]');

    input.val(value);
}

$(document).ready(function() {
    $('#interfaceIP').change(interfaceIPChanged);
});