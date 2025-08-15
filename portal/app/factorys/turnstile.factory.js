app

.factory("Turnstile", function($q, $http, $sessionStorage, Login, Url) {
    var config = {
        headers: {
            Authorization: ''
        }
    };

    function _read(filter) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.turnstile + '/access/';
                var prefix = '?';
                filter = filter || {};

                config.headers.Authorization = $sessionStorage.session.token;

                if (filter.hasOwnProperty('dt_inicio') && filter.hasOwnProperty('dt_final')) {
                    url += filter.dt_inicio.format('DD/MM/YYYY') + '/' + filter.dt_final.format('DD/MM/YYYY');
                }

                for (key in filter) {
                    switch (key) {
                        case 'cd_horario':
                        case 'cd_setor':
                        case 'op_tempo':
                        case 'tm_dentro':
                        case 'nr_cracha_cartao':
                        case 'nome':
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

    return {
        read: _read
    };
});