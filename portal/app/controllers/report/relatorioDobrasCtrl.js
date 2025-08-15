app

.controller("relatorioDobrasCtrl", function(growl, Util, ThirdFolds) {
    vm = this;
    
    vm.loader = false;
    vm.loaderBtnMore = false;
    vm.firstSearch = true;
    vm.loadMore = 0;
    vm.dobras = [];
    vm.tipo_dobra_terceiro = [
        {cd_categoria: "D", ds_categoria: "Dobra"},
        {cd_categoria: "T", ds_categoria: "Terceiro"}
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

    function _fnVerificarInstanciaMoment(filter) {
        if (!(filter.dt_inicio instanceof moment) || !(filter.dt_final instanceof moment)) {;
            var dt_inicio = $('#idDtInicio').val();
            var dt_final = $('#idDtFinal').val();

            filter.dt_inicio = moment(dt_inicio, 'DD/MM/YYYY').utc();
            filter.dt_final = moment(dt_final, 'DD/MM/YYYY').utc();
        } 

        return filter;
    }

    vm.fnSearch = function(filter, more) {
        filter = _fnVerificarInstanciaMoment(filter);
        
        if (filter.dt_inicio.isValid() && filter.dt_final.isValid()) {
            if (filter.dt_final.isAfter(filter.dt_inicio) || filter.dt_final.isSame(filter.dt_inicio, 'day')) {
                vm.loader = more ? false : true;
                vm.loaderBtnMore = more;
                vm.firstSearch = false;
                
                var params = {};
                params.dt_inicio = filter.dt_inicio.format('DD/MM/YYYY');
                params.dt_fim = filter.dt_final.format('DD/MM/YYYY'); 
                
                if (/D|T/.test(filter.ie_terceiro_dobra)) {
                    params.ie_terceiro_dobra = filter.ie_terceiro_dobra
                }
                
                if (more) {
                    params.skip_line = vm.loadMore;
                }

                ThirdFolds.read(params, 'free')
                    .then(function(resp) {
                        if (more) {
                            vm.dobras = vm.dobras.concat(resp);
                        } else {
                            vm.dobras = resp;
                        }

                        if (resp.length > 0) {
                            vm.loadMore = vm.dobras.length;
                        } else {
                            vm.loadMore = 1;
                        }
                    })
                    .catch(function(err) {
                        Util.treatError(err);
                    })
                    .finally(function() {
                        vm.loader = false;
                        vm.loaderBtnMore = false;
                    });
            } else {
                growl.error("Data final está menor que data início!");
            }      
        } else {
            growl.error("Verifique as data informada!");
        }              
    }
});
