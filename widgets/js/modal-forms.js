(function ($) {
    $(document).ready(function () {

    });
}(jQuery));

/**
 * Configure the modal form
 *
 * @param title
 * @param url
 * @param formId
 */
function modalForm(title, url, formId) {
    jQuery("#" + fvModalId + " .modal-header span").html(title);
    jQuery("#" + fvModalId + " .loading").removeClass("hide");

    jQuery.ajax({
        url: url,
        success: function (data) {
            jQuery("#" + fvModalId + " .body .loading").addClass("hide");
            var form = jQuery(data).find("#" + formId);

            // Check if form exists
            if (form.length === 0) {
                console.log("Error: form not found");
                return;
            }
            var formHtml = form.prop('outerHTML');

            // Output the scripts (for validation)
            var page = jQuery(data);
            page.filter('script').each(function () {
                formHtml += jQuery(this).prop('outerHTML');
            });

            // Output on to the page
            jQuery("#" + fvModalId + " .body .form").html(formHtml);
        }
    })
}

function modalFormSubmit() {
    var form = jQuery(this),
        id = form.attr("id"),
        action = form.attr("action"),
        method = form.attr("method")
        ;

    // Return true if not ajax submit
    if (typeof(fvAjaxSubmit[id]) === "undefined" || fvAjaxSubmit[id] !== true) {
        return true;
    }

    jQuery.ajax({
        type: method,
        url: action,
        data: form.serialize(),
        success: function(data) {
            if (typeof(fvCallbacks[id]) !== 'undefined' && typeof(fvCallbacks[id]['success']) === 'function') {
                var fn = fvCallbacks[id]['success'];
                fn(data);
            }

            $("#" + fvModalId).modal('hide');
        },
        error: function(data) {
            if (typeof(fvCallbacks[id]) !== 'undefined' && typeof(fvCallbacks[id]['error']) === 'function') {
                var fn = fvCallbacks[id]['error'];
                fn(data);
            }

            $("#" + fvModalId).modal('hide');
        }
    });

    return false;
}