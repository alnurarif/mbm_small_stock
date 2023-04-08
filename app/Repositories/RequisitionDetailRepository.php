<?php
namespace App\Repositories;

use App\Models\RequisitionDetail;

class RequisitionDetailRepository extends BaseRepository
{
    public function __construct(RequisitionDetail $model)
    {
        parent::__construct($model);
    }
}