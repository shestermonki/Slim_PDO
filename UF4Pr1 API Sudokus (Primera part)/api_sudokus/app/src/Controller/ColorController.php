<?php

namespace ApiSudoku\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ColorController
{
  public static function initRoutes($app)
  {
    $app->get('/color', '\ApiSudoku\Controller\ColorController:getAllColors');
    $app->get('/color/{id:[0-9]+}', '\ApiSudoku\Controller\ColorController:getColorById');
    $app->get('/color/{name:[a-zA-Z]+}', '\ApiSudoku\Controller\ColorController:getColorByName');
    $app->post('/color', '\ApiSudoku\Controller\ColorController:postColor');
    $app->put('/color/{id:[0-9]+}', '\ApiSudoku\Controller\ColorController:putColor');
    $app->patch('/color/{id:[0-9]+}', '\ApiSudoku\Controller\ColorController:patchColor');
    $app->delete('/color/{id:[0-9]+}', '\ApiSudoku\Controller\ColorController:deleteColor');
  }

  public function getAllColors(Request $request, Response $response, array $args)
  {
    $colors = \ApiSudoku\Model\ColorDB::getInstance()->getAllColors();
    if (is_null($colors)) {
      $response = $response->withStatus(500);
    } else {
      $response = $response->withJson($colors);
    }
    return $response;
  }

  public function getColorById(Request $request, Response $response, array $args)
  {
    $id = $args['id'];
    $color = \ApiSudoku\Model\ColorDB::getInstance()->getColorById($id);
    if (is_null($color)) {
      $response = $response->withStatus(404);
    } else {
      $response = $response->withJson($color);
    }
    return $response;
  }

  public function getColorByName(Request $request, Response $response, array $args)
  {
    $name = $args['name'];
    $color = \ApiSudoku\Model\ColorDB::getInstance()->getColorByName($name);
    if (is_null($color)) {
      $response = $response->withStatus(404);
    } else {
      $response = $response->withJson($color);
    }
    return $response;
  }

  public function postColor(Request $request, Response $response, array $args)
  {
    $data = $request->getParsedBody();
    return $this->validatePersist($data, $response, null);
  }

  public function putColor(Request $request, Response $response, array $args)
  {
    $id = $args['id'];
    $data = $request->getParsedBody();
    return $this->validatePersist($data, $response, $id);
  }

  public function patchColor(Request $request, Response $response, array $args)
  {
    $id = $args['id'];
    $color = \ApiSudoku\Model\ColorDB::getInstance()->getColorById($id);
    if (is_null($color)) {
      $response = $response->withStatus(404);
      return $response;
    }
    $data = $request->getParsedBody();
    // Pass existing data as default values
    if (!isset($data["name"])) $data["name"] = $color->getName();
    if (!isset($data["red"])) $data["red"] = $color->getRed();
    if (!isset($data["green"])) $data["green"] = $color->getGreen();
    if (!isset($data["blue"])) $data["blue"] = $color->getBlue();

    return $this->validatePersist($data, $response, $id);
  }

  public function deleteColor(Request $request, Response $response, array $args)
  {
    $id = $args['id'];
    $color = \ApiSudoku\Model\ColorDB::getInstance()->getColorById($id);
    if (is_null($color)) {
      $response = $response->withStatus(404, 'Color not found');
    } else {
      $result = \ApiSudoku\Model\ColorDB::getInstance()->deleteColorById($id);
      $response = $response->withStatus($result ? 200 : 500);
    }
    return $response;
  }

  private function validatePersist($data, $response, $id)
  {
    // Check all fields are set
    foreach (["name", "red", "green", "blue"] as $key) {
      if (!isset($data[$key])) {
        $response = $response->withStatus(400);
        return $response;
      }
    }
    // Validate numbers
    foreach (["red", "green", "blue"] as $key) {
      $value = $data[$key];
      if (!is_numeric($value) || ($value < 0) || ($value > 255)) {
        $response = $response->withStatus(400);
        return $response;
      }
    }
    // Validate name
    $name = trim($data["name"]);
    if (empty($name)) {
      $response = $response->withStatus(400);
      return $response;
    }
    $color = \ApiSudoku\Model\ColorDB::getInstance()->getColorByName($name);
    if (!is_null($color) && !is_null($id) && $id != $color->getId()) {
      $response = $response->withStatus(400, "This color name already exists in another color");
      return $response;
    }
    // All ok
    $data['id'] = $id;
    $color = \ApiSudoku\Model\Color::fromAssoc($data);
    $color = \ApiSudoku\Model\ColorDB::getInstance()->persistColor($color);
    if (is_null($color)) {
      $response = $response->withStatus(500);
    } else {
      $response = $response->withJson($color);
    }
    return $response;
  }
}
