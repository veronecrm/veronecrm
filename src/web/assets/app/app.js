/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

var APP = {
    _isDomReady: false,
    isDomReady: function() {
        return APP._isDomReady;
    },
    domIsReady: function() {
        APP._isDomReady = true;
        APP.plugin.domReady();
    },
    factory: {
        plugin: function(name) {
            this.name = name;
            this.getName = function() {
                return this.name;
            }
        }
    },
    plugin: {
        list: [],
        register: function(plugin) {
            APP.plugin.list.push(plugin);
            APP[plugin.getName()] = plugin;

            if(plugin.init)
            {
                plugin.init();
            }
        },
        isRegistered: function(name) {
            for(var i in APP.plugin.list)
            {
                if(APP.plugin.list[i].getName() == name)
                {
                    return true;
                }
            }

            return false;
        },
        domReady: function() {
            for(var i in APP.plugin.list)
            {
                if(APP.plugin.list[i].domReady)
                {
                    APP.plugin.list[i].domReady();
                }
            }
        }
    },
    locale: 'en',
    system: {
        root: '',
        baseUrl: '',
        assets: '',
        routing: {
            module:     'Home',
            controller: 'Home',
            action:     'index',
            isRoute: function(module, controller, action) {
                if(! module)
                {
                    module = 'Home';
                }
                if(! controller)
                {
                    controller = 'Home';
                }
                if(! action)
                {
                    action = 'index';
                }

                return APP.system.routing.module == module && APP.system.routing.controller == controller && APP.system.routing.action == action;
            }
        }
    }
};

/**
 * To the APP variable we assign methods like these from Base class in PHP so
 * we can call them like in Controller or View.
 */
APP.createUrl = function(module, controller, action, params) {
    if(! module)
    {
        module = APP.system.routing.module;
    }
    if(! controller)
    {
        controller = APP.system.routing.controller;
    }
    if(! action)
    {
        action = APP.system.routing.action;
    }
    if(! params)
    {
        params = [];
    }
    else
    {
        var newParams = [];

        for(var i in params)
        {
            newParams.push(i + '=' + params[i]);
        }

        params = '&' + newParams.join('&');
    }

    return APP.system.baseUrl + '?mod=' + module + '&cnt=' + controller + '&act=' + action + params;
};
APP.asset = function(path) {
    return APP.system.assets + path;
};
APP.t = function(def) {
    return APP.Locale.t(def);
};
APP.filePath = function(path) {
    return APP.system.root + path;
};



/**
 * Language Plugin.
 */
var APP_Locale = new APP.factory.plugin('Locale');
// Main function
APP_Locale.domReady = function() { };
APP_Locale.t = function(def) {
    if(APP.Locale.translations[def])
    {
        return APP.Locale.translations[def];
    }

    return def;
};
APP_Locale.translations = {};
APP_Locale.locale = 'pl';
// Registering plugin
APP.plugin.register(APP_Locale);


/**
 * Plugin create click event on row cells which go to the link in attribute.
 */
var APP_RowClickTarget = new APP.factory.plugin('RowClickTarget');
// Main function
APP_RowClickTarget.domReady = function() {
    var self = this;
    $('table tr[data-row-click-target]').each(function() {
        self._applyForTR($(this));
    });
};

APP_RowClickTarget.apply = function(target) {
    var self = this;
    $('tr[data-row-click-target]', target).each(function() {
        self._applyForTR($(this));
    });
};

APP_RowClickTarget._applyForTR = function(target) {
    $(target).each(function() {
        $(this).find('> td').not('.app-click-prevent').click(function() {
            document.location.href = $(this).parent().attr('data-row-click-target');
        });

        $(this).find('> td').not('.app-click-prevent').find('> *').click(function(e) {
            e.stopPropagation();
        });
    });
};
// Registering plugin
APP.plugin.register(APP_RowClickTarget);



/**
 * Plugin that select all checkboxes, when we change main checkbox.
 */
var APP_CheckboxSelectAll = new APP.factory.plugin('CheckboxSelectAll');
// Main function
APP_CheckboxSelectAll.domReady = function() {
    $('input[data-select-all]').each(function() {
        $(this).change(function() {
            if($(this)[0].checked)
            {
                $('input[name=' + $(this).attr('data-select-all') + ']').each(function() {
                    $(this)[0].checked = true;
                });
            }
            else
            {
                $('input[name=' + $(this).attr('data-select-all') + ']').removeAttr('checked');
            }
        });
    });
};
// Registering plugin
APP.plugin.register(APP_CheckboxSelectAll);


/**
 * Plugin that sends firm by ID, when clicked on element which is not in this form.
 */
