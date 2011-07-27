//TODO rewrite page using history to navigate between links

var $ = goog.dom.getElement;
var $$ = goog.dom.getElementsByTagNameAndClass;

var Wizard = function() {
    var dialog = new goog.ui.Dialog();
    dialog.setButtonSet(false);
    dialog.setHasTitleCloseButton(false);
    dialog.setEscapeToCancel(false);
    dialog.setTitle("Please wait...");
    dialog.setContent(''+
            '<div class="loading-message">'+
              '<p>Currently fetching content for this page...</p>'+
              '<img src="/css/custom-theme/images/loading_indeterminate.gif" '+
                   'width="500" height="22" />'+
            '</div>');
    this.loading_dialog_ = dialog;
    
    this.current_page = $$('div', 'current-page')[0];
    this.setupPageStyle(this.current_page);
    //this.configLinks(this.current_page);
    try {
        this.history = new goog.history.Html5History();
        this.history.setUseFragment(false);
        this.history.setPathPrefix("/wizard/page/");
    } catch (e) {
        this.history = new goog.History();
    }
    this.history.setEnabled(true);
    var $this = this;
    goog.events.listen(this.history,goog.history.EventType.NAVIGATE,function(e){
        $this.processHistoryEvent(e);
    });
    this.registerButtons();
}

Wizard.prototype.processHistoryEvent = function(evt) {
    var tok = evt.token;
    var prevTok = parseInt(goog.dom.getTextContent($('page-number')));
    if (!tok) {
        this.history.replaceToken(1);
        tok = "1";
    } else if (!goog.string.isNumeric(tok)) {
        throw Error("History tokens must be numeric.");
    }
    tok = parseInt(tok);
    if (tok == prevTok) return;
    goog.dom.setTextContent($('page-number'), tok);
    var href = '/ajax/wizard/page/'+tok;
    var $this = this;
    goog.net.XhrIo.send(href, function(netEvt) {
        var xhr = netEvt.target;
        var new_page = goog.dom.createDom('div', 'page');
        var wrapper = goog.dom.createDom('div', 'page-wrapper');
        goog.dom.appendChild(new_page, wrapper);
        var inner = goog.dom.createDom('div', 'page-content');
        goog.dom.appendChild(wrapper, inner);
        inner.innerHTML = xhr.getResponseText();

        $this.updateWizard(new_page, tok > prevTok);
    });
}

Wizard.prototype.updateWizard = function(dom, fromRight) {
    var parent = $$(null, 'content')[0];
    dom.style.position = "absolute";
    dom.style.display = "block";
    var pos_r = goog.style.getPosition($$(null, 'right-page')[0]);
    var pos_l = Wizard.LEFT_POSITION;
    var pos_c = goog.style.getPosition(this.current_page);
    var flyIn = new goog.fx.AnimationParallelQueue();
    var coord_r = [pos_r.x, pos_r.y];
    var coord_c = [pos_c.x, pos_c.y];
    var coord_l = [pos_l.x, pos_l.y];
    var old_end, new_start;
    if (fromRight) {
        goog.style.setPosition(dom, pos_r);
        old_end = coord_l;
        new_start = coord_r;
    } else {
        goog.style.setPosition(dom, pos_l);
        old_end = coord_r;
        new_start = coord_l;
    }
    var $this = this;
    goog.dom.appendChild(parent, dom);
    flyIn.add(new goog.fx.dom.Slide(this.current_page, coord_c, old_end, 
                Wizard.ANIMATION_LENGTH, Wizard.ANIMATION_EASING));
    flyIn.add(new goog.fx.dom.Slide(dom, new_start, coord_c,
                Wizard.ANIMATION_LENGTH, Wizard.ANIMATION_EASING));
    goog.events.listen(flyIn, goog.fx.Transition.EventType.BEGIN, function(e) {
        if (fromRight) {
            $this.registerNextButton(dom);
        } else {
            $this.registerPrevButton(dom);
        }
    });
    goog.events.listen(flyIn, goog.fx.Transition.EventType.END, function(e) {
        goog.dom.removeNode($this.current_page);
        goog.dom.classes.add(dom, 'current-page');
        if (fromRight) {
            $this.registerPrevButton(dom);
        } else {
            $this.registerNextButton(dom);
        }
        $this.registerFormSubmit(dom);
        $this.current_page = dom;
        $this.unloading();
    });
    flyIn.play();
};

Wizard.prototype.setLoading = function(loading) {
    this.loading_dialog_.setVisible(loading);
};

Wizard.prototype.loading = function() {
    if (this.loadingGif_ || this.loadingDiv_) this.unloading();
    var div = goog.dom.createDom('div', 'modal');
    var loading = goog.dom.createDom('span', 'loading-wheel');
    var pagesize = goog.dom.getViewportSize();
    loading.style.position = 'fixed';
    loading.style.zIndex = 11;
    loading.style.visibility = "hidden";
    
    goog.dom.appendChild($$('body')[0], div);
    goog.dom.appendChild($$('body')[0], loading);
    var size = goog.style.getSize(loading);
    var position = new goog.math.Coordinate((pagesize.width-size.width)/2,
            (pagesize.height-size.height)/2);
    goog.style.setPosition(loading, position);
    loading.style.visibility = "visible";

    this.loadingDiv_ = div;
    this.loadingGif_ = loading;
};

