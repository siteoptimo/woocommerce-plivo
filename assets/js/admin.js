(function($) {

    var $submitButton = $("#wcp_send_button + .description .button");
    var $phoneNumber = $("#wcp_demo_phone_number");
    var $message = $("#wcp_demo_message");

    if($submitButton.length > 0)
    {
        $submitButton.on('click', function(e) {
            e.preventDefault();

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    'action': 'wcp_send_message',
                    'to': $phoneNumber.val(),
                    'message': $message.val()
                },
                success: function(data) {
                    console.log(data);
                },
                error: function(jqXHR, textStatus,errorThrown) {
                    console.log(errorThrown);
                },
                dataType: 'JSON'

            });


            console.log('clicked');
        })
    }

})(jQuery);