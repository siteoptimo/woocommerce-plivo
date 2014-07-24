(function ($) {
    var $submitButton = $("#wcp_send_button + .description .button");
    var $phoneNumber = $("#wcp_demo_phone_number");
    var $message = $("#wcp_demo_message");

    if ($submitButton.length > 0) {
        $submitButton.on('click', function (e) {
            e.preventDefault();

            $('a.button').after('<span class="loadbutton" style="position:relative;top:0.2em;"><img src="' + WCP.plugin_url + 'assets/images/wpspin_light.gif" style="position:relative;top:0.2em;padding-left:1em;padding-right:0.3em;">Sending...</span>');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    'action': 'wcp_send_message',
                    'to': $phoneNumber.val(),
                    'message': $message.val()
                },
                success: function (data) {
                    console.log(data);
                    $('span.loadbutton').html('<img src="' + WCP.plugin_url + 'assets/images/ok.png" style="position:relative;top:0.2em;padding-left:1em;padding-right:0.3em;"><span style="color:green;font-weight:bold">OK</span>');

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown);
                    $('span.loadbutton').html('<img src="' + WCP.plugin_url + 'assets/images/fail.png" style="position:relative;top:0.2em;padding-left:1em;padding-right:0.3em;"><span style="color:red;font-weight:bold">Error</span>');
                },
                dataType: 'JSON'

            });
        });
        $('select option').each(function () {
            if (!$(this).is(':selected')) {
                var slug = $(this).val();
                $('textarea#wcp_notification_message_' + slug).closest('tr').hide();
            }
        });

        $('select#wcp_notification').change(function () {
            $('select option').each(function () {
                var slug = $(this).val();
                if ($(this).is(':selected')) {
                    $('textarea#wcp_notification_message_' + slug).closest('tr').show();
                } else {
                    $('textarea#wcp_notification_message_' + slug).closest('tr').hide();
                }
            });
        });

        $("label[for='wcp_auth_id'],label[for='wcp_auth_password']").append('<sup style="color:red">*</sup>');
    }
})(jQuery);