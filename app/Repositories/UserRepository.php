<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data abstraction for users
 *
 * @package    BuildDesk
 * @author     Rohit
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\User;
use DB;

class UserRepository
{
    /**
     * The user model instance.
     */
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * get all rows
     * @param int|null $id the id of the row
     * @return object
     */
    public function rows($id = null)
    {
        $query = $this->user->newQuery();

        if ($id) {
            $query->where('id', $id);
        }

        // filter by organisation
        if (request()->filled('filter_organisation_id')) {
            $query->where('organisation_id', request('filter_organisation_id'));
        }

        return $query;
    }

    /**
     * Create a new record
     * @param array $data item qty by country json
     * @return mixed object|bool
     */
    public function create($data)
    {        
        return $this->user->create($data);
    }

    /**
     * update
     * @param int $id the id of the row
     * @param array $data the data to update
     * @return object
     */
    public function update($id, $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $user = $this->user->findOrFail($id);
            $user->fill($data);
            return $user->save();
        });
    }
}
