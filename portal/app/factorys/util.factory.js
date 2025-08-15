app

.factory("Util", function($state, $timeout, $sessionStorage, growl, cfpLoadingBar) {

    function _treatError(error, defaultError) {
        var delay = 0;
        defaultError = defaultError || 'yes';

        if (cfpLoadingBar.status() != 0) {
            cfpLoadingBar.complete();
            delay = 500;
        } else {
            delay = 0;
        }

        $timeout(function() {
            if (error.status == 401) {
                $sessionStorage.$reset();
                return $state.go('login', {error: "Sessão expirou!"});

            } else if (error.status == 403) {
                $sessionStorage.$reset();
                return $state.go('login', {error: "Acesso negado!"});

            }  else if (error.status == 500) {                
                if(defaultError == 'yes') {
                    growl.error("Erro no sistema desculpe pelos transtornos, comunique a TI!");
                } else {
                    $sessionStorage.$reset();
                    return $state.go('login', {error: "Erro no sistema desculpe pelos transtornos, comunique a TI!"});
                }
            } else {
                growl.error("Erro no sistema desculpe pelos transtornos, comunique a TI!");
            }
        }, delay);
    }

    function _treatErrorGrowl(error) {
        if (error.status == 401) {
            growl.error("Sessão expirou");
        } else if (error.status == 403) {
            growl.error("Acesso negado!");
        } else {                
            growl.error("Erro no sistema desculpe pelos transtornos, comunique a TI!");
        }
    }

    return {
        treatError: _treatError,
        treatErrorGrowl: _treatErrorGrowl
    };
});