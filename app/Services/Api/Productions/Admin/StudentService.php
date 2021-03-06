<?php
/**
 * Created by PhpStorm.
 * User: quand
 * Date: 7/16/2018
 * Time: 1:37 PM
 */

namespace App\Services\Api\Productions\Admin;


use App\Models\Branch;
use App\Models\Course;
use App\Models\Province;
use App\Models\Rating;
use App\Models\Student;
use App\Models\User;
use App\Services\Api\Interfaces\ManageInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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
                    if(array_key_exists($column,$inputs))
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
//        dd($data);
        if(count($data) > 0)
        {
            foreach ($data as $item)
            {

                try{
//                    $keyFilter = ['province_id','rating_id','user_id','course_code','branch_code','sex'];
                    //province
                    if(array_key_exists('province_id',$item))
                    {
                        if(!is_numeric($item['province_id']))
                        {
                            $province = Province::where(DB::raw('LOWER(name)'),'like','%'.strtolower($item['province_id']).'%')->select('id')->first();
                            if($province != null) {
                                $item['province_id'] = $province->id;
                            }
                        }
                    }

                    //sex

                    if(array_key_exists('sex',$item))
                    {
                        if(strtolower($item['sex']) == 'nam')
                        {
                            $item['sex'] = 1;
                        }
                        if(strtolower($item['sex']) == 'nữ')
                        {
                            $item['sex'] = 0;
                        }
                    }


                    //rating

                    if(array_key_exists('rating_id',$item))
                    {
                        if($item['rating_id'] !== null && $item['rating_id'] !== '')
                        {
                            $rating = Rating::where(DB::raw('LOWER(name)'),'like','%'.strtolower($item['rating_id']).'%')->select('id')->first();
                            if($rating != null)
                            {
                                $item['rating_id'] = $rating->id;
                                $item['graduated']= 1;
                            }
                        }
                    }
                    //user

                    if(array_key_exists('user_id',$item))
                    {
                        if(!is_numeric($item['user_id']))
                        {
                            $user = User::where(DB::raw('LOWER(email)'),$item['user_id'])->select('id')->first();
                            if($user != null)
                            {
                                $item['user_id'] = $user->id;
                            }
                        }
                    }

                    //course
                    if(array_key_exists('course_code',$item))
                    {
                        $course = Course::where('code',$item['course_code'])->select('code')->first();
                        if($course != null)
                        {
                            $course = Course::where(DB::raw('LOWER(name)'),'like','%'.strtolower($item['course_code']).'%')->select('code')->first();
                            if($course != null)
                            {
                                $item['course_code'] = $course->code;
                            }
                        }
                    }

                    //branch
                    if(array_key_exists('branch_code',$item))
                    {
                        $branch = Branch::where('code',$item['branch_code'])->select('code')->first();
                        if($branch == null)
                        {
                            $branch = Branch::where(DB::raw('LOWER(name)'),'like','%'.strtolower($item['branch_code']).'%')->select('code')->first();
                            if($branch != null)
                            {
                                $item['branch_code'] = $branch->code;
                            }
                        }
                    }

                    if(array_key_exists('birth_day',$item))
                    {
                        if($item['birth_day'] != null)
                        {
                            $item['birth_day'] = str_replace('/','-',$item['birth_day']);
                            $item['birth_day'] = Carbon::parse($item['birth_day']);
                        }

                    }
                    if(array_key_exists('date_graduated',$item))
                    {
                        if($item['date_graduated'] != null)
                        {
                            $item['date_graduated'] = str_replace('/','-',$item['date_graduated']);
                            $item['date_graduated'] = Carbon::parse($item['date_graduated']);
                        }

                    }

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
            'url' => Storage::url($url)
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
