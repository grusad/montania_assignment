<?php


namespace App\Command;


use App\Product;
use App\Program;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends Command
{

    protected static $defaultName = 'app:run';


    protected function configure()
    {
        $this->setDescription("Lists products.");
        $this->setHelp("This command runs the program");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $program = new Program();

        $rowCounter = 0;

        //Expensive product table
        $expensiveTable = new Table($output);
        $expensiveTable->setHeaders(["Most expensive product(s)"]);

        foreach ($program->getMostExpensiveProducts() as $product)
        {
            $expensiveTable->setRow($rowCounter++, [$product->getName()]);
        }

        $expensiveTable->render();

        //Cheap product table
        $cheapTable = new Table($output);
        $cheapTable->setHeaders(["Cheapest product(s)"]);

        foreach ($program->getCheapestProducts() as $product)
        {
            $cheapTable->setRow($rowCounter++, [$product->getName()]);
        }

        $cheapTable->render();

        //All product table
        $productsTable = new Table($output);
        $productsTable
            ->setHeaders(["Name", "Price", "In Stock", "Err flag"]);

        $productsTable->setRow($rowCounter++, ["Number of products: " .$program->getNumberOfProducts()]);

        foreach ($program->getProducts() as $category)
        {

            $productsTable->setRow($rowCounter++, [new TableSeparator()]);
            $productsTable->setRow($rowCounter++, [$category[0]->getCategory()]);
            $productsTable->setRow($rowCounter++, [""]);
            foreach ($category as $product)
            {

                $productsTable->setRow($rowCounter++, [
                    $product->getName(),
                    $product->getPrice(),
                    $product->getStock(),
                    (count($product->getErrors()) > 0) ? "*" : ""
                ]);
            }

        }

        $productsTable->render();

        return Command::SUCCESS;
    }
}