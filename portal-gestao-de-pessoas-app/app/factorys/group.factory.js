app

.factory("Group", function($q, $http, $sessionStorage, Login, Url) {
    var config = {
        headers: {
            Authorization: ''
        }
    };

    function _create(group) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.group;

                config.headers.Authorization = $sessionStorage.session.token;

                $http.post(url, group, config)
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
                var url = Url.group;
                var prefix = '?';
                filter = filter || {};

                config.headers.Authorization = $sessionStorage.session.token;

                for (key in filter) {
                    switch (key) {
                        case 'nm_grupo':
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

    function _update(group) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.group + '/' + group.cd_grupo;

                config.headers.Authorization = $sessionStorage.session.token;

                $http.put(url, group, config)
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

    function _delete(group) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.group + '/' + group.cd_grupo;

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