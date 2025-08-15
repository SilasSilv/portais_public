app

.factory("Meal", function ($http, $q, $sessionStorage, Login, Url) {
    var config = {
        headers: {
            Authorization: ''
        }
    };

    function _create(refeicao) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.meal;
                
                config.headers.Authorization = $sessionStorage.session.token;
        
                if (refeicao.dt_refeicao instanceof moment) {
                    refeicao.dt_refeicao = refeicao.dt_refeicao.format('DD/MM/YYYY');            
                }
        
                if (refeicao.dt_inicio instanceof moment) {
                    refeicao.dt_inicio = refeicao.dt_inicio.format('DD/MM/YYYY HH:mm:ss'); 
                }
        
                if (refeicao.dt_final instanceof moment) {
                    refeicao.dt_final = refeicao.dt_final.format('DD/MM/YYYY HH:mm:ss');
                }
                
                $http.post(url, refeicao, config)
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
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.meal,
                    prefixo = '?';
                filter = filter || {};

                config.headers.Authorization = $sessionStorage.session.token;

                if (filter.hasOwnProperty('nr_refeicao')) {
                    url += '/' + filter.nr_refeicao;
                }

                Object.keys(filter)
                    .forEach(function (key) {
                        switch (key) {
                            case 'crachaOuNome':
                                if (filter[key].trim().length > 0) {
                                    if (/^[0-9]*$/.exec(filter[key])) {
                                        url += prefixo + 'nr_cracha=' + filter[key];
                                    } else if (/^([A-z]| )*$/.test(filter[key])) {
                                        url += prefixo + 'nm_pessoa_fisica=' + filter[key];
                                    } else {
                                        url += prefixo + 'nr_cracha=0';
                                    }

                                    prefixo = '&';
                                }

                                break;
                            case 'data':
                                if (filter[key] instanceof Date) {
                                    var data = ('0' + filter[key].getDate()).slice(-2) + '/' + ('0' + (filter[key].getMonth() + 1)).slice(-2) + '/' + filter[key].getFullYear();
                                    url += prefixo + 'dt_inicio=' + data + '&dt_fim=' + data;

                                    prefixo = '&';
                                }

                                break;                                                            
                            case 'tbRefeicao':
                                if (filter[key].val == 'A') {
                                    url += prefixo + 'ie_tipo_refeicao=A';
                                } else if (filter[key].val == 'J') {
                                    url += prefixo + 'ie_tipo_refeicao=J';
                                };
                                
                                prefixo = filter[key].val != 'A' ? '&' : '?';
                        }
                    });

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
                var url = Url.meal + '/' + refeicao.nr_refeicao;
                
                config.headers.Authorization = $sessionStorage.session.token;
        
                if (refeicao.dt_refeicao instanceof moment) {
                    refeicao.dt_refeicao = refeicao.dt_refeicao.format('DD/MM/YYYY');
                }
        
                if (refeicao.dt_inicio instanceof moment) {
                    refeicao.dt_inicio = refeicao.dt_inicio.format('DD/MM/YYYY HH:mm:ss');
                }
        
                if (refeicao.dt_final instanceof moment) {
                    refeicao.dt_final = refeicao.dt_final.format('DD/MM/YYYY HH:mm:ss');
                }
        
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

    function _delete(nr_sequencia) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.meal + '/' + nr_sequencia;
                
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
    };
    
    return {
        read: _read,
        create: _create,
        update: _update,
        delete: _delete
    };
});