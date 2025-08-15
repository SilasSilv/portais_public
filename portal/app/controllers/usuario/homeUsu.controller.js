app

.controller("homeUsuCtrl", function ($scope, $sessionStorage, Menu, Util, growl) {
    vmHomeUser = this;
    
    vmHomeUser.dsRefAltTemp = '';               
    vmHomeUser.loader = false;
    vmHomeUser.loaderBtn = false;
    vmHomeUser.loaderBtnAlt = false;
    vmHomeUser.filtro = {};
    vmHomeUser.nr_refeicao = 0;
    vmHomeUser.refeicoes = [];
    vmHomeUser.refeicaoTemp = {};
    
    /*
    * CRUD Functions
    */
    vmHomeUser.fnBuscarRefeicao2 = function() {
        vmHomeUser.loader = true;

        Menu.readRequest()
            .then(function(resp) {
                vmHomeUser.refeicoes = resp;
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {          
                vmHomeUser.loader = false;
            });
    };

    vmHomeUser.fnSolicitar = function(refeicao) {
        vmHomeUser.nr_refeicao = refeicao.nr_refeicao;
        vmHomeUser.loaderBtn = true;        

        if (refeicao.ie_solicitado == 0) {
            Menu.create(refeicao)
                .then(function(resp) {
                    growl.success("Pedido de refeição realizado com sucesso!");
                    refeicao.ie_solicitado = 1;
                })
                .catch(function(err) {
                    if (err.status == 404) {
                        growl.warning("Solicitação não permitida, refeição foi excluído do cardápio!");
                    } else if (err.status == 406) {
                        growl.warning("Não permitido realizar pedido de refeição!");
                    } else if (err.status == 422) {
                        growl.warning("Você não tem permissão para solicitar refeição de final de semana ou feriado!");
                    } else {
                        Util.treatError(err);
                    }
                })
                .finally(function() {
                    vmHomeUser.loaderBtn = false;
                });
        } else {
            Menu.delete(refeicao.nr_refeicao)
                .then(function(resp) {
                    growl.success("<span class='glyphicon glyphicon-trash growl-trash'></span> Refeição foi <strong style='color: red'>excluída</strong> com sucesso!", {disableIcons: true});
                    refeicao.ie_solicitado = 0;
                    refeicao.ds_ref_alt = '';
                    refeicao.ie_ref_alt = 0;
                })
                .catch(function(err) {
                    if (err.status == 406) {
                        growl.warning("Não permitido realizar a exclusão do pedido de refeição!");
                    } else if (err.status == 422) {
                        growl.warning("Você não tem permissão para excluir o pedido de refeição de final de semana ou feriado!");   
                    } else {
                        Util.treatError(err);
                    }
                })
                .finally(function() {
                    vmHomeUser.loaderBtn = false;
                });
        }
    };

    vmHomeUser.fnAlteraRefeicao = function(refeicao) {
        var refTemp = angular.copy(refeicao);
        refTemp.ds_ref_alt = vmHomeUser.dsRefAltTemp;
        vmHomeUser.loaderBtnAlt = true;

        Menu.update(refTemp)
            .then(function(resp) {
                refeicao.ds_ref_alt = refTemp.ds_ref_alt;

                if (refeicao.ds_ref_alt.length > 0) {
                    refeicao.ie_ref_alt = 1;
                } else {
                    refeicao.ie_ref_alt = 0;
                }

                growl.success("Pedido de refeição realizado com sucesso!");
                $("#myModalAltCardapio").modal('hide');
            })
            .catch(function(err) {
                if (err.status == 406) {
                    growl.warning("Não permitido realizar alteração do pedido de refeição!");
                } else if (err.status == 422) {
                    growl.warning("Você não tem permissão para alterar o pedido de refeição de final de semana ou feriado!");
                } else {
                    Util.treatError(err);
                }
            })
            .finally(function() {
                vmHomeUser.loaderBtnAlt = false;
            });
    };
    
    /*
    * Auxiliary Functions 
    */
    vmHomeUser.fnStatusRefeicao = function(ie_solicitado, type) {
        switch (type) {
            case "color":
                if (ie_solicitado == 0) {
                    return "btn-success";
                } else {
                    return "btn-danger";
                }
                break;
            case "description":
                if (ie_solicitado == 0) {
                    return "Solicitar";
                } else {
                    return "Excluir";
                }
                break;
            case "icon":
                if (ie_solicitado == 0) {
                    return "glyphicon glyphicon-cutlery";
                } else {
                    return "glyphicon glyphicon-remove";
                }
                break;
            case "loader":
                if (ie_solicitado == 0) {
                    return "loader-btn-success";
                } else {
                    return "loader-btn-danger";
                }
                break;
            default:
                console.error("Opção invalida: fnCorStatusRef");
        }
    };

    vmHomeUser.fnVerificarPermissao = function(refeicao) {
        var dtFinal = moment(refeicao.dt_final, 'DD/MM/YYYY HH/mm/ss');
        
        if (moment().isAfter(dtFinal)) {
            return 'Expirado';
        }

        if (refeicao.ds_dia == 'Sab' || refeicao.ds_dia == 'Dom' || refeicao.ie_feriado == 'S') {
            if ($sessionStorage.hasOwnProperty('permissions')) {
                if ($sessionStorage.permissions.hasOwnProperty('14')) {
                    return $sessionStorage.permissions['14'].vl_permissao == 'N' ? 'FDS/Feriado-N' : '';
                }
            }
        }
        
        return '';
    }

    vmHomeUser.fnMostraRefeicaoAlt = function(refeicao) {
        vmHomeUser.refeicaoTemp = refeicao;
        vmHomeUser.dsRefAltTemp = refeicao.ds_ref_alt;
    };

    vmHomeUser.fnLimparAltRefeicao = function(refeicao) {
        refeicao.ds_ref_alt = '';
    };

    /*
        Functions init
    */

    $scope.$on("authentication", function(event, args) {
        if (args.auth) {
            vmHomeUser.fnBuscarRefeicao2();
        } 
    });

    if ($sessionStorage.session.hasOwnProperty('token')) {
        vmHomeUser.fnBuscarRefeicao2();
    }
});