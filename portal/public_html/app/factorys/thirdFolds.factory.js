app

.factory("ThirdFolds", function ($q, $http, $sessionStorage, Login, Url) {
    var config = {
        headers: {
            Authorization: ''
        }
    };

    function _create(terceiro) {
        var deferred = $q.defer();
        
        Login.refreshSession()
            .then(function(resp) {
                var url = Url.thirdFolds;
                
                config.headers.Authorization = $sessionStorage.session.token;
        
                $http.post(url, terceiro, config)
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

    function _read(filter, typeUrl) {
        var deferred = $q.defer();
        filter = filter || {};
        typeUrl = typeUrl || '';
        
        Login.refreshSession()
            .then(function(resp) {
                var url = Url.thirdFolds,
                    prefix = '?';

                if (typeUrl == 'free') {
                    url += '/free/';
                }

                for (key in filter) {
                    switch (key) {
                        case 'skip_line':
                        case 'dt_inicio':
                        case 'dt_fim':
                        case 'ie_terceiro_dobra':
                            url += prefix + key + '=' + filter[key];
                            prefix = '&';
                    } 
                }
                
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

    function _update(terceiro) {        
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.thirdFolds + '/' + terceiro.nr_sequencia;
                
                config.headers.Authorization = $sessionStorage.session.token;
        
                $http.put(url, terceiro, config)
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

    function _delete(terceiro) {        
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.thirdFolds + '/' + terceiro.nr_sequencia;
                
                config.headers.Authorization = $sessionStorage.session.token;
        
                $http.delete(url, config)
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
        read: _read,
        update: _update,
        delete: _delete
    };
});