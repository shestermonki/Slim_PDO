<?php

namespace ApiSudoku\Model;

class Sudoku implements \JsonSerializable
{
  private ?int $id;
  private int $level;
  private string $problem;
  private string $solved;

  public function __construct(?int $id, int $level, string $problem, string $solved)
  {
    $this->id = $id;
    $this->level = $level;
    $this->problem = $problem;
    $this->solved = $solved;
  }

  public function getId(): ?int
  {
    return $this->id;
  }
  public function setId(?int $id)
  {
    $this->id = $id;
  }
  public function getLevel(): int
  {
    return $this->level;
  }
  public function setLevel(int $level)
  {
    $this->level = $level;
  }
  public function getProblem(): string
  {
    return $this->problem;
  }
  public function setProblem(string $problem)
  {
    $this->problem = $problem;
  }
  public function getSolved(): string
  {
    return $this->solved;
  }
  public function setSolved(string $solved)
  {
    $this->solved = $solved;
  }

  // Needed to deserialize an object from an associative array
  public static function fromAssoc(array $data): Sudoku
  {
    return new \ApiSudoku\Model\Sudoku(
      $data['id'],
      $data['level'],
      $data['problem'],
      $data['solved'],
    );
  }

  // Needed for implicit JSON serialization with json_encode()
  public function jsonSerialize()
  {
    return [
      'id' => $this->id,
      'level' => $this->level,
      'problem' => $this->problem,
      'solved' => $this->solved,
    ];
  }
}
