<?php


namespace App;


class Product
{
    private $name;
    private $price;
    private $stock;
    private $category;
    private $errors;

    public function __construct($name, $price, $stock, $category, $errors = [])
    {
        $this->name = $name;
        $this->price = $price;
        $this->stock = $stock;
        $this->category = $category;
        $this->errors = $errors;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getStock()
    {
        return $this->stock;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}