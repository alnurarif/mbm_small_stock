<?php
namespace App\Repositories;

use App\Models\Requisition;

class RequisitionRepository extends BaseRepository
{
    public function __construct(Requisition $model)
    {
        parent::__construct($model);
    }
}