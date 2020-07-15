<?php

namespace Command;

use Core\Factory\EntityManagerFactory;
use Exception;
use Marketplace\Entity\Product;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateProductsCommand extends Command
{
    protected static $defaultName = 'app:create-products';

    protected function configure()
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $entityManagerFactory = new EntityManagerFactory();
            $entityManager = $entityManagerFactory->build();
            $count = 20;
            $currentCount = 0;
            while ($currentCount++ < $count) {
                $product = new Product();
                $product->setName('test' . $currentCount);
                $product->setAmount(rand(10, 100));
                $product->setIsEnable(true);
                $product->setIsSold(false);
                $entityManager->persist($product);
            }
            $entityManager->flush();

            $output->writeln('SUCCESS');

            return Command::SUCCESS;
        } catch (Exception $exception) {
            $output->writeln('FAILURE');

            return Command::FAILURE;
        }
    }
}