<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\CommunityGroup;
use App\Community;
use Auth;

class GroupController extends Controller
{
    public function __construct()
    {
        // only guests are allowed to view this
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('communities.groups.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate(request(), [
            'name' => 'required|string|max:255',
            'district' => 'required'
        ]);

        Auth::user()->addCommunityGroupRequest($request);

        session()->flash('message', 'Solicitud realizada');

        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $groups = Community::find($request -> input('id'))->communityGroup()
                            -> leftJoin("users_by_community_groups", "users_by_community_groups.community_group_id", "=", "community_groups.id")
                            -> select("users_by_community_groups.id as userGroupID", "community_groups.*")
                            ->orderBy('userGroupID', 'desc')
                            -> get();

                                
        return \Response::json($groups ->toJson()); 
    }

    /**
     * Display the specified resource.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function communities(Request $request)
    {

        $exists = DB::table('community_groups')->where('id', $request -> input('id'))->exists();


        
        if($exists > 0)
        {
            $communities = CommunityGroup::find($request -> input('id')) -> community;
            $communities = $communities -> toJson();
        }


        else
        {
            $communities = array(
                "status" => "Empty"
            );
        }

        return \Response::json($communities);
    }


    /**
     * Display the specified resource.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function follow(Request $request)
    {

        $communityGroup = CommunityGroup::find($request -> input('id'));
        $exists = $communityGroup -> user() -> exists();

        if (!$exists)
        {
            $communityGroup -> user() -> attach($request -> input('id'), ['user_id' => Auth::id()]);
    
        }

        $response = array(
            "status" => "done"
        );

        return \Response::json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function unfollow(Request $request)
    {

        $communityGroup = CommunityGroup::find($request -> input('id'));
        $exists = $communityGroup -> user() -> exists();

        if ($exists)
        {
            $communityGroup -> user() -> detach([
                'user_id' => Auth::id(),
                'community_id' => $request -> input('id')     
            ]);
    
        }

        $response = array(
            "status" => "done"
        );

        return \Response::json($response);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
