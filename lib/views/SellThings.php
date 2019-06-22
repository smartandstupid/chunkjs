<div id="inventary-block" class="blocks">
    <div id="inventary-block-radio">
        <input type="radio" id="inventaryLabel" name="inventaryRadioLabel" {{ click.openInventoryType }} checked/>
        <label for="inventaryLabel">Продать вещи Dota 2</label>
    </div>
    <p class="info-link">Получить <a target="_blank" href="https://steamcommunity.com/id/me/tradeoffers/privacy#trade_offer_access_url">TradeURL</a></p>
    <div id="trade-url-enter" class="input-forlink" [[tradeUrlParent]]>
        <input placeholder="Trade URL(Ссылка на обмен)" [[tradeUrlInput]] />
        <button type="button" {{ click.tradeUrlEnter }}>Ввести</button>
    </div>
    <div id="inventory-options">
        <div id="inventary-block-search">
            <input placeholder="Поиск по инвентарю" class="standart-input" id="search" style="margin:0px;" {{ focus.searchInputClick }} {{ keydown.searchInputKeydown }} />
        </div>
    </div>
    <div id="inventary" class="sell-inventory">
        {{ inventoryItemsBlock_Chunk }}
    </div>
    <div id="inventary-block-scroll">
        <button type="button" id="choose-all" {{ click.chooseAll }}>Выбрать все</button>
    </div>
</div>