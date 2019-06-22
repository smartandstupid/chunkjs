class SellSkins {
    constructor() {
        this.initPages();

        getId('content').innerHTML = this.page.get() + this.page.anchor('pawnshop');
        // this.page.event();
        // this.page.findElements();
        // if(this.page.rerenderDom != undefined) { this.page.rerenderDom(); }
        // for(let child = 0; child < this.page.children.length; child++) {
        //     this.page.children[child].contentComplete();
        // }
        // this.page.contentComplete();
        window.ComponentRenderComplete = true;
        document.dispatchEvent(new CustomEvent('ComponentRenderComplete'));
    }

    initPages() {
        this.page = new SellSkinsInventoryBlock('<?=getHTML('../views/SellSkins.php') ?>');
        // new SellSkinsChunk('<?=getHTML('../views/SellSkins.php') ?>');
    }
}

class SellSkinsChunk extends Chunk {
    /*------------------------|
     |        FUNCTIONS       |
     |-----------------------*/
    /*-----------------------|
     |    INITIALIZATION     |
     |-----------------------*/
    initClass() {
        this.className = 'PawnshopChunk';
        this.storage = 'pawnshop';
        this.content({'inventoryBlock_Chunk' : this.inventoryBlock});
        // this.content({'ransomBlock_Chunk' : this.putBlock});
    }
    initChild() {

        this.inventoryBlock = new SellSkinsInventoryBlock('<?=getHTML('../views/Pawnshop.inventory.php') ?>');
        //
        // this.ransomBlock = new Chunk('<?=getHTML('../views/Pawnshop.ransom.php') ?>');
        // this.ransomBlock.content({'payments_Chunk' : new Chunk('<?=getHTML('../views/Payments.php') ?>')});

            // this.putBlock = new SellSkinsPutBlock('<?=getHTML('../views/Pawnshop.putinpledge.php') ?>');

        this.completeBlock = new Chunk('<?=getHTML('../views/Pawnshop.complete.php') ?>');

    }
}

class SellSkinsInventoryBlock extends Chunk {
    /*-----------------------|
     |         EVENTS         |
     |-----------------------*/
    openInventoryType(event) {
        const innerVar = {
            'inventaryLabel' : 'officePage',
            'reedemLabel' : 'historyPage'
        }
        if(event.target.id == 'reedemLabel') {
            this.inventoryOptions.style({display : 'none'});
            this.parent.content({'ransomBlock_Chunk' : this.parent.ransomBlock});

            if(window.innerWidth < 860) {
                getId('content').style.paddingTop = '60px';
                getId('trade-url-enter').style.top = '-50px';
            } else { getId('content').style.paddingTop = ''; }
            // this.gritPreloader('inventoryItemsBlock_Chunk');
        } else {
            this.inventoryOptions.style({display : ''});
            this.parent.content({'ransomBlock_Chunk' : this.parent.putBlock});

            if(window.innerWidth < 860) {
                getId('content').style.paddingTop = '120px';
                getId('trade-url-enter').style.top = '';
            } else { getId('content').style.paddingTop = ''; }
            // this.gritPreloader('inventoryItemsBlock_Chunk', false);
        }
        // this.content({'privateOfficePage' : this[innerVar[event.target.innerHTML]]});
    }
    tradeUrlEnter() {
        if(this.e.tradeUrlInput.value == '') {
            this.exceptionEmptyTradeUrl(this.e.tradeUrlInput);
        }
    }
    chooseAll() {
        if(this.e.tradeUrlInput.value == '') {
            this.exceptionEmptyTradeUrl('inventoryItemsBlock_Chunk');
        }
    }
    /*-----------------------|
     |       FUNCTIONS       |
     |-----------------------*/
    exceptionEmptyTradeUrl(el) {
        const text = '<p class="info-link">Ссылка на обмен пуста. Введите ссылку на обмен. Получить можно <a target="_blank" href="https://steamcommunity.com/id/me/tradeoffers/privacy#trade_offer_access_url">здесь</a>.</p>';
        if(typeof el == 'object') {
            this.modalArrow(el, text);
        } else {
            this.modalW(el, text);
        }
    }
    /*-----------------------------------------|
     |        FUNCTIONS  InventoryOptions      |
     |----------------------------------------*/
    searchInputClick(event) {
        console.log(this.parent.e);
        if(this.e.tradeUrlInput.value == '') {
            this.exceptionEmptyTradeUrl(event.target);
        }
    }
    searchInputKeydown(event) {
        if(this.parent.e.tradeUrlInput.value == '') {
            event.preventDefault();
            this.parent.exceptionEmptyTradeUrl(event.target);
        }
    }
    /*-----------------------|
     |    INITIALIZATION     |
     |-----------------------*/
    initClass() {
        this.className = 'InventoryBlock';

        if(window.innerWidth < 860) { getId('content').style.paddingTop = '120px'; }
        else { getId('content').style.paddingTop = ''; }

        document.addEventListener('ComponentRenderComplete', function () {
            this.content({'inventoryItemsBlock_Chunk' : this.itemsBlockEmpty});
            // gritPreloader(getId('inventary'), 'rgb(11, 89, 158)');
        }.bind(this));
        window.addEventListener('resize', () => {
        });

    }
    initChild() {

        this.itemsBlockEmpty = function() {
            if(window.innerWidth > 1155)
                return '<label></label> <label></label> <label></label> <label></label> <label></label> <label></label> <label></label> <label></label> <label></label> <label></label><label class="invisibility"></label><label class="invisibility"></label><label class="invisibility"></label><label class="invisibility"></label><label class="invisibility"></label>';
            else
                return '<label></label> <label></label> <label></label> <label></label> <label></label> <label></label> <label></label> <label></label> <label></label><label class="invisibility"></label><label class="invisibility"></label><label class="invisibility"></label><label class="invisibility"></label><label class="invisibility"></label>';
        }
    }
    rerenderDom() {
        this.content({'inventoryItemsBlock_Chunk' : this.itemsBlockEmpty});
        if(window.innerWidth < 860) {
            getId('content').style.paddingTop = '120px';
            this.e.tradeUrlParent.style.top = '-110px';
        } else {
            this.e.tradeUrlParent.style.top = '';
            getId('content').style.paddingTop = '';
        }
    }
}

class SellSkinsPutBlock extends Chunk {
    /*-----------------------|
     |        FUNCTIONS       |
     |-----------------------*/
    getCash(event) {
        if(this.parent.inventoryBlock.e.tradeUrlInput.value == '') {
            this.parent.inventoryBlock.exceptionEmptyTradeUrl(event.target);
        }
    }
    /*-----------------------|
     |    INITIALIZATION     |
     |-----------------------*/
    initClass() {
        this.className = 'PutBlock';

        this.content({'payments_Chunk' : this.paymentsBlock});
    }

    initChild() {

        this.paymentsBlock = new Chunk('<?=getHTML('../views/Payments.php') ?>');

    }
}