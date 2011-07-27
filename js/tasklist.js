// Note: This file reqiures Google Closure, jQuery, and jQuery-UI to work
// correctly.  I would recommend compiling all of them together into one
// file when complete.

// TODO Add methods for when both the Add Account and Add Task methods are 
// clicked.
// TODO Add all account manipulation functions

/*
goog.require('goog.History');
goog.require('goog.Uri');
goog.require('goog.date');
goog.require('goog.date.Date');
goog.require('goog.dom');
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
goog.require('goog.i18n.DateTimeFormat');
goog.require('goog.i18n.DateTimeParse');
goog.require('goog.i18n.DateTimeSymbols');
goog.require('goog.i18n.DateTimeSymbols_en_US');
goog.require('goog.locale');
goog.require('goog.net.XhrIo');
goog.require('goog.string');
goog.require('goog.style');
goog.require('goog.ui.Button');
goog.require('goog.ui.ButtonRenderer');
goog.require('goog.ui.ButtonSide');
goog.require('goog.ui.Component');
goog.require('goog.ui.CustomButton');
goog.require('goog.ui.CustomButtonRenderer');
goog.require('goog.ui.DatePicker');
goog.require('goog.ui.ProgressBar');
goog.require('goog.ui.Slider');
goog.require('goog.ui.decorate');
goog.require('goog.ui.editor.DefaultToolbar');
goog.require('goog.ui.editor.ToolbarController');
*/

var $ = goog.dom.getElement;
var $$ = goog.dom.getElementsByTagNameAndClass;

/**
 * Utility class for manipulating the screen's tasks and description items.
 * @constructor
 */
var Util = function() {
    this.dialog_ = new goog.ui.Dialog();
    this.dialog_.setButtonSet(false);
    this.dialog_.setEscapeToCancel(false);
    this.dialog_.setHasTitleCloseButton(false);
    this.dialog_.setTitle("Please wait...");
    this.dialog_.setContent(''+
            '<div class="loading-message">'+
              '<p>Currently fetching content for this page...</p>'+
              '<img src="/css/custom-theme/images/loading_indeterminate.gif" '+
                   'width="500" height="22" />'+
            '</div>');

    try {
        this.history = new goog.history.Html5History();
        this.history.setUseFragment(false);
    } catch (e) {
        this.history = new goog.History();
    }
    this.historyEvent = goog.history.EventType.NAVIGATE;
    this.history.setEnabled(true);
    var $this = this;
    goog.events.listen(this.history, this.historyEvent, function(e) {
        e.preventDefault();
        $this.historyHandler(e);
    });
};

/**
 * Constants for Task event types.
 * @enum {string}
 */
Util.EventType = {
    VIEW: 'view',
    EDIT: 'edit',
    COPY: 'copy',
    NEW: 'new',
    DELETE: 'delete',
    TASKLIST: 'tasklist'
}

/** 
 * The default amount of time to display the message in the header.
 * @const 
 */ 
Util.FLASH_MESSAGE_DISPLAY_SECONDS = 5;

/**
 * Static function for return the path of an href.  If the passed element
 * is a string, the value is just returned.
 * @static
 * @param {(Element|string)} anchor An anchor to grab the path from.
 * @return {string} The path part of a URI.
 */
Util.anchorToHref = function(anchor) {
    if (goog.isString(anchor)) {
        return anchor;
    } else {
        return new goog.Uri(anchor.href).getPath();
    }
};


/**
 * Static function for trimming a string with a given set of characters at the 
 * beginning and end of a string.
 * @static
 * @param {string} string The string to trim
 * @param {string=} opt_chars An optional string containing characters to trim.
 * @return {string} The trimmed string.
 */
Util.trim = function(string, opt_chars) {
    var specials = new RegExp("[.*+?|()\\[\\]{}\\\\]", "g");
    var chars = opt_chars ||"["+opt_chars.replace(specials, "$&")+"]";
    var re = new RegExp("^"+chars+"*(.*?)"+chars+"*$","g");
    return string.replace(re, "$1");
};

