app

.controller("homePrincipalCtrl", function ($stateParams, $timeout, $location, Menu, Login, People, TestPassword, Util, growl) {
    var vm = this,
        systemData = {};

    vm.loaderBtn = false;
    vm.loaderBtn2 = false;
    vm.loaderMenu = false;
    vm.loginAdmin = false;
    vm.powerPassword = 0;
    vm.refeicoes = [];

    vm.fnBuscaRefeicao = function() {
        vm.loaderMenu = true;

        Menu.read()
            .then(function (resp) {
                vm.refeicoes = resp;
            })
            .catch(function (err) {
                Util.treatError(err);
            })
            .finally(function () {
                vm.loaderMenu = false;
            });
    }

    vm.fnLogin = function(user) {
        vm.loaderBtn = true;
        vm.loginAdmin = false;

        Login.login(user)
            .then(function(resp) {
                if (resp.user.update_password == 'S') {
                    systemData = resp.user.system_data;
                    $('#modalAlterarSenha').modal({backdrop: 'static', keyboard: false, show: true});
                } else if (resp.user.system_data.avaliacao_refeicao == 'S') {                    
                    $("#myModalAvaliacaoRefeicao").modal({backdrop: 'static', keyboard: false, show: true});
                } else {
                    $location.path('/solicitante/home');
                }
            })
            .catch(function(err) {
                if (err.status == 401) {
                    growl.error("Usuário ou senha inválido!");
                } else if (err.status == 403) {
                    growl.error("Acesso negado!");
                }  else if (err.status == 500) {
                    growl.error("Erro no sistema desculpe pelos transtornos, comunique a TI!");
                }
            }).finally(function () {
                vm.loaderBtn = false;
            });
    };

    vm.fnLoginAdmin = function(user) {
        vm.loaderBtn2 = true;
        vm.loginAdmin = true;

        Login.login(user, true)
            .then(function(resp) {                    
                $("#myModalAdmin").modal("hide");

                $timeout(function() {
                    if (resp.user.update_password == 'S') {
                        $('#modalAlterarSenha').modal('show');                        
                    } else {
                        $location.path('/administrador/cadRefeicao');
                    }                                     
                }, 200);          
            })
            .catch(function(err) {
                if (err.status == 401) {
                    growl.error("Usuário ou senha inválido!");
                } else if (err.status == 403) {
                    growl.error("Não tem permissão para acessar o administrativo!");
                }  else if (err.status == 500) {
                    growl.error("Erro no sistema desculpe pelos transtornos, comunique a TI!");
                }
            }).finally(function () {
                vm.loaderBtn2 = false;
            });
    };

    /**
    * Função de Senhas - Begin
    **/

    vm.fnAlterarSenha = function(altSenha) {
        vm.loaderAlterarSenha = true;

        People.updatePassword(altSenha)
            .then(function (resp) {
                if (resp.foi_alterado) {
                    $('#modalAlterarSenha').modal('hide');

                    $timeout(function() {
                        if (vm.loginAdmin) {
                            $location.path('/administrador/cadRefeicao');
                        } else {
                            if (systemData.avaliacao_refeicao == 'S') {
                                $("#myModalAvaliacaoRefeicao").modal({backdrop: 'static', keyboard: false, show: true});
                            } else {
                                $location.path('/solicitante/home');
                            }
                        }
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

    vm.fnVerificarSenha = function(altSenha) {
        vm.powerPassword = TestPassword.check(altSenha.ds_senha);
    };

    vm.fnAlterarCorBarraSenha = function(powerSenha) {
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

    vm.fnAlterarStatusSenha = function(powerSenha, senha) {
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

    vm.fnSenhaiguais = function(senha, confirmarSenha) {
        senha = senha ? senha : '';
        confirmarSenha = confirmarSenha ? confirmarSenha : '';

        if (confirmarSenha.length > 0) {
            if (senha != confirmarSenha) {
                return false
            }
        }

        return true;
    };

    /**
     * Função de Senhas - End
     **/

    vm.fnMostrarRefeicao = function(refeicao) {
       /* if (refeicao.ds_dia == 'Sab' || refeicao.ds_dia == 'Dom' || refeicao.ie_feriado == 'S') {
           return false;
        } 
        //COMENTADO AQUI PARA MOSTRAR FINAL DE SEMANA E FERIADO
        */
        return true;
    }
    
    vm.fnBuscaRefeicao();

    $timeout(function() {
		if ($stateParams.error) {
			growl.error($stateParams.error);
		}
	}, 250);	
});