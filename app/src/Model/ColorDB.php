<?php

namespace ApiSudoku\Model;

class ColorDB
{
  protected static ?\ApiSudoku\Model\ColorDB $instance = null;

  public static function getInstance(): \ApiSudoku\Model\ColorDB
  {
    if (is_null(static::$instance)) {
      static::$instance = new \ApiSudoku\Model\ColorDB();
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

  // COLORS
  public function getAllColors(): ?array
  {
    $sql = "SELECT * FROM colors";
    $result = $this->conn->query($sql);
    $colorsAssoc = $result->fetchAll(\PDO::FETCH_ASSOC);
    if (!$colorsAssoc) return null;
    $colors = [];
    foreach ($colorsAssoc as $colorAssoc) {
      $colors[] = \ApiSudoku\Model\Color::fromAssoc($colorAssoc);
    }
    return $colors;
  }

  public function getColorById(int $id): ?\ApiSudoku\Model\Color
  {
    $sql = "SELECT * FROM colors WHERE id=:id";
    $statement = $this->conn->prepare($sql);
    $result = $statement->execute(['id' => $id]);
    $colorAssoc = $statement->fetch(\PDO::FETCH_ASSOC);
    if (!$colorAssoc) return null;
    $color = \ApiSudoku\Model\Color::fromAssoc($colorAssoc);
    return $color;
  }

  public function getColorByName(string $name): ?\ApiSudoku\Model\Color
  {
    $sql = "SELECT * FROM colors WHERE LOWER(name)=LOWER(:name)";
    $statement = $this->conn->prepare($sql);
    $result = $statement->execute(['name' => $name]);
    $colorAssoc = $statement->fetch(\PDO::FETCH_ASSOC);
    if (!$colorAssoc) return null;
    $color = \ApiSudoku\Model\Color::fromAssoc($colorAssoc);
    return $color;
  }

  public function persistColor(Color $color): ?Color
  {
    if (is_null($color->getId())) {
      return $this->insertColor($color);
    } else {
      return $this->updateColor($color);
    }
  }

  public function insertColor(Color $color): ?Color
  {
    $sql = "INSERT INTO colors VALUES (NULL, :name, :red, :green, :blue)";
    $statement = $this->conn->prepare($sql);
    $result = $statement->execute([
      'name' => $color->getName(),
      'red' => $color->getRed(),
      'green' => $color->getGreen(),
      'blue' => $color->getBlue(),
    ]);
    if (!$result) return null;
    $id = $this->conn->lastInsertId();
    $color->setId($id);
    return $color;
  }

  public function updateColor(Color $color): ?Color
  {
    $sql = "UPDATE colors SET name=:name, red=:red, green=:green, blue=:blue WHERE id=:id";
    $statement = $this->conn->prepare($sql);
    $result = $statement->execute([
      'id' => $color->getId(),
      'name' => $color->getName(),
      'red' => $color->getRed(),
      'green' => $color->getGreen(),
      'blue' => $color->getBlue(),
    ]);
    if (!$result) return null;
    return $color;
  }

  public function deleteColorById(int $id): bool
  {
    $sql = "DELETE FROM colors WHERE id=:id";
    $statement = $this->conn->prepare($sql);
    $result = $statement->execute(['id' => $id]);
    return $result;
  }
}
