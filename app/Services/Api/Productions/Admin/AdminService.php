<?php
/**
 * Created by PhpStorm.
 * User: quand
 * Date: 7/17/2018
 * Time: 10:57 AM
 */

namespace App\Services\Api\Productions\Admin;


use App\Models\Admin;
use App\Services\Api\Interfaces\ManageInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;

class AdminService extends BaseService implements ManageInterface
{
    public function __construct()
    {
        $this->model = new Admin();
    }
    public function getAll($inputs)
    {

        /*$me = Auth::user();
        $me = $me->admin;
        if(isset($inputs['size']))
        {
            if($inputs['size'] == -1)
            {
                $admins = Admin::where('id','<>',$me->id)->paginate(100000);
            }
            else{
                $admins = Admin::where('id','<>',$me->id)->paginate($inputs['size']);
            }
        }
        else{
            $admins = Admin::where('id','<>',$me->id)->paginate(500);
        }
        foreach ($admins as $index => $admin)
        {
            $admin->user = $admin->user;

        }*/
        $me = Auth::user();
        $me = $me->admin;
        if(isset($inputs['size']))
        {
            if($inputs['size'] == -1)
            {
                $admins = Admin::paginate(100000);
            }
             else{
                 $admins = Admin::paginate($inputs['size']);
             }
        }
        else{
            $admins = Admin::paginate(500);
        }
        foreach ($admins as $index => $admin)
        {
            $admin->user = $admin->user;
            if($admin->id == $me->id)
            {
                $admin->me = 1;
            }
        }
        return $admins;

    }

    public function getOne($id)
    {
        $admin = Admin::findOrFail($id);
        return $admin;
    }

    public function getProfile($option)
    {
        return Admin::select($option)->get();
    }

    public function save($inputs)
    {
        $admin = Admin::create($inputs->all());

        return $admin;
    }

    public function update($inputs, $id)
    {
        try{
            $columns = Schema::getColumnListing((new Admin())->getTable());
            $admin = Admin::findOrFail($id);
            foreach ($columns as $column)
            {
                if(array_key_exists($column,$inputs))
                {
                    $admin->$column = $inputs[$column];
                }

            }
            $admin->update();
            return $admin;
        }catch (\Exception $exception)
        {
            return ['err' => $exception->getMessage()];
        }
    }

    public function destroy($id)
    {
        $admin = Admin::findOrFail($id);
        $admin->tasks()->detach();
        $admin->delete();
        return $admin;

    }

    public function delete($array)
    {
        $success = $array;
        foreach ($array as $item)
        {
            $admin = Admin::find($item);
            if($admin)
            {
                $admin->tasks()->detach();
                $admin->delete();
                unset($success[$item]);
            }

        }
        return $success;
    }

    public function csvStore($path){
        $list_err = [];

        $data = Excel::load($path,function($reader){})->get()->toArray();
        if(count($data) > 0)
        {
            foreach ($data as $item)
            {

                try{
                    Admin::create($item);
                }catch (\Exception $exception)
                {
                    $list_err[] = [
                        'error' => $exception->getCode(),
                        'message' => $exception->getMessage()
                    ];
                }
            }
        }
        if(count($list_err) == count($data))
        {
            return response()->json([
                'message' => "File rỗng | Sai định dạng | Trùng dữ liệu toàn bộ",
                'error' => $list_err
            ],422);
        }
        return [
            'message' => 'Thành công',
            'error' => $list_err
        ];
    }
}
