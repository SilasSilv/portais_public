app

.controller("relatorioQualidadeCtrl", function(growl, Util, Quality) {
	vm = this;

	vm.primeiraPesq = true;
	vm.loaderQuality = false;
	vm.avaliacao = {};
	vm.labels = ["Ótimo", "Bom", "Regular", "Ruim"];
	vm.apresentacaoOpt = { title: {display: true, fontSize: 15, text: 'Apresentação'}};
	vm.temperaturaOpt = { title: {display: true, fontSize: 15, text: 'Temperatura'}};
	vm.saborOpt = { title: {display: true, fontSize: 15, text: 'Sabor'}};
	vm.simpatiaOpt = { title: {display: true, fontSize: 15, text: 'Simpatia'}};
	vm.higieneOpt = { title: {display: true, fontSize: 15, text: 'Higiene Local'}};
	vm.data = {
		format: 'MM/YYYY',
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
		if (!(params.data instanceof moment)) {
				var data = $('#idData').val();
				params.data = moment(data, 'MM/YYYY').utc()
		}

		return params;
	}

	vm.fnPesquisar = function(params) {
		vm.loaderQuality = true;
		params = fnVerificarInstanciaMoment(params);
		
		if (params.data.isValid()) {

			Quality.statistics(params.data) 
				.then(function(resp) {
					vm.avaliacao = resp;
				})
				.catch(function(err) {
					Util.treatError(err);
				})
				.finally(function() {
					vm.loaderQuality = false;
					vm.primeiraPesq = false;
				});
	
		} else {
			vm.loaderQuality = false;
			growl.error("Verifique a data informada!");
		}
	}
});