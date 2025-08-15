// criação do modulo
var app = angular.module("GestaoPessoas", ['ui.router', 'ngStorage', 'angular-growl', 'ae-datetimepicker', 'ngImgCrop', 'angularFileUpload', 'cfp.loadingBar'])
        
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
			url: '/gestao-pessoas',
			abstract: true,
			templateUrl: 'app/views/template.html',
			onEnter: function($rootScope, $state, $timeout, $sessionStorage, $location, cfpLoadingBar, Login, Category, Util) {
				cfpLoadingBar.start();

				if (typeof $sessionStorage.session == "object" || $location.search().hasOwnProperty('token')) {
					var queryParams = angular.copy($location.search());
					$location.search('token', null);

					Login.me(queryParams)
						.then(function(resp) {
							if (resp.session.cd_sistema != 5) {
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
		.state('app.funcionarioCadastrar', {
			url: '/funcionario/cadastrar',
			templateUrl: 'app/views/funcionario/gerenciar.html'
		})
		.state('app.funcionarioEditar', {
			url: '/funcionario/editar/:nr_cracha',
			templateUrl: 'app/views/funcionario/gerenciar.html'
		})
		.state('app.funcionarioConsultar', {
			url: '/funcionario/consultar',
			templateUrl: 'app/views/funcionario/consultar.html'
		})
		.state('app.cartao', {
			url: '/cartao',
			templateUrl: 'app/views/cartao.html'
		})
		.state('app.setor', {
			url: '/setor',
			templateUrl: 'app/views/setor.html'
		})
		.state('app.grupo', {
			url: '/grupo',
			templateUrl: 'app/views/grupo.html'
		})
		.state('app.funcao', {
			url: '/funcao',
			templateUrl: 'app/views/funcao.html'
		});
            
    $urlRouterProvider.otherwise('/');
})

.provider('Url', function() {

    return {
        $get: function() {
			var urlBase = "https://localhost/web-service-feak/v1/";
            return {
                "login": urlBase + "login",
                "logout": urlBase + "logout",
				"people": urlBase + "people",
				"permission": urlBase + "permission",
				"photo": urlBase + "people/photo",
				"card": urlBase + "card",
				"sector": urlBase + "sector",
				"group": urlBase + "group",
				"office": urlBase + "office",
                "category": urlBase + "category"
            };
        }
    };
    
});