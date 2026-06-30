<?php

namespace App\Repositories\Interfaces;

interface LeadRepositoryInterface extends BaseRepositoryInterface
{
    public function getLeadsBySource(string $source);
}
