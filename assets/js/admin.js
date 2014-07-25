/**
 * @author SiteOptimo <team@siteoptimo.com>
 * @package WooCommerce_Plivo
 */
(function ($) {
    var $submitButton = $("#wcp_send_button + .description .button");
    var $phoneNumber = $("#wcp_demo_phone_number");
    var $message = $("#wcp_demo_message");

    if ($submitButton.length > 0) {
        $submitButton.on('click', function (e) {
            e.preventDefault();

            // Add loading gif.
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
                    if (data) { // Success!
                        $('span.loadbutton').html('<img src="' + WCP.plugin_url + 'assets/images/ok.png" style="position:relative;top:0.2em;padding-left:1em;padding-right:0.3em;"><span style="color:green;font-weight:bold">Sent</span>');
                    } else { // Failure :(
                        $('span.loadbutton').html('<img src="' + WCP.plugin_url + 'assets/images/fail.png" style="position:relative;top:0.2em;padding-left:1em;padding-right:0.3em;"><span style="color:red;font-weight:bold">Error</span>');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $('span.loadbutton').html('<img src="' + WCP.plugin_url + 'assets/images/fail.png" style="position:relative;top:0.2em;padding-left:1em;padding-right:0.3em;"><span style="color:red;font-weight:bold">Error</span>');
                },
                dataType: 'JSON'

            });
        });

    }

    var $notification = $("#wcp_notification");

    if($notification.length > 0) {

        var $options = $notification.find("option");

        // First hide all unneeded textareas.
        $options.each(function () {
            if (!$(this).is(':selected')) {
                var slug = $(this).val();
                $('textarea#wcp_notification_message_' + slug).closest('tr').hide();
            }
        });

        // Next, show and hide matching notification message textareas.
        $notification.change(function () {
            $options.each(function () {
                var slug = $(this).val();
                if ($(this).is(':selected')) {
                    $('textarea#wcp_notification_message_' + slug).closest('tr').show();
                } else {
                    $('textarea#wcp_notification_message_' + slug).closest('tr').hide();
                }
            });
        });

        // Add asterisk to required field labels.
        $("label[for='wcp_auth_id'],label[for='wcp_auth_password'],label[for='wcp_from_number']").append('<sup style="color:red">*</sup>');
    }
})(jQuery);