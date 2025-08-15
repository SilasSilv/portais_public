app

.controller("SidebarMenuAdminCtrl", function($scope, $timeout, $location, $sessionStorage, growl, Login, cfpLoadingBar, Opinion, Util) {
    var sidebarAdmin = this;

    sidebarAdmin.nameUser = '';
    sidebarAdmin.photoUser = '';
    sidebarAdmin.menuItem = '';
    sidebarAdmin.qtOpinioes = 0;
    sidebarAdmin.consulta_rh = 'N';
    sidebarAdmin.consulta_catraca = 'N';
    sidebarAdmin.mostrarPreco = 'N';
    sidebarAdmin.loaderLogout = false;
    sidebarAdmin.authenticationVerificationLoader = true;

    //Change System
    sidebarAdmin.loaderSystem = false;
    sidebarAdmin.loaderSystemChange = false;  
    sidebarAdmin.system = [];
    
    sidebarAdmin.fnRota = function(rota) {       
        $location.path(rota);
    }

    window.onhashchange = function() {
        _selectMenuItem();
    }
    
    sidebarAdmin.fnLogout = function() {
        sidebarAdmin.loaderLogout = true;

        Login.logout()
            .then(function (resp) {
                $location.path('/');
            })
            .catch(function (err) {
                sidebarAdmin.loaderLogout = false;
                Util.treatError(err);
            });
    }
    
    function _selectMenuItem() {
        rota = window.location.href.replace(/[\w\d\/:\.]*#!/, '');

        switch (rota) {
            case '/administrador/sugestao':
                sidebarAdmin.menuItem = 'sugestao';
                break;
            case '/administrador/refeicao/preco':
                sidebarAdmin.menuItem = 'preco';
                break;
            case '/administrador/relatorio/rh':             
            case '/administrador/relatorio/catraca':
            case '/administrador/relatorio/refeicao':
            case '/administrador/relatorio/dobra-terceiro':
            case '/administrador/relatorio/avaliacao/qualidade':
                sidebarAdmin.menuItem = 'relatorio';
                break;
            case '/administrador/cadRefeicao':
            default:
                sidebarAdmin.menuItem = 'cadRefeicao';
        }
    }

    function _loadOpinion() {
        Opinion.read()
            .then(function(resp) {
                $sessionStorage.opinioes = resp;
                sidebarAdmin.qtOpinioes = resp.length;
            })
            .catch(function(err) {
                Util.treatError(err);
            });
    }

    //Change System

    sidebarAdmin.fnFindSystemsAccess = function() {
        sidebarAdmin.loaderSystem = true;
        $('#myModalSystem').modal({'show': true, backdrop: 'static'}); 

        Login.findSystemsAccess()
            .then(function(resp) {
                sidebarAdmin.systems = resp;
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                sidebarAdmin.loaderSystem = false;
            });
    }

    sidebarAdmin.fnChangeSystem = function(system) {
        sidebarAdmin.loaderSystemChange = true;
        
        Login.changeOfSystem(system)
            .then(function(resp) {
                if (resp.foi_alterado) {
                    var token = $sessionStorage.session.token;
                    $sessionStorage.$reset();
                    window.location.href = system.url_acesso + '?token=' + token;
                } else {
                    var url = window.location.href;

                    if (url.indexOf('administrador') !== -1 && system.url_acesso.indexOf('solicitante') !== -1) {
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
                sidebarAdmin.loaderSystemChange = false; 
            });
    }
    
    /*
        Register Events
    */

    $scope.$on('sugestaoBroadcast', function(event, args) {
        sidebarAdmin.qtOpinioes = args.qtOpinioes;
    });


    $scope.$on("authentication", function(event, args) {
        if (args.auth) {
            cfpLoadingBar.complete();

            $timeout(function() {
                _loadOpinion();
                _selectMenuItem();

                if ($sessionStorage.hasOwnProperty('user')) {
                    sidebarAdmin.photoUser = $sessionStorage.user.photo;
                    sidebarAdmin.nameUser = $sessionStorage.user.name.match(/[^\d\s]+/)[0];
                }

                if ($sessionStorage.hasOwnProperty('permissions')) {
                    if ($sessionStorage.permissions.hasOwnProperty('13')) {
                        sidebarAdmin.mostrarPreco = $sessionStorage.permissions['13'].vl_permissao || 'N';
                    }
                    if ($sessionStorage.permissions.hasOwnProperty('15')) {
                        sidebarAdmin.consulta_rh = $sessionStorage.permissions['15'].vl_permissao || 'N';
                    }
                    if ($sessionStorage.permissions.hasOwnProperty('17')) {
                        sidebarAdmin.consulta_catraca = $sessionStorage.permissions['17'].vl_permissao || 'N';
                    }
                }

                sidebarAdmin.authenticationVerificationLoader = false;
            }, 500)
        } 
    });    
});