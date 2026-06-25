<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    // Show login page
    public function showLogin()
    {
        if (Auth::check()) {
            return Auth::user()->role === 'admin' ? redirect('/admin/dashboard') : redirect('/');
        }
        return view('auth.login');
    }

    // Handle Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && $user->status === 'inactive') {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda dinonaktifkan oleh Admin. Silakan hubungi customer service.'
            ], 403);
        }

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            $redirectUrl = Auth::user()->role === 'admin' ? '/admin/dashboard' : '/';
            return response()->json([
                'success' => true,
                'redirect' => $redirectUrl,
                'message' => 'Login Berhasil!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Email atau password salah.'
        ], 401);
    }

    // Show register page
    public function showRegister()
    {
        // We can fetch provinces for registration dropdown from RajaOngkir
        $provinces = [];
        $apiKey = env('RAJAONGKIR_API_KEY');
        
        // Native HTTP call to RajaOngkir
        try {
            $response = Http::withHeaders([
                'key' => $apiKey
            ])->get('https://api.rajaongkir.com/starter/province');

            if ($response->successful()) {
                $provinces = $response->json()['rajaongkir']['results'] ?? [];
            }
        } catch (\Exception $e) {
            // Mocks if offline or API key invalid
            $provinces = [
                ['province_id' => 9, 'province' => 'Jawa Barat'],
                ['province_id' => 11, 'province' => 'Jawa Timur'],
                ['province_id' => 10, 'province' => 'Jawa Tengah'],
                ['province_id' => 6, 'province' => 'DKI Jakarta'],
                ['province_id' => 5, 'province' => 'DI Yogyakarta'],
            ];
        }

        return view('auth.register', compact('provinces'));
    }

    // Handle register
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'required',
            'province_id' => 'required',
            'city_id' => 'required',
            'address' => 'required',
            'postal_code' => 'required',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $avatarPath = '/assets/images/avatar.png'; // default
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/avatars'), $filename);
            $avatarPath = '/uploads/avatars/' . $filename;
        }

        // Fetch province and city names
        $provinceName = $request->province_name ?? 'Jawa Barat';
        $cityName = $request->city_name ?? 'Bandung';

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer',
            'status' => 'active',
            'phone' => $request->phone,
            'avatar' => $avatarPath,
            'province_id' => $request->province_id,
            'city_id' => $request->city_id,
            'province' => $provinceName,
            'city' => $cityName,
            'address' => $request->address,
            'postal_code' => $request->postal_code,
        ]);

        Auth::login($user);

        return response()->json([
            'success' => true,
            'redirect' => '/',
            'message' => 'Registrasi Berhasil!'
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    // Get cities based on province ID
    public function getCities($province_id)
    {
        $apiKey = env('RAJAONGKIR_API_KEY');
        try {
            $response = Http::withHeaders([
                'key' => $apiKey
            ])->get("https://api.rajaongkir.com/starter/city?province={$province_id}");

            if ($response->successful()) {
                return response()->json($response->json()['rajaongkir']['results'] ?? []);
            }
        } catch (\Exception $e) {}

        // Mock response if failed / mock key
        $mocks = [
            9 => [
                ['city_id' => 23, 'city_name' => 'Bandung', 'type' => 'Kota', 'postal_code' => '40111'],
                ['city_id' => 54, 'city_name' => 'Bekasi', 'type' => 'Kota', 'postal_code' => '17111'],
                ['city_id' => 78, 'city_name' => 'Bogor', 'type' => 'Kota', 'postal_code' => '16111'],
            ],
            11 => [
                ['city_id' => 444, 'city_name' => 'Surabaya', 'type' => 'Kota', 'postal_code' => '60111'],
                ['city_id' => 256, 'city_name' => 'Malang', 'type' => 'Kota', 'postal_code' => '65111'],
            ],
        ];

        return response()->json($mocks[$province_id] ?? []);
    }
}
