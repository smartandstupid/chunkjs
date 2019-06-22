class Scrollbar {
    constructor(el) {
        if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
            return;
        }
        this.el = el;
        el.style.overflowY = 'scroll';
        var thisObj = this;

        this.scrollbar = document.createElement('div');
        this.scrollbar.className = 'scrollbar';
        el.parentNode.appendChild(this.scrollbar);
        this.setBarHeight(el, thisObj);
        el.addEventListener('scroll', function (event) {
            thisObj.scrolling(event, thisObj);
        });
        el.addEventListener('DOMNodeInserted', function (event) {
            thisObj.setBarHeight(el, thisObj);
        });
        window.addEventListener('resize', function (event) {
            thisObj.setBarHeight(el, thisObj);
        });
        this.scrollbar.addEventListener('mousedown', function (event) {
            thisObj.fix(event, thisObj);
        });
    }

    setBarHeight(el, thisObj) {
        el.parentNode.style.overflow = 'hidden';
        var elWidth = el.parentNode.clientWidth;
        if(el.parentNode.tagName == 'BODY') {
            var elHeight = window.innerHeight;
            document.body.style.height = window.innerHeight + 'px';
        } else {
            var elHeight = el.parentNode.clientHeight;
        }
        el.style.width = (elWidth + thisObj.barWidth()).toFixed() + 'px';
        el.style.height = elHeight + 'px';

        var el = el.target || el;
        this.scrollHeight = Math.max(
            el.scrollHeight,
            el.offsetHeight,
            el.clientHeight
        );
        var perc = 100 / (this.scrollHeight / el.parentNode.clientHeight);
        thisObj.scrollbar.style.height = (el.parentNode.clientHeight / 100 * perc).toFixed() + 'px';

        thisObj.scrolling(el, thisObj);
    }

    scrolling(event, thisObj) {
        var el = event.target || event;
        var offset = el.pageYOffset || el.scrollTop;
        var scrollTop = (el.parentNode.clientHeight - thisObj.scrollbar.clientHeight) / ((thisObj.scrollHeight - el.parentNode.clientHeight) / offset);
        thisObj.scrollbar.style.top = scrollTop + 'px';
    }

    barWidth() {
        var outside = document.createElement('div');
        var within = document.createElement('div');
            outside.appendChild(within);
            within.innerHTML = 'scroll';
            outside.setAttribute('style', 'position:absolute; top:-1000px; left:0px;');
        document.body.appendChild(outside);
        var w1 = outside.clientWidth;
            within.style.overflowY = 'scroll';
        var w2 = outside.clientWidth;
        return (w2 - w1);
    }

    fix(event, thisObj) {
        event.preventDefault();
        thisObj.mouse = event.pageY || event.clientY;
        document.addEventListener('mousemove', doscroll);
        function doscroll(event) {
            thisObj.doscroll(event, thisObj);
        }
        window.onmouseup = function(event) {
            thisObj.mouse = event.pageY || event.clientY;
            document.removeEventListener('mousemove', doscroll);
        }
    }

    doscroll(event, thisObj) {
        var nowMouse = event.pageY || event.clientY;
        thisObj.scrollDelta(nowMouse - thisObj.mouse, thisObj);
        thisObj.mouse = event.pageY || event.clientY;
    }

    scrollDelta(delta, thisObj) {
        var height = thisObj.el.parentNode.clientHeight;
        var perc = height / delta;
        var pxDelta = thisObj.scrollHeight / perc;

        var offset = thisObj.el.pageYOffset || thisObj.el.scrollTop;
        thisObj.el.scrollTo(0, offset + pxDelta);
    }
}