app

.factory("Office", function($q, $http, $sessionStorage, Login, Url) {
    var config = {
        headers: {
            Authorization: ''
        }
    };

    function _create(office) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.office;

                config.headers.Authorization = $sessionStorage.session.token;

                $http.post(url, office, config)
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
                var url = Url.office;
                var prefix = '?'
                filter = filter || {};

                config.headers.Authorization = $sessionStorage.session.token;

                for (key in filter) {
                    switch (key) {
                        case 'ds_cargo':
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

    function _update(office) {
        var deferred = $q.defer();
        
        Login.refreshSession()
            .then(function(resp) {
                var url = Url.office + '/' + office.cd_cargo;

                config.headers.Authorization = $sessionStorage.session.token;

                $http.put(url, office, config)
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

    function _delete(office) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.office + '/' + office.cd_cargo;

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