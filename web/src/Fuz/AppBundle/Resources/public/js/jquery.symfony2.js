/*
 * jQuery Symfony2 utilities
 */
;
(function ($, windowObj) {

    $.symfony2 = function () {

        /*
         * Dispatch form validation errors at their right location when form was
         * submit using ajax.
         *
         * General errors container should have id="general-errors"
         * Specific errors containers should have id="errors-<field's id>"
         *
         * @see Fuz\AppBundle\BaseController::getErrorMessagesAjaxFormat
         */
        dispatchErrors = function (errors) {

            var entityMap = {
                "&": "&amp;",
                "<": "&lt;",
                ">": "&gt;",
                '"': '&quot;',
                "'": '&#39;',
                "/": '&#x2F;'
            };

            function escapeHtml(string) {
                return String(string).replace(/[&<>"'\/]/g, function (s) {
                    return entityMap[s];
                });
            }

            if (($("#general-errors").length > 0) && (errors['#'] !== undefined)) {
                var html = '<div class="row" id="errors"><div class="col-md-12 alert alert-danger text-center">';
                $.each(errors['#'], function (index, error) {
                    html += '<div class="error">' + escapeHtml(error) + '</div>';
                });
                html += '</div><div>&nbsp;</div></div>';
                $('#general-errors').html(html);
            }

            $.each(errors, function (id, fieldErrors) {
                if (('#' !== id) && ($('#errors-' + id).length > 0)) {
                    var html = '';
                    $.each(fieldErrors, function (key, value) {
                        html += '<div class="control-label text-danger">' + escapeHtml(value) + '</div>';
                    });
                    $('#errors-' + id).html(html);
                }
            });

        }; // dispatchErrors

    }; // $.symfony2

})(jQuery, window);
