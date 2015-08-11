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