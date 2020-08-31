<?php


namespace App\Controller;


use App\Article;
use App\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class MainController extends AbstractController
{

    private const ERR_NAME = '[No name info]';
    private const ERR_PRICE = '[No price info]';
    private const ERR_STOCK_FLAG = '[No stock info]';
    private const ERR_CATEGORY = "UNCATEGORIZED";

    private $mostExpensiveProducts = [];
    private $cheapestProducts = [];

    /**
     * @Route("/")
     */
    public function homepage()
    {

        $content = $this->requestData();
        $items = $this->generateProducts($content);
        $this->sortProducts($items);

         return $this->render('questions/show.html.twig',
            ['products' => $items,
                'mostExpensiveProducts' => $this->mostExpensiveProducts,
                'cheapestProducts' => $this->cheapestProducts,
                'numberOfProducts' => array_sum(array_map("count", $items))]
        );

    }

    private function requestData()
    {
        define("URL", "https://dev14.ageraehandel.se/sv/api/product");
        $data = file_get_contents(URL);
        return json_decode($data)->products;

    }

    private function generateProducts($content)
    {
        $items = array();
        foreach ($content as $item)
        {

            $article = $this->buildProduct($item);
            $key = $article->getCategory();

            if(array_key_exists($key, $items))
            {
                $items[$key] = array_merge([$article], $items[$key]);
            }
            else
            {
                $items[$key] = [$article];
            }
        }
        return $items;
    }

    //Sorts every product in each category
    private function sortProducts(&$items)
    {
        $categories = array_keys($items);

        foreach ($categories as $category)
        {
            usort($items[$category], function($a, $b)
            {
                return strcmp($a->getName(), $b->getName());
            });
        }
    }

    // returns a Product object based on data provided as an argument
    private function buildProduct($itemData)
    {

        $name = (array_key_exists("artiklar_benamning", $itemData))
            ? $itemData->artiklar_benamning : self::ERR_NAME;

        $price = (array_key_exists("pris", $itemData))
            ? $itemData->pris : self::ERR_PRICE;

        $stock = (array_key_exists("lagersaldo", $itemData))
            ? $itemData->lagersaldo : self::ERR_STOCK_FLAG;

        $category = (array_key_exists("artikelkategorier_id", $itemData))
            ? $itemData->artikelkategorier_id : self::ERR_CATEGORY;

        $VAT = (array_key_exists("momssats", $itemData))
            ? $itemData->momssats : 1;

        $price *= $VAT * 0.01 + 1.0;

        $product = new Product($name, $price, $stock, $category);

        $this->inspectProduct($product);

        return $product;
    }

    //Stores the cheapest and most expensive products in arrays
    private function inspectProduct($product)
    {
        // most expensive products array is not empty, compare
        if(!empty($this->mostExpensiveProducts)){
            //Is it more expensive?
            $current = $this->mostExpensiveProducts[0]->getPrice();
            if($product->getPrice() > $current)
            {
                $this->mostExpensiveProducts = array($product);
            }
            // Same price?
            elseif ($product->getPrice() == $current)
            {
                array_push($this->mostExpensiveProducts, $product);
            }
        }
        else { // insert product in empty array
            array_push($this->mostExpensiveProducts, $product);
        }

        // cheapest products array is not empty, compare
        if(!empty($this->cheapestProducts)){
            //Is it even cheaper?
            $current = $this->cheapestProducts[0]->getPrice();
            if($product->getPrice() < $current)
            {
                $this->cheapestProducts = array($product);
            }
            // Same price?
            elseif ($product->getPrice() == $current)
            {
                array_push($this->cheapestProducts, $product);
            }
        }
        else { // insert product in empty array
            array_push($this->cheapestProducts, $product);
        }
    }

}