var APP_FormSubmit = new APP.factory.plugin('FormSubmit');
// Main function, bind events.
APP_FormSubmit.domReady = function() {
    $('[data-form-submit]').click(function() {
        APP_FormSubmit.submit($(this).attr('data-form-submit'), { 'formParam': $(this).attr('data-form-param') });
        return false;
    });
};
// Sends form, by given ID and additional options
APP_FormSubmit.submit = function(id, options) {
    options = $.extend({
        formParam: null
    }, options);

    // Add special input, that informs Controller, what to do when saveing is completed.
    if(options.formParam)
    {
        $('form#' + id).append($('<input />', {
            'type': 'hidden',
            'name': options.formParam,
            'value': 1
        }));
    }

    $('form#' + id).trigger('submit');
};
// Registering plugin
APP.plugin.register(APP_FormSubmit);



/**
 * Plugin to help form validation.
 */
var APP_FormValidation = new APP.factory.plugin('FormValidation');
// Helpers
APP_FormValidation.helper = {};
// Helper - finds parent, with given class
APP_FormValidation.helper.findParentWithClass = function(target, className) {
    var result = target;

    while(! result.hasClass(className))
    {
        result = result.parent();
    }

    return result;
};
/**
 * Generates DOM selector for element.
 */
APP_FormValidation.helper.generateSelector = function(element) {
    var selector = element
            .parents()
            .map(function() { return this.tagName; })
            .get()
            .reverse()
            .concat([this.nodeName])
            .join('>');

    var id = element.attr('id');

    if(id)
    { 
        selector += '#'+ id;
    }

    var classNames = $(this).attr('class');
    if(classNames)
    {
        selector += '.' + $.trim(classNames).replace(/\s/gi, '.');
    }

    return selector;
};

// Checks if the form can be submited
APP_FormValidation.canSubmit = function(formObject) {
    return formObject.find('.form-group.has-error').length == 0;
};

/**
 * Default validation functions
 */
APP_FormValidation.rules = {};

// Main function, bind events.
APP_FormValidation.domReady = function() {
    /**
     * Required value. Check if value is empty (not 0).
     * Trim value before check. Needs language pack loaded,
     * so we have to declare it on DOM ready.
     */
    APP_FormValidation.rules.required = {
        validate: function(elm, val) {
            if(jQuery.trim(val) == '')
            {
                return false;
            }

            return true;
        },
        errorText: APP.t('thisFieldIsRequired')
    };

    APP_FormValidation.create({selector:'.form-validation'});
};

APP_FormValidation.create = function(options) {
    options = $.extend({
        selector: ''
    }, options);

    $(options.selector)
        .each(function() {
            $(this).submit(function() {
                return APP_FormValidation.validateForm($(this));
            });
        })
        .find('input.required, textarea.required, select.required')
        .each(function() {
            APP_FormValidation.bind($(this).closest('.form-validation').attr('id'), APP_FormValidation.helper.generateSelector($(this)), 'required');
        });
};

/**
 * Binds validation rules for given form input.
 */
APP_FormValidation.bind = function(formId, selector, options) {
    var form  = $('#' + formId);
    var rules = form.data('validation-rules');

    if(! rules)
    {
        rules = [];
    }

    // Rewrite default options
    if(options == 'required')
    {
        options = APP_FormValidation.rules.required;
    }


    var added = false;

    for(var i in rules)
    {
        if(rules[i].selector == selector)
        {
            rules[i].options.push(options);
            added = true;
            break;
        }
    }

    if(! added)
    {
        rules.push({
            selector: selector,
            options: [options]
        });

        $(selector).focus(function() {
            APP_FormValidation.helper.findParentWithClass($(this), 'form-group')
            .removeClass('has-error')
            .removeClass('has-warning')
            .removeClass('has-success')
            .find('.info-text')
            .remove();
        }).blur(function() {
            APP_FormValidation.validateControl($(this));
        });
    }

    form.data('validation-rules', rules);
};

APP_FormValidation.reset = function(form) {
    var rules = form.data('validation-rules');

    for(var i in rules)
    {
        APP_FormValidation.helper.findParentWithClass($(rules[i].selector), 'form-group')
            .removeClass('has-error')
            .removeClass('has-warning')
            .removeClass('has-success')
            .find('.info-text')
            .remove();
    }
};

APP_FormValidation.resetControl = function(control) {
    var rules = control.closest('.form-validation').data('validation-rules');

    for(var i in rules)
    {
        if($(rules[i].selector).is(control))
        {
            APP_FormValidation.helper.findParentWithClass(control, 'form-group')
                .removeClass('has-error')
                .removeClass('has-warning')
                .removeClass('has-success')
                .find('.info-text')
                .remove();
        }
    }
};

