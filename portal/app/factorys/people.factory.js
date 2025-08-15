app

.factory("People", function($q, $http, $sessionStorage, Login, Url) {
    var config = {
        headers: {
            Authorization: ''
        }
    };

    function _read(filter) {
        var deferred = $q.defer();
        
        Login.refreshSession()
            .then(function(resp) {
                var url = Url.people;
                var prefix = '?';
                filter = filter || {};
    
                config.headers.Authorization = $sessionStorage.session.token;
                
                if (filter.hasOwnProperty('crachaNome')) {
                    if (/^[0-9]*$/.exec(filter['crachaNome']) &&
                        filter['crachaNome'].trim().length > 0) {
                        url += '/' + filter['crachaNome'];
                    } else if (/^([\wÀ-ú]| )*$/.test(filter['crachaNome']) &&
                                filter['crachaNome'].trim().length > 0) {
                        url += prefix + 'nm_pessoa_fisica=' + filter['crachaNome'];
                        prefix = '&';
                    } else {
                        url += '/0';
                    }
                } else {
                    url += '/0';
                }
    
                url += prefix + 'fields=url_foto_perfil,nr_cracha,nm_pessoa_fisica,ds_setor,ds_cargo';
    
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

    function _updatePassword(altSenha) {
        var deferred = $q.defer();
        
        Login.refreshSession()
            .then(function(resp) {
                var url = Url.people + '/password/';
                
                config.headers.Authorization = $sessionStorage.session.token;
    
                $http.put(url, altSenha, config)
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
        read: _read,
        updatePassword: _updatePassword
    };
});