/**
 * Update the URL bar's current URL (without refreshing the page).
 * @this {Util}
 * @param {(Element|string)} anchor An &lt;a&gt; element, or a string link.
 * @param {string=} opt_string A string to use to replace the title.
 */
Util.prototype.updateHistory = function(anchor, opt_title) {
    var url;
    if (goog.dom.isNodeLike(anchor)) {
        url = new goog.Uri(anchor.href).getPath();
    } else {
        url = anchor;
    }
    url = Util.trim(url, '/') + '/';
    this.history.setToken(url, opt_title);
};

/**
 * Puts a loading screen on the page for whenever AJAX content is being fetched
 * @param {boolean} visible Whether or not to show the loading screen.
 */
Util.prototype.setLoading = function(visible) {
    this.dialog_.setVisible(visible);
};

/**
 * Updates the URL bar's current URL (without refreshing the page). This also
 * replaces the current item in the history stack instead of adding a new one
 * @this {Util}
 * @param {(Element|string)} anchor An &lt;a&gt; element, or string link.
 * @param {string=} opt_string A string to use to replace the title.
 */
Util.prototype.replaceHistory = function(anchor, opt_title) {
    var url;
    if (goog.dom.isNodeLike(anchor)) {
        url = new goog.Uri(anchor.href).getPath();
    } else {
        url = anchor;
    }
    url = Util.trim(url, '/')+'/';
    this.history.replaceToken(url, opt_title);
};

/**
 * Handles the NAVIGATE event for the history object.
 * @this {Util}
 * @param {Event} evt The event to handle.
 */
Util.prototype.historyHandler = function(evt) {
    this.setLoading(true);
    if (evt.token) {
        var token = Util.trim(evt.token, '/').toLowerCase();
        var $this = this;
        var eventType;

        if (goog.string.endsWith(token, "edit")) {
            eventType = Util.EventType.EDIT;
            this.getTaskEditor(evt.token);
        } else if (goog.string.endsWith(token, "copy")) {
            eventType = Util.EventType.COPY;
            this.getTaskEditor(evt.token);
        } else if (goog.string.endsWith(token, "new")) {
            eventType = Util.EventType.NEW;
            this.getTaskEditor(evt.token);
        } else if (goog.string.endsWith(token, "delete")) {
            eventType = Util.EventType.DELETE;
        } else if (token.split("/").length-1 == 0) {
            eventType = Util.EventType.TASKLIST;
            this.getTaskList(evt.token);
        } else {
            eventType = Util.EventType.VIEW;
            this.getTaskDescription(evt.token);
        }
    } else {
        // Looks like the token is null (at the root view), so lets update it
        var params = [['li', 'selected', 'tasks-list'],
                      ['li', null, 'tasks-list'],
                      ['li', 'selected', 'accounts-list'],
                      ['li', null, 'accounts-list']];
        for (var i = 0; i < params.length; i++) {
            var p = params[i];
            var li = goog.dom.getElementsByTagNameAndClass(p[0], p[1], $(p[2]));
            if (li.length != 0) {
                var a = goog.dom.getElementsByTagNameAndClass('a', null, li[0]);
                this.setSelected(li[0]);
                this.replaceHistory(Util.anchorToHref(a[0]));
                return;
            }
        }
        // We should never ever ever make it here.
        throw "wtf";
    }
};

/**
 * Updates the screen to register all the items related with viewing a task
 * description (not editing one).  Uses a progress bar, and some buttons.
 * @this {Util}
 */
