app

.factory("Menu", function ($q, $http, $sessionStorage, Login, Url) {
    var config = {
        headers: {
            Authorization: ''
        }
    };

    function _create(refeicao) {
        var deferred = $q.defer();
        
        Login.refreshSession()
            .then(function(resp) {
                var url = Url.menuRequest + refeicao.nr_refeicao;
                
                config.headers.Authorization = $sessionStorage.session.token;
                        
                $http.post(url, {}, config)
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
        var deferred = $q.defer(),
            url = Url.menu,
            prefix = '?';
        filter = filter || {};

        config.headers.Authorization = '4b348ba0-ccb1-4ae1-89a4-6b49effa8249';

        for (key in filter) {
            switch (key) {
                case 'dobras_terceiros':
                    url += prefix + key + '=' + filter[key];
                    prefix = '&';
            } 
        }

        $http.get(url, config)
            .then(function (resp) {
                deferred.resolve(resp.data);
            })
            .catch(function (err) {
                deferred.reject(err);
            });

        return deferred.promise;
    }

    function _readRequest() {
        var deferred = $q.defer();
        
        Login.refreshSession()
            .then(function(resp) {
                var url = Url.menuRequest;
                
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
    
    function _update(refeicao) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.menuRequest + refeicao.nr_refeicao;
                
                config.headers.Authorization = $sessionStorage.session.token;
        
                $http.put(url, refeicao, config)
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

    function _delete(nr_refeicao) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.menuRequest + nr_refeicao;
                
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
        readRequest: _readRequest,
        update: _update,
        delete: _delete
    };
});