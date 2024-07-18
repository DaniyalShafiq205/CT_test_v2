<?php
namespace App\Repositories;

interface ProductRepositoryInterface
{
    public function all();
    public function store(array $data);
    public function update($id, array $data);
    public function delete($id);
}
