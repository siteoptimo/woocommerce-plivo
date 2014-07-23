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
        })
    }
    $( "select option").each(function(){
        if($(this).is(':selected')){
            var slug = $(this).val();
            $('textarea#wcp_notification_message_'+slug).closest('tr').show();
        }else{
            var slug = $(this).val();
            $('textarea#wcp_notification_message_'+slug).closest('tr').hide();
        }
    });

    $('select#wcp_notification').change(function() {
        $( "select option").each(function(){
            if($(this).is(':selected')){
            var slug = $(this).val();
            $('textarea#wcp_notification_message_'+slug).closest('tr').show();
            }else{
                var slug = $(this).val();
                $('textarea#wcp_notification_message_'+slug).closest('tr').hide();
            }
        });

    });
})(jQuery);