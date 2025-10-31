<?php

namespace App\Imports;

use App\Models\Customer;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomerImport implements ToModel, WithHeadingRow
{
    use Importable;

    /**
     * Map Excel rows to Customer model.
     */
    public function model(array $row)
    {
        // Validate required fields before inserting
        $validator = Validator::make($row, [
            'firm_name'      => 'required|string|max:100',
            'person_name'    => 'required|string|max:100',
            'contact_number' => 'required|numeric|unique:customers,contact_number',
            'email'          => 'nullable|email|unique:customers,email',
        ]);

        if ($validator->fails()) {
            // Skip invalid rows silently
            return null;
        }

        return new Customer([
            'firm_name'      => $row['firm_name'] ?? '',
            'person_name'    => $row['person_name'] ?? '',
            'contact_number' => $row['contact_number'] ?? '',
            'email'          => $row['email'] ?? '',
            'status'         => 'active', // Default active
        ]);
    }
}
