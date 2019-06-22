window.openClass = [];

class Chunk {
    constructor(arg, childs = null) {
        this.template = arg;
        this.i18n();
        this.parent = '';
        this.children = [];
        this.states = this.parseStates();
        this.events = this.parseEvents();
        this.storage = '';
        this.className = '';
        this.props = this.parseProps();
        this.modals = {};
        this.modalCss = "\
                background: rgb(0,99,132);\
                opacity: 0;\
                font-size: 1em !important;\
                padding: 2em;\
                color: #fff;\
                position: absolute;\
                transform: translateY(100%);\
                transition: all 500ms ease-out;\
            ";
        this.modalsTime = [];
        this.initChild();
        this.initClass();
        this.e = this.parseElements();
        this.renderHandler = this.renderCompleteCallback.bind(this);
        document.addEventListener('ComponentRenderComplete', this.renderHandler);
    }

    i18n() {
        const regexp = /[(]\`(.*?)\`[)]/g;
        let result;
        while (result = regexp.exec(this.template)) {
            this.template = this.template.replace(result[0], l[result[1]]);
        }
    }

    renderCompleteCallback() {
        this.renderCompleteCallback = false;
        document.removeEventListener('ComponentRenderComplete', this.renderHandler);
        this.contentComplete(false);
        if(this.rerenderDom != undefined) {
            this.rerenderDom();
            window.addEventListener('resize', () => {
                this.rerenderDom();
            });
        }
        if(this.destructor != undefined) {
            this.destructHandler = function destInit(event) {
                this.destructor();
                document.removeEventListener('ComponentRenderComplete', this.destructHandler);
                console.log('Destruct');
            }.bind(this);
            document.addEventListener('ComponentRenderComplete', this.destructHandler);
        }
    }

    initClass() {
        this.className = 'Chunk';
    }

    initChild(childs) {
        if(childs != null) {
            for(let child in childs) {
                this.content({child : childs[child]});
            }
        }
    }

    get() {
        // Надо бы переименовать этот метод в replace
        let content = this.template;
        for(let key in this.states) {
            while(content.indexOf('{{ ' + key + ' }}') != -1) {
                content = content.replace('{{ ' + key + ' }}', this.states[key]);
            }
        }
        for(let key in this.events) {
            while(content.indexOf('{{ ' + key + ' }}') != -1) {
                content = content.replace(
                    '{{ ' + key + ' }}',
                    key.split('.')[0] + '="' + this.className + key.split('.')[1] + '"'
                );
            }
        }
        for(let key in this.props) {
            while(content.indexOf('{{[' + key + ']}}') != -1) {
                content = content.replace(
                    '{{[' + key + ']}}',
                    this.props[key]
                );
            }
        }
        return content;
    }

    render() {
        const massEl = getClass('in' + this.storage);
        for(let el = 0; el < massEl.length; el++) {
            massEl[el].parentNode.innerHTML = this.get() + this.anchor(this.storage);
        }
    }

    parseStates() {
        const regexp = /\{\{\s([^\s|^\.]*)\s\}\}/g;
        let obj = {};
        let result;
        while (result = regexp.exec(this.template)) {
            obj[result[1]] = this.states && this.states[result[1]] ? (this.states[result[1]] + this.anchor(result[1])) : this.anchor(result[1]);
        }
        return obj;
    }

    parseEvents() {
        const regexp = /\{\{\s([^\s]+\.[^\s]*)\s\}\}/g;
        let obj = {};
        let result;
        while (result = regexp.exec(this.template)) {
            obj[result[1]] = result[1].split('.')[1];
        }
        return obj;
    }

    parseProps() {
        const regexp = /\{\{\[([^\s]*)\]\}\}/g;
        let obj = {};
        let result;
        while (result = regexp.exec(this.template)) {
            obj[result[1]] = '';
        }
        return obj;
    }

    parseElements() {
        const regexp = /\[\[([^\s]*)\]\]/g;
        let obj = {};
        this.template = this.template.replace(regexp, (m, p1, offset) => {
            obj[p1] = '';
            return 'element="' + this.className + p1 + '"';
        });
        return obj;
    }

    findElements() {
        for(let key in this.e) {
            const el = document.querySelector('[element="'+ this.className + key +'"]');
            try {
                this.e[key] = el;
            } catch (e) { console.log(e); }
        }
    }

    setStorage(key, parent) {
        this.storage = key;
        this.parent = parent;
    }

    content(arg) {
        for(let key in arg) {
            if(typeof arg[key] == 'function') {
                this.states[key] = arg[key]() + this.anchor(key);
                const massEl = getClass('in' + key);
                for(let el = 0; el < massEl.length; el++) {
                    if(massEl[el] != undefined) {
                        massEl[el].parentNode.innerHTML = this.states[key];
                    }
                }
            } else if(typeof arg[key] == 'object') {
                this.states[key] = arg[key].get() + this.anchor(key);
                this.children.push(arg[key]);
                arg[key].setStorage(key, this);
                if(!window.ComponentRenderComplete) { return; }
                const massEl = getClass('in' + key);
                for(let el = 0; el < massEl.length; el++) {
                    if(massEl[el] != undefined) {
                        massEl[el].parentNode.innerHTML = this.states[key];
                    }
                }
                arg[key].contentComplete(true);
            } else {
                const massEl = getClass('in' + key);
                for(let el = 0; el < massEl.length; el++) {
                    if(massEl[el] != undefined) {
                        massEl[el].parentNode.innerHTML = arg[key] + this.anchor(key);
                    }
                }
                this.states[key] = arg[key] + this.anchor(key);
            }
        }

    }

    contentComplete(isRecurs = true) {
        // if(getClass('in' + this.parent.storage)[0] == undefined) { return; }
        this.event();
        this.findElements();
        if(isRecurs) {
            for(let child = 0; child < this.children.length; child++) {
                this.children[child].contentComplete();
            }
        }
    }

    property(arg, rendering) {
        for(let key in arg) {
            this.props[key] = arg[key];
        }
        if(getClass('in' + this.storage)[0] != undefined){ this.render(); }
    }

    event() {
        for(let key in this.events) {
            const els = document.querySelectorAll('[' + key.split('.')[0] + '="'+ this.className + key.split('.')[1] +'"]');
            for(let el = 0; el < els.length; el++) {
                try {
                    els[el].addEventListener(key.split('.')[0], this[key.split('.')[1]].bind(this));
                } catch (e) {
                    console.log('Error: ' + e + '; El: ' + els[el] + ' ; ' + this.className);
                }
            }
        }
    }

    anchor(key) {
        return '<span style="display:none" class="in' + key + '"></span>';
    }

    style(obj) {
        for(let key in obj) {
            const massEl = getClass('in' + this.storage);
            for(let el = 0; el < massEl.length; el++) {
                massEl[el].parentNode.style[key] = obj[key];
            }
        }
    }

    gritPreloader(chunk, alive = true) {
        if(!alive) {
            const massEl = getClass('in' + chunk);
            for(let el = 0; el < massEl.length; el++) {
                if(massEl[el].parentNode.getElementsByClassName('gritPreloader')[0] != undefined) {
                    massEl[el].parentNode.removeChild(
                        massEl[el].parentNode.getElementsByClassName('gritPreloader')[0]
                    );
                }
            }
            return;
        }
        const massEl = getClass('in' + chunk);
        for(let el = 0; el < massEl.length; el++) {
            gritPreloader(massEl[el].parentNode, 'rgb(11, 89, 158)');
        }
    }

    modalW(addr, text) {
        if(this.modals.hasOwnProperty(addr)) {
            this.modals[addr].style.background = 'rgb(255,53,49)';
            setTimeout(function () {
                this.modals[addr].style.background = 'rgb(0,99,132)';
            }.bind(this), 500);
            return;
        }
        this.deleteModals();
        let modal = document.createElement('div');
        this.modals[addr] = modal;
        modal.style.cssText = this.modalCss;
        const massEl = getClass('in' + addr);
        for(let el = 0; el < massEl.length; el++) {
            setAbsolute(massEl[el].parentNode);
            massEl[el].parentNode.appendChild(modal);
        }
        modal.innerHTML = text;
        const closeMod = this.closeMod();
        modal.insertBefore(closeMod, modal.children[0]);
        for(let el = 0; el < massEl.length; el++) {
            modal.style.left = massEl[el].parentNode.clientWidth/2 - modal.clientWidth/2 + 'px';
            modal.style.top = massEl[el].parentNode.clientHeight/2 - modal.clientHeight/2 + 'px';
        }
        modal.style.transform = 'translateY(0px)';
        modal.style.opacity = 1;
        setTimeout(this.deleteModalsParallax.bind(this), 5000);
    }

    modalArrow(addr, text) {
        if(this.modals.hasOwnProperty(addr)) {
            this.modals[addr].style.background = 'rgb(255,53,49)';
            setTimeout(function () {
                this.modals[addr].style.background = 'rgb(0,99,132)';
            }.bind(this), 500);
            return;
        }
        this.deleteModals();
        let modal = document.createElement('div');
        this.modals[addr] = modal;
        modal.style.cssText = this.modalCss;
        modal.innerHTML = text;
        const closeMod = this.closeMod();
        modal.insertBefore(closeMod, modal.children[0]);
        if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
            document.body.appendChild(modal);
        } else {
            document.body.children[0].appendChild(modal);
        }
        const offset = this.offset(addr);
        const styMod = getComputedStyle(modal);
        modal.style.left = offset.x + 'px';
        let scrolled = 0;
        if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
            scrolled = document.body.pageYOffset || document.body.scrollTop;
        } else {
            scrolled = document.body.children[0].pageYOffset || document.body.children[0].scrollTop;
        }
        scrolled += window.innerHeight/2;
        modal.style.minWidth = '200px';
        modal.style.maxWidth = window.innerWidth - offset.x + 'px';
        modal.style.width = 'calc(' + addr.clientWidth + 'px - ' + styMod.padding + ' - ' + styMod.padding + ')';
        if( (scrolled - offset.y) > (offset.y - scrolled) ) {
            modal.style.top = offset.y + addr.clientHeight + 'px';
        } else {
            modal.style.top = 'calc(' + (offset.y - modal.clientHeight) + 'px' + ')';
        }
        modal.style.transform = 'translateY(0px)';
        modal.style.opacity = 1;
        this.modalsTime.push(setTimeout(this.deleteModalsParallax.bind(this), 8000));
    }

    closeMod() {
        let closeMod = document.createElement('div');
        closeMod.style.cssText = "\
            padding: 5px;\
            position: absolute;\
            right: 0px;\
            top: 0px;\
            color: rgb(199,239,252);\
            font-size: 0.5em;\
            cursor: pointer;\
            transition: background 200ms;\
            ";
        closeMod.addEventListener('mouseover', function (event) {
            event.target.style.background = 'rgb(92, 188, 219)';
        });
        closeMod.addEventListener('mouseout', function (event) {
            event.target.style.background = '';
        });
        closeMod.addEventListener('click', function () {
            this.deleteModalsParallax();
        }.bind(this));
        closeMod.innerHTML = 'ЗАКРЫТЬ <span style="border-radius:100%; border:1px solid rgb(199,239,252);">&#10008;</span>';
        return closeMod;
    }

    deleteModals() {
        for(let key in this.modalsTime) {
            clearTimeout(this.modalsTime[key]);
        }
        this.modalsTime = [];
        for(let key in this.modals) {
            this.modals[key].parentNode.removeChild(this.modals[key]);
        }
        this.modals = [];
    }

    deleteModalsParallax() {
        for(let key in this.modalsTime) {
            clearTimeout(this.modalsTime[key]);
        }
        this.modalsTime = [];
        for(let key in this.modals) {
            this.modals[key].style.transform = 'translateY(100%)';
            this.modals[key].style.opacity = '0';
            const modal = this.modals[key];
            setTimeout(function () {
                if(modal != undefined) {
                    modal.parentNode.removeChild(modal);
                }
            }.bind(this), 500);
        }
        this.modals = [];
    }

    offset(el) {
        let offs = {x : 0, y : 0};
        while (el.offsetParent.tagName != 'BODY') {
            offs.y += el.offsetTop;
            offs.x += el.offsetLeft;
            el = el.offsetParent;
        }
        return offs;
    }
}