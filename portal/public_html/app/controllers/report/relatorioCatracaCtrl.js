app

.controller("relatorioCatracaCtrl", function(Util, Sector, Turnstile) {
    var vm = this;

    //Turnstile
    vm.accesses = [];
    vm.loader = false;
    vm.moreFilters = false;
    vm.firstSearch = true;
    vm.page = 0;
    vm.displayLoaderPlus = false;
    vm.disabledNext = false;
    vm.operatorTime = [
        {"code": "Igual", "description": "Igual"},
        {"code": "Maior", "description": "Maior"},
        {"code": "Menor", "description": "Menor"}
    ];  
    vm.hours = [
        {"code": 0, "description": "Todos"},
        {"code": 1, "description": "Café da Manha"},
        {"code": 2, "description": "Almoço"},
        {"code": 3, "description": "Café da Tarde"},
        {"code": 100, "description": "Fora do Horário"},
    ];
    vm.options = {
        data: {
            format: 'DD/MM/YYYY',
            showClear: true,
            allowInputToggle: true,
            ignoreReadonly: true
        },
        hour: {
            format: 'h:mm:ss',
            showClear: true,
            allowInputToggle: true,
            ignoreReadonly: true
        }
    };
    
    //Sector
    vm.sectors = [];
    vm.loaderSector = false;

    /**
     * Functions
    */

    //Turnstile

    function _treatParams(params) {
        var paramsCopy = angular.copy(params)

        if (paramsCopy.hasOwnProperty('op_tempo')) {
            if (! paramsCopy.op_tempo) {
                delete paramsCopy.op_tempo;
                delete paramsCopy.tm_dentro;
            }
        }
        if (paramsCopy.hasOwnProperty('cracha_cartao_nome')) {
            delete paramsCopy.nr_cracha_cartao;
            delete paramsCopy.nome;

            if (! paramsCopy.cracha_cartao_nome) {
                delete paramsCopy.cracha_cartao_nome;
            } else {
                if (/^\d+$/.test(paramsCopy.cracha_cartao_nome)) {
                    paramsCopy.nr_cracha_cartao = paramsCopy.cracha_cartao_nome;
                } else {
                    paramsCopy.nome = paramsCopy.cracha_cartao_nome;
                }   
            }
        }
        if (paramsCopy.hasOwnProperty('cd_setor')) {
            if (! paramsCopy.cd_setor) {
                delete paramsCopy.cd_setor;
            }
        }
        if (paramsCopy.cd_horario == 0) {
            delete paramsCopy.cd_horario;
        }

        return paramsCopy;
    }

    vm.fnSearch = function(filter) {
        vm.loader = true;

        var filter = filter || {};
        filter = _treatParams(filter);
        filter.plus = false;
        vm.page = 0;
        
        _fnSearch(filter);    
    }

    vm.fnSearchPlus = function(filter, direction) {
        vm.loader = true;

        var filter = filter || {};
        filter = _treatParams(filter);
        filter.plus = true;

        if (direction == 'back') {
            vm.page--;
            filter.pagination = (vm.page * 40) + ',40';
        } else if (direction == 'next') {
            vm.page++;
            filter.pagination = (vm.page * 40)  + ',40';
        }

        _fnSearch(filter);        
    }

    function _fnSearch(filter) {
        Turnstile.read(filter)
            .then(function(resp) {
                if (filter.plus) {
                    vm.displayLoaderPlus = true;
                } else {
                    vm.displayLoaderPlus = resp.length < 40 ? false : true;
                }

                vm.disabledNext = resp.length < 40 ? true : false;  
                vm.accesses = resp;
            })
            .catch(function(err) {
                Util.treatError(err); 
            })
            .finally(function() {
                vm.firstSearch = false;
                vm.loader = false;
            });
    }

    // Sector

    vm.fnLoadSectors = function() {
        vm.loaderSector = true;
        var filter = {pagination: "ALL"};

        Sector.read(filter)
            .then(function(resp) {
                vm.sectors = resp;
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loaderSector = false;
            });
    }


    /**
     * Function Init
     */
    vm.fnLoadSectors();
});