Util.prototype.registerViewingItems = function() {
    var progress = new goog.ui.ProgressBar();
    progress.render($('task-progress-bar'));
    progress.setValue(goog.dom.getRawTextContent($('task-progress')));

    var edit_link     = $('edit-task-link');
    var copy_link     = $('create-copy-link');
    var delete_link   = $('delete-task-link');
    
    var edit_button   = goog.ui.decorate(edit_link);
    var copy_button   = goog.ui.decorate(copy_link);
    var delete_button = goog.ui.decorate(delete_link);

    var edit_link_a   = goog.dom.getElementsByTagNameAndClass('a', null,
            edit_link)[0];
    var copy_link_a   = goog.dom.getElementsByTagNameAndClass('a', null,
            copy_link)[0];
    var delete_link_a = goog.dom.getElementsByTagNameAndClass('a', null,
            delete_link)[0];
    
    // TODO I'm not sure that we want to override the /delete/ method yet.  It 
    // also still needs to be fixed.
    var $this = this;
    goog.array.forEach([edit_link_a, copy_link_a], function(elem, ind, arr) {
        goog.events.listen(elem, goog.events.EventType.CLICK, function(evt) {
            evt.preventDefault();
            $this.updateHistory(elem);
        });
    });
};

/**
 * Registers a datepicker item with an input box.
 * @param {Element} widget The div to attach the datepicker to.
 * @param {Element} inputwrapper The inputwrapper element to use with the 
 *              datepicker.
 * @param {string=} opt_format An optional format to parse and format the date 
 *              with.
 */
Util.registerDatePicker = function(widget, input_wrapper, opt_format) {
    var dp = new goog.ui.DatePicker();
    var input = goog.dom.getElementsByTagNameAndClass('input', null, 
            input_wrapper)[0];
    var image = goog.dom.getElementsByTagNameAndClass(null, 'date-icon', 
            input_wrapper)[0];

    /**
     * Function for hiding the datepicker.
     * @type {function()}
     */
    var hideDatePicker = function() {
        goog.style.showElement(widget, false);
    };

    /**
     * Function for showing the datepicker
     * @type {function()}
     */
    var showDatePicker = function() {
        goog.style.showElement(widget, true);
    };

    var formatter = new goog.i18n.DateTimeFormat(opt_format || 'MM/dd/yyyy');
    var parser    = new goog.i18n.DateTimeParse(opt_format || 'MM/dd/yyyy');

    /**
     * Functoin for setting the date on the datepicker to the value from the
     * input box.
     * @type {function()}
     */
    var setDate = function() {
        var d = new goog.date.Date();
        var value = goog.string.trim(input.value);
        var parsed = parser.strictParse(value, d);
        input.value = value;
        if (value.length > 0) {
            dp.setDate(d);
        }
    }

    setDate();

    if (!widget.id) {
        widget.id = "goog-datepicker-"+goog.getUid(widget);
    }
    goog.i18n.DateTimeSymbols = goog.i18n.DateTimeSymbols_en_US;
    goog.style.showElement(widget, false);
    dp.create(widget);
    dp.setAllowNone(false);
    dp.setUseSimpleNavigationMenu(true);
    
    goog.events.listen(image, goog.events.EventType.CLICK, function(evt) {
        showDatePicker();
    });

    goog.events.listen(input, goog.events.EventType.CHANGE, setDate);

    goog.events.listen(document,goog.events.EventType.MOUSEDOWN, function(evt) {
        var target = evt.target;
        if (target.id != widget.id && !goog.dom.contains(widget, target) &&
                !goog.dom.contains(input_wrapper, target) && 
                goog.style.isElementShown(widget)) {
            hideDatePicker();
        }
    });

    goog.events.listen(dp, goog.ui.DatePicker.Events.CHANGE, function(evt) {
        input.value = formatter.format(evt.date);
        hideDatePicker();
    });

    goog.events.listen(input, goog.events.EventType.KEYDOWN, function(evt) {
        if (evt.keyCode == goog.events.KeyCodes.TAB) {
            hideDatePicker();
        }
    });
};

/**
 * Updates the screen to register all items related with editing a task 
 * description (not viewing one).  Uses a slider, some buttons, two datepickers.
 * @this {Util}
 */
