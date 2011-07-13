goog.require('goog.editor.Field');
goog.require('goog.editor.plugins.BasicTextFormatter');
goog.require('goog.editor.plugins.EnterHandler');
goog.require('goog.editor.plugins.HeaderFormatter');
goog.require('goog.editor.plugins.LinkBubble');
goog.require('goog.editor.plugins.LinkDialogPlugin');
goog.require('goog.editor.plugins.ListTabHandler');
goog.require('goog.editor.plugins.RemoveFormatting');
goog.require('goog.editor.plugins.SpacesTabHandler');
goog.require('goog.editor.plugins.UndoRedo');
goog.require('goog.ui.editor.DefaultToolbar');
goog.require('goog.ui.editor.ToolbarController');

var FLASH_MESSAGE_DISPLAY_SECONDS = 5;

function hideMenus() {
    $("#edit-account-menu,#edit-task-menu").hide();
}

function registerViewingItems() {
    $("#task-progress-bar").progressbar({
        value: parseInt($("#task-progress").text())
    });
}

function setSelected(item, click) {
    item.siblings().removeClass('selected');
    if (click) item.children("a").click();
}

function registerEditingItems() {
    $("#task-progress-edit-slider").slider({
        range: 'min',
        value: $("#task-progress-edit").val(),
        slide: function(event, ui) {
            $("#task-progress-edit").val(ui.value);
        }
    });

    $("#task-progress-edit").change( function(e) {
        $("#task-progress-edit-slider").slider('value', $(this).val());
    });

    $("#task-start-date-edit").datepicker();
    $("#task-expiration-date-edit").datepicker();

    $(".submit-section input[type=submit]").button({
        icons: {
            primary: 'ui-icon-gear'
        }
    });

    $("#task-tags-edit").scroll( function(e) {
        var rows = parseInt($(this).attr('rows'));
        if (rows < 5) {
            $(this).attr('rows', rows+1);
        }
    });

    // Stuff for editor
    var richEditor = new goog.editor.Field('task-details-rich-editor');
    richEditor.registerPlugin(new goog.editor.plugins.BasicTextFormatter());
    richEditor.registerPlugin(new goog.editor.plugins.RemoveFormatting());
    richEditor.registerPlugin(new goog.editor.plugins.UndoRedo());
    richEditor.registerPlugin(new goog.editor.plugins.ListTabHandler());
    richEditor.registerPlugin(new goog.editor.plugins.SpacesTabHandler());
    richEditor.registerPlugin(new goog.editor.plugins.EnterHandler());
    richEditor.registerPlugin(new goog.editor.plugins.HeaderFormatter());
    richEditor.registerPlugin(
      new goog.editor.plugins.LinkDialogPlugin());
    richEditor.registerPlugin(new goog.editor.plugins.LinkBubble());

    var buttons = [
        goog.editor.Command.BOLD,
        goog.editor.Command.ITALIC,
        goog.editor.Command.UNDERLINE,
        goog.editor.Command.FONT_COLOR,
        goog.editor.Command.BACKGROUND_COLOR,
        goog.editor.Command.FONT_FACE,
        goog.editor.Command.FONT_SIZE,
        goog.editor.Command.LINK,
        goog.editor.Command.UNDO,
        goog.editor.Command.REDO,
        goog.editor.Command.UNORDERED_LIST,
        goog.editor.Command.ORDERED_LIST,
        goog.editor.Command.INDENT,
        goog.editor.Command.OUTDENT,
        goog.editor.Command.JUSTIFY_LEFT,
        goog.editor.Command.JUSTIFY_CENTER,
        goog.editor.Command.JUSTIFY_RIGHT,
        goog.editor.Command.SUBSCRIPT,
        goog.editor.Command.SUPERSCRIPT,
        goog.editor.Command.STRIKE_THROUGH,
        goog.editor.Command.REMOVE_FORMAT
    ];

    var richToolbar = goog.ui.editor.DefaultToolbar.makeToolbar(buttons,
            $('#task-details-toolbar')[0]);
    var toolbarController = new goog.ui.editor.ToolbarController(richEditor,
            richToolbar);
    goog.events.listen(richEditor, goog.editor.Field.EventType.DELAYEDCHANGE,
            function() {
               $("#task-details-edit").val(richEditor.getCleanContents());
            });
    richEditor.makeEditable();
    richEditor.setHtml(false, $("#task-details-edit").hide().val());
}