/**
 * Valdates given form control.
 */
APP_FormValidation.validateControl = function(control) {
    var rules = control.closest('.form-validation').data('validation-rules');

    for(var i in rules)
    {
        if($(rules[i].selector).is(control))
        {
            APP_FormValidation.resetControl(control);

            var group = APP_FormValidation.helper.findParentWithClass(control, 'form-group');

            return APP_FormValidation._validateControl(control, rules[i].options, group);
        }
    }
};

APP_FormValidation._validateControl = function(control, options, group) {
    var canSubmit = true;
    var successText = '';

    for(var o in options)
    {
        var result = options[o].validate(control, control.val());

        if(result === 'error' || result === false)
        {
            group.addClass('has-error');

            if(options[o].errorText)
            {
                group.addClass('has-info-text').append('<div class="info-text">' + options[o].errorText + '</div>');
            }

            if(options[o].canSubmitOnError !== true)
            {
                canSubmit = false;
            }

            break;
        }
        else if(result === 'warning')
        {
            group.addClass('has-warning');

            if(options[o].warningText)
            {
                group.addClass('has-info-text').append('<div class="info-text">' + options[o].warningText + '</div>');
            }

            if(options[o].canSubmitOnWarning !== true)
            {
                canSubmit = false;
            }

            break;
        }

        /**
         * Only from last validate rule text we show on success. Success can be
         * only when all rules are passed, so only one rule (last one) can contain
         * this success text.
         */
        if(options[o].successText)
        {
            successText = options[o].successText;
        }
    }

    if(canSubmit)
    {
        group.addClass('has-success');

        if(successText)
        {
            group.addClass('has-info-text').append('<div class="info-text">' + options[o].successText + '</div>');
        }
    }

    return canSubmit;
};


/**
 * Validates all form inputs.
 */
APP_FormValidation.validateForm = function(form) {
    APP_FormValidation.reset(form);

    var rules     = form.data('validation-rules');
    var canSubmit = true;

    for(var i in rules)
    {
        var control = $(rules[i].selector),
                group   = APP_FormValidation.helper.findParentWithClass(control, 'form-group');

        if(! APP_FormValidation._validateControl(control, rules[i].options, group))
        {
            canSubmit = false;
        }
    }

    return canSubmit;
};

// Registering plugin
APP.plugin.register(APP_FormValidation);



/**
 * Plugin binds events on buttons with special class, and go back in history page.
 */
var APP_History = new APP.factory.plugin('History');
// Main function
APP_History.domReady = function() {
    $('.app-history-back').click(function() {
        history.back(2);
        return false;
    });
};
// Registering plugin
APP.plugin.register(APP_History);



/**
 * Plugin that creates fluis notifications.
 */
var APP_FluidNotification = new APP.factory.plugin('FluidNotification');
// Main function
APP_FluidNotification.domReady = function() {
    APP.Asset.js('/assets/jgrowl/jquery.jgrowl.min.js');
    APP.Asset.css('/assets/jgrowl/jquery.jgrowl.min.css');
};
APP_FluidNotification.loaded = false;
APP_FluidNotification.loadInterval = null;
APP_FluidNotification.notifications = [];
APP_FluidNotification.open = function(text, options) {
    var options = $.extend({
        theme:    'success',
        position: 'bottom-right',
        closerTemplate: '<div>' + APP.Locale.t('closeAllNotifications') + '</div>',
        sticky: false
    }, options);

    this.notifications.push({'text': text, 'options': options});
    this._openAll();
};
APP_FluidNotification._openAll = function() {
    clearInterval(this.loadInterval);

    if(this.loaded)
    {
        var item = null;
        while(item = this.notifications.pop())
        {
            $.jGrowl(item.text, item.options);
        }
    }
    else
    {
        this.loadInterval = setInterval(function() {
            if($.jGrowl)
            {
                APP.FluidNotification.loaded = true;
                APP.FluidNotification._openAll();
            }
        }, 300);
    }
}
// Registering plugin
APP.plugin.register(APP_FluidNotification);





/**
 * Script and CSS loader
 */
