<?php
require 'vendor/autoload.php';

$c = new \Slim\Container();
$app = new \Slim\App($c);

//Habilitando o CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers:  Content-Type, Accept, Origin, Authorization');

date_default_timezone_set('America/Sao_Paulo');

$app->options('/{routes:.+}', function ($req, $res, $args) {
    return $res;
});

//Container da API
require 'api/v1/vendor/container/container.php';

//Rotas
require 'api/v1/resource/login/routes/login.php';
require 'api/v1/resource/people/routes/people.php';
require 'api/v1/resource/meal/routes/meal.php';
require 'api/v1/resource/meal/routes/analyze.php';
require 'api/v1/resource/meal/routes/menu.php';
require 'api/v1/resource/meal/routes/thirdFolds.php';
require 'api/v1/resource/meal/routes/opinion.php';
require 'api/v1/resource/meal/routes/quality.php';
require 'api/v1/resource/meal/routes/price.php';
require 'api/v1/resource/pointOccurrence/routes/pointOccurrence.php';
require 'api/v1/resource/accidentOccurrence/routes/accidentOccurrence.php';
require 'api/v1/resource/category/routes/category.php';
require 'api/v1/resource/category/routes/category_type.php';
require 'api/v1/resource/phonebook/routes/phonebook.php';
require 'api/v1/resource/card/routes/card.php';
require 'api/v1/resource/sector/routes/sector.php';
require 'api/v1/resource/group/routes/group.php';
require 'api/v1/resource/office/routes/office.php';
require 'api/v1/resource/permission/routes/system.php';
require 'api/v1/resource/permission/routes/people.php';
require 'api/v1/resource/permission/routes/sector.php';
require 'api/v1/resource/session/routes/session.php';
require 'api/v1/resource/system/routes/system.php';
require 'api/v1/resource/turnstile/routes/turnstile.php';


//Executando o Web Service
$app->run();