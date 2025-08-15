app

.controller("templateCtrl", function($scope, $location, $timeout, $sessionStorage, growl, cfpLoadingBar, Login, Util) {
    var vm = this;

    vm.authenticationVerificationLoader = true;
    vm.loaderLogout = false;
    vm.nameUser = '';
    vm.menuItem = '';

    //Change System
    vm.loaderSystem = false;
    vm.loaderSystemChange = false;  

    /*
        Functions Utils
    */

    vm.fnLogout = function() {
        vm.loaderLogout = true;
        
        Login.logout()
            .then(function (resp) {
                $location.path('/');
            })
            .catch(function (err) {
                vm.loaderLogout = false;
                Util.treatError(err);
            });
    }     

    vm.fnRota = function(rota) {       
        $location.path(rota);
        $("#nav-main").collapse('hide');
    }

    window.onhashchange = function() {
        _selectMenuItem('');
    }

    function _selectMenuItem() {
        rota = window.location.href.replace(/[\w\d\/:\.]*#!/, '');

        if (/^\/gestao-pessoas\/funcionario\/editar\/[0-9]+$/.test(rota)) {
            rota = '/gestao-pessoas/funcionario/editar';
        }

        switch (rota) {
            case '/gestao-pessoas/funcionario/editar':
            case '/gestao-pessoas/funcionario/cadastrar':
            case '/gestao-pessoas/funcionario/consultar':
                vm.menuItem = 'funcionario';
                break;
            case '/gestao-pessoas/cartao':
                vm.menuItem = 'cartao';
                break;
            case '/gestao-pessoas/setor':
                vm.menuItem = 'setor';
                break;
            case '/gestao-pessoas/grupo':
                vm.menuItem = 'grupo';
                break;
            case '/gestao-pessoas/funcao':
                vm.menuItem = 'funcao';
                break;
            case '/gestao-pessoas/home':
            default:
                vm.menuItem = 'home';
        }
    }

    //Change System

    vm.fnFindSystemsAccess = function() {
        vm.loaderSystem = true;
        $('#myModalSystem').modal({'show': true, backdrop: 'static'}); 

        Login.findSystemsAccess()
            .then(function(resp) {
                vm.systems = resp;
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loaderSystem = false;
            });
    }

    vm.fnChangeSystem = function(system) {
        vm.loaderSystemChange = true;
        
        Login.changeOfSystem(system)
            .then(function(resp) {
                if (resp.foi_alterado) {
                    var token = $sessionStorage.session.token;
                    $sessionStorage.$reset();
                    window.location.href = system.url_acesso + '?token=' + token;
                } else {
                    growl.warning("Você já se encontra neste sistema");
                }
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loaderSystemChange = false; 
            });
    }

    /*
        Register Events
    */

    $scope.$on("authentication", function(event, args) {
        if (args.auth) {
            cfpLoadingBar.complete();

            $timeout(function() {
                vm.authenticationVerificationLoader = false;

                if ($sessionStorage.hasOwnProperty('user')) {
                    vm.nameUser = $sessionStorage.user.name.match(/[^\d\s]+/)[0];
                }
            }, 500)
        } 
    });

    /*
        Function Init
    */

    _selectMenuItem();
});