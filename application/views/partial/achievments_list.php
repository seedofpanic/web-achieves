<div ng-controller="achievmentsController as ac" class="ui segment bottom attached">
    <div class="ui huge breadcrumb">
        <a class="section" href="/">Домены</a>
        <i class="right chevron icon divider"></i>
        <div class="active section">Достижения</div>
    </div>
    <div class="achievments ui segments">
        <div class="achieve ui segment" ng-repeat="achievment in ac.achievments">
            <div class="info" ng-hide="achievment.edit">
                <button class="ui button right floated icon red" ng-click="ac.remove(achievment)" ng-hide="achievment.deleted>0" title="Удалить"><i class="icon delete"></i></button>
                <button class="ui button right floated icon green" ng-class="{'loading': achievment.loading}" ng-click="ac.remove(achievment)" ng-show="achievment.deleted>0" title="Вернуть"><i class="icon refresh"></i></button>
                <button class="ui button right floated icon blue" ng-class="{'loading': achievment.loading}" ng-click="ac.edit(achievment)" ng-hide="achievment.deleted>0" title="Редактировать"><i class="icon edit"></i></button>
                <h2>{{achievment.name}}</h2>
                <div class="ui items segment">
                    <div class="item">
                        <div class="image"><img ng-src="{{achievment.image}}"></div>
                        <div class="content">
                            <div class="header">{{achievment.title}}</div>
                            <div class="description">{{achievment.text}}</div>
                            <div class="ui toggle checkbox active" ng-show="achievment.id>0" ng-model="achievment.active" offText="Не активно" onText="Активно"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="edit" ng-show="achievment.edit">
                <form class="ui form">
                    <div class="field">
                        <label>Название</label>
                        <input type="text" ng-model="achievment.name"/>
                    </div>
                    <div class="field">
                        <label>Название которое увидет пользователь</label>
                        <input type="text" ng-model="achievment.title"/>
                    </div>
                    <div class="field">
                        <label>Url картинки</label>
                        <input type="text" ng-model="achievment.image"/>
                    </div>
                    <div class="field">
                        <label>Текст</label>
                        <textarea type="text" ng-model="achievment.text"/>
                    </div>
                </form>
                    <h4 class="ui dividing header">Условия</h4>
                    <div class="ui segments" ng-controller="RulesController as rc">
                        <div class="ui segment" ng-repeat="rule in achievment.rules">
                            <form class="ui form">
                                <button type="button" class="ui button red icon right floated mini" ng-click="rule.deleted=1" ng-hide="rule.deleted>0"><i class="icon remove"></i></button>
                                <button type="button" class="ui button green icon right floated mini" ng-click="rule.deleted=0" ng-show="rule.deleted>0"><i class="icon refresh"></i></button>
                                <div class="field" ng-class="{disabled:rule.deleted>0}">
                                    <label>Тип</label>
                                    <select ng-model="rule.type" ng-disabled="rule.deleted>0">
                                        <option value="1">Заход на страницу</option>
                                        <option value="2">Выполнить другие достижения (В разработке)</option>
                                    </select>
                                </div>
                                <div ng-switch on="rule.type">
                                    <div ng-switch-when="1">
                                        <div class="field" ng-class="{disabled:rule.deleted>0}">
                                            <label>Ссылка</label>
                                            <input type="text" ng-model="rule.data" ng-disabled="rule.deleted>0"/>
                                        </div>
                                    </div>
                                    <div ng-switch-when="2">
                                        <div class="field" ng-class="{disabled:rule.deleted>0}">
                                            <label>Достижение</label>
                                            <table class="ui table">
                                                <tr ng-repeat="rule_achiev in rule.data2">
                                                    <td>
                                                        <select ng-model="rule_achiev" ng-disabled="rule.deleted>0">
                                                            <option ng-repeat="ra in ac.achievments" value="{{ra.id}}">{{ra.name}}</option>
                                                        </select>
                                                    </td>
                                                    <td class="collapsing">
                                                        <button class="ui button icon red" ng-click="rule.data2.remove(ra)"><i class="icon close"></i></button>
                                                    </td>
                                                </tr>
                                                <tr ng-hide="rule.data2.length>0">
                                                    <td><div class="ui message info">Добавьте достижения, которые надо выполнить для получения данного</div></td>
                                                </tr>
                                            </table>
                                            <button class="ui button right floated" ng-click="rc.addData2(rule)">Добавить</button>
                                            <div class="clear"></div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="ui segment" ng-hide="achievment.rules.length>0">
                            <div class="ui message error">Не забудьте добавить условия получения данного достижения!</div>
                        </div>
                        <div class="ui segment">
                            <button type="button" class="ui button icon labeled blue right floated" ng-click="rc.add(achievment)"><i class="icon plus"></i>Добавить условие</button>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <button type="button" class="ui button right floated red" ng-click="ac.revert(achievment)" ng-class="{'loading': achievment.loading}">Отмена</button>
                    <button type="button" class="ui button right floated green" ng-click="ac.save(achievment)" ng-class="{'loading': achievment.loading}">Сохранить</button>
                    <div class="clear"></div>
            </div>
        </div>
        <div class="ui segment" ng-hide="ac.achievments.length>0">
            <div class="ui message info">Пока нет созданных вами достижений</div>
        </div>
    </div>
    <button class="ui button right floated primary" ng-click="ac.add()">Добавить достижение</button>
    <div class="clear"></div>
</div>