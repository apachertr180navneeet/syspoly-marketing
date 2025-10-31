<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Validator;
use Exception;

use App\Imports\CustomerImport;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{
    /**
     * Show customers index page.
     */
    public function index()
    {
        return view('admin.customer.index');
    }

    /**
     * Fetch all customers.
     */
    public function getall(Request $request)
    {
        $customers = Customer::orderBy('id', 'desc')->get();

        return response()->json([
            'success' => true,
            'data'    => $customers
        ], 200);
    }

    /**
     * Store a new customer.
     */
    public function store(Request $request)
    {
        $rules = [
            'firm_name'      => 'required|string|max:255',
            'person_name'    => 'required|string|max:255',
            'contact_number' => 'required|regex:/^[0-9]+$/|digits_between:10,15|unique:customers,contact_number',
            'email'          => 'required|email|unique:customers,email',
            'logo'           => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $logoPath = null;

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/customers'), $filename);

            // ✅ Save full URL
            $logoPath = url('uploads/customers/' . $filename);
        }

        $customer = Customer::create([
            'firm_name'      => $request->firm_name,
            'person_name'    => $request->person_name,
            'contact_number' => $request->contact_number,
            'email'          => $request->email,
            'logo'           => $logoPath,
            'status'         => 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Customer created successfully!',
            'data'    => $customer,
        ], 201);
    }

    /**
     * Fetch a single customer.
     */
    public function get($id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $customer,
        ], 200);
    }

    /**
     * Update a customer.
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'firm_name'      => 'required|string|max:255',
            'person_name'    => 'required|string|max:255',
            'contact_number' => 'required|regex:/^[0-9]+$/|digits_between:10,15|unique:customers,contact_number,' . $id,
            'email'          => 'required|email|unique:customers,email,' . $id,
            'logo'           => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found',
            ], 404);
        }

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/customers'), $filename);

            // ✅ Save full URL again
            $customer->logo = url('uploads/customers/' . $filename);
        }

        $customer->update([
            'firm_name'      => $request->firm_name,
            'person_name'    => $request->person_name,
            'contact_number' => $request->contact_number,
            'email'          => $request->email,
            'logo'           => $customer->logo,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Customer updated successfully!',
            'data'    => $customer,
        ], 200);
    }

    /**
     * Update status.
     */
    public function status(Request $request)
    {
        try {
            $customer = Customer::find($request->id);

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found',
                ], 404);
            }

            $customer->status = $request->status;
            $customer->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully!',
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Soft delete a customer.
     */
    public function destroy($id)
    {
        try {
            $customer = Customer::find($id);

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found',
                ], 404);
            }

            $customer->delete();

            return response()->json([
                'success' => true,
                'message' => 'Customer deleted successfully',
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Import customers from an Excel file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            Excel::import(new CustomerImport, $request->file('import_file'));

            return response()->json([
                'success' => true,
                'message' => 'Customers imported successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error importing file: ' . $e->getMessage()
            ]);
        }
    }
}
