<?php 
namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function search(Request $request)
    {
       // dd($request->name);
        // Validate the input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Perform the search on the 'name' field of the Country model
        $country = Country::where('name', 'like', '%' . $validated['name'] . '%')
            ->with('periods')  // Eager load periods
            ->first();

        if (!$country) {
            return response()->json(['message' => 'Country not found'], 404);
        }

        // Return the country and its periods as a JSON response
        $request->flash();
        //$request->flash();
        return response()->json([
            'country' => $country,
            'periods' => $country->periods,
        ]);
    }

        // Function to list all country names
        public function listCountryNames()
        {
            // Retrieve all countries, only selecting the 'name' column
            $countries = Country::select('name')->orderBy('name')->get();
    
            // Return as JSON or view, depending on your use case
            return response()->json($countries);
        }
}
