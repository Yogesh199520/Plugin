(function ($, sr) {
    var debounce = function (func, threshold, execAsap) {
        var timeout;
        return function debounced() {
            var obj = this,
                args = arguments;
            function delayed() {
                if (!execAsap) func.apply(obj, args);
                timeout = null;
            }
            if (timeout) clearTimeout(timeout);
            else if (execAsap) func.apply(obj, args);
            timeout = setTimeout(delayed, threshold || 100);
        };
    };
    jQuery.fn[sr] = function (fn) {
        return fn ? this.on("resize", debounce(fn)) : this.trigger(sr);
    };
})(jQuery, "menusmartresize");

var menu_supports = (function () {
    var div = document.createElement("div"),
        vendors = "Khtml Ms O Moz Webkit".split(" ");

    return function (prop) {
        var len = vendors.length;

        if (prop in div.style) return true;

        prop = prop.replace(/^[a-z]/, function (val) {
            return val.toUpperCase();
        });

        while (len--) {
            if (vendors[len] + prop in div.style) {
                return true;
            }
        }
        return false;
    };
})();

(function ($, window, document, undefined) {
    var pluginName = "smpnav",
        defaults = {
            mouseEvents: true,
            retractors: true,
            touchOffClose: true,
            clicktest: false,
            windowstest: false,
            debug: false,

            open_current: false,
            collapse_accordions: false,
            scroll_offset: 100,
            disable_transforms: false,
            close_on_target_click: false,
            process_uber_segments: true,
        };

    function Plugin(element, options) {
        this.element = element;

        this.$smpnav = $(this.element);
        this.$menu = this.$smpnav.find("ul.smpnav-menu");

        this.settings = $.extend({}, defaults, options);
        this._defaults = defaults;
        this._name = pluginName;

        this.touchenabled = "ontouchstart" in window || navigator.maxTouchPoints > 0 || navigator.msMaxTouchPoints > 0;

        if (window.navigator.pointerEnabled) {
            this.touchStart = "pointerdown";
            this.touchEnd = "pointerup";
            this.touchMove = "pointermove";
        } else if (window.navigator.msPointerEnabled) {
            this.touchStart = "MSPointerDown";
            this.touchEnd = "MSPointerUp";
            this.touchMove = "MSPointerMove";
        } else {
            this.touchStart = "touchstart";
            this.touchEnd = "touchend";
            this.touchMove = "touchmove";
        }

        this.toggleevent = this.touchEnd == "touchend" ? this.touchEnd + " click" : this.touchEnd; //add click except for IE
        //this.toggleevent = 'click';
        this.transitionend = "transitionend.smpnav webkitTransitionEnd.smpnav msTransitionEnd.smpnav oTransitionEnd.smpnav";

        //TESTING
        if (this.settings.clicktest) this.touchEnd = "click";

        this.init();
    }

    Plugin.prototype = {
        init: function () {
            this.$smpnav.removeClass("smpnav-nojs"); 
            this.$toggles = $('.smpnav-toggle[data-smpnav-target="' + this.$smpnav.data("smpnav-id") + '"]');

            this.initializesmpnav();

            this.initializeTargets();
            this.initializeSubmenuToggleMouseEvents();
            this.initializeSubmenuToggleKeyboardEvents();
            this.initializeRetractors();
            this.initializeResponsiveToggle();

   
        },

        /* Initalizers */

        initializesmpnav: function () {
            var $body = $("body"),
                plugin = this;

            //Only enable the site once
            if (!$body.hasClass("smpnav-enabled")) {
                $body.addClass("smpnav-enabled");
                if (smpnav_data.lock_body == "on") $body.addClass("smpnav-lock");
                if (smpnav_data.lock_body_x == "on") $body.addClass("smpnav-lock-x");

                if (smpnav_data.menu_body != "off") {
                    if (smpnav_data.menu_body_wrapper != "") {
                        $(smpnav_data.menu_body_wrapper).addClass("smpnav-wrap");
                    } else {
                        $body.wrapInner('<div class="smpnav-wrap"></div>'); //unique
                        $("video[autoplay]").each(function () {
                            $(this).get(0).play();
                        });
                    }
                } else $body.addClass("smpnav-disable-menu-body");

                //Move elements outside of menuer
                $("#smpnav-toggle-main, #wpadminbar, .smpnav-fixed-left, .smpnav-fixed-right").appendTo("body");

                var $wrap = $(".smpnav-wrap");

                
                var $main_toggle = $("#smpnav-toggle-main");
                if ($main_toggle.length) {
                   
                    if ((!$main_toggle.hasClass("smpnav-toggle-style-burger_only") && $main_toggle.hasClass("smpnav-togglebar-gap-auto")) || $main_toggle.hasClass("smpnav-togglebar-gap-on")) {
                        var toggleHeight = $main_toggle.outerHeight();
                        $wrap.css("padding-top", toggleHeight);
                        $main_toggle.addClass("smpnav-togglebar-gap-on");

                        
                        if (smpnav_data.menu_body == "off") {
                            
                            var style = "body.smpnav-disable-menu-body{ padding-top:" + toggleHeight + "px; }";

                            
                            if (smpnav_data.breakpoint !== "") {
                                style = "@media screen and (max-width:" + (smpnav_data.breakpoint - 1) + "px){ " + style + " }";
                            }

                            var sheet = null;

                            
                            var style_el = document.getElementById("smpnav-dynamic-css");
                            if (style_el) {
                                sheet = style_el.sheet;
                            } else {
                                style_el = document.createElement("style");
                                style_el.appendChild(document.createTextNode(""));
                                document.head.appendChild(style_el);
                                sheet = style_el.sheet;
                            }

                           
                            if (sheet && "insertRule" in sheet) {
                                sheet.insertRule(style, 0);
                            }
                           
                        }
                    } else if ($("body").hasClass("admin-bar")) $("html").addClass("smpnav-nogap");
                }

               
                var fpos = false; 
                var ua = navigator.userAgent.toLowerCase();

                
                if (/android/.test(ua)) {
                    fpos = true; 
                    if (/android [1-3]\./.test(ua)) fpos = true;
                    
                    else if (/chrome/.test(ua)) fpos = false;
                   
                    else if (/firefox/.test(ua)) fpos = false;

                    
                }

                if (!menu_supports("transform") || fpos || plugin.settings.disable_transforms) {
                    $body.addClass("smpnav-no-transforms");
                }

                //Handle searchbar toggle
                $(".smpnav-searchbar-toggle").each(function () {
                    var $drop = $(this).next(".smpnav-searchbar-drop");

                    $(this)
                        .on(plugin.toggleevent, function (e) {
                            plugin.toggleSearchBar(e, $drop, plugin);
                        })
                        .on("keyup.smpnav-searchbar-toggle", function (e) {
                            if (e.keyCode === 13) {
                                //return/Enter
                                plugin.toggleSearchBar(e, $drop, plugin);
                            }
                        })
                        .on("keydown.smpnav-searbar-toggle", function (e) {
                            if (e.keyCode === 13) {
                                e.stopPropagation();
                            }
                        });
                });
                $(".smpnav-searchbar-drop").on(this.toggleevent, function (e) {
                    e.stopPropagation();
                });

                //When the dropdown loses focus, close it it touch off close is enabled
                if (this.settings.touchOffClose) {
                    $(".smpnav-searchbar-drop .smpnav-search-input").on("blur", function (e) {
                        if ($(this).val() == "" && !toggle_clicked) {
                            $(this).parents(".smpnav-searchbar-drop").removeClass("smpnav-searchbar-drop-open");
                        }
                    });
                }

                var toggle_clicked;
                $(".smpnav-searchbar-toggle").on("mousedown", function (e) {
                    toggle_clicked = true;
                });
                $(".smpnav-searchbar-toggle").on("mouseup", function (e) {
                    toggle_clicked = false;
                });

                //Setup menu panel height
                $(".smpnav").css("max-height", window.innerHeight);
                $(window).menusmartresize(function () {
                    $(".smpnav").css("max-height", window.innerHeight);
                });
            }

            this.$smpnav.appendTo("body");

            if (this.$smpnav.hasClass("smpnav-right-edge")) {
                this.edge = "right";
            } else this.edge = "left";

            this.openclass = "smpnav-open smpnav-open-" + this.edge;

            this.$smpnav.find(".smpnav-panel-close").on("click", function (e) {
                plugin.closesmpnav();
            });
            this.$smpnav.find(".smpnav-sr-close").on("click", function (e) {
                plugin.closesmpnav();
                plugin.focusMainToggle();
            });

            //Set retractor heights
            this.$smpnav.find(".smpnav-submenu-activation").each(function () {
                //var length = $( this ).outerHeight();
                var length = $(this).siblings(".smpnav-target").outerHeight();
                $(this).css({ height: length, width: length });

                //$( this ).css( 'height' , $( this ).parent( '.menu-item' ).height() );
            });

            
            if (plugin.settings.process_uber_segments) {
                this.$smpnav.find(".sub-menu .menu-item.current-menu-item").parents(".menu-item").addClass("current-menu-ancestor");
            }

            //Current open
            if (plugin.settings.open_current) {
                $(".smpnav .smpnav-sub-accordion.current-menu-item, .smpnav .smpnav-sub-accordion.current-menu-ancestor").addClass("smpnav-active");
            }
        },

        initializeTargets: function () {
            var plugin = this;

            this.$smpnav.find(".smpnav-scrollto").removeClass("current-menu-item").removeClass("current-menu-ancestor");

            // Retractors are also targets, so only listen on menu items
            this.$smpnav.on("click", ".menu-item > .smpnav-target", function (e) {
                var scrolltarget = $(this).data("smpnav-scrolltarget");
                if (scrolltarget) {
                    var $target = $(scrolltarget).first();
                    if ($target.length > 0) {
                        //Make current
                        var $li = $(this).parent(".menu-item");
                        $li.siblings().removeClass("current-menu-item").removeClass("current-menu-ancestor");
                        $li.addClass("current-menu-item");
                        var top = $target.offset().top;
                        top = top - plugin.settings.scroll_offset;
                        $("html,body").animate(
                            {
                                scrollTop: top,
                            },
                            1000,
                            "swing",
                            function () {
                                plugin.closesmpnav(); 
                            }
                        );
                        return false; 
                    }
                    
                    else {
                        var href = $(this).attr("href");
                        if (href && href.indexOf("#") == -1) {
                            if (scrolltarget.indexOf("#") == -1) {
                                scrolltarget = "#" + scrolltarget;
                            }
                            window.location = href + scrolltarget; 
                            e.preventDefault();
                        }
                        //No href, no worries
                    }
                } else if ($(this).is("span")) {
                    var $li = $(this).parent(".menu-item");
                    if ($li.hasClass("smpnav-active")) {
                        plugin.closeSubmenu($li, "disabledLink", plugin);
                    } else {
                        plugin.openSubmenu($li, "disabledLink", plugin);
                    }
                }

                if ($(this).prop("tagName").toLowerCase() === "a" && plugin.settings.close_on_target_click) {
                    plugin.closesmpnav();
                }
            });
        },

        initializeSubmenuToggleMouseEvents: function () {
            if (!this.settings.mouseEvents) return;
            if (this.settings.clicktest) return;
            if (this.settings.windowstest) return;

            if (this.settings.debug) console.log("initializeSubmenuToggleMouseEvents");

            var plugin = this;

            this.$smpnav.on("mouseup.menu-submenu-toggle", ".smpnav-submenu-activation", function (e) {
                plugin.handleMouseActivation(e, this, plugin);
            });
            
        },

        disableSubmenuToggleMouseEvents: function () {
            if (this.settings.debug) console.log("disableSubmenuToggleMouseEvents");
            $smpnav.off("mouseover.menu-submenu-toggle");
            $smpnav.off("mouseout.menu-submenu-toggle");
        },

        initializeSubmenuToggleKeyboardEvents: function () {
            if (this.settings.debug) console.log("initializeSubmenuToggleKeyboardEvents");

            var plugin = this;

            this.$smpnav.on("keyup.menu-submenu-toggle", ".smpnav-submenu-activation", function (e) {
                if (e.keyCode === 13) {
                    //return/enter
                    plugin.handleMouseActivation(e, this, plugin);

                    //For accordions, set focus on opposite toggle
                    var $switch = $(this).siblings(".smpnav-submenu-activation").first();
                    if ($switch.length) {
                        setTimeout(function () {
                            $switch.focus();
                        }, 10);
                    }
                }
            });
        },

        initializeRetractors: function () {
            if (!this.settings.retractors) return; 
            var plugin = this;

            //set up the retractors
            this.$smpnav.on("mouseup.smpnav", ".smpnav-retract", function (e) {
                plugin.handleSubmenuRetractorEnd(e, this, plugin);
            });
            this.$smpnav.on("keyup.smpnav", ".smpnav-retract", function (e) {
                if (e.keyCode === 13) {
                    //return/enter
                    plugin.handleSubmenuRetractorEnd(e, this, plugin);
                }
            });
        },

        initializeResponsiveToggle: function () {
            var plugin = this;

            this.$toggles.on("click", "a", function (e) {
               
                e.stopPropagation();
            });

            //Toggle on click
            this.$toggles.on("click", function (e) {
                plugin.toggle($(this), plugin, e);
            });

            
            this.$toggles.on("keydown", function (e) {
                if (e.keyCode === 9) {
                    //TAB
                    var target_id = $(this).data("smpnav-target");
                    var $panel = $('[data-smpnav-id="' + target_id + '"]');
                    if ($panel.length && $panel.hasClass("smpnav-open-target")) {
                        e.preventDefault(); //If we don't do this, then we end up on the second focusable - focus, then tab (keyup)
                        $panel.find("a[href], area[href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), button:not([disabled]), iframe, object, embed, *[tabindex], *[contenteditable]").first().focus();
                    }
                }
            });

            
            if ("smpnav-main" === this.$smpnav.attr("id")) {
                //Special setup for clicking entire toggle bar, which is a div
                $("#smpnav-toggle-main.smpnav-toggle-main-entire-bar").on("keydown", function (e) {
                    if (e.keyCode === 13) {
                        //return/enter
                        plugin.toggle($(this), plugin, e);
                    }
                });
            }
        },

        toggle: function ($toggle, plugin, e) {
            e.preventDefault();
            e.stopPropagation();

            
            if (e.originalEvent.type == "click" && $(this).data("disableToggle")) {
                return;
            }

            if (plugin.$smpnav.hasClass("smpnav-open-target")) {
                //console.log( 'close menu nav' );
                plugin.closesmpnav();
            } else {
                //console.log('open menu nav');
                var toggle_id = $toggle.attr("id");
                var tag = toggle_id == "smpnav-toggle-main" ? "[Main Toggle Bar]" : '"' + $(this).text() + '"';

                
                if ((toggle_id == "smpnav-toggle-main-button" || toggle_id == "smpnav-toggle-main") && $("body").hasClass("smpnav-open")) {
                    //Close all smpnavs
                    $(".smpnav.smpnav-open-target").smpnav("closesmpnav");
                } else {
                    plugin.opensmpnav("toggle: " + tag);
                }
            }

            
            if (e.originalEvent.type !== "click" && e.originalEvent.type !== "keydown") {
                $(this).data("disableToggle", true);
                setTimeout(function () {
                    $(this).data("disableToggle", false);
                }, 1000);
            }

            return false;
        },

        opensmpnav: function (tag) {
            tag = tag || "?";

            var plugin = this;

            if (this.settings.debug) console.log("opensmpnav " + tag);

            $("body").removeClass("smpnav-open-right smpnav-open-left").addClass(this.openclass).addClass("smpnav-transitioning");

            
            $(".smpnav-open-target").removeClass("smpnav-open-target");
            this.$smpnav.addClass("smpnav-open-target").on(plugin.transitionend, function () {
                
                $("body").removeClass("smpnav-transitioning");
                $(this).off(plugin.transitionend);
            });

            this.$smpnav.trigger("smpnav-open");

            this.disableTouchoffClose();
            this.initializeTouchoffClose();

            //When focus leaves Panel, close
            $("body").on("focusin.smpnavPanel", function (e) {
                plugin.closesmpnav();
                plugin.focusMainToggle();
            });
            this.$smpnav.on("focusin.smpnavPanel", function (e) {
                e.stopPropagation();
            });

            //On Escape, close panel
            $(document).on("keyup.smpnavPanel", function (e) {
                if (e.keyCode === 27) {
                    plugin.closesmpnav();
                    plugin.focusMainToggle();
                }
            });
        },

        closesmpnav: function () {
            var plugin = this;

            $("body").removeClass(this.openclass).addClass("smpnav-transitioning");

            this.$smpnav.removeClass("smpnav-open-target").on(plugin.transitionend, function () {
                //if( plugin.settings.debug ) console.log( 'finished submenu close transition' );
                $("body").removeClass("smpnav-transitioning");
                $(this).off(plugin.transitionend);
            });

            this.$smpnav.trigger("smpnav-close");

            this.disableTouchoffClose();

            //Remove focus handlers
            $("body").off("focusin.smpnavPanel");
            this.$smpnav.off("focusin.smpnavPanel");
            $(document).off("keyup.smpnavPanel");
        },

        focusMainToggle: function (plugin) {
            if (this.$smpnav.attr("id") === "smpnav-main") {
                $('#smpnav-toggle-main .smpnav-toggle[data-smpnav-target="smpnav-main"]').focus();
            }
        },

        initializeTouchoffClose: function () {
            if (!this.settings.touchOffClose) return; 

            var plugin = this;
            $(document).on("click.smpnav " + this.touchEnd + ".smpnav", function (e) {
                plugin.handleTouchoffClose(e, this, plugin);
            });
        },
        disableTouchoffClose: function () {
            $(document).off(".smpnav");
        },

        handleMouseActivation: function (e, activator, plugin) {
            if (plugin.settings.debug) console.log("handleMouseover, add mouseout", e);

            var $li = $(activator).parent();

            if ($li.hasClass("smpnav-active")) {
                plugin.closeSubmenu($li, "mouseActivate", plugin);
            } else {
                plugin.openSubmenu($li, "mouseActivate", plugin);
            }

        },

        handleSubmenuRetractorEnd: function (e, li, plugin) {
            e.preventDefault();
            e.stopPropagation();

            var $li = $(li).parent("ul").parent("li");
            plugin.closeSubmenu($li, "handleSubmenuRetractor", plugin);

            if (plugin.settings.debug) console.log("handleSubmenuRetractorEnd " + $li.find("> a").text());
        },

        handleTouchoffClose: function (e, _this, plugin) {
            //Don't fire during transtion
            if ($("body").is(".smpnav-transitioning")) return;

            if ($(e.target).parents().add($(e.target)).filter(".smpnav, .smpnav-toggle, .smpnav-ignore").length === 0) {
                if (plugin.settings.debug) console.log("touchoff close ", e);

                e.preventDefault();
                e.stopPropagation();

                plugin.closesmpnav();
                plugin.disableTouchoffClose();
            }
        },

        /* Controllers */
        scrollPanel: function (y) {
            if (smpnav_data.scroll_panel == "off") return 0;

            if (typeof y == "undefined") {
                return this.$smpnav.find(".smpnav-inner").scrollTop();
            } else {
                this.$smpnav.find(".smpnav-inner").scrollTop(y);
            }
        },

        openSubmenu: function ($li, tag, plugin) {
            if (!$li.hasClass("smpnav-active")) {
                //plugin.setMinimumHeight( 'open' , $li );

                if ($li.hasClass("smpnav-sub-menu")) {
                    $li.siblings(".smpnav-active").removeClass("smpnav-active");

                    //switch to position absolute, then delay activation below due to Firefox browser bug
                    $li.toggleClass("smpnav-caulk");

                    plugin.$smpnav.addClass("smpnav-sub-menu-active");
                } else {
                    if (plugin.settings.collapse_accordions) {
                        $li.siblings(".smpnav-active").removeClass("smpnav-active");
                    }
                }

               
                $li.parents("ul").removeClass("smpnav-sub-active-current");
                $li.find("> ul").addClass("smpnav-sub-active").addClass("smpnav-sub-active-current");

                
                setTimeout(function () {
                    $li.addClass("smpnav-active");
                    $li.trigger("smpnav-open-submenu"); //API
                    $li.removeClass("smpnav-caulk");

                    
                    setTimeout(function () {
                        
                        var y = plugin.scrollPanel();
                        $li.data("scroll-back", y);
                        var scrollPanelTo = $li.offset().top + y - $(window).scrollTop(); 
                        plugin.scrollPanel(scrollPanelTo);
                        
                    }, 100);
                }, 1);
            }
        },

        closeSubmenu: function ($li, tag, plugin) {
            //var plugin = this;

            if (this.settings.debug) console.log("closeSubmenu " + $li.find(">a").text() + " [" + tag + "]");

            
            if ($li.hasClass("menu-item-has-children") && $li.hasClass("smpnav-active")) {
                $li.addClass("smpnav-in-transition");

                $li.each(function () {
                    var _$li = $(this);
                    var _$ul = _$li.find("> ul");

                    
                    _$ul.on(plugin.transitionend + "_closesubmenu", function () {
                        if (plugin.settings.debug) console.log("finished submenu close transition");
                        _$li.removeClass("smpnav-in-transition");
                        _$ul.off(plugin.transitionend + "_closesubmenu");
                    });

                    //Close all children
                    plugin.closeSubmenu(_$li.find(".smpnav-active"), tag + "_recursive", plugin);
                });
            }

            //Actually remove the active class, which causes the submenu to close
            $li.removeClass("smpnav-active");

            //menu Sub Specific
            if ($li.hasClass("smpnav-sub-menu")) {
                if ($li.parents(".smpnav-sub-menu").length == 0) plugin.$smpnav.removeClass("smpnav-sub-menu-active");

                //return to original position
                var y = $li.data("scroll-back");
                if (y !== "undefined") plugin.scrollPanel(y);
                //console.log( 'y = ' + y );
            }

            //Active flags
            $li.find("> ul").removeClass("smpnav-sub-active").removeClass("smpnav-sub-active-current");
            $li.closest("ul").addClass("smpnav-sub-active-current");

            $li.trigger("smpnav-close-submenu"); //API
        },

        closeAllSubmenus: function () {
            $(this.element).find("li.menu-item-has-children").removeClass("smpnav-active");
        },

        toggleSearchBar: function (e, $drop, plugin) {
            e.stopPropagation();
            e.preventDefault();

            if ($drop.hasClass("smpnav-searchbar-drop-open")) {
                $drop.removeClass("smpnav-searchbar-drop-open");
                $("body").off("click.smpnav-searchbar-drop");
            }
            //Open
            else {
                $drop.addClass("smpnav-searchbar-drop-open");
                $drop.find(".smpnav-search-input").focus();

                
                if (plugin.settings.touchOffClose) {
                    setTimeout(function () {
                        $("body").on("click.smpnav-searchbar-drop", function (e) {
                            $(".smpnav-searchbar-drop").removeClass("smpnav-searchbar-drop-open");
                            $("body").off("click.smpnav-searchbar-drop");
                        });
                    }, 100);
                }
            }
        },
    };

    $.fn[pluginName] = function (options) {
        var args = arguments;

        if (options === undefined || typeof options === "object") {
            return this.each(function () {
                if (!$.data(this, "plugin_" + pluginName)) {
                    $.data(this, "plugin_" + pluginName, new Plugin(this, options));
                }
            });
        } else if (typeof options === "string" && options[0] !== "_" && options !== "init") {
            // Cache the method call to make it possible to return a value
            var returns;

            this.each(function () {
                var instance = $.data(this, "plugin_" + pluginName);
                if (instance instanceof Plugin && typeof instance[options] === "function") {
                    returns = instance[options].apply(instance, Array.prototype.slice.call(args, 1));
                }

                if (options === "destroy") {
                    $.data(this, "plugin_" + pluginName, null);
                }
            });

            return returns !== undefined ? returns : this;
        }
    };
})(jQuery, window, document);

