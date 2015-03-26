/*globals jQuery,fvModalId,fvAjaxSubmit,fvCallbacks*/

/**
 * Configure the modal form
 *
 * @param title
 * @param url
 * @param formId
 */
function modalForm(title, url, formId) {
    "use strict";

    jQuery("#" + fvModalId + " .modal-header span").html(title);
    jQuery("#" + fvModalId + " .loading").removeClass("hide");

    jQuery.ajax({
        url: url,
        success: function (data) {
            jQuery("#" + fvModalId + " .body .loading").addClass("hide");
            var form = jQuery(data).find("#" + formId),
                formHtml,
                page;

            // Check if form exists
            if (form.length === 0) {
                console.log("Error: form not found");
                return;
            }
            formHtml = form.prop('outerHTML');

            // Output the scripts (for validation)
            page = jQuery(data);
            page.filter('script').each(function () {
                formHtml += jQuery(this).prop('outerHTML');
            });

            // Need to output css as well (or styling is lost)
            page.filter('link[rel="stylesheet"]').each(function () {
                formHtml = jQuery(this).prop('outerHTML') + formHtml;
            });

            // Output on to the page
            jQuery("#" + fvModalId + " .body .form").html(formHtml);
            jQuery(document).trigger('fvModalLoaded');
        }
    });


}

/**
 * When the form has been submitted, close the box
 */
function modalFormSubmit() {
    "use strict";

    var form = jQuery(this),
        id = form.attr("id"),
        action = form.attr("action"),
        method = form.attr("method");

    // Return true if not ajax submit
    if (fvAjaxSubmit === undefined || fvAjaxSubmit[id] === undefined || fvAjaxSubmit[id] !== true) {
        return true;
    }

    jQuery.ajax({
        type: method,
        url: action,
        data: form.serialize(),
        success: function (data) {
            if (fvCallbacks && fvCallbacks[id] && typeof fvCallbacks[id].success === 'function') {
                var fn = fvCallbacks[id].success;
                fn(data);
            }

            jQuery(document).trigger('onFvModalFormSubmitted', [id, data]);
            jQuery("#" + fvModalId).modal('hide');
        },
        error: function (data) {
            if (fvCallbacks && fvCallbacks[id] && typeof fvCallbacks[id].error === 'function') {
                var fn = fvCallbacks[id].error;
                fn(data);
            }

            jQuery("#" + fvModalId).modal('hide');
        }
    });

    return false;
}

(function ($) {
    "use strict";

    // Forms can trigger the modal complete event to hide
    $(document).on('fvModalComplete', function () {
        jQuery("#" + fvModalId).modal('hide');
    });
}(jQuery));