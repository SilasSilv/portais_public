app

.factory("Login", function ($http, $q, $sessionStorage, Url) {
    var config = {
        headers: {
            Authorization: ''
        }
    };

    function _login(user) {
        var deferred = $q.defer();
        var url = Url.login;
                        
        config.headers.Authorization = '34b1477e-a6f7-4558-9729-1136306285bb';

        $http.post(url, user, config)
                .then(function (resp) {
                    $sessionStorage.user = resp.data.user;
                    $sessionStorage.session = resp.data.session;
                    $sessionStorage.permissions = resp.data.permissions;

                    deferred.resolve(resp.data);
                })
                .catch(function (err) {
                    deferred.reject(err);
                });

        return deferred.promise;
    }
    
    function _logout() {
        var deferred = $q.defer();

        config.headers.Authorization = $sessionStorage.session.token;

        $http.put(Url.logout, {}, config)
            .then(function(resp) {
                $sessionStorage.$reset();
                deferred.resolve(resp);
            })
            .catch(function(err) {
                deferred.reject(err);
            });

        return deferred.promise;
    }

    function _refreshSession() {
        var deferred = $q.defer();

        if (typeof $sessionStorage.session == "object") {
            var dtNow = moment(),
                dtExpire = moment($sessionStorage.session.expire, 'DD/MM/YYYY HH:mm:ss'),
                verifySessionExpired = moment(dtNow).isBetween(dtExpire, moment(dtExpire).add(5, 'm')),
                url = Url.login + '/' + $sessionStorage.session.refresh_token;

            config.headers.Authorization = $sessionStorage.session.token;
            
            if (verifySessionExpired) {
                $http.put(url, {}, config)
                    .then(function(resp) {
                        $sessionStorage.session = resp.data.session;
                        deferred.resolve(resp);
                    })
                    .catch(function(err) {
                        deferred.reject(err);
                    });
            } else {
                deferred.resolve('OK');
            }
        } else {
            deferred.reject({status: 403});
        } 
        
        return deferred.promise;
    }
    
    function _me(filter) {       
        var deferred = $q.defer();
        var url = Url.login + '/ME';
        filter = filter || {};
        $sessionStorage.session = $sessionStorage.session || {};

        config.headers.Authorization = filter.token || $sessionStorage.session.token || "";

        if (filter.hasOwnProperty('cd_permissao')) {
            url += '?cd_permissao=' + filter.cd_permissao;
        }   

        $http.get(url, config)
            .then(function(resp) {
                $sessionStorage.user = resp.data.user;
                $sessionStorage.session = resp.data.session;
                $sessionStorage.permissions = resp.data.permissions;

                deferred.resolve(resp.data);
            })
            .catch(function(err) {
                deferred.reject(err);
            });

        return deferred.promise;
    }

    function _findSystemsAccess() {
        var deferred = $q.defer();

        _refreshSession()
            .then(function(resp) {
                var url = Url.login + '/system';

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

    function _changeOfSystem(system) {
        var deferred = $q.defer();

        _refreshSession()
            .then(function(resp) {
                var url = Url.login + '/system/' + system.cd_sistema + '/' + system.cd_permissao;

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
        me: _me,
        login: _login,
        logout: _logout,
        refreshSession: _refreshSession,
        changeOfSystem: _changeOfSystem,
        findSystemsAccess: _findSystemsAccess
    }
});