var APP_Asset = new APP.factory.plugin('Asset');
APP_Asset.loaded = [];
// Main function
APP_Asset.domReady = function() { };
APP_Asset.css = function(path, callback) {
    callback = callback || function() {};

    if(this.loaded.indexOf(path) == -1)
    {
        var s    = document.createElement("link");
        s.rel    = "stylesheet";
        s.onload = callback;
        s.href   = path;
        s.loaded = false;

        this.loaded.push(path);
        this.append(s);

        s.onload = function() {
            if(s.loaded === false)
            {
                s.loaded = true;
                callback();
            }
        }

        if(s.addEventListener)
        {
            s.addEventListener('load', function() {
                if(s.loaded === false)
                {
                    s.loaded = true;
                    callback();
                }
            }, false);
        }

        s.onreadystatechange = function() {
            var state = s.readyState;
            if (state === 'loaded' || state === 'complete') {
                s.onreadystatechange = null;
                if(s.loaded === false)
                {
                    callback();
                    s.loaded = true;
                }
            }
        };

        var cssnum = document.styleSheets.length;
        var ti = setInterval(function() {
            if(document.styleSheets.length > cssnum)
            {
                // needs more work when you load a bunch of CSS files quickly
                // e.g. loop from cssnum to the new length, looking
                // for the document.styleSheets[n].href === url
                // ...
                
                // FF changes the length prematurely :()
                if(s.loaded === false)
                {
                    callback();
                    s.loaded = true;
                    clearInterval(ti);
                }
            }
        }, 10);
    }
    else if(callback)
    {
        callback();
    }
};
APP_Asset.js = function(path, callback) {
    callback = callback || function() {};

    if(this.loaded.indexOf(path) == -1)
    {
        $.getScript(path, callback);

        this.loaded.push(path);
    }
    else if(callback)
    {
        callback();
    }
};
APP_Asset.append = function(element) {
    $("head").append(element);
};
// Registering plugin
APP.plugin.register(APP_Asset);




/**
 * AJAX wrapper for jQuery's
 */
var APP_AJAX = new APP.factory.plugin('AJAX');
// Main function
APP_AJAX.domReady = function() { };
APP_AJAX.call = function(options) {
    /**
     * Extending user given options.
     */
    var userOptions = $.extend({
        type: 'POST',
        url : '',
        data: {},
        preventDefault: false,
        parseResult: true,
        success: function() { },
        error: function() { }
    }, options);

    /**
     * Options passed to AJAX call with user defined options
     * and default functionality of response methods.
     */
    var endsOptions = {
        type: userOptions.type,
        url : userOptions.url,
        data: userOptions.data,
        preventDefault: userOptions.preventDefault,
        success: function(data) {
            if(userOptions.parseResult)
            {
                var result = {};

                try {
                    result = jQuery.parseJSON(data);
                }
                catch(e) {
                    console.log(e);
                    console.log(data);
                    APP.FluidNotification.open(APP.Locale.t('APPAJAXMalformedResponseData'), {theme: 'error'});
                    userOptions.success(null, data);
                    return;
                }

                if(result.hasOwnProperty('status') && result.hasOwnProperty('type') && result.status == 'error' && result.type == 'session-exipre')
                {
                    APP.VEPanel.open('.session-expire-panel', true);
                }
                else if(result.hasOwnProperty('status') && result.hasOwnProperty('message') && result.message != '')
                {
                    APP.FluidNotification.open(result.message, {theme: result.status});
                }

                if(result.hasOwnProperty('data'))
                {
                    userOptions.success(result.data, data);
                }
                else
                {
                    userOptions.success(null, data);
                }
            }
            else
            {
                try {
                    var result = jQuery.parseJSON(data);

                    if(result.hasOwnProperty('status') && result.hasOwnProperty('type') && result.status == 'error' && result.type == 'session-exipre')
                    {
                        APP.VEPanel.open('.session-expire-panel', true);
                    }
                    else if(result.hasOwnProperty('status') && result.hasOwnProperty('message') && result.message != '')
                    {
                        APP.FluidNotification.open(result.message, {theme: result.status});
                    }
                }
                catch(e) {}

                userOptions.success(data, data);
            }
        },
        error: function(error) {
            APP.FluidNotification.open(APP.Locale.t('APPAJAXErrorDuringRequest'), {theme: 'error'});
            userOptions.error(error);
        }
    };

    /**
     * If user wont default functionality of this call, we replace this functions
     * by user-defined.
     */
    if(userOptions.preventDefault)
    {
        endsOptions.success = userOptions.success;
        endsOptions.error   = userOptions.error;
    }

    // Finally call
    $.ajax(endsOptions);
};

// Registering plugin
APP.plugin.register(APP_AJAX);







/**
 * QuickEdit plugin.
 */
