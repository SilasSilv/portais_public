app

.factory("System", function($q, $http, $sessionStorage, Login, Url) {
    var config = {
        headers: {
            Authorization: ''
        }
    };

    function _read(filter) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.system;
                var prefix = '?'
                filter = filter || {};

                config.headers.Authorization = $sessionStorage.session.token;

                for (key in filter) {
                    switch (key) {
                        case 'nm_sistema':
                        case 'pagination':
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

    function _id() {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.system + '/id/';
                var prefix = '?'

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

    function _create(system) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.system;
                system = system || {};
                system = _transformsFormData(system);
                
                $http({
                    method: 'post',
                    url: url,
                    data: system,                   
                    headers: {'Content-Type': undefined, 'Authorization': $sessionStorage.session.token}
                })
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

    function _update(system) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.system + '/' + system.cd_sistema;
                system = system || {};
                system = _transformsFormData(system);
                
                $http({
                    method: 'post',
                    url: url,
                    data: system,                   
                    headers: {'Content-Type': undefined, 'Authorization': $sessionStorage.session.token}
                })
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

    function _delete(cd_sistema) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.system + '/' + cd_sistema;

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

    /*
        Utils
    */

    function _transformsFormData(system) {
        var payload = new FormData();

        payload.append("cd_token", system.cd_token);
        payload.append("nm_sistema", system.nm_sistema);
        payload.append("ds_sistema", system.ds_sistema);
        payload.append("ie_situacao", system.ie_situacao);

        if (system.hasOwnProperty("img_logo")) {
            payload.append("img_logo", system.img_logo);
        }        

        return payload;
    }

    return {
        read: _read,
        id: _id,
        create: _create,
        update: _update,
        delete: _delete
    };
});