(function ($) {
    var smpnav_is_initialized = false;

    jQuery(function ($) {
        initialize_smpnav("document.ready");
    });

    //Backup
    $(window).on("load", function () {
        initialize_smpnav("window.load");
    });

    function initialize_smpnav(init_point) {
        if (smpnav_is_initialized) return;

        smpnav_is_initialized = true;

        if (typeof console != "undefined" && init_point == "window.load") console.log("smpnav initialized via " + init_point);

        //Remove Loading Message
        $(".smpnav-loading").remove();

        //Run smpnav
        jQuery(".smpnav").smpnav({
            open_current: smpnav_data.open_current == "on" ? true : false,
            collapse_accordions: smpnav_data.collapse_accordions == "on" ? true : false,
            breakpoint: parseInt(smpnav_data.breakpoint),
            touchOffClose: smpnav_data.touch_off_close == "on" ? true : false,
            scroll_offset: smpnav_data.scroll_offset,
            disable_transforms: smpnav_data.disable_transforms == "on" ? true : false,
            close_on_target_click: smpnav_data.close_on_target_click == "on" ? true : false,
            process_uber_segments: smpnav_data.process_uber_segments == "on" ? true : false,
            //debug: true
            //mouseEvents: false
            //clicktest: true
        });

        //Scroll to non-ID "hashes"
        if (window.location.hash.substring(1, 2) == ".") {
            var $scrollTarget = $(window.location.hash.substring(1));
            if ($scrollTarget.length) {
                var top = $scrollTarget.offset().top - smpnav_data.scroll_offset;
                if ($scrollTarget.length) window.scrollTo(0, top);
            }
        }

        if (window.location.hash) {
            var hash = window.location.hash;
            if (hash.substring(1, 2) == ".") hash = hash.substring(1);

            
            hash = hash.replace(/[^#a-z0-9!$&'()*+,;=]/gi, ""); 
            var $li = $(".smpnav")
                .find('.smpnav-target[data-smpnav-scrolltarget="' + hash + '"]')
                .parent();
            if ($li.length) {
                //console.log( $li );
                $li.siblings().removeClass("current-menu-item").removeClass("current-menu-ancestor");
                $li.addClass("current-menu-item");
            }
        }

        if (smpnav_data.pro == "1") {
            updateScrollDirection();
            $(window).on("scroll", throttle(debounce(updateScrollDirection, 200), 200));
        }

        //Done
        $(".smpnav").trigger("smpnav-loaded");
    }

    var prevScrollTop = 0; //$(window).scrollTop();
    var prevScrollDir = "";
    function updateScrollDirection() {
        var plugin = this;
        var $body = $("body");

       
        var scrollTop = $(window).scrollTop();
        var scrollDir = "";
        var scrollTolerance = smpnav_data.scroll_tolerance;
        if (scrollTop <= smpnav_data.scroll_top_boundary) scrollDir = "top";
        else if (scrollTop - prevScrollTop > scrollTolerance) scrollDir = "down";
        else if (prevScrollTop - scrollTop > scrollTolerance) scrollDir = "up";
        else return; 
        if (scrollDir !== prevScrollDir) {
            $body.removeClass("smpnav--scroll-top smpnav--scroll-up smpnav--scroll-down");
            switch (scrollDir) {
                case "top":
                    $body.addClass("smpnav--scroll-top");
                    break;
                case "down":
                    $body.addClass("smpnav--scroll-down");
                    break;
                case "up":
                    $body.addClass("smpnav--scroll-up");
                    break;
                default:
            }
        }

        // Emit event
        $body.trigger("smpnav-window-scroll", { scrollTop: scrollTop, scrollDir: scrollDir, prevScrollDir: prevScrollDir });

        // Update
        prevScrollDir = scrollDir;
        prevScrollTop = scrollTop;
    }

    function debounce(func, wait, immediate) {
        var timeout;
        return function () {
            var context = this,
                args = arguments;
            var later = function () {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    }

    function throttle(func, limit) {
        var inThrottle;
        return function () {
            var args = arguments;
            var context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(function () {
                    inThrottle = false;
                }, limit);
            }
        };
    }

    function escapeHtml(str) {
        var div = document.createElement("div");
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }
})(jQuery);

jQuery(document).ready(function () {
    jQuery("#smpnav-toggle-main-button").click(function () {
        console.log("working");
        jQuery("i").toggleClass("fa fa-bars fa-minus-square");
    });
});
