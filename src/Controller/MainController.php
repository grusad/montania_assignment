<?php


namespace App\Controller;


use App\Article;
use App\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class MainController extends AbstractController
{

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

        $err = array();

        $name =     $this->getPropertyFromData($itemData, "artiklar_benamning", $err, "No name info");
        $price =    $this->getPropertyFromData($itemData, "pris", $err, "No price info");
        $stock =    $this->getPropertyFromData($itemData, "lagersaldo", $err, "No stock info");
        $category = $this->getPropertyFromData($itemData, "artikelkategorier_id", $err, "No category") ;
        $VAT =      $this->getPropertyFromData($itemData, "momssats", $err, "No VAT");

        if ($price != null && $VAT != null) $price *= $VAT * 0.01 + 1.0;
        if ($name == null) $name = "[Unnamed]";
        if ($category == null) $category = "Uncategorized";
        $stock = ($stock > 0) ? "Yes" : "No";

        $product = new Product($name, $price, $stock, $category, $err);
        $this->inspectProduct($product);

        return $product;
    }

    // Get data from stdClass. Returns value or null if none existent
    private function getPropertyFromData($source, $property, &$err, $errMessage = "ERR")
    {
        if(!array_key_exists($property, $source))
        {
            array_push($err, $errMessage);
            return null;
        }
        return $source->{$property};
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