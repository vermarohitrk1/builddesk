<?php

namespace App\Services;

use App\Repositories\Interfaces\OrganisationRepositoryInterface;

class OrganisationService extends BaseService
{
    public function __construct(OrganisationRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
