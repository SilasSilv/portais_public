app

.controller("categoryCtrl", function( $timeout, growl, Category, CategoryType, Util) {
    var vm = this;
    
    //Category Type
    vm.loaderBtnSave = false;
    vm.loaderBtnDelete = false;
    vm.loaderCategoriesTypes = false;
    vm.statusRegister = false;
    vm.displayLoaderPlus = false;
    vm.loaderPlus = false;
    vm.ieOperation = "";
    vm.categoryType = {};
    vm.name = "";
    vm.cd_tipo_categoria = 0;
    vm.categories_types = [];

    //Category Values
    vm.loaderAdd = false;
    vm.loaderCategoriesValues = false;
    vm.ieRegisterCategoryValues = true;
    vm.cd_tipo_categoria__values = 0;
    vm.ds_tipo_categoria = '';
    vm.categories_values = [];
    vm.cd_categoria = 0;
    vm.categoryValues = {}

    /*
        Functions CRUD
    */

    //Category Type

    vm.fnSearch = function(name) {
        var filter = {};
        vm.loaderCategoriesTypes = true;
        vm.name = name || '';

        if (name) {
            filter.ds_tipo_categoria = name;
        }

        CategoryType.read(filter)
            .then(function(resp) {
                if (resp.length < 20) {
                    vm.displayLoaderPlus = false;
                } else {
                    vm.displayLoaderPlus = true;
                }

                vm.categories_types = resp;
            })
            .catch(function(err) {
                Util.treatError(err);
            }) 
            .finally(function() {
                vm.loaderCategoriesTypes = false;
            });          
    }

    vm.fnSearchPlus = function(name) {
        vm.loaderPlus = true;

        var categoriesTypesLength = vm.categories_types.length;
        var filter = {pagination: (categoriesTypesLength + ",20")};
        vm.name = name || '';

        if (name) {
            filter.ds_tipo_categoria = name;
        }

        CategoryType.read(filter)
            .then(function(resp) {
                if (resp.length < 20) {
                    vm.displayLoaderPlus = false;
                } else {
                    vm.displayLoaderPlus = true;
                }

                vm.categories_types = vm.categories_types.concat(resp);
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loaderPlus = false;
            });
    }
    
    vm.fnSave = function(category_type) {
        var categoryTypeCopy = angular.copy(category_type)
       
        if (vm.ieOperation == "register") {
            _fnRegister(categoryTypeCopy);
        } else if (vm.ieOperation == "update") {
            _fnUpdate(categoryTypeCopy);
        }
    }

    function _fnRegister(category_type) {
        vm.loaderBtnSave = true;       

        CategoryType.create(category_type)
            .then(function(resp) {
                growl.success("Tipo de categoria cadastrado com sucesso!");
                vm.fnSearch(vm.name);
                vm.fnClose();
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loaderBtnSave = false;
            });
    }

    function _fnUpdate(category_type) {
        vm.loaderBtnSave = true;       

        CategoryType.update(category_type)
            .then(function(resp) {
                growl.success("Tipo de categoria atualizado com sucesso!");
                vm.fnSearch(vm.name);
                vm.fnClose();
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loaderBtnSave = false;
            });
    }

    vm.fnDelete = function(cd_tipo_categoria) {
        vm.loaderBtnDelete = true;
        vm.cd_tipo_categoria = cd_tipo_categoria;

        CategoryType.delete(cd_tipo_categoria)
            .then(function(resp) {
                growl.success("<span class='glyphicon glyphicon-trash growl-trash'></span> Tipo de categoria <strong style='color: red'>excluído</strong> com sucesso!", {disableIcons: true});
                vm.fnSearch(vm.name);
            })
            .catch(function(err) {
                if (err.status = 422) {
                    growl.warning("Tipo de categoria não pode ser excluída, possui valores de categoria.");
                } else {
                    Util.treatError(err);
                }
            })
            .finally(function() {
                vm.loaderBtnDelete = false;
            });
    }

    //Category Values

    vm.fnSearchValues = function(cd_tipo_categoria, ds_tipo_categoria) {
        var filter = {};
        vm.loaderCategoriesValues = true;
        cd_tipo_categoria = cd_tipo_categoria || 0;
        vm.categoryValues.cd_tipo_categoria = cd_tipo_categoria;
        vm.cd_tipo_categoria__values = cd_tipo_categoria;
        vm.ds_tipo_categoria = ds_tipo_categoria;

        if (cd_tipo_categoria) {
            filter.cd_tipo_categoria = cd_tipo_categoria;
        }

        $('#myModalAddValues').modal({
            'backdrop': 'static',
            'show': true
        });

        Category.read(filter)
            .then(function(resp) {
                vm.categories_values = resp;
            })
            .catch(function(err) {
                Util.treatError(err);
            }) 
            .finally(function() {
                vm.loaderCategoriesValues = false;
            });          
    }

    vm.fnToAdd = function(category_values) {
        var categoryValuesCopy = angular.copy(category_values);
        vm.loaderAdd = true;       

        Category.create(categoryValuesCopy)
            .then(function(resp) {
                growl.success("Valor da categoria cadastrado com sucesso!");
                vm.fnClearCategoryValues();
                vm.fnSearchValues(vm.cd_tipo_categoria__values, vm.ds_tipo_categoria);
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loaderAdd = false;
            }); 
    }

    vm.fnToEdit = function(category_values) {
        var categoryValuesCopy = angular.copy(category_values);
        vm.loaderAdd = true;       

        Category.update(categoryValuesCopy)
            .then(function(resp) {
                vm.ieRegisterCategoryValues = true;
                growl.success("Valor da categoria atualizado com sucesso!");
                vm.fnClearCategoryValues();
                vm.fnSearchValues(vm.cd_tipo_categoria__values, vm.ds_tipo_categoria);
            })
            .catch(function(err) {
                Util.treatError(err);
            })
            .finally(function() {
                vm.loaderAdd = false;
            });
    }

    vm.fnToDelete = function(category_values) {
        vm.loaderDelete = true;
        vm.cd_categoria = category_values.cd_categoria || 0;
        

        Category.delete(category_values.cd_categoria)
            .then(function(resp) {
                growl.success("<span class='glyphicon glyphicon-trash growl-trash'></span> Valor de categoria <strong style='color: red'>excluído</strong> com sucesso!", {disableIcons: true});
                vm.fnSearchValues(vm.cd_tipo_categoria__values, vm.ds_tipo_categoria);
            })
            .catch(function(err) {
                if (err.status = 422) {
                    growl.warning("Valor de categoria não pode ser excluída, já foi utilizada no sistema.");
                } else {
                    Util.treatError(err);
                }
            })
            .finally(function() {
                vm.loaderDelete = false;
            }); 
    }

    /*
        Util 
    */

    //Category Type

    vm.fnNew = function() {
        vm.ieOperation = "register";
        vm.categoryType = {};
        vm.statusRegister = true;
    }

    vm.fnLoadEdit = function(category_type) {
        vm.categoryType.cd_tipo_categoria = category_type.cd_tipo_categoria;
        vm.categoryType.ds_tipo_categoria = category_type.ds_tipo_categoria;

        vm.ieOperation = "update";
        vm.statusRegister = true;

        $('html, body').animate({scrollTop:0}, 'slow');
    }

    vm.fnClose = function() {
        vm.statusRegister = false;
        vm.categoryType = {};
    }

    vm.fnClear = function() {
        vm.categoryType.ds_tipo_categoria = '';
    }


    //Category Values 

    vm.fnLoadEditCategoryValues = function(category_values) {
        vm.categoryValues.cd_categoria = category_values.cd_categoria;
        vm.categoryValues.ds_categoria = category_values.ds_categoria;

        vm.ieRegisterCategoryValues = false;
        
        $('html, body').animate({scrollTop:0}, 'slow');
    }

    vm.fnClearCategoryValues = function() {
        delete vm.categoryValues.cd_categoria;
        delete vm.categoryValues.ds_categoria;
    }

    /*
        Init
    */

    vm.fnSearch();
});