Util.prototype.registerEditingItems = function() {
    var element = $("task-progress-edit-slider");
    var input = $('task-progress-edit');
    var slider = new goog.ui.Slider();
    slider.setMinimum(0);
    slider.setMaximum(100);
    slider.decorate(element);
    slider.setValue(parseInt(input.value));
    slider.setMoveToPointEnabled(true);

    // TODO write some mechanism to ensure that the given value is an int
    slider.addEventListener(goog.ui.Component.EventType.CHANGE, function(evt) {
        $('task-progress-edit').value = slider.getValue();
    });

    goog.events.listen(input, goog.events.EventType.CHANGE, function(evt) {
        slider.setValue(parseInt(input.value));
    });

    //Datepicker stuff
    var start_widget = $('start-date-widget');
    var end_widget = $('end-date-widget');
    var start_input_wrapper = $('task-start-date-edit').parentElement;
    var end_input_wrapper = $('task-expiration-date-edit').parentElement;

    Util.registerDatePicker(start_widget, start_input_wrapper);
    Util.registerDatePicker(end_widget, end_input_wrapper);
    
    // Rich Text Editor
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
            $('task-details-toolbar'));
    var toolbarController = new goog.ui.editor.ToolbarController(richEditor,
            richToolbar);
    goog.events.listen(richEditor, goog.editor.Field.EventType.DELAYEDCHANGE,
            function() {
               $('task-details-edit').value = richEditor.getCleanContents();
            });
    richEditor.makeEditable();
    richEditor.setHtml(false, $('task-details-edit').value);
    goog.style.showElement($('task-details-edit'), false);

    // Apparently in the current version of closure this doesn't work in
    // WebKit, so no Chrome. :(
    var details_label = $('task-details-label');
    goog.events.listen(details_label, goog.events.EventType.CLICK, function(e) {
        e.preventDefault();
        richEditor.focus();
    });
};

/**
 * Gets a task description for a given link and puts it in #task-content
 * @param {(Element|string)} anchor The anchor to get the href from
 * @this {Util}
 */
Util.prototype.getTaskDescription = function(anchor) {
    var href = Util.trim(Util.anchorToHref(anchor), '/') + '/';
    var $this = this;
    goog.net.XhrIo.send('/ajax/'+href, function(e) {
        var xhr = e.target;
        $('task-content').innerHTML = xhr.getResponseText();
        $this.registerViewingItems();
        $this.setLoading(false);
    });
};

/**
 * Gets the form for a task description for a link and puts it in #task-content
 * @param {(Element|string)} anchor The anchor to get the href from
 * @this {Util}
 */
Util.prototype.getTaskEditor = function(anchor) {
    var href = Util.trim(Util.anchorToHref(anchor), '/') + '/';
    var $this = this;
    goog.net.XhrIo.send('/ajax/'+href, function(e) {
        var xhr = e.target;
        $('task-content').innerHTML = xhr.getResponseText();
        $this.registerEditingItems();
        $this.setLoading(false);
    });
};

/**
 * Gets the list of tasks and puts it in #tasks-list.
 * @param {(Element|string)} anchor The anchor to get the href from
 * @this {Util}
 */
Util.prototype.getTaskList = function(anchor) {
    var href = Util.trim(Util.anchorToHref(anchor), '/') + '/';
    var $this = this;
    goog.net.XhrIo.send('/ajax/'+href, function(e) {
        var xhr = e.target;
        var list = $('tasks-list');
        list.innerHTML = xhr.getResponseText();
        var items = goog.dom.getElementsByTagNameAndClass('li', null, list);
        if (items.length == 0) {
            $('task-content').innerHTML = '<h2 style="padding:10px 30px;">'+
                'There are no tasks available to display.</h2>';
            $this.setLoading(false);
        } else {
            $this.setSelected(items[0]);
            var a = goog.dom.getElementsByTagNameAndClass('a', null, items[0]);
            $this.getTaskDescription(a[0]);

            goog.array.forEach(items, function(elem, ind, arr) {
                $this.registerForSelection(elem);
                $this.registerForTaskContextMenu(elem);
            });
        }
    });
};

