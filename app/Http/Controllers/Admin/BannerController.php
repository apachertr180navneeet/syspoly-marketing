<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{
    Banner,
    Customer
};
use Validator;
use Exception;

class BannerController extends Controller
{
    /**
     * Show the banner index page.
     */
    public function index()
    {
        return view('admin.banner.index');
    }

    /**
     * Fetch all banners (latest first).
     */
    public function getall(Request $request)
    {
        $banners = Banner::orderBy('id', 'desc')->get();

        return response()->json([
            'success' => true,
            'data'    => $banners
        ], 200);
    }

    /**
     * Store a new banner.
     */
    public function store(Request $request)
    {
        $rules = [
            'name'             => 'required|string|max:255',
            'whatsappcontent'  => 'nullable|string',
            'emailcontant'     => 'nullable|string',
            'image'            => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $imagePath = null;

        // ✅ Handle image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/banners'), $filename);
            $imagePath = 'uploads/banners/' . $filename;
        }

        // ✅ Create banner record
        $banner = Banner::create([
            'name'             => $request->name,
            'whatsappcontent'  => $request->whatsappcontent,
            'emailcontant'     => $request->emailcontant,
            'image'            => $imagePath,
            'status'           => 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Banner created successfully!',
            'data'    => $banner,
        ], 201);
    }

    /**
     * Fetch a single banner by ID.
     */
    public function get($id)
    {
        $banner = Banner::find($id);

        if (!$banner) {
            return response()->json([
                'success' => false,
                'message' => 'Banner not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $banner,
        ], 200);
    }

    /**
     * Update an existing banner.
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'name'             => 'required|string|max:255',
            'whatsappcontent'  => 'nullable|string',
            'emailcontant'     => 'nullable|string',
            'image'            => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $banner = Banner::find($id);

        if (!$banner) {
            return response()->json([
                'success' => false,
                'message' => 'Banner not found',
            ], 404);
        }

        // ✅ Handle new image upload if available
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/banners'), $filename);
            $banner->image = 'uploads/banners/' . $filename;
        }

        // ✅ Update banner data
        $banner->update([
            'name'             => $request->name,
            'whatsappcontent'  => $request->whatsappcontent,
            'emailcontant'     => $request->emailcontant,
            'image'            => $banner->image,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Banner updated successfully!',
            'data'    => $banner,
        ], 200);
    }

    /**
     * Update banner status (active/inactive).
     */
    public function status(Request $request)
    {
        try {
            $banner = Banner::find($request->id);

            if (!$banner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Banner not found',
                ], 404);
            }

            $banner->status = $request->status;
            $banner->save();

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
     * Soft delete a banner.
     */
    public function destroy($id)
    {
        try {
            $banner = Banner::find($id);

            if (!$banner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Banner not found',
                ], 404);
            }

            $banner->delete();

            return response()->json([
                'success' => true,
                'message' => 'Banner deleted successfully!',
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the logo management page.
     */
    public function logo($id)
    {
        // Find banner by ID
        $banner = Banner::find($id);

        // Check if banner exists
        if (!$banner) {
            abort(404);
        }

        // Get all active customers
        $customers = Customer::where('status', 'active')->get();

        // Return view with banner and active customers
        return view('admin.banner.set-logo', compact('banner', 'customers'));
    }

    /**
    * Show the logo management page.
     */
    public function logoupdate(Request $request, $id)
    {
        echo $id;

        print_r($request->all());
        die;
        // Find banner by ID
        $banner = Banner::find($id);

        // Check if banner exists
        if (!$banner) {
            abort(404);
        }

        // Get all active customers
        $customers = Customer::where('status', 'active')->get();

        // Return view with banner and active customers
        return view('admin.banner.preview_send_image', compact('banner', 'customers'));
    }
}
