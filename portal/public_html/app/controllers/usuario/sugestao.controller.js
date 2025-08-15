app

.controller("SugestaoCtrl", function ($scope, $sessionStorage, Util, growl, Opinion, Category) {
    var vm = this;
    
    vm.loaderBtnOpinar = false; 
    vm.tipo_opiniao = [];

    vm.fnCadastrar = function(opiniao) {
        vm.loaderBtnOpinar = true;
        var opiniaoTemp = angular.copy(opiniao);
        vm.fnLimparOpiniao(opiniao);

        Opinion.create(opiniaoTemp)
            .then(function(resp) {
                growl.success("Sugestão cadastrada com sucesso!");
                $("#myModalsugestao").modal('hide');
            })
            .catch(function(err) {
                if (err.status == 406) {
                    growl.warning("Não permitido, cadastrar a sua sugestão!");
                } else {
                    Util.treatError(err);
                }
            })
            .finally(function() {
                    vm.loaderBtnOpinar = false;
            });
    }; 
    
    vm.fnLimparOpiniao = function(opiniao) {
        if (opiniao) {
            opiniao.cd_tipo_opiniao = null;
            opiniao.ds_opiniao = '';
        }        
    } 

    $scope.$on("authentication", function(event, args) {
        if (args.auth) {
            vm.tipo_opiniao = $sessionStorage.tipo_opiniao || [];
    
            if (vm.tipo_opiniao.length == 0) {
                Category.read({cd_tipo_categoria: 6})
                    .then(function(resp) {                            
                        $sessionStorage.tipo_opiniao = resp;
                        vm.tipo_opiniao = resp;
                    })
                    .catch(function(err) {                                
                        Util.treatError(err, 'no');   
                    });
            } 
        } 
    });
});