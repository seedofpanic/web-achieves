<div class="ui huge breadcrumb">
    <div class="active section">Домены</div>
</div>
<div ng-controller="domainsController as dc" class="ui segment bottom attached">
    <table class="ui table">
        <tr class="domains ui link list" ng-repeat="domain in dc.domains">
            <td><a class="domain item" href="?action=achivments&domain_id={{domain.id}}">{{domain.name}}</a></td>
            <td class="collapsing">
                <button class="ui button floated red icon" ng-class="{'loading': domain.loading}" ng-hide="domain.deleted>0" ng-click="dc.remove(domain)"><i class="icon delete"></i></button>
                <button class="ui button floated green icon" ng-class="{'loading': domain.loading}" ng-show="domain.deleted>0" ng-click="dc.remove(domain)"><i class="icon refresh"></i></button>
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