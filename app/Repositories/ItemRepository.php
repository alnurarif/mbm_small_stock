<?php
namespace App\Repositories;

use App\Models\Item;

class ItemRepository extends BaseRepository
{
    public function __construct(Item $model)
    {
        parent::__construct($model);
    }
}