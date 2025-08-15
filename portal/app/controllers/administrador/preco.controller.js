app

.controller("PrecoCtrl", function(Price, Util, growl) {
    var vm =  this,
        indexAltPreco = -1;

    vm.loader = true;
    vm.loaderBtn = false;
    vm.loaderBtnDel = false;
    vm.tipoSalvar = '';
    vm.ieCadastrar = false;
    vm.validateValor = false;
    vm.validadeValorText = '';
    vm.validateDtIncio = false;
    vm.validateDtIncioText = '';
    vm.validateDtFinal = false;
    vm.validateDtFinalText = '';
    vm.preco = {ie_situacao: 'A'};
    vm.nr_sequencia = 0;
    vm.precos = [];
    vm.data_inicio = {
        format: 'DD/MM/YYYY',
        showClear: true,
        keepInvalid: true,
        allowInputToggle: true,
        keyBinds: {
            left: null,
            right: null,
            'delete': null
        }
    };
    vm.data_final = {
        format: 'DD/MM/YYYY',
        showClear: true,
        keepInvalid: true,
        allowInputToggle: true,
        keyBinds: {
            left: null,
            right: null,
            'delete': null
        }
    };

    /*
    * CRUD Functions
    */

    vm.fnCarregar = function() {
        vm.loader =  true;

        Price.read()
            .then(function(resp) {
                vm.precos = resp;
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loader = false;
            });
    }

    vm.fnSalvar = function(preco) {
        var valido = true
            precoTemp = angular.copy(preco),
            _callbackError = function(err) {
                Util.treatError(err);
            },
            _callbackFinally = function() {
                vm.ieCadastrar = false;
                vm.loaderBtn = false;
                vm.fnLimparPreco(preco);
            };

        precoTemp.dt_vigencia_inicial = _fnVerificarInstanciaMoment(precoTemp.dt_vigencia_inicial, '#idDtInicio', 'DD/MM/YYYY');
        precoTemp.dt_vigencia_final = _fnVerificarInstanciaMoment(precoTemp.dt_vigencia_final, '#idDtFinal', 'DD/MM/YYYY');

        console.log(precoTemp);
        
        if ( _validarSalvarPreco(precoTemp)) {
            vm.loaderBtn = true;
            precoTemp.dt_vigencia_inicial = preco.dt_vigencia_inicial.format('DD/MM/YYYY');
            precoTemp.dt_vigencia_final = preco.dt_vigencia_final.format('DD/MM/YYYY');

            if (vm.tipoSalvar == 'create') {

                Price.create(precoTemp)
                    .then(function(resp) {
                        vm.fnCarregar();
                        growl.success("Refeição salva com sucesso!");
                    })
                    .catch(_callbackError)
                    .finally(_callbackFinally);

            } else if (vm.tipoSalvar == 'update') {

                Price.update(precoTemp)
                    .then(function(resp) {                                              
                        vm.precos[indexAltPreco] =  precoTemp;
                        growl.success("Refeição salva com sucesso!");
                    })
                    .catch(_callbackError)
                    .finally(_callbackFinally);
                
            }
        }
    }

    vm.fnExcluir = function(preco, index) {
        vm.nr_sequencia = preco.nr_sequencia;
        vm.loaderBtnDel = true;

        Price.delete(preco.nr_sequencia)
            .then(function(resp) {   
                if (vm.ieCadastrar && indexAltPreco == index) {
                    vm.ieCadastrar = false;
                }
                
                vm.fnLimparPreco(vm.preco);
                vm.precos.splice(index, 1);
                growl.success("<span class='glyphicon glyphicon-trash growl-trash'></span> Refeição foi <strong style='color: red'>excluída</strong> com sucesso!", {disableIcons: true});
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loaderBtnDel = false;
            });        
    }

    function _fnVerificarInstanciaMoment(pData, idData, mascara) {
        if (!(pData instanceof moment)) {
            var data = $(idData).val();
            return moment(data, mascara).utc();
        } 
        return pData;
    }

    /*
    * Auxiliary Functions 
    */

    function _validarSalvarPreco(preco) {
        var valido = true;      

        if (preco.vl_refeicao < 0) {
            vm.validateValor = true;
            vm.validateValorText = 'O valor não pode ser negativo';
            valido = false;
        } else {
            vm.validateValor = false;
        }

        if (!preco.dt_vigencia_inicial.isValid()) {
            vm.validateDtIncio = true;
            vm.validateDtIncioText = 'A data inicio é invalida';
            valido = false;
        } else {
            vm.validateDtIncio = false;
        }

        if (!preco.dt_vigencia_final.isValid()) {
            vm.validateDtFinal = true;
            vm.validateDtFinalText = 'A data final é invalida';
            valido = false;
        } else {
            vm.validateDtFinal = false;
        }

        if (preco.dt_vigencia_inicial.isValid() && preco.dt_vigencia_final.isValid()) {
            if (preco.dt_vigencia_inicial.isAfter(preco.dt_vigencia_final, 'day')) {
                vm.validateDtFinal = true;
                vm.validateDtFinalText = 'A data final não pode ser menor que a data de inicio';
                valido = false;
            } else {
                vm.validateDtFinal = false;
            }
        }
    
        return valido;
    }

    vm.fnAlterarPreco =  function(preco, index) {
        indexAltPreco = index;
        vm.ieCadastrar = true;
        vm.tipoSalvar = 'update';

        vm.preco.nr_sequencia = preco.nr_sequencia;
        vm.preco.vl_refeicao = Number(preco.vl_refeicao);
        vm.preco.dt_vigencia_inicial = moment(preco.dt_vigencia_inicial, 'DD/MM/YYYY');
        vm.preco.dt_vigencia_final = moment(preco.dt_vigencia_final, 'DD/MM/YYYY');
        vm.preco.ie_situacao = preco.ie_situacao;
    }

    vm.fnLimparPreco = function(preco) {
        if (preco) {
            Object.keys(preco).forEach(function(key) {
                if (/ie_situacao/.test(key)) {
                    preco[key] = 'A';
                } else {
                    delete preco[key];
                }
            });
        }
    }

    /*
    * Initialization Functions
    */
    vm.fnCarregar();
});