function loadDescription(data, edit, callback) {
    $.get("ajaxtaskdescription.php", data, function(html) {
        $("#task-content").html(html);

        if (callback) callback(html);

        if (edit) {
            registerEditingItems();
        } else {
            registerViewingItems();
        }
    }, 'html');
}

function deleteTask(anchor) {
    var data = anchor.attr('href').substring(
            anchor.attr('href').indexOf('?')+1);
    $.get("ajaxtaskdelete.php", data, function(json) {
        if (!json) {
            flash("Oops!  Something went wrong.  Please refresh the page.");
        }
        if (json.success) {
            if (anchor.parent().hasClass('selected')) {
                if (anchor.parent().siblings().size() == 0) {
                    $("#task-content").html(
                        "<h2 style='padding:10px 30px'>There are no tasks "+
                        "available to display.</h2>");
                } else {
                    setSelected(anchor.parent().siblings(":first"), true);
                }
            }
            $(anchor.parent()).hide();
            $("#edit-task-menu").hide();
        }
        flash(json.message);
    }, 'json');
}

function flash(message) {
    if (message) {
        $("#flash-inner").text(message).slideDown();
    }
    $("#flash").click( function() {
        $(this).slideUp();
    });
    setTimeout( function() {
        $("#flash").slideUp();
    }, FLASH_MESSAGE_DISPLAY_SECONDS*1000);
}

var editing = window.location.href.substring(
        window.location.href.indexOf('?')).indexOf('edit') != -1;

