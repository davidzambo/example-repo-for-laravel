<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Carousel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\User;
use App\Carrier;
use App\Country;
use App\Sport;
use App\Language;
use App\Profile;
use App\Position;
use App\Picture;
use App\ExpertType;
use App\ClubExperts;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
      public function index()
    {
      $user = Auth::user();
      $carousels = Carousel::get(5);

      return view('users.index', compact(['user', 'carousels']));
 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
      if (Auth::check()){
        $user = Auth::user();
        $countries = Country::all();
        $sports = Sport::all();
        $languages = Language::all();
        $carriers = Carrier::where('user_id', $user->id)->orderBy('season', 'DESC')->get();
        $carousels = Carousel::get(5);
        switch ($user->user_type_id){
          case 1:
            return view('users.update.player', compact(['user', 'countries', 'sports', 'languages', 'carriers', 'positions', 'carousels']));
          case 2:
            $experttypes = ExpertType::all();
            return view('users.update.expert', compact(['user', 'countries', 'sports', 'languages', 'carriers', 'experttypes', 'carousels']));
          case 3:
            $experttypes = ExpertType::all();
            return view('users.update.club', compact(['user', 'countries', 'sports', 'languages', 'carriers', 'positions', 'experttypes', 'carousels']));
          case 4:
            return view('admin.update', compact(['user', 'carousels']));
        }        
      } else {
        return redirect('/');
      }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
       if (Auth::check()){

        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'last_name' => 'required|string|min:4|max:255',
            'middle_name' => 'nullable|string|min:3|max:255',
            'first_name' => 'required|string|min:4|max:255',
            'club_name' => 'string|min:4|max:255|required_if:user_type_id,==,3',
            'place_of_birth' => 'string|min:2|max:255|required_unless:user_type_id,==,3',
            'country_of_birth' => 'required|numeric|min:0|max:255',
            'height' => 'numeric|required_if:user_type_id,==,1',
            'weight' => 'numeric|required_if:user_type_id,==,1',
            'right_handed' => 'required_if:user_type_id,==,1',
            'sex' => 'numeric|required_unless:user_type_id,==,3',
            'transfer_status' => 'numeric|required_unless:user_type_id,==,3',
            'expert_type' => 'numeric|required_if:user_type_id,==,2',
            'language' => 'min:1|required_unless:user_type_id,==,3',
            'target_country' => 'min:1|required_unless:user_type_id,==,3',
            'date_of_birth' => 'required|date',
            'postal_code' => 'numeric|min:0|max:999999|required_if:user_type_id,==,3',
            'street' => 'string|min:3|max:64|required_if:user_type_id,==,3',
            'house_number' => 'string|min:1|max:8|required_if:user_type_id,==,3',
            'image' => 'nullable|image|min:30|max:10000',
            'experts' => 'array|required_if:user_type_id,==,3',
            'experts.*' => 'nullable|string|min:3|max:64'
        ]);

        if ($validator->passes()){

          // storing the user data's
          $user->last_name = $request->last_name;
          $user->first_name = $request->first_name;
          $user->middle_name = $request->middle_name;
          $user->club_name = $request->club_name;
          $user->save();

          // updating the user details
          $details = Profile::updateOrCreate(
            ['user_id' => $request->user_id],
            [
              'country_of_birth' => $request->country_of_birth,
              'place_of_birth' => $request->place_of_birth,
              'date_of_birth' => $request->date_of_birth,
              'height' => $request->height,
              'weight' => $request->weight,
              'right_handed' => $request->right_handed,
              'expert_type' => $request->expert_type,
              'sex' => $request->sex,
              'transfer_status' => $request->transfer_status,
              'postal_code' => $request->postal_code,
              'street' => $request->street,
              'house_number' => $request->house_number
            ]
          );

          // check if a new picture will be uploaded
          if (!empty($request->file('image'))) {
            // store the original Picture
            $file = $request->file('image');
            $fileName = time().hash("sha256", $file->getClientOriginalName());
            $fileExt = $file->getClientOriginalExtension();
            $filePath = "images/" . date('Y') . "/" . date('m') . "/";

            $original = $filePath . $fileName . '.' . $fileExt;
            $file->storeAs($filePath, $fileName.'.'.$fileExt);

            $origin = Image::make($original)->orientate();
            $origin->save($original);

            // create thumbnail from the original
            $thumb = Image::make($original)->resize(200, null, function($constraint){
              $constraint->aspectRatio();
            })->orientate();

            // store thumbnail
            $thumbnail = $filePath . $fileName . '-thumb.' . $fileExt;
            $thumb->save($thumbnail);

            // create display from the original
            $disp = Image::make($original)->resize(800, null, function($constraint){
              $constraint->aspectRatio();
            })->orientate();

            // store display
            $display = $filePath . $fileName . '-disp.' . $fileExt;
            $disp->save($display);


            // Get the previous profile picture
            $oldPic = Picture::where(['user_id' => $user->id, 'is_profile' => true])->first();
            
            // if that was a stock photo, delete just the entry
            if ($oldPic->thumbnail == 'images/stock-profile.png'){
              $oldPic->delete();
            } else {
              //if that was a real photo, just set the is_profile flag to false
              $oldPic->is_profile = false;
              $oldPic->save();
            }


            // create new entry @ Pictures
            $picture = Picture::create(
              [
                'user_id' => $user->id,
                'is_profile' => true,
                'original' => $original,
                'thumbnail' => $thumbnail,
                'display' => $display
              ]);
          }

          // storing the spoken languages
          $user->language()->sync($request->language);

          // storing the target countries
          $user->country()->sync($request->target_country);

          // storing the positions
          $user->position()->sync($request->position);

          if ($user->user_type_id == 3){
            foreach ($request->experts as $expert_type_id => $name){
              ClubExperts::updateOrCreate([
                'user_id' => $user->id,
                'expert_type_id' => $expert_type_id,                
              ],[
                'user_id' => $user->id,
                'expert_type_id' => $expert_type_id,
                'name' => $name
              ]);
            }
          }

          if (!$user->is_carousel)
            Carousel::setShowable($user);

          return redirect('/');

        } else { // validator not passes
          
          return redirect('/update')->withInput()->withErrors($validator->errors());

        }

      } else {

        return redirect('/');

      }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
