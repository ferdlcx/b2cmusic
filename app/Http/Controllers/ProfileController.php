<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{

    /**
     * Show the user profile page with addresses.
     */
    public function show()
    {
        $user = Auth::user();
        $addresses = $user->addresses()->orderBy('is_default', 'desc')->orderBy('created_at', 'asc')->get();

        return view('auth.profile', compact('user', 'addresses'));
    }

    /**
     * Update the user's basic profile information.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::defaults()],
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password berhasil diubah.');
    }

    /**
     * Store a new address.
     */
    public function storeAddress(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'label'       => ['required', 'string', 'max:50'],
            'name'        => ['required', 'string', 'max:255'],
            'phone'       => ['required', 'string', 'max:20'],
            'address'     => ['required', 'string'],
            'city'        => ['required', 'string', 'max:100'],
            'area_id'     => ['nullable'],
            'province'    => ['required', 'string', 'max:100'],
            'district'    => ['nullable', 'string', 'max:100'],
            'village'     => ['nullable', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:10'],
            'latitude'    => ['nullable', 'numeric'],
            'longitude'   => ['nullable', 'numeric'],
            'is_default'  => ['nullable', 'boolean'],
        ]);

        try {
            $isDefault = $request->boolean('is_default');

            // If this is the first address, ALWAYS make it default
            $isFirstAddress = $user->addresses()->count() === 0;
            if ($isFirstAddress) {
                $isDefault = true;
            }

            // If this is set as default, unset previous default
            if ($isDefault) {
                $user->addresses()->where('is_default', true)->update(['is_default' => false]);
            }

            $address = $user->addresses()->create(array_merge($validated, ['is_default' => $isDefault]));

            // Return JSON for AJAX/API requests (e.g., from checkout page)
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Alamat berhasil ditambahkan.',
                    'address' => $address,
                ], 201);
            }

            return back()->with('success', 'Alamat berhasil ditambahkan.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Store address failed: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan alamat: ' . $e->getMessage(),
                ], 500);
            }

            return back()->withErrors(['address' => 'Gagal menyimpan alamat.']);
        }
    }

    /**
     * Update an existing address.
     */
    public function updateAddress(Request $request, Address $address)
    {
        // Ensure the address belongs to the authenticated user
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'label'       => ['required', 'string', 'max:50'],
            'name'        => ['required', 'string', 'max:255'],
            'phone'       => ['required', 'string', 'max:20'],
            'address'     => ['required', 'string'],
            'city'        => ['required', 'string', 'max:100'],
            'area_id'     => ['nullable'],
            'province'    => ['required', 'string', 'max:100'],
            'district'    => ['nullable', 'string', 'max:100'],
            'village'     => ['nullable', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:10'],
            'latitude'    => ['nullable', 'numeric'],
            'longitude'   => ['nullable', 'numeric'],
            'is_default'  => ['nullable', 'boolean'],
        ]);

        $isDefault = $request->boolean('is_default');

        if ($isDefault) {
            Auth::user()->addresses()->where('id', '!=', $address->id)->where('is_default', true)->update(['is_default' => false]);
        }

        $address->update(array_merge($validated, ['is_default' => $isDefault]));

        return back()->with('success', 'Alamat berhasil diperbarui.');
    }

    /**
     * Delete an address.
     */
    public function destroyAddress(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $wasDefault = $address->is_default;
        $address->delete();

        // If deleted address was the default, set first remaining as default
        if ($wasDefault) {
            $first = Auth::user()->addresses()->first();
            if ($first) {
                $first->update(['is_default' => true]);
            }
        }

        return back()->with('success', 'Alamat berhasil dihapus.');
    }

    /**
     * Set an address as the default.
     */
    public function setDefaultAddress(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        Auth::user()->addresses()->where('is_default', true)->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return back()->with('success', 'Alamat utama berhasil diubah.');
    }

    /**
     * Upload profile photo.
     */
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => ['required', 'image', 'max:2048'],
        ]);

        $user = Auth::user();

        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->profile_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile_photo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_photo);
            }

            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->update(['profile_photo' => $path]);

            return back()->with('success', 'Foto profil berhasil diperbarui.');
        }

        return back()->withErrors(['profile_photo' => 'Gagal mengunggah foto profil.']);
    }
}
