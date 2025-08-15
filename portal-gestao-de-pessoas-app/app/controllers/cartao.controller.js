app

.controller("cartaoCtrl", function($timeout, Card, People, Sector, Util, growl) {
    var vm = this,
        _statusNewRegister;

    vm.loader = false;
    vm.loaderModal = false;
    vm.loaderBtnSave = false;
    vm.loaderBtnDelete = false;
    vm.loaderPlus = false;
    vm.loaderPlusModal = false;
    vm.displayLoaderPlus = false;
    vm.displayLoaderPlusModalPeople = false;
    vm.displayLoaderPlusModalSector = false;
    vm.statusNewAndUpdate = false;
    vm.nr_cartao = -1;
    vm.card = {};
    vm.cards = [];
    vm.peoples = [];
    vm.firstSearchPeople = true;
    vm.sectors = [];
    vm.firstSearchSector = true;
    
    /*
        Functions CRUD
    */

    //Card

    vm.fnSearch = function(filter) {
        vm.loader = true;

        Card.read(filter)
            .then(function(resp) {
                if (resp.length < 20) {
                    vm.displayLoaderPlus = false;
                } else {
                    vm.displayLoaderPlus = true;
                }

                vm.cards = resp;
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {               
                vm.loader = false;
            });
    }

    vm.fnSearchPlus = function(filter) {
        vm.loaderPlus = true;
        var cardsLength = vm.cards.length;
        var filter = {pagination: (cardsLength + ",20")};

        Card.read(filter)
            .then(function(resp) {
                if (resp.length < 20) {
                    vm.displayLoaderPlus = false;
                } else {
                    vm.displayLoaderPlus = true;
                }

                vm.cards = vm.cards.concat(resp);
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {               
                vm.loaderPlus = false;
            });
    }

    vm.fnSave = function(card) {
        vm.loaderBtnSave = true;

        if (_statusNewRegister) {
            _fnRegister(card);
        } else {
            _fnUpdate(card);   
        }
    }

    function _fnRegister(card) {
        Card.create(card)
            .then(function(resp) {
                growl.success("Cartão cadastrado com sucesso!");
                vm.fnCloseCardForm();
                vm.fnSearch();
            })
            .catch(function(err) {
                if (err.status = 422) {
                    growl.warning("Número do cartão já cadastrado.");
                } else {
                    Util.treatError(err);
                }
            })
            .finally(function() {
                vm.loaderBtnSave = false;
            });        
    }

    function _fnUpdate(card) {
        card.nr_cartao_url = vm.nr_cartao;

        Card.update(card)
            .then(function(resp) {
                growl.success("Cartão atualizado com sucesso!");
                vm.fnCloseCardForm();
                vm.fnSearch();
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loaderBtnSave = false;
            });       
    }

    vm.fnDelete = function(card) {
        vm.nr_cartao = card.nr_cartao;
        vm.loaderBtnDelete = true;

        Card.delete(card)
            .then(function(resp) {
                growl.success("<span class='glyphicon glyphicon-trash growl-trash'></span> Cartão <strong style='color: red'>excluído</strong> com sucesso!", {disableIcons: true});
                vm.fnSearch();
            })
            .catch(function(err) {
                if (err.status = 422) {
                    growl.warning("Cartão já foi usado na catraca ou em solicitação de dobra ou terceiros, não pode ser mais excluído. Agora para inutilização do cartão deve desativar.");
                } else {
                    Util.treatError(err);
                }
            })
            .finally(function() {
                vm.loaderBtnDelete = false;
            });
    }

    //People 

    vm.fnSearchPeople = function(crachaNome) {
        vm.loaderModal = true;
        var filter = {crachaNome: crachaNome, ie_situacao: 'A'};
        
        People.read(filter)
            .then(function(resp) {
                if (resp.length < 20) {
                    vm.displayLoaderPlusModalPeople = false;
                } else {
                    vm.displayLoaderPlusModalPeople = true;
                }

                vm.peoples = resp;
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loaderModal = false;
                vm.firstSearchPeople = false;
            });
    }

    vm.fnSearchPeoplePlus = function(crachaNome) {
        vm.loaderPlusModal = true;
        var peoplesLength = vm.peoples.length;
        var filter = {crachaNome: crachaNome, ie_situacao: 'A', pagination: (peoplesLength + ",20")};
        
        People.read(filter)
            .then(function(resp) {
                if (resp.length < 20) {
                    vm.displayLoaderPlusModalPeople = false;
                } else {
                    vm.displayLoaderPlusModalPeople = true;
                }

                vm.peoples = vm.peoples.concat(resp);
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loaderPlusModal = false;
                vm.firstSearchPeople = false;
            });
    }

    vm.fnAddPeople = function(people) {
        vm.card.nr_cracha = people.nr_cracha;
        vm.card.nm_pessoa_fisica = people.nm_pessoa_fisica;
        
        $('#myModalPeople').modal('hide');
        
        $timeout(function() {
            vm.firstSearchPeople = true;
            vm.peoples = [];
        }, 500); 
    }
    
    vm.fnRemovePeople = function() {
        vm.card.nr_cracha = '';
        vm.card.nm_pessoa_fisica = '';
    }

    //Sector

    vm.fnSearchSector = function(name) {
        vm.loaderModal = true;
        name = name || 0;
        var filter = {ds_setor: name};
        
        Sector.read(filter)
            .then(function(resp) {
                if (resp.length < 20) {
                    vm.displayLoaderPlusModalSector = false;
                } else {
                    vm.displayLoaderPlusModalSector = true;
                }

                vm.sectors = resp;
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loaderModal = false;
                vm.firstSearchSector = false;
            });
    }

    vm.fnSearchSectorPlus = function(name) {
        vm.loaderPlusModal = true;
        name = name || 0;
        var sectorsLength = vm.sectors.length;
        var filter = {ds_setor: name, pagination: (sectorsLength + ",20")};
        
        Sector.read(filter)
            .then(function(resp) {
                if (resp.length < 20) {
                    vm.displayLoaderPlusModalSector = false;
                } else {
                    vm.displayLoaderPlusModalSector = true;
                }

                vm.sectors = vm.sectors.concat(resp);
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loaderPlusModal = false;
                vm.firstSearchSector = false;
            });
    }

    vm.fnAddSector = function(sector) {
        vm.card.cd_setor = sector.cd_setor;
        vm.card.ds_setor = sector.ds_setor;
        
        $('#myModalSector').modal('hide');
       
        $timeout(function() {
            vm.firstSearchSector = true;
            vm.sectors = [];
        }, 500); 
    }

    vm.fnRemoveSector =  function() {
        vm.card.cd_setor = '';
        vm.card.ds_setor = '';
    }
    
    /*
        Functions utils
    */

    vm.fnStatusNew = function() {
        vm.statusNewAndUpdate = true;
        _statusNewRegister = true;
        vm.fnToCleanCard();
    }

    vm.fnStatusUpdate = function(card) {
        vm.card = _fnCopyCard(card);
        vm.nr_cartao = card.nr_cartao;
        vm.statusNewAndUpdate = true;
        _statusNewRegister = false;

        $('html, body').animate({scrollTop:0}, 'slow');
    }

    vm.fnCloseCardForm =  function() {
        vm.statusNewAndUpdate = false; 
        vm.fnToCleanCard();
    }

    function _fnCopyCard(card) {
        var cardNew = {};

        for (key in card) {
            if (key == "nr_cartao") {
                cardNew[key] = parseInt(card[key]);
            } else {
                cardNew[key] = card[key];
            }            
        }
        
        return cardNew;
    } 

    vm.fnToCleanCard = function() {
        vm.card = {
                nr_cartao: undefined, 
                nr_cracha: '',
                nm_pessoa_fisica: '',
                cd_setor: '',
                ds_setor: '',
                ie_passe_livre_catraca: "N", 
                ie_situacao: "A"
            };
    } 

    /*
        Functions init
    */

    vm.fnSearch();
    vm.fnToCleanCard();
});