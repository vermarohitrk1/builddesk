<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data abstraction for organisations
 *
 * @package    BuildDesk
 * @author     Rohit
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\Organisation;
use Illuminate\Support\Facades\DB;

class OrganisationRepository
{
    /**
     * The organisation model instance.
     */
    protected $organisation;

    public function __construct(Organisation $organisation)
    {
        $this->organisation = $organisation;
    }

    /**
     * get all rows
     * @param int|null $id the id of the row
     * @return object
     */
    public function rows($id = null)
    {
        $query = $this->organisation->newQuery();

        if ($id) {
            $query->where('id', $id);
        }

        return $query;
    }

    public function getById($id)
    {
        return $this->organisation->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->organisation->create($data);
    }

    public function update($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $organisation = $this->organisation->findOrFail($id);
            $organisation->fill($data);
            return $organisation->save();
        });
    }
}
