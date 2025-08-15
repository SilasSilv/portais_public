app

.controller("homeCtrl", function($scope, $q, $sessionStorage, $location, People, Util) {
    var vm = this;

    vm.loader = false;
    vm.peoples = [];
    vm.amounts = [];
    
    /*
        Functions CRUD
    */

    //People

    vm.fnMountPageHome = function() {
        vm.loader = true;
        var peopleList = People.read({sort_field: 'dt_inclusao', sort_type: 'DESC', number_line: 5}).then(function(resp) {vm.peoples = resp;}),
            peopleAmount = People.readStatisticAmount().then(function(resp) {vm.amounts = resp;});

        $q.all([peopleList, peopleAmount])
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loader = false;  
            });
    }

    /*
        Functions Utils
    */

    vm.fnGoToTheConsultation = function(nr_cracha) {
        $location.path('/gestao-pessoas/funcionario/consultar').search("nr_cracha", nr_cracha);
    } 

    /*
        Functions init
    */

    $scope.$on("authentication", function(event, args) {
        if (args.auth) {
            vm.fnMountPageHome();
        } 
    });

    if ($sessionStorage.session.hasOwnProperty('token')) {
        vm.fnMountPageHome();
    }
});