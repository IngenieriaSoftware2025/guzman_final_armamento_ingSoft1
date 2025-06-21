<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use MVC\Router;

class MapasController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        $router->render('mapas/index', []);
    }
}