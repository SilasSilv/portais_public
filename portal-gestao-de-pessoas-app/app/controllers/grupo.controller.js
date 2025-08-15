app

.controller("grupoCtrl", function($timeout, Group, Util, growl) {
    var vm = this,
        _statusNewRegister;

    vm.loader = false;
    vm.loaderBtnSave = false;
    vm.loaderBtnDelete = false;
    vm.loaderPlus = false;
    vm.displayLoaderPlus = false;
    vm.statusNewAndUpdate = false;
    vm.cd_grupo = -1;
    vm.group = {nm_grupo: undefined};
    vm.groups = [];
  
    /*
        Functions CRUD
    */

    vm.fnSearch = function(name) {
        vm.loader = true;
        var filter = {};

        if (name) {
            filter.nm_grupo = name;
        }

        Group.read(filter)
            .then(function(resp) {
                if (resp.length < 20) {
                    vm.displayLoaderPlus = false;
                } else {
                    vm.displayLoaderPlus = true;
                }

                vm.groups = resp;
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {               
                vm.loader = false;
            });
    }

    vm.fnSearchPlus = function(name) {
        vm.loaderPlus = true;
        var groupsLength = vm.groups.length;
        var filter = {pagination: (groupsLength + ",20")};

        if (name) {
            filter.nm_grupo = name;
        }

        Group.read(filter)
            .then(function(resp) {
                if (resp.length < 20) {
                    vm.displayLoaderPlus = false;
                } else {
                    vm.displayLoaderPlus = true;
                }

                vm.groups = vm.groups.concat(resp);
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {               
                vm.loaderPlus = false;
            });
    }

    vm.fnSave = function(group) {
        vm.loaderBtnSave = true;

        if (_statusNewRegister) {
            _fnRegister(group);
        } else {
            _fnUpdate(group);   
        }
    }

    function _fnRegister(group) {
        Group.create(group)
            .then(function(resp) {
                growl.success("Grupo cadastrado com sucesso!");
                vm.fnCloseGroupForm();
                vm.fnSearch();
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loader = false;
            });   
    }

    function _fnUpdate(group) {
        Group.update(group)
            .then(function(resp) {
                growl.success("Grupo atualizado com sucesso!");
                vm.fnCloseGroupForm();
                vm.fnSearch();
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loader = false;
            });
    }

    vm.fnDelete = function(group) {
        vm.cd_grupo = group.cd_grupo;
        vm.loaderBtnDelete = true;

        Group.delete(group)
            .then(function(resp) {
                growl.success("<span class='glyphicon glyphicon-trash growl-trash'></span> Grupo <strong style='color: red'>excluído</strong> com sucesso!", {disableIcons: true});
                vm.fnSearch();
            })
            .catch(function(err) {
                if (err.status = 422) {
                    growl.warning("Grupo não pode ser excluído, tem pessoas vinculadas a ele.");
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
        vm.fnToCleanGroup();
    }

    vm.fnStatusUpdate = function(group) {
        vm.group = angular.copy(group);
        vm.statusNewAndUpdate = true;
        _statusNewRegister = false;

        $('html, body').animate({scrollTop:0}, 'slow');
    }

    vm.fnCloseGroupForm =  function() {
        vm.statusNewAndUpdate = false;
        vm.loaderBtnSave = false; 
        vm.fnToCleanGroup();
    }

    vm.fnToCleanGroup = function() {
        vm.group = {nm_grupo: undefined};
    } 

    /*
        Functions init
    */

    vm.fnSearch();
});