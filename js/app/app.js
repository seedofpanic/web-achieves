{
    Array.prototype.remove = function (item) {
        this.splice(this.indexOf(item), 1);
    };
    var PARTIAL_URL = '/index.php/partial/';
    var AUTH_URL = '/index.php/auth/';
    var API_URL = '/index.php/api/';

    angular.module('loginModule', [])
    .directive('loginPage', function (){
        return {
            restrict: 'C',
            templateUrl: PARTIAL_URL + 'login'
        }
    })
    .controller('loginForm', ['$http', function ($http) {
        this.send = function () {
            if (this.loading) {return}
            this.loading = true;
            $http.post(AUTH_URL + 'login', $.param({identity: this.email, password: this.password, submit: 'Login'})).success(
                function () {
                    window.location = '/?action=domains';
                }
            );
        }
    }]);

    angular.module('accountModule', [])
        .factory('route', function () {
            var data = [];
            var tmp = window.location.search.slice(1).split('&');
            var search = [];
            tmp.forEach(function (item) {
                var pair = item.split('=');
                search[pair[0]] = pair[1];
            });
            data['search'] = search;
            return data;
        })
        .controller('accountController', ['route', '$scope', '$element', function (route, $scope, $element) {
            this.action = route['search']['action'] ? route['search']['action'] : 'domains';
        }])
        .directive('accountPage', function () {
            return {
                restrict: 'C',
                templateUrl: PARTIAL_URL + 'account'
            }
        })
        .directive('domainsList', function () {
            return {
                restrict: 'C',
                templateUrl: PARTIAL_URL + 'domains_list'
            }
        })
        .controller('domainsController', ['$http', '$scope', function ($http, $scope) {
            var that = this;
            that.domains = [];
            $http.get(API_URL + 'domains').success(function (domains) {
                that.domains = domains;
            });
            this.add = function () {
                var data = {};
                if (this.name) {
                    data['name'] = this.name;
                    $http.post(API_URL + 'domain/0/new', $.param(data)).success(function (domain) {
                        if (domain['id'] > 0) {
                            that.domains.push(domain);
                        }
                    });
                }
            };
            this.update = function (domain) {
                $http.post(API_URL + 'domain/update', $.param(domain)).success(function (domain) {})
            };
            this.remove = function (domain) {
                if (domain.loading) {return}
                var deleted = domain.deleted > 0 ? '0' : '1';
                if (domain.deleted === '0' && !confirm('Действительно хотите удалить?')) {return;}
                domain.loading = true;
                $http.post(API_URL + 'domain/' + domain.id + '/delete', $.param({deleted: deleted})).success(function () {
                    domain.deleted = deleted;
                    domain.loading = false;
                });
            }
        }])
        .directive('achivmentsList', function () {
            return {
                restrict: 'C',
                templateUrl: PARTIAL_URL + 'achivments_list'
            }
        })
        .controller('achivmentsController', ['$http', 'route', function ($http, route) {
            var that = this;
            that.achivments = [];
            $http.get(API_URL + 'achivments/' + route['search']['domain_id']).success(function (achivments) {
                that.achivments = achivments;
            });
            this.add = function () {
                that.achivments.push({name: 'Новое достижение', edit: true, domain_id: route['search']['domain_id']})
            };
            this.save = function (achivment) {
                if (achivment.loading) {return}
                achivment.loading = true;
                var id = achivment.id ? achivment.id : 0;
                $http.post(API_URL + 'achivment/' + id  + '/save', $.param(achivment)).success(function (){
                    achivment.edit = false;
                    achivment.loading = false;
                });
            };
            this.remove = function (achivment) {
                if (achivment.loading) {return}
                var deleted = achivment.deleted > 0 ? '0' : '1';
                if (achivment.deleted === '0' && !confirm('Действительно хотите удалить?')) {return;}
                achivment.loading = true;
                $http.post(API_URL + 'achivment/' + achivment.id + '/delete', $.param({deleted: deleted})).success(function () {
                    achivment.deleted = deleted;
                    achivment.loading = false;
                });
            }
        }])
        .controller('RulesController', ['$http', function ($http) {
            this.add = function (achivment) {
                if (!achivment.rules) {
                    achivment.rules = [];
                }
                achivment.rules.push({});
            }
        }]);

    angular.module('achivesApp', ['loginModule', 'accountModule'])
        .run(function ($http){
            $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";
        });


}