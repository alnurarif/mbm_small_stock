<?php
namespace App\Repositories;

use App\Models\IssueDetail;

class IssueDetailRepository extends BaseRepository
{
    public function __construct(IssueDetail $model)
    {
        parent::__construct($model);
    }
}