var APP_QuickEdit = new APP.factory.plugin('QuickEdit');
// Main function
APP_QuickEdit.domReady = function() { };
APP_QuickEdit.create = function(options) {
    /**
     * Extending user given options.
     */
    var userOptions = $.extend({
        // URL to save form
        url: '',
        // QuickEdit HTML container.
        target: '.quick-edit-form',
        // Element that clicked - shows QE form.
        trigger: '.quick-edit-trigger',
        // Array of objects sources
        src: [],
        // Called when all ready.
        onInit: function() { },
        // Called when form is saved. Should return false (boolean) if some error, or values to save on success.
        onSave: function(values) { return values },
        // Called when inputs values is change
        onChange: function(id) { },
        // Called when target is opened
        onOpen: function(id) { },
        // Called when target is closed
        onClose: function(id) { }
    }, options);

    /**
     * Main object of QE.
     */
    var QuickEditMain = function(options) {
        this.options = options;
        this.trigger = null;
        this.target  = null;

        this.currentId = 0;
        this.currentSrc = [];

        this.opened = false;

        this.init = function() {
            var self = this;

            self.target  = $(self.options.target);
            self.trigger = $(self.options.trigger);

            self.trigger.click(function() {
                var id = $(this).attr('data-quick-edit-id');

                if(id)
                {
                    if(self.currentId == id && self.opened)
                    {
                        self.close();
                        return false;
                    }

                    var src = self.findSrc(id);

                    if(src === null)
                    {
                        return false;
                    }

                    self.currentSrc = $.extend(src, {});
                    self.currentId  = id;
                    self.setValues(self.currentSrc);
                    self.options.onChange(self.currentId);
                    self.open();
                }
            });

            // Close button
            self.target.find('.btn-quick-edit-close').click(function() {
                self.close();
            });

            // Save button
            self.target.find('.btn-quick-edit-save').click(function() {
                self.save();
            });

            // Form PreventDefault
            self.target.find('form').submit(function(e) {
                e.preventDefault();
                return false;
            });

            // Loader
            self.target.append('<div class="loader hidden loader-fit-to-container"><div class="loader-animate"></div></div>');

            // ESC for close
            $('body').keyup(function(e) {
                if(e.which == 27 && self.opened)
                {
                    self.close();
                }
            });

            // Padding-bottom DIV
            self.target.find('.qef-cnt').append('<div style="height:230px"></div>');

            if($.fn.mCustomScrollbar)
            {
                self.target.find('.qef-cnt').mCustomScrollbar({
                    theme: 'minimal-dark',
                    scrollEasing: 'linear',
                    scrollInertia: 0,
                    mouseWheel: {
                        scrollAmount: 150
                    }
                });
            }

            self.options.onInit();
        };

        this.setValues = function(values) {
            var self = this;

            for(var i in values)
            {
                self.target.find('#' + i).val(values[i]);
            }
        };

        this.updateValues = function(id, values) {
            var self = this;

            for(var s in self.options.src)
            {
                if(self.options.src[s].id == id)
                {
                    for(var v in values)
                    {
                        for(var i in self.options.src[s])
                        {
                            if(i == v)
                            {
                                var item = self.target.find('#' + v);

                                if(item.length)
                                {
                                    self.options.src[s][i] = item.val();
                                }
                            }
                        }
                    }

                    return self.options.src[s];
                }
            }
        };

        this.findSrc = function(id) {
            for(var i in this.options.src)
            {
                if(this.options.src[i].id == id)
                {
                    return this.options.src[i];
                }
            }

            return null;
        };

        this.save = function() {
            var self = this;
            this.showLoader();

            var newValues = this.updateValues(self.currentId, self.currentSrc);

            newValues = this.options.onSave(newValues);

            /**
             * onSave callback should return false (boolean) if some error,
             * or values to save on success.
             */
            if(newValues === false)
            {
                this.hideLoader();
                return false;
            }

            APP.AJAX.call({
                url: this.options.url,
                data: newValues,
                success: function() {
                    self.close();
                },
                error: function() {
                    self.close();
                }
            });
        };

        this.open = function() {
            if(this.opened)
            {
                return false;
            }

            this.target.addClass('opened')
                // When open, we focus on first input in QE form.
                .find('input[type=text]').eq(0).trigger('focus');

            this.options.onOpen(this.currentId);
            this.opened = true;
        };

        this.close = function() {
            if(this.opened === false)
            {
                return false;
            }

            this.target.removeClass('opened');
            this.options.onClose(this.currentId);
            this.hideLoader();

            this.opened = false;
        };

        this.showLoader = function() {
            this.target.find('> .loader').removeClass('hidden');
        };

        this.hideLoader = function() {
            this.target.find('> .loader').addClass('hidden');
        };
    };

    var o = new QuickEditMain(userOptions);
    o.init();
    return o;
};

