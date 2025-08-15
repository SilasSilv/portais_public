app

.factory("Photo", function($q, $sessionStorage, Url, Login, FileUploader) {
    var imageUpload = undefined;

    function _dataURItoBlob(dataURI) {
		var binary = atob(dataURI.split(',')[1]);
		var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];
		var array = [];

		for(var i = 0; i < binary.length; i++) {
			array.push(binary.charCodeAt(i));
		}

		return new Blob([new Uint8Array(array)], {type: mimeString});
    }
    
    function _upload(uploader, imageUpload, nr_cracha) {
        var deferred = $q.defer();

        Login.refreshSession()
            .then(function(resp) {
                uploader.onBeforeUploadItem = function(item) {
                    var blob = _dataURItoBlob(imageUpload);
        
                    item._file = blob;
                    item.alias = 'photo';
                    item.url = Url.photo + "/" + nr_cracha;
                    item.headers = {Authorization: $sessionStorage.session.token};
                }
            
                uploader.onSuccessItem = function(item, response, status, headers) {
                    uploader.clearQueue();
                    deferred.resolve(response);
                }
        
                uploader.onErrorItem = function(item, response, status, headers) {
                    deferred.reject(response);
                }
        
                uploader.uploadAll();
            })
            .catch(function(err) {
                deferred.reject(err);
            });

        return deferred.promise;
    }

    return {
        upload: _upload
    };
});