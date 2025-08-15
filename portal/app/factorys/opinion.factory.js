app

.factory("Opinion", function ($q, $http, $sessionStorage, Login, Url) {
    var config = {
        headers: {
            Authorization: ''
        }
    };

    function _create(opiniao) {
        var deferred = $q.defer();
        
        Login.refreshSession()
            .then(function(resp) {
                var url = Url.opinion;
                
                config.headers.Authorization = $sessionStorage.session.token;
        
                $http.post(url, opiniao, config)
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

    function _read(filter) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.opinion;
                var prefix = '?';
                filter = filter || {};
        
                config.headers.Authorization = $sessionStorage.session.token;
        
                if (filter.hasOwnProperty('cd_tipo_categoria')) {
                    url += prefix + 'cd_tipo_categoria=' + filter['cd_tipo_categoria'];
                    prefix = '&';
                }
        
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

    function _readOpinion(opinion) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.opinion + '/read/' + opinion.nr_sequencia;
                
                config.headers.Authorization = $sessionStorage.session.token;
        
                $http.put(url, {}, config)
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
        create: _create,
        read: _read,
        readOpinion: _readOpinion
    };
});

