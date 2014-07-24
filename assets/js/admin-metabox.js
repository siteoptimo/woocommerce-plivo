/**
 * @author Koen Van den Wijngaert <koen@siteoptimo.com>
 * @package WooCommerce_Plivo
 */
(function ($) {
    var $noteTypeSelect = $("#order_note_type");
    var $noteTextArea = $("#add_order_note");

    if($noteTypeSelect.length) {
        var checkbox = '<p><label><input type="checkbox" name="order_note_sms" value="1" id="order_note_sms"> ' + WCP_Metabox.send_as_sms + '</label>';

        $noteTextArea.closest('p').after(checkbox);
    }

    $("a.add_note").on('click', function (e) {
        // Quickly prepend the message with [SMS] before the form is being handled by WooCommerce.
        if($("#order_note_sms").prop('checked')) {
            $noteTextArea.val('[SMS] ' + $noteTextArea.val());
        }
    });

})(jQuery);