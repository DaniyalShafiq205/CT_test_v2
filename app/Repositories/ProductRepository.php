<?php
namespace App\Repositories;

use Carbon\Carbon;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductRepository implements ProductRepositoryInterface
{
    protected $jsonFileName = 'products.json';

    public function __construct()
    {
        if (!Storage::exists($this->jsonFileName)) {
            $this->initializeJSONFile();
        }
    }

    public function all()
    {
        return $this->getProductsFromJSON();
    }

    public function store(array $data)
    {
        $products = $this->getProductsFromJSON();

        $product = [
            'id' => count($products) + 1, // Generate a unique ID, adjust as per your needs
            'name' => $data['name'],
            'quantity' => $data['quantity'],
            'price' => $data['price'],
            'created_at' => Carbon::now()->toDateTimeString(),
        ];

        $products[] = $product;
        $this->storeProductsToJSON($products);

        return $product;
    }

    public function update($id, array $data)
    {
        $products = $this->getProductsFromJSON();

        foreach ($products as &$product) {
            if ($product['id'] == $id) {
                $product['name'] = $data['name'];
                $product['quantity'] = $data['quantity'];
                $product['price'] = $data['price'];
                break;
            }
        }

        $this->storeProductsToJSON($products);

        return $data;
    }

    public function delete($id)
    {
        $products = $this->getProductsFromJSON();

        foreach ($products as $key => $product) {
            if ($product['id'] == $id) {
                unset($products[$key]);
                break;
            }
        }

        $this->storeProductsToJSON(array_values($products)); // Re-index array keys

        return true;
    }

    protected function getProductsFromJSON()
    {
        $json = Storage::get($this->jsonFileName);
        return json_decode($json, true);
    }

    protected function storeProductsToJSON($products)
    {
        Storage::put($this->jsonFileName, json_encode($products));
    }

    protected function initializeJSONFile()
    {
        Storage::put($this->jsonFileName, '[]');
    }
}
