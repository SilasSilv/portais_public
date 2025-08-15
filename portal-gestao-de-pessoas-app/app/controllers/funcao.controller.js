app

.controller("funcaoCtrl", function($timeout, Office, Util, growl) {
    var vm = this,
        _statusNewRegister;

    vm.loader = false;
    vm.loaderBtnSave = false;
    vm.loaderBtnDelete = false;
    vm.loaderPlus = false;
    vm.displayLoaderPlus = false;
    vm.statusNewAndUpdate = false;
    vm.cd_cargo = -1;
    vm.office = {ds_cargo: undefined};
    vm.offices = [];

    /*
        Functions CRUD
    */

    vm.fnSearch = function(name) {
        vm.loader = true;
        var filter = {};

        if (name) {
            filter.ds_cargo = name;
        }

        Office.read(filter)
            .then(function(resp) {
                if (resp.length < 20) {
                    vm.displayLoaderPlus = false;
                } else {
                    vm.displayLoaderPlus = true;
                }

                vm.offices = resp;
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {               
                vm.loader = false;
                vm.loaderPlus = false;
            });
    }

    vm.fnSearchPlus = function(name) {
        vm.loaderPlus = true;
        var officesLength = vm.offices.length;
        var filter = {pagination: (officesLength + ",20")};

        if (name) {
            filter.ds_cargo = name;
        }

        Office.read(filter)
            .then(function(resp) {
                if (resp.length < 20) {
                    vm.displayLoaderPlus = false;
                } else {
                    vm.displayLoaderPlus = true;
                }
                
                vm.offices = vm.offices.concat(resp);
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {               
                vm.loaderPlus = false;
            });
    }

    vm.fnSave = function(office) {
        vm.loaderBtnSave = true;

        if (_statusNewRegister) {
            _fnRegister(office);
        } else {
            _fnUpdate(office);   
        }
    }

    function _fnRegister(office) {
        Office.create(office)
            .then(function(resp) {
                growl.success("Função cadastrado com sucesso!");
                vm.fnCloseOfficeForm();
                vm.fnSearch();
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loader = false;
            });         
    }

    function _fnUpdate(office) {
        Office.update(office)
            .then(function(resp) {
                growl.success("Função atualizado com sucesso!");
                vm.fnCloseOfficeForm();
                vm.fnSearch();
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loader = false;
            });        
    }

    vm.fnDelete = function(office) {
        vm.cd_cargo = office.cd_cargo;
        vm.loaderBtnDelete = true;

        Office.delete(office)
            .then(function(resp) {
                growl.success("<span class='glyphicon glyphicon-trash growl-trash'></span> Função <strong style='color: red'>excluído</strong> com sucesso!", {disableIcons: true});
                vm.fnSearch();
            })
            .catch(function(err) {
                if (err.status = 422) {
                    growl.warning("Função não pode ser excluída, tem pessoas vinculadas a ela.");
                } else {
                    Util.treatError(err);
                }                
            })
            .finally(function() {
                vm.loaderBtnDelete = false;
            });
    }

    /*
        Functions utils
    */

   vm.fnStatusNew = function() {
        vm.statusNewAndUpdate = true;
        _statusNewRegister = true;
        vm.fnToCleanOffice();
    }

    vm.fnStatusUpdate = function(office) {
        vm.office = angular.copy(office);
        vm.statusNewAndUpdate = true;
        _statusNewRegister = false;

        $('html, body').animate({scrollTop:0}, 'slow');
    }

    vm.fnCloseOfficeForm =  function() {
        vm.statusNewAndUpdate = false;
        vm.loaderBtnSave = false; 
        vm.fnToCleanOffice();
    }

    vm.fnToCleanOffice = function() {
        vm.office = {ds_cargo: undefined};
    } 

    /*
        Functions init
    */

    vm.fnSearch();
});