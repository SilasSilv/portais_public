app

.controller("funcionarioPermissaoCtrl", function($scope, $timeout, $stateParams, growl, Util, Permission) {
    var vm = this,
    _updatePermissions = [],
    _nrCracha = 0;

    vm.loader = false;
    vm.loaderSave = false;
    vm.permissions = [];
    vm.permissionsUpdateStatus = false;
    vm.permissionDetails = {};

    /*
        Functions CRUD
    */

    //Permission

    function _loadingPermissions(nr_cracha) {
        vm.loader = true;
        nr_cracha = nr_cracha || 0;

        Permission.read(nr_cracha)
            .then(function(resp) {
                vm.permissions = resp;

                $timeout(function() {
                    $('#accordion a').click(function(e) {
                        $(this).collapse('show');
                        e.preventDefault();
                    });

                    $('.panel-collapse.collapse').on('show.bs.collapse', function (e) {
                        $(this).siblings(".panel-heading")
                            .find("button").removeClass('btn-info-clear').addClass('btn-danger-clear')
                            .find("span").removeClass('glyphicon-plus').addClass('glyphicon-minus');
                    });
                    
                    $('.panel-collapse.collapse').on('hide.bs.collapse', function (e) {
                        $(this).siblings(".panel-heading")
                            .find("button").removeClass('btn-danger-clear').addClass('btn-info-clear')
                            .find("span").removeClass('glyphicon-minus').addClass('glyphicon-plus');
                    });
                }, 250);
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loader = false;
            });
    }

    vm.fnManipulatePermission = function(permission, value) {
        var existsPermission =  _updatePermissions.map(function(e) { return e.cd_permissao; }).indexOf(permission.cd_permissao);
        vm.permissionsUpdateStatus = true;

        if (permission.vl_permissao == value) {
            permission.vl_permissao = '';

            if (existsPermission != -1) {
                _updatePermissions[existsPermission].vl_permissao = 'D';
            } else {
                _updatePermissions.push({cd_permissao: permission.cd_permissao, vl_permissao: 'D'});
            }
        } else {
            permission.vl_permissao = value;

            if (existsPermission != -1) {
                _updatePermissions[existsPermission].vl_permissao = value;
            } else {
                _updatePermissions.push({cd_permissao: permission.cd_permissao, vl_permissao: value});
            }            
        }
    }

    vm.fnClearPermissions = function() {
        vm.permissions.forEach(function(system) {
            system.tipos_permissoes.forEach(function(typesPermission) {
                typesPermission.permissoes.forEach(function(permission) {
                    
                    if (permission.vl_permissao != '') {
                        var existsPermission =  _updatePermissions.map(function(e) { return e.cd_permissao; }).indexOf(permission.cd_permissao);
                        vm.permissionsUpdateStatus = true;
                        permission.vl_permissao = '';
                        
                        if (existsPermission != -1) {
                            _updatePermissions[existsPermission].vl_permissao = 'D';
                        } else {
                            _updatePermissions.push({cd_permissao: permission.cd_permissao, vl_permissao: 'D'});
                        }
                    }
                   
                });
            });
        });
    }

    vm.fnSalvar = function() {
        vm.loaderSave = true;

        Permission.persist(_updatePermissions, _nrCracha)
            .then(function(resp) {
                _updatePermissions = [];
                vm.permissionsUpdateStatus = false;
                growl.success("Permissões salvas com sucesso!");
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loaderSave = false;
            });        
    }

    /*
        Function Utils
    */

    //Modal Details Permission

    vm.fnShowModal = function(permission) {
        vm.permissionDetails = permission;
        $('#myModalDetailsPermission').modal({
            show: true,
            backdrop: 'static',
        });
    }
    
    /*
        Register Events
    */

   $scope.$on("update_cracha", function(event, args) {
        _nrCracha = args.nr_cracha;
        _loadingPermissions(_nrCracha);
   });


    /*
        Functions init
    */

   if ($stateParams.nr_cracha) {
         _nrCracha = $stateParams.nr_cracha
        _loadingPermissions(_nrCracha);
    } else {
        _loadingPermissions();
    }
    console.log('teste');
});