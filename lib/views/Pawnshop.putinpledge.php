<div id="ransom-block-visible" class="active">
    <div id="inventary-block-radio">
        <input type="radio" name="type-sale" id="put" checked/>
        <label for="put">Заложить</label>
        <input type="radio" name="type-sale" id="sale" />
        <label for="sale">Продать</label>
    </div>
    <div id="ransom-price">
        <div id="ransom-stage-one">+0%</div>
        <div id="ransom-stage-line">Размер выплаты за товары<div id="ransom-percent">+<span>0</span>%</div><div id="ransom-price-view"><span>0.00</span> <span>&#8381;</span></div><div id="perc-line"></div></div>
        <div id="ransom-stage-two">+3%</div>
    </div>
    <div id="ransom-block-payments">
        <span>Выберите метод выплаты:</span>
        {{ payments_Chunk }}
        <button id="makeOffer" class="standart-button" {{ click.getCash }}>получить деньги</button>
    </div>
</div>