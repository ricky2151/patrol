<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Photo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class PhotoController extends Controller
{
    private $photo;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(Photo $photo)
    {
        $this->photo = $photo;
    }
    public function index()
    {
        $data = $this->photo->orderBy('id', 'desc')->get();
        
        return response()->json(['error' => false, 'data'=>$data]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $this->photo->find($id)->delete();
        

        return response()->json(['error' => false, 'message'=>'delete data success !']);
    }
}
