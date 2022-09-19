<?php

namespace ApiSudoku\Model;

class SudokuDB
{
  protected static ?\ApiSudoku\Model\SudokuDB $instance = null;

  public static function getInstance(): \ApiSudoku\Model\SudokuDB
  {
    if (is_null(static::$instance)) {
      static::$instance = new \ApiSudoku\Model\SudokuDB();
    }
    return static::$instance;
  }

  private \PDO $conn;

  protected function __construct()
  {
    $this->conn = new \PDO(
      "mysql:host=api_sudoku_db;dbname=sudokudb",
      "sudokuuser",
      "sudokupassword"
    );
  }

  // PLAYERS
  public function getAllPlayers(): array
  {
    $sql = "SELECT * FROM players";
    $result = $this->conn->query($sql);
    $playersAssoc = $result->fetchAll(\PDO::FETCH_ASSOC);
    if (!$playersAssoc) return [];
    $players = [];
    foreach ($playersAssoc as $playerAssoc) {
      $players[] = new \ApiSudoku\Model\Player(
        $playerAssoc['id'],
        $playerAssoc['username'],
        $playerAssoc['password'],
        $playerAssoc['email']
      );
    }
    return $players;
  }

  public function getPlayerByUsername(string $username): ?\ApiSudoku\Model\Player
  {
    $sql = "SELECT * FROM players WHERE  LOWER(username)=LOWER(:username)";
    $statement = $this->conn->prepare($sql);
    $result = $statement->execute(['username' => $username]);
    $playerAssoc = $statement->fetch(\PDO::FETCH_ASSOC);
    if (!$playerAssoc) return null;
    $player = \ApiSudoku\Model\Player::fromAssoc($playerAssoc);
    return $player;
  }

  public function getPlayerById(int $id): ?\ApiSudoku\Model\Player
  {
    $sql = "SELECT * FROM players WHERE id=:id";
    $statement = $this->conn->prepare($sql);
    $result = $statement->execute([':id' => $id]);
    $playerAssoc = $statement->fetch(\PDO::FETCH_ASSOC);
    if (!$playerAssoc) return null;
    $player = \ApiSudoku\Model\Player::fromAssoc($playerAssoc);
    return $player;
  }



  public function deletePlayer(\ApiSudoku\Model\Player $player): bool
  {
    return $this->deletePlayerById($player->getId());
  }

  public function deletePlayerById(int $id): bool
  {
    $sql = "DELETE FROM players WHERE id=:id";
    $statement = $this->conn->prepare($sql);
    $result = $statement->execute([
      ':id' => $id
    ]);
    return $result;
  }


  public function persistPlayer(Player $player): ?Player
  {
    if (is_null($player->getId())) {
      return $this->insertPlayer($player);
    } else {
      return $this->savePlayer($player);
    }
  }

  public function insertPlayer(Player $player): ?Player
  {
    $sql = "INSERT INTO players VALUES (NULL, :username, :password, :email)";
    $statement = $this->conn->prepare($sql);
    $result = $statement->execute([
      ':username' => $player->getUsername(),
      ':password' => $player->getPassword(),
      ':email' => $player->getEmail()
    ]);
    if (!$result) return null;
    $id = $this->conn->lastInsertId();
    $player->setId($id);
    return $player;
  }

  public function savePlayer(Player $player): ?Player
  {
    //$id = $player->getId();
    //if (is_null($this->getPlayerById($id))) return null;
    $sql = "UPDATE players SET username=:username, password=:password, email=:email WHERE id=:id";
    $statement = $this->conn->prepare($sql);
    $result = $statement->execute([
      ':id' => $player->getId(),
      ':username' => $player->getUsername(),
      ':password' => $player->getPassword(),
      ':email' => $player->getEmail()
    ]);
    if (!$result) return null;
    return $player;
  }

  // SUDOKUS
  public function insertSudoku(\ApiSudoku\Model\Sudoku $sudoku): ?\ApiSudoku\Model\Sudoku
  {
    $sql = "INSERT INTO sudokus VALUES (:id, :level, :problem, :solved)";
    $statement = $this->conn->prepare($sql);
    $result = $statement->execute([
      ':id' => null,
      ':level' => $sudoku->getLevel(),
      ':problem' => $sudoku->getProblem(),
      ':solved' => $sudoku->getSolved()
    ]);
    $id = $this->conn->lastInsertId();
    return $this->getSudokuById($id);
  }

  public function getAllSudokus(): array
  {
    $sql = "SELECT * FROM sudokus";
    $result = $this->conn->query($sql);
    $sudokusAssoc = $result->fetchAll(\PDO::FETCH_ASSOC);
    if (!$sudokusAssoc) return null;
    $sudokus = [];
    foreach ($sudokusAssoc as $sudokuAssoc) {
      $sudokus[] = \ApiSudoku\Model\Sudoku::fromAssoc($sudokuAssoc);
    }
    return $sudokus;
  }

  public function getSudokuById(int $id): ?\ApiSudoku\Model\Sudoku
  {
    $sql = "SELECT * FROM sudokus WHERE id=:id";
    $statement = $this->conn->prepare($sql);
    $result = $statement->execute([':id' => $id]);
    $sudokuAssoc = $statement->fetch(\PDO::FETCH_ASSOC);
    if (!$sudokuAssoc) return null;
    $sudoku =  \ApiSudoku\Model\Sudoku::fromAssoc($sudokuAssoc);
    return $sudoku;
  }

  public function deleteSudokuById(int $id): bool
  {
    $sql = "DELETE FROM sudokus WHERE id=:id";
    $statement = $this->conn->prepare($sql);
    $result = $statement->execute([
      ':id' => $id
    ]);
    return $result;
  }

  public function deleteSudoku(\ApiSudoku\Model\Sudoku $sudoku): bool
  {
    $sql = "DELETE FROM sudokus WHERE id=:id";
    $statement = $this->conn->prepare($sql);
    $result = $statement->execute([
      ':id' => $sudoku->getId()
    ]);
    return $result;
  }

  // GAMES
  // TODO
}
