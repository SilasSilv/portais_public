app

.directive('fileModel', ['$parse', function ($parse) {
    return {
        restrict: 'A',
        link: function(scope, element, attrs) {                                      
            element.bind('change', function(event) {                                  
                $parse(attrs.fileModel).assign(scope, event.target.files);
                scope.$apply();
            });
        }
    }
}]);