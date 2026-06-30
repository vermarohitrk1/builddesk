<?php

namespace App\Services;

use App\Repositories\Interfaces\LeadRepositoryInterface;

class LeadService extends BaseService
{
    public function __construct(LeadRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function create(array $data)
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            $orgId = $data['organisation_id'];
            $mobile = $data['contact_mobile'];

            // 1. Check if customer already exists in this organisation
            $customer = \App\Models\Customer::where('organisation_id', $orgId)
                ->where('phone', $mobile)
                ->first();

            // 2. If no customer exists, create one
            if (!$customer) {
                $customer = \App\Models\Customer::create([
                    'organisation_id' => $orgId,
                    'name' => $data['contact_name'],
                    'phone' => $mobile,
                    'email' => $data['contact_email'] ?? null,
                    'address' => $data['address'] ?? null,
                ]);
            }

            // 3. Link lead to customer
            $data['customer_id'] = $customer->id;

            return parent::create($data);
        });
    }

    public function assignLead(int $leadId, int $userId)
    {
        return $this->update($leadId, ['assigned_to' => $userId]);
    }
}
