app

.controller("cadRefeicaoCtrl", function ($scope, $location, $sessionStorage, Meal, Util, growl) {
    var vm = this;
                            
    vm.loader = true;
    vm.loaderBtn = false;
    vm.loaderBtnDel = false;
    vm.ieCadastrar = false;
    vm.tipoSalvar = '';
    vm.refeicao = {};
    vm.refeicoes = []; 
    vm.nr_refeicao = 0;    
    vm.indexRefeicaoAlt = -1;          
    vm.validDate = true;
    vm.validForm = false;
    vm.validateDtInicioCad = false;
    vm.validateDtFinalCad = false;
    vm.validateDtFinalMsg = '';
    vm.patternHoras = '';
    vm.tipo_refeicao = [
        {cd_categoria: "A", ds_categoria: "Almoço"},
        {cd_categoria: "J", ds_categoria: "Jantar"}
    ]; 
    vm.validPattern = {
        data: /(^[A-z]{3} [A-z]{3} \d\d \d{4} \d{2}:\d{2}:\d{2} [A-z]{3}-\d{4}$)|(^\d{2}\/\d{2}\/\d{4}$)/,
        data_hora: /(^[A-z]{3} [A-z]{3} \d\d \d{4} \d{2}:\d{2}:\d{2} [A-z]{3}-\d{4}$)|(^\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}$)/
    };                
    vm.options = {
        data: {
            format: 'DD/MM/YYYY',
            minDate: moment(moment().format('DD/MM/YYYY') + ' 00:00:00', 'DD/MM/YYYY HH:mm:ss'),
            maxDate: moment(moment().add(30, 'days').format('DD/MM/YYYY') + ' 23:59:59', 'DD/MM/YYYY HH:mm:ss'),
            showClear: true,
            keepInvalid: true,
            allowInputToggle: true,
            keyBinds: {
                left: null,
                right: null,
                'delete': null
            }
        },
        data_inicio: {
            format: 'DD/MM/YYYY HH:mm',
            minDate: moment().subtract(2, 'M'),
            maxDate: moment(moment().add(30, 'days').format('DD/MM/YYYY') + ' 23:59:59', 'DD/MM/YYYY HH:mm:ss'),
            showClear: true,
            keepInvalid: true,
            allowInputToggle: true,
            keyBinds: {
                left: null,
                right: null,
                'delete': null
            }
        },
        data_final: {
            format: 'DD/MM/YYYY HH:mm',
            minDate: moment(moment().format('DD/MM/YYYY') + ' 00:00:00', 'DD/MM/YYYY HH:mm:ss'),
            maxDate: moment(moment().add(30, 'days').format('DD/MM/YYYY') + ' 23:59:59', 'DD/MM/YYYY HH:mm:ss'),
            showClear: true,
            keepInvalid: true,
            allowInputToggle: true,
            keyBinds: {
                left: null,
                right: null,
                'delete': null
            }
        }
    };

    /*
    * Validation Functions
    */

    var validateDate = function(dateCad, mask, regex, dateType) { 
        var date;

        if (regex.test(dateCad)) {
            date = moment(dateCad, mask);

            if (date.isValid()) {
                switch(dateType) {
                    case 'refeicao': vm.refeicao.dt_refeicao = date.toISOString(); break;
                    case 'inicio':  vm.refeicao.dt_inicio = date.toISOString(); break;
                    case 'final':  vm.refeicao.dt_final = date.toISOString();
                }                      
            }
        }

        if (date instanceof moment) {
            if (dateType == 'inicio') {
                interval = moment(date).isBetween(moment().subtract(2, 'M'),
                                                moment(moment().add(30, 'days').format('DD/MM/YYYY') + ' 23:59:59', 'DD/MM/YYYY HH:mm:ss'), 
                                                null, '[]');
            } else {
                interval = moment(date).isBetween(moment(moment().format('DD/MM/YYYY') + ' 00:00:00', 'DD/MM/YYYY HH:mm:ss'),
                                                moment(moment().add(30, 'days').format('DD/MM/YYYY') + ' 23:59:59', 'DD/MM/YYYY HH:mm:ss'), 
                                                null, '[]');
            }

            if (!date.isValid() || !interval) {           
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    };

    var validaRefeicao = function(refeicao) {
        var dtRefeicao = moment(refeicao.dt_refeicao.format('DD/MM/YYYY'), 'DD/MM/YYYY').add(86399, 's');
            isValid = true;

        if (refeicao.dt_inicio.isAfter(dtRefeicao)) {
            vm.validateDtInicioCad = true;
            isValid = false;
        } else {
            vm.validateDtInicioCad = false;
        }
        
        if (refeicao.dt_final.isSameOrBefore(refeicao.dt_inicio)) {
            vm.validateDtFinalCad = true;
            isValid = false;
            vm.validateDtFinalMsg = 'A data de fim não pode ser menor ou igual a data de início';
        } else if (refeicao.dt_final.isAfter(dtRefeicao)) { 
            vm.validateDtFinalCad = true;
            isValid = false;
            vm.validateDtFinalMsg = 'A data de fim não pode ser maior que a data da refeição';
        } else {
            vm.validateDtFinalCad = false;
        }

        return isValid;
    };

    vm.fnValidarData = function(dateType) {
        var dateCad, mask, regex;

        switch (dateType) {
            case 'refeicao':
                dateCad = $('#idDtRefeicao')[0].value;
                mask = 'DD/MM/YYYY';
                regex = /\d{2}\/\d{2}\/\d{4}/;

                vm.validForm = validateDate(dateCad, mask, regex, dateType);
                vm.validDtRefeicao = vm.validForm;
                break;
            case 'inicio':
            case 'final':
                mask = 'DD/MM/YYYY HH:mm';
                regex = /\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}/;

                if (dateType == 'inicio') {
                    dateCad = $('#idDtInicio')[0].value;

                    vm.validForm = validateDate(dateCad, mask, regex, dateType);
                    vm.validDtInicio = vm.validForm;
                } else {
                    dateCad = $('#idDtFinal')[0].value;

                    vm.validForm = validateDate(dateCad, mask, regex, dateType);
                    vm.validDtFinal = vm.validForm;
                }
        }    
    };

    /*
    * CRUD Functions
    */

   vm.fnSearch = function() {
        Meal.read()
            .then(function(resp) {
                vm.refeicoes = resp;
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loader = false;
            });
    }   

    vm.fnSalvar = function (refeicao) {
        vm.loaderBtn = true;
        vm.validForm = true;

        var refeicaoTemp = angular.copy(refeicao);

        var _fnSuccess = function(resp, refeicaoTemp) {
            if (refeicaoTemp.ie_situacao == 'A') { 
                if (refeicaoTemp.ie_tipo_refeicao == 'A') {
                    refeicaoTemp.ds_tipo_refeicao = 'Almoço'
                } else if (refeicaoTemp.ie_tipo_refeicao == 'J') {
                    refeicaoTemp.ds_tipo_refeicao = 'Jantar'
                }

                var dt_refeicao = moment(refeicaoTemp.dt_refeicao, 'DD/MM/YYYY')
                switch (dt_refeicao.day()) {
                    case 0: refeicaoTemp.ds_dia = 'Dom'; break;
                    case 1: refeicaoTemp.ds_dia = 'Seg'; break;
                    case 2: refeicaoTemp.ds_dia = 'Ter'; break;
                    case 3: refeicaoTemp.ds_dia = 'Qua'; break;
                    case 4: refeicaoTemp.ds_dia = 'Qui'; break;
                    case 5: refeicaoTemp.ds_dia = 'Sex'; break;
                    case 6: refeicaoTemp.ds_dia = 'Sab';
                }
            }

            growl.success("Refeição salva com sucesso!");
        },
        _callbackError = function(err) {
            Util.treatError(err);
        },
        _callbackFinally = function() {
            vm.loaderBtn = false;
            vm.validForm = false;
            vm.ieCadastrar = false;
            vm.validateDtInicioCad = false;
            vm.validateDtFinalCad = false;
            vm.refeicao = {};
        };

        if (validaRefeicao(refeicaoTemp)) {            
            if (vm.tipoSalvar == 'create') {
                Meal.create(refeicaoTemp)
                    .then(function(resp) {
                        refeicaoTemp.nr_refeicao = resp.nr_sequencia;
                        refeicaoTemp.ie_situacao = 'A';
                        vm.refeicoes.push(refeicaoTemp);
                        _orderByMeal(vm.refeicoes);
                        _fnSuccess(resp, refeicaoTemp);
                    })
                    .catch(_callbackError)
                    .finally(_callbackFinally);
            } else if (vm.tipoSalvar == 'update') {
                Meal.update(refeicaoTemp)
                    .then(function(resp) {
                        if (refeicaoTemp.ie_situacao == 'A') {
                            vm.refeicoes[vm.indexRefeicaoAlt] = refeicaoTemp;
                        } else {
                            vm.refeicoes.splice(vm.indexRefeicaoAlt, 1);
                        }
                        _orderByMeal(vm.refeicoes);
                        _fnSuccess(resp, refeicaoTemp);
                    })
                    .catch(_callbackError)
                    .finally(_callbackFinally);
            }  
        } else {
            vm.loaderBtn = false;
            vm.validForm = false;
        }
    };
        
    vm.fnExcluir = function(nr_refeicao, index) {
        vm.loaderBtnDel = true;
        vm.nr_refeicao = nr_refeicao;

        Meal.delete(nr_refeicao)
            .then(function(resp) {   
                if (vm.refeicao.nr_refeicao == nr_refeicao) {
                    vm.ieCadastrar = false;
                    vm.fnLimparRefeicao(vm.refeicao);
                }
                
                vm.refeicoes.splice(index, 1);                       
                growl.success("<span class='glyphicon glyphicon-trash growl-trash'></span> Refeição foi <strong style='color: red'>excluída</strong> com sucesso!", {disableIcons: true});
            })
            .catch(function(err) {
                if (err.status == 422) {
                    growl.warning("Não permitido pois a refeição já tem pedidos realizados, caso precise excluir, inative a refeição");
                }  else {
                    Util.treatError(err);
                }
            })
            .finally(function() {
                vm.loaderBtnDel = false;
            });
    };

    /*
    * Auxiliary Functions 
    */

    function _orderByMeal(refeicoes) {
        refeicoes.sort(function(ref1, ref2) {
            dt1 = moment(ref1.dt_refeicao, 'DD/MM/YYYY').utc();
            dt2 = moment(ref2.dt_refeicao, 'DD/MM/YYYY').utc();

            if (dt1.isAfter(dt2)) {
                return 1;
            } else if (dt1.isBefore(dt2)) {
                return -1;
            }

            return 0;
        })
    }

    vm.fnLimparRefeicao = function (refeicao) {
        if (refeicao) {
            Object.keys(refeicao)
                .forEach(function (key) {
                    if (!/nr_cracha/.test(key))
                        delete refeicao[key];
                });
        }
    };
    
    vm.fnAlterarRef = function(refeicao, index) {
        vm.ieCadastrar = true;
        vm.validForm = false;
        vm.tipoSalvar = 'update';
        vm.indexRefeicaoAlt = index;

        vm.refeicao.nr_refeicao = refeicao.nr_refeicao;
        vm.refeicao.ie_tipo_refeicao = refeicao.ie_tipo_refeicao;
        vm.refeicao.dt_refeicao = moment(refeicao.dt_refeicao, 'DD/MM/YYYY').toISOString();
        vm.refeicao.ds_refeicao = refeicao.ds_refeicao;        
        vm.refeicao.dt_inicio = moment(refeicao.dt_inicio, 'DD/MM/YYYY HH:mm:ss').toISOString();
        vm.refeicao.dt_final = moment(refeicao.dt_final, 'DD/MM/YYYY HH:mm:ss').toISOString();
        vm.refeicao.ie_feriado = refeicao.ie_feriado; 
        vm.refeicao.ie_situacao = refeicao.ie_situacao;
    }

    $scope.$on("authentication", function(event, args) {
        if (args.auth) {
            vm.fnSearch();
        } 
    });

    if ($sessionStorage.session.hasOwnProperty('token')) {
        vm.fnSearch();
    }
});