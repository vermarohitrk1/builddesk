<?php

namespace App\Repositories\Eloquent;

use App\Models\Lead;
use App\Repositories\Interfaces\LeadRepositoryInterface;

class LeadRepository extends BaseRepository implements LeadRepositoryInterface
{
    public function __construct(Lead $model)
    {
        parent::__construct($model);
    }

    public function getLeadsBySource(string $source)
    {
        return $this->model->where('source', $source)->get();
    }
}
