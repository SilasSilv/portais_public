app

.factory("Analyze", function($q, $http, $sessionStorage, Login, Url) {
  var config = {
    headers: {
      Authorization: ''
    }
  };

  function _publicSynthetic(filter) {
    var deferred = $q.defer();

    Login.refreshSession()
      .then(function(resp) {
        var url = Url.analyzePublicSynthetic;
        var prefix = '?';
        
        config.headers.Authorization = $sessionStorage.session.token;
    
        url += '/' + filter.dt_inicio.format('DD/MM/YYYY') + '/' +filter.dt_final.format('DD/MM/YYYY');

        if (filter.hasOwnProperty('skip_line')) {
          url += prefix + 'skip_line=' + filter.skip_line;
          prefix = '&';
        }
    
        if (filter.hasOwnProperty('ie_tipo_refeicao')) {
          url += prefix + 'ie_tipo_refeicao=' + filter.ie_tipo_refeicao;
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

  function _publicAnalytical(nr_refeicao) {
    var deferred = $q.defer();

    Login.refreshSession()
      .then(function(resp) {
        var url =  Url.analyzePublicAnalytical;
        
        config.headers.Authorization = $sessionStorage.session.token;
    
        url += '/' + nr_refeicao;            

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

  function _publicAnalyticalFree(params) {
    var deferred = $q.defer(),
		url =  Url.analyzePublicAnalytical;
	
	url += '/' + params.dt_refeicao.format('DD/MM/YYYY') + '/' + params.dt_refeicao.format('DD/MM/YYYY');
	config.headers.Authorization = 'a9736142-6824-42bd-96b8-64e738d663f6';

	$http.get(url, config)
		.then(function(resp) {
			deferred.resolve(resp.data);
		})
		.catch(function(err) {
			deferred.reject(err);
		});

    return deferred.promise;    
  }

  function _private(filter) {
    var deferred = $q.defer();
    
    Login.refreshSession()
      .then(function(resp) {
        var url = Url.analyzePrivate;
        var prefix = '?';    
        
        config.headers.Authorization = $sessionStorage.session.token;
    
        url += '/' + filter.dt_inicio.format('DD/MM/YYYY') + '/' +filter.dt_final.format('DD/MM/YYYY');

        if (filter.hasOwnProperty('skip_line')) {
          url += prefix + 'skip_line=' + filter.skip_line;
          prefix = '&';
        }
    
        if (filter.hasOwnProperty('ie_tipo_refeicao')) {
          url += '?ie_tipo_refeicao=' + filter.ie_tipo_refeicao;
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

  function _payroll(filter) {
    var deferred = $q.defer();
    
    Login.refreshSession()
      .then(function(resp) {
        var url = Url.analyzePayroll;
        var prefix = '?'
    
        config.headers.Authorization = $sessionStorage.session.token;
    
        url += '/' + filter.dt_inicio.format('DD/MM/YYYY') + '/' +filter.dt_final.format('DD/MM/YYYY');
    
        if (filter.hasOwnProperty('ie_tipo_refeicao')) {
          url += prefix + 'ie_tipo_refeicao=' + filter.ie_tipo_refeicao;
          prefix = '&'
        }
    
        if (filter.hasOwnProperty('cracha_ou_nome')) {
          url += prefix + 'cracha_ou_nome=' + filter.cracha_ou_nome;
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
    publicSynthetic: _publicSynthetic,
	publicAnalytical: _publicAnalytical,
	publicAnalyticalFree: _publicAnalyticalFree,
    private: _private,
    payroll: _payroll
  };
});