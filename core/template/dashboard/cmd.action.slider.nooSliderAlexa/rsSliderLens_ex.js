!
function(m, f) {
    "use strict";
console.log ("Lancementtttttttttttttttttt");
    function i(b, $) {
        var g = {
                $wrapper: null,
                $svg: null,
                tabindexAttr: null,
                autofocusable: !1,
                initSvgOutsideHandle: function() {
  console.log ("initSvgOutsideHandle");
                  var e = {
                        left: (S.isFixedHandle ? S.isHoriz ? 100 * p.fixedHandleRelPos : 50 : 0) + "%",
                        top: (S.isFixedHandle ? S.isHoriz ? 50 : 100 * p.fixedHandleRelPos : 0) + "%"
                    };
                    S.isFixedHandle ? b.css(S.isHoriz ? "height" : "width", (S.isHoriz ? u.height : u.width) + "px") : e.height = e.width = "100%", this.$svg = v.createSvg(this.width, this.height).css(e), $.ruler.visible && v.renderSvg(this.$svg, this.width, this.height), b.triggerHandler("customRuler.rsSliderLens", [this.$svg, this.width, this.height, 1, !1, v.createSvgDom]), this.$svg.prependTo(this.$wrapper), b.css("visibility", "hidden")
                },
                initSize: function(e) {
  console.log ("initSize");
                    var n = e || b;
                    e === f && ("auto" !== $.width && "auto" !== $.height || "inline" !== n.css("display") || n.css("display", "inline-block"), this.width = ("auto" === $.width ? n.width() : $.width) || 150, this.height = ("auto" === $.height ? n.height() : $.height) || 50), e === f && S.isFixedHandle && !S.hasRuler || ((0 === n.width() || "auto" !== $.width || e !== f && S.isHoriz) && n.width(this.width), 0 !== n.height() && "auto" === $.height && (e === f || S.isHoriz) || n.height(this.height)), e === f ? S.isHoriz = "auto" === $.orientation ? this.width >= this.height : "vert" !== $.orientation : S.isFixedHandle && !S.hasRuler && (S.isHoriz ? n.height(this.height * $.handle.zoom) : n.width(this.width * $.handle.zoom))
                },
                init: function() {
  console.log ("initg");
                    this.tabindexAttr = b.attr("tabindex"), this.autofocusable = b.attr("autofocus"), this.style = b.attr("style"), S.isFixedHandle = !1 !== $.fixedHandle, S.isFixedHandle ? p.fixedHandleRelPos = !0 === $.fixedHandle ? .5 : $.flipped ? 1 - $.fixedHandle : $.fixedHandle : p.fixedHandleRelPos = 0, this.initSize(), S.hasRuler || (b.css(S.isHoriz ? "width" : "height", "auto"), this.initSize(), S.isFixedHandle && (S.isHoriz ? b.css("line-height", this.height * $.handle.zoom + "px") : b.css("width", this.width * $.handle.zoom + "px")));
                    var e = b.css("position"),
                        n = b.position(),
                        a = {
                            display: "inline-block",
                            position: "relative",
                            "white-space": "nowrap"
                        };
                    S.isHoriz || (a.left = 100 * $.contentOffset + "%"), this.$wrapper = b.css(a).wrap("<div>").parent().css({
                        overflow: S.isFixedHandle ? "hidden" : "visible",
                        display: "inline-block"
                    }).addClass($.style.classSlider).addClass(S.isFixedHandle ? $.style.classFixed : null).addClass(S.isHoriz ? $.style.classHoriz : $.style.classVert).addClass($.enabled ? null : $.style.classDisabled), S.isFixedHandle ? b.css(S.isHoriz ? "left" : "top", 100 * p.fixedHandleRelPos + "%") : S.isHoriz ? b.css("transform", "translateY(" + (100 * $.contentOffset - 50) + "%)") : b.css("transform", "translateX(-50%)"), this.initSize(this.$wrapper), S.hasRuler && S.isFixedHandle && (this[S.isHoriz ? "width" : "height"] *= $.ruler.size), "static" === e ? this.$wrapper.css("position", "relative") : this.$wrapper.css({
                        position: e,
                        left: n.left + "px",
                        top: n.top + "px"
                    })
                }
            },
            o = {
                $rangeWrapper: null,
                $range: null,
                getPropMin: function() {
                    return $.flipped ? S.isHoriz ? "right" : "bottom" : S.isHoriz ? "left" : "top"
                },
                getPropMax: function() {
                    return $.flipped ? S.isHoriz ? "left" : "top" : S.isHoriz ? "right" : "bottom"
                },
                init: function() {
  console.log ("inito");
                    var e, n = {
                            display: "inline-block",
                            position: "absolute"
                        },
                        a = {
                            overflow: "hidden"
                        },
                        t = {};
                    switch (S.isHoriz ? (a.top = 100 * $.range.pos + "%", a.height = S.isFixedHandle ? g.height * $.range.size + "px" : 100 * $.range.size + "%", a.transform = "translateY(-50%)", t.height = "100%") : (a.left = 100 * $.range.pos + "%", a.width = S.isFixedHandle ? g.width * $.range.size + "px" : 100 * $.range.size + "%", a.transform = "translateX(-50%)", t.width = "100%"), $.range.type) {
                        case "min":
                            t[this.getPropMin()] = "0%", e = (S.getCurrValue(S.currValue[0]) - $.min) / ($.max - $.min) * 100, t[this.getPropMax()] = ($.flipped ? e : 100 - e) + "%";
                            break;
                        case "max":
                            t[this.getPropMax()] = "0%", e = (S.getCurrValue(S.currValue[0]) - $.min) / ($.max - $.min) * 100, t[this.getPropMin()] = ($.flipped ? 100 - e : e) + "%";
                            break;
                        default:
                            S.isRangeFromToDefined && (t[this.getPropMin()] = ($.range.type[0] - $.min) / ($.max - $.min) * 100 + "%", t[this.getPropMax()] = ($.max - $.range.type[1]) / ($.max - $.min) * 100 + "%")
                    }
                    S.isFixedHandle ? (a[S.isHoriz ? "width" : "height"] = Math.round(g[S.isHoriz ? "width" : "height"] * (1 - $.paddingStart - $.paddingEnd)) + "px", a[S.isHoriz ? "left" : "top"] = 100 * p.fixedHandleRelPos + "%") : (a[this.getPropMin()] = 100 * $.paddingStart + "%", a[this.getPropMax()] = 100 * $.paddingEnd + "%"), this.$rangeWrapper = m("<div>").css(n).css(a).addClass($.style.classRange), S.hasRuler || this.$rangeWrapper.hide(), $.range.type && "hidden" !== $.range.type && (this.$range = m("<div>").css(n).css(t), this.$rangeWrapper.append(this.$range)), S.canDragRange && this.$rangeWrapper.addClass($.style.classRangeDraggable)
                },
                appendToDOM: function(e) {
  console.log ("appendToDOM");
                    e ? o.$rangeWrapper.insertBefore(p.$elem1st) : o.$rangeWrapper.appendTo(g.$wrapper), u.$elemRange1st && u.$elemRange1st.appendTo(p.$elem1st), u.$elemRange2nd && u.$elemRange2nd.appendTo(p.$elem2nd)
                },
                doUpdate: function(e, n, a, t, s) {
  console.log ("doUpdate");
                    if (a) switch ($.range.type) {
                        case "min":
                            t && a.css(o.getPropMax(), ($.flipped ? e : 100 - e) + "%");
                            break;
                        case "max":
                            s && a.css(o.getPropMin(), ($.flipped ? 100 - e : e) + "%");
                            break;
                        case !0:
                        case "between":
                            n ? a.css($.flipped ? o.getPropMax() : o.getPropMin(), e + "%") : a.css($.flipped ? o.getPropMin() : o.getPropMax(), 100 - e + "%")
                    }
                },
                update: function(e, n) {
  console.log ("update");
                    o.doUpdate(e, n, o.$range, !S.doubleHandles || !$.flipped && n || $.flipped && !n, !S.doubleHandles || $.flipped && n || !$.flipped && !n)
                }
            },
            u = {
                $elem1st: null,
                $elem2nd: null,
                $elemRange1st: null,
                $elemRange2nd: null,
                width: 0,
                height: 0,
                getRelativePosition: function() {
                    var e = S.hasRuler ? .5 : $.contentOffset,
                        n = S.isFixedHandle ? 1 : "zoom" === $.handle.otherSize ? $.handle.zoom : $.handle.otherSize,
                        n = 100 * ((e - (S.isFixedHandle ? .5 : $.handle.pos)) / n + .5) + "%";
                    return S.isHoriz ? {
                        left: S.doubleHandles ? "100%" : "50%",
                        top: n
                    } : {
                        left: n,
                        top: S.doubleHandles ? "100%" : "50%"
                    }
                },
                initClone: function() {
  console.log ("initCloneu");
                    this.$elem1st = b.clone().css("transform-origin", "0 0").css(this.getRelativePosition()).removeAttr("tabindex autofocus id"), S.isHoriz ? S.isFixedHandle && (this.$elem1st.css("top", ""), S.hasRuler || this.$elem1st.css("line-height", g.height + "px")) : S.isFixedHandle || S.hasRuler || this.$elem1st.css("width", g.width * $.handle.zoom + "px"), S.doubleHandles && (this.$elem2nd = this.$elem1st.clone().css(S.isHoriz ? "left" : "top", ""))
                },
                initSvgHandle: function() {
  console.log ("initSvgHandleu");
                    return g.initSvgOutsideHandle(), this.$elem1st = v.createSvg(this.width, this.height).css(this.getRelativePosition()), $.ruler.visible && v.renderSvg(this.$elem1st, this.width, this.height, !v.areTheSame($.handle.zoom, 1)), b.triggerHandler("customRuler.rsSliderLens", [this.$elem1st, this.width, this.height, $.handle.zoom, !0, v.createSvgDom]), S.doubleHandles && (this.$elem2nd = this.$elem1st.clone().css(S.isHoriz ? "left" : "top", "")), !0
                },
                init: function() {
  console.log ("initu");
                    this.width = g.width * $.handle.zoom, this.height = g.height * $.handle.zoom, S.hasRuler ? this.initSvgHandle() : this.initClone()
                },
                resizeUpdate: function() {
    console.log ("resizeUpdateu");
                  S.updateTicksStep();
                    var e = g.$wrapper.width(),
                        n = g.$wrapper.height();
                    S.isFixedHandle || u.$elem1st.add(u.$elem2nd).css({
                        width: e * $.handle.zoom,
                        height: n * $.handle.zoom
                    }), this.initRanges(e, n), S.isRangeFromToDefined && (S.doubleHandles ? (S.setValue(S.currValue[0], $.flipped ? p.$elem2nd : p.$elem1st, !0), S.setValue(S.currValue[1], $.flipped ? p.$elem1st : p.$elem2nd, !0)) : S.setValue(S.currValue[0], p.$elem1st, !0))
                },
                createMagnifRange: function(e, n, a) {
                    var t = {};
                    return t[S.isHoriz ? "width" : "height"] = Math.round((S.isHoriz ? n : a) * (1 - $.paddingStart - $.paddingEnd) * $.handle.zoom) + "px", t[S.isHoriz ? "left" : "top"] = S.doubleHandles ? e ? "100%" : "0%" : "50%", t[S.isHoriz ? "height" : "width"] = S.isFixedHandle ? $.range.size * u[S.isHoriz ? "height" : "width"] + "px" : 100 * $.range.size + "%", (e && u.$elemRange1st ? u.$elemRange1st : !e && u.$elemRange2nd ? u.$elemRange2nd : o.$rangeWrapper.clone()).css(t)
                },
                initRanges: function(e, n) {
    console.log ("initRangesu");
                    if (e === f && (e = g.width), n === f && (n = g.height), this.$elemRange1st = u.createMagnifRange(!0, e, n), S.doubleHandles) switch (this.$elemRange2nd = u.createMagnifRange(!1, e, n), $.range.type) {
                        case "min":
                            ($.flipped ? this.$elemRange1st : this.$elemRange2nd).empty();
                            break;
                        case "max":
                            ($.flipped ? this.$elemRange2nd : this.$elemRange1st).empty();
                            break;
                        case !0:
                        case "between":
                            this.$elemRange1st.add(this.$elemRange2nd).empty()
                    }
                },
                updateRanges: function(e, n) {
                    o.doUpdate(e, n, (n ? u.$elemRange1st : u.$elemRange2nd).children(), !0, !0)
                }
            },
            p = {
                $elem1st: null,
                $elem2nd: null,
                stopPosition: [0, 0],
                fixedHandleRelPos: 0,
                key: {
                    left: 37,
                    up: 38,
                    right: 39,
                    down: 40,
                    pgUp: 33,
                    pgDown: 34,
                    home: 36,
                    end: 35,
                    esc: 27
                },
                init: function() {
    console.log ("initp");
                    var e = {
                        display: "inline-block",
                        overflow: "hidden",
                        outline: "none",
                        position: "absolute"
                    };
                    S.isHoriz ? (e.width = 100 * $.handle.size + "%", S.isFixedHandle ? (e.left = 100 * this.fixedHandleRelPos + "%", e.top = 0, e.bottom = 0, e.transform = "translateX(-50%)") : (e.top = 100 * $.handle.pos + "%", e.height = 100 * ("zoom" === $.handle.otherSize ? $.handle.zoom : $.handle.otherSize) + "%", e.transform = "translate(-" + (S.doubleHandles ? 100 : 50) + "%, -50%)")) : (e.height = 100 * $.handle.size + "%", S.isFixedHandle ? (e.top = 100 * this.fixedHandleRelPos + "%", e.left = 0, e.right = 0, e.transform = "translateY(-50%)") : (e.left = 100 * $.handle.pos + "%", e.width = 100 * ("zoom" === $.handle.otherSize ? $.handle.zoom : $.handle.otherSize) + "%", e.transform = "translate(-50%, -" + (S.doubleHandles ? 100 : 50) + "%)")), this.$elem1st = u.$elem1st.wrap("<div>").parent().addClass(S.doubleHandles ? $.style.classHandle1 : $.style.classHandle).css(e), this.bindTabEvents(!0), S.doubleHandles && (this.$elem2nd = u.$elem2nd.wrap("<div>").parent().addClass($.style.classHandle2).css(e).css("transform", "translate" + (S.isHoriz ? "Y(-50%)" : "X(-50%)")), this.bindTabEvents(!1))
                },
                bindTabEvents: function(e) {
    console.log ("bindTabEventsp");
                    var n;
                    (g.tabindexAttr || S.isInputTypeRange) && $.enabled && (n = function() {
                        p.$elem2nd.attr("tabindex", g.tabindexAttr || 0).bind("focusin.rsSliderLens", h.gotFocus2nd).bind("focusout.rsSliderLens", h.loseFocus)
                    }, e || e === f ? (b.removeAttr("tabindex"), this.$elem1st.attr("tabindex", g.tabindexAttr || 0).bind("focusin.rsSliderLens", h.gotFocus1st).bind("focusout.rsSliderLens", h.loseFocus), g.autofocusable && (b.removeAttr("autofocus"), this.$elem1st.attr("autofocus", "autofocus")), e === f && this.$elem2nd && n()) : n())
                },
                unbindTabEvents: function() {
    console.log ("unbindTabEventsp");
                    !g.tabindexAttr && !S.isInputTypeRange || $.enabled || (this.$elem1st.add(this.$elem2nd).removeAttr("tabindex autofocus").unbind("focusout.rsSliderLens", h.loseFocus), this.$elem1st.unbind("focusin.rsSliderLens", h.gotFocus1st), this.$elem2nd && this.$elem2nd.unbind("focusin.rsSliderLens", h.gotFocus2nd))
                },
                navigate: function(e, n, a, t, s, r) {
                    var i;
                    h.$animObj || (i = S.currValue[S.doubleHandles && h.$handle !== p.$elem1st ? 1 : 0], e = S.isStepDefined ? Math.round((i + n - $.min) / $.step) * $.step + $.min : i + e / S.ticksStep, s !== f && (e < s[0] && (e = s[0]), e > s[1] && (e = s[1])), e < $.min && (e = $.min), e > $.max && (e = $.max), h.gotoAnim(i, e, a, t, r))
                },
                keydown: function(e) {
    console.log ("keydownp");
                    var n = [S.isRangeFromToDefined ? S.getCurrValue($.range.type[$.flipped ? 1 : 0]) : $.min, S.isRangeFromToDefined ? S.getCurrValue($.range.type[$.flipped ? 0 : 1]) : $.max];
                    if (n[0] = (!S.doubleHandles || h.$handle === p.$elem1st ? n : S.currValue)[0], n[1] = (S.doubleHandles && h.$handle === p.$elem1st ? S.currValue : n)[1], function() {
                            switch (e.which) {
                                case p.key.left:
                                    return -1 < m.inArray("left", $.keyboard.allowed);
                                case p.key.down:
                                    return -1 < m.inArray("down", $.keyboard.allowed);
                                case p.key.right:
                                    return -1 < m.inArray("right", $.keyboard.allowed);
                                case p.key.up:
                                    return -1 < m.inArray("up", $.keyboard.allowed);
                                case p.key.pgUp:
                                    return -1 < m.inArray("pgup", $.keyboard.allowed);
                                case p.key.pgDown:
                                    return -1 < m.inArray("pgdown", $.keyboard.allowed);
                                case p.key.home:
                                    return -1 < m.inArray("home", $.keyboard.allowed);
                                case p.key.end:
                                    return -1 < m.inArray("end", $.keyboard.allowed);
                                case p.key.esc:
                                    return -1 < m.inArray("esc", $.keyboard.allowed)
                            }
                            return !1
                        }()) {
                        e.preventDefault();
                        var a = S.currValue[S.doubleHandles && h.$handle !== p.$elem1st ? 1 : 0];
                        switch (e.which) {
                            case p.key.left:
                            case p.key.down:
                                h.beingDraggedByKeyboard = !0, p.navigate(S.isHoriz ? -1 : 1, S.isHoriz ? -$.step : $.step, S.isStepDefined ? $.handle.animation * $.step / ($.max - $.min) : 0, $.keyboard.easing, n);
                                break;
                            case p.key.right:
                            case p.key.up:
                                h.beingDraggedByKeyboard = !0, p.navigate(S.isHoriz ? 1 : -1, S.isHoriz ? $.step : -$.step, S.isStepDefined ? $.handle.animation * $.step / ($.max - $.min) : 0, $.keyboard.easing, n);
                                break;
                            case p.key.pgUp:
                            case p.key.pgDown:
                                e.which === p.key.pgUp ? p.navigate((S.fromPixel - S.toPixel) / $.keyboard.numPages, ($.min - $.max) / $.keyboard.numPages, $.handle.animation / $.keyboard.numPages, $.keyboard.easing, n) : p.navigate((S.toPixel - S.fromPixel) / $.keyboard.numPages, ($.max - $.min) / $.keyboard.numPages, $.handle.animation / $.keyboard.numPages, $.keyboard.easing, n);
                                break;
                            case p.key.home:
                                h.gotoAnim(a, n[0], $.handle.animation, $.keyboard.easing);
                                break;
                            case p.key.end:
                                h.gotoAnim(a, n[1], $.handle.animation, $.keyboard.easing);
                                break;
                            case p.key.esc:
                                S.doubleHandles ? (h.gotoAnim(S.currValue[0], S.uncommitedValue[0], $.handle.animation, $.keyboard.easing, p.$elem1st), h.gotoAnim(S.currValue[1], S.uncommitedValue[1], $.handle.animation, $.keyboard.easing, p.$elem2nd)) : h.gotoAnim(S.currValue[0], S.uncommitedValue[0], $.handle.animation, $.keyboard.easing), S.currValue[0] = S.uncommitedValue[0], S.currValue[1] = S.uncommitedValue[1]
                        }
                    }
                },
                keyup: function(e) {
                    switch (e.which) {
                        case p.key.left:
                        case p.key.down:
                        case p.key.right:
                        case p.key.up:
                            h.beingDraggedByKeyboard || c.processFinalChange(h.$handle), h.beingDraggedByKeyboard = !1
                    }
                },
                onMouseWheel: function(e) {
					    console.log ("onMouseWheelp");

                    var n, a, t;
                    $.enabled && !v.isAlmostZero($.step) && (n = {
                        x: 0,
                        y: 0
                    }, e.wheelDelta !== f || e.originalEvent === f || e.originalEvent.wheelDelta === f && e.originalEvent.detail === f || (e = e.originalEvent), e.wheelDelta && (n.y = e.wheelDelta / 120), e.detail && (n.y = -e.detail / 3), (a = e || window.event).axis !== f && a.axis === a.HORIZONTAL_AXIS && (n.x = -n.y, n.y = 0), a.wheelDeltaY !== f && (n.y = a.wheelDeltaY / 120), a.wheelDeltaX !== f && (n.x = -a.wheelDeltaX / 120), e.preventDefault(), n.y *= $.handle.mousewheel, t = $.step * $.handle.mousewheel, e = function() {
                        p.navigate(-n.y, n.y < 0 ? t : -t, $.handle.animation, $.handle.easing, f, h.$handle)
                    }, .5 < Math.abs(n.y) && (h.$handle = p.$elem1st, e(), S.doubleHandles && (h.$handle = p.$elem2nd, e())))
                }
            },
            c = {
                onGetter: function(e, n) {
                    switch (n) {
                        case "value":
                            return S.doubleHandles ? [S.getCurrValue(S.currValue[0]), S.getCurrValue(S.currValue[1])] : S.getCurrValue(S.currValue[0]);
                        case "range":
                            return $.range.type;
                        case "enabled":
                            return $.enabled
                    }
                    return null
                },
                onSetter: function(e, n, a) {
					    console.log ("onSetter");
                    var t, s, r;
                    switch (n) {
                        case "enabled":
                            !1 === a ? $.enabled && ($.enabled = !1, g.$wrapper.addClass($.style.classDisabled), p.unbindTabEvents()) : !0 === a && ($.enabled || ($.enabled = !0, g.$wrapper.removeClass($.style.classDisabled), p.bindTabEvents()));
                            break;
                        case "value":
                            var i = a && "object" == typeof a && 2 === a.length;
                            S.doubleHandles ? i && (t = a, r = [S.isRangeFromToDefined ? S.getCurrValue($.range.type[$.flipped ? 1 : 0]) : $.min, S.isRangeFromToDefined ? S.getCurrValue($.range.type[$.flipped ? 0 : 1]) : $.max], null !== t[1] && (t[1] = S.getCurrValue(t[1]), t[1] < r[0] && (t[1] = r[0]), t[1] > r[1] && (t[1] = r[1])), null !== t[0] ? (t[0] = S.getCurrValue(t[0]), t[0] < r[0] && (t[0] = r[0]), t[0] > r[1] && (t[0] = r[1]), null !== t[1] ? t[0] > t[1] && (r = (s = t)[0], s[0] = s[1], s[1] = r) : t[0] > S.currValue[1] && (t[1] = t[0])) : null !== t[1] && t[1] < S.currValue[0] && (t[0] = t[1]), null !== a[0] && h.gotoAnim(S.currValue[0], a[0], $.handle.animation, $.keyboard.easing, p.$elem1st), null !== a[1] && h.gotoAnim(S.currValue[1], a[1], $.handle.animation, $.keyboard.easing, p.$elem2nd)) : i || h.gotoAnim(S.currValue[0], S.getCurrValue(a), $.handle.animation, $.keyboard.easing);
                            break;
                        case "range":
                            a && (S.doubleHandles || !0 !== a.type && "between" !== a.type) && (u.$elemRange1st && (u.$elemRange1st.remove(), u.$elemRange1st = null), u.$elemRange2nd && (u.$elemRange2nd.remove(), u.$elemRange2nd = null), o.$rangeWrapper.remove(), $.range = m.extend({}, $.range, a), S.initRangeVars(), o.init(), u.initRanges(), o.appendToDOM(!0), .5 < Math.abs($.handle.mousewheel) && o.$rangeWrapper.bind("DOMMouseScroll.rsSliderLens mousewheel.rsSliderLens", p.onMouseWheel), S.canDragRange && o.$range.bind("mousedown.rsSliderLens touchstart.rsSliderLens", l.startDrag), d(o.$rangeWrapper), d(u.$elemRange1st), S.doubleHandles && d(u.$elemRange2nd), S.updateHandles(S.currValue))
                    }
                    return c.onGetter(e, n)
                },
                onResizeUpdate: function() {
					    console.log ("onResizeUpdate");
                    u.resizeUpdate()
                },
                onChange: function(e, n, a) {
					    console.log ("onChangec");
                    $.onChange && $.onChange(e, n, a)
                },
                onCreate: function(e) {
					    console.log ("onCreatec");
                    $.onCreate && $.onCreate(e)
                },
                onDestroy: function() {
					    console.log ("onDestroyc");
                    b.add(g.$wrapper).add(g.$canvas).add(o.$rangeWrapper).add(p.$elem1st).add(p.$elem2nd).unbind("DOMMouseScroll.rsSliderLens mousewheel.rsSliderLens", p.onMouseWheel), b.unbind("getter.rsSliderLens", c.onGetter).unbind("setter.rsSliderLens", c.onSetter).unbind("resizeUpdate.rsSliderLens", c.onResizeUpdate).unbind("change.rsSliderLens", c.onChange).unbind("finalchange.rsSliderLens", c.onFinalChange).unbind("create.rsSliderLens", c.onCreate).unbind("destroy.rsSliderLens", c.onDestroy).unbind("customLabel.rsSliderLens", c.onCustomLabel).unbind("customLabelAttrs.rsSliderLens", c.onCustomLabelAttrs).unbind("customRuler.rsSliderLens", c.onCustomRuler), g.$wrapper.unbind("mousedown.rsSliderLens touchstart.rsSliderLens", h.startDrag).unbind("mouseup.rsSliderLens touchend.rsSliderLens", h.stopDrag), o.$rangeWrapper.unbind("mousedown.rsSliderLens touchstart.rsSliderLens", h.startDrag), o.$range && o.$range.unbind("mousedown.rsSliderLens touchstart.rsSliderLens", l.startDrag), m(document).unbind("keydown.rsSliderLens", p.keydown).unbind("keyup.rsSliderLens", p.keyup).unbind("mousemove.rsSliderLens touchmove.rsSliderLens", S.isHoriz ? h.dragHoriz : h.dragVert).unbind("mouseup.rsSliderLens touchend.rsSliderLens", h.stopDragFromDoc).unbind("mousemove.rsSliderLens touchmove.rsSliderLens", l.drag), p.$elem1st.unbind("focusin.rsSliderLens", h.gotFocus1st).unbind("focusout.rsSliderLens", h.loseFocus).unbind("mousedown.rsSliderLens touchstart.rsSliderLens", h.startDrag).unbind("mousedown.rsSliderLens touchstart.rsSliderLens", h.startDragFromHandle1st), p.$elem2nd && p.$elem2nd.unbind("focusin.rsSliderLens", h.gotFocus2nd).unbind("focusout.rsSliderLens", h.loseFocus).unbind("mousedown.rsSliderLens touchstart.rsSliderLens", h.startDragFromHandle2nd), g.$canvas && g.$canvas.remove(), o.$rangeWrapper.remove(), p.$elem1st.remove(), p.$elem2nd && p.$elem2nd.remove(), g.$svg && g.$svg.remove(), g.style ? b.attr("style", g.style) : b.removeAttr("style"), g.tabindexAttr && b.attr("tabindex", g.tabindexAttr), b.unwrap()
                },
                onCustomLabel: function(e, n) {
                    return $.ruler.labels.onCustomLabel ? $.ruler.labels.onCustomLabel(e, n) : n
                },
                onCustomLabelAttrs: function(e, n, a, t) {
                    if ($.ruler.labels.onCustomAttrs) return $.ruler.labels.onCustomAttrs(e, n, a, t)
                },
                onCustomRuler: function(e, n, a, t, s, r, i) {
                    if ($.ruler.onCustom) return $.ruler.onCustom(e, n, a, t, s, r, i)
                },
                finalChangeValueFirst: null,
                finalChangeValueSecond: null,
                processFinalChange: function(e) {
					    console.log ("processFinalChangec");
                    var n = e !== f ? e : S.isFixedHandle || h.$handle === p.$elem1st,
                        e = S.getCurrValue(S.currValue[n ? 0 : 1]);
                    n ? e !== c.finalChangeValueFirst && (b.triggerHandler("finalchange.rsSliderLens", [e, !0]), c.finalChangeValueFirst = e) : e !== c.finalChangeValueSecond && (b.triggerHandler("finalchange.rsSliderLens", [e, !1]), c.finalChangeValueSecond = e)
                },
                onFinalChange: function(e, n, a) {
					    console.log ("onFinalChangec");
					//console.log ("Dans JS1");
                    $.onFinalChange && $.onFinalChange(e, n, a)
					//console.log ("Dans JS2");
                }
            },
            S = {
                ns: "http://www.w3.org/2000/svg",
                currValue: [0, 0],
                ticksStep: 0,
                startPixel: 0,
                isFixedHandle: !1,
                isInputTypeRange: !1,
                isHoriz: !0,
                hasRuler: !1,
                fromPixel: 0,
                toPixel: 0,
                doubleHandles: !1,
                isRangeFromToDefined: !1,
                isStepDefined: !1,
                isAutoFocusable: !1,
                canDragRange: !1,
                isDocumentEventsBound: !1,
                uncommitedValue: [0, 0],
                getCurrValue: function(e) {
                    return $.flipped ? $.max - e + $.min : e
                },
                checkBounds: function() {
                    function e(e, n) {
                        $.value < e ? $.value = e : $.value > n && ($.value = n)
                    }

                    function n(e, n, a) {
                        e[0] < n && (e[0] = n), e[1] > a && (e[1] = a)
                    }
                    var a;
                    $.min > $.max && (a = $.min, $.min = $.max, $.max = a), S.doubleHandles ? $.value[0] > $.value[1] && (a = $.value[0], $.value[0] = $.value[1], $.value[1] = a) : !0 !== $.range.type && "between" !== $.range.type || ($.range.type = !1), S.isRangeFromToDefined ? ($.range.type[0] > $.range.type[1] && (a = $.range.type[0], $.range.type[0] = $.range.type[1], $.range.type[1] = a), n($.range.type, $.min, $.max), S.doubleHandles ? n($.value, $.range.type[0], $.range.type[1]) : e($.range.type[0], $.range.type[1])) : S.doubleHandles ? n($.value, $.min, $.max) : e($.min, $.max), S.doubleHandles ? (p.stopPosition[0] = S.currValue[0] = $.value[0], p.stopPosition[1] = S.currValue[1] = $.value[1]) : S.currValue[0] = $.value
                },
                initRangeVars: function() {
                    this.isRangeFromToDefined = "object" == typeof $.range.type && 2 === $.range.type.length, this.canDragRange = $.range.draggable && !1 === $.fixedHandle && (this.doubleHandles && (!0 === $.range.type || "between" === $.range.type) || this.isRangeFromToDefined)
                },
                initVars: function() {
                    this.initRangeVars(), !1 !== $.fixedHandle && $.value && "object" == typeof $.value && 2 === $.value.length && ($.value = $.value[0]), this.doubleHandles = !!$.value && "object" == typeof $.value && 2 === $.value.length;
                    var e = $.max - $.min;
                    $.step = $.step < 0 ? 0 : $.step > e ? e : $.step, this.isStepDefined = 5e-5 < $.step, this.isInputTypeRange = b.is("input[type=range]"), this.isAutoFocusable = (this.isInputTypeRange || b.attr("tabindex") !== f) && b.attr("autofocus") !== f, this.hasRuler = $.ruler.visible || !!$.ruler.onCustom, v.isAlmostZero($.handle.zoom) && ($.handle.zoom = 1), v.isAlmostZero($.handle.otherSize) && ($.handle.otherSize = 1), $.handle.animation = v.getSpeedMs($.handle.animation), $.keyboard.numPages < 1 && ($.keyboard.numPages = 5), S.doubleHandles && ($.handle.size /= 2)
                },
                updateTicksStep: function() {
                    var e = S.isFixedHandle ? S.hasRuler ? g.$svg : b : g.$wrapper,
                        e = S.isHoriz ? e.width() : e.height();
                    this.ticksStep = e * (1 - $.paddingStart - $.paddingEnd) / ($.max - $.min), this.startPixel = e * ($.flipped ? $.paddingEnd : $.paddingStart), S.isRangeFromToDefined && ($.flipped ? (this.fromPixel = Math.round(($.max - $.range.type[1]) * this.ticksStep), this.toPixel = Math.round(($.max - $.range.type[0]) * this.ticksStep)) : (this.fromPixel = Math.round(($.range.type[0] - $.min) * this.ticksStep), this.toPixel = Math.round(($.range.type[1] - $.min) * this.ticksStep)))
                },
                init: function() {
                    this.checkBounds(), this.updateTicksStep(), S.isRangeFromToDefined || (this.fromPixel = 0, this.toPixel = Math.round(($.max - $.min) * this.ticksStep))
                },
                doSetHandles: function(e) {
                    S.doubleHandles ? (S.setValue(e[0], $.flipped ? p.$elem2nd : p.$elem1st, S.isStepDefined, f, !0), S.setValue(e[1], $.flipped ? p.$elem1st : p.$elem2nd, S.isStepDefined, f, !0), c.processFinalChange(!0), c.processFinalChange(!1)) : (S.setValue(e[0], p.$elem1st, S.isStepDefined, f, !0), c.processFinalChange(!0))
                },
                initHandles: function() {
                    S.doubleHandles ? this.doSetHandles([S.getCurrValue($.value[0]), S.getCurrValue($.value[1])]) : this.doSetHandles([S.getCurrValue($.value)])
                },
                updateHandles: function(e) {
                    this.doSetHandles(e)
                },
                checkLimits: function(e) {
                    var n = $.min;
                    return S.isRangeFromToDefined && (n = S.getCurrValue($.range.type[$.flipped ? 1 : 0]), S.isStepDefined && (n = Math.ceil((n - $.min) / $.step) * $.step + $.min)), e < n ? n : (n = $.max, S.isRangeFromToDefined && (n = S.getCurrValue($.range.type[$.flipped ? 0 : 1]), S.isStepDefined && (n = Math.trunc((n - $.min) / $.step) * $.step + $.min)), n < e ? n : e)
                },
                setValue: function(e, n, a, t, s) {
                    S.doubleHandles && (n === p.$elem1st ? e > p.stopPosition[1] && (e = p.stopPosition[1]) : e < p.stopPosition[0] && (e = p.stopPosition[0]));
                    var r = e - $.min,
                        i = r;
                    S.isStepDefined && (r = Math.round(r / $.step) * $.step), S.isRangeFromToDefined && (d = S.getCurrValue($.range.type[$.flipped ? 1 : 0]) - $.min, r < (d = Math.ceil(d / $.step) * $.step) ? r = t ? d : r + $.step : (d = S.getCurrValue($.range.type[$.flipped ? 0 : 1]) - $.min, (d = Math.trunc(d / $.step) * $.step) < r && (r = t ? d : r - $.step))), r < 0 && (r += $.step), r > $.max - $.min && (r -= $.step), S.isStepDefined && !1 !== a && (i = r), r = S.checkLimits(r + $.min) - $.min;
                    var l = (i = S.checkLimits(i + $.min) - $.min) / ($.min - $.max) * 100,
                        e = n === p.$elem1st,
                        t = $.flipped ? $.paddingEnd : $.paddingStart,
                        d = l * (1 - t - ($.flipped ? $.paddingStart : $.paddingEnd)) - 100 * t,
                        a = "translate(" + (S.isHoriz ? d + "%, -50%)" : "-50%, " + d + "%)"),
                        i = "translate(" + (S.isHoriz ? l + "%, -50%)" : "-50%, " + l + "%)"),
                        t = S.currValue[e ? 0 : 1];
                    S.currValue[e ? 0 : 1] = r + $.min, S.isFixedHandle ? (S.hasRuler ? (u.$elem1st.css("transform", a), g.$svg.css("transform", a)) : S.isHoriz ? (u.$elem1st.css("transform", "scale(" + $.handle.zoom + ") " + a.replace(/-50%\)$/, "0)")), b.css("transform", "translate(" + d + "%, " + (100 * $.contentOffset - 50) + "%)")) : (u.$elem1st.css("transform", "scale(" + $.handle.zoom + ") " + a), b.css("transform", "translate(-50%, " + d + "%)")), o.$rangeWrapper.css("transform", i), u.$elemRange1st.css("transform", i)) : (n.css(S.isHoriz ? "left" : "top", -d + "%"), (e ? u.$elem1st : u.$elem2nd).css("transform", S.hasRuler ? a : "scale(" + $.handle.zoom + ") " + a), p.stopPosition[e ? 0 : 1] = r + $.min, (e ? u.$elemRange1st : u.$elemRange2nd).css("transform", i)), o.update(-l, e), u.updateRanges(-l, e), S.isInputTypeRange && e && b.attr("value", S.getCurrValue(S.currValue[0]));
                    l = S.getCurrValue(S.currValue[e ? 0 : 1]);
                    !s && v.areTheSame(t, l) || b.triggerHandler("change.rsSliderLens", [l, e])
                }
            },
            v = {
                getEventPageX: function(e) {
                    return (e.originalEvent && e.originalEvent.touches && 0 < e.originalEvent.touches.length ? e.originalEvent.touches[0] : e).pageX
                },
                getEventPageY: function(e) {
                    return (e.originalEvent && e.originalEvent.touches && 0 < e.originalEvent.touches.length ? e.originalEvent.touches[0] : e).pageY
                },
                pixel2Value: function(e) {
                    return (e - S.startPixel) / S.ticksStep + $.min
                },
                value2Pixel: function(e) {
                    return (e - $.min) * S.ticksStep + S.startPixel
                },
                isDefined: function(e) {
                    return e !== f && null !== e
                },
                toInt: function(e) {
                    e = e && "auto" !== e && "" !== e ? parseInt(e, 10) : 0;
                    return isNaN(e) ? 0 : e
                },
                toFloat: function(e) {
                    e = e && "auto" !== e && "" !== e ? parseFloat(e) : 0;
                    return isNaN(e) ? 0 : e
                },
                roundToDecimalPlaces: function(e, n) {
                    n = Math.pow(10, n);
                    return Math.round(e * n) / n
                },
                roundNtoMultipleOfM: function(e, n) {
                    return Math.round(e / n) * n
                },
                isAlmostZero: function(e, n) {
                    return this.areTheSame(e, 0, n)
                },
                areTheSame: function(e, n, a) {
                    return Math.abs(e - n) < (a === f ? 5e-5 : a)
                },
                createSvgDom: function(e, n) {
                    var a, t = document.createElementNS(S.ns, e);
                    for (a in n) t.setAttribute(a, n[a]);
                    return m(t)
                },
                createSvg: function(e, n) {
                    return v.createSvgDom("svg", {
                        width: e,
                        height: n,
                        viewBox: "0 0 " + e + " " + n,
                        preserveAspectRatio: "none",
                        "shape-rendering": "geometricPrecision",
                        xmlns: S.ns,
                        version: "1.1"
                    }).css({
                        position: "absolute",
                        "pointer-events": "none"
                    })
                },
                renderSvg: function(o, e, n, u) {
                    var a, g = S.isHoriz ? e : n,
                        p = S.isHoriz ? n : e,
                        c = $.ruler.tickMarks,
                        n = $.paddingStart * g,
                        e = $.paddingEnd * g,
                        h = g - n - e,
                        m = $.flipped ? e : n,
                        f = $.flipped ? n : e;
                    if (! function() {
                            function e(e, n, a) {
                                S.isHoriz ? r += "M" + Math.round(100 * e) / 100 + " " + Math.round(100 * n) / 100 + " v" + Math.round(100 * a) / 100 + " " : r += "M" + Math.round(100 * n) / 100 + " " + Math.round(100 * e) / 100 + " h" + Math.round(100 * a) / 100 + " "
                            }
                            var n = (a = function(e) {
                                    var n = c[e].step;
                                    return c[e].visible ? {
                                        step: n,
                                        tickStep: (0 < n && !v.isAlmostZero($.max - $.min) ? n : 1) / ($.max - $.min) * h,
                                        pos: c[e].pos * (1 - c[e].size) * p,
                                        size: c[e].size * p
                                    } : null
                                })("short"),
                                a = a("long"),
                                t = null,
                                s = null,
                                r = "";
                            if (n && a ? (t = n.tickStep > a.tickStep ? a : n, s = n.tickStep > a.tickStep ? n : a) : t = n || a, t) {
                                for (var i = m, l = m; i <= g - f + 5e-5; i += t.tickStep) {
                                    var d = !1;
                                    s && ((d = v.areTheSame(i, l, 5e-5)) || 5e-5 < i + t.tickStep - l) && (e(l, s.pos, s.size), l += s.tickStep), d || e(i, t.pos, t.size)
                                }
                                o.append(v.createSvgDom("path", {
                                    d: r,
                                    "stroke-width": u ? $.handle.zoom : 1
                                }))
                            }
                        }(), $.ruler.labels.visible && (("step" === $.ruler.labels.values || !0 === $.ruler.labels.values) && 0 < $.step || $.ruler.labels.values instanceof Array)) {
                        var s, t, e = {
                                "dominant-baseline": "central",
                                "text-anchor": "middle"
                            },
                            r = $.max - $.min,
                            i = function(e) {
                                var n = $.flipped ? $.max - e : e - $.min,
                                    a = $.ruler.labels.pos * p / (u ? $.handle.zoom : 1),
                                    n = n / r * h / (u ? $.handle.zoom : 1) + (u ? m / $.handle.zoom : m),
                                    t = Math.round(100 * (S.isHoriz ? n : a)) / 100,
                                    a = Math.round(100 * (S.isHoriz ? a : n)) / 100,
                                    n = b.triggerHandler("customLabelAttrs.rsSliderLens", [e, t, a]);
                                "[object Object]" !== Object.prototype.toString.call(n) && (n = {}), n.x = t, n.y = a, e = b.triggerHandler("customLabel.rsSliderLens", [e]), s.append(v.createSvgDom("text", n).append(e))
                            };
                        if (u && (e.transform = "scale(" + $.handle.zoom + ")"), s = v.createSvgDom("g", e), $.ruler.labels.values instanceof Array)
                            for (t in $.ruler.labels.values.sort(function(e, n) {
                                    return e - n
                                }), $.ruler.labels.values) $.ruler.labels.values && (a = +(a = $.ruler.labels.values[t])) >= $.min && a <= $.max && i($.ruler.labels.values[t]);
                        else
                            for (t = $.min; t <= $.max; t += $.step) i(t);
                        s.appendTo(o)
                    }
                },
                getSpeedMs: function(e) {
                    var n = e;
                    return "string" == typeof e && (n = m.fx.speeds[e]) === f && (n = m.fx.speeds._default), n === f && (n = 150), n
                }
            },
            h = {
                doDrag: !0,
                firstClickWasOutsideHandle: !1,
                mouseBtnStillDown: !1,
                beingDraggedByKeyboard: !1,
                dragDelta: 0,
                $handle: null,
                $animObj: null,
                dragging: !1,
                fixedHandleStartDragPos: 0,
                textSelection: function(e) {
                    e = e ? "" : "none";
                    m("body").css({
                        "-webkit-touch-callout": e,
                        "-webkit-user-select": e,
                        "-khtml-user-select": e,
                        "-moz-user-select": e,
                        "-ms-user-select": e,
                        "-o-user-select": e,
                        "user-select": e
                    })
                },
                disableTextSelection: function() {
                    h.textSelection(!1)
                },
                enableTextSelection: function() {
                    h.textSelection(!0)
                },
                animDone: function(e, n) {
                    S.setValue(v.pixel2Value(e + h.dragDelta), n || h.$handle || p.$elem1st, f, !!n), h.doDrag && m(document).bind("mousemove.rsSliderLens touchmove.rsSliderLens", S.isHoriz ? h.dragHoriz : h.dragVert).bind("mouseup.rsSliderLens touchend.rsSliderLens", h.stopDragFromDoc), h.$animObj = null
                },
                anim: function(e, n, a, t, s, r, i, l) {
                    function d() {
                        h.animDone(v.value2Pixel(a), o), !l || "key" === l && !h.beingDraggedByKeyboard ? c.processFinalChange(r === p.$elem1st) : "key" === l && (h.beingDraggedByKeyboard = !1), i && i()
                    }
                    var o = r,
                        u = S.isHoriz ? g.$wrapper.offset().left : g.$wrapper.offset().top;
                    h.$animObj && !r && (h.$animObj.stop(), n = v.value2Pixel(h.$animObj[0].n)), a === f && (a = (S.isHoriz ? v.getEventPageX(e) : v.getEventPageY(e)) - u), n === f && (n = !S.doubleHandles || a <= v.value2Pixel((S.currValue[0] + S.currValue[1]) / 2) ? (h.$handle = p.$elem1st, v.value2Pixel(S.currValue[0])) : (h.$handle = p.$elem2nd, v.value2Pixel(S.currValue[1]))), t === f && (t = $.handle.animation), r = r || h.$handle || p.$elem1st, (n = v.pixel2Value(n)) !== (a = v.pixel2Value(a)) && 0 < t ? (h.$animObj = m({
                        n: n
                    }), h.$animObj.animate({
                        n: a
                    }, {
                        duration: t,
                        easing: s === f ? $.handle.easing : s,
                        step: function(e) {
                            S.setValue(e, r, $.snapOnDrag)
                        },
                        complete: d
                    })) : d()
                },
                gotoAnim: function(e, n, a, t, s) {
                    a = v.getSpeedMs(a), e = v.value2Pixel(e), n = v.value2Pixel(n);
                    h.dragDelta = 0, h.doDrag = !1, h.beingDraggedByKeyboard ? h.anim(null, e, n, a, t, s, f, "key") : h.anim(null, e, n, a, t, s)
                },
                startDrag: function(e) {
                    var n;
                    S.canDragRange && m(e.target).is(o.$range) ? e.preventDefault() : $.enabled && !h.$animObj && (S.updateTicksStep(), h.disableTextSelection(), l.dragged = !1, h.doDrag = !0, S.isFixedHandle ? (h.$handle = p.$elem1st, h.fixedHandleStartDragPos = S.isHoriz ? v.getEventPageX(e) : v.getEventPageY(e), h.fixedHandleStartDragPos += v.value2Pixel(S.currValue[0]), u.$elem1st.parent().add(g.$wrapper).addClass($.style.classDragging), m(document).bind("mousemove.rsSliderLens touchmove.rsSliderLens", S.isHoriz ? h.dragHoriz : h.dragVert).bind("mouseup.rsSliderLens touchend.rsSliderLens", h.stopDragFromDoc), setTimeout(function() {
                        h.$handle.focus()
                    })) : (h.mouseBtnStillDown = h.firstClickWasOutsideHandle = !0, n = [S.currValue[0], S.currValue[1]], h.anim(e, f, f, f, f, f, function() {
                        h.$handle.focus(), S.uncommitedValue[0] = n[0], S.uncommitedValue[1] = n[1], h.mouseBtnStillDown || h.stopDrag(!0)
                    }, !0)))
                },
                startDragFromHandle: function(e, n) {
                    var a;
                    $.enabled && (e.stopPropagation(), S.updateTicksStep(), h.disableTextSelection(), l.dragged = !1, (h.$handle = n).add(g.$wrapper).addClass($.style.classDragging), a = S.isHoriz ? g.$wrapper.offset().left : g.$wrapper.offset().top, n = v.value2Pixel(S.currValue[n === p.$elem1st ? 0 : 1]), e = (S.isHoriz ? v.getEventPageX(e) : v.getEventPageY(e)) - a, h.doDrag = !0, h.dragging = !0, h.dragDelta = n - e, h.animDone(e), h.dragDelta = a - h.dragDelta)
                },
                startDragFromHandle1st: function(e) {
                    $.enabled && !h.$animObj && h.startDragFromHandle(e, p.$elem1st)
                },
                startDragFromHandle2nd: function(e) {
                    $.enabled && !h.$animObj && h.startDragFromHandle(e, p.$elem2nd)
                },
                handleStartsToMoveWhen1stClickWasOutsideHandle: function() {
                    h.firstClickWasOutsideHandle && (h.$handle.add(g.$wrapper).addClass($.style.classDragging), h.firstClickWasOutsideHandle = !1, h.dragDelta = S.isHoriz ? g.$wrapper.offset().left : g.$wrapper.offset().top)
                },
                dragHorizVert: function(e) {
                    h.dragging = !0, S.isFixedHandle ? S.setValue(v.pixel2Value(-e + h.fixedHandleStartDragPos), h.$handle, $.snapOnDrag) : (h.handleStartsToMoveWhen1stClickWasOutsideHandle(), S.setValue(v.pixel2Value(e - h.dragDelta), h.$handle, $.snapOnDrag))
                },
                dragHoriz: function(e) {
                    h.dragHorizVert(v.getEventPageX(e))
                },
                dragVert: function(e) {
                    h.dragHorizVert(v.getEventPageY(e))
                },
                stopDrag: function(e) {
                    (h.dragging || h.mouseBtnStillDown || !0 === e) && (l.dragged ? (l.stopDrag(), l.dragged = !1) : $.enabled && (h.enableTextSelection(), h.doDrag = !1, h.firstClickWasOutsideHandle = !1, m(document).unbind("mousemove.rsSliderLens mouseup.rsSliderLens touchmove.rsSliderLens touchend.rsSliderLens"), S.isStepDefined && !h.$animObj && S.setValue(S.currValue[h.$handle === p.$elem1st ? 0 : 1], h.$handle, !0), h.dragDelta = 0, (h.$handle === p.$elem1st ? u.$elem1st : u.$elem2nd).parent().add(g.$wrapper).removeClass($.style.classDragging), c.processFinalChange()), h.dragging = !1), h.mouseBtnStillDown = !1
                },
                stopDragFromDoc: function() {
                    h.stopDrag()
                },
                gotFocus: function() {
                    g.$wrapper.addClass($.style.classFocused), S.isDocumentEventsBound || (m(document).bind("keydown.rsSliderLens", p.keydown).bind("keyup.rsSliderLens", p.keyup), S.isDocumentEventsBound = !0, S.uncommitedValue[0] = S.currValue[0], S.uncommitedValue[1] = S.currValue[1])
                },
                gotFocus1st: function() {
                    h.$animObj || (h.$handle = p.$elem1st, h.gotFocus())
                },
                gotFocus2nd: function() {
                    h.$animObj || (h.$handle = p.$elem2nd, h.gotFocus())
                },
                loseFocus: function() {
                    g.$wrapper.removeClass($.style.classFocused), h.$animObj ? h.$handle && setTimeout(function() {
                        h.$handle.focus()
                    }) : setTimeout(function() {
                        var e = b.add(g.$canvas).add(o.$rangeWrapper).add(o.$range).add(u.$elem1st).add(u.$elem1st.parent()).add(u.$elem1st.parent().parent()).add(u.$elemRange1st).add(u.$elemRange2nd).add(p.$elem1st).add(p.$elem2nd),
                            n = document.activeElement;
                        S.doubleHandles && (e = e.add(u.$elem2nd).add(u.$elem2nd.parent()).add(u.$elem2nd.parent().parent())), m(n).is(e) || (m(document).unbind("keydown.rsSliderLens", p.keydown).unbind("keyup.rsSliderLens", p.keyup), S.isDocumentEventsBound = !1)
                    })
                }
            },
            l = {
                dragDelta: 0,
                dragged: !1,
                origin: 0,
                deltaRange: 0,
                startDrag: function(e) {
                    $.enabled && (S.updateTicksStep(), h.disableTextSelection(), l.origin = S.isHoriz ? g.$wrapper.offset().left : g.$wrapper.offset().top, S.canDragRange && S.doubleHandles && (!0 === $.range.type || "between" === $.range.type) ? l.deltaRange = S.currValue[1] - S.currValue[0] : l.deltaRange = $.range.type[1] - $.range.type[0], l.dragDelta = S.isHoriz ? v.getEventPageX(e) - o.$range.offset().left : v.getEventPageY(e) - o.$range.offset().top, l.dragged = !1, m(document).bind("mousemove.rsSliderLens touchmove.rsSliderLens", l.drag).bind("mouseup.rsSliderLens touchend.rsSliderLens", l.stopDrag))
                },
                drag: function(e) {
                    var n, a = !l.dragged;
                    l.dragged = !0, (S.isRangeFromToDefined || S.canDragRange && S.doubleHandles && (!0 === $.range.type || "between" === $.range.type)) && (a && o.$rangeWrapper.add(u.$elemRange1st).add(u.$elemRange2nd).addClass($.style.classDragging), e = (a = v.pixel2Value((S.isHoriz ? v.getEventPageX(e) : v.getEventPageY(e)) - l.dragDelta - l.origin)) + l.deltaRange, a = S.getCurrValue(a), e = S.getCurrValue(e), $.flipped && (n = a, a = e, e = n), 0 < (n = e - $.max) && n < S.ticksStep && (e = $.max, a -= n), 0 < (n = $.min - a) && n < S.ticksStep && (a = $.min, e += n), a >= $.min && e <= $.max && (!0 === $.range.type || "between" === $.range.type ? (S.currValue[$.flipped ? 1 : 0] = S.getCurrValue(a), S.doubleHandles && (S.currValue[$.flipped ? 0 : 1] = S.getCurrValue(e))) : ($.range.type[0] = a, $.range.type[1] = e, S.currValue[0] = S.getCurrValue(Math.min(Math.max(a, S.getCurrValue(S.currValue[0])), e)), S.doubleHandles && (S.currValue[1] = S.getCurrValue(Math.min(Math.max(a, S.getCurrValue(S.currValue[1])), e)))), S.doubleHandles && (p.stopPosition[0] = S.currValue[0], p.stopPosition[1] = S.currValue[1]), o.$range.add(u.$elemRange1st.children()).add(u.$elemRange2nd ? u.$elemRange2nd.children() : null).css(o.getPropMin(), (a - $.min) / ($.max - $.min) * 100 + "%").css(o.getPropMax(), ($.max - e) / ($.max - $.min) * 100 + "%"), S.setValue(S.currValue[0], p.$elem1st, !0), S.doubleHandles && S.setValue(S.currValue[1], p.$elem2nd, !0)))
                },
                stopDrag: function() {
                    $.enabled && (h.enableTextSelection(), m(document).unbind("mousemove.rsSliderLens mouseup.rsSliderLens touchmove.rsSliderLens touchend.rsSliderLens"), l.dragged && (S.setValue(S.currValue[0], p.$elem1st, !0), S.doubleHandles && S.setValue(S.currValue[1], p.$elem2nd, !0)), S.doubleHandles ? (c.processFinalChange(!0), c.processFinalChange(!1)) : c.processFinalChange(!0), o.$rangeWrapper.add(u.$elemRange1st).add(u.$elemRange2nd).removeClass($.style.classDragging))
                }
            },
            d = function(e) {
                e && (e[0].ondragstart = e[0].onselectstart = function() {
                    return !1
                })
            };
        b.bind("customRuler.rsSliderLens", c.onCustomRuler).bind("customLabel.rsSliderLens", c.onCustomLabel).bind("customLabelAttrs.rsSliderLens", c.onCustomLabelAttrs), S.initVars(), g.init(), u.init(), S.init(), o.init(), u.initRanges(), p.init(), o.appendToDOM(), p.$elem1st.add(p.$elem2nd).appendTo(g.$wrapper), b.bind("getter.rsSliderLens", c.onGetter).bind("setter.rsSliderLens", c.onSetter).bind("resizeUpdate.rsSliderLens", c.onResizeUpdate).bind("change.rsSliderLens", c.onChange).bind("finalchange.rsSliderLens", c.onFinalChange).bind("create.rsSliderLens", c.onCreate).bind("destroy.rsSliderLens", c.onDestroy), .5 < Math.abs($.handle.mousewheel) && b.add(g.$canvas).add(o.$rangeWrapper).add(p.$elem1st).add(p.$elem2nd).bind("DOMMouseScroll.rsSliderLens mousewheel.rsSliderLens", p.onMouseWheel), S.canDragRange && o.$range.bind("mousedown.rsSliderLens touchstart.rsSliderLens", l.startDrag), S.isFixedHandle ? g.$wrapper.bind("mousedown.rsSliderLens touchstart.rsSliderLens", h.startDrag).bind("mouseup.rsSliderLens touchend.rsSliderLens", h.stopDrag) : (g.$wrapper.bind("mousedown.rsSliderLens touchstart.rsSliderLens", h.startDrag).bind("mouseup.rsSliderLens touchend.rsSliderLens", h.stopDrag), p.$elem1st.bind("mousedown.rsSliderLens touchstart.rsSliderLens", h.startDragFromHandle1st)), d(b), d(u.$elem1st), d(p.$elem1st), d(g.$canvas), d(o.$rangeWrapper), d(o.$range), d(u.$elemRange1st), S.doubleHandles && (d(u.$elem2nd), d(p.$elem2nd), d(u.$elemRange2nd), p.$elem2nd.bind("mousedown.rsSliderLens touchstart.rsSliderLens", h.startDragFromHandle2nd)), $.enabled && S.isAutoFocusable && p.$elem1st.focus(), b.triggerHandler("create.rsSliderLens"), S.initHandles()
    }
    m.fn.rsSliderLens = function(e) {
        if ("string" == typeof e) {
            var n = Array.prototype.slice.call(arguments, 1);
            switch (e) {
                case "option":
                    return function() {
                        if ("string" == typeof arguments[0]) switch (arguments.length) {
                            case 1:
                                return this.eq(0).triggerHandler("getter.rsSliderLens", arguments);
                            case 2:
                                for (var e = this.length - 1; - 1 < e; --e) this.eq(e).triggerHandler("setter.rsSliderLens", arguments);
                                return this
                        }
                    }.apply(this, n);
                case "resizeUpdate":
                    return function() {
                        return this.trigger("resizeUpdate.rsSliderLens")
                    }.call(this);
                case "destroy":
                    return function() {
                        return this.trigger("destroy.rsSliderLens")
                    }.call(this);
                default:
                    return this
            }
        }
        var r = m.extend({}, m.fn.rsSliderLens.defaults, e);
        return r.handle = m.extend({}, m.fn.rsSliderLens.defaults.handle, e && e.handle), r.style = m.extend({}, m.fn.rsSliderLens.defaults.style, e && e.style), r.ruler = m.extend({}, m.fn.rsSliderLens.defaults.ruler, e && e.ruler), r.ruler.labels = m.extend({}, m.fn.rsSliderLens.defaults.ruler.labels, e && e.ruler && e.ruler.labels), r.ruler.tickMarks = m.extend({}, m.fn.rsSliderLens.defaults.ruler.tickMarks, e && e.ruler && e.ruler.tickMarks), r.ruler.tickMarks.short = m.extend({}, m.fn.rsSliderLens.defaults.ruler.tickMarks.short, e && e.ruler && e.ruler.tickMarks && e.ruler.tickMarks.short), r.ruler.tickMarks.long = m.extend({}, m.fn.rsSliderLens.defaults.ruler.tickMarks.long, e && e.ruler && e.ruler.tickMarks && e.ruler.tickMarks.long), r.range = m.extend({}, m.fn.rsSliderLens.defaults.range, e && e.range), r.keyboard = m.extend({}, m.fn.rsSliderLens.defaults.keyboard, e && e.keyboard), this.each(function() {
            function e(e) {
                return e = e && "auto" !== e && "" !== e ? parseFloat(e) : 0, isNaN(e) ? 0 : e
            }
            var n, a, t = m(this),
                s = m.extend(!0, {}, r);
            t.is("input[type=range]") && (n = t.val(), a = r.value && "object" == typeof r.value && 2 === r.value.length, n === f || a || (s = m.extend({}, s, {
                value: e(n)
            })), (n = t.attr("min")) !== f && (s = m.extend({}, s, {
                min: e(n)
            })), (n = t.attr("max")) !== f && (s = m.extend({}, s, {
                max: e(n)
            })), (n = t.attr("step")) !== f && (s = m.extend({}, s, {
                step: e(n)
            })), (n = t.attr("disabled")) !== f && (s = m.extend({}, s, {
                enabled: !1
            }))), 0 === t.contents().length && (s.ruler.visible = !0), i(t, s)
        })
    }, m.fn.rsSliderLens.defaults = {
        orientation: "auto",
        width: "auto",
        height: "auto",
        fixedHandle: !1,
        value: 0,
        min: 0,
        max: 100,
        step: 0,
        snapOnDrag: !1,
        enabled: !0,
        flipped: !1,
        contentOffset: .5,
        paddingStart: 0,
        paddingEnd: 0,
        style: {
            classSlider: "sliderlens",
            classFixed: "fixed",
            classHoriz: "horiz",
            classVert: "vert",
            classDisabled: "disabled",
            classHandle: "handle",
            classHandle1: "handle1",
            classHandle2: "handle2",
            classDragging: "dragging",
            classRange: "range",
            classRangeDraggable: "drag",
            classFocused: "focus"
        },
        handle: {
            size: .3,
            zoom: 1.5,
            pos: .5,
            otherSize: "zoom",
            animation: 100,
            easing: "swing",
            mousewheel: 1
        },
        ruler: {
            visible: !0,
            size: 1.5,
            labels: {
                visible: !0,
                values: "step",
                pos: .8,
                onCustomLabel: null,
                onCustomAttrs: null
            },
            tickMarks: {
                short: {
                    visible: !0,
                    step: 2,
                    pos: .2,
                    size: .1
                },
                long: {
                    visible: !0,
                    step: 10,
                    pos: .15,
                    size: .15
                }
            },
            onCustom: null
        },
        range: {
            type: "hidden",
            draggable: !1,
            pos: .46,
            size: .1
        },
        keyboard: {
            allowed: ["left", "right", "up", "down", "home", "end", "pgup", "pgdown", "esc"],
            easing: "swing",
            numPages: 5
        },
        onChange: null,
        onFinalChange: null,
        onCreate: null
    }
}(jQuery);