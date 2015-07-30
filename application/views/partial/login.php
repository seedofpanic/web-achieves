<div class="ui middle aligned center aligned grid">
    <div class="column">
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
                        <input ng-model="lf.email" type="text" name="email" placeholder="E-mail address">
                    </div>
                </div>
                <div class="field">
                    <div class="ui left icon input">
                        <i class="lock icon"></i>
                        <input ng-model="lf.password" type="password" name="password" placeholder="Password">
                    </div>
                </div>
                <button type="submit" class="ui fluid large teal submit button" ng-class="{'loading':lf.loading}" ng-click="lf.send()">Login</button>
            </div>

            <div class="ui error message"></div>

        </form>

        <div class="ui message">
            New to us? <a href="#">Sign Up</a>
        </div>
    </div>
</div>