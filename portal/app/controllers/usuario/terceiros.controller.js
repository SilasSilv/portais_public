app

.controller("TerceirosCtrl", function ($timeout, $sessionStorage, Util, growl, Menu, ThirdFolds) {
    var vm = this;

    vm.loaderBtn = false;
    vm.loaderMenu = false;
    vm.refeicoes = [];
    vm.dobras = [];
    vm.nr_sequencia = -1;
    vm.nova_dobra = false;
    vm.alterar_dobra = false;
    vm.indexDobraAlterar = -1;
    vm.tipo_terceiro = [
        {cd_categoria: "D", ds_categoria: "Dobras"},
        {cd_categoria: "T", ds_categoria: "Terceiros"}
    ];    
    vm.tipo_refeicao = [
        {cd_categoria: "A", ds_categoria: "Almoço"},
        {cd_categoria: "J", ds_categoria: "Jantar"}
    ];    

    vm.fnBuscarRefeicao = function() {
        Menu.read({dobras_terceiros: 'S'})
            .then(function(resp) {
                vm.refeicoes = resp;
            })
            .catch(function(err) {
                Util.treatError(err);
            });
    };

    vm.fnBuscarDobras = function() {
        vm.loaderMenu = true;

        ThirdFolds.read()
            .then(function(resp) {
                vm.dobras = resp;
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loaderMenu = false;
                
                if (vm.dobras.length > 0) {
                    vm.nova_dobra = true;
                }
            });
    };

    vm.fnGateway = function(dobra) {
        if (vm.alterar_dobra) {
            vm.fnAlterar(dobra);
        } else {
            vm.fnCadastrar(dobra);
        }
    };

    vm.fnCadastrar = function(dobra) {
        vm.loaderBtn = true;
        var dobraTemp = angular.copy(dobra);

        ThirdFolds.create(dobraTemp)
            .then(function(resp) {
                vm.fnBuscarDobras();
                vm.fnLimpar(dobra, true);                          
                vm.nova_dobra = true;
                growl.success("Foi cadastrado com sucesso!"); 
            })
            .catch(function(err) {
                if (err.status == 404) {
                    if (dobraTemp.nr_cracha.length > 0) {
                        growl.error("O número do cracha não existe!");  
                    } else {
                        growl.error("O número do cartão não existe!");    
                    }                   
                } else {
                    Util.treatError(err);
                }                
            })
            .finally(function() {
                vm.loaderBtn = false;
            });             
    };

    vm.fnAlterar = function(dobra) {
        vm.loaderBtn = true;
        var dobraTemp = angular.copy(dobra);

        ThirdFolds.update(dobraTemp)
            .then(function(resp) {
                vm.fnBuscarDobras(); 
                vm.fnLimpar(dobra, true);
                vm.alterar_dobra = false;
                vm.nova_dobra = true;
                growl.success("Foi alterado com sucesso!"); 
            })
            .catch(function(err) {
                if (err.status == 404) {
                    if (dobraTemp.nr_cracha.length > 0) {
                        growl.error("O número do cracha não existe!");  
                    } else {
                        growl.error("O número do cartão não existe!");    
                    }                   
                } else {
                    Util.treatError(err);
                }
            })
            .finally(function() {                
                vm.loaderBtn = false;                
            });
    };

    vm.fnExcluir = function(dobra) {
        vm.nr_sequencia = dobra.nr_sequencia;
        vm.loaderBtn = true;
        
        ThirdFolds.delete(dobra)
            .then(function(resp) {
                index = vm.dobras.indexOf(dobra);
                vm.dobras.splice(index, 1);
            })
            .catch(function(err) {
                Util.treatError(err);   
            })
            .finally(function() {
                if (vm.dobras.length == 0) {
                    vm.fnLimpar(dobra, true);
                    vm.alterar_dobra = false;
                    vm.nova_dobra = false;
                }
                vm.loaderBtn = false;
            });
    };   

    vm.fnLimpar = function(dobra, limparTipo) {
        if (dobra != undefined) {
            if (limparTipo) {
                dobra.ie_terceiro_dobra = null;
            }

            if (!vm.alterar_dobra) {
                dobra.nr_sequencia = '';    
            }

            dobra.nr_cracha = '';
            dobra.nr_cartao = '';
            dobra.nm_pessoa_cartao = '';
            dobra.dt_refeicao = '';
        }
    };

    vm.fnCopiaAlterar = function(dobra, index) {
        vm.dobra = angular.copy(dobra);
        vm.indexDobraAlterar = index;
        vm.nova_dobra = false;
        vm.alterar_dobra = true;
    };

    vm.fnNovaDobra = function() {
        vm.fnLimpar(vm.dobra, true); 
        vm.nova_dobra = false; 
        vm.alterar_dobra = false;
    };

    vm.fnFecharModal = function() {
        $timeout(function() {
            vm.fnLimpar(vm.dobra, true);

            if (vm.dobras.length > 0) {
                vm.nova_dobra = true;
            } else {
                vm.nova_dobra = false;
            }
        }, 200);        
    }

    if ($sessionStorage.hasOwnProperty('permissions')) {
        if ($sessionStorage.permissions.hasOwnProperty('8')) {
            if ($sessionStorage.permissions[8].vl_permissao == 'S') {
                vm.fnBuscarRefeicao();
                vm.fnBuscarDobras();
            } 
        }
    }       
});