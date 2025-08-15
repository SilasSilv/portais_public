app

.factory("Card", function($q, $http, $sessionStorage, Login, Url) {
    var config = {
        headers: {
            Authorization: ''
        }
    };

    function _create(card) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.card;

                config.headers.Authorization = $sessionStorage.session.token;

                $http.post(url, card, config)
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
                var url = Url.card;
                var prefix = '?';
                filter = filter || {};

                config.headers.Authorization = $sessionStorage.session.token;

                url += filter.nr_cartao ? '/' + filter.nr_cartao : '';

                for (key in filter) {
                    switch (key) {
                        case 'available':
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

    function _update(card) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.card + '/' + card.nr_cartao_url;

                config.headers.Authorization = $sessionStorage.session.token;

                $http.put(url, card, config)
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

    function _delete(card) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.card + '/' + card.nr_cartao;

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