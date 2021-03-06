<?php
/**
 * Created by PhpStorm.
 * User: quand
 * Date: 7/16/2018
 * Time: 1:37 PM
 */

namespace App\Services\Api\Productions\Admin;

use App\Jobs\SendNotify;
use App\Models\Notification;
use App\Notifications\NotifyEvent;
use App\Services\Api\Interfaces\ManageInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class NotificationService extends BaseService implements ManageInterface
{
    public function __construct()
    {
        $this->model = new Notification();
    }

    public function getAll($inputs)
    {
        if(isset($inputs['size']))
        {
            if($inputs['size'] == -1)
            {
                $notifies = Notification::paginate(100000);
            }
            else{
                $notifies = Notification::paginate($inputs['size']);
            }
            foreach ($notifies as $notify)
            {
                $notify->admin = $notify->admin;
            }
            return $notifies;
        }
        $notifies = Notification::paginate(500);
        foreach ($notifies as $notify)
        {
            $notify->admin = $notify->admin;
        }
        return $notifies;

    }

    public function getOne($id)
    {
        $notify = Notification::findOrFail($id);
        $notify->admin = $notify->admin;
        return $notify;
    }

    public function getProfile($option)
    {
        return Notification::select($option);
    }

    public function save($inputs){

        try{
            $inputs['admin_id'] = Auth::user()->admin->id;
            $notification = Notification::create($inputs);
            return $notification;
        }catch (\Exception $exception)
        {
            return ['err' => $exception->getMessage()];
        }
    }

    public function update($inputs,$id)
    {
            try{
                $columns = Schema::getColumnListing((new Notification())->getTable());
                $notification = Notification::findOrFail($id);
                foreach ($columns as $column)
                {
                    if(array_key_exists($column,$inputs))
                    {
                        $notification->$column = $inputs[$column];
                    }

                }
                $notification->update();
                return $notification;
            }catch (\Exception $exception)
            {
                return ['err' => $exception->getMessage()];
            }
    }

    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);

        $notification->delete();
        if(Storage::exists($notification->attachment))
        {
            Storage::delete($notification->attachment);
        }
        return $notification;
    }

    public function delete($array)
    {
        $success = $array;
        foreach ($array as $key => $item)
        {
            $notification = Notification::find($item);
            if($notification)
            {
                if(Storage::exists($notification->attachment))
                {
                    Storage::delete($notification->attachment);
                }
                $notification->delete();
                unset($success[$key]);
            }
        }
        return $array;
    }

    public function csvStore($path){
        $list_err = [];

        $data = Excel::load($path,function($reader){})->get()->toArray();
        if(count($data) > 0)
        {
            foreach ($data as $item)
            {

                try{
                    Notification::create($item);
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
            'message' => 'Thêm danh sách thông báo thành công',
            'error' => $list_err
        ];
    }
}