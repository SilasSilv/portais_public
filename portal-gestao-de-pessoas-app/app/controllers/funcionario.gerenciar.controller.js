app

.controller("funcionarioGerenciarCtrl", function($scope, $q, $timeout, $state, $stateParams, $location, $rootScope, growl, Util, People, Card, Sector, Group, Office, Photo, FileUploader) {
    var vm = this,
        _peopleNotRegistered = true,
        _nrCrachaCurrent = 0,
        _imgCurrent = '',
        _stream;
    navigator.getUserMedia = (navigator.getUserMedia || 
                            navigator.webkitGetUserMedia || 
                            navigator.mozGetUserMedia || 
                            navigator.msGetUserMedia || 
                            navigator.oGetUserMedia);

    vm.loader = false;                          
    vm.loaderSavePeople = false;
    vm.loaderCard = false;
    vm.loaderSector = false;
    vm.loaderGroup = false;
    vm.loaderOffice = false;
    vm.loaderWebCam = false;
    vm.imageUpload = new FileUploader();
    vm.photoTaken = false;
    vm.viewUpdate = false;
    vm.enablePermissions = false;
    vm.cards = [];
    vm.sectors = [];
    vm.groups = [];
    vm.offices = [];
    vm.person = {ie_situacao: 'A'};
    vm.invalidDate = false;
    vm.options = {
        format: 'DD/MM/YYYY',
        showClear: true,
        allowInputToggle: true,
        ignoreReadonly: true
    };

    //Photo
    vm.myImage = 'img/default.jpg';
    vm.myImageTemp = '';
    vm.myCroppedImage ='';
    vm.imageFormat = '';
    _imgCurrent = vm.myImage;

    /*
        Functions CRUD
    */

    //People

    function _generateCopyPeople(people) {
        var copyPeople = angular.copy(people);

        if (copyPeople.setor) {
            copyPeople.cd_setor = copyPeople.setor.cd_setor;
        } else {
            copyPeople.cd_setor = null;
        }

        if (copyPeople.cargo) {
            copyPeople.cd_cargo = copyPeople.cargo.cd_cargo;
        } else {
            copyPeople.cd_cargo = null;
        }

        if (copyPeople.grupo) {
            copyPeople.cd_grupo = copyPeople.grupo.cd_grupo;
        } else {
            copyPeople.cd_grupo = null;
        }

        if (copyPeople.cartao) {
            copyPeople.nr_cartao = copyPeople.cartao.nr_cartao;
        } else {
            copyPeople.nr_cartao = "";
        }
        
        if (vm.myImage == 'img/default.jpg') {
            copyPeople.url_foto_perfil = 'img/people/default.jpg';
        }

        if (copyPeople.dt_demissao instanceof moment) {
            copyPeople.dt_demissao = copyPeople.dt_demissao.format('DD/MM/YYYY');
        } else if (copyPeople.dt_demissao === null) {
            copyPeople.dt_demissao = ""; 
        }

        return copyPeople;
    }

    function _treatErrorPeople(err) { 
        if (err.status == 422) {
            if (err.data.message_error.hasOwnProperty("code")) {
                switch(err.data.message_error.code) {
                    case "PEOPLE-01":
                        growl.warning("Número do crachá já existe");
                        break;
                    case "PEOPLE-02":
                        growl.warning("Login alternativo já existe");
                        break;
                    case "PEOPLE-03":
                        growl.warning("O funcionário dessa matricula já interagiu com os portais, não é possível mais mudar seu número do crachá");
                        break;
                    default:
                        growl.error("Erro no sistema desculpe pelos transtornos, comunique a TI!");
                }                
            } else {
                growl.error("Erro no sistema desculpe pelos transtornos, comunique a TI!");
            }                    
        } else {
            Util.treatError(err);
        }
    }

    vm.fnSave = function(people, check) {
        var copyPeople = _generateCopyPeople(people);

        if (check) {
            if ((copyPeople.hasOwnProperty('dt_demissao') && copyPeople.dt_demissao != "")) {
                $('#myModalAlertDismissed').modal({
                    show: true,
                    keyboard: false,
                    backdrop: 'static'
                });
                return false;
            }
        }

        if (_peopleNotRegistered) { 
            _fnRegister(copyPeople);
        } else {
            _fnUpdate(copyPeople);
        }
    }

    function _fnRegister(people) {           
        vm.loaderSavePeople = true;

        People.create(people)
            .then(function(resp) {
                _peopleNotRegistered = false;
                _nrCrachaCurrent = resp.nr_cracha;
                $rootScope.$broadcast('update_cracha', {nr_cracha: _nrCrachaCurrent});

                if (vm.myImage != 'img/default.jpg') {
                    _imgCurrent = vm.myImage;
                    return Photo.upload(vm.imageUpload, vm.myImage, _nrCrachaCurrent);
                } else {
                    growl.success("Funcionário cadastrado com sucesso!");
                    vm.enablePermissions = true;
                }
            })
            .then(function(resp) {
                if (vm.myImage != 'img/default.jpg') {
                    growl.success("Funcionário cadastrado com sucesso!");
                    vm.enablePermissions = true;
                }
            })
            .catch(function(err) {
                _treatErrorPeople(err);
            })
            .finally(function() {
                vm.loaderSavePeople = false;
            });
    }

    function _fnUpdate(people) {
        vm.loaderSavePeople = true;

        People.update(people, _nrCrachaCurrent)
            .then(function(resp) {
                _nrCrachaCurrent = people.nr_cracha;
                vm.person.ie_alterar_senha = 'N';
                
                if (vm.myImage != _imgCurrent && vm.myImage != 'img/default.jpg') {
                    return Photo.upload(vm.imageUpload, vm.myImage, _nrCrachaCurrent);
                } else {
                    growl.success("Funcionário alterado com sucesso!");
                }                
            })
            .then(function(resp) {
                if (vm.myImage != _imgCurrent && vm.myImage != 'img/default.jpg') {
                    _imgCurrent = vm.myImage;
                    growl.success("Funcionário alterado com sucesso!");
                }
            })
            .catch(function(err) {
                _treatErrorPeople(err);
            })
            .finally(function() {
                vm.loaderSavePeople = false;
            });
    }

    //Photo

    // Photo -- Upload
    function _fnPhotoSelect(evt) {
        var file = evt.currentTarget.files[0];
        var reader = new FileReader();

        if (file.type == 'image/png') {
            vm.imageFormat = file.type;
        } else {
            vm.imageFormat = 'image/jpeg';
        }
        
        reader.onload = function(evt) {
            $scope.$apply(function() {
                vm.myImageTemp = evt.target.result;
            });
        }
        
        reader.readAsDataURL(file);
        $('#myModalPhotoUpload').modal({
            keyboard: false,
            backdrop: 'static',
            show: true
        });
        $('#fileInput').val('');
    };

    vm.fnPhotoCutOutUpload = function() { 
        vm.myImage = vm.myCroppedImage;
        $('#myModalPhotoUpload').modal('hide');
    }

    // Photo -- WebCam
    vm.fnWebCam = function() {
        if (navigator.getUserMedia) {
            vm.loaderWebCam = true; 
            var video = $('#videoElement');

            navigator.getUserMedia(
                {video: true}, 
                function(stream) {
                    _stream = stream;

                    video.attr("src", window.URL.createObjectURL(stream));

                    $('#myModalPhotoWebCam').modal({
                        keyboard: false,
                        backdrop: 'static',
                        show: true
                    });
        
                    $timeout(function() {
                        vm.loaderWebCam = false; 
                    }, 1000);
                },
                function(err) {
                    var msgError = err.toString();
                    
                    if (msgError == "NotFoundError: The object can not be found here.") {
                        growl.warning("Seu navegador não conseguiu acessar a webcam. Segue a lista dos navegadores que suportam: https://caniuse.com/#search=navigator");
                    }

                    if (msgError == 'NotFoundError: Requested device not found') {
                        growl.warning("Nenhuma webcam foi encontrada no seu computador");
                    } 

                    if (msgError == 'DOMException: Permission denied') {
                        growl.warning("Você precisa dar permissão para o acesso a sua webcam");
                    } 
                }
            );
        } else {
            growl.warning("Seu navegador não da suporte nativo para o uso da webcam. Segue a lista dos navegadores que suportam: https://caniuse.com/#search=navigator");
        }
    }

    vm.fnSnapShot = function() {        
        var context, canvas;
        var video = document.querySelector("#videoElement");
        var width = video.offsetWidth, height = video.offsetHeight;

        canvas = canvas || document.createElement('canvas');
        canvas.width = width;
        canvas.height = height;

        context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, width, height);

        vm.imageFormat = 'image/jpeg';
        vm.myImageTemp = canvas.toDataURL(vm.imageFormat);

        _fnStopStreamedVideo();
        vm.photoTaken = true;
        canvas.toBlob(function(blob){
            vm.imageUpload.addToQueue(blob);
        }, vm.imageFormat);
    }

    vm.fnPhotoCutOutWebCam = function() {
        vm.myImage = vm.myCroppedImage;
        $('#myModalPhotoWebCam').modal('hide');
        vm.photoTaken = false;
    }

    vm.fnCloseModalWebCam = function() {
        _fnStopStreamedVideo();
        $('#myModalPhotoWebCam').modal('hide');
        vm.photoTaken = false;
    }

    function _fnStopStreamedVideo() {
        var tracks = _stream.getTracks();                

        tracks.forEach(function(track) {
            track.stop();
        });
    }

    /*
        Functions Utils
    */

   function _loadPendingAsync() {
        vm.loaderCard = true;
        vm.loaderSector = true;
        vm.loaderGroup = true;
        vm.loaderOffice = true;
        
        var sector = Sector.read({pagination: "ALL"}).then(function(resp) {vm.sectors = resp; vm.loaderSector = false;}),
            office = Office.read({pagination: "ALL"}).then(function(resp) {vm.offices = resp; vm.loaderOffice = false;}),
            group = Group.read({pagination: "ALL"}).then(function(resp) {vm.groups = resp; vm.loaderGroup = false;}),
            card = Card.read({available: 'S', pagination: 'ALL', sort_type: 'ASC'}).then(function(resp) {vm.cards = resp; vm.loaderCard = false;});

        $q.all([sector, office, group, card])
            .catch(function(err) {
                Util.treatError(err);
            });        
    }

    function _loadPendingPeople(nr_cracha) {
        vm.loader = true;

        var sector = Sector.read({pagination: "ALL"}).then(function(resp) {vm.sectors = resp;}),
            office = Office.read({pagination: "ALL"}).then(function(resp) {vm.offices = resp;}),
            group = Group.read({pagination: "ALL"}).then(function(resp) {vm.groups = resp;}),
            card = Card.read({available: 'S', pagination: 'ALL', sort_type: 'ASC'}).then(function(resp) {vm.cards = resp;})

            $q.all([sector, office, group, card])
                .then(function(resp) {
                    People.read({crachaNome: nr_cracha, fields: '*'})
                        .then(function(resp) {
                            if (resp.length == 0) {
                                $location.path('/gestao-pessoas/funcionario/consultar');
                                vm.loader = false;
                            } else {
                                var person = resp[0]; 
                                _nrCrachaCurrent = parseInt(person.nr_cracha);
                                vm.person.nr_cracha = parseInt(person.nr_cracha);
                                vm.person.ds_login_alternativo = person.ds_login_alternativo;
                                vm.person.nm_pessoa_fisica = person.nm_pessoa_fisica;
                                vm.person.ds_mail = person.ds_mail;
                                vm.person.setor = vm.sectors[vm.sectors.map(function(sector) { return sector.cd_setor; }).indexOf(person.cd_setor)];
                                vm.person.cargo = vm.offices[vm.offices.map(function(office) { return office.cd_cargo; }).indexOf(person.cd_cargo)];
                                vm.person.grupo = vm.groups[vm.groups.map(function(group) { return group.cd_grupo; }).indexOf(person.cd_grupo)];
                                _imgCurrent = vm.myImage = person.url_foto_perfil;
                                vm.person.ie_situacao = person.ie_situacao;
                                if (person.dt_demissao != "") {
                                    vm.person.dt_demissao = moment(person.dt_demissao, 'DD/MM/YYYY').format();
                                }
                                
                                if (person.nr_cartao != "" ) {
                                    vm.cards.push({nr_cartao: person.nr_cartao});
                                    vm.cards.sort(function(cardOld, carNew) {return cardOld.nr_cartao - carNew.nr_cartao;});
                                    vm.person.cartao = vm.cards[vm.cards.map(function(card) { return card.nr_cartao; }).indexOf(person.nr_cartao)];
                                }
                            }
                        })
                        .catch(function(err) {
                            Util.treatError(err);
                        })
                        .finally(function() {
                            vm.loader = false;
                            _peopleNotRegistered = false;
                        });
                        
                })
                .catch(function(err) {
                    Util.treatError(err);
                });
    }

    vm.fnCleanPerson = function() {
        vm.person = {ie_situacao: 'A', ds_mail: "", ds_login_alternativo: "", ie_alterar_senha: 'S'};
        vm.myImage = 'img/default.jpg';
    }

    vm.fnReload = function() {
        $state.reload();
    }

    /*
        Functions init
    */
    if ($stateParams.nr_cracha) {
        vm.viewUpdate = true;
        vm.enablePermissions = true;
        _loadPendingPeople($stateParams.nr_cracha);
    } else {
        _loadPendingAsync();
    }
    

    $timeout(function() {
        angular.element($('#fileInput')).on('change', _fnPhotoSelect);
    }, 1000);
});