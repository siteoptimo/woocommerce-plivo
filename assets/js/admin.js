/**
 * Created by pieter on 23/07/14.
 */

(function($){
    $('textarea.optional_textarea').closest('tr').hide();

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