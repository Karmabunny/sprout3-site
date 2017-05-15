/**
* A list of widgets
**/
function widget_list(field_name) {
    var $list = $('#wl-' + field_name);
    var list = this;
    $list.data('wl', this);

    this.field_name = field_name;
    this.next_widget_id = 0;


    function onAjaxFailure(data) {
        alert('Error loading content block settings form; failed to parse JSON response: ' + data.responseText);
    }


    /**
    * Adds a widget to the list
    **/
    this.add_widget = function (widget_name, english_name, widget_settings, is_new, is_active) {
        var wid_id = list.next_widget_id++;
        var field_name = list.field_name;

        // Add empty div immediately, to be replaced upon AJAX return. This allows multiple
        // requests to return out of order without them becoming disordered in the UI
        var html_id = 'widget_' + field_name + '_' + wid_id;
        var $elem_placeholder = $('<div id="' + html_id + '"></div>');
        var $widget_group = $list.find('.widgets-sel');
        $widget_group.append($elem_placeholder);

        $.ajax({
            url: 'admin_ajax/widget_settings/' + encodeURIComponent(widget_name),
            method: 'POST',
            dataType: 'json',
            data: {
                settings: widget_settings,
                prefix: 'widget_settings_' + wid_id
            },
            success: onAjaxSuccess,
            error: onAjaxFailure
        });

        function onAjaxSuccess(data) {
            if (data.success == 0) {
                alert('Error loading addon settings form: ' + data.message);
                return;
            }

            if (is_new) {
                data.edit_url = null;
                data.info_labels = null;
            }

            var html = '';
            html += '<div class="widget' + (is_active ? ' widget-enabled' : ' widget-disabled content-block-collapsed') + '" id="' + html_id + '">';
            html += '<input type="hidden" name="widgets[' + field_name + '][]" value="' + wid_id + ',' + widget_name + '">';
            html += '<input type="hidden" name="widget_active[' + field_name + '][]" value="' + (is_active ? '1' : '0') + '">';
            html += '<input type="hidden" name="widget_deleted[' + field_name + '][]" value="0">';

            // Wrapper around header
            html += '<p class="content-block-title">Content block</p>';
            html += '<div class="widget-header -clearfix">';
            html += '<div class="widget-header--main -clearfix">';

            // Right: Buttons includeing dropdown menu
            html += '<div class="widget-header-buttons -clearfix">';
            html += '<button type="button" class="widget-header-button content-block-toggle-open-button icon-before icon-keyboard_arrow_up" title="Collapse"><span class="-vis-hidden">Collapse widget block</span></button>';
            html += '<div class="content-block-settings-wrapper">';
            html += '<button type="button" class="widget-header-button content-block-settings-button icon-before icon-settings" title="Settings"><span class="-vis-hidden">Content block settings</span></button>';
            html += '<div class="dropdown-box content-block-settings-dropdown">';
            html += '<ul class="content-block-settings-dropdown-list list-style-2">';
            html += '<li class="content-block-settings-dropdown-list-item"><button type="button" class="content-block-toggle-active">' + (is_active ? 'Disable' : 'Enable') + '</button></li>';
            html += '</ul>';
            html += '</div>';
            html += '</div>';
            html += '<button type="button" class="widget-header-button content-block-reorder-button icon-before icon-open_with" title="Reorder"><span class="-vis-hidden">Reorder content block</span></button>';
            html += '<button type="button" class="widget-header-button content-block-remove-button icon-before icon-close" title="Remove"><span class="-vis-hidden">Remove content block</span></button>';
            html += '</div>';

            // Left: Type and description
            html += '<div class="widget-header-text">';
            html += '<h3 class="widget-header-content-block-title">' + english_name + '</h3>';
            html += '<p class="widget-header-description">' + data.description + '</p>';
            html += '</div>';

            // End of header
            html += '</div>';
            html += '</div>';

            html += '<div class="settings">';
            html += data.settings;
            html += '</div>';

            if (data.edit_url) {
                html += '<p><a href="' + data.edit_url + '" target="_blank" class="button button-small button-grey icon-after icon-edit">edit content</a></p>';
            }
            html += '</div>';

            // Create element; inject into the page
            var $elem = $(html);
            $elem_placeholder.replaceWith($elem);

            // Nuke the 'empty' message if any widgets have been added
            $list.find('.widgets-empty').remove();

            // Init any extra FB bits which may be required
            Fb.initAll($elem);

            if ($elem.is('.widget-disabled')) {
                list.uiDisableWidget($elem);
                list.uiCollapseWidget($elem, 0);
            }

            // Event handler -- remove widget button
            $elem.find('.content-block-remove-button').on('click', function() {
                $('#edit-form').triggerHandler('setDirty');

                var $widget = $(this).closest('div.widget');
                list.deleteWidget($widget);

                return false;
            });

            // Event handler -- set widget active toggle
            $elem.find('.content-block-toggle-active').on('click', function() {
                $(".content-block-settings-visible").removeClass("content-block-settings-visible");

                var $widget = $(this).closest('div.widget');
                var $input = $widget.find('input[name^="widget_active["]');

                if ($input.val() == '1') {
                    $input.val('0');
                    list.uiDisableWidget($widget);
                    list.uiCollapseWidget($widget, 800);
                } else {
                    $input.val('1');
                    list.uiEnableWidget($widget);
                    if(!$widget.closest(".widget-list").hasClass("all-collapsed")){
                        list.uiExpandWidget($widget, 800);
                    }
                }
                return false;
            });

            // Event handler -- toggle the widget area open or closed
            $elem.find('.content-block-toggle-open-button').on('click', function() {
                var $widget = $(this).closest('div.widget');

                if ($widget.hasClass('content-block-collapsed')) {
                    list.uiExpandWidget($widget, 800);
                } else {
                    list.uiCollapseWidget($widget, 800);
                }
            });

            // Expand collapsed content blocks when they're clicked
            $elem.on('click', function(e){
                if($(this).hasClass("content-block-collapsed") && !$(this).hasClass('widget-disabled')){
                    $elem.find('.content-block-toggle-open-button').triggerHandler('click');
                }
            });

            // Settings (cog) menu button click
            $elem.on('click', '.content-block-settings-button', function(){
                $closestWidget = $(this).closest(".widget");

                var nodeActive = false;
                if($closestWidget.hasClass("content-block-settings-visible")){
                    nodeActive = true;
                }
                $(this).closest(".widgets-sel").find(".content-block-settings-visible").not(this).removeClass("content-block-settings-visible");
                if(nodeActive === true){
                    $closestWidget.removeClass("content-block-settings-visible");
                    $("body").off("click", widgetSettingsClick);
                } else if(nodeActive === false){
                    $closestWidget.addClass("content-block-settings-visible");
                    $("body").on("click", widgetSettingsClick);
                }
            });

            // Checks if click is out of bounds of settings (cog) menu, and close dropdown
            function widgetSettingsClick(e){
                if(!$(e.target).parents(".content-block-settings-dropdown").length && !$(e.target).is('.content-block-settings-dropdown') && !$(e.target).is('.content-block-settings-button')) {
                    $("body").off("click", widgetSettingsClick);
                    $(".content-block-settings-visible").removeClass("content-block-settings-visible");
                }
            }

        }  // onAjaxSuccess
    };

    this.uiDisableWidget = function($widget) {
        $(this).html('Enable');
        $widget.removeClass("widget-enabled").addClass("widget-disabled");
        $widget.find('.content-block-toggle-open-button').hide();
    }

    this.uiEnableWidget = function($widget) {
        $(this).html('Disable');
        $widget.removeClass("widget-disabled").addClass("widget-enabled");
        $widget.find('.content-block-toggle-open-button').show();
    }

    this.uiCollapseWidget = function($widget, time) {
        var $button = $widget.find('.content-block-toggle-open-button');
        $button.removeClass('icon-keyboard_arrow_up').addClass('icon-keyboard_arrow_down');
        $button.attr('title', 'Expand').find('.-vis-hidden').html("Collapse content block");
        
        var collapsedHeight = $widget.find(".widget-header--main").height() + $widget.find(".content-block-title").height() + 33;
        $widget.attr("data-expanded-height", $widget.outerHeight());
        $widget.stop().animate({height: collapsedHeight}, time, "easeInOutCirc", function(){
            $(this).addClass("content-block-collapsed");
        });
    };

    this.uiExpandWidget = function($widget, time) {
        var $button = $widget.find('.content-block-toggle-open-button');
        $button.removeClass('icon-keyboard_arrow_down').addClass('icon-keyboard_arrow_up');
        $button.attr('title', 'Collapse').find('.-vis-hidden').html("Collapse content block");
        
        var animateHeight = $widget.attr("data-expanded-height");
        $widget.removeClass("content-block-collapsed").stop().animate({height: animateHeight}, time, "easeInOutCirc", function(){
            $(this).css({"height": ""});
        });
    }

    this.uiCollapseAll = function(time) {
        $list.find(".widget:not(.widget-disabled)").each(function(){
            list.uiCollapseWidget($(this), time);
        });
    }

    this.uiExpandAll = function(time) {
        $list.find(".widget:not(.widget-disabled)").each(function(){
            list.uiExpandWidget($(this), time);
        });
    }

    this.deleteWidget = function($widget) {
        var $deletedHidden = $widget.find('input[name^="widget_deleted["]');
        $deletedHidden.val('1');

        var widgetTitle = $widget.find(".widget-header-content-block-title").html();
        var $undoButton = $('<div class="content-block-button-wrap"><button type="button" class="button button-grey button-regular button-block icon-after icon-delete undo-content-block-button"><span class="button-unhover-state">'+widgetTitle+' removed</span> <span class="button-hover-state">undo</span></button></div>');
        $undoButton.insertBefore($widget);

        $widget.addClass('content-block-removed').removeClass('content-block-settings-visible').slideUp(300, function(){
            $widget.hide();
            $undoButton.on('click', function() {
                list.undoDeleteWidget($widget, $undoButton);
            });
        });
    }

    this.undoDeleteWidget = function($widget, $undoButton) {
        var $deletedHidden = $widget.find('input[name^="widget_deleted["]');
        $deletedHidden.val('0');

        $widget.show();

        if ($widget.closest('.widget-list').hasClass('all-collapsed')) {
            $widget.addClass('content-block-collapsed');
        } else {
            $widget.removeClass('content-block-collapsed');
        }

        $widget.css({'height': ''});
        $widget.slideDown(300, function(){
            $widget.removeClass('content-block-removed');
            $undoButton.remove();
        });
    }

    // Sorting for widgets
    $list.find(".widgets-sel").sortable({
        placeholder: 'content-block-placeholder',
        handle: '.content-block-reorder-button',
        cancel: '',
        axis: "y",
        revert: 150,
        cursor: "-webkit-grabbing",
        helper: "clone",
        start: function(e, ui){
            // Disable TinyMCE because it breaks when being dragged
            var $richtext = ui.helper.find('.field-element--richtext');
            if ($richtext.length) {
                var mce_id = $richtext.find('textarea').first().attr('id');
                tinyMCE.execCommand('mceRemoveEditor', false, mce_id);
            }

            placeholderHeight = ui.item.outerHeight();
            ui.placeholder.height(placeholderHeight+25);
            $('<div class="content-block-placeholder-animator" data-height="' + placeholderHeight + '"></div>').insertAfter(ui.placeholder);
        },
        change: function( event, ui ){
            ui.placeholder.stop().height(0).animate({
                height: ui.item.outerHeight()+25
            }, 300);
            placeholderAnimatorHeight = parseInt($(".content-block-placeholder-animator").attr("data-height"));
            $(".content-block-placeholder-animator").stop().height(placeholderAnimatorHeight+25).animate({
                height: 0
            }, 300, function(){
                $(this).remove();

                placeholderHeight = ui.item.outerHeight();
                $('<div class="content-block-placeholder-animator" data-height="' + placeholderHeight + '"></div>').insertAfter(ui.placeholder);
            });
        },
        stop: function(e, ui){
            // Re-enable TinyMCE because it breaks when being dragged
            var $richtext = ui.item.find('.field-element--richtext');
            if ($richtext.length) {
                var mce_id = $richtext.find('textarea').first().attr('id');
                tinyMCE.execCommand('mceAddEditor', false, mce_id);
            }

            $(".content-block-placeholder-animator").remove();

            $('#edit-form').triggerHandler('setDirty');
        },
    });
};

/**
 * Expand/collapse all button
 * Just calls uiExpandAll/uiCollapseAll to do the work
 */
$(document).ready(function() {
    $('.content-block-collapse-button').on('click', function() {
        var $btn = $(this);
        var $list = $("#" + $btn.attr("data-target"));
        var widget_list = $list.data('wl');

        $btn.toggleClass('icon-keyboard_arrow_up icon-keyboard_arrow_down');

        if ($list.hasClass('all-collapsed')) {
            // Already collapsed; expand widgets
            widget_list.uiExpandAll(800);
            $btn.html('Collapse all');
            $list.removeClass('all-collapsed');
        } else {
            // Already expanded; collapse widgets
            widget_list.uiCollapseAll(800);
            $btn.html("Expand all");
            $list.addClass('all-collapsed');
        }
    });
});
