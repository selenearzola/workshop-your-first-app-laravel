<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Images;
use App\Models\User;

use Illuminate\Http\Request;

class ImagesController extends Controller
{

	public function show()
	{

		$User = auth()->user() ?? null;

		return Images::latest()->when($User, function ($query) use ($User) {

			return $query->where('user_id', $User->id);
		})->get()->whenNotEmpty(function ($collection) use ($User) {

			return collect([
				'is_logged' => $User ? true : false,
				'items' => $collection
			]);


			$collection->map(function ($Image)  use ($User) {

				$Image->is_logged = $User ?: false;
				return $Image;
			});
		})->whenEmpty(function ($collection) use ($User) {

			return collect([
				'is_logged' => $User ? true : false,
				'items' => [],
			]);
		});
	}

	public function store(Request $request)
	{

		if (!$request->hasFile('image')) {

			return response()->json(['The is not image present'], 400);
		}

		$request->validate([
			'image' => 'required|file|image|mimes:jpg,jpeg,png'
		]);

		if (!($path = $request->file('image')->store('public/images'))) {

			return response()->json(['The file could not be saved'], 500);
		}

		$uploadedFile = $request->file('image');

		return Images::create([
			'original_name' => pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME),
			'name' => $uploadedFile->hashName(),
			'size' => $uploadedFile->getSize(),
			'user_id' => auth()->user()->id,
		]);
	}
}