/**
 * Selects the given item (and unselects all the other items around it)
 * @param {Element} list_item The item to select.
 * @this {Util}
 */
Util.prototype.setSelected = function(list_item) {
    var ul = list_item.parentElement;
    var li =goog.dom.getElementsByTagNameAndClass('li', 'selected', ul);
    goog.array.forEach(li, function(elem, ind, arr) {
        goog.dom.classes.remove(elem, 'selected');
    });
    goog.dom.classes.add(list_item, 'selected');
};

/**
 * Returns whether or not the curent list_item is selected
 * @param {Element} list_item The item to check.
 * @this {Util}
 * @return {boolean} Whether or not the list_item is selected.
 */
Util.prototype.isSelected = function(list_item) {
    return goog.dom.classes.has(list_item, "selected");
};

/**
 * Register a list item as being eligible for selection on click.
 * @param {Element} list_item The item to register.
 * @this {Util}
 */
Util.prototype.registerForSelection = function(list_item) {
    var $this = this;
    var anchor = goog.dom.getElementsByTagNameAndClass('a', null, list_item)[0];

    goog.events.listen(anchor, goog.events.EventType.CLICK, function(evt) {
        evt.preventDefault();
        if (goog.dom.classes.has(evt.target, 'edit-task') ||
            goog.dom.classes.has(evt.target, 'edit-account')) return;
        if (!$this.isSelected(list_item)) {
            $this.setSelected(list_item);
            $this.updateHistory(anchor);
        }
    });
};

/**
 * Determins whether or not a list item has been registered for a context menu.
 * @param {Element} list_item The list item to check
 */
Util.isRegisteredForContextMenu = function(list_item) {
    return goog.dom.classes.has(list_item, 'context-menu-registered');
};

/**
 * Registers a list item for the context menu.
 * @param {Element} list_item The item to register.
 * @this {Util}
 */
Util.prototype.registerForTaskContextMenu = function(list_item) {
    if (Util.isRegisteredForContextMenu(list_item)) return;
    goog.dom.classes.add(list_item, 'context-menu-registered');
    var anchor = goog.dom.getElementsByTagNameAndClass('a', null, list_item)[0];
    var span = goog.dom.createDom('span', 'edit-account');
    goog.dom.appendChild(anchor, span);
    var $this = this;

    goog.events.listen(span, goog.events.EventType.CLICK, function(evt) {
        var position = goog.style.getPosition(span);
        var size     = goog.style.getSize(span);
        var edit_task_menu = $('edit-task-menu');
        if (edit_task_menu) goog.dom.removeNode(edit_task_menu);
        edit_task_menu = goog.dom.createDom('div', {id: 'edit-task-menu'});
        var ul = goog.dom.createDom('ul');
        var edit = goog.dom.createDom('li', 'edit', 'Edit Task');
        var copy = goog.dom.createDom('li', 'copy', 'Copy Task');
        var dele = goog.dom.createDom('li', 'delete', 'Delete Task');
        goog.dom.appendChild(ul, edit);
        goog.dom.appendChild(ul, copy);
        goog.dom.appendChild(ul, dele);
        goog.dom.appendChild(edit_task_menu, ul);
        edit_task_menu.style.position = 'absolute';
        goog.style.setPosition(edit_task_menu, position.x, position.y + 
            size.height);
        
        var body = goog.dom.getElementsByTagNameAndClass('body', null)[0];
        goog.dom.appendChild(body, edit_task_menu);

        goog.events.listenOnce(document, goog.events.EventType.MOUSEDOWN, 
                function(evt) {
            edit_task_menu = $('edit-task-menu');
            if (edit_task_menu) goog.dom.removeNode(edit_task_menu);
            var what;
            if (evt.target == edit) {
                what = "edit";
            } else if (evt.target == copy) {
                what = "copy";
            } else if (evt.target == dele) {
                what = "delete";
            } else {
                return;
            }
            var href = '/'+Util.trim(Util.anchorToHref(anchor),'/')+'/'+what+
                    '/';
            $this.updateHistory(href);
        });
    });
};

