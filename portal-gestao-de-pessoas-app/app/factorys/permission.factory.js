app

.factory("Permission", function($q, $http, $sessionStorage, Login, Url) {
    var config = {
        headers: {
            Authorization: ''
        }
    };

    function _persist(permissions, nr_cracha) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.permission + '/' + nr_cracha + '/people';

                config.headers.Authorization = $sessionStorage.session.token;

                $http.post(url, {permissoes: permissions}, config)
                    .then(function(resp) {
                        deferred.resolve(resp.data);
                    })
                    .catch(function(err) {
                        deferred.reject(err);
                    });
            })
            .catch(function(err) {
                deferred.reject(err);
            });

        return deferred.promise;
    }

    function _read(nr_cracha) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.permission + '/' + nr_cracha + '/people?ie_situacao=A&ie_situacao_sis=A';

                config.headers.Authorization = $sessionStorage.session.token;
                
                $http.get(url, config)
                    .then(function(resp) {
                        deferred.resolve(resp.data);
                    })
                    .catch(function(err) {
                        deferred.reject(err);
                    });                
            })
            .catch(function(err) {
                deferred.reject(err);
            });

        return deferred.promise; 
    }

    return {
        persist: _persist,
        read: _read
    };
});