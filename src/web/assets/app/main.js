/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

$(function() {
    $(window).bind('load resize', function() {
        var height = (this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height;

        if(height < 1)
        {
            height = 1;
        }

        if($.cookie('page-min-height') != height)
        {
            $('#page-wrapper').css('min-height', (height) + 'px');
            $.cookie('page-min-height', height);
        }
    });

    $('[data-toggle="tooltip"]').tooltip({container:'body'});
    $('.navbar-toggle-large').click(function() {
        $('.sidebar').toggleClass('sidebar-opened').toggleClass('sidebar-closed');
    });
    $('.open-action-panel').click(function() {
        APP.VEPanel.open('.actions-panel');
        return false;
    });

    $('.widget-alphabet-links .links-list').scroll(function(e) {
        var elm = $(this).parent();

        if($(this).scrollLeft() == 0)
        {
            elm.removeClass('shadow-left');
        }
        else
        {
            elm.addClass('shadow-left');
        }

        if($(this).scrollLeft() + $(this).get(0).clientWidth == $(this).get(0).scrollWidth)
        {
            elm.removeClass('shadow-right');
        }
        else
        {
            elm.addClass('shadow-right');
        }
    }).trigger('scroll');

    // Temporary unavailable
    /*$(window).keydown(function(e) {
        // CTRL + L = Go to lockscreen
        if(e.which == 76 && e.ctrlKey == true)
        {
            if($.inArray(document.activeElement.tagName, ['INPUT', 'TEXTAREA', 'BUTTON', 'SELECT']))
            {
                return true;
            }

            document.location.href = $('#link-user-lockscreen').attr('href');
            e.stopPropagation();
            e.preventDefault();
            return false;
        }
    });*/

    setInterval(function() {
        APP.AJAX.call({ url: APP.createUrl('Home', 'Home', 'ping') });
    }, 7200000 - 120000); // 7200000 miliseconds = 2 hours; Waiting 120000 milisecond less for call PING before session close.

    /**
     * This fix column width, where actions-box exists. This columns isn't
     * clicked for edit/summary. We shrink width of this column to minimal
     * that only contain this box.
     *
     * If this will be very exacting, consist to remove this code.
     * We don't want the same situation as in phpMyAdmin...
     *
     * For now, this is a alpha-feature.
     */
    $('table.table .actions-box').each(function() {
        $(this).closest('td').css('width', $(this).outerWidth() + 50);
    });

    /**
     * Class toggle for element.
     */
    var toggleClass = function(element, classToggle) {
        var className = ' ' + element.className + ' ';

        if(className.indexOf(' ' + classToggle + ' ') === -1)
            className += classToggle + ' ';
        else
            className = className.replace(' ' + classToggle + ' ', ' ');

        element.className = className;
    };

    /**
     * Code for toggling visibility of breadcrumb and heading in page-title bars.
     */
    var clickToggleVisibilityEvent = function(trigger, groupClass) {
        var nodes = trigger.parentNode.childNodes;

        for(var n = 0; n < nodes.length; n++)
        {
            if(nodes[n].className && nodes[n].className.indexOf(groupClass) !== -1)
            {
                toggleClass(nodes[n], 'visible');
            }
        }
    };
    
    var bet = document.querySelectorAll('.breadcrumb-elements-toggle');
    for(var i = 0; i < bet.length; i++)
    {
        bet[i].addEventListener('click', function() {
            clickToggleVisibilityEvent(this, 'breadcrumb-elements');
        });
    }

    var het = document.querySelectorAll('.heading-elements-toggle');
    for(var i = 0; i < het.length; i++)
    {
        het[i].addEventListener('click', function() {
            clickToggleVisibilityEvent(this, 'heading-elements');
        });
    }

    /**
     * Navbar notificator toggle open.
     */
    var nots = document.querySelectorAll('#wrapper .notificator .notificator-btn');
    for(var i = 0; i < nots.length; i++)
    {
        nots[i].addEventListener('click', function() {
            toggleClass(this.parentNode, 'open');
        });
    }

    applyTextareaAutoGrow('textarea.auto-grow');

    APP.domIsReady();
});

/**
 * Textareas autogrow.
 * @todo Rewrite to Vanilla JS.
 */
function autoHeightResize(element) {
    $(element).css({'height':'auto','overflow-y':'hidden'}).height(element.scrollHeight);
}
function applyTextareaAutoGrow(selector) {
    $(selector)
        .trigger('autogrow.update')
        .not('.auto-grow-binded')
        .addClass('.auto-grow-binded')
        .each(function() {autoHeightResize(this)})
        .on('input autogrow.update', function() {autoHeightResize(this)});
}

/**
 * For RADIOs.
 */
if($.fn.bootstrapSwitch)
{
    $.fn.bootstrapSwitch.defaults.labelText = '&nbsp;';
    $.fn.bootstrapSwitch.defaults.onText    = APP.t('syes');
    $.fn.bootstrapSwitch.defaults.offText   = APP.t('sno');
    $.fn.bootstrapSwitch.defaults.size      = 'normal';
    $.fn.bootstrapSwitch.defaults.onColor   = 'success';
    $.fn.bootstrapSwitch.defaults.labelWidth = 1;

    // We have to fire-up event on this input manualy, becouse in future we could
    // change this script for some other one.
    $('input.radio-switch').bootstrapSwitch().on('switchChange.bootstrapSwitch', function(event, state) {
        $(this).trigger('change');
    });
}

/**
 * For CHECKBOXes.
 */
if($.fn.bootstrapToggle)
{
    $('input.checkbox-toggle').bootstrapToggle({
        on  : APP.t('syes'),
        off : APP.t('sno'),
        size    : 'small',
        onstyle : 'success'
    });
}

if($.fn.datetimepicker)
{
    $.fn.datetimepicker.defaults.locale = APP.locale;
}

/**
 * Belowed code extends DefaultBootBox functionality. Allows create help modal
 * only with title and message defined by user. Rest of options are predefined.
 */
if(window.bootbox)
{
    window.bootbox.helpdialog = function(title, message) {
        bootbox.dialog({
            title: title,
            message: message,
            backdrop: true,
            onEscape: true,
            buttons: {
                success: {
                    label: APP.Locale.t('close'),
                    className: 'btn-primary'
                }
            }
        });
    };
}


/* ========================================================================
 * Bootstrap: dropdown.js v3.3.5
 * http://getbootstrap.com/javascript/#dropdowns
 * ========================================================================
 * Copyright 2011-2015 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */
/**
 * Dropdown modified for work with sub-menus opened on hover and click (mobile).
 * Original code taken from Bootstrap Dropdown plugin, and added code for sub-menus.
 */
+function ($) {
    'use strict';

    // DROPDOWN CLASS DEFINITION
    // =========================

    var backdrop = '.dropdown-backdrop'
    var toggle   = '[data-toggle="dropdown"]'
    var Dropdown = function (element) {
        $(element).on('click.bs.dropdown', this.toggle)
    }

    Dropdown.VERSION = '3.3.5'

    function getParent($this) {
        var selector = $this.attr('data-target')

        if (!selector) {
            selector = $this.attr('href')
            selector = selector && /#[A-Za-z]/.test(selector) && selector.replace(/.*(?=#[^\s]*$)/, '') // strip for ie7
        }

        var $parent = selector && $(selector)

        return $parent && $parent.length ? $parent : $this.parent()
    }

    function clearMenus(e) {
        if (e && e.which === 3) return
        $(backdrop).remove()
        $(toggle).each(function () {
            var $this         = $(this)
            var $parent       = getParent($this)
            var relatedTarget = { relatedTarget: this }

            if (!$parent.hasClass('open')) return

            if (e && e.type == 'click' && /input|textarea/i.test(e.target.tagName) && $.contains($parent[0], e.target)) return

            $parent.trigger(e = $.Event('hide.bs.dropdown', relatedTarget))

            if (e.isDefaultPrevented()) return

            $this.attr('aria-expanded', 'false')
            $parent.removeClass('open').trigger('hidden.bs.dropdown', relatedTarget)

            $this.find('li.dropdown-submenu').removeClass('visible')
        })
    }

    function resolveSubmenus ($this) {
        $this.find('li.dropdown-submenu').each(function() {
            var $a = $(this).find('> a'),
                    $this = $(this),
                    $submenu = $(this).find('> .dropdown-menu')

            $a.on('click', function (e) {
                openSubmenu($this, $submenu)

                e.preventDefault()
                return false
            })

            $this.hover(function (e) {
                openSubmenu($this, $submenu)
            }, function (e) {
                $this.removeClass('visible')
            })
        })
    }

    function openSubmenu ($trigger, $submenu) {
        $trigger.addClass('visible')
        $submenu.removeClass('left')

        if(($submenu.offset().left + $submenu.outerWidth()) > $(window).width())
        {
            $submenu.addClass('left');

            if($submenu.offset().left <= 0)
            {
                $submenu.removeClass('left');
            }
        }
    }

    Dropdown.prototype.toggle = function (e) {
        var $this = $(this)

        if ($this.is('.disabled, :disabled')) return

        var $parent  = getParent($this)
        var isActive = $parent.hasClass('open')

        clearMenus()

        if (!isActive) {
            if ('ontouchstart' in document.documentElement && !$parent.closest('.navbar-nav').length) {
                // if mobile we use a backdrop because click events don't delegate
                $(document.createElement('div'))
                    .addClass('dropdown-backdrop')
                    .insertAfter($(this))
                    .on('click', clearMenus)
            }

            var relatedTarget = { relatedTarget: this }
            $parent.trigger(e = $.Event('show.bs.dropdown', relatedTarget))

            if (e.isDefaultPrevented()) return

            $this
                .trigger('focus')
                .attr('aria-expanded', 'true')

            $parent
                .toggleClass('open')
                .trigger('shown.bs.dropdown', relatedTarget)

            var $menu = $parent.find('> .dropdown-menu').removeClass('right')

            if(($menu.offset().left + $menu.outerWidth()) > $(window).width())
            {
                $menu.addClass('right');
            }

            if(! $this.data('bs.submenu-resolved'))
            {
                $this.data('bs.submenu-resolved', 1);
                resolveSubmenus($menu);
            }
        }

        return false
    }

    Dropdown.prototype.keydown = function (e) {
        if (!/(38|40|27|32)/.test(e.which) || /input|textarea/i.test(e.target.tagName)) return

        var $this = $(this)

        e.preventDefault()
        e.stopPropagation()

        if ($this.is('.disabled, :disabled')) return

        var $parent  = getParent($this)
        var isActive = $parent.hasClass('open')

        if (!isActive && e.which != 27 || isActive && e.which == 27) {
            if (e.which == 27) $parent.find(toggle).trigger('focus')
            return $this.trigger('click')
        }

        var desc = ' li:not(.disabled):visible a'
        var $items = $parent.find('.dropdown-menu' + desc)

        if (!$items.length) return

        var index = $items.index(e.target)

        if (e.which == 38 && index > 0)                 index--         // up
        if (e.which == 40 && index < $items.length - 1) index++         // down
        if (!~index)                                    index = 0

        $items.eq(index).trigger('focus')
    }


    // DROPDOWN PLUGIN DEFINITION
    // ==========================

    function Plugin(option) {
        return this.each(function () {
            var $this = $(this)
            var data  = $this.data('bs.dropdown')

            if (!data) $this.data('bs.dropdown', (data = new Dropdown(this)))
            if (typeof option == 'string') data[option].call($this)
        })
    }

    var old = $.fn.dropdown

    $.fn.dropdown             = Plugin
    $.fn.dropdown.Constructor = Dropdown


    // DROPDOWN NO CONFLICT
    // ====================

    $.fn.dropdown.noConflict = function () {
        $.fn.dropdown = old
        return this
    }


    // APPLY TO STANDARD DROPDOWN ELEMENTS
    // ===================================

    $(document)
        .on('click.bs.dropdown.data-api', clearMenus)
        .on('click.bs.dropdown.data-api', '.dropdown form', function (e) { e.stopPropagation() })
        .on('click.bs.dropdown.data-api', toggle, Dropdown.prototype.toggle)
        .on('keydown.bs.dropdown.data-api', toggle, Dropdown.prototype.keydown)
        .on('keydown.bs.dropdown.data-api', '.dropdown-menu', Dropdown.prototype.keydown)

}(jQuery);
