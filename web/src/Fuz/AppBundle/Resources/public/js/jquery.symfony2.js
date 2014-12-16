/*
 * jQuery Symfony2 utilities
 */
;
$.symfony2 = (function ($) {

    var _entityMap = {
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': '&quot;',
        "'": '&#39;',
        "/": '&#x2F;'
    };

    var _escapeHtml = function(string) {
        return String(string).replace(/[&<>"'\/]/g, function (s) {
            return _entityMap[s];
        });
    };

    /*
     * Dispatch form validation errors at their right location when form was
     * submit using ajax.
     *
     * @param object errors
     *      Object containing fields id as key, and an array of errors as error messages
     * @param string generalSelector
     *      Selector that will contain general errors
     * @param string specificSelectorPrefix
     *      Prefix for field-specific error containers
     * @param string allErrorsSelector
     *      Selector used by all error containers, so they can be cleaned if error was fixed
     * @see Fuz\AppBundle\BaseController::getErrorMessagesAjaxFormat
     */
    var dispatchErrors = function (errors, generalSelector, specificSelectorPrefix, allErrorsSelector) {

        if (undefined === allErrorsSelector) {
            allErrorsSelector = '.error-container';
        }

        $(allErrorsSelector).html('');

        if (undefined === errors) {
            return;
        }

        if (undefined === generalSelector) {
            generalSelector = '#general-errors';
        }
        if (undefined === specificSelectorPrefix) {
            specificSelectorPrefix = '#errors-';
        }

        if (($(generalSelector).length > 0) && (undefined !== errors['#'])) {
            var html = '<div class="row" id="errors"><div class="col-md-12 alert alert-danger text-center">';
            $.each(errors['#'], function (index, error) {
                html += '<div class="error">' + _escapeHtml(error) + '</div>';
            });
            html += '</div><div>&nbsp;</div></div>';
            $(generalSelector).html(html);
        }

        $.each(errors, function (id, fieldErrors) {
            if (('#' !== id) && ($(specificSelectorPrefix + id).length > 0)) {
                var html = '';
                $.each(fieldErrors, function (key, value) {
                    html += '<div class="control-label text-danger">' + _escapeHtml(value) + '</div>';
                });
                $(specificSelectorPrefix + id).html(html);
            }
        });

    }; // dispatchErrors

    return {
        dispatchErrors: dispatchErrors
    };

})(jQuery); // $.symfony2

