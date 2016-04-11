<div ng-class="{show: domain && !(domain.id > 0)}" class="protected">
    <div class="ui message error">
        У вас нет доступа к данному разделу
    </div>
</div>
<div ng-controller="domainStatisticController as dsc" class="ui segment bottom attached">
    <div class="ui huge breadcrumb">
        <a class="section" href="/">Домены</a>
        <i class="right chevron icon divider"></i>
        <div class="active section">{{domain.name}} - Статистика</div>
    </div>
    <div class="ui form">
        <div class="two fields">
            <div class="field">
                <label>Начиная с:</label>
                <input class="datetimepicker" type="text" ng-model="dsc.statistic.start_date" ng-change="dsc.get()">
            </div>
            <div class="field">
                <label>Заканчивая:</label>
                <input class="datetimepicker" type="text" ng-model="dsc.statistic.end_date" ng-change="dsc.get()">
            </div>
        </div>
    </div>
    <div style="position: relative">
        <div class="ui dimmer" ng-class="{active: dsc.loading}">
            <div class="ui loader"></div>
        </div>
        <table class="ui table bordered">
            <tr ng-repeat="val in dsc.statistic.vals">
                <td>{{val.name}}</td>
                <td class="right aligned">{{val.data}}</td>
            </tr>
            <tr ng-repeat="achieve in dsc.statistic.achieves">
                <td>{{achieve.name}}</td>
                <td class="right aligned">{{achieve.totals}} ({{(achieve.totals / dsc.statistic.totals) * 100 | number:0}}%)</td>
            </tr>
        </table>
    </div>
</div>