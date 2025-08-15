app

.factory("CategoryType", function($q, $http, $sessionStorage, Login, Url) {
    var config = {
        headers: {
            Authorization: ''
        }
    };

    function _read(filter) {
        var deferred = $q.defer(),
            url = Url.categoryType,
            prefix = '?';
        filter = filter || {};

        config.headers.Authorization = $sessionStorage.session.token;

        for (key in filter) {
            switch (key) {
                case 'ds_tipo_categoria':
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

        return deferred.promise;
    }

    function _create(category_type) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.categoryType;
                category_type = category_type || {};

                config.headers.Authorization = $sessionStorage.session.token;
                
                $http.post(url, category_type, config)
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

    function _update(category_type) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.categoryType + category_type.cd_tipo_categoria;
                category_type = category_type || {};

                config.headers.Authorization = $sessionStorage.session.token;
                
                $http.put(url, category_type, config)
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

    function _delete(cd_tipo_categoria) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.categoryType + cd_tipo_categoria;

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
        read: _read,
        create: _create,
        update: _update,
        delete: _delete
    };
});