<?php
/**
 * Created by PhpStorm.
 * User: quand
 * Date: 7/16/2018
 * Time: 1:37 PM
 */

namespace App\Services\Api\Productions\Admin;


use App\Models\Student;
use App\Services\Api\Interfaces\ManageInterface;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class StudentService extends BaseService implements ManageInterface
{
    public function __construct()
    {
        $this->model = new Student();
    }

    public function getAll($inputs)
    {
        if(isset($inputs['size']))
        {
            if($inputs['size'] == -1)
            {
                $students = Student::paginate(100000);
            }
            else{
                $students = Student::paginate($inputs['size']);
            }
        }
        else {
            $students = Student::paginate(500);
        }
        foreach ($students as $student)
        {
            $student->branch = $student->branch;
            $student->department = $student->branch == null? null : $student->branch->department;
            $student->course = $student->course;
        }
        return $students;
    }

    public function getOne($id)
    {
        $student = Student::findOrFail($id);
        $student->branch = $student->branch;
        $student->province = $student->province;
        $student->rating = $student->rating;
        $student->course = $student->course;
        $department = $student->branch != null ?  $student->branch->department: null;
        return response()->json([
            'student' => $student,
            'department' => $department
        ]);
    }

    public function getProfile($option)
    {
        return Student::select($option);
    }

    public function save($inputs){
        try{
            $student = Student::create($inputs);
            return $student;
        }catch (\Exception $exception)
        {
            return ['err' => $exception->getMessage()];
        }
    }

    public function update($inputs,$id)
    {

            try{
                $columns = Schema::getColumnListing((new Student())->getTable());
                $student = Student::findOrFail($id);
                foreach ($columns as $column)
                {
                    if(isset($inputs[$column]))
                    {
                        $student->$column = $inputs[$column];
                    }
                }
                $student->update();
                return $student;
            }catch (\Exception $exception)
            {
                return ['err' => $exception->getMessage()];
            }
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        if(Storage::exists($student->avatar) && $student->avatar != env('AVATAR_DEFAULT'))
        {
            Storage::delete($student->avatar);
        }
        $student->works()->delete();
        $student->user()->delete();
        $student->delete();
        return $student;
    }

    public function delete($array)
    {
        $success = $array;
        foreach ($array as $key => $item)
        {
            $student = Student::find($item);
            if($student)
            {
                if(Storage::exists($student->avatar) && $student->avatar != env('AVATAR_DEFAULT'))
                {
                    Storage::delete($student->avatar);
                }
                $student->works()->delete();
                $student->user()->delete();
                $student->delete();
                unset($success[$item]);
            }
        }
        return $success;
    }

    public function csvStore($path){
        $list_err = [];
        $size_success = 0;
        $data = Excel::load($path,function($reader){})->get()->toArray();
        if(count($data) > 0)
        {
            foreach ($data as $item)
            {

                try{
                    Student::create($item);
                    $size_success++;
                }catch (\Exception $exception)
                {
                    $list_err[] = $item['code'];
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
            'message' => 'Thêm danh sách sinh viên thành công',
            'error' => $list_err,
            'lengthError' => $size_success
        ];
    }

    public function csvUpdate($path){
        $list_err = [];
        $size_success = 0;
        $data = Excel::load($path,function($reader){})->get()->toArray();
        if(count($data) > 0)
        {
            foreach ($data as $item)
            {
                try{
                    $student = Student::find($item['code']);
                    if(!$student)
                    {
                        $list_err[] = $item['code'];
                    }
                    else{
                        $student->update($item);
                        $size_success++;
                    }

                }catch (\Exception $exception)
                {
                    $list_err[] = $item['code'];
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
            'message' => 'Update sách sinh viên thành công',
            'error' => $list_err,
            'lengthSuccess' => $size_success
        ];
    }

    public function updateAvatar($id,$avatar){
        $student = Student::findOrFail($id);
        if(Storage::exists($student->avatar) && $student->avatar != env('AVATAR_DEFAULT'))
        {
            Storage::delete($student->avatar);
        }
        $url = $avatar->store('/public/avatar');
        $student->avatar = $url;
        $student->update();
        return [
            'url' => $url
        ];
    }

    public function getListEnterprise($id)
    {
        $student = Student::findOrFail($id);
        return $student->enterprises;
    }

    public function getListWork($id){
        $student = Student::findOrFail($id);
        $works = $student->works;
        foreach ($works as $work)
        {
            $work->enterprise = $work->enterprise;
            $work->rank = $work->rank;
            $work->salary = $work->salary;
        }
        return $works;
    }
    public function getUser($id)
    {
        $student = Student::findOrFail($id);

        return $student->user;
    }
}
