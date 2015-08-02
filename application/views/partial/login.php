<div class="ui middle aligned center aligned grid">
    <div class="column">
        <div ng-hide="register">
            <h2 class="ui teal image header">
                <div class="content">
                    Войдите в свою учетную запись
                </div>
            </h2>
            <form class="ui large form" ng-controller="loginForm as lf">
                <div class="ui stacked segment">
                    <div class="field">
                        <div class="ui left icon input">
                            <i class="user icon"></i>
                            <input ng-model="lf.email" type="text" name="email" placeholder="E-mail">
                        </div>
                    </div>
                    <div class="field">
                        <div class="ui left icon input">
                            <i class="lock icon"></i>
                            <input ng-model="lf.password" type="password" name="password" placeholder="Пароль">
                        </div>
                    </div>
                    <button type="submit" class="ui fluid large teal submit button" ng-class="{'loading':lf.loading}" ng-click="lf.send()">Вход</button>
                </div>

                <div class="ui error message"></div>

            </form>

            <div class="ui message">
                Первый раз здесь? <a href="#" ng-click="register=true">Зарегистрироваться</a>
            </div>
        </div>
        <div ng-show="register">
            <h2 class="ui teal image header">
                <div class="content">
                    Зарегистрируйтесь
                </div>
            </h2>
            <form class="ui large form" ng-controller="registerForm as rf">
                <div class="ui stacked segment">
                    <div class="field">
                        <input ng-model="rf.first_name" type="text" name="first_name" placeholder="Имя">
                    </div>
                    <div class="field">
                        <input ng-model="rf.last_name" type="text" name="last_name" placeholder="Фамилия">
                    </div>
                    <div class="field">
                        <div class="ui left icon input">
                            <i class="user icon"></i>
                            <input ng-model="rf.email" type="text" name="email" placeholder="E-mail">
                        </div>
                    </div>
                    <div class="field">
                        <div class="ui left icon input">
                            <i class="lock icon"></i>
                            <input ng-model="rf.password" type="password" name="password" placeholder="Пароль">
                        </div>
                    </div>
                    <div class="field">
                        <div class="ui left icon input">
                            <i class="lock icon"></i>
                            <input ng-model="rf.password_confirm" type="password" name="password_confirm" placeholder="Подтвердить пароль">
                        </div>
                    </div>
                    <button type="submit" class="ui fluid large teal submit button" ng-class="{'loading':rf.loading}" ng-click="rf.send()">Зарегистрироваться</button>
                </div>

                <div class="ui error message"></div>

            </form>

            <div class="ui message">
                Уже есть аккаунт? <a href="#" ng-click="register=false">Войти</a>
            </div>
        </div>
    </div>
</div>