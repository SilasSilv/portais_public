app
   
.controller("ConfiguracaoCtrl", function ($timeout, Util, growl) {
    var vm = this;
    
    vm.loader = false;
    vm.loaderBtn = false;
    vm.configuracao = {};
    vm.validForm = false;
    vm.pessoas = [];
    
    vm.configuracao = {
        url_foto_perfil: '',
        nr_cracha: '',
        nm_pessoa_fisica: '',
        ds_mail: '',
        ds_senha: '',
        ds_senha2: ''
    };
    
    vm.fnCadastrarconf = function (configuracao){
        vm.loaderBtn = true;
        var configuracaoTemp = angular.copy(configuracao);


        Configuration.create (configuracaoTemp)
            .then(function (resp){
                growl.success("Alteracão de senha realizada com sucesso");
            })
            .catch(function (){
                if (err.status == 406) {
                    growl.warning("Não permitido reconfigurar a senha!");
                    window.history.go(-1);
                } else {
                    Util.treatError(err);
                }
            })
            .finally(function (){
                $timeout(function (){
                    vm.loaderBtn = false;
                }, 250);
            });
    };
    
    vm.fnLimparConfiguracao = function (configuracao){
        if (configuracao) {
            Object.keys(configuracao)
                .forEach (function (key) {
                    if (!/nr_cracha/.test(key)) {
                        delete configuracao[key];
                    }
                });
        }
    }; 
});