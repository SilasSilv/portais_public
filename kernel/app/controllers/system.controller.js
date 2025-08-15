app

.controller("systemCtrl", function($scope, $timeout, growl, System, Util) {
    var vm = this;

    vm.loader = false;
    vm.loaderPlus = false;
    vm.loaderID = false;
    vm.loaderBtnSave = false;
    vm.statusRegister = false;
    vm.displayLoaderPlus = false;
    vm.ieBtnClearImgLogo = false;
    vm.name = '';
    vm.cd_sistema = 0;
    vm.system = {};
    vm.previewLogo = 'img/system-default.png';
    vm.ieOperation = '';
    vm.ieSituacoes = [
        {cd_situacao: 'A', ds_situacao: 'Ativo'},
        {cd_situacao: 'I', ds_situacao: 'Inativo'},
        {cd_situacao: 'M', ds_situacao: 'Manutenção'}
    ];
    vm.systems = [];

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

        _fnSearch(filter);
    }
    
    vm.fnSearchPlus = function(name) {
        vm.loaderPlus = true;

        var systemsLength = vm.systems.length;
        var filter = {pagination: (systemsLength + ",20")};
        vm.name = name || '';

        if (name) {
            filter.ds_sistema = name;
        }

        _fnSearch(filter);        
    }

    function _fnSearch(filter) {
        System.read(filter)
            .then(function(resp) {
                if (resp.length < 20) {
                    vm.displayLoaderPlus = false;
                } else {
                    vm.displayLoaderPlus = true;
                }

                if (vm.loader) {
                    vm.systems = resp;
                } else {
                    vm.systems = vm.systems.concat(resp);
                }
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loader = false;
                vm.loaderPlus = false;
            });
    }

    vm.fnGetID = function() {
        vm.system.cd_token = 'Carregando...'

        System.id()
            .then(function(resp) {
                vm.system.cd_token = resp.id_sistema;
            }) 
            .catch(function(err) {
                vm.system.cd_token = '';
                Util.treatError(err);
            });
    }

    function _treatSystemData(system, img_logo) {
        var systemCopy = angular.copy(system);
        
        if (systemCopy.img_logo == vm.previewLogo) {
            delete systemCopy.img_logo;
        } else if (img_logo) {
            if (img_logo.length > 0) {
                systemCopy.img_logo = img_logo[0];
            }
        }
        
        systemCopy.ie_situacao = systemCopy.ie_situacao.cd_situacao;

        return systemCopy;
    }

    vm.fnSave = function(system, img_logo) {
        var systemTemp = _treatSystemData(system, img_logo);
       
        if (vm.ieOperation == "register") {
            _fnRegister(systemTemp);
        } else if (vm.ieOperation == "update") {
            _fnUpdate(systemTemp);
        }
    }

    function _fnRegister(system) {
        vm.loaderBtnSave = true;       

        System.create(system)
            .then(function(resp) {
                growl.success("Sistema cadastrado com sucesso!");
                vm.fnSearch(vm.name);
                vm.fnClose();
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loaderBtnSave = false;
            });
    }

    function _fnUpdate(system) {
        vm.loaderBtnSave = true;       

        System.update(system)
            .then(function(resp) {
                growl.success("Sistema atualizado com sucesso!");
                vm.fnSearch(vm.name);
                vm.fnClose();
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loaderBtnSave = false;
            });
    }


    vm.fnDelete = function(cd_sistema) {
        vm.loaderBtnDelete = true;
        vm.cd_sistema = cd_sistema;

        System.delete(cd_sistema)
            .then(function(resp) {
                growl.success("<span class='glyphicon glyphicon-trash growl-trash'></span> Sistema <strong style='color: red'>excluído</strong> com sucesso!", {disableIcons: true});
                vm.fnSearch(vm.name);
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loaderBtnDelete = false;
            });
    }

    // Util 

    vm.fnNew = function() {
        vm.statusRegister = true;
        vm.ieOperation = "register";
        vm.fnClear();
    }

    vm.fnLoadEdit = function(system) {
        _fnClearInputFile();

        vm.system.cd_sistema = system.cd_sistema;       
        vm.system.cd_token = system.cd_token;
        vm.system.nm_sistema = system.nm_sistema;
        vm.system.ds_sistema = system.ds_sistema;
        vm.system.ie_situacao = vm.ieSituacoes[vm.ieSituacoes.map(function(s) { return s.cd_situacao; }).indexOf(system.ie_situacao)];
        vm.system.img_logo = system.img_logo;

        if (system.img_logo == null) {
            vm.previewLogo = 'img/system-default.png';
            vm.system.img_logo = '';
            vm.ieBtnClearImgLogo = false;
        } else {
            vm.previewLogo = system.img_logo;
            vm.ieBtnClearImgLogo = true;
        }

        vm.ieOperation = "update";
        vm.statusRegister = true;

        $('html, body').animate({scrollTop:0}, 'slow');
    }


    vm.fnClose = function() {
        vm.statusRegister = false;
        vm.fnClear();
    }

    vm.fnClear = function() {
        vm.system.nm_sistema = '';
        vm.system.ds_sistema = '';
        vm.system.ie_situacao = null;
        vm.system.img_logo = '';
        vm.img_logo = {};
        vm.fnClearImgLogo();
        vm.fnGetID();
    }

    //Function Preview and Input File

    vm.fnClearImgLogo = function () {
        _fnClearInputFile();
        vm.previewLogo = 'img/system-default.png';
        vm.ieBtnClearImgLogo = false;
        vm.system.img_logo = '';
    }

    function _fnClearInputFile() {
        var img_logo = $("#idImgLogo");
        img_logo.replaceWith(img_logo.val('').clone(true));
    }

    function _handlePreviewFiles(evt) {
        var file = evt.target.files;

        if (file.length > 0) {
            var reader = new FileReader();
            file = file[0];
            
            reader.onload = function(loadEvent) {
                $scope.$apply(function() {
                    vm.previewLogo = loadEvent.target.result;
                    vm.ieBtnClearImgLogo = true;
                });
            }

            reader.readAsDataURL(file);
        } else {
            $scope.$apply(function() {
                vm.previewLogo = 'img/system-default.png';
                vm.system.img_logo = '';
                vm.ieBtnClearImgLogo = false;                
            });
        }
    }

    /*
        Init 
    */

    vm.fnSearch();

    $timeout(function() {
        angular.element(document.getElementById('idImgLogo')).on('change', _handlePreviewFiles);
    }, 500);
});