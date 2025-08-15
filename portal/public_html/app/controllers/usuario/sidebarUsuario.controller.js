app
        
.controller("SidebarUsuarioCtrl", function($scope, $timeout, $location, $sessionStorage, growl, cfpLoadingBar, Login, Util) {
    var sidebarUsu = this;

    sidebarUsu.nameUser = '';
    sidebarUsu.photoUser = '';
    sidebarUsu.mostrarDobra = 'N';
    sidebarUsu.menuItem = 'cardapio';
    sidebarUsu.consulta_rh = 'N';
    sidebarUsu.consulta_catraca = 'N';
    sidebarUsu.loaderLogout = false;
    sidebarUsu.authenticationVerificationLoader = true;

    //Change System
    sidebarUsu.loaderSystem = false;
    sidebarUsu.loaderSystemChange = false;  
    sidebarUsu.system = [];

    sidebarUsu.fnRota = function(rota) {       
        $location.path(rota);
    }

    window.onhashchange = function() {
        _selectMenuItem();
    }
    
    sidebarUsu.fnLogout = function() {
        sidebarUsu.loaderLogout = true;
        
        Login.logout()
            .then(function (resp) {
                $location.path('/');
            })
            .catch(function (err) {
                sidebarUsu.loaderLogout = false;
                Util.treatError(err);
            });
    };

    function _selectMenuItem() {
        rota = window.location.href.replace(/[\w\d\/:\.]*#!/, '');

        switch (rota) {
            case '/solicitante/relatorio/rh':
            case '/solicitante/relatorio/pessoal':
            case '/solicitante/relatorio/catraca':
                sidebarUsu.menuItem = 'relatorio';
                break;
            case '/solicitante/home':
            default:
                sidebarUsu.menuItem = 'cardapio'; 
        }
    }

    //Change System

    sidebarUsu.fnFindSystemsAccess = function() {
        sidebarUsu.loaderSystem = true;
        $('#myModalSystem').modal({'show': true, backdrop: 'static'}); 

        Login.findSystemsAccess()
            .then(function(resp) {
                sidebarUsu.systems = resp;
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                sidebarUsu.loaderSystem = false;
            });
    }

    sidebarUsu.fnChangeSystem = function(system) {
        sidebarUsu.loaderSystemChange = true;
        
        Login.changeOfSystem(system)
            .then(function(resp) {
                if (resp.foi_alterado) {
                    var token = $sessionStorage.session.token;
                    $sessionStorage.$reset();
                    window.location.href = system.url_acesso + '?token=' + token;
                } else {
                    var url = window.location.href;

                    if (url.indexOf('solicitante') !== -1 && system.url_acesso.indexOf('administrador') !== -1) {
                        $('#myModalSystem').modal('hide'); 
                        $timeout(function() {
                            window.location.href = system.url_acesso;    
                        }, 300);
                    } else {
                        growl.warning("Você já se encontra neste sistema");

                    }
                }
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                sidebarUsu.loaderSystemChange = false; 
            });
    }

    /*
        Register Events
    */

   $scope.$on("authentication", function(event, args) {
        console.log('teste');
        if (args.auth) {
            cfpLoadingBar.complete();

            $timeout(function() {
                _selectMenuItem();

                if ($sessionStorage.hasOwnProperty('user')) {
                    sidebarUsu.photoUser = $sessionStorage.user.photo;
                    sidebarUsu.nameUser = $sessionStorage.user.name.match(/[^\d\s]+/)[0];
                }
                
                if ($sessionStorage.hasOwnProperty('permissions')) {
                    if ($sessionStorage.permissions.hasOwnProperty('8')) {
                        sidebarUsu.mostrarDobra = $sessionStorage.permissions[8].vl_permissao || 'N';
                    }
                    if ($sessionStorage.permissions.hasOwnProperty('15')) {
                        sidebarUsu.consulta_rh = $sessionStorage.permissions['15'].vl_permissao || 'N';
                    }
                    if ($sessionStorage.permissions.hasOwnProperty('17')) {
                        sidebarUsu.consulta_catraca = $sessionStorage.permissions['17'].vl_permissao || 'N';
                    }
                }                 

                sidebarUsu.authenticationVerificationLoader = false;
            }, 500)
        } 
    });
});