/**
 * Registers the add account button to bring up a popup with some information
 * to fill out.
 * @param {Element} button The button to register to the dialog.
 */
Util.prototype.registerAddAccountButton = function(button) {
    this.account_dialog_ = new goog.ui.Dialog();
    this.account_dialog_.setTitle("Please enter the details for the " +
            "new account.");
    this.account_dialog_.setModal(false);
    this.account_dialog_.setHasTitleCloseButton(true);
    this.account_dialog_.setDraggable(true);
    this.account_dialog_.setEscapeToCancel(true);
    this.account_dialog_.setButtonSet(goog.ui.Dialog.ButtonSet.OK_CANCEL);
    var form = goog.dom.createDom('form', {'action':'/new_account.php',
        'method':'POST'});
    var flash = goog.dom.createDom('div', 'flash');
    var table = goog.dom.createDom('table');
    var items = [{name:'username',text:'Name'},{name:'uri',text:'Link',size:40},
        {name:'password',text:'Password',type:'password'}];
    goog.array.forEach(items, function(item, ind, arr) {
        var tr = goog.dom.createDom('tr');
        var td1 = goog.dom.createDom('td', {'style':'text-align:right;'});
        var td2 = goog.dom.createDom('td');
        var label = goog.dom.createDom('label', {'for':item.name+'_actdial'});
        goog.dom.setTextContent(label, item.text);
        var input = goog.dom.createDom('input', {'type':item.type||'text', 
            'name':item.name, 'id':item.name+'_actdial'});
        if (item.size) input.size = item.size;
        goog.dom.appendChild(td1, label);
        goog.dom.appendChild(td2, input);
        goog.dom.append(tr, td1, td2);
        goog.dom.append(table, tr);
    }, this);
    goog.dom.append(form, flash, table);
    this.account_dialog_.setContent(goog.dom.getOuterHtml(form));
    var $this = this;
    goog.events.listen(button, goog.events.EventType.CLICK, function(evt) {
        evt.preventDefault();
        evt.stopPropagation();
        $this.account_dialog_.setVisible(true);
    });

    var formsubmit = function(evt) {
        evt.preventDefault();
        var form = $$('form', null, 
                $this.account_dialog_.getContentElement())[0];
        var in_data = goog.dom.forms.getFormDataString(form);
        $this.account_dialog_.setVisible(false);
        if (!evt.key || evt.key != 'ok') {
            goog.array.forEach($$('input', null, form), function(input) {
                input.value = '';
            });
            return;
        }
        $this.setLoading(true);
        var callback = function(evt) {
            var xhr = evt.target;
            var out_data = xhr.getResponseJson();
            if (out_data['success']) {
                // Move on to the next thing.
                $this.addAccount(out_data['account']);
                goog.array.forEach($$('input', null, form), function(input) {
                    input.value = '';
                });
            } else {
                $this.account_dialog_.setVisible(true);
                // Display error message.
                var message = out_data['reason'];
                var flash = $$('div', 'flash', form)[0];
                flash.style.color = "red";
                flash.style.fontWeight = "bold";
                flash.style.textAlign = "center";
                goog.dom.setTextContent(flash, message);
            }
            $this.setLoading(false);
        };
        goog.net.XhrIo.send("/ajaxwizform.php", callback, "POST", in_data);
    };

    goog.events.listen(form, goog.events.EventType.SUBMIT, formsubmit);
    goog.events.listen(this.account_dialog_, goog.ui.Dialog.EventType.SELECT,
            formsubmit);
};

/**
 * Adds an account to the list of accounts.
 * @param {Object} account The account object with some useful parameters to 
 *              accept.
 */
