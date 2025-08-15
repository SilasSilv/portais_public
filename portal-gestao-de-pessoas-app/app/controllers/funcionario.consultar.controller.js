app

.controller("funcionarioConsultarCtrl", function($location, growl, Util, People, Sector) {
    var vm = this,
        params = $location.search();

    vm.loader = false;
    vm.loaderPlus = false;
    vm.loaderBtnDelete = false;
    vm.displayLoaderPlus = false;
    vm.nr_cracha = 0;
    vm.filter = {};
    vm.people = {};
    vm.peoples = [];

    /*
        Functions CRUD
    */

    //People

    vm.fnSearch = function(crachaNome) {
        vm.loader = true;
        var filter = {};
        
        if (crachaNome) {
            filter.crachaNome = crachaNome;  
        }

        People.read(filter)
            .then(function(resp) {
                if (resp.length < 20) {
                    vm.displayLoaderPlus = false;
                } else {
                    vm.displayLoaderPlus = true;
                }

                vm.peoples = resp;
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loader = false;
            });
    }

    vm.fnSearchPlus = function(crachaNome) {
        vm.loaderPlus = true;
        var peoplesLength = vm.peoples.length;
        var filter = {pagination: (peoplesLength + ",20")};

        if (crachaNome) {
            filter.crachaNome = crachaNome;  
        }

        People.read(filter)
            .then(function(resp) {
                if (resp.length < 20) {
                    vm.displayLoaderPlus = false;
                } else {
                    vm.displayLoaderPlus = true;
                }

                vm.peoples = vm.peoples.concat(resp);
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loaderPlus = false;
            });
    }

    vm.fnDelete = function(people, crachaNome) {
        vm.loaderBtnDelete = true;
        vm.nr_cracha = people.nr_cracha;

        People.delete(people.nr_cracha)
            .then(function(resp) {
                growl.success("<span class='glyphicon glyphicon-trash growl-trash'></span> Grupo <strong style='color: red'>excluído</strong> com sucesso!", {disableIcons: true});
                vm.fnSearch(crachaNome);
            })
            .catch(function(err) {
                if (err.status == 422) {
                    growl.warning("Funcionário não pode ser excluído, porque ele já realizou operações nos portais. O que pode ser feito é inativar o seu perfil.");
                } else {
                    Util.treatError(err);
                }
            })
            .finally(function() {
                vm.loaderBtnDelete = false;
            });
    }

    /*
        Functions init
    */

    if (params.hasOwnProperty('nr_cracha')) {
        vm.fnSearch(params.nr_cracha);
        vm.filter.crachaNome = params.nr_cracha;
        $location.search('nr_cracha', null);
    } else {
        vm.fnSearch();
    }
});