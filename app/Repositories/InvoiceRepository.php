<?php

namespace App\Repositories;

use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class InvoiceRepository
{
    protected $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function rows($id = null)
    {
        $query = $this->invoice->newQuery();

        if ($id) {
            $query->where('id', $id);
        }

        return $query;
    }

    public function getById($id)
    {
        return $this->invoice->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->invoice->create($data);
    }

    public function update($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $invoice = $this->invoice->findOrFail($id);
            $invoice->fill($data);
            return $invoice->save();
        });
    }
}
