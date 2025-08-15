var app = angular.module("Kernel", ['ui.router', 'ngStorage', 'angular-growl', 'ae-datetimepicker', 'angularFileUpload', 'cfp.loadingBar'])
        
.config(function ($stateProvider, $urlRouterProvider, growlProvider) {
    growlProvider.globalTimeToLive(3000);
    growlProvider.onlyUniqueMessages(false);

	$stateProvider
		.state('login', {
			url: '/',
			templateUrl: 'app/views/login.html',
			params: {'error': ''}
		})           
		.state('app', {
			url: '/kernel',
			abstract: true,
			templateUrl: 'app/views/template.html',
			onEnter: function($rootScope, $state, $timeout, $sessionStorage, $location, cfpLoadingBar, Login, Category, Util) {
				cfpLoadingBar.start();

				if (typeof $sessionStorage.session == "object" || $location.search().hasOwnProperty('token')) {
					var queryParams = angular.copy($location.search());
					$location.search('token', null);
						
					Login.me(queryParams)
						.then(function(resp) {
							if (resp.session.cd_sistema != 6) {
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
		.state('app.home', {
			url: '/home',
			templateUrl: 'app/views/home.html'
		})
		.state('app.sistema', {
			url: '/sistema',
			templateUrl: 'app/views/sistema.html'
		})
		.state('app.categoria', {
			url: '/categoria',
			templateUrl: 'app/views/categoria.html'
		})
		.state('app.permissaoGerenciar', {
			url: '/permissao/gerenciar',
			templateUrl: 'app/views/permissao/gerenciar.html'
		})
		.state('app.permissaoConceder', {
			url: '/permissao/conceder',
			templateUrl: 'app/views/permissao/conceder.html'
		})
		.state('app.sessao', {
			url: '/sessao',
			templateUrl: 'app/views/sessao.html'
		});
            
    $urlRouterProvider.otherwise('/');
})

.provider('Url', function() {

    return {
        $get: function() {
			var urlBase = "http://localhost:8888/v1/";
            return {
                "login": urlBase + "login",
                "logout": urlBase + "logout",
				"people": urlBase + "people",
				"session": urlBase + "session",
				"system": urlBase + "system",
				"sector": urlBase + "sector",
				"category": urlBase + "category",
				"categoryType": urlBase + "category/type/",
				"permission": urlBase + "permission",
				"permissionSystem": urlBase + "permission/system"
            };
        }
    };
    
});