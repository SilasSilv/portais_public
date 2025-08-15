app

.factory("Session", function($q, $http, $sessionStorage, Login, Url) {
    var config = {
        headers: {
            Authorization: ''
        }
    };

    function _read(filter) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.session;
                var prefix = '?'
                filter = filter || {};

                config.headers.Authorization = $sessionStorage.session.token;

                for (key in filter) {
                    switch (key) {
                        case 'logado':
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

    function _amount() {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.session + '/amount/';
               
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

    function _close(nr_sequencia) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.session + '/' + nr_sequencia + '/close';

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
        read: _read,
        amount: _amount,
        close: _close
    };
});