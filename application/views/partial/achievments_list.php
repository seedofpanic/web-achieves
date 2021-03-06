<div ng-class="{show: domain && !(domain.id > 0)}" class="protected" xmlns="http://www.w3.org/1999/html">
    <div class="ui message error">
        У вас нет доступа к данному разделу
    </div>
</div>
<div ng-controller="achievmentsController as ac" class="ui segment bottom attached" ng-show="domain.id > 0">
    <div class="ui huge breadcrumb">
        <a class="section" href="/">Домены</a>
        <i class="right chevron icon divider"></i>
        <div class="active section">{{domain.name}} - Достижения</div>
    </div>
    <div class="achievments ui segments" ng-show="ac.achievments.length>0">
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
                            <div class="description" ng-bind-html="achievment.text | sanitize"></div>
                            <div class="ui toggle checkbox active" ng-show="achievment.id>0" ng-model="achievment.active" offText="Не активно" onText="Активно" ng-change="ac.activate(achievment)"></div>
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
                        <textarea type="text" ng-model="achievment.text" ckeditor="editorOptions"></textarea>
                    </div>
                    <div class="grouped fields">
                        <label>Скрыть до выполнения:</label>
                        <div class="field">
                            <div class="ui">
                                <label>Название</label>
                                <div class="ui toggle checkbox" ng-model="achievment.title_hidden" offText="Видимо" onText="Скрыто"></div>
                            </div>
                        </div>
                        <div class="field">
                            <div class="ui">
                                <label>Картинка</label>
                                <div class="ui toggle checkbox" ng-model="achievment.image_hidden" offText="Видимо" onText="Скрыто"></div>
                            </div>
                        </div>
                        <div class="field">
                            <div class="ui">
                                <label>Название</label>
                                <div class="ui toggle checkbox" ng-model="achievment.text_hidden" offText="Видимо" onText="Скрыто"></div>
                            </div>
                        </div>
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
                                        <option value="2">Выполнить другие достижения</option>
                                        <option value="3">Таймаут</option>
                                        <option value="5">Таймаут на любой странице</option>
                                        <option value="4">Количество переходов по страницам</option>
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
                                                <tr ng-repeat="(id, rule_achiev) in rule.data2">
                                                    <td>
                                                        <select ng-model="rule.data2[id]" ng-disabled="rule.deleted>0">
                                                            <option ng-repeat="ra in ac.achievments" value="{{ra.id}}" ng-selected="rule.data2[id]==ra.id">{{ra.name}}</option>
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
                                    <div ng-switch-when="3">
                                        <div class="field" ng-class="{disabled:rule.deleted>0}">
                                            <label>Ссылка</label>
                                            <input type="text" ng-model="rule.data" ng-disabled="rule.deleted>0"/>
                                        </div>
                                        <div class="field" ng-class="{disabled:rule.deleted>0}">
                                            <label>Время</label>
                                            <div class="fields four">
                                                <div class="field one">
                                                    <label>Минуты:</label>
                                                </div>
                                                <div class="field">
                                                    <input type="text" ng-model="rule.data3.min" ng-disabled="rule.deleted>0"/>
                                                </div>
                                                <div class="field one">
                                                    <label>Секунды:</label>
                                                </div>
                                                <div class="field">
                                                    <input type="text" ng-model="rule.data3.sec" ng-disabled="rule.deleted>0"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div ng-switch-when="4">
                                        <div class="field" ng-class="{disabled:rule.deleted>0}">
                                            <label>Количество переходов</label>
                                            <input type="number" string-to-number ng-model="rule.data" ng-disabled="rule.deleted>0"/>
                                        </div>
                                    </div>
                                    <div ng-switch-when="5">
                                        <div class="field" ng-class="{disabled:rule.deleted>0}">
                                            <label>Время</label>
                                            <div class="fields four">
                                                <div class="field one">
                                                    <label>Минуты:</label>
                                                </div>
                                                <div class="field">
                                                    <input type="text" ng-model="rule.data3.min" ng-disabled="rule.deleted>0"/>
                                                </div>
                                                <div class="field one">
                                                    <label>Секунды:</label>
                                                </div>
                                                <div class="field">
                                                    <input type="text" ng-model="rule.data3.sec" ng-disabled="rule.deleted>0"/>
                                                </div>
                                            </div>
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
    </div>
    <div class="center aligned" ng-click="ac.showStarterPacks()" ng-hide="ac.achievments.length>0">
        <div class="ui button orange big">Использовать стартовый набор</div>
        <div class="ui message info">Пока нет созданных вами достижений</div>
    </div>
    <div class="ui divider"></div>
    <button class="ui button right floated primary" ng-click="ac.add()">Добавить достижение</button>
    <div class="ui button orange" ng-click="ac.showStarterPacks()" ng-show="ac.achievments.length>0">Использовать стартовый набор</div>
    <div class="clear"></div>
</div>

<div id="StarterPack" class="ui modal" ng-controller="starterPackController as spc">
    <i class="close icon"></i>
    <div class="header">
        Начните с использования готового набора достижений
    </div>
    <div class="content">
        <form class="ui form">
            <div class="field">
                <select ng-model="spc.type" ng-init="spc.type=0">
                    <option value="1" ng-selected="spc.type==0">Стандарт</option>
                </select>
            </div>
            <form class="ui form">
                <table class="ui table" ng-switch on="spc.type">
                    <tbody ng-switch-when="0" ng-init="spc.data[0] = {2: 3, 3: {min: 0, sec: 30}, 4: '/'}">
                        <tr>
                            <td>
                                Добро пожаловать!
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                Первое знакомство.
                            </td>
                            <td><input type="number" ng-model="spc.data[0][2]"/></td>
                        </tr>
                        <tr>
                            <td>
                                Внимательный читатель.
                            </td>
                            <td>
                                <div class="fields two">
                                    <div class="inline field">
                                        <label>Минут</label>
                                        <input type="number" ng-model="spc.data[0][3].min">
                                    </div>
                                    <div class="inline field">
                                        <label>Секунд</label>
                                        <input type="number" ng-model="spc.data[0][3].sec">
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Интересная страница.
                            </td>
                            <td>
                                <input type="text" ng-model="spc.data[0][4]">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Добро пожаловать!
                            </td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </form>
    </div>
    <div class="actions">
        <div class="ui black deny button">
            Отмена
        </div>
        <div class="ui positive right labeled button" ng-click="spc.add()">
            Добавить
        </div>
    </div>
</div>