// Registering plugin
APP.plugin.register(APP_QuickEdit);




/**
 * Creates HistoryLog in document, managed in AJAX.
 */
var APP_RecordHistoryLog = new APP.factory.plugin('RecordHistoryLog');
// Main function
APP_RecordHistoryLog.domReady = function() { };
APP_RecordHistoryLog.create = function(options) {
    /**
     * Extending user given options.
     */
    var userOptions = $.extend({
        target: '.summary-panel.history-summary',
        module: '',
        entity: '',
        id: '',
        targetTotalCount: ''
    }, options);

    var RecordHistoryLogMain = function(options) {
        this.options = options;
        this.target  = null;

        this.page    = 1;
        this.total   = 1;
        this.perpage = 10;

        this.init = function() {
            this.target = $(this.options.target);
            this.showPage(1);
        };

        this.showPage = function(page) {
            var self = this;

            self.page = new Number(page);
            self.page = self.page <= 1 ? 1 : self.page;

            self.target.html('');
            self.target.append('<div class="loader"><div class="loader-animate"></div></div>');

            APP.AJAX.call({
                url: APP.createUrl('HistoryLog', 'History', 'history', {
                    module: self.options.module,
                    entity: self.options.entity,
                    id:     self.options.id,
                    page:   self.page
                }),
                success: function(data) {
                    self.total   = new Number(data.total);
                    self.perpage = new Number(data.perpage);

                    self.target.html('');

                    self.createHTML(data.changes);
                    self.bindEvents();

                    if(self.options.targetTotalCount)
                    {
                        $(self.options.targetTotalCount).text(self.total);
                    }
                }
            });
        };

        this.createHTML = function(elements) {
            for(var i in elements)
            {
                var element = '<div class="history-row"><div class="head"><strong>' + elements[i].object + '</strong> ' + elements[i].status + ' <strong>' + elements[i].authorName + ' (' + elements[i].authorId + ')</strong> <span class="date"><i class="fa fa-calendar"></i> ' + elements[i].date + '</span></div><ul class="change-details">';

                for(var j in elements[i].changes)
                {
                    element += '<li class="field-name">' + elements[i].changes[j].field + '</li><li class="changed-value"><span class="from">' + elements[i].changes[j].pre + '</span><span class="to">' + elements[i].changes[j].post + '</span></li>';
                }

                this.target.append(element + '</ul></div>');
            }

            this.createPagination();
        };

        this.createPagination = function() {
            // Total pages to show
            var pages = Math.ceil(this.total / this.perpage);

            // Start page to show
            var startPage = this.page - 2;
                    startPage = startPage <= 1 ? 1 : startPage;

            // Last page to show
            var lastPage = this.page + 2;
                    lastPage = lastPage >= pages ? pages : lastPage;

            var pagination = '<nav class="text-center"><ul class="pagination pagination-sm">';

            if(this.page > 1)
            {
                // First page
                pagination += '<li><a href="#" data-page="1"><span class="fa fa-angle-double-left"></span></a></li>';

                // Previous page
                pagination += '<li><a href="#" data-page="' + (this.page - 1) + '"><span class="fa fa-angle-left"></span></a></li>';
            }

            for(var i = startPage; i <= lastPage; i++)
            {
                pagination += '<li' + (this.page == i ? ' class="active"' : '') + '><a href="#" data-page="' + i + '">' + i + '</a></li>';
            }

            if(this.page < pages)
            {
                // Previous page
                pagination += '<li><a href="#" data-page="' + (this.page + 1) + '"><span class="fa fa-angle-right"></span></a></li>';

                // Last page
                pagination += '<li><a href="#" data-page="' + pages + '"><span class="fa fa-angle-double-right"></span></a></li>';
            }

            this.target.append(pagination + '</ul></nav>');
        };

        this.bindEvents = function() {
            var self = this;

            this.target.find('.history-row .head').click(function() {
                var t = $(this).parent().find('.change-details');

                t.slideDown(150);
                self.target.find('.change-details').not(t).slideUp(150);
            }).eq(0).trigger('click');

            this.target.find('.pagination a').click(function() {
                self.showPage($(this).attr('data-page'));
                return false;
            });
        };
    };

    var o = new RecordHistoryLogMain(userOptions);
    o.init();
    return o;
};

// Registering plugin
APP.plugin.register(APP_RecordHistoryLog);





/**
 * 
 */
