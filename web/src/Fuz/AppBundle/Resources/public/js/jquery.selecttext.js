
/*
 * Text selection
 *
 * Demo:
 * <a class="select-text" data-selector="#container-to-select">Select</a>
 */

// @see http://stackoverflow.com/questions/9975707/use-jquery-select-to-select-contents-of-a-div
;
(function ($) {

    $.fn.selectText = function () {

        this.find('input').each(function () {
            if ($(this).prev().length == 0 || !$(this).prev().hasClass('p_copy')) {
                $('<p class="p_copy" style="position: absolute; z-index: -1;"></p>').insertBefore($(this));
            }
            $(this).prev().html($(this).val());
        });
        var doc = document;
        var element = this[0];
        if (doc.body.createTextRange) {
            var range = document.body.createTextRange();
            range.moveToElementText(element);
            range.select();
        } else if (window.getSelection) {
            var selection = window.getSelection();
            var range = document.createRange();
            range.selectNodeContents(element);
            selection.removeAllRanges();
            selection.addRange(range);
        }
    };
})(jQuery);

$('body').delegate('.select-text', 'click', function (e) {
    var selector = $(this).data('selector');
    $(selector).selectText();
    e.preventDefault();
});