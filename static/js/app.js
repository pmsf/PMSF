(function () {
    // Methods/polyfills.
    if (!Element.prototype.matches) {
        Element.prototype.matches =
            Element.prototype.matchesSelector ||
            Element.prototype.mozMatchesSelector ||
            Element.prototype.msMatchesSelector ||
            Element.prototype.oMatchesSelector ||
            Element.prototype.webkitMatchesSelector ||
            function (s) {
                var matches = (this.document || this.ownerDocument).querySelectorAll(s)
                var i = matches.length
                while (--i >= 0 && matches.item(i) !== this) {
                }
                return i > -1
            }
    }

    // classList | (c) @remy | github.com/remy/polyfills | rem.mit-license.org
    (function () {
        function t(t) {
            this.el = t
            for (var n = t.className.replace(/^\s+|\s+$/g, '').split(/\s+/), i = 0; i < n.length; i++) {
                e.call(this, n[i])
            }
        }

        function n(t, n, i) {
            Object.defineProperty ? Object.defineProperty(t, n, {
                get: i
            }) : t.__defineGetter__(n, i)
        }

        /* eslint-disable no-unused-expressions */
        if (!(typeof window.Element === 'undefined' || 'classList' in document.documentElement)) {
            var i = Array.prototype
            var e = i.push
            var s = i.splice
            var o = i.join
            t.prototype = {
                add: function (t) {
                    this.contains(t) || (e.call(this, t), this.el.className = this.toString())
                },
                contains: function (t) {
                    return this.el.className.indexOf(t) !== -1
                },
                item: function (t) {
                    return this[t] || null
                },
                remove: function (t) {
                    if (this.contains(t)) {
                        for (var n = 0; n < this.length && this[n] !== t; n++) {
                            s.call(this, n, 1)
                            this.el.className = this.toString()
                        }
                    }
                },
                toString: function () {
                    return o.call(this, ' ')
                },
                toggle: function (t) {
                    this.contains(t) ? this.remove(t) : this.add(t)
                    return this.contains(t)
                }
            }
            window.DOMTokenList = t
            n(Element.prototype, 'classList', function () {
                return new t(this) // eslint-disable-line new-cap
            })
        }
    })()

    // Vars.
    var $body = document.querySelector('body')

    // Breakpoints.
    skel.breakpoints({
        xlarge: '(max-width: 1680px)',
        large: '(max-width: 1280px)',
        medium: '(max-width: 980px)',
        small: '(max-width: 736px)',
        xsmall: '(max-width: 480px)'
    })

    // Disable animations/transitions until everything's loaded.
    $body.classList.add('is-loading')

    window.addEventListener('load', function () {
        $body.classList.remove('is-loading')
    })
})()
