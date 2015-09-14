/*globals jQuery,fvModalId,fvAjaxSubmit,fvCallbacks*/

var FV_MODAL_FORM_SCRIPTS_LOADED_EVENT = 'fvModalFormScriptsLoaded',

    /**
     * Retrieves the script tags on the page
     * @returns {Array}
     */
    getPageScriptTags = function () {
        "use strict";

        var scripts = [];
        jQuery('script[src]').each(function () {
            scripts.push(jQuery(this).attr('src'));
        });

        return scripts;
    },

    /**
     * Retrieves the page CSS links
     * @returns {Array}
     */
    getPageCssLinks = function () {
        "use strict";

        var links = [];
        jQuery('link[rel="stylesheet"]').each(function () {
            links.push(jQuery(this).attr('href'));
        });

        return links;
    },

    /**
     * Fetch the modal group
     * @param group
     * @returns {boolean}
     */
    getModalId = function(group) {
        return fvModalId[group] !== undefined ? fvModalId[group] : false;
    },

    /**
     * Configure the modal form
     *
     * @param title
     * @param url
     * @param formId
     * @param group
     */
    modalForm = function (title, url, formId, group) {
        "use strict";

        // Fetch the modal ID
        var modalId = getModalId(group);
        if (modalId === false) {
            console.log('[FVMODAL] Invalid ID');
            return;
        }

        // Set the modal for loading
        jQuery("#" + modalId + " .body .form").html("");
        jQuery("#" + modalId + " .modal-header span").html(title);
        jQuery("#" + modalId + " .loading").removeClass("hide");

        jQuery.ajax({
            url: url,
            success: function (data) {
                jQuery("#" + modalId + " .body .loading").addClass("hide");
                var form = jQuery(data).find("#" + formId),
                    formHtml,
                    page,
                    existingScripts = getPageScriptTags(),
                    existingCssLinks = getPageCssLinks(),
                    injections = [],
                    scriptTags = [],
                    loadedScripts = 0,

                    /**
                     * Execute the JS on the page
                     * @param injections
                     */
                    executeInjections = function (injections) {
                        var iterator;
                        for (iterator = 0; iterator < injections.length; iterator += 1) {
                            eval(injections[iterator]);
                        }
                    },

                    /**
                     * Scripts loaded callback
                     */
                    scriptLoaded = function () {
                        loadedScripts += 1;
                        if (loadedScripts === scriptTags.length) {
                            executeInjections(injections);
                            jQuery(document).trigger(FV_MODAL_FORM_SCRIPTS_LOADED_EVENT);
                        }
                    },

                    iterator;

                // Check if form exists
                if (form.length === 0) {
                    console.log("Error: form not found");
                    return;
                }
                formHtml = form.prop('outerHTML');

                // Output on to the page
                jQuery("#" + modalId + " .body .form").html(formHtml).promise().done(function () {
                    page = jQuery(data);

                    // CSS stylesheets that haven't been added need to be loaded before end of head
                    page.filter('link[rel="stylesheet"]').each(function () {
                        var href = jQuery(this).attr('href'),
                            head = jQuery('head');

                        if (existingCssLinks.indexOf(href) < 0) {
                            // Append the CSS link to the page
                            if (head.length === 0) {
                                head = jQuery('body');
                            }

                            if (head.length === 0) {
                                head = jQuery(document);
                            }

                            head.append(jQuery(this).prop('outerHTML'));

                            // Store the link so its not needed to be requested again
                            existingCssLinks.push(href);
                        }
                    });

                    // Scripts that haven't yet been loaded need to be added to the end of the body
                    page.filter('script').each(function () {
                        var src = jQuery(this).attr("src");

                        if (!src) {
                            // If no src supplied, execute the raw JS (need to execute after the script tags have been loaded)
                            injections.push(jQuery(this).text());

                        } else if (existingScripts.indexOf(src) < 0) {
                            // Append a random timestamp to the end to force browser to send the second+ request
                            src += (src.indexOf('?') < 0) ? '?' : '&';
                            scriptTags.push(src);
                        }
                    });

                    // Load each script tag
                    for (iterator = 0; iterator < scriptTags.length; iterator += 1) {
                        jQuery.getScript(scriptTags[iterator] + (new Date().getTime()), scriptLoaded);
                    }

                    // Output on to the page
                    jQuery(document).trigger('fvModalLoaded');
                });
            }
        });
    },

    /**
     * When the form has been submitted, close the box
     */
    modalFormSubmit = function () {
        "use strict";

        var form = jQuery(this),
            id = form.attr("id"),
            action = form.attr("action"),
            method = form.attr("method"),
            modalId = getModalId(group);

        if (modalId === false) {
            console.log('[FVMODAL] Invalid ID');
            return;
        }

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
                jQuery("#" + modalId).modal('hide');
            },
            error: function (data) {
                if (fvCallbacks && fvCallbacks[id] && typeof fvCallbacks[id].error === 'function') {
                    var fn = fvCallbacks[id].error;
                    fn(data);
                }

                jQuery("#" + modalId).modal('hide');
            }
        });

        return false;
    };

(function ($) {
    "use strict";

    // Forms can trigger the modal complete event to hide
    $(document).on('fvModalComplete', function () {
        var key;

        for (var key in fvModalId) {
            if (fvModalId.hasOwnProperty(key)) {
                $("#" + fvModalId[key]).modal('hide');
            }
        }
    });
}(jQuery));