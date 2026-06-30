<?php

namespace App\Repositories\Eloquent;

use App\Models\Organisation;
use App\Repositories\Interfaces\OrganisationRepositoryInterface;

class OrganisationRepository extends BaseRepository implements OrganisationRepositoryInterface
{
    public function __construct(Organisation $model)
    {
        parent::__construct($model);
    }
}