Util.prototype.addAccount = function(account) {
    // account['name'], account['uri']
    var account_item = goog.dom.createDom('li');
    var anchor = goog.dom.createDom('a', {'href':'/'+escape(account['name'])});
    goog.dom.appendChild(anchor, goog.dom.createDom('span', 'account-name',
                account['name']));
    goog.dom.appendChild(account_item, anchor);
    this.registerForAccountContextMenu(account_item);
    goog.dom.appendChild($('accounts-list'), account_item);
};

/**
 * Registers an account item for the context menu.
 * @param {Element} list_item The item to register.
 * @this {Util}
 */
Util.prototype.registerForAccountContextMenu = function(list_item) {
    if (Util.isRegisteredForContextMenu(list_item)) return;
    goog.dom.classes.add(list_item, 'context-menu-registered');
    var anchor = goog.dom.getElementsByTagNameAndClass('a', null, list_item)[0];
    var span = goog.dom.createDom('span', 'edit-account');
    goog.dom.appendChild(anchor, span);
    var $this = this;

    goog.events.listen(span, goog.events.EventType.CLICK, function(evt) {
        var position = goog.style.getPosition(span);
        var size     = goog.style.getSize(span);
        var edit_account_menu = $('edit-account-menu');
        if (edit_account_menu) goog.dom.removeNode(edit_account_menu);
        edit_account_menu = goog.dom.createDom('div', 
            {id: 'edit-account-menu'});
        var ul = goog.dom.createDom('ul');
        var edit = goog.dom.createDom('li', 'edit', 'Edit Account Details');
        var dele = goog.dom.createDom('li', 'delete', 'Unlink Account');
        goog.dom.appendChild(ul, edit);
        goog.dom.appendChild(ul, dele);
        goog.dom.appendChild(edit_account_menu, ul);
        edit_account_menu.style.position = 'absolute';
        goog.style.setPosition(edit_account_menu, position.x, position.y + 
            size.height);
        
        var body = goog.dom.getElementsByTagNameAndClass('body', null)[0];
        goog.dom.appendChild(body, edit_account_menu);

        goog.events.listenOnce(document, goog.events.EventType.MOUSEDOWN, 
                function(evt) {
            // TODO allow for editing accounts
            // TODO allow for deleiting accounts
            edit_account_menu = $('edit-account-menu');
            if (edit_account_menu) goog.dom.removeNode(edit_account_menu);
            var what;
            if (evt.target == edit) {
                what = "edit";
            } else if (evt.target == dele) {
                what = "delete";
            } else {
                return;
            }
            var href = '/'+Util.trim(Util.anchorToHref(anchor),'/')+'/'+what+
                    '/';
            //$this.updateHistory(href);
        });
    });
};

/**
 * Registers the message with flash viewer (banner across top).
 * @this {Util}
 * @param {string=} opt_message Optional message to use.  Otherwise, just sets 
 *              events.
 * @param {int=} opt_speed Optional speed to use.
 */
Util.prototype.registerFlashMessage = function(opt_message, opt_speed) {
    var flash = $('flash');
    var flash_inner = $('flash-inner');
    var size, padbox;
    flash.style.overflow = "hidden";
    flash_inner.style.overflow = "hidden";
    var speed = opt_speed || 300;
    if (opt_message) {
        flash_inner.innerHTML = opt_message;
        
        if (!goog.style.isElementShown(flash)) {
            flash.style.height = "auto";
            flash_inner.style.height = "auto";
            size = goog.style.getSize(flash);
            padbox = goog.style.getPaddingBox(flash);
            size.height -= padbox.top + padbox.bottom;
            size.width -= padbox.left + padbox.right;
            goog.style.setSize(flash, size.width, 0);
            goog.style.showElement(flash, true);

            //Animate the flash message by sliding downwards.
            var show = new goog.fx.dom.ResizeHeight(flash, 0, size.height, 
                    speed);
            show.play();
        }
    }
    
    size = goog.style.getSize(flash);
    padbox = goog.style.getPaddingBox(flash);
    size.height -= padbox.top + padbox.bottom;
    size.width -= padbox.left + padbox.right;
    var timeout;
    var slideUpAndHide = function () {
        var hide = new goog.fx.dom.ResizeHeight(flash, size.height, 0, speed);
        goog.events.listen(hide, goog.fx.Animation.EventType.END, function() {
            flash.style.display = "none";
            flash.style.height = "auto";
        });
        hide.play();
    };
    var listener = function() {
        clearTimeout(timeout);
        slideUpAndHide();
    };

    // Animate the flash message by sliding upwards.
    var timeout = setTimeout(function() {
        goog.events.unlisten(flash, goog.events.EventType.CLICK, listener);
        slideUpAndHide();
    }, Util.FLASH_MESSAGE_DISPLAY_SECONDS*1000);
    goog.events.listenOnce(flash, goog.events.EventType.CLICK, listener);
}

