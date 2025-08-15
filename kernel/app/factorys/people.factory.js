app

.factory("People", function($q, $http, $sessionStorage, Login, Url) {
    var config = {
        headers: {
            Authorization: ''
        }
    };

    function _create(people) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.people;

                config.headers.Authorization = $sessionStorage.session.token;
                
                $http.post(url, people, config)
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
                var url = Url.people + '/free/';
                var prefix = '?';
                filter = filter || {};
    
                config.headers.Authorization = $sessionStorage.session.token;
                
                if (filter.hasOwnProperty('crachaNome')) {
                    if (/^[0-9]*$/.exec(filter['crachaNome']) &&
                        filter['crachaNome'].trim().length > 0) {
                        url += filter['crachaNome'];
                    } else if (/^([\wÀ-ú]| )*$/.test(filter['crachaNome']) &&
                                filter['crachaNome'].trim().length > 0) {
                        url += prefix + 'nm_pessoa_fisica=' + filter['crachaNome'];
                        prefix = '&';
                    } else {
                        url += '0';
                    }
                }               
                
                for (key in filter) {
                    switch (key) {
                        case 'pagination':
                            number_line = filter[key].split(",")[1];
                            skip_line = filter[key].split(",")[0];
                            url += prefix + 'number_line=' + number_line + '&skip_line=' + skip_line;
                            prefix = '&';
                            break;
                        case 'sort_type':
                        case 'sort_field':
                        case 'number_line':
                        case 'ie_situacao':
                            url += prefix + key + '=' + filter[key];
                            prefix = '&';
                    } 
                }

                if (filter.hasOwnProperty('fields')) {
                    if (filter.fields != '*') {
                        url += prefix + 'fields=' + filter.fields;
                    }                     
                } else {
                    url += prefix + 'fields=url_foto_perfil,nr_cracha,nm_pessoa_fisica,ds_setor,ds_cargo,ds_mail,ie_situacao';
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

    function _readStatisticAmount() {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.people + "/statistic/amount";

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

    function _update(people, nr_cracha) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.people + "/" + nr_cracha;

                config.headers.Authorization = $sessionStorage.session.token;
                
                $http.put(url, people, config)
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
 
    function _delete(nr_cracha) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.people + "/" + nr_cracha;

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
        create: _create,
        read: _read,
        readStatisticAmount: _readStatisticAmount,
        update: _update,
        delete: _delete,
        updatePassword: _updatePassword
    };
});