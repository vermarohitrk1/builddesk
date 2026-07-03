<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for leads
 *
 * @package    BuildDesk
 * @author     Rohit
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\Lead;
use App\Models\Customer;
use Log;
use DB;

class LeadRepository
{

    /**
     * The lead repository instance.
     */
    protected $lead;

    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

     /**
     * get all categories on a given type
     * @param string $type the type of the category
     * @return object
     */
    public function rows($id = null)
    {

        //new object
        $query = $this->lead->Query();

        //filters
        if ($id) {
            $query->where('id', $id);
        }

        //filter by organisation
        if (request()->filled('filter_organisation_id')) {
            $query->where('organisation_id', request('filter_organisation_id'));
        }

        $query->with(['measurements', 'measurements.items', 'quotations', 'quotations.items', 'followups', 'customer', 'project', 'project.createdBy', 'assignedTo']);

        //get
        return $query;

    }

    /**
     * Create a new record
     * @param array $data item qty by country json
     * @return mixed object|bool
     */
    public function create($data)
    {        
        return DB::transaction(function () use ($data) {
            $orgId = $data['organisation_id'];
            $mobile = $data['contact_mobile'];

            // 1. Check if customer already exists in this organisation
            $customer = Customer::where('organisation_id', $orgId)
                ->where('phone', $mobile)
                ->first();

            // 2. If no customer exists, create one
            if (!$customer) {
                $customer = Customer::create([
                    'organisation_id' => $orgId,
                    'name' => $data['contact_name'],
                    'phone' => $mobile,
                    'email' => $data['contact_email'] ?? null,
                    'address' => $data['address'] ?? null,
                ]);
            }

            // 3. Link lead to customer
            $data['customer_id'] = $customer->id;

            return $this->lead::create($data);
        });

    }

    /**
     * update
     * @param string $type the type of the category
     * @return object
     */
    public function update($id, $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $lead = $this->lead->findOrFail($id);
            $lead->fill($data);
            return $lead->save();
        });
    }

    /**
     * Assign lead to user
     * @param int $leadId lead id
     * @param int $userId user id
     * @return bool
     */
    public function assignLead(int $leadId, int $userId)
    {
        return $this->lead->where('id', $leadId)->update(['assigned_to' => $userId]);
    }
}
