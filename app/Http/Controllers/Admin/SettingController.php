<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Slider;
use App\Models\Setting;

class SettingController extends Controller
{
    // ================= SLIDERS =================
    public function sliders()
    {
        return view('admin.settings.sliders');
    }

    public function sliderData()
    {
        return response()->json(['data' => Slider::all()]);
    }

    public function storeSlider(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048',
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'link' => 'nullable|string',
        ]);

        $imagePath = '';
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/sliders'), $filename);
            $imagePath = '/uploads/sliders/' . $filename;
        }

        Slider::create([
            'image_path' => $imagePath,
            'title' => $request->title,
            'description' => $request->description,
            'link' => $request->link,
        ]);

        return response()->json(['success' => true, 'message' => 'Slider berhasil ditambahkan.']);
    }

    public function showSlider($id)
    {
        return response()->json(Slider::findOrFail($id));
    }

    public function updateSlider(Request $request, $id)
    {
        $slider = Slider::findOrFail($id);
        $request->validate([
            'image' => 'nullable|image|max:2048',
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'link' => 'nullable|string',
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'link' => $request->link,
        ];

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/sliders'), $filename);
            $data['image_path'] = '/uploads/sliders/' . $filename;
        }

        $slider->update($data);

        return response()->json(['success' => true, 'message' => 'Slider berhasil diperbarui.']);
    }

    public function destroySlider($id)
    {
        $slider = Slider::findOrFail($id);
        $slider->delete();
        return response()->json(['success' => true, 'message' => 'Slider berhasil dihapus.']);
    }


    // ================= GENERAL WEBSITE SETTINGS =================
    public function website()
    {
        $settings = [
            'website_name' => Setting::getValue('website_name', 'Ecommerce Seragam'),
            'logo_path' => Setting::getValue('logo_path', '/assets/images/logo.png'),
            'facebook_url' => Setting::getValue('facebook_url', ''),
            'instagram_url' => Setting::getValue('instagram_url', ''),
            'youtube_url' => Setting::getValue('youtube_url', ''),
            'address_text' => Setting::getValue('address_text', ''),
            'maps_iframe' => Setting::getValue('maps_iframe', ''),
            'operational_hours' => Setting::getValue('operational_hours', ''),
            'about_us' => Setting::getValue('about_us', ''),
            'rajaongkir_api_key' => env('RAJAONGKIR_API_KEY', 'mock_key_12345'),
            'rajaongkir_origin' => env('RAJAONGKIR_ORIGIN', '152'), // Bandung
        ];

        // Fetch provinces for origin setup
        $provinces = [];
        $apiKey = env('RAJAONGKIR_API_KEY');
        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'key' => $apiKey
            ])->get('https://api.rajaongkir.com/starter/province');

            if ($response->successful()) {
                $provinces = $response->json()['rajaongkir']['results'] ?? [];
            }
        } catch (\Exception $e) {}

        if (empty($provinces)) {
            $provinces = [
                ['province_id' => 9, 'province' => 'Jawa Barat'],
                ['province_id' => 11, 'province' => 'Jawa Timur'],
            ];
        }

        return view('admin.settings.website', compact('settings', 'provinces'));
    }

    public function updateWebsite(Request $request)
    {
        $request->validate([
            'website_name' => 'required|string',
            'logo' => 'nullable|image|max:2048',
            'facebook_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'youtube_url' => 'nullable|url',
            'address_text' => 'required',
            'maps_iframe' => 'required',
            'operational_hours' => 'required',
            'about_us' => 'required',
        ]);

        Setting::setValue('website_name', $request->website_name);
        Setting::setValue('facebook_url', $request->facebook_url ?? '');
        Setting::setValue('instagram_url', $request->instagram_url ?? '');
        Setting::setValue('youtube_url', $request->youtube_url ?? '');
        Setting::setValue('address_text', $request->address_text);
        Setting::setValue('maps_iframe', $request->maps_iframe);
        Setting::setValue('operational_hours', $request->operational_hours);
        Setting::setValue('about_us', $request->about_us);

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = 'logo_' . time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $filename);
            Setting::setValue('logo_path', '/uploads/' . $filename);
        }

        return response()->json(['success' => true, 'message' => 'Settings website berhasil diperbarui.']);
    }

    public function updateRajaongkir(Request $request)
    {
        $request->validate([
            'rajaongkir_api_key' => 'required',
            'rajaongkir_origin' => 'required',
        ]);

        // Write directly to .env or settings. Here, we update setting database values
        // which our controllers will read first, falling back to .env
        Setting::setValue('rajaongkir_api_key', $request->rajaongkir_api_key);
        Setting::setValue('rajaongkir_origin', $request->rajaongkir_origin);

        return response()->json(['success' => true, 'message' => 'Konfigurasi RajaOngkir berhasil diperbarui.']);
    }
}
