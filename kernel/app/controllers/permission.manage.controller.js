app

.controller("manageCtrl", function($scope, $timeout, growl, Category, System, Permission, Util) {
    var vm = this;
    
    vm.loader = false;
    vm.loaderPermission = false;
    vm.loaderPermissionsTypesCategory = false;
    vm.loaderBtnSave = false;
    vm.ieSystems = true;
    vm.displayLoaderPlus = false;
    vm.cd_sistema = 0;
    vm.cd_permissao = 0;
    vm.params = {};
    vm.systems = [];
    vm.system = {};
    vm.permissionsTypesCategory = [];
    vm.permissions = [];
    vm.permission = {};
    vm.ieOperation = "";

    vm.imgDefault = 'img/picture.png';
    vm.previewLogo = vm.imgDefault;

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

    vm.fnSearchPermission = function(params) {
        vm.loaderPermission = true;
        var filter = {cd_sistema: vm.cd_sistema};
        params = params || {};
        vm.params = params;
        
        if (params.hasOwnProperty('codigo_ou_titulo')) {
            if (/[0-9]+/.test(params.codigo_ou_titulo)) {
                filter.cd_permissao = params.codigo_ou_titulo; 
            } else if (/[\wÀ-ú]+/.test(params.codigo_ou_titulo)) {
                filter.ds_titulo = params.codigo_ou_titulo;
            }
        }        

        if (params.hasOwnProperty('tipo_permissao')) {
            if (params.tipo_permissao !== null) {
                filter.cd_tipo_permissao = params.tipo_permissao.cd_categoria;
            }
        } 

        Permission.read(filter)
            .then(function(resp) {
                vm.permissions = resp;
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loaderPermission = false;
            });
    }

    function _treatPermisssionData(permission) {
        var permissionCopy = angular.extend({}, permission);
        
        if (permissionCopy.hasOwnProperty('tipo_permissao')) {
            if (permissionCopy.tipo_permissao.hasOwnProperty('cd_categoria')) {
                permissionCopy.cd_tipo_permissao = permissionCopy.tipo_permissao.cd_categoria;
            }
        } 

        permissionCopy.cd_sistema = vm.cd_sistema;
        
        return permissionCopy;
    }

    vm.fnSave = function(permission) {
        var permissionTemp = _treatPermisssionData(permission);

        if (vm.ieOperation == "register") {
            _fnRegister(permissionTemp);
        } else if (vm.ieOperation == "update") {
            _fnUpdate(permissionTemp);
        }
    }

    function _fnRegister(permission) {
        vm.loaderBtnSave = true;

        Permission.create(permission)
            .then(function(resp) {
                if (permission.logo_alternativo_acesso instanceof FileList) {
                    if (permission.logo_alternativo_acesso.hasOwnProperty("0")) {
                        return Permission.registerImage(permission, resp.cd_permissao);
                    }
                }

                growl.success("Permissão cadastrado com sucesso!");
                vm.fnSearchPermission(vm.params);
                vm.fnClose();
            })
            .then(function(resp) {
                growl.success("Permissão cadastrado com sucesso!");
                vm.fnSearchPermission(vm.params);
                vm.fnClose();
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loaderBtnSave = false;
            });
    }

    function _fnUpdate(permission) {
        vm.loaderBtnSave = true;

        Permission.update(permission)
            .then(function(resp) {
                if (permission.logo_alternativo_acesso instanceof FileList) {
                    if (permission.logo_alternativo_acesso.hasOwnProperty("0")) {
                        return Permission.registerImage(permission, permission.cd_permissao);
                    }
                }
                
                if (vm.previewLogo == vm.imgDefault) {
                    return Permission.deleteImage(permission.cd_permissao);
                }
            })
            .then(function(resp) {
                growl.success("Permissão atualizada com sucesso!");
                vm.fnSearchPermission(vm.params);
                vm.fnClose();
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loaderBtnSave = false;
            });
    }

    vm.fnDelete = function(cd_permissao) {
        vm.loaderBtnDelete = true;
        vm.cd_permissao = cd_permissao;

        Permission.delete(cd_permissao)
            .then(function(resp) {
                growl.success("<span class='glyphicon glyphicon-trash growl-trash'></span> Permissão <strong style='color: red'>excluído</strong> com sucesso!", {disableIcons: true});
                vm.fnSearchPermission(vm.params);
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loaderBtnDelete = false;
            });
    }

    // Permission - Util

    vm.fnChangeShowAPI = function(ie_mostrar_cliente) {
        if (ie_mostrar_cliente == 'S') {
            vm.permission.ie_mostrar_cliente = 'N';
            vm.permission.ds_descricao_cliente = '';
        } else {
            vm.permission.ie_mostrar_cliente = 'S';
        }
    }

    vm.fnNewPermission = function() {
        vm.permission = {
            ie_mostrar_cliente: "N",
            ie_mostrar_parametro: "N",
            ie_situacao: 'A'
        }
        vm.fnClearImgLogo();

        vm.ieOperation = "register";

        $('#myModalPermission').modal({
            show: true,
            backdrop: 'static'
        });
    }

    vm.fnLoadEdit = function(permission) {
        vm.fnClearImgLogo();
        
        vm.permission.cd_permissao = permission.cd_permissao;
        vm.permission.tipo_permissao = vm.permissionsTypesCategory[
            vm.permissionsTypesCategory
                .map(function(c) { return c.cd_categoria; })
                .indexOf(parseInt(permission.cd_tipo_permissao))
        ];
        vm.permission.ds_titulo = permission.ds_titulo;
        vm.permission.ds_descricao = permission.ds_descricao;
        vm.permission.url_acesso = permission.url_acesso;
        vm.permission.vl_padrao = permission.vl_padrao;
        vm.permission.ie_mostrar_cliente = permission.ie_mostrar_cliente;
        vm.permission.ds_descricao_cliente = permission.ds_descricao_cliente;
        vm.permission.ie_mostrar_parametro = permission.ie_mostrar_parametro;
        vm.permission.ie_situacao = permission.ie_situacao;
        vm.permission.nm_apelido_acesso = permission.nm_apelido_acesso;

        if (permission.logo_alternativo_acesso != '') {
            vm.permission.logo_alternativo_acesso = permission.logo_alternativo_acesso;
            vm.previewLogo = permission.logo_alternativo_acesso;
        }

        vm.ieOperation = "update";

        $('#myModalPermission').modal({
            show: true,
            backdrop: 'static'
        });
    }

    vm.fnClose = function() {
        $('#myModalPermission').modal('hide');
    }

    //Category

    vm.loadPermissionsTypesCategory = function() {
        vm.loaderPermissionsTypesCategory = true;
        var filter = {cd_tipo_categoria: 8};

        Category.read(filter)
            .then(function(resp) {
                vm.permissionsTypesCategory = resp;
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loaderPermissionsTypesCategory = false;
            });
    }

    /*
        Util
    */

    vm.fnSelectSystem = function(system) {
        vm.system = system;
        vm.ieSystems = false;
        vm.cd_sistema = system.cd_sistema;
        vm.fnSearchPermission()
    }

    //Function Preview and Input File

    vm.fnClearImgLogo = function() {
        var img_logo = $("#idImgLogo");
        img_logo.replaceWith(img_logo.val('').clone(true)); 
        vm.previewLogo = vm.imgDefault;
        vm.permission.logo_alternativo_acesso = '';
    }

    function _handlePreviewFiles(evt) {
        var file = evt.target.files;

        if (file.length > 0) {
            var reader = new FileReader();
            file = file[0];
            
            reader.onload = function(loadEvent) {
                $scope.$apply(function() {
                   vm.previewLogo = loadEvent.target.result;
                });
            }

            reader.readAsDataURL(file);
        } else {
            $scope.$apply(function() {
                vm.previewLogo = vm.imgDefault;
            });
        }
    }

    /*
        Init
    */

    vm.fnSearch();
    vm.loadPermissionsTypesCategory();

    $timeout(function() {
        angular.element(document.getElementById('idImgLogo')).on('change', _handlePreviewFiles);
    }, 500);
});