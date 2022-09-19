<?php

namespace SalesBun\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SudokuController
{
    public static function initRoutes($app)
    {
        $app->get('/sudoku', '\SalesBun\Controller\SudokuController:getAllSudokus');
        $app->get('/sudoku/{id:[0-9]+}', '\SalesBun\Controller\SudokuController:getSudokuById');
        $app->get('/player/{id:[0-9]+}', '\SalesBun\Controller\SudokuController:getPlayerById');
        $app->post('/player', '\SalesBun\Controller\SudokuController:postPlayer');
        $app->put('/player/{id:[0-9]+}', '\SalesBun\Controller\SudokuController:putPlayer');
        $app->patch('/player/{id:[0-9]+}', '\SalesBun\Controller\SudokuController:patchPlayer');
    }

    public function getAllSudokus(Request $reques, Response $response, array $args)
    {
        $sudokus = \SalesBun\Model\SudokuDB::getInstance()->getAllSudokus();
        if (is_null($sudokus)) {
            $response = $response->withStatus(500);
        } else {
            $response = $response->withJson($sudokus);
        }
        return $response;
    }

    public function getSudokuById(Request $reques, Response $response, array $args)
    {
        $id = $args['id'];
        $sudoku = \SalesBun\Model\SudokuDB::getInstance()->getSudokuById($id);
        if (is_null($sudoku)) {
            $response = $response->withStatus(404);
        } else {
            $response = $response->withJson($sudoku);
        }
        return $response;
    }

    public function getPlayerById(Request $reques, Response $response, array $args)
    {
        $id = $args['id'];
        $player = \SalesBun\Model\SudokuDB::getInstance()->getPlayerById($id);
        if (is_null($player)) {
            $response = $response->withStatus(404);
        } else {
            $response = $response->withJson($player);
        }
        return $response;
    }

    public function postPlayer(Request $request, Response $response, array $args)
    {
        $data = $request->getParsedBody();
        return $this->validatePersist($data, $response, null);
    }

    public function putPlayer(Request $request, Response $response, array $args)
    {
        $id = $args['id'];
        $data = $request->getParsedBody();
        return $this->validatePersist($data, $response, $id);
    }


    private function validatePersist($data, $response, $id)
    {
        // Check all fields are set
        foreach (["username", "password", "email"] as $key) {
            if (!isset($data[$key])) {
                $response = $response->withStatus(400);
                return $response;
            }
        }
        /* Validate numbers
        foreach (["red", "green", "blue"] as $key) {
            $value = $data[$key];
            if (!is_numeric($value) || ($value < 0) || ($value > 255)) {
                $response = $response->withStatus(400);
                return $response;
            }
        }
        */
        // Validate username
        $username = trim($data["username"]);
        if (empty($username)) {
            $response = $response->withStatus(400);
            return $response;
        }

        //validate password
        $password = trim($data["password"]);
        if (empty($password)) {
            $response = $response->withStatus(400);
            return $response;
        }

        $email = trim($data["email"]);
        if (empty($email)) {
            $response = $response->withStatus(400);
            return $response;
        }

        $player = \SalesBun\Model\SudokuDB::getInstance()->getPlayerByUsername($username);
        if (!is_null($player) && !is_null($id) && $id != $player->getId()) {
            $response = $response->withStatus(400, "This player userusername already exists in another player");
            return $response;
        }
        // All ok
        $data['id'] = $id;
        $player = \SalesBun\Model\Player::fromAssoc($data);
        $player = \SalesBun\Model\SudokuDB::getInstance()->persistPlayer($player);
        if (is_null($player)) {
            $response = $response->withStatus(500);
        } else {
            $response = $response->withJson($player);
        }
        return $response;
    }

    public function patchPlayer(Request $request, Response $response, array $args)
    {
        $id = $args['id'];
        $player = \SalesBun\Model\SudokuDB::getInstance()->getPlayerById($id);
        if (is_null($player)) {
            $response = $response->withStatus(404);
            return $response;
        }
        $data = $request->getParsedBody();
        // Pass existing data as default values
        if (!isset($data["username"])) $data["username"] = $player->getUsername();
        if (!isset($data["password"])) $data["password"] = $player->getPassword();
        if (!isset($data["email"])) $data["email"] = $player->getEmail();
        return $this->validatePersist($data, $response, $id);
    }
}
