app

.controller("relatorioPessoalCtrl", function(Analyze, Util, growl) {
    vm = this;
    
    vm.loader = false;
    vm.loaderBtn = false;
    vm.primeiraPesq = true;
    vm.ieVisualizar = 1;
    vm.carregarMais = 0;
    vm.refeicoes = [];
    vm.tipo_refeicao = [
        {cd_categoria: "A", ds_categoria: "Almoço"},
        {cd_categoria: "J", ds_categoria: "Jantar"}
    ];
    vm.data = {
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

    function fnVerificarInstanciaMoment(params) {
        if (!(params.dt_inicio instanceof moment) || !(params.dt_final instanceof moment)) {;
            var dt_inicio = $('#idDtInicio').val();
            var dt_final = $('#idDtFinal').val();

            params.dt_inicio = moment(dt_inicio, 'DD/MM/YYYY').utc();
            params.dt_final = moment(dt_final, 'DD/MM/YYYY').utc();
        } 

        return params;
    }

    vm.fnPesquisar = function(params, skip_line) {
        if (!/J|A/.test(params.ie_tipo_refeicao)) {
          delete params.ie_tipo_refeicao;
        }
    
        params = fnVerificarInstanciaMoment(params);

        if (skip_line) {
            params.skip_line = vm.carregarMais;
        }
        
        if (params.dt_inicio.isValid() && params.dt_final.isValid()) {
            if (params.dt_final.isAfter(params.dt_inicio) || params.dt_final.isSame(params.dt_inicio, 'day')) {
                vm.loader = skip_line ? false : true;
                vm.loaderBtn = true;
                vm.primeiraPesq = false;

                Analyze.private(params)
                    .then(function(resp) {
                        if (skip_line) {
                            vm.refeicoes = vm.refeicoes.concat(resp);
                        } else {
                            vm.refeicoes = resp;
                        }

                        if (resp.length > 0) {
                            vm.carregarMais = vm.refeicoes.length;
                        } else {
                            vm.carregarMais = 1;
                        }
                    })
                    .catch(function(err) {
                        Util.treatError(err);
                    })
                    .finally(function() {
                        vm.loader = false;                        
                        vm.loaderBtn = false;
                    });
            } else {
                growl.error("Data final está menor que data início!");
            }      
        } else {
            growl.error("Verifique as data informada!");
        }
    }
});