/**
 * Registers a menu as collapsible.
 * @param {Element} menu to set as collapsible
 * @param {int=} opt_speed The speed to set. Optional.
 */
Util.registerCollapsibleMenu = function(menu, opt_speed) {
    var header = goog.dom.getElementsByTagNameAndClass('span', 'header', 
            menu)[0];
    var list   = goog.dom.getElementsByTagNameAndClass('ul', 'list', menu)[0];
    var collapser = goog.dom.createDom('span', 'ui-icon ui-icon-triangle-1-s');
    var li = goog.dom.getElementsByTagNameAndClass('li', null, list);
    var speed = opt_speed || 300;
    goog.dom.insertChildAt(header, collapser, 0);

    goog.events.listen(header, goog.events.EventType.CLICK, function(evt) {
        var anim, height;
        if (goog.dom.classes.has(list, 'collapsed-list')) {
            // UNCOLLAPSE
            list.style.overflow = "";
            height = goog.style.getSize(list).height;
            list.style.display = "";
            anim = new goog.fx.dom.ResizeHeight(list, 0, height, speed);
            goog.dom.classes.addRemove(collapser, 'ui-icon-triangle-1-e',
                'ui-icon-triangle-1-s');
            goog.events.listen(anim, goog.fx.Animation.EventType.END, 
                    function() {
                goog.dom.classes.remove(list, 'collapsed-list');
                list.style.height = "";
            });
        } else {
            // COLLAPSE
            list.style.overflow = "hidden";
            height = goog.style.getSize(list).height;
            anim = new goog.fx.dom.ResizeHeight(list, height, 0, speed);
            goog.dom.classes.addRemove(collapser, 'ui-icon-triangle-1-s',
                'ui-icon-triangle-1-e');
            goog.events.listen(anim, goog.fx.Animation.EventType.END, 
                    function() {
                goog.dom.classes.add(list, 'collapsed-list');
                list.style.display = "none";
                list.style.height = "";
            });
        }
        anim.play();
    });
};

/**
 * Registers the add task button to work with JavaScript
 * @param {Element} button The add task button
 */
Util.prototype.registerAddTaskButton = function(button) {
    var $this = this;
    goog.events.listen(button, goog.events.EventType.CLICK, function(evt) {
        evt.preventDefault();
        evt.stopPropagation();
        $this.updateHistory(Util.anchorToHref(button));
    });
};


var t = new Util();
t.registerFlashMessage();
var task_li = goog.dom.getElementsByTagNameAndClass('li', null, 
        $('tasks-list'));
goog.array.forEach(task_li, function(elem) {
    t.registerForSelection(elem);
    t.registerForTaskContextMenu(elem);
});

var account_li = goog.dom.getElementsByTagNameAndClass('li', null,
        $('accounts-list'));
goog.array.forEach(account_li, function(elem) {
    t.registerForSelection(elem);
    t.registerForAccountContextMenu(elem);
});

Util.registerCollapsibleMenu($('accounts'));
Util.registerCollapsibleMenu($('tasks'));
t.registerAddTaskButton($('add-task'));
t.registerAddAccountButton($('add-account'));
