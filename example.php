<?php
/**
 * Box Lib
 */
require 'box.php';

/**
 * Class Product
 */
class Product
{
    public $name = 'Test Product';
}

/**
 * Interface ProductRepositoryInterface
 */
interface ProductRepositoryInterface
{
    public function getName();
}

/**
 * Class ProductRepository
 */
class ProductRepository implements ProductRepositoryInterface
{
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function getName()
    {
        return $this->product->name;
    }
}

/**
 * Class ProductController
 */
class ProductController
{
    protected $repository;

    public function __construct(ProductRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getName()
    {
        return $this->repository->getName();
    }
}

/**
 * Initialize a new Box
 */
$box = new Box;

/**
 * Bind Product to the container
 */
$box->bind('Product', function ()
{
    return new Product;
});

/**
 * Bind Repository to the container
 */
$box->bind('ProductRepositoryInterface', function () use ($box)
{
    return new ProductRepository($box->make('Product'));
});

/**
 * Create new Reflection Class for the ProductController
 */
$class = new ReflectionClass('ProductController');

/**
 * Get the constructor
 */
$constructor = $class->getConstructor();

/**
 * Check with parameter the constructor needs
 */
$parameters = $constructor->getParameters();

$arguments = [];

/**
 * Get the objects from the box by parameter name
 */
foreach($parameters as $parameter)
{
    $arguments[] = $box->make(strval($parameter->getClass()->getName()));
}

/**
 * Create a new instance of the controller with the arguments that
 * where taken from the box
 */
$controller = $class->newInstanceArgs($arguments);

/**
 * Fire a test method
 */
echo $controller->getName()."\n";
