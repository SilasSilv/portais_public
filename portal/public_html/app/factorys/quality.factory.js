app

.factory("Quality", function ($q, $http, $sessionStorage, Login, Url) {
    var config = {
        headers: {
            Authorization: ''
        }
    };

    function _create(qualidade) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.quality;
                
                config.headers.Authorization = $sessionStorage.session.token;

                $http.post(url, qualidade, config)
                    .then(function (resp) {
                        deferred.resolve(resp.data);
                    })
                    .catch(function (err) {
                        deferred.reject(err);
                    });
            })
            .catch(function(err) {
                deferred.reject(err);
            });              

        return deferred.promise;
    }

    function _statistics(data) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.quality + '/statistics/';
                url += data.format('MM/YYYY');
                
                config.headers.Authorization = $sessionStorage.session.token;
                
                $http.get(url, config)
                    .then(function (resp) {
                        deferred.resolve(resp.data);
                    })
                    .catch(function (err) {
                        deferred.reject(err);
                    });
            })
            .catch(function(err) {
                deferred.reject(err);
            }); 
        
        return deferred.promise;
    }

    return {
        create: _create,
        statistics: _statistics
    }
});