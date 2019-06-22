<div id="inventary-block-radio">
    <input type="radio" id="inventaryLabel" name="inventaryRadioLabel" {{ click.openInventoryType }} checked/>
    <label for="inventaryLabel">Инвентарь</label>
    <input type="radio" id="reedemLabel" name="inventaryRadioLabel" {{ click.openInventoryType }} />
<!--    onclick="openInventoryType(event)"-->
    <label for="reedemLabel">Выкупить</label>
</div>
<p class="info-link">Получить <a target="_blank" href="https://steamcommunity.com/id/me/tradeoffers/privacy#trade_offer_access_url">TradeURL</a></p>
<div id="trade-url-enter" class="input-forlink" [[tradeUrlParent]]>
    <input placeholder="Trade URL(Ссылка на обмен)" [[tradeUrlInput]] />
    <button type="button" {{ click.tradeUrlEnter }}>Ввести</button>
</div>
<div id="inventory-options">
    {{ inventoryOptions_Chunk }}
</div>
<div id="inventary">
    {{ inventoryItemsBlock_Chunk }}
</div>
<div id="inventary-block-scroll">
    <button type="button" id="choose-all" {{ click.chooseAll }}>Выбрать все</button>
</div>