app

.factory("Category", function($q, $http, $sessionStorage, Url, Login) {
    var config = {
        headers: {
            Authorization: ''
        }
    };

    function _read(filter) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.category,
                    prefix = '?';
                filter = filter || {};
                
                config.headers.Authorization = $sessionStorage.session.token;
                        
                if (filter.hasOwnProperty('cd_tipo_categoria')) {
                    url += prefix + 'cd_tipo_categoria=' + filter['cd_tipo_categoria'];
                    prefix = '&';
                }
        
                url += prefix + 'fields=cd_categoria,ds_categoria&sort_field=ds_categoria';
        
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