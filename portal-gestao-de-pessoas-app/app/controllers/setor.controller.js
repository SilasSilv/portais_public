app

.controller("setorCtrl", function($timeout, Sector, Card, Util, growl) {
    var vm = this,
        _statusNewRegister,
        _cardManipulation = [];

    vm.loader = false;
    vm.loaderBtnSave = false;
    vm.loaderBtnDelete = false;
    vm.loaderPlus = false;
    vm.displayLoaderPlus = false;
    vm.statusNewAndUpdate = false;
    vm.availableCards = [];
    vm.cd_setor = -1;
    vm.sector = {ds_setor: undefined, cartoes: []};
    vm.sectors = [];
    vm.filter = {name: ''};

    /*
        Functions CRUD
    */

    //Sector

    vm.fnSearch = function(name) {
        vm.loader = true;
        var filter = {};
        
        if (name) {
            filter.ds_setor = name;
        }

        Sector.read(filter)
            .then(function(resp) {
                if (resp.length < 20) {
                    vm.displayLoaderPlus = false;
                } else {
                    vm.displayLoaderPlus = true;
                }

                vm.sectors = resp;
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {               
                vm.loader = false;
            });
    }

    vm.fnSearchPlus = function(name) {
        vm.loaderPlus = true;
        var sectorsLength = vm.sectors.length;
        var filter = {pagination: (sectorsLength + ",20")};
        
        if (name) {
            filter.ds_setor = name;
        }

        Sector.read(filter)
            .then(function(resp) {
                if (resp.length < 20) {
                    vm.displayLoaderPlus = false;
                } else {
                    vm.displayLoaderPlus = true;
                }

                vm.sectors = vm.sectors.concat(resp);
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {               
                vm.loaderPlus = false;
            });
    }

    vm.fnSave = function(sector) {
        vm.loaderBtnSave = true;
        sectorCopy = angular.copy(sector);
        sectorCopy.cartoes = _cardManipulation;

        if (_statusNewRegister) {
            _fnRegister(sectorCopy);
        } else {
            _fnUpdate(sectorCopy);   
        }
    }

    function _fnRegister(sector) {
        Sector.create(sector)
            .then(function(resp) {
                growl.success("Setor cadastrado com sucesso!");
                vm.fnCloseSectorForm();
                vm.fnSearch();
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loaderBtnSave = false; 
            });     
    }

    function _fnUpdate(sector) {
        Sector.update(sector)
            .then(function(resp) {
                growl.success("Setor atualizado com sucesso!");
                vm.fnCloseSectorForm();
                vm.fnSearch(vm.filter.name);
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loaderBtnSave = false; 
            });
    }

    vm.fnDelete = function(sector) {
        vm.cd_setor = sector.cd_setor;
        vm.loaderBtnDelete = true;

        Sector.delete(sector)
            .then(function(resp) {
                growl.success("<span class='glyphicon glyphicon-trash growl-trash'></span> Setor <strong style='color: red'>excluído</strong> com sucesso!", {disableIcons: true});
                vm.fnSearch(vm.filter.name);
            })
            .catch(function(err) {
                if (err.status = 422) {
                    growl.warning("Setor não pode ser excluído, tem pessoas vinculadas a ele.");
                } else {
                    Util.treatError(err);
                }
            })
            .finally(function() {
                vm.loaderBtnDelete = false;
            });
    }

    //Card

    function _fnAvailableCard() {
        filter = {available: 'S', pagination: 'ALL', sort_type: 'ASC'};

        Card.read(filter)
            .then(function(resp) {
                vm.availableCards = resp;
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {               
                vm.loader = false;
            });
    }

    vm.fnAddCard = function(card) {
        cardTemp = angular.copy(card);
        cardTemp.operacao = 'C';

        vm.availableCards = vm.availableCards.filter(function(insideCard) { return insideCard != card});
        vm.sector.cartoes.push(cardTemp);
        _mountCardManipulation(cardTemp);

        _sortCards(vm.sector.cartoes);
    }

    vm.fnToRemove = function(card) {
        cardTemp = angular.copy(card);
        cardTemp.operacao = 'D';
        
        vm.sector.cartoes = vm.sector.cartoes.filter(function(insideCard) { return insideCard != card});
        vm.availableCards.push(cardTemp);
        _mountCardManipulation(cardTemp);

        _sortCards(vm.availableCards);
    }

    function _mountCardManipulation(card) {
        _cardManipulation = _cardManipulation.filter(function(insideCard) { return insideCard.nr_cartao != card.nr_cartao});
        _cardManipulation.push(card);
    }

    function _sortCards(cards) {
        cards = cards || [];

        cards.sort(function(cardOld, carNew) {
            return cardOld.nr_cartao - carNew.nr_cartao;
        })
    }

    /*
        Functions utils
    */

   vm.fnStatusNew = function() {
        vm.statusNewAndUpdate = true;
        _statusNewRegister = true;

        vm.fnToCleanSector();
        _fnAvailableCard();
    }

    vm.fnStatusUpdate = function(sector) {
        vm.sector = angular.copy(sector);
        vm.statusNewAndUpdate = true;
        _statusNewRegister = false;

        _fnAvailableCard();

        $('html, body').animate({scrollTop:0}, 'slow');
    }

    vm.fnCloseSectorForm =  function() {
        vm.statusNewAndUpdate = false;
        vm.fnToCleanSector();
    }

    vm.fnToCleanSector = function() {
        vm.sector = {ds_setor: undefined, cartoes: []};
        _cardManipulation = [];
    }

    vm.fnToCleanSectorBtn = function() {
        if (_statusNewRegister) {
            //Register
            vm.sector.cartoes.forEach(function(insideCard) {vm.availableCards.push(insideCard);});
            _sortCards(vm.availableCards);
            vm.fnToCleanSector();
        } else {
            //Update
            vm.sector.cartoes.forEach(function(insideCard) {
                insideCard.operacao = 'D';
                vm.availableCards.push(insideCard);
                _mountCardManipulation(insideCard);
            });
            _sortCards(vm.availableCards);
            vm.sector.ds_setor = undefined;
            vm.sector.cartoes = [];
        }
    }

    /*
        Functions init
    */

    vm.fnSearch();
});