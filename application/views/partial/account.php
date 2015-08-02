<div class="ui fixed inverted menu">
    <div class="ui container">
        <div href="#" class="header item">
            WebAchievments
        </div>

        <a href="/index.php/auth/logout" class="item right">Выход</a>
    </div>
</div>

<div class="ui main text container">
    <div ng-controller="accountController as ac">
        <div ng-switch on="ac.action">
            <div ng-switch-when="domains" class="domains-list"></div>
            <div ng-switch-when="achievments" class="achievments-list"></div>
            <div ng-switch-when="domain" class="domain-config"></div>
            <div ng-switch-when="statistics" class="domain-statistics"></div>
        </div>
    </div>
</div>

<div class="ui inverted vertical footer segment">
    <div class="ui center aligned container">
        <div class="ui inverted section divider"></div>
        <img src="assets/images/logo.png" class="ui centered mini image">
    </div>
</div>