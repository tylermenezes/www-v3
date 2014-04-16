(function(){
"use strict";

// Typekit
(function(d) {
    var config = {
            kitId: 'ogt6dod',
            scriptTimeout: 3000
        },
        h=d.documentElement,t=setTimeout(function(){h.className=h.className.replace(/\bwf-loading\b/g,"")+" wf-inactive";},config.scriptTimeout),tk=d.createElement("script"),f=false,s=d.getElementsByTagName("script")[0],a;h.className+=" wf-loading";tk.src='//use.typekit.net/'+config.kitId+'.js';tk.async=true;tk.onload=tk.onreadystatechange=function(){a=this.readyState;if(f||a&&a!="complete"&&a!="loaded")return;f=true;clearTimeout(t);try{Typekit.load(config)}catch(e){}};s.parentNode.insertBefore(tk,s)
})(document);

// Utils
var utils = {
    load_external: function(src, callback) {
        var s = document.createElement('script');
        s.src = src;
        s.async = true;
        s.onreadystatechange = s.onload = function() {
            var state = s.readyState;
            if (!callback.done && (!state || /loaded|complete/.test(state))) {
                callback.done = true;
                callback();
            }
        };
        document.getElementsByTagName('head')[0].appendChild(s);
    },
    is_leap_year: function(year) {
        !((year % 4) || (!(year % 100) && (year % 400)));
    },
    days_in_year: function(year) {
        return this.is_leap_year(year) ? 366 : 365;
    },
    y_combinator: function(le) {
        return function(f) {
            return f(f);
        }(function(f) {
            return le(
            function(x) { return (f(f))(x); }
            );
        });
    },
    get_elem_vertical_offset: function(obj) {
        var curtop = 0;
        if (obj.offsetParent) {
            do {
                curtop += obj.offsetTop;
            } while (obj = obj.offsetParent);
            return [curtop];
        }
    }
}

var life_progress = {
    born_at: 710395200 * 1000,
    die_at: 3487464000 * 1000,
    has_started: false,
    get_die_at_date: function() {
        return new Date(this.die_at);
    },
    get_elapsed_seconds: function() {
        return Math.floor(((+new Date()) - this.born_at)/1000);
    },
    get_remaining_seconds: function() {
        return Math.floor((this.die_at - (+ new Date()))/1000);
    },
    get_total_seconds: function() {
        return Math.floor((this.die_at - this.born_at)/1000);
    },
    get_percent: function() {
        return this.get_elapsed_seconds()/this.get_total_seconds();
    },
    get_countdown_component_simple: function(div, mod) {
        return Math.floor((this.get_remaining_seconds()/div) % mod);
    },
    get_years: function() {
        return Math.floor(this.get_die_at_date().getFullYear() - (new Date()).getFullYear())
    },
    get_countdown: function(){
        return {
            years: this.get_years(),
            months: Math.floor(   (this.get_die_at_date().getMonth() + (this.get_years() * 12))
                                - (new Date()).getMonth()
                    ) % 12,
            days: this.get_countdown_component_simple(60*60*24, utils.days_in_year((new Date()).getFullYear())),
            hours: this.get_countdown_component_simple(60*60, 24),
            minutes: this.get_countdown_component_simple(60, 60),
            seconds: this.get_countdown_component_simple(1, 60)
        };
    },
    tick: function() {
        var countdown = life_progress.get_countdown();
        var progress = life_progress.get_percent();

        document.getElementById('life-remaining-years').textContent = countdown.years + ' year' + (countdown.years != 1 ? 's': '');
        document.getElementById('life-remaining-months').textContent = countdown.months + ' month' + (countdown.months != 1 ? 's' : '');
        document.getElementById('life-remaining-days').textContent = countdown.days + ' day' + (countdown.days != 1 ? 's' : '');
        document.getElementById('life-remaining-hours').textContent = countdown.hours + ' hour' + (countdown.hours != 1 ? 's' : '');
        document.getElementById('life-remaining-minutes').textContent = countdown.minutes + ' minute' + (countdown.minutes != 1 ? 's' : '');
        document.getElementById('life-remaining-seconds').textContent = countdown.seconds + ' second' + (countdown.seconds != 1 ? 's' : '');

        document.getElementById('life-progress-elapsed').style.width = Math.round(progress * 10000)/100+'%';
        document.getElementById('life-progress-elapsed').textContent = Math.round(progress * 10000)/100+'%';
    },
    start: function() {
        if (this.has_started) return;
        this.has_started = true;

        var animator = null;
        if (typeof(window['requestAnimationFrame']) !== 'undefined') {
            animator = window['requestAnimationFrame'];
        } else if (typeof(window['mozRequestAnimationFrame']) !== 'undefined') {
            animator = window['mozRequestAnimationFrame'];
        } else if (typeof(window['webkitRequestAnimationFrame']) !== 'undefined') {
            animator = window['webkitRequestAnimationFrame'];
        } else {
            animator = function(lambda) {
                lambda();
            }
        }

        setInterval(function(){
            animator(life_progress.tick);
        }, 1000);

        animator(function(){
            life_progress.tick();
            document.getElementById('lifecount').style.display = 'inline';
        });
    }
}

// Events
window.addEventListener('load', function(){
    life_progress.start();
});

})();