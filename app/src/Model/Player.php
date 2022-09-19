<?php

namespace ApiSudoku\Model;

// Implements JsonSerializable for implicit json_encode() serialization
class Player implements \JsonSerializable
{
  private ?int $id;
  private string $username;
  private string $password;
  private string $email;

  public function __construct(?int $id, string $username, string $password, string $email)
  {
    $this->id = $id;
    $this->username = $username;
    $this->password = $password;
    $this->email = $email;
  }

  public function getId(): ?int
  {
    return $this->id;
  }
  public function setId(?int $id)
  {
    $this->id = $id;
  }
  public function getUsername(): string
  {
    return $this->username;
  }
  public function setUsername(string $username)
  {
    $this->username = $username;
  }
  public function getPassword(): string
  {
    return $this->password;
  }
  public function setPassword(string $password)
  {
    $this->password = $password;
  }
  public function getEmail(): string
  {
    return $this->email;
  }
  public function setEmail(string $email)
  {
    $this->email = $email;
  }

  // $length must be an even integer greater or equal to 4
  public static function randomPassword(int $length = 10)
  {
    $chars1 = "abcdefghijklmnopqrstuvwxyz";
    $chars2 = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $chars3 = "0123456789";
    $chars4 = "!$%&/+-_.:*";
    $password = substr(str_shuffle($chars1), 0, $length / 2 - 1);
    $password .= substr(str_shuffle($chars2), 0, $length / 2 - 1);
    $password .= substr(str_shuffle($chars3), 0, 1);
    $password .= substr(str_shuffle($chars4), 0, 1);
    $password = str_shuffle($password);
    return $password;
  }

  // Needed to deserialize an object from an associative array
  public static function fromAssoc(array $data): Player
  {
    return new \ApiSudoku\Model\Player(
      $data['id'],
      $data['username'],
      $data['password'],
      $data['email'],
    );
  }

  // Needed for implicit JSON serialization with json_encode()
  public function jsonSerialize()
  {
    return [
      'id' => $this->id,
      'username' => $this->username,
      'password' => $this->password,
      'email' => $this->email,
    ];
  }
}
