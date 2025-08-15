app

.controller("loginCtrl", function($timeout, $location, $stateParams, Login, TestPassword, People, growl) {
	var vm = this,
		params = $location.search();

	vm.loader = false;        
	vm.powerPassword = 0;
	vm.loaderAlterarSenha = false;

	/*
		Function	
	*/

	//Login 

	vm.fnLogin = function(user) {
		vm.loader = true;

		Login.login(user)
			.then(function(resp) {
				if (resp.user.update_password == 'S') {
					$('#modalAlterarSenha').modal({
						show: true,
						keyboard: false,
                        backdrop: 'static'
                    });
				} else {
					$location.path('/gestao-pessoas/home');
				}
			})
			.catch(function(err) {
				if (err.status == 401) {
					growl.error("Usuário ou senha inválido!");
				} else if (err.status == 403) {
					growl.error("Acesso negado!");
				}  else {
					growl.error("Erro no sistema desculpe pelos transtornos, comunique a TI!");
				}
			}).finally(function () {
				vm.loader = false;
			});
	};

	//Password

	vm.fnAlterarSenha = function (altSenha) {
		vm.loaderAlterarSenha = true;

		People.updatePassword(altSenha)
			.then(function(resp) {
				if (resp.foi_alterado) {
					$('#modalAlterarSenha').modal('hide');

					$timeout(function() {
						$location.path('/gestao-pessoas/home');
					}, 250);
				} else {
					growl.error("Essa senha é a atual, altera para uma nova!");
				}
			})
			.catch(function (err) {
				if (err.status == 401 || err.status == 403) {
					growl.error("Acesso negado!");
				} else if (err.status == 500) {
					growl.error("Erro no sistema desculpe pelos transtornos, comunique a TI!");
				}
			})
			.finally(function () {
				vm.loaderAlterarSenha = false;
			});
	}

	vm.fnVerificarSenha = function (altSenha) {
		vm.powerPassword = TestPassword.check(altSenha.ds_senha);
	};

	vm.fnAlterarCorBarraSenha = function (powerSenha) {
		if (powerSenha < 30) {
			return 'progress-bar-danger';
		} else if (powerSenha < 60) {
			return 'progress-bar-warning';
		} else if (powerSenha < 100) {
			return 'progress-bar-success';
		} else {
			return 'progress-bar-info';
		}
	};

	vm.fnAlterarStatusSenha = function (powerSenha, senha) {
		senha = senha ? senha : '';

		if (senha.length > 0) {
			if (powerSenha < 0) {
				return 'Super Fraca'
			} else if (powerSenha < 30) {
				return 'Fraca';
			} else if (powerSenha < 60) {
				return 'Média';
			} else if (powerSenha < 100) {
				return 'Forte';
			} else {
				return 'Super Forte';
			}
		}
	};

	vm.fnSenhaiguais = function (senha, confirmarSenha) {
		senha = senha ? senha : '';
		confirmarSenha = confirmarSenha ? confirmarSenha : '';

		if (confirmarSenha.length > 0) {
			if (senha != confirmarSenha) {
				return false
			}
		}

		return true;
	};

	/*
		Function Init
	*/

	$timeout(function() {
		if ($stateParams.error) {
			growl.error($stateParams.error);
		}
	}, 250);	
});