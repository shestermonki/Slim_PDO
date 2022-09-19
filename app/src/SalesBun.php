<?php

namespace SalesBun;

use Slim\Factory\AppFactory;

class SalesBun
{
  public static function processRequest()
  {
    $app = AppFactory::create();
    \SalesBun\Controller\HelloController::initRoutes($app);
    \SalesBun\Controller\ColorController::initRoutes($app);
    \SalesBun\Controller\SudokuController::initRoutes($app);
    $app->run();
  }
}
