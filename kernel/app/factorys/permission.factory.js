app

.factory("Permission", function($q, $http, $sessionStorage, Login, Url) {
    var config = {
        headers: {
            Authorization: ''
        }
    };

    // Permission

    function _read(filter, isGroup) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.permissionSystem;
                var prefix = '?'
                filter = filter || {};

                config.headers.Authorization = $sessionStorage.session.token;

                if (isGroup) {
                    url += '/group';
                }

                if (filter.hasOwnProperty('cd_sistema')) {
                    url += '/' + filter.cd_sistema;
                }

                for (key in filter) {
                    switch (key) {
                        case 'ds_titulo':
                        case 'pagination':
                        case 'ie_situacao':
                        case 'cd_permissao':
                        case 'cd_tipo_permissao':
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

    function _create(permission) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.permissionSystem;
                permission = permission || {};

                config.headers.Authorization = $sessionStorage.session.token;
                
                $http.post(url, permission, config)
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

    function _registerImage(permission, cd_permissao) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.permissionSystem + '/image/' + cd_permissao;
                var payload = new FormData();
                
                payload.append("logo_alternativo_acesso", permission.logo_alternativo_acesso[0]);

                $http({
                    method: 'post',
                    url: url,
                    data: payload,                   
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

    function _update(permission) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.permissionSystem + '/' + permission.cd_permissao;
                permission = permission || {};

                config.headers.Authorization = $sessionStorage.session.token;
                
                $http.put(url, permission, config)
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

    function _delete(cd_permissao) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.permissionSystem + '/' + cd_permissao;

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

    function _deleteImage(cd_permissao) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.permissionSystem + '/image/' + cd_permissao;
               
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

    // Permission People

    function _readPeople(filter) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.permissionSystem + '/' + filter.cd_sistema + '/people';
                var prefix = '?'
                filter = filter || {};

                config.headers.Authorization = $sessionStorage.session.token;

                if (filter.hasOwnProperty('cd_permissao')) {
                    url += '/' + filter.cd_permissao;
                }

                for (key in filter) {
                    switch (key) {
                        case 'nr_cracha':                 
                        case 'pagination':                        
                        case 'nm_pessoa_fisica':
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

    function _grantPeople(permission) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.permission + '/' + permission.nr_cracha + '/people/' + permission.cd_permissao + '/' + permission.vl_pf;

                config.headers.Authorization = $sessionStorage.session.token;

                $http.post(url, {}, config)
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

    function _grantUpdatePeople(permission) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.permission + '/' + permission.nr_cracha + '/people/' + permission.cd_permissao + '/' + permission.vl_pf;

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

    function _revokePeople(permission) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.permission + '/' + permission.nr_cracha + '/people/' + permission.cd_permissao;
              
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

    // Permission Sector

    function _readSector(filter) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.permissionSystem + '/' + filter.cd_sistema + '/sector';
                var prefix = '?'
                filter = filter || {};

                config.headers.Authorization = $sessionStorage.session.token;

                if (filter.hasOwnProperty('cd_permissao')) {
                    url += '/' + filter.cd_permissao;
                }

                for (key in filter) {
                    switch (key) {
                        case 'cd_setor':                 
                        case 'pagination':                        
                        case 'ds_setor':
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

    function _grantSector(permission) {
        var deferred = $q.defer();
        
        Login.refreshSession()
            .then(function(resp) {
                var url = Url.permission + '/' + permission.cd_setor + '/sector/' + permission.cd_permissao + '/' + permission.vl_setor;

                config.headers.Authorization = $sessionStorage.session.token;

                $http.post(url, {}, config)
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

    function _grantUpdateSector(permission) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.permission + '/' + permission.cd_setor + '/sector/' + permission.cd_permissao + '/' + permission.vl_setor;

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

    function _revokeSector(permission) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                var url = Url.permission + '/' + permission.cd_setor + '/sector/' + permission.cd_permissao;
              
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
        registerImage: _registerImage,
        update: _update,
        delete: _delete,
        deleteImage: _deleteImage,
        readPeople: _readPeople,
        grantPeople: _grantPeople,
        grantUpdatePeople: _grantUpdatePeople,
        revokePeople: _revokePeople,
        readSector: _readSector,
        grantSector: _grantSector,
        grantUpdateSector: _grantUpdateSector,
        revokeSector: _revokeSector
    };
});