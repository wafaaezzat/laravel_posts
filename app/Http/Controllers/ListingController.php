<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ListingController extends Controller
{
    public function index (){
        return  response()->json([
            'heading'=>'latest listing',
            'listings'=>Listing::latest()->filter(request(['tag','search']))->paginate(6)
        ]);
    }
    
    public function show (Listing $listing ){
        response()->json([
            'listing'=>$listing
        ]);
    }

    // public function create (){
    //     return view('listings.create');
    // }


    public function store (Request $request ){
$formFields=$request->validate([
    'title'=>'required',
    'company'=>['required',Rule::unique('listings','company')],
    'location'=>'required',
    'website'=>'required',
    'email'=>['required','email'],
    'tags'=>'required',
    'description'=>'required'
]);


if ($request->hasFile('logo')) {
    $formFields['logo']=$request->file('logo')->store('logos','public');
}

$formFields['user_id']=auth()->id();

    Listing::create($formFields);
        return  response('Listing Created Sucessfully!');
    }


    // public function edit(listing $listing){
    //     return view('listings.edit',['listing'=>$listing]);
    // }

    public function update(Request $request,Listing $listing ){
        if($listing->user_id!=auth()->id()){
            abort(403,'Unauthorized Action');
        }

        $formFields=$request->validate([
            'title'=>'required',
            'company'=>['required'],
            'location'=>'required',
            'website'=>'required',
            'email'=>['required','email'],
            'tags'=>'required',
            'description'=>'required'
        ]);
        
        if ($request->hasFile('logo')) {
            $formFields['logo']=$request->file('logo')->store('logos','public');
        }
        
        
           $listing->update($formFields);
                return back()->with('message','Listing updated Sucessfully!');
            }

    public function destroy(listing $listing){


        if($listing->user_id!=auth()->id()){
            abort(403,'Unauthorized Action');
        }


        $listing->delete();
        return  response('Listing deleted Sucessfully!');

    }       
    
    
    public function manage(listing $listing){
        return  response()->json(['listings'=>auth()->user()->listings()->get()]);
    }

}