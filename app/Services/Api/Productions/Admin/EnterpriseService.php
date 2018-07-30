<?php
/**
 * Created by PhpStorm.
 * User: quand
 * Date: 7/16/2018
 * Time: 1:37 PM
 */

namespace App\Services\Api\Productions\Admin;
define('AVATAR_DEFAULT', 'public/storage/avatar/avatar-default.png');

use App\Http\Requests\GetDataRequest;
use App\Models\Enterprise;
use App\Services\Api\Interfaces\ManageInterface;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class EnterpriseService extends BaseService implements ManageInterface
{

    public function __construct()
    {
        $this->model = new Enterprise();
    }

    public function getAll($inputs)
    {
        if(isset($inputs['size']))
        {
            if($inputs['size'] == -1)
            {

                return Enterprise::paginate(10000);
            }
            return Enterprise::paginate($inputs['size']);
        }
        return Enterprise::paginate(500);
    }

    public function getOne($id)
    {
        return Enterprise::findOrFail($id);
    }

    public function getProfile($option)
    {
        return Enterprise::select($option);
    }

    public function save($inputs){
        try{
            $enterprise = Enterprise::create($inputs);
            return $enterprise;
        }catch (\Exception $exception)
        {
            return ['err' => $exception->getMessage()];
        }
    }

    public function update($inputs,$id)
    {

            try{
                $columns = Schema::getColumnListing((new Enterprise())->getTableName());
                $enterprise = Enterprise::findOrFail($id);
                foreach ($columns as $column)
                {
                    if(isset($inputs[$column]))
                    {
                        $enterprise->$column = $inputs[$column];
                    }
                }
                $enterprise->update();
                return $enterprise;
            }catch (\Exception $exception)
            {
                return ['err' => $exception->getMessage()];
            }
    }
    public function destroy($id)
    {
        $enterprise = Enterprise::findOrFail($id);
        if(Storage::exists($enterprise->avatar))
        {
            Storage::delete($enterprise->avatar);
        }

        $enterprise->user()->delete();
        $enterprise->jobs()->delete();
        $enterprise->delete();
        return $enterprise;
    }

    public function delete($array)
    {
        $success = $array;
        foreach ($array as $key => $item)
        {
            $enterprise = Enterprise::find($item);
            if($enterprise)
            {
                if(Storage::exists($enterprise->avatar)) {
                    Storage::delete($enterprise->avatar);
                }

                $enterprise->user()->delete();
                $enterprise->jobs()->delete();
                $enterprise->delete();
                unset($success[$key]);
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
                   Enterprise::create($item);
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
            ],406);
        }
        return [
            'message' => 'Thêm danh sách doanh nghiệp thành công',
            'error' => $list_err
        ];
    }

    public function updateAvatar($id,$avatar){
        $enterprise = Enterprise::findOrFail($id);
        if(Storage::exists($enterprise->avatar) && $enterprise->avatar != AVATAR_DEFAULT)
        {
            Storage::delete($enterprise->avatar);

        }
        $url = Storage::url($avatar->store('/public/avatar'));
        $enterprise->avatar = $url;
        $enterprise->update();
        return [
            'url' => $url
        ];
    }
    public function getListWork($id)
    {
        $enterprise = Enterprise::findOrFail($id);

        $works = $enterprise->works;

        foreach ($works as $work)
        {
            $work->student = $work->student()->select('code','full_name','avatar')->first();
            $work->salary = $work->salary()->first();
            $work->rank = $work->rank()->first();
        }
        return $works;
    }
    public function getListJob($inputs,$id){
        $enterprise = Enterprise::findOrFail($id);
        if(isset($inputs['size']))
        {
            if('size' == -1)
            {
                $jobs = $enterprise->jobs;
            }
            else{
                $jobs = $enterprise->jobs()->paginate($inputs['size']);
            }
        }
        foreach ($jobs as $job)
        {

            $job->salary = $job->salary;
            $job->rank = $job->rank;
        }
        return $jobs;
    }
    public function getUser($id)
    {
        $enterprise = Enterprise::findOrFail($id);
        return $enterprise->user;
    }
}