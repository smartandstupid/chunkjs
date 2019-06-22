<div id="profile">
    <div id="profile-menu">
        <p {{ click.openPage }}>Личный кабинет</p>
        <p {{ click.openPage }}>История сделок</p>
        <p {{ click.openPage }} class="active">Рефералы</p>
    </div>
    <div id="profile-referals">
        <div id="profile-avatar">
            <div>
                <img src="{{[avatar]}}" />
            </div>
            <span>{{[name]}}</span>
        </div>
        <div id="profile-referal-link">
            {{ privateOfficePage }}
        </div>
        <canvas id="profile-earned-today" width="225" height="225"></canvas>
    </div>
</div>
<span style="display:none" id="inprofile"></span>