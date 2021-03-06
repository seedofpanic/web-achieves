{
    $.copy = function(target, source) {
        if (typeof target != 'object') {
            target = {};
        }
        if (typeof source != 'object') {
            return;
        }
        var rex = new RegExp('^\\$');
        $.each(source, function (key, val) {
            if (rex.test(key)) {return}
            if (typeof val == 'object' && !val.forEach) {
                $.copy(target[key], source[key]);
            } else {
                console.log(key);
                target[key] = val;
            }
        });
    };
    Array.prototype.remove = function (item) {
        this.splice(this.indexOf(item), 1);
    };
    var PARTIAL_URL = '/index.php/partial/';
    var AUTH_URL = '/index.php/auth/';
    var API_URL = '/index.php/api/';

    angular.module('tools', [])
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
        .filter("sanitize", ['$sce', function($sce) {
            return function(htmlCode){
                return $sce.trustAsHtml(htmlCode);
            }
        }])
        .run(function($rootScope) {
            $rootScope.typeOf = function(value) {
                return typeof value;
            };
        })
        .directive('stringToNumber', function() {
            return {
                require: 'ngModel',
                link: function(scope, element, attrs, ngModel) {
                    ngModel.$parsers.push(function(value) {
                        return '' + value;
                    });
                    ngModel.$formatters.push(function(value) {
                        return parseFloat(value, 10);
                    });
                }
            };
        });

    angular.module('loginModule', ['tools'])
    .directive('loginPage', ['route', function (route){
        return {
            restrict: 'C',
            templateUrl: PARTIAL_URL + 'login',
            link: function (scope) {
                if (route.search.action == 'register') {
                    scope.register = true;
                }
            }
        }
    }])
    .controller('loginForm', ['$http', '$rootScope', function ($http, $rootScope) {
            var that = this;
        this.send = function () {
            if (this.loading) {return}
            this.loading = true;
            $http.post(AUTH_URL + 'login', $.param({identity: this.email, password: this.password, submit: 'Login'})).success(
                function (user) {
                    $rootScope.user = user;
                    that.loading = false;
                    var config = $rootScope.failed_request.config;
                    if (config.url == "/index.php/api/domains") {
                        window.location.reload();
                    }
                    /*var defer = $rootScope.failed_request.defer;
                    console.log($rootScope.failed_request);
                    $http(config).success(function (data) {
                        defer.resolve(data);
                    });*/
                }
            ).error(function (data) {
                    that.errors = [{text: data.message}];
                    that.loading = false;
                });
        }
    }])
    .controller('registerForm', ['$http', function ($http) {
        this.send = function () {
            if (this.loading) {return}
            this.loading = true;
            $http.post(AUTH_URL + 'create_user', $.param(
                {
                    first_name: this.first_name,
                    last_name: this.last_name,
                    email: this.email,
                    password: this.password,
                    password_confirm: this.password_confirm,
                    submit: 'Register',
                    force_login: true
                }
            )).success(
                function () {
                    window.location = '/?action=domains';
                }
            );
        }
    }]);

    angular.module('privatePageModule', [], function ($httpProvider) {
        $httpProvider.interceptors.push(function($q, $rootScope) {
            return {
                'responseError': function(response, r) {
                    if (response.status == 401) {
                        $rootScope.user = undefined;
                        $rootScope.failed_request = response;
                        $rootScope.failed_request.defer = $q.defer();
                    }
                    return $q.reject(response);
                }
            };
        });
    })
        .run(['$http', '$rootScope', function ($http, $rootScope) {
            $rootScope.loading = true;
            $http.get(API_URL + 'me').success(function (user){
                $rootScope.user = user;
            }).error(function () {
                $rootScope.loading = false;
            });
        }]);

    angular.module('accountModule', ['tools', 'ngCkeditor'])
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
        .directive('messages', function () {
            return {
                restrict: 'C',
                template: '<div ng-repeat="error in messages"><div class="ui message" ng-class="{error: error.type==\'error\',info: error.type==\'info\',success: error.type==\'success\'}">{{error.msg}}</div></div>'
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
                if (!this.name) {
                    $scope.messages = [
                        {
                            msg: 'Введите имя домена, например: google.com',
                            type: 'error'
                        }
                    ];
                    return;
                }
                data['name'] = this.name;
                $http.post(API_URL + 'domain/0/new', $.param(data)).success(function (domain) {
                    if (domain['id'] > 0) {
                        that.domains.push(domain);
                    }
                    $scope.messages = [
                        {
                            msg: 'Домен успешно добавлен',
                            type: 'success'
                        }
                    ];
                }).error(function (msg) {
                    $scope.messages = [
                        {
                            msg: msg,
                            type: 'error'
                        }
                    ];
                });
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
        .directive('achievmentsList', ['route', '$http', function (route, $http) {
            return {
                restrict: 'C',
                templateUrl: PARTIAL_URL + 'achievments_list',
                link: function (scope) {
                    $http.get(API_URL + 'domain/' + route.search.domain_id + '/get').success(function (domain) {
                        scope.domain = domain;
                    });
                }
            }
        }])
        .controller('starterPackController', ['$http', 'Achievments', 'route', function ($http, Achievments, route) {
            var that = this;
            var domain_id = route['search']['domain_id'];
            this.add = function () {
                $http.post(API_URL + 'domain/' + domain_id + '/starter_pack', $.param({type: that.type, data: that.data[that.type]})).success(function (achievments) {
                    Achievments.set(achievments);
                });
            };
        }])
        .factory('Achievments', function () {
            var achieves = [];
            var prepareAchiev = function (achievment) {
                achievment.active = achievment.active > 0;
                achievment.title_hidden = achievment.title_hidden > 0;
                achievment.image_hidden = achievment.image_hidden > 0;
                achievment.text_hidden = achievment.text_hidden > 0;
                achievment.rules.forEach(function (rule) {
                    if (rule.type == 2) {
                        rule.data2 = JSON.parse(rule.data);
                        rule.data = '';
                    }
                    if (rule.type == 3) {
                        var rdata = rule.data.split('::');
                        rule.data3 = JSON.parse(rdata[1]);
                        rule.data = rdata[0];
                    }
                    if (rule.type == 5) {
                        rule.data3 = JSON.parse(rule.data);
                    }
                });
            };
            var set = function (achievments) {
                achievments.forEach(function (achievment) {
                    achieves.push(achievment);
                    prepareAchiev(achievment);
                });
            };
            return {
                set: set,
                achieves: achieves
            }
        })
        .controller('achievmentsController', ['$http', 'route', '$scope', 'Achievments', function ($http, route, $scope, Achievments) {
            var that = this;
            var edit_cache = [];
            that.achievments = Achievments.achieves;
            $scope.editorOptions = {
                language: 'ru',
                height: '100px'
            };

            $http.get(API_URL + 'achievments/' + route['search']['domain_id']).success(function (achievments) {
                Achievments.set(achievments);
            });
            this.showStarterPacks = function () {
                $('#StarterPack').modal('show');
            };
            this.activate = function (achievment) {
                $http.post(API_URL + 'achievment/' + achievment.id + '/activate', $.param({activate: achievment.active}));
            };
            this.add = function () {
                var achievment = {active: false, name: 'Новое достижение', edit: true, domain_id: route['search']['domain_id']};
                that.achievments.push(achievment);
            };
            this.save = function (achievment) {
                if (achievment.loading) {return}
                achievment.loading = true;
                var id = achievment.id ? achievment.id : 0;
                $http.post(API_URL + 'achievment/' + id  + '/save', $.param(achievment)).success(function (achievement){
                    achievment.id = achievement.id;
                    achievment.edit = false;
                    achievment.loading = false;
                }).error(function () {
                    achievment.loading = false;
                });
                if (achievment.rules) {
                    $.each(achievment.rules, function (key, item) {
                        if (item.deleted > 0) {
                            achievment.rules.remove(item);
                        }
                    });
                }
            };
            this.revert = function (achievment) {
                if (achievment.id > 0) {
                    $.copy(achievment, edit_cache[achievment.id]);
                    achievment.edit = 0
                } else {
                    that.achievments.remove(achievment);
                }
            };
            this.edit = function (achievment) {
                edit_cache[achievment.id] = {};
                edit_cache[achievment.id] = $.extend(true, {}, achievment);
                achievment.edit = 1;
            };
            this.remove = function (achievment) {
                if (achievment.loading) {return}
                var deleted = achievment.deleted > 0 ? '0' : '1';
                if (deleted === '1' && !confirm('Действительно хотите удалить?')) {return;}
                achievment.loading = true;
                $http.post(API_URL + 'achievment/' + achievment.id + '/delete', $.param({deleted: deleted})).success(function () {
                    achievment.deleted = deleted;
                    achievment.loading = false;
                });
            }
        }])
        .controller('RulesController', ['$http', function ($http) {
            this.add = function (achievment) {
                if (!achievment.rules) {
                    achievment.rules = [];
                }
                achievment.rules.push({});
            };
            this.addData2 = function (rule) {
                if (!rule.data2) {
                    rule.data2 = [];
                }
                rule.data2.push({});
            };
        }])
        .directive('domainConfig', function () {
            return {
                restrict: 'C',
                templateUrl: PARTIAL_URL + 'domain_config'
            }
        })
        .controller('CodeInjectorController', ['route', function (route) {
            this.domain_id = route['search']['domain_id'];
        }])
        .directive('domainStatistics', function () {
            return {
                restrict: 'C',
                templateUrl: PARTIAL_URL + 'domain_statistics'
            }
        })
        .controller('domainStatisticController', ['$http', 'route', function ($http, route) {
            var that = this;
            this.domain_id = route['search']['domain_id'];
            this.get = function (force) {
                var params = {};
                if (!force) {
                    params.start_date = that.statistic.start_date;
                    params.end_date = that.statistic.end_date;
                    if (!(params.start_date && params.end_date)) {
                        return
                    }
                }
                that.loading = true;
                $http.get(API_URL + 'domain/' + that.domain_id + '/statistics', {params: params}).success(function (statistic) {
                    that.statistic = statistic;
                    that.loading = false;
                }).error(function () {
                    that.loading = false;
                });
            };
            this.get(true);
        }]);

    angular.module('UITools', [])
    .directive('datetimepicker', function(){
        return {
            restrict: 'C',
            link: function (scope, element, attr) {
                $(element).datetimepicker();
            }
        }
    })
    .directive('checkbox', function () {
        return {
            scope: {},
            require:"ngModel",
            restrict: 'C',
            template: '<input type="checkbox" class="hidden"/><label ng-click="switch()">{{text}}<span ng-hide="on">{{offText}}</span><span ng-show="on">{{onText}}</span></label>',
            link: function (scope, element, attrs, ngModel) {
                var input = element.find('input');
                scope.text = attrs.text;
                scope.offText = attrs.offtext;
                scope.onText = attrs.ontext;
                scope.switch = function () {
                    ngModel.$setViewValue(!ngModel.$modelValue);
                };
                scope.$watch(function(){
                    return ngModel.$modelValue;
                }, function(modelValue){
                    scope.on = modelValue;
                    element.toggleClass('checked', modelValue);
                    input[0].checked = modelValue;
                });
            }
        }
    });

    angular.module('achievesApp', ['loginModule', 'accountModule', 'privatePageModule', 'UITools'])
        .run(function ($http){
            $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";
        });


}