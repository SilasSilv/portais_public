app

.factory("Price", function($http, $q, $sessionStorage, Login, Url) {
	var config = {
		headers: {
			Authorization: ''
		}
	};

	function _create(price) {
		var deferred = $q.defer(); 
			  
		Login.refreshSession()
        	.then(function(resp) {
				var url = Url.price;

				config.headers.Authorization = $sessionStorage.session.token;

				$http.post(url, price, config)
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

	function _read() {
		var deferred = $q.defer(); 

		Login.refreshSession()
            .then(function(resp) {
				var url = Url.price;

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

	function _update(price) {
		var deferred = $q.defer(); 

		Login.refreshSession()
            .then(function(resp) {
				var url = Url.price + '/' + price.nr_sequencia;

				config.headers.Authorization = $sessionStorage.session.token;

				$http.put(url, price, config)
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

	function _delete(nr_sequencia) {
		var deferred = $q.defer(); 

		Login.refreshSession()
            .then(function(resp) {
				var url = Url.price + '/' + nr_sequencia;

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