(function($) {
    var $noteTypeSelect = $("#order_note_type");
    var $noteTextArea = $("#add_order_note");

    if($noteTypeSelect.length) {
        var checkbox = '<p><label><input type="checkbox" name="order_note_sms" value="1" id="order_note_sms"> Send as SMS?</label>';

        $noteTextArea.closest('p').after(checkbox);
    }

    $("a.add_note").on('click', function(e) {
        if($("#order_note_sms").prop('checked')) {
            $noteTextArea.val('[SMS] ' + $noteTextArea.val());
        }
    });

})(jQuery);