var APP_VEPanel = new APP.factory.plugin('VEPanel');
// Main function
APP_VEPanel.domReady = function() {
    $('body').keyup(function(e) {
        if(e.which == 27 && APP_VEPanel.allowCloseByUser)
        {
            APP.VEPanel.close();
        }
    });
};
APP_VEPanel.opened = false;
APP_VEPanel.allowCloseByUser = false;
APP_VEPanel.open = function(selector, allowCloseByUser) {
    APP.VEPanel.close();
    APP_VEPanel.opened = selector;
    APP_VEPanel.allowCloseByUser = ! allowCloseByUser;

    var panel = $(selector);

    panel.removeClass('hidden').appendTo('body');
    $('#wrapper').addClass('blurred');

    var fl = panel.find('.ve-fl');
    var bl = panel.find('.ve-bl');

    fl.css({
        left: (bl.outerWidth() - fl.outerWidth()) / 2,
        top: (bl.outerHeight() - fl.outerHeight()) / 2
    });

    if(! panel.hasClass('ve-panel-events'))
    {
        panel.find('a').click(function() {
            panel.find('.loader').removeClass('hidden');
        });

        if(APP_VEPanel.allowCloseByUser === true)
        {
            panel.find('.ve-bl').click(function() {
                APP.VEPanel.close();
            });
        }

        panel.addClass('ve-panel-events');
    }

    panel.trigger('app.ve-panel:open');

    return panel;
};
APP_VEPanel.close = function() {
    if(APP_VEPanel.opened)
    {
        $(APP_VEPanel.opened).addClass('hidden').trigger('app.ve-panel:close');
        $('#wrapper').removeClass('blurred');
        APP_VEPanel.opened = false;
        return true;
    }

    return false;
};
// Registering plugin
APP.plugin.register(APP_VEPanel);




/**
 * Creates Comments panel, managed in AJAX.
 */
var APP_Comments = new APP.factory.plugin('Comments');
// Main function
APP_Comments.domReady = function() { };
APP_Comments.create = function(options) {
    /**
     * Extending user given options.
     */
    var userOptions = $.extend({
        target: '.comments-panel',
        module: '',
        entity: '',
        id: ''
    }, options);

    var CommentsMain = function(options) {
        this.options = options;
        this.target  = null;

        this.init = function() {
            var self = this;

            self.target = $(self.options.target);

            self.target.html('<div class="comments-new"><div class="form-group"><textarea name="comment" class="form-control required" placeholder="' + APP.t('addComment') + '"></textarea></div><div class="text-right hidden"><button type="button" class="btn btn-primary">' + APP.t('save') + '</button></div></div><div class="loader"><div class="loader-animate"></div></div><div class="comments-container"></div>');

            APP.AJAX.call({
                url: APP.createUrl('Comment', 'Comment', 'get', {
                    module: self.options.module,
                    entity: self.options.entity,
                    id:     self.options.id
                }),
                success: function(data) {
                    console.log(data);

                    self.createHTML(data);

                    self.target.find('.loader').addClass('hidden');
                    self.bindEvents();
                }
            });
        };

        this.createHTML = function(elements) {
            var target = this.target.find('.comments-container');

            for(var i in elements)
            {
                target.append('<div><div class="hl"><span class="date">' + elements[i].date + '</span><span class="user">' + elements[i].userId + '</span></div><div class="comment">' + elements[i].comment + '</div></div>');
            }
        };

        this.bindEvents = function() {
            var self = this;
            self.target.find('.comments-new textarea').focus(function() {
                $(this).parent().next('div').removeClass('hidden');
                $(this).parent().removeClass('has-info-text').removeClass('has-error').find('.info-text').remove();
            });

            self.target.find('.comments-new button').click(function() {
                var elm = self.target.find('.comments-new textarea');
                var val = elm.val().trim();

                elm.parent().removeClass('has-info-text').removeClass('has-error').find('.info-text').remove();

                if(val == '')
                {
                    elm.parent().addClass('has-info-text').addClass('has-error').append('<div class="info-text">To pole jest wymagane.</div>');
                }
                else
                {
                    self.target.find('.loader').removeClass('hidden');

                    APP.AJAX.call({
                        url: APP.createUrl('Comment', 'Comment', 'put', {
                            module: self.options.module,
                            entity: self.options.entity,
                            id:     self.options.id,
                            comment:val
                        }),
                        success: function(data) {
                            self.target.find('.loader').addClass('hidden');

                            self.target.find('.comments-container').prepend('<div><div class="hl"><span class="date">' + data.date + '</span><span class="user">' + data.userId + '</span></div><div class="comment">' + val + '</div></div>');

                            elm.val('');
                        }
                    });
                }
            });
        };
    };

    APP_Comments.instances++;

    var o = new CommentsMain(userOptions);
    o.init();
    return o;
};

// Registering plugin
APP.plugin.register(APP_Comments);





