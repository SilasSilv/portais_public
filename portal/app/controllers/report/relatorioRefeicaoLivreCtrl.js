app

.controller("relatorioRefeicaoLivreCtrl", function($timeout, growl, Analyze) {
    var vm = this;

    vm.params = {
        dt_refeicao: moment().toISOString()
    }
    vm.options = {
        data: {
            format: 'DD/MM/YYYY',
            showClear: true,
            allowInputToggle: true,
            ignoreReadonly: true
        }
    }
    vm.meals = [];
    vm.loader = true;

    vm.fnSearch = function(params) {
        vm.loader = true;

        Analyze.publicAnalyticalFree(params)
            .then(function(resp) {
                vm.meals = resp;
            })
            .catch(function(err) {
                console.log(err);
                growl.error("Erro, avise ao administrador do sistema!");
            })
            .finally(function() {
                vm.loader = false;
            }); 
    }

    //Init

    $timeout(function() {
        vm.fnSearch(vm.params);
    }, 500);   
});