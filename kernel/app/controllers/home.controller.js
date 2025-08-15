app

.controller("homeCtrl", function($scope, $sessionStorage, $q, Session, System, Util) {
    var vm = this;

    vm.loader = false;
    vm.amount = {logados: 0, acessos: 0};
    vm.systems = [];

    /*
        Function
    */

    function _loadHome() {
        vm.loader = true;
        var session =  Session.amount().then(function(resp) {vm.amount = resp;});
            system = System.read().then(function(resp) {vm.systems = resp;});

        $q.all([session, system])    
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {               
                vm.loader = false;
            });
    }

    /*
        Init 
    */
    
    $scope.$on("authentication", function(event, args) {
        if (args.auth) {
            _loadHome();
        } 
    });
    
    if ($sessionStorage.session.hasOwnProperty('token')) {
        _loadHome();
    }
});