/**
 * 
 */
var APP_ConfirmationPanel = new APP.factory.plugin('ConfirmationPanel');
// Main function
APP_ConfirmationPanel.domReady = function() {
    $('body').keyup(function(e) {
        if(e.which == 27)
        {
            APP.ConfirmationPanel.close();
        }
    });

    APP_ConfirmationPanel.reset();

    /**
     * Here we can use Language translations.
     */
    APP_ConfirmationPanel.defaults.heading = APP.t('confirmationRequired');
    APP_ConfirmationPanel.defaults.content = '<p>' + APP.t('APPConfirmationPanelConfirmationRequiredDescription') + '</p>';
    APP_ConfirmationPanel.defaults.btnText = APP.t('APPConfirmationPanelApply');
};
APP_ConfirmationPanel.panel = null;
APP_ConfirmationPanel.panelHTML = '<div class="ve-panel ve-panel-darken confirmation-panel hidden"><div class="ve-bl"></div><div class="ve-fl"><h2 class="ve-heading"></h2><div></div><div class="form-group"><div class="input-group"><input type="password" name="password" class="form-control" /><div class="input-group-btn"><button type="button" class="btn btn-default"></button></div></div></div><div class="loader hidden loader-fit-to-container"><div class="loader-animate"></div></div></div></div>';
APP_ConfirmationPanel.onConfirm = function() {};
APP_ConfirmationPanel.onFailure = function() {};
APP_ConfirmationPanel.options = {};
APP_ConfirmationPanel.defaults = {
    heading: '',
    content: '',
    btnText: '',
    onConfirm: function() {},
    onFailure: function() {}
};
APP_ConfirmationPanel.status = false;
APP_ConfirmationPanel.open = function(options) {

    APP_ConfirmationPanel.options = $.extend(APP_ConfirmationPanel.defaults, options);

    APP_ConfirmationPanel.panel = $(APP_ConfirmationPanel.panelHTML);

    APP_ConfirmationPanel.panel.appendTo('body').bind('app.ve-panel:close', function() {
        APP_ConfirmationPanel.status
            ? APP_ConfirmationPanel.options.onConfirm()
            : APP_ConfirmationPanel.options.onFailure();

        APP_ConfirmationPanel.reset();
        APP_ConfirmationPanel.panel.remove();
        APP_ConfirmationPanel.panel = null;
    });

    APP_ConfirmationPanel.panel
        .find('.ve-heading')
        .html(APP_ConfirmationPanel.options.heading)
        .next('div')
        .html(APP_ConfirmationPanel.options.content)
        .parent()
        .find('.btn.btn-default')
        .html(APP_ConfirmationPanel.options.btnText)
        .closest('.confirmation-panel')
        .find('input')
        .attr('placeholder', APP.t('typePassword'));

    APP_ConfirmationPanel.panel.find('input').keyup(function(e) {
        if(e.which == 13)
        {
            APP_ConfirmationPanel.panel.find('button.btn').trigger('click');
        }
    });

    APP_ConfirmationPanel.panel.find('button.btn').click(function() {
        APP_ConfirmationPanel.panel.find('.loader').removeClass('hidden');

        APP.AJAX.call({
            url: APP.createUrl('Home', 'Home', 'validateUserPassword'),
            data: {
                password: APP_ConfirmationPanel.panel.find('input').val()
            },
            success: function(status) {
                if(status == 'success')
                {
                    APP_ConfirmationPanel.status = true;

                    APP.VEPanel.close();
                }
                else
                {
                    APP_ConfirmationPanel.status = false;
                    APP_ConfirmationPanel.panel.find('.loader').addClass('hidden');
                    APP_ConfirmationPanel.panel.find('.info-text').remove();
                    APP_ConfirmationPanel.panel.find('.form-group').addClass('has-info-text').addClass('has-error').append('<div class="info-text">' + APP.t('givenPasswordIsInvalid') + '</div>');
                }
            },
            error: function(data) {
                APP_ConfirmationPanel.options.onFailure();
                APP.VEPanel.close();
            }
        });

        APP_ConfirmationPanel.panel.find('input').focus(function() {
            APP_ConfirmationPanel.panel.find('.info-text').remove();
            APP_ConfirmationPanel.panel.find('.form-group').removeClass('has-info-text').removeClass('has-error');
        });
    });

    APP.VEPanel.open('.confirmation-panel');
};

APP_ConfirmationPanel.reset = function() {
    APP_ConfirmationPanel.options = {};
    APP_ConfirmationPanel.status = false;
};

// Registering plugin
APP.plugin.register(APP_ConfirmationPanel);