$(document).ready( function() {
    //TODO add functions for when the add button is clicked and when the edit
    //button is clicked

    flash();

    $("#flash").click( function() {
        $(this).slideUp();
    });

    $(".navbar .header").click( function(e) {
        e.preventDefault();

        if ($(e.target).hasClass("add-item")) return;

        $(this).next().slideToggle();
        $(this).children("span")
            .toggleClass("ui-icon-triangle-1-e ui-icon-triangle-1-s");

    }).prepend('<span class="ui-icon ui-icon-triangle-1-s"></span>');
    
    $("#accounts-list li a").append('<span class="edit-account"></span>');
    $("#tasks-list li a").append('<span class="edit-task"></span>');

    $("#edit-task-link").button({
        icons: {
            primary: "ui-icon-pencil"
        }   
    }).css("font-size", "85%").live('click', function(e) {
        e.preventDefault();
        var href = $(this).attr('href');
        var data = href.substring(href.indexOf('?')+1);
        
        loadDescription(data, true);
    });

    $("#delete-task-link").button({
        icons: {
            primary: "ui-icon-trash"
        }
    }).css("font-size", "85%");
    //TODO maybe make this automagically work with JavaScript?  For now the 
    //refresh should be fine.
    
    $("#accounts-list li a").live('click', function(e) {
        e.preventDefault();

        if ($(e.target).hasClass('edit-account')) return;

        if ($(this).parent().hasClass("selected")) return;
    
        var list = $("#tasks-list").empty();
        
        var href = $(this).attr('href');
        var data = href.substring(href.indexOf('?')+1);
        var rest = href.substring(href.indexOf('user=')+5);
        var user = unescape(rest.substring(rest.indexOf('&')));
        
        var $this = $(this);
        
        $.get('ajaxtasklist.php', data, function(data) {
            $("#tasks-list").html(data);
            $("#tasks-list li:first a").click();
            if ($("#tasks-list li").size() == 0) {
                $("#task-content").html(
                        "<h2 style='padding:10px 30px'>There are no tasks available to display.</h2>");
            }

            $("#tasks-list li a").append('<span class="edit-task"></span>');
            
            var parent = $this.parent();
            $("title").text("Task list for "+ user);
            
            parent.siblings(".selected").removeClass("selected");
            parent.addClass("selected");
        }, "html");
    });

    
    $("#tasks-list li a").live('click', function(e) {
        e.preventDefault();

        if ($(e.target).hasClass('edit-task')) return;

        if ($(this).parent().hasClass("selected")) return;
        
        var href = $(this).attr('href');
        var data = href.substring(href.indexOf('?')+1);
        
        var $this = $(this);
        
        loadDescription(data, false, function(data) {
            var parent = $this.parent();
            
            parent.siblings(".selected").removeClass("selected");
            parent.addClass("selected");
            
            $("#task-content").html(data);
            
            $("#edit-task-link").button({
                icons: {
                    primary: "ui-icon-pencil"
                }   
            }).css("font-size", "85%");
            $("#delete-task-link").button({
                icons: {
                    primary: "ui-icon-trash"
                }
            }).css("font-size", "85%");
        });
    });

    $("<div id='edit-account-menu' />").html(
            "<ul><li class='edit'>Edit Account</li>"+
            "<li class='delete'>Unlink Account</li></ul>")
        .appendTo("body");

    $("<div id='edit-task-menu' />").html(
            "<ul><li class='edit'>Edit Task</li>"+
            "<li class='create-copy'>Create From Copy</li>"+
            "<li class='delete'>Delete Task</li></ul>").appendTo("body");

    $(".edit-task").live('click', function(e) {
        if ($("#edit-account-menu").is(":visible")) {
            $("#edit-account-menu").hide();
        }
        var parent = $(this).parent()[0];
        if ($("#edit-task-menu").is(":visible") &&
                $("#edit-task-menu").data("last-elem") == parent) {
            $("#edit-task-menu").hide();
        } else {
            var pos = $(this).position();
            var dim = {width: $(this).width(), height: $(this).height()};
            $("#edit-task-menu").css({
                top: pos.top+dim.height,
                left: pos.left
            }).slideDown('fast');
            $("#edit-task-menu").data("last-elem", parent);
        }
    });


    $(".edit-account").live('click', function(e) {
        if ($("#edit-task-menu").is(":visible")) {
                $("#edit-task-menu").hide();
        }
        var parent = $(this).parent()[0];
        if ($("#edit-account-menu").is(":visible") &&
                $("#edit-account-menu").data("last-elem") == parent) {
            $("#edit-account-menu").hide();
        } else {
            var pos = $(this).position();
            var dim = {width: $(this).width(), height: $(this).height()};
            $("#edit-account-menu").css({
                top: pos.top+dim.height,
                left: pos.left
            }).slideDown('fast');
            $("#edit-account-menu").data("last-elem", parent);
        }
    });

    $("#edit-task-menu .edit").bind('click', function(e) {
        var anchor = $($("#edit-task-menu").data("last-elem"));
        var href = anchor.attr('href');
        var data = href.substring(href.indexOf('?')+1)+ "&edit=1";

        loadDescription(data, true, function(data) {
            setSelected(anchor.parent());
            hideMenus();
            $("#task-save-button").button({
                icons: {
                    primary: 'ui-icon-disk'
                }
            }).click( function(e) {
                //TODO use this to submit to the save data php script
            });
        });
    });

    $("#edit-task-menu .create-copy").bind('click', function(e) {
        var anchor = $($("#edit-task-menu").data("last-elem"));
        var href = anchor.attr('href');
        var data = href.substring(href.indexOf('?')+1)+ "&edit=1&copy=1";

        loadDescription(data, true, function(data) {
            setSelected(anchor.parent());
            hideMenus();
            $("#task-save-button").button({
                icons: {
                    parimary: 'ui-icon-disk'
                }
            });
        });
    });

    $("#edit-task-menu .delete").click( function(e) {
        deleteTask($($("#edit-task-menu").data("last-elem")));
    });
    
    // Only do the following if editing a task

    if (editing) {
        registerEditingItems();
    } else {
        registerViewingItems();
    }
});
