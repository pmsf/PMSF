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

    // addEventsListener
    var addEventsListener = function (o, t, e) {
        var n
        var i = t.split(' ')
        for (n in i) {
            o.addEventListener(i[n], e)
        }
    };

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

    // Nav.
    var $nav = document.querySelector('#nav')
    var $navToggle = document.querySelector('a[href="#nav"]')
    var $navClose

    // Stats.
    var $stats = document.querySelector('#stats')
    var $statsToggle = document.querySelector('a[href="#stats"]')
    var $statsClose

    // Event: Prevent clicks/taps inside the nav from bubbling.
    addEventsListener($nav, 'click touchend', function (event) {
        event.stopPropagation()
    })

    if ($stats) {
        // Event: Prevent clicks/taps inside the stats from bubbling.
        addEventsListener($stats, 'click touchend', function (event) {
            event.stopPropagation()
        })
    }

    // Event: Hide nav on body click/tap.
    addEventsListener($body, 'click touchend', function (event) {
        // on ios safari, when navToggle is clicked,
        // this function executes too, so if the target
        // is the toggle button, exit this function
        if (event.target.matches('a[href="#nav"]')) {
            return
        }
        if ($stats && event.target.matches('a[href="#stats"]')) {
            return
        }
        $nav.classList.remove('visible')
        if ($stats) {
            $stats.classList.remove('visible')
        }
    })
    // Toggle.

    // Event: Toggle nav on click.
    $navToggle.addEventListener('click', function (event) {
        event.preventDefault()
        event.stopPropagation()
        $nav.classList.toggle('visible')
    })

    // Event: Toggle stats on click.
    if ($statsToggle) {
        $statsToggle.addEventListener('click', function (event) {
            event.preventDefault()
            event.stopPropagation()
            $stats.classList.toggle('visible')
        })
    }

    // Close.

    // Create elements.
    $navClose = document.createElement('a')
    $navClose.href = '#'
    $navClose.className = 'close'
    $navClose.tabIndex = 0
    $nav.appendChild($navClose)

    if ($stats) {
        $statsClose = document.createElement('a')
        $statsClose.href = '#'
        $statsClose.className = 'close'
        $statsClose.tabIndex = 0
        $stats.appendChild($statsClose)
    }

    // Event: Hide on ESC.
    window.addEventListener('keydown', function (event) {
        if (event.keyCode === 27) {
            $nav.classList.remove('visible')
            if ($stats) {
                $stats.classList.remove('visible')
            }
        }
    })

    // Event: Hide nav on click.
    $navClose.addEventListener('click', function (event) {
        event.preventDefault()
        event.stopPropagation()
        $nav.classList.remove('visible')
    })

    if ($statsClose) {
        // Event: Hide stats on click.
        $statsClose.addEventListener('click', function (event) {
            event.preventDefault()
            event.stopPropagation()
            $stats.classList.remove('visible')
        })
    }
})()
