<?php
namespace App\Services\Scraper\Convert;

use App\Services\Scraper\Convert\Product;

class Products
{
    /** @var array */
    protected $products;

    public function __construct(array $products)
    {
        $this->products = $products;
    }

    public static function fromArray(array $edges): self
    {
        $products = collect($edges)->map(function ($edge) {
            return Product::fromArray($edge);
        })->toArray();

        return new self($products);
    }

    public function getProducts(): array
    {
        return $this->products;
    }
}