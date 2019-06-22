class Shop {
    constructor() {
        this.initPages();

        getId('content').innerHTML = this.page.get();
        window.ComponentRenderComplete = true;
        document.dispatchEvent(new CustomEvent('ComponentRenderComplete'));
    }

    initPages() {
        this.page = new ShopChunk('<?=getHTML('../views/Shop.php') ?>');
    }
}

class ShopChunk extends Chunk {
    openCategory(event) {
        const innerVar = {
            'check-csgo' : 'csgo',
            'check-dota2' : 'dota'
        }
        this.sortParams = {
            'lang' : lang,
            'pageNumber' : 1,
            'appid' : innerVar[event.target.id],
            'desc' : this.sortParams['desc'] || '',
            'less' : this.sortParams['less'] || '',
            'more' : this.sortParams['more'] || '',
            'LIKE' : this.sortParams['LIKE'] || ''
        };
        this.shopOptions = this.genDOMParams(innerVar[event.target.id]);
        this.rerenderDom();
        callingA('<?=DIR?>lib/php/Shop', false, '&params=' + JSON.stringify(this.sortParams), this.saveShopItems.bind(this));
    }
    initClass() {
        this.className = 'ShopChunk';
        this.storage = 'shop';
        this.content({'iconcur_Chunk' : this.iconcur});
        this.content({'amountB_Chunk' : this.amountB});
        this.sortParams = {'lang' : lang, 'pageNumber' : 1, 'appid' : 'csgo'};
        this.fetch = <?=getShopItems()?>;
        this.shopOptions = this.genDOMParams('csgo');
        this.marketItems = this.fetch.marketItems;
        this.sortParams['amountPage'] = this.fetch.amountPage;
        const basketBrief = this.basket.getBrief();
        this.content({'basketAmnt_Chunk' : basketBrief.length});
        this.content({'basketValue_Chunk' : basketBrief.summ});
        this.content({'basket_Chunk' : this.basket});
        this.genShop();
        this.genPagenation();
    }
    initChild() {
        this.typeViewChunk = new TypeView('<?=getHTML('../views/Shop.typeView.php') ?>');
        this.optionsButton = new OptionsButton('<div class="valign-center"><button class="standart-button" {{ click.wind }}><div class="icons icon-list"></div>Параметры</button></div><div>{{ popUp }}</div>');
        this.basket = new Basket('<?=getHTML('../views/Shop.basket.php') ?>');
        this.categories = <?=getMassDescriptions()?>;
        this.iconcur = function () {
            if(lang == 'ru') {
                return '&#8381;';
            } else {
                return '&#36;';
            }
        }
        this.amountB = function () {
            if(lang == 'ru') {
                return 'кол.';
            } else {
                return 'amt.';
            }
        }
    }
    rerenderDom() {
        if(window.innerWidth > 1050) {
            this.content({'categories_Chunk' : this.shopOptions});
            this.e.cart.style.marginLeft = '';
            this.e.cart.style.maxWidth = '';
            this.e.cart.children[1].style.display = '';
            this.content({'typeView_Chunk' : this.typeViewChunk});
        } else {
            this.e.cart.style.marginLeft = '0px';
            this.e.cart.style.maxWidth = '150px';
            this.e.cart.children[1].style.display = 'none';
            this.content({'typeView_Chunk' : ''});
            this.content({'categories_Chunk' : this.optionsButton});
        }
    }
    /*-------------------------|
    |                          |
    |  Неопределенные Функции  |
    |                          |
    |-------------------------*/
    genDOMParams(game) {
        let dom = '';

        for(let item in this.categories[game]) {
            const gtNmCtgr = getNameCategory(item, game);
            dom += '<div class="shop-params" name="' + gtNmCtgr + '"><p>' + item + '</p>' +
                '<select {{ change.genParam }} name="' + gtNmCtgr + '">' +
                '<option value="">';
            if(lang == 'ru') { dom += 'Все'; }
            else { dom += 'All'; }
            dom += '</option>';
            for(let category in this.categories[game][item]) {
                dom += '<option>' + this.categories[game][item][category] + '</option>';
            }
            dom += '</select></div>';
        }
        this.shopParams = new Chunk(dom);
        this.shopParams.genParam = this.genParam.bind(this);
        return this.shopParams;
    }
    genParam(event)
    {
        if(event.target.name == 'type') {
            if(event.target.value == 'Граффити' || event.target.value == 'Graffiti') {
                document.querySelector('select[name=graffiti]').parentNode.style.display = 'block';
            } else if(document.querySelector('select[name=graffiti]').parentNode.style.display == 'block') {
                document.querySelector('select[name=graffiti]').parentNode.style.display = '';
            }
        }
        this.sortParams[event.target.name] = event.target.value;
        callingA('<?=DIR?>lib/php/Shop', false, '&params=' + JSON.stringify(this.sortParams), this.saveShopItems.bind(this));
    }
    genShop() {
        let stringItems = '';
        this.marketItems.forEach(function (item, key) {
            stringItems +=
                '<div class="shop-item" style="background-image:url(\'' + item.icon + '\');" ' +
                'value="' + item.value + '" assetid="' + item.assetid + '" appid="' + item.appid + '" name="' + item.name + '" icon="' + item.icon + '">' + //classid="' + item.classid + '"
                '<p>' + item.name + '</p>' +
                '<div><p class="item-price" style="background-color:#' + item.color + ';"><span>' + item.value + '</span><span>&#8381;</span></p>';
            let containBasket = false;
            this.basket.items.forEach(function (bItem) {
                if(bItem.assetid == item.assetid) containBasket = true;
            });
            if(!containBasket) {
                stringItems += '<p class="item-basket" {{ click.toBasket }}></p></div>';
            } else {
                stringItems += '<p class="item-basket active"></p></div>';
            }
            stringItems += '</div>';
        }.bind(this));
        for(let i = 0; i < 7; i++) {
            stringItems += '<div class="shop-item nodisplay"></div>';
        }
        let shopChunk = new Chunk(stringItems);
            shopChunk.toBasket = this.basket.toBasket.bind(this.basket);
        this.content({'shopItems_Chunk' : shopChunk});
    }
    saveShopItems(text) {
        try {
            var shop = '';
            shop = JSON.parse(text);
            this.sortParams['amountPage'] = shop.amountPage;
            this.marketItems = shop.marketItems;
            this.genShop();
        } catch (err) {
            alert('saveShopItems error: ' + err);
        }
        this.genPagenation();
    }
    genPagenation() {
        let strPages = '<span>(`Страницы`):</span><span>';
        for(let i = 1; i < this.sortParams['amountPage']+1; i++) {
            if(i == 4) {
                strPages += ' ... <a href="#" {{ click.openPagenation }} page="' +
                    this.sortParams['amountPage'] + '">' + this.sortParams['amountPage'] + '</a>';
                break;
            }
            if(this.sortParams['pageNumber'] != i) {
                strPages += '<a href="#" {{ click.openPagenation }} page="' + i + '">' + i + '</a>';
            } else {
                strPages += '<a class="active nowPageNumber" style="text-decoration:none;">' + i + '</a>';
            }
        }
        strPages += '</span><span>';

        if(this.sortParams['pageNumber'] != 1) {
            const pN = Number(this.sortParams['pageNumber']) - 1;
            strPages += '<a class="link-prev-page active" {{ click.openPagenation }} page="' +
                pN + '">(`Пред`)</a>';
        } else {
            strPages += '<a class="link-prev-page" onclick="event.preventDefault()">(`Пред`)</a>';
        }
        if(this.sortParams['amountPage'] != this.sortParams['pageNumber']) {
            const pN = Number(this.sortParams['pageNumber']) + 1;
            strPages += '<a class="link-next-page active" {{ click.openPagenation }} page="' +
                pN + '">(`След`)</a>';
        } else {
            strPages += '<a class="link-next-page" onclick="event.preventDefault()">(`След`)</a>';
        }
        strPages += '</span>';
        this.pagenation = new Chunk(strPages);
        this.pagenation.openPagenation = this.openPagenation.bind(this);
        this.content({'shopPagenation_Chunk' : this.pagenation});
    }
    openPagenation(event) {
        event.preventDefault();
        this.sortParams['pageNumber'] = event.target.getAttribute('page');
        callingA('<?=DIR?>lib/php/Shop', false, '&params=' + JSON.stringify(this.sortParams), this.saveShopItems.bind(this));
    }
    /*--------------------------|
     |                          |
     |     Работа с корзиной    |
     |                          |
     |-------------------------*/
    openBasket() {
        if(this.e.cart.classList.contains('fixed')) { this.basket.closeBasket(); return; }
        this.e.cart.classList.add('fixed');
        this.e.cart.style.top = 110 - this.e.cart.offsetHeight + 'px';
        this.basket.openBasket();
    }
}



class TypeView extends Chunk {
    priceSort(event) {
        this.parent.sortParams['desc'] = event.target.value;
        callingA('<?=DIR?>lib/php/Shop', false, '&params=' + JSON.stringify(this.parent.sortParams), this.parent.saveShopItems.bind(this.parent));
    }
    typeView(event) {
        const type = event.target.value;
        const items = document.getElementById('shop-items');
        if(type == 'tile') {
            if(items.classList.contains('list')) {
                items.classList.remove('list');
            }
        } else {
            if(!items.classList.contains('list')) {
                items.classList.add('list');
            }
        }
    }
    priceInterval(event) {
        const elTarget = event.target;
        let sortParams = this.parent.sortParams;
        setTimeout(() => {
            if(elTarget.getAttribute('type') == 'less') {
                sortParams['less'] = {};
                sortParams['less'][elTarget.name] = elTarget.value;
            } else if(elTarget.getAttribute('type') == 'more') {
                sortParams['more'] = {};
                sortParams['more'][elTarget.name] = elTarget.value;
            } else if(elTarget.getAttribute('type') == 'LIKE') {
                sortParams['LIKE'] = {};
                sortParams['LIKE'][elTarget.name] = elTarget.value;
            }
            callingA('<?=DIR?>lib/php/Shop', false, '&params=' + JSON.stringify(sortParams), this.parent.saveShopItems.bind(this.parent));
        }, 1500);
    }
}


