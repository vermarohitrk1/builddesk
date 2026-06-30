<?php

namespace App\Services;

class LeadImportService
{
    protected LeadService $leadService;

    public function __construct(LeadService $leadService)
    {
        $this->leadService = $leadService;
    }

    public function fromWebsite(array $data)
    {
        return $this->leadService->create(array_merge($data, ['source' => 'website']));
    }

    public function fromFacebook(array $data)
    {
        return $this->leadService->create(array_merge($data, ['source' => 'facebook']));
    }

    public function fromGoogle(array $data)
    {
        return $this->leadService->create(array_merge($data, ['source' => 'google']));
    }

    public function fromWhatsApp(array $data)
    {
        return $this->leadService->create(array_merge($data, ['source' => 'whatsapp']));
    }
}
