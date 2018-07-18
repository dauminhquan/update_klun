<?php
/**
 * Created by PhpStorm.
 * User: daumi
 * Date: 17/07/2018
 * Time: 21:40
 */

namespace App\Http\Controllers\Enterprise;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProfileManageController extends Controller
{
    public function index(){
        return view('enterprise.profile.index');
    }
    public function edit(){
        return view('enterprise.profile.edit',['id' => Auth::user()->id]);
    }
}