Wizard.prototype.unloading = function() {
    if (this.loadingDiv_) {
        goog.dom.removeNode(this.loadingDiv_);
        this.loadingDiv_ = null;
    }
    if (this.loadingGif_) {
        goog.dom.removeNode(this.loadingGif_);
        this.loadingGif_ = null;
    }
};

Wizard.prototype.registerFormSubmit = function(opt_page) {
    var page = opt_page || this.current_page;
    var form = $$('form', null, page);
    var $this = this;
    if (form && form.length > 0) {
        form = form[0];
        goog.events.listen(form, goog.events.EventType.SUBMIT, function(evt) {
            $this.loading();
            evt.preventDefault();
            var in_data = goog.dom.forms.getFormDataString(form);
            var callback = function(evt) {
                var xhr = evt.target;
                var out_data = xhr.getResponseJson();
                console.log(xhr.getResponseText());
                console.log(xhr.getResponseType());
                console.log(out_data);
                if (out_data.success) {
                    // Move on to the next thing.
                    var current_page = parseInt(goog.dom.getTextContent(
                            $('page-number')));
                    $this.updateHistory(current_page+1);
                } else {
                    // Display error message.
                    var message = out_data.reason;
                    var flash = $('flash');
                    flash.style.color = "red";
                    flash.style.fontWeight = "bold";
                    flash.style.textAlign = "center";
                    goog.dom.setTextContent(flash, message);
                }
            };
            goog.net.XhrIo.send("/ajaxwizform.php", callback, "POST", in_data);
        });
    } 
};

Wizard.prototype.registerPrevButton = function(opt_page) {
    var page = opt_page || this.current_page;
    var prev_link = $$(null, 'prev-link', page);
    var $this = this;
    if (prev_link && prev_link.length > 0 && prev_link[0].rel != 'last') {
        prev_link = prev_link[0];
        $$('div', 'left-page')[0].style.visibility = 'visible';
    } else {
        prev_link = null;
        $$('div', 'left-page')[0].style.visibility = 'hidden';
    }
    if (prev_link) {
        prev_link
        goog.events.listen(prev_link, goog.events.EventType.CLICK, function(e) {
            if (prev_link.nodeName.toUpperCase()=="A") {
                e.preventDefault();
                var href = new goog.Uri(prev_link.href).getPath();
                if (goog.string.startsWith(href, '/wizard/page/')) {
                    href = href.substring(13);
                }
                if (goog.string.endsWith(href, '/')) {
                    href = href.substring(0, href.length-1);
                }
                $this.updateHistory(href);
            } 
        });
    }
};

Wizard.prototype.registerNextButton = function(opt_page) {
    var page = opt_page || this.current_page;
    var next_link = $$(null, 'next-link', page);
    var $this = this;
    if (next_link && next_link.length > 0 && next_link[0].rel != 'last') {
        next_link = next_link[0];
        $$('div', 'right-page')[0].style.visibility = 'visible';
    } else {
        next_link = null;
        $$('div', 'right-page')[0].style.visibility = 'hidden';
    }
    if (next_link) {
        goog.events.listen(next_link, goog.events.EventType.CLICK, function(e) {
            if (next_link.nodeName.toUpperCase()=="A") {
                e.preventDefault();
                var href = new goog.Uri(next_link.href).getPath();
                if (goog.string.startsWith(href, '/wizard/page/')) {
                    href = href.substring(13);
                }
                if (goog.string.endsWith(href, '/')) {
                    href = href.substring(0, href.length-1);
                }
                $this.updateHistory(href);
            }
        });
    }
};

Wizard.prototype.registerButtons = function(opt_page) {
    this.registerNextButton(opt_page);
    this.registerPrevButton(opt_page);
    this.registerFormSubmit(opt_page);
};

Wizard.prototype.updateHistory = function(where, opt_title) {
    this.history.setToken(where, opt_title);
}

Wizard.prototype.setupPageStyle = function(page) {
    var position = goog.style.getPosition(page);
    page.style.position = "absolute";
    goog.style.setPosition(page, position);
}



Wizard.WIDTH = 700;
Wizard.HEIGHT = 700;
Wizard.PADDING = 25;
Wizard.TOP = 50;
Wizard.ANIMATION_LENGTH = 700;
Wizard.ANIMATION_EASING = goog.fx.easing.inAndOut;

Wizard.LEFT_POSITION = new goog.math.Coordinate(-Wizard.WIDTH, Wizard.TOP);
// Right position can't be used since the screen size can change.

var wiz = new Wizard();
