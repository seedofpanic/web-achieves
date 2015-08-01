<div ng-controller="domainsController as dc" class="ui segment bottom attached">
    <div class="ui huge breadcrumb">
        <div class="active section">Домены</div>
    </div>
    <div class="messages"></div>
    <table class="ui table">
        <tr class="domains ui link list" ng-repeat="domain in dc.domains">
            <td><a class="domain item" href="?action=achievments&domain_id={{domain.id}}">{{domain.name}}</a></td>
            <td class="collapsing">
                <a type="button" href="?action=achievments&domain_id={{domain.id}}" class="ui button orange icon" title="Редактировать достижения"><i class="icon edit"></i></a>
            </td>
            <td class="collapsing">
                <a class="ui button blue icon" href="?action=domain&domain_id={{domain.id}}" title="Получть скрипт"><i class="icon options"></i></a>
            </td>
            <td class="collapsing">
                <button class="ui button red icon" ng-class="{'loading': domain.loading}" ng-hide="domain.deleted>0" ng-click="dc.remove(domain)" title="Удалить"><i class="icon delete"></i></button>
                <button class="ui button green icon" ng-class="{'loading': domain.loading}" ng-show="domain.deleted>0" ng-click="dc.remove(domain)" title="Вернуть"><i class="icon refresh"></i></button>
            </td>
        </tr>
    </table>
    <form class="ui form">
        <div class="ui right action input">
            <input type="text" ng-model="dc.name"/>
            <button type="submit" class="ui button right attached floated primary labeled icon" ng-click="dc.add()">
                <i class="privacy icon"></i>Добавить домен</button>
        </div>
    </form>
    <div class="clear"></div>
</div>