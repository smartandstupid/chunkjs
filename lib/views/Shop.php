<div id="shop">
    <div id="shop-header"><p><?=$l['Магазин']?></p></div>
    <div id="shop-search-params">
        <div class="shop-params icon">
            <input type="radio" id="check-csgo" name="appid" value="730" game="CSGO" {{ click.openCategory }} checked/>
            <label class="iconimg-parent" for="check-csgo" id="for-check-csgo">
                <div class="iconimg csgo"></div>
            </label>
            <input type="radio" id="check-dota2" game="DOTA" name="appid" value="570" {{ click.openCategory }} />
            <label class="iconimg-parent" for="check-dota2" id="for-check-dota2">
                <div class="iconimg dota2"></div>
            </label>
        </div>
        <div id="_categories">
            {{ categories_Chunk }}
        </div>
        <div id="shop-search">
            <input placeholder="(`Поиск вещей`)" class="standart-input" name="name" type="LIKE" {{ keyup.priceInterval }} />
            <div id="shop-search-things" [[cart]] >
                <div id="icon-basket" {{ click.openBasket }}></div>
                <div id="shop-basket-info">
                    <p><span id="shop-basket-info-amount">{{ basketAmnt_Chunk }}</span><span>{{ amountB_Chunk }}</span></p>
                    <p><span id="shop-basket-info-value">{{ basketValue_Chunk }}</span><span>{{ iconcur_Chunk }}</span></p>
                </div>
            </div>
            <div id="openBasket">
                {{ basket_Chunk }}
            </div>
        </div>
    </div>
    <div id="shop-pagenation">
        <div id="shop-type-view">

            {{ typeView_Chunk }}

        </div>
        <div class="shop-pagenation-page">
            {{ shopPagenation_Chunk }}
        </div>
    </div>
    <div id="shop-items">
        {{ shopItems_Chunk }}
    </div>
    <div id="bottom-pagenation">
        <div class="shop-pagenation-page">
            {{ shopPagenation_Chunk }}
        </div>
    </div>
</div>