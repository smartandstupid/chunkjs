<div id="shop-sort">
    <button>(`Сортировать`):</button>
    <select {{ change.priceSort }}>
        <option value="">(`Цена возрастает`)</option>
        <option value="desc">(`Цена понижается`)</option>
    </select>
</div>
<div id="shop-view">
    <button>(`Отображать`):</button>
    <select {{ change.typeView }}>
        <option value="tile">(`Плитка`)</option>
        <option value="list">(`Список`)</option>
    </select>
</div>
<div id="shop-pricesort">
    <input placeholder="(`Цена от`)" name="value" type="more" {{ keyup.priceInterval }} />
    <input placeholder="(`Цена до`)" name="value" type="less" {{ keyup.priceInterval }} />
</div>