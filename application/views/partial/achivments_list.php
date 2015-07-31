<div ng-controller="achivmentsController as ac" class="ui segment bottom attached">
    <div class="ui huge breadcrumb">
        <a class="section" href="/">Домены</a>
        <i class="right chevron icon divider"></i>
        <div class="active section">Достижения</div>
    </div>
    <div class="achivments ui segments">
        <div class="avivment ui segment" ng-repeat="achivment in ac.achivments">
            <div class="info" ng-hide="achivment.edit">
                <button class="ui button right floated icon red" ng-click="ac.remove(achivment)" ng-hide="achivment.deleted>0"><i class="icon delete"></i></button>
                <button class="ui button right floated icon green" ng-class="{'loading': achivment.loading}" ng-click="ac.remove(achivment)" ng-show="achivment.deleted>0"><i class="icon refresh"></i></button>
                <button class="ui button right floated icon blue" ng-class="{'loading': achivment.loading}" ng-click="achivment.edit=true" ng-hide="achivment.deleted>0"><i class="icon edit"></i></button>
                <h2>{{achivment.name}}</h2>
                <div class="ui items segment">
                    <div class="item">
                        <div class="image"><img ng-src="{{achivment.image}}"></div>
                        <div class="content">
                            <div class="header">{{achivment.title}}</div>
                            <div class="description">{{achivment.text}}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="edit" ng-show="achivment.edit">
                <form class="ui form">
                    <div class="field">
                        <label>Название</label>
                        <input type="text" ng-model="achivment.name"/>
                    </div>
                    <div class="field">
                        <label>Название которое увидет пользователь</label>
                        <input type="text" ng-model="achivment.title"/>
                    </div>
                    <div class="field">
                        <label>Url картинки</label>
                        <input type="text" ng-model="achivment.image"/>
                    </div>
                    <div class="field">
                        <label>Текст</label>
                        <textarea type="text" ng-model="achivment.text"/>
                    </div>
                </form>
                    <h4 class="ui dividing header">Условия</h4>
                    <div class="ui segments" ng-controller="RulesController as rc">
                        <div class="ui segment" ng-repeat="rule in achivment.rules">
                            <form class="ui form">
                                <button type="button" class="ui button red icon right floated mini" ng-click="rule.deleted=1" ng-hide="rule.deleted>0"><i class="icon remove"></i></button>
                                <button type="button" class="ui button green icon right floated mini" ng-click="rule.deleted=0" ng-show="rule.deleted>0"><i class="icon refresh"></i></button>
                                <div class="field" ng-class="{disabled:rule.deleted>0}">
                                    <label>Тип</label>
                                    <select ng-model="rule.type" ng-disabled="rule.deleted>0">
                                        <option value="1">Заход на страницу</option>
                                    </select>
                                </div>
                                <div ng-switch on="rule.type">
                                    <div ng-switch-when="1">
                                        <div class="field" ng-class="{disabled:rule.deleted>0}">
                                            <label>Ссылка</label>
                                            <input type="text" ng-model="rule.data" ng-disabled="rule.deleted>0"/>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="ui segment" ng-hide="achivment.rules.length>0">
                            <div class="ui message error">Не забудьте добавить условия получения данного достижения!</div>
                        </div>
                        <div class="ui segment">
                            <button type="button" class="ui button icon labeled green right floated" ng-click="rc.add(achivment)"><i class="icon plus"></i>Добавить условие</button>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <button type="submit" class="ui button right floated green" ng-click="ac.save(achivment)" ng-class="{'loading': achivment.loading}">Сохранить</button>
                    <div class="clear"></div>
            </div>
        </div>
        <div class="ui segment" ng-hide="ac.achivments.length>0">
            <div class="ui message info">Пока нет созданных вами достижений</div>
        </div>
    </div>
    <button class="ui button right floated primary" ng-click="ac.add()">Добавить достижение</button>
    <div class="clear"></div>
</div>