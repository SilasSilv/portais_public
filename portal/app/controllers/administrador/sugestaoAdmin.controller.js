app

.controller("SugestaoAdminCtrl", function ($scope, $sessionStorage, Util, growl, Opinion) {
    var vm = this;
    
    vm.loader = false;
    vm.loaderLidoBtn = false;
    vm.opinioes = $sessionStorage.opinioes || [];
    vm.nr_sequencia = -1;

    vm.fnLido = function(opiniao, index) {
        vm.nr_sequencia = opiniao.nr_sequencia;
        vm.loaderLidoBtn = true;

        Opinion.readOpinion(opiniao)
            .then(function(resp) {
                vm.opinioes.splice(index, 1);
                $sessionStorage.opinioes = vm.opinioes;
                
                $scope.$emit('notificacaoSugestao', {qtOpinioes: vm.opinioes.length});
                growl.success('Sugestão lida com sucesso!');
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loaderLidoBtn = false;
            });
    };

    vm.fnBuscar = function() {
        vm.loader = true;

        Opinion.read()
            .then(function(resp) {
                $sessionStorage.opinioes = resp;
                vm.opinioes = resp;
                $scope.$emit('notificacaoSugestao', {qtOpinioes: vm.opinioes.length});
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loader = false;
            });
    };

    vm.fnBuscar();
});