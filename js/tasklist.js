// Note: This file reqiures Google Closure, jQuery, and jQuery-UI to work
// correctly.  I would recommend compiling all of them together into one
// file when complete.
//
// TODO add closure-style comments to everything.

//goog.require('goog.History');
goog.require('goog.Uri');
goog.require('goog.editor.Field');
goog.require('goog.editor.plugins.BasicTextFormatter');
goog.require('goog.editor.plugins.EnterHandler');
goog.require('goog.editor.plugins.HeaderFormatter');
goog.require('goog.editor.plugins.LinkBubble');
goog.require('goog.editor.plugins.LinkDialogPlugin');
goog.require('goog.editor.plugins.ListTabHandler');
goog.require('goog.editor.plugins.RemoveFormatting');
goog.require('goog.editor.plugins.UndoRedo');
goog.require('goog.events');
goog.require('goog.history.EventType');
goog.require('goog.history.Html5History');
goog.require('goog.ui.editor.DefaultToolbar');
goog.require('goog.ui.editor.ToolbarController');

var FLASH_MESSAGE_DISPLAY_SECONDS = 5;

var Task = function() {
    // TODO make history work with the old Html4 way, too.
    // TODO figure out why history is messed up for editing items
    this.history = new goog.history.Html5History();
    this.historyEvent = goog.history.EventType.NAVIGATE;
    this.history.setUseFragment(false);
    this.history.setEnabled(true);
    var $this = this;
    goog.events.listen(this.history, this.historyEvent, function(e) {
        e.preventDefault();
        var token = e.token;
        if (e.token) {
            var tok = Task.trim(e.token, '/');
            console.log(tok);
            var edit = Task.endsWith(tok, "edit");
            var copy = Task.endsWith(tok, "copy");
            var isNew = Task.endsWith(tok, "new");
            var delt = Task.endsWith(tok, "delete");
            
            var isTaskList = (tok.split("/").length-1)==0;
            
            if (delt) return;
            $.get('/ajax/'+tok+'/', function(html) {
                console.log("EDIT: "+edit);
                console.log("COPY: "+copy);
                console.log("NEW:  "+isNew);
                if (!isTaskList) {
                    var href=e.token;
                    if (edit||copy) href = href.substring(0,href.length-5);
                    $("#task-content").html(html);
                    var anchor = $("a[href*=\""+e.token+"\"]");
                    var parent = anchor.parent();
                    $("title").text(anchor.text());
                    
                    parent.siblings(".selected").removeClass("selected");
                    parent.addClass("selected");
                    if (edit||copy||isNew) {
                        $this.registerEditingItems();
                        if (edit) {
                            $("title").text("Edit: "+$("title").text());
                        } else if (copy) {
                            $("title").text("Copy: "+$("title").text());
                        } else {
                            $("title").text("New Task");
                        }
                    } else {
                        $this.registerViewingItems();
                    }
                } else {
                    $("#tasks-list").html(html);
                    $("#tasks-list li:first a").click();
                    if ($("#tasks-list li").size() == 0) {
                        $("#task-content").html(
                                "<h2 style='padding:10px 30px'>" +
                                "There are no tasks available to display." +
                                "</h2>");
                    }

                    $("#tasks-list li a").append('<span class="edit-task">'+
                        '</span>');

                    var user = tok;
                    
                    var anchor = $("#accounts-list a[href*='"+e.token+"']");
                    var parent = anchor.parent();
                    $("title").text("Task list for "+ unescape(user));
                    $("#add-task").parent().parent().attr('href', 
                                            '/'+user+'/new/');
                    
                    parent.siblings(".selected").removeClass("selected");
                    parent.addClass("selected");
                }
            });
        }
    });
}

Task.endsWith = function(string, substr, caseInsensitive) {
    if (caseInsensitive) {
        string = string.toLowerCase();
        substr = substr.toLowerCase();
    }
    return string.substring(string.length-substr.length)==substr;
}

Task.startsWith = function(string, substr, caseInsensitive) {
    if (caseInsensitive) {
        string = string.toLowerCase();
        substr = substr.toLowerCase();
    }
    return string.substring(0, substr.length)==substr;
}

Task.trim = function(string, chars) {
    var specials = new RegExp("[.*+?|()\\[\\]{}\\\\]", "g");
    if (!chars) {
        chars = "\\s";
    } else {
        chars = "["+chars.replace(specials, "$&")+"]";
    }
    var re = new RegExp("^"+chars+"*(.*?)"+chars+"*$","g");
    return string.replace(re, "$1");
}

Task.prototype.updateHistory = function(anchor) {
    var url;
    if (typeof anchor != "string") {
        url = anchor.attr('href');
    } else {
        url = anchor;
    }
    url = Task.trim(url, '/') + '/';
    this.history.setToken(url);
}

Task.prototype.registerViewingItems = function() {
    var $this = this;
    $("#task-progress-bar").progressbar({
        value: parseInt($("#task-progress").text())
    });
    $("#edit-task-link").button({
        icons: {
            primary: "ui-icon-pencil"
        }   
    }).css("font-size", "85%").live('click', function(e) {
        e.preventDefault();
        $this.updateHistory($(this));
    });

    $("#delete-task-link").button({
        icons: {
            primary: "ui-icon-trash"
        }
    }).css("font-size", "85%");
};

Task.prototype.registerEditingItems = function() {
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
};

Task.prototype.get = function(anchor, callback) {
    var $this = this;
    $.get('/ajax/'+url, function(html) {
        $("#task-content").html(html);
        $this.registerViewingItems();
        if (callback) callback(html);
    });
};

Task.prototype.getEditor = function(anchor, callback) {
    var $this = this;
    $.get('/ajax/'+url, function(html) {
        $("#task-content").html(html);
        $this.registerEditingItems();
        if (callback) callback(html);
    });
};

Task.prototype.deleteItem = function(anchor, callback) {
    var $this = this;
    $.get('/ajax/'+url, function(json) {
        if (!json) {
            $this.flash("Oops! Something went wrong. Please refresh the page.");
        }
        if (json.success) {
            if (anchor.parent().hasClass('selected')) {
                if (anchor.parent().siblings().size() == 0) {
                    $("#task-content").html(
                        "<h2 style='padding:10px 30px'>There are no tasks "+
                        "available to display.</h2>");
                } else {
                    $this.setSelected(anchor.parent().siblings(":first"), true);
                }
            }
            $(anchor.parent()).hide();
            $("#edit-task-menu").hide();
        }
        $this.flash(json.message);
        if (callback) callback(json);
    }, 'json');
}

Task.prototype.getList = function(anchor, callback) {
    $.get('/ajax/'+url, {}, function(data) {
        $("#tasks-list").html(data);
        $("#tasks-list li:first a").click();
        if ($("#tasks-list li").size() == 0) {
            $("#task-content").html(
                    "<h2 style='padding:10px 30px'>There are no tasks available to display.</h2>");
        }

        $("#tasks-list li a").append('<span class="edit-task"></span>');

        var userReg = new RegExp("^/([^/]+)");
        var user = new goog.Uri(document.location.href).getPath().replace(
            userReg, "$1");
        
        var parent = anchor.parent();
        $("title").text("Task list for "+ unescape(user));
        $("#add-task").parent().parent().attr('href', 
                                '/'+user+'/new/');
        
        parent.siblings(".selected").removeClass("selected");
        parent.addClass("selected");

        if (callback) callback();
    }, "html");
}

Task.prototype.hideMenus = function() {
    $("#edit-account-menu,#edit-task-menu").hide();
}

Task.prototype.setSelected = function(item, click) {
    item.siblings().removeClass('selected');
    if (click) {
        item.children("a").click();
    } else {
        item.addClass('selected');
    }
};

Task.prototype.flash = function(message) {
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

var editHref = window.location.href.substring(
        window.location.href.indexOf('/'));
var editing = editHref.indexOf('edit') != -1 || editHref.indexOf('new') != -1 ||
                editHref.indexOf('copy') != -1;

$(document).ready( function() {
    //TODO add functions for when the add button is clicked and when the edit
    //button is clicked
    
    var task = new Task();
    task.flash();

    $("#flash").click( function() {
        $(this).slideUp();
    });

    $(".navbar .header").click( function(e) {
        $(this).next().slideToggle();
        $(this).children("span")
            .toggleClass("ui-icon-triangle-1-e ui-icon-triangle-1-s");

    }).prepend('<span class="ui-icon ui-icon-triangle-1-s"></span>');
    
    $("#accounts-list li a").append('<span class="edit-account"></span>');
    $("#tasks-list li a").append('<span class="edit-task"></span>');

    //TODO maybe make this automagically work with JavaScript?  For now the 
    //refresh should be fine.
    
    $("#accounts-list li a").live('click', function(e) {
        e.preventDefault();

        if ($(e.target).hasClass('edit-account')) return;

        if ($(this).parent().hasClass("selected")) return;
    
        var list = $("#tasks-list").empty();
        task.updateHistory($(this));
    });

    $("#tasks-list li a").live('click', function(e) {
        e.preventDefault();

        if ($(e.target).hasClass('edit-task')) return;
        if ($(this).parent().hasClass("selected")) return;
        
        var $this = $(this);
        
        task.updateHistory($(this));
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
        var link = Task.trim(href.substring(href.indexOf('/')),'/')+'/edit/';

        task.updateHistory(link);
    });

    $("#edit-task-menu .create-copy").bind('click', function(e) {
        var anchor = $($("#edit-task-menu").data("last-elem"));
        var href = anchor.attr('href');
        var link = Task.trim(href.substring(href.indexOf('/')),'/')+'/copy/';
        task.updateHistory(link);
    });

    $("#edit-task-menu .delete").click( function(e) {
        var anchor = $($("#edit-task-menu").data("last-elem"));
        var href = anchor.attr('href');
        var link = Task.trim(href.substring(href.indexOf('/')),'/')+'/delete/';

        task.deleteItem(link);
    });
    
    // Only do the following if editing a task

    if (editing) {
        task.registerEditingItems();
    } else {
        task.registerViewingItems();
    }
});
