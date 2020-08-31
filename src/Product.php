<?php


namespace App;


class Product
{
    private $name;
    private $price;
    private $stock;
    private $category;

    public function __construct($name, $price, $stock, $category)
    {
        $this->name = $name;
        $this->price = $price;
        $this->stock = $stock;
        $this->category = $category;
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
}