app

.controller("sessionCtrl", function(Session, Login, Util) {
    var vm = this;

    vm.loader = false;
    vm.loaderBtn = false;
    vm.nr_sequencia = 0;
    vm.sessions = [];

    /*
        Function
    */

    function _find() {
        vm.loader = true;

        Session.read({logado: "S"})
            .then(function(resp) {
                vm.sessions = resp;
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {               
                vm.loader = false;
            });
    }

    vm.fnToClose = function(nr_sequencia, index) {
        vm.loaderBtn = true;
        vm.nr_sequencia = nr_sequencia;

        Session.close(nr_sequencia)
            .then(function(resp) {
                return Login.me();
            })
            .then(function(resp) {
                vm.sessions.splice(index, 1);
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {               
                vm.loaderBtn = false;
            });
    }

    /*
        Init
    */

    _find();
});