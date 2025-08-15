app

.controller("relatorioRhCtrl", function(Analyze, Util, growl) {
    vm = this;

    vm.loader = false;
    vm.primeiraPesq = true;
    vm.funcionarios = [];
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

    vm.fnPesquisar = function(params) {
		if (!/J|A/.test(params.ie_tipo_refeicao)) {
			delete params.ie_tipo_refeicao;
		}

		if (params.hasOwnProperty('cracha_ou_nome')) {
			if (params.cracha_ou_nome.length == 0) {   
				delete params.cracha_ou_nome;
			}        
		}

      	params = fnVerificarInstanciaMoment(params); 

		if (params.dt_inicio.isValid() && params.dt_final.isValid()) {
			if (params.dt_final.isAfter(params.dt_inicio) || params.dt_final.isSame(params.dt_inicio, 'day')) {
				vm.loader = true;

				Analyze.payroll(params)
					.then(function(resp) {
					vm.funcionarios = resp;;
					})
					.catch(function(err) {
					Util.treatError(err);
					})
					.finally(function() {
					vm.loader = false;
					vm.primeiraPesq = false;
					});
			} else {
				growl.error("Data final está menor que data início!");
			}      
		} else {
			growl.error("Alguma data informada está invalida!");
		}           
    }
});