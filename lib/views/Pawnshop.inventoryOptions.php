<div id="inventary-block-search">
    <input type="radio" id="check-csgo" name="checkcsgodota2" game="CSGO" checked/>
    <label class="iconimg-parent" for="check-csgo" id="for-check-csgo">
        <div class="iconimg csgo"></div>
    </label>
    <input type="radio" id="check-dota2" game="DOTA" name="checkcsgodota2"/>
    <label class="iconimg-parent" for="check-dota2" id="for-check-dota2">
        <div class="iconimg dota2"></div>
    </label>
    <input placeholder="Поиск по инвентарю" class="standart-input" id="search" {{ focus.searchInputClick }} {{ keydown.searchInputKeydown }} />
</div>