<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ad;
use Illuminate\Validation\Rule;
use Validator;
use Storage;

class AdsController extends Controller
{
    public function index()
    {
        $ads = Ad::all();
        return response()->json($ads, 200);
    }

    public function store(Request $request)
    {
        $validator = self::validation();
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }else {
            $data = $validator->valid();
            $data['image'] = self::uploadImage();
            $ad = Ad::create($data);
            return response()->json($ad, 201);
        }

    }

    public function show(Ad $ad)
    {
        return response()->json($ad, 200);
    }

    public function update(Ad $ad, Request $request)
    {
        $validator = self::validation($ad->id);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }else {
            $data = $validator->valid();
            $data['image'] = self::uploadImage($ad->image);
            $ad->update($data);
            return response()->json($ad, 200);
        }
    }

    public function destroy(Ad $ad)
    {
        $ad->delete();
        if ($ad->image) {
            Storage::disk('public')->delete("images/$ad->image");
        }
        return response()->json(null, 204);
    }

    public static function uploadImage($oldFile='')
    {
        if ($file = request('image')) {

            if ($oldFile) {
                Storage::disk('public')->delete("images/$oldFile");
            }

            $dir = "public/images";
            $path = $file->store($dir);
            return str_replace("$dir/", '', $path);
        }
    }

    public static function validation($id = 0)
    {
        $rules = [
            'title' => [Rule::requiredIf(!$id), 'string'],
            'phone' => [Rule::requiredIf(!$id), 'string', 'size:11', "unique:ads,phone,$id"],
            'info' => [Rule::requiredIf(!$id), 'string'],
            'payment' => ['nullable', 'integer'],
            'gender' => [Rule::requiredIf(!$id), Rule::in(['male', 'female', 'both'])],
            'service' => ['nullable', 'boolean'],
            'type' => [Rule::requiredIf(!$id), Rule::in(['full-time', 'part-time'])],
            'address' => [Rule::requiredIf(!$id), 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
        ];
        return Validator::make(request()->all(), $rules);
    }
}
