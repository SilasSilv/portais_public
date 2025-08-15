var app = angular.module("PortalRefeicao", ['ui.router', 'ngStorage', 'angular-growl', 'ae-datetimepicker', 'ui.mask', 'chart.js', 'cfp.loadingBar'])
        
.config(function ($stateProvider, $urlRouterProvider, growlProvider) {

    growlProvider.globalTimeToLive(3000);
    growlProvider.onlyUniqueMessages(false);

        $stateProvider
            .state('login', {
                url: '/',
                templateUrl: 'app/views/homePrincipal.html',
                params: {'error': ''}
            })   
            .state('relatorio', {
                url: '/relatorio',
                templateUrl: 'app/views/report/relatorioRefeicaoLivre.html'
            })
            // ROTAS DO ADMINISTRADOR            
            .state('appAdmin', {
                url: '/administrador',
                abstract: true,
                templateUrl: 'app/views/templates/sidebarAdmin.html',
                onEnter: function($rootScope, $state, $timeout, $sessionStorage, $location, cfpLoadingBar, Login, Util) {
                    cfpLoadingBar.start();

                    if (typeof $sessionStorage.session == "object" || $location.search().hasOwnProperty('token')) {
                        var queryParams = {
                            cd_permissao: 7,
                            token: $location.search().token
                        }
                        $location.search('token', null);
                        
                        Login.me(queryParams)
                            .then(function(resp) {
                               
                                if (resp.session.cd_sistema != 4) {
                                    cfpLoadingBar.complete();
                                    $sessionStorage.$reset();
                                    
                                    $timeout(function() {
                                        return $state.go('login', {error: "Acesso negado!"});
                                    }, 500);
                                } else {
                                    $rootScope.$broadcast("authentication", {auth: true});
                                }
                            })
                            .catch(function(err) {
                                Util.treatError(err, 'no');
                            });
                    } else {
                        cfpLoadingBar.complete();

                        $timeout(function() {
                            return $state.go('login', {error: "Acesso negado!"});
                        }, 500);
                    }
                } 
            })
            .state('appAdmin.sugestao', {
                url: '/sugestao',
                templateUrl: 'app/views/admin/sugestao.html'
            })                                             
            .state('appAdmin.cadRefeicao', {
                url: '/cadRefeicao',
                templateUrl: 'app/views/admin/cadRefeicao.html'
            }) 
            .state('appAdmin.preco', {
                url: '/refeicao/preco',
                templateUrl: 'app/views/admin/preco.html'
            }) 
            .state('appAdmin.relatorioCatraca', {
                url: '/relatorio/catraca',
                templateUrl: 'app/views/report/relatorioCatraca.html'
            })
            .state('appAdmin.relatorioRH', {
                url: '/relatorio/rh',
                templateUrl: 'app/views/report/relatorioRH.html'
            })
            .state('appAdmin.relatorioRefeicao', {
                url: '/relatorio/refeicao',
                templateUrl: 'app/views/report/relatorioRefeicao.html'
            })
            .state('appAdmin.relatorioAvaliacao', {
                url: '/relatorio/avaliacao/qualidade',
                templateUrl: 'app/views/report/relatorioQualidade.html'
            })
            .state('appAdmin.relatorioDobra', {
                url: '/relatorio/dobra-terceiro',
                templateUrl: 'app/views/report/relatorioDobra.html'
            })

            //ROTAS DO USUÁRIO
            .state('appUser', {
                url: '/solicitante',
                abstract: true,
                templateUrl: 'app/views/templates/sidebarUser.html',
                onEnter: function($rootScope, $state, $q, $timeout, $sessionStorage, $location, cfpLoadingBar, Login, Category,  Util) {
                    cfpLoadingBar.start();
                    
                    if (typeof $sessionStorage.session == "object" || $location.search().hasOwnProperty('token')) {
                        var queryParams = angular.copy($location.search());
                        $location.search('token', null);
                                        
                        Login.me(queryParams)
                            .then(function(resp) {
                                if (resp.session.cd_sistema != 4) {
                                    cfpLoadingBar.complete();
                                    $sessionStorage.$reset();
                                    
                                    $timeout(function() {
                                        return $state.go('login', {error: "Acesso negado!"});
                                    }, 500);
                                } else {
                                    var tipo_opiniao = Category.read({cd_tipo_categoria: 6}).then(function(resp) {$sessionStorage.tipo_opiniao = resp;}),
                                        tipo_qualidade = Category.read({cd_tipo_categoria: 7}).then(function(resp) {$sessionStorage.tipo_qualidade = resp;});

                                        $q.all([tipo_opiniao, tipo_qualidade])
                                            .then(function() {
                                                $rootScope.$broadcast("authentication", {auth: true});
                                            })
                                            .catch(function(err) {                                
                                                Util.treatError(err, 'no');      
                                            }); 
                                }
                            })
                            .catch(function(err) {
                                console.log(err);
                                console.log('teste');
                                Util.treatError(err, 'no');
                            });

                    } else {
                        cfpLoadingBar.complete();

                        $timeout(function() {
                            return $state.go('login', {error: "Acesso negado!"});
                        }, 500);
                    }                           
                } 
            })                    
            .state('appUser.home', {
                url: '/home',
                templateUrl: 'app/views/usu/homeUsu.html'
            })
            .state('appUser.relatorioPessoal', {
                url: '/relatorio/pessoal',
                templateUrl: 'app/views/report/relatorioPessoal.html'
            })
            .state('appUser.relatorioCatraca', {
                url: '/relatorio/catraca',
                templateUrl: 'app/views/report/relatorioCatraca.html'
            })
            .state('appUser.relatorioRH', {
                url: '/relatorio/rh',
                templateUrl: 'app/views/report/relatorioRH.html'
            });
            
    $urlRouterProvider.otherwise('/');

    Chart.defaults.global.colors = [ '#5cb85c', '#337ab7', '#f0ad4e', '#d9534f'];
})

.run(function($rootScope) { 
    $rootScope.$on('notificacaoSugestao', function(event, args) {
        $rootScope.$broadcast('sugestaoBroadcast', args);
    });
})

.provider('Url', function() {

    return {
        $get: function() {
            var urlBase = "http://localhost:8888/v1/"
            return {
                "login": urlBase + "login",
                "logout": urlBase +"logout",
                "meal": urlBase + "meal",
                "menu": urlBase + "meal/menu",
                "price": urlBase + "meal/price",
                "people": urlBase + "people",
                "category": urlBase + "category",
                "sector": urlBase + "sector",
                "opinion": urlBase + "meal/opinion",
                "quality": urlBase + "meal/quality",
                "turnstile": urlBase + "turnstile",
                "thirdFolds": urlBase + "meal/thirdFolds",
                "menuRequest": urlBase + "meal/menu/request/",
                "analyzePublicSynthetic": urlBase + "meal/analyze/public/synthetic",
                "analyzePublicAnalytical": urlBase + "meal/analyze/public/analytical",
                "analyzePrivate": urlBase + "meal/analyze/private",
                "analyzePayroll": urlBase + "meal/analyze/payroll",
            };
        }
    };
    
});