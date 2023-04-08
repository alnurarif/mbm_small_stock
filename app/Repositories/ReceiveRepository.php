<?php
namespace App\Repositories;

use App\Models\Receive;

class ReceiveRepository extends BaseRepository
{
    public function __construct(Receive $model)
    {
        parent::__construct($model);
    }
}