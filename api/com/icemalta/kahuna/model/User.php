<?php
namespace com\icemalta\kahuna\model;

use \JsonSerializable;
use \PDO;

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

    public static function authenticate(User $user): ?User
    {
        $sql = 'SELECT * FROM Users WHERE email = :email';
        $sth = self::$db->prepare($sql);
        $sth->bindValue('email', $user->email);
        $sth->execute();

        $result = $sth->fetch(PDO::FETCH_OBJ);
        if ($result && password_verify($user->password, $result->password)) {
            return new User(
                name: $result->name,
                surname: $result->surname,
                email: $result->email,
                password: $result->password,
                role: $result->role,
                id: $result->user_id
            );
        }
        return null;
    }

    public static function saveToken(User $user, string $token): bool
    {
        $sql = 'UPDATE Users SET token = :token WHERE user_id = :id';
        $sth = self::$db->prepare($sql);
        $sth->bindValue('token', $token);
        $sth->bindValue('id', $user->getId());
        $sth->execute();
        return $sth->rowCount() > 0;
    }

    public static function verifyToken(int $userId, string $token): bool
    {
        self::$db = DBConnect::getInstance()->getConnection();
        $sql = 'SELECT * FROM Users WHERE user_id = :id AND token = :token';
        $sth = self::$db->prepare($sql);
        $sth->bindValue('id', $userId);
        $sth->bindValue('token', $token);
        $sth->execute();
        return (bool) $sth->fetch(PDO::FETCH_OBJ);
    }

    public static function verifyAdmin(int $userId, string $token): bool
    {
        self::$db = DBConnect::getInstance()->getConnection();
        $sql = 'SELECT * FROM Users WHERE user_id = :id AND token = :token AND role = :role';
        $sth = self::$db->prepare($sql);
        $sth->bindValue('id', $userId);
        $sth->bindValue('token', $token);
        $sth->bindValue('role', 'admin');
        $sth->execute();
        return (bool) $sth->fetch(PDO::FETCH_OBJ);
    }

    public static function deleteToken(User $user): bool
    {
        self::$db = DBConnect::getInstance()->getConnection();
        $sql = 'UPDATE Users SET token = NULL WHERE user_id = :id';
        $sth = self::$db->prepare($sql);
        $sth->bindValue('id', $user->getId());
        $sth->execute();
        return $sth->rowCount() > 0;
    }

    public static function getInfo(User $user): object
    {
        self::$db = DBConnect::getInstance()->getConnection();
        $sql = 'SELECT user_id, name, surname, email, role FROM Users WHERE user_id = :id';
        $sth = self::$db->prepare($sql);
        $sth->bindValue('id', $user->getId());
        $sth->execute();
        $result = $sth->fetch(PDO::FETCH_OBJ);
        return $result;
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