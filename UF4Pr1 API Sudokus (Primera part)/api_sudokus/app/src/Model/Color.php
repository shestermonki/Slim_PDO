<?php

namespace ApiSudoku\Model;

class Color implements \JsonSerializable
{
  private ?int $id;
  private string $name;
  private int $red;
  private int $green;
  private int $blue;

  public function __construct(?int $id, string $name, int $red, int $green, int $blue)
  {
    $this->id = $id;
    $this->name = $name;
    $this->red = $red;
    $this->green = $green;
    $this->blue = $blue;
  }

  public function setId(?int $id)
  {
    $this->id = $id;
  }
  public function getId(): ?int
  {
    return $this->id;
  }
  public function setName(?string $name)
  {
    $this->name = $name;
  }
  public function getName(): ?string
  {
    return $this->name;
  }
  public function setRed(?int $red)
  {
    $this->red = $red;
  }
  public function getRed(): ?int
  {
    return $this->red;
  }
  public function setGreen(?int $green)
  {
    $this->green = $green;
  }
  public function getGreen(): ?int
  {
    return $this->green;
  }
  public function setBlue(?int $blue)
  {
    $this->blue = $blue;
  }
  public function getBlue(): ?int
  {
    return $this->blue;
  }

  // Needed to deserialize an object from an associative array
  public static function fromAssoc(array $data): Color
  {
    return new \ApiSudoku\Model\Color(
      $data['id'],
      $data['name'],
      $data['red'],
      $data['green'],
      $data['blue']
    );
  }

  // Needed for implicit JSON serialization with json_encode()
  public function jsonSerialize()
  {
    return [
      'id' => $this->id,
      'name' => $this->name,
      'red' => $this->red,
      'green' => $this->green,
      'blue' => $this->blue,
    ];
  }
}
