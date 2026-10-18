<?php

namespace Modules\School\Service;

use Exception;
use Modules\User\App\Models\User;
use Illuminate\Support\Facades\File;
use Modules\School\App\Models\School;
use Modules\Common\Helpers\UploadHelper;

class SchoolService
{
    use UploadHelper;
    function findAll($data = [])
    {
        $schools = School::query()
            ->when($data['name'] ?? null, function ($query) use ($data) {
                $query->where('name', 'like', '%' . $data['name'] . '%');
            })
            ->when($data['email'] ?? null, function ($query) use ($data) {
                $query->where('email', 'like', '%' . $data['email'] . '%');
            })
            ->with([
                'manager',
                'grades',
                'teachers' => function ($query) {
                    $query->with('teacher');
                },
                'students',
                'classes',
                'subjects',
                'sessions'
            ])
            ->orderByDesc('created_at');
        return getCaseCollection($schools, $data);
    }

    function active(){
        return School::active()->get();
    }

    function findById($id,$relations = [])
    {
        $school = School::with($relations)->findOrFail($id);
        return $school;
    }

    function findBy($key, $value)
    {
        $school = School::where($key, $value)->get();
        return $school;
    }

    function create($data, $managerData)
    {
        if (request()->hasFile('image')) {
            $image = request()->file('image');
            $imageName = $this->upload($image, 'user');
            $managerData['image'] = $imageName;
        }
        $school = School::create($data);
        $managerData['school_id'] = $school->id;
        $managerData['role'] = 'School Manager';
        $schoolManager = User::create($managerData);
        $schoolManager->assignRole('School Manager');
        // $school->grades()->sync($schoolGradesData['grades']);
        return $school;
    }

    function update($school, $schoolData, $managerData)
    {
        if (request()->hasFile('image')) {
            File::delete(public_path('uploads/user/' . $this->getImageName('user', $school->manager->image)));
            $image = request()->file('image');
            $imageName = $this->upload($image, 'user');
            $managerData['image'] = $imageName;
        }
        if ($schoolData)
            $school->update($schoolData);
        if ($managerData) {
            $school->manager()->update($managerData);
        }
        // if ($schoolGradesData) {
        //     $school->grades()->sync($schoolGradesData['grades']);
        // }
        return $school->fresh()->load('manager');
    }

    function delete($school)
    {
        File::delete(public_path('uploads/user/' . $this->getImageName('user', $school->manager->image)));
        $school->delete();
    }

    function activate($id)
    {
        $school = $this->findById($id);
        $school->is_active = !$school->is_active;
        $school->save();
    }

    function updateSchoolSettings($data)
    {
        $school = $this->findById(auth('user')->user()->school_id);
        if (!$school) {
            throw new Exception('School not found');
        }
        $school->settings()->updateOrCreate(
            ['school_id' => $school->id],
            $data
        );
    }
}
