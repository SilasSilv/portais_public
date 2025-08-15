app

.controller("QualidadeCtrl",  function($location, $timeout, Quality, Util) {
    var vm =  this;    

    vm.loaderBtn = false;
    vm.qualidade = {cd_apresentacao: 0, cd_temperatura: 0, cd_sabor: 0, cd_simpatia: 0, cd_higiene_loc: 0};

    vm.fnCadastraAvaliacao = function(qualidade) {
        vm.loaderBtn = true;

        Quality.create(qualidade)
            .then(function(resp) {
                $("#myModalAvaliacaoRefeicao").modal('hide');

                $timeout(function() {
                    $location.path('/solicitante/home');
                }, 200);
            })
            .catch(function(err) {
                Util.treatErrorGrowl(err);
            })
            .finally(function() {
                vm.loaderBtn = false;
            })      
    }

    vm.fnValidaQualidade = function(qualidade) {
        for (var prop in qualidade) {
            if (qualidade[prop] == 0) {
                return true;
            }
        }

        return false;
    }
});