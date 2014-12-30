
/*
 * Text selection
 *
 * Demo:
 * <a class="select-text" data-selector="#container-to-select">Select</a>
 *
 * @see http://stackoverflow.com/a/987376/731138
 * @todo not working on input fields
 */
;
(function ($) {

    $.fn.selectText = function () {

        var RandomNumber = function () {
            var rand = '' + Math.random() * 1000 * new Date().getTime();
            return rand.replace('.', '').split('').sort(function () {
                return 0.5 - Math.random();
            }).join('');
        };

        var GetOrCreateId = function ( jqElement ) {
            if (!jqElement.attr('id')) {
                var generated_id;
                do {
                    generated_id = 'i' + RandomNumber();
                } while ($('#' + generated_id).length > 0);
                jqElement.attr('id', generated_id);
            }
            return jqElement.attr('id');
        };

        var SelectText = function( element ) {
            var doc = document
                , text = doc.getElementById(element)
                , range, selection
            ;
            if (doc.body.createTextRange) {
                range = document.body.createTextRange();
                range.moveToElementText(text);
                range.select();
            } else if (window.getSelection) {
                selection = window.getSelection();
                range = document.createRange();
                range.selectNodeContents(text);
                selection.removeAllRanges();
                selection.addRange(range);
            }
        };

        SelectText( GetOrCreateId ( $(this) ) );

    };

})(jQuery);

$('body').delegate('.select-text', 'click', function (e) {
    var selector = $(this).data('selector');
    $(selector).selectText();
    e.preventDefault();
});
