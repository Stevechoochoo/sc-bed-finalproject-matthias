<?php
namespace com\icemalta\kahuna\model;

use \JsonSerializable;
use \PDO;

class Product implements JsonSerializable
{
    private static $db;
    private int $id;
    private ?string $serialNumber;
    private ?string $productName;
    private ?int $warranty;
    private ?string $purchaseDate;

    public function __construct(?string $serialNumber = null, ?string $productName = null, ?int $warranty = 0, ?string $purchaseDate = null, ?int $id = 0)
    {
        $this->serialNumber = $serialNumber;
        $this->productName = $productName;
        $this->warranty = $warranty;
        $this->purchaseDate = $purchaseDate;
        $this->id = $id;
        self::$db = DBConnect::getInstance()->getConnection();
    }

    public static function getBySerialNumber(Product $product): Product|array
    {
        $sql = 'SELECT serial_number, product_name, warranty, NULL, product_id FROM Products WHERE serial_number = :serialNumber';
        $sth = self::$db->prepare($sql);
        $sth->bindValue('serialNumber', $product->getSerialNumber());
        $sth->execute();
        $product = $sth->fetchAll(PDO::FETCH_FUNC, fn(...$fields) => new Product(...$fields));
        return count($product) > 0 ? $product[0] : [];
    }

    public static function getAll(): array
    {
        self::$db = DBConnect::getInstance()->getConnection();
        $sql = 'SELECT serial_number, product_name, warranty, NULL, product_id FROM Products';
        $sth = self::$db->prepare($sql);
        $sth->execute();
        $products = $sth->fetchAll(PDO::FETCH_FUNC, fn(...$fields) => new Product(...$fields));
        return $products;
    }

    public static function getRegistered(User $user): array
    {
        self::$db = DBConnect::getInstance()->getConnection();
        $sql = 'SELECT Products.serial_number, Products.product_name, Products.warranty, RegisteredProducts.purchase_date, Products.product_id FROM RegisteredProducts INNER JOIN Products ON RegisteredProducts.product_id = Products.product_id WHERE RegisteredProducts.user_id = :userId';
        $sth = self::$db->prepare($sql);
        $sth->bindValue('userId', $user->getId());
        $sth->execute();
        $products = $sth->fetchAll(PDO::FETCH_FUNC, fn(...$fields) => new Product(...$fields));
        return $products;
    }

    public static function register(User $user, Product $product): Product
    {
        $sql = 'INSERT INTO RegisteredProducts(user_id, product_id, purchase_date) VALUES (:userId, :productId, :purchaseDate)';
        $sth = self::$db->prepare($sql);
        $sth->bindValue('userId', $user->getId());
        $sth->bindValue('productId', $product->getId());
        $sth->bindValue('purchaseDate', $product->getPurchaseDate());
        $sth->execute();

        return $product;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
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

    public function getSerialNumber(): string
    {
        return $this->serialNumber;
    }

    public function setSerialNumber(string $serialNumber): self
    {
        $this->serialNumber = $serialNumber;
        return $this;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function setProductName(string $productName): self
    {
        $this->productName = $productName;
        return $this;
    }

    public function getWarranty(): int
    {
        return $this->warranty;
    }

    public function setWarranty(int $warranty): self
    {
        $this->warranty = $warranty;
        return $this;
    }

    public function getPurchaseDate(): string
    {
        return $this->purchaseDate;
    }

    public function setPurchaseDate(string $purchaseDate): self
    {
        $this->purchaseDate = $purchaseDate;
        return $this;
    }
}