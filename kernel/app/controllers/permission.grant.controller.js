app

.controller("grantCtrl", function($timeout, growl, Sector, People, Permission, System, Util) {
    var vm = this;

    vm.loader = false;
    vm.ieSystems = true;
    vm.iePeoplePermission = false;
    vm.ieSectorPermission = false;
    vm.displayLoaderPlus = false;
    vm.reloadPermissionDOM = false;
    vm.loaderManipulate = false;
    vm.systems = [];
    vm.system = {};
    vm.permissions = [];
    vm.permission = {};
    vm.permissionOptions = [{value: 'S', name: 'Sim'}, {value: 'N', name: 'Não'}];

    //People
    vm.pesqPeople = {};
    vm.loaderPeople = false;
    vm.loaderPlusPeople = false;
    vm.displayBtnPlusPeople = false;
    vm.firstSearchPeople = true;
    vm.peoples = [];

    //Permission People
    vm.pesqPermissionPeople = {};
    vm.displayBtnPlusPermissionPeople = false;
    vm.loaderPermissionPeople = false;
    vm.loaderPermissionPlusPeople = false;
    vm.permissionsPeople = [];
    
    //Sector
    vm.pesqSector = {};
    vm.loaderSector = false;
    vm.loaderPlusSector = false;
    vm.displayBtnPlusSector = false;
    vm.firstSearchSector = true;
    vm.sectors = [];

    //Permission People
    vm.pesqPermissionSector = {};
    vm.displayBtnPlusPermissionSector = false;
    vm.loaderPermissionSector = false;
    vm.loaderPermissionPlusSector = false;
    vm.permissionsSector = [];

    /*
        Function
    */

    // System

    vm.fnSearch = function(name) {
        vm.loader = true;
        
        var filter = {};
        vm.name = name || '';

        if (name) {
            filter.nm_sistema = name;
        }

        System.read(filter)
            .then(function(resp) {
                if (resp.length < 20) {
                    vm.displayLoaderPlus = false;
                } else {
                    vm.displayLoaderPlus = true;
                }

                vm.systems = resp;
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loader = false;
            });
    }

    //Permission

    vm.fnSearchPermission = function() {
        vm.loader = true;
        vm.reloadPermissionDOM = true;
        var filter = {cd_sistema: vm.system.cd_sistema};
        
        Permission.read(filter, true)
            .then(function(resp) {
                vm.permissions = resp;
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.reloadPermissionDOM = false;

                $timeout(function() {
                    $('#myTabs a').click(function (e) {
                        e.preventDefault()
                        $(this).tab('show');
                    });     
                    vm.loader = false;
                }, 150);
            });
    }

    //Permission - Util

    vm.fnPermissionDetails = function(permission) {
        vm.permission = permission;

        $('#myModalInfoPermission').modal({
            show: true,
            backdrop: 'static'
        });
    }

    vm.fnShowPermissionPeople = function(permission) {
        vm.permission = permission;
        vm.pesqPeople = {};
        vm.pesqPermissionPeople = {};
        vm.peoples = [];
        vm.iePeoplePermission = false;
        vm.fnSearchPermissionPeople();

        $('#myModalPeoplePermission').modal({
            show: true,
            backdrop: 'static'
        });
    }

    vm.fnShowPermissionSector = function(permission) {
        vm.permission = permission;
        vm.pesqSector = {};
        vm.pesqPermissionSector = {};
        vm.sectors = [];
        vm.ieSectorPermission = false;
        vm.fnSearchPermissionSector();

        $('#myModalSectorPermission').modal({
            show: true,
            backdrop: 'static'
        });
    }

    //People 

    vm.fnSearchPeople = function(crachaNome) {
        vm.loaderPeople = true;
        var filter = {crachaNome: crachaNome, ie_situacao: 'A'};
        
        _fnSearchPeople(filter);
    }

    vm.fnSearchPeoplePlus = function(crachaNome) {
        vm.loaderPlusPeople = true;
        var filter = {crachaNome: crachaNome, ie_situacao: 'A', pagination: (vm.peoples.length + ",20")};
        
        _fnSearchPeople(filter);
    }

    function _fnSearchPeople(filter) {
        People.read(filter)
            .then(function(resp) {
                if (resp.length < 20) {
                    vm.displayBtnPlusPeople = false;
                } else {
                    vm.displayBtnPlusPeople = true;
                }

                if (vm.loaderPeople) {
                    vm.peoples = resp;
                } else {
                    vm.peoples = vm.peoples.concat(resp);
                }
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loaderPeople = false;
                vm.loaderPlusPeople = false;
                vm.firstSearchPeople = false;
            });
    }

    //Permission People

    vm.fnSearchPermissionPeople = function(params) {
        vm.loaderPermissionPeople = true;
        var filter = _fnPreampParametersPeople(params);

        _fnSearchPermissionPeople(filter);
    }

    vm.fnSearchPermissionPeoplePlus = function(params) {
        vm.loaderPermissionPlusPeople = true;
        var filter = _fnPreampParametersPeople(params);
        filter.pagination = (vm.permissionsPeople.length + ",20");

        _fnSearchPermissionPeople(filter);        
    }

    function _fnPreampParametersPeople(params)  {
        var filter = {
            cd_sistema: vm.system.cd_sistema,
            cd_permissao: vm.permission.cd_permissao,
           
        };
        params = params || {};

        if (params.hasOwnProperty('crachaNome')) {
            if (/[0-9]+/.test(params.crachaNome)) {
                filter.nr_cracha = params.crachaNome; 
            } else if (/[\wÀ-ú]+/.test(params.crachaNome)) {
                filter.nm_pessoa_fisica = params.crachaNome;
            }
        }
        
        return filter;
    }

    function _fnSearchPermissionPeople(filter) {
        Permission.readPeople(filter)
            .then(_fnSearchPermissionPeopleSuccess)
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loaderPermissionPeople = false; 
                vm.loaderPermissionPlusPeople = false;
            });
    }

    function _fnSearchPermissionPeopleSuccess(resp) {
        if (resp.length < 20) {
            vm.displayBtnPlusPermissionPeople = false;
        } else {
            vm.displayBtnPlusPermissionPeople = true;
        }

        if (vm.loaderPermissionPlusPeople) {
            vm.permissionsPeople = vm.permissionsPeople.concat(resp);
        } else {
            vm.permissionsPeople = resp;
        }
    }

    function _fnTreatPeoplePermisssionData(permission) {
        var permissionCopy = angular.copy(permission);
        permissionCopy.cd_permissao = vm.permission.cd_permissao;

        return permissionCopy;
    }

    vm.fnGrantPeople = function(permission) {
        var success = false;
        vm.loaderManipulate = true;
        permissionTemp = _fnTreatPeoplePermisssionData(permission);
        
        Permission.grantPeople(permissionTemp)
            .then(function(resp) {
                success = true;
                return Permission.readPeople(
                    _fnPreampParametersPeople(vm.pesqPermissionPeople)
                );
            })
            .then(_fnSearchPermissionPeopleSuccess)
            .catch(function(err) {
                if (err.status == 422) {
                    growl.warning('Usuário já tem a permissão, verifique se está com o parâmetro correto.');
                } else {
                    Util.treatError(err);
                };
            })
            .finally(function() {
                if (success) {
                    growl.success("Permissão foi parametrizada para o usuário!");
                    vm.iePeoplePermission = false;
                }
                vm.loaderManipulate = false;
            });            
    }

    vm.fnGrantUpdatePeople = function(permission) {
        vm.loaderManipulate = true;
        permissionTemp = _fnTreatPeoplePermisssionData(permission);

        Permission.grantUpdatePeople(permissionTemp)
            .then(function(resp) {
                growl.success("Parametrização foi atualizada na permissão do usuário!");
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.iePeoplePermission = false;
                vm.loaderManipulate = false;
            });
    }

    vm.fnRevokePeople = function(permission) {
        vm.loaderManipulate = true;
        permissionTemp = _fnTreatPeoplePermisssionData(permission);

        Permission.revokePeople(permissionTemp)
            .then(function(resp) {
                growl.success("<span class='glyphicon glyphicon-trash growl-trash'></span> Permissão <strong style='color: red'>excluída</strong> para o usuário com sucesso!", {disableIcons: true});
                
                return Permission.readPeople(
                    _fnPreampParametersPeople(vm.pesqPermissionPeople)
                );
            })
            .then(_fnSearchPermissionPeopleSuccess)
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.iePeoplePermission = false;
                vm.loaderManipulate = false;
            });
    }

    //Sector 

    vm.fnSearchSector = function(p_ds_setor) {
        vm.loaderSector = true;
        var filter = {ds_setor: p_ds_setor};
        
        _fnSearchSector(filter);
    }

    vm.fnSearchSectorPlus = function(p_ds_setor) {
        vm.loaderPlusSector = true;
        var filter = {ds_setor: p_ds_setor, pagination: (vm.sectors.length + ",20")};
        
        _fnSearchSector(filter);
    }

    function _fnSearchSector(filter) {
        filter.ds_setor = filter.ds_setor || 0;

        Sector.read(filter)
            .then(function(resp) {
                if (resp.length < 20) {
                    vm.displayBtnPlusSector = false;
                } else {
                    vm.displayBtnPlusSector = true;
                }

                if (vm.loaderSector) {
                    vm.sectors = resp;
                } else {
                    vm.sectors = vm.sectors.concat(resp);
                }
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loaderSector = false;
                vm.loaderPlusSector = false;
                vm.firstSearchSector = false;
            });
    }

    //Permission Sector

    vm.fnSearchPermissionSector = function(params) {
        vm.loaderPermissionSector = true;
        var filter = _fnPreampParametersSector(params);

        _fnSearchPermissionSector(filter);
    }

    vm.fnSearchPermissionSectorPlus = function(params) {
        vm.loaderPermissionPlusSector = true;
        var filter = _fnPreampParametersSector(params);
        filter.pagination = (vm.permissionsSector.length + ",20");

        _fnSearchPermissionSector(filter);        
    }

    function _fnPreampParametersSector(params)  {
        var filter = {
            cd_sistema: vm.system.cd_sistema,
            cd_permissao: vm.permission.cd_permissao,
           
        };
        params = params || {};

        if (params.hasOwnProperty('ds_setor')) {
            filter.ds_setor = params.ds_setor; 
        }
        
        return filter;
    }

    function _fnSearchPermissionSector(filter) {
        Permission.readSector(filter)
            .then(_fnSearchPermissionSectorSuccess)
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loaderPermissionSector = false; 
                vm.loaderPermissionPlusSector = false;
            });
    }

    function _fnSearchPermissionSectorSuccess(resp) {
        if (resp.length < 20) {
            vm.displayBtnPlusPermissionSector = false;
        } else {
            vm.displayBtnPlusPermissionSector = true;
        }

        if (vm.loaderPermissionPlusSector) {
            vm.permissionsSector = vm.permissionsSector.concat(resp);
        } else {
            vm.permissionsSector = resp;
        }
    }

    function _fnTreatSectorPermisssionData(permission) {
        var permissionCopy = angular.copy(permission);  
        permissionCopy.cd_permissao = vm.permission.cd_permissao;

        return permissionCopy;
    }

    vm.fnGrantSector = function(permission) {
        var success = false;
        vm.loaderManipulate = true;
        permissionTemp = _fnTreatSectorPermisssionData(permission);
        
        Permission.grantSector(permissionTemp)
            .then(function(resp) {
                success = true;
                return Permission.readSector(
                    _fnPreampParametersSector(vm.pesqPermissionSector)
                );
            })
            .then(_fnSearchPermissionSectorSuccess)
            .catch(function(err) {
                if (err.status == 422) {
                    growl.warning('Setor já tem a permissão, verifique se está com o parâmetro correto.');
                } else {
                    Util.treatError(err);
                };
            })
            .finally(function() {
                if (success) {
                    growl.success("Permissão foi parametrizada para o usuário!");
                    vm.ieSectorPermission = false;
                }
                vm.loaderManipulate = false;
            });            
    }

    vm.fnGrantUpdateSector = function(permission) {
        vm.loaderManipulate = true;
        permissionTemp = _fnTreatSectorPermisssionData(permission);

        Permission.grantUpdateSector(permissionTemp)
            .then(function(resp) {
                growl.success("Parametrização foi atualizada na permissão do usuário!");
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.ieSectorPermission = false;
                vm.loaderManipulate = false;
            });
    }

    vm.fnRevokeSector = function(permission) {
        vm.loaderManipulate = true;
        permissionTemp = _fnTreatSectorPermisssionData(permission);

        Permission.revokeSector(permissionTemp)
            .then(function(resp) {
                growl.success("<span class='glyphicon glyphicon-trash growl-trash'></span> Permissão <strong style='color: red'>excluída</strong> para o setor com sucesso!", {disableIcons: true});
                return Permission.readSector(
                    _fnPreampParametersSector(vm.pesqPermissionSector)
                );
            })
            .then(_fnSearchPermissionSectorSuccess)
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.ieSectorPermission = false;
                vm.loaderManipulate = false;
            });
    }

    /*
        Util
    */

    vm.fnSelectSystem = function(system) {
        vm.system = system;
        vm.ieSystems = false;
        vm.fnSearchPermission();
    }

    /*
        Init
    */

    vm.fnSearch();
});