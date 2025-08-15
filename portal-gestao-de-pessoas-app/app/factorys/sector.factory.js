app

.factory("Sector", function($q, $http, $sessionStorage, Login, Url) {
    var config = {
        headers: {
            Authorization: ''
        }
    };

    function _create(sector) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.sector;

                config.headers.Authorization = $sessionStorage.session.token;

                $http.post(url, sector, config)
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

    function _read(filter) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.sector;
                var prefix = '?';
                filter = filter || {};

                config.headers.Authorization = $sessionStorage.session.token;

                for (key in filter) {
                    switch (key) {
                        case 'ds_setor':
                        case 'pagination':
                        case 'sort_type':
                            url += prefix + key + '=' + filter[key];
                            prefix = '&';
                    } 
                }

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

    function _update(sector) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.sector + '/' + sector.cd_setor;

                config.headers.Authorization = $sessionStorage.session.token;

                $http.put(url, sector, config)
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

    function _delete(sector) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.sector + '/' + sector.cd_setor;

                config.headers.Authorization = $sessionStorage.session.token;

                $http.delete(url, config)
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
        update: _update,
        delete: _delete
    };
});