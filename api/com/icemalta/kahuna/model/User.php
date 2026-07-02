<?php
namespace com\icemalta\kahuna\model;

use \JsonSerializable;

class User implements JsonSerializable
{
    private static $db;
    private int $id;
    private ?string $name;
    private ?string $surname;
    private ?string $email;
    private ?string $password;
    private $role = 'client';

    public function __construct(?string $name = null, ?string $surname = null, ?string $email = null, ?string $password = null, ?string $role = 'client', ?int $id = 0)
    {
        $this->name = $name;
        $this->surname = $surname;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
        $this->id = $id;
        self::$db = DBConnect::getInstance()->getConnection();
    }

    public static function save(User $user): User
    {
        $hashed = password_hash($user->password, PASSWORD_DEFAULT);
        // Register new user
        $sql = 'INSERT INTO Users(name, surname, email, password, role) VALUES (:name, :surname, :email, :password, :role)';
        $sth = self::$db->prepare($sql);
        $sth->bindValue('name', $user->getName());
        $sth->bindValue('surname', $user->getSurname());
        $sth->bindValue('email', $user->getEmail());
        $sth->bindValue('password', $hashed);
        $sth->bindValue('role', $user->getRole());
        $sth->execute();

        if ($sth->rowCount() > 0) {
            $user->setId(self::$db->lastInsertId());
        }

        return $user;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'surname' => $this->surname,
            'email' => $this->email,
            'role' => $this->role
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getRole(): string
    {
        return $this->role;
    }
}