class OptionsButton extends Chunk {
    openMUpShop() {
        let newChunk = new Chunk(
            '<div class="popup-left">{{ underCategories_Chunk }} {{ underTypeView_Chunk }}</div>');
        newChunk.className = 'NewChunk';
        newChunk.content({'underTypeView_Chunk' : this.typeViewChunk, 'underCategories_Chunk' : this.shopOptions});
        this.content({'popUp' : newChunk});
    }
}


class Basket extends Chunk {
    initClass() {
        this.items = <?=getBasketString()?>;
        this.len = 0;
        this.summ = 0;
    }
    /**
     * @func getBrief
     * Возвращает количество и суммарную стоимость предметов в корзине
     */
    getBrief() {
        this.items.forEach((item) => {
            this.summ += item.value * item.amount;
            this.len += +item.amount;
        });
        return {'length' : this.len, 'summ' : Number(this.summ).toFixed(2)};
    }
    /**
     * @func toBasket
     * @param {event} assetid, appid, icon, name, value
     * @output:
     * @param {array} item
     * Добавляет товар в массив корзины товаров
     * Устанавливает cookie
     */
    toBasket(event)
    {
        event.target.classList.add('active');
        event.target.onclick = '';
        var elTarget = event.target.parentNode.parentNode;
        this.push(elTarget.getAttribute('value'));
        this.items.push({
            "assetid" : elTarget.getAttribute('assetid'),
            "appid" : elTarget.getAttribute('appid'),
            "icon" : elTarget.getAttribute('icon'),
            "name" : elTarget.getAttribute('name'),
            "value" : elTarget.getAttribute('value'),
            "amount" : 1
        });
        this.setCookie();
    }
    /**
     * @func setCookie
     */
    setCookie() {
        let cBasket = [];
        this.items.forEach(function (item) {
            cBasket.push({
                "assetid" : item.assetid,
                "appid" : item.appid,
                "amount" : item.amount
            });
        });
        setCookie('basket', JSON.stringify(cBasket), {"expires" : 60*60*24*360, "path" : "/"});
    }
    /**
     * @func push
     */
    push(price)
    {
        getId('shop-basket-info-amount').classList.add('push');
        getId('shop-basket-info-amount').innerHTML = ++this.len;
        getId('shop-basket-info-value').classList.add('push');
        this.summ = (+this.summ + +price).toFixed(2);
        getId('shop-basket-info-value').innerHTML = this.summ;
        setTimeout(function () {
            getId('shop-basket-info-amount').classList.remove('push');
            getId('shop-basket-info-value').classList.remove('push');
        }, 500);
    }
    /**
     * @func openBasket
     */
    openBasket() {
        //"assetid"  "appid"  "icon"  "name"  "value"
        getId('openBasket').style.display = 'block';
        this.constructBasket();
        getId('basketFooterValue').innerHTML = getId('shop-basket-info-value').innerHTML;
        var back = document.createElement('div');
        back.setAttribute('id', 'substrate');
        back.style.cssText =
            "background-color: rgba(0,0,0,0.5);\
            position: fixed;\
            top: 0px;\
            left: 0px;\
            right: 0px;\
            bottom: 0px;\
            z-index: 19;";
        document.body.children[0].appendChild(back);
        back.addEventListener('click', this.closeBasket.bind(this));
    }
    closeBasket() {
        this.parent.e.cart.classList.remove('fixed');
        this.parent.e.cart.style.top = '';
        document.body.children[0].removeChild(getId('substrate'));
        getId('openBasket').classList.add('close');
        setTimeout(function () {
            getId('openBasket').style.display = 'none';
            getId('openBasket').classList.remove('close');
        }, 500);
        return;
    }
    /**
     * @func constructBasket
     */
    constructBasket() {
        var strItems = '';
        this.items.forEach(function (item) {
            var minus = item.amount == 0 ? '&times;' : '-';
            var plus = item.amount == 0 ? '' : 'disabled';
            strItems +=
                '<div><img src="' + item.icon + '" /><p class="basketNameP">' + item.name + '</p>' +
                '<p class="basketValP"><span>' + Number(item.value).toFixed(2) + '</span><span>&#8381;</span></p>' +
                '<p class="basketAmP" assetid="' + item.assetid + '">' +
                '<button type="minus" onclick="setAmount(event)">' + minus + '</button><input id="amountBask" value="' + item.amount + '" disabled/>' +
                '<button type="plus" onclick="setAmount(event)" ' + plus + '>+</button></p></div>';
        });
        getId('openBasketItems').innerHTML = strItems;
    }
}