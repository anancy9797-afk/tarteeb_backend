<?php

namespace Modules\Teacher\Service;

use Modules\User\App\Models\User;
use Illuminate\Support\Facades\File;
use Modules\Common\Helpers\UploadHelper;
use Modules\Teacher\App\Models\TeacherProfile;

class TeacherService
{
    use UploadHelper;
    function findAll($data = [])
    {
        $teachers = TeacherProfile::query()
            ->with(['teacher', 'subjects'])
            ->when($data['name'] ?? null, function ($query) use ($data) {
                $query->whereHas('teacher', function ($query) use ($data) {
                    $query->where('name', 'like', '%' . $data['name'] . '%');
                });
            })
            ->when($data['email'] ?? null, function ($query) use ($data) {
                $query->whereHas('teacher', function ($query) use ($data) {
                    $query->where('email', 'like', '%' . $data['email'] . '%');
                });
            })
            ->available()->orderByDesc('created_at');
        return getCaseCollection($teachers, $data);
    }

    function findById($id)
    {
        $teacher = TeacherProfile::available()->findOrFail($id);
        return $teacher;
    }

    function findBy($key, $value)
    {
        $teacher = TeacherProfile::available()->where($key, $value)->get();
        return $teacher;
    }

    function create($teacherData, $teacherProfileData)
    {
        if (request()->hasFile('image')) {
            $image = request()->file('image');
            $imageName = $this->upload($image, 'user');
            $teacherData['image'] = $imageName;
        }
        $teacherData['role'] = 'Teacher';
        $teacher = User::create($teacherData);
        $teacher->assignRole('Teacher');
        $teacher->teacherProfile()->create($teacherProfileData);
        $teacher->teacherProfile->subjects()->attach($teacherProfileData['subjects']);
        return $teacher;
    }

    function update($teacherProfile, $teacherData, $teacherProfileData)
    {
        if (request()->hasFile('image')) {
            File::delete(public_path('uploads/user/' . $this->getImageName('user', $teacherProfile->user->image)));
            $image = request()->file('image');
            $imageName = $this->upload($image, 'user');
            $teacherData['image'] = $imageName;
        }
        if ($teacherData)
            $teacherProfile->teacher()->update($teacherData);
        if ($teacherProfileData)
            $teacherProfile->update($teacherProfileData);
        if (isset($teacherProfileData['subjects']) && count($teacherProfileData['subjects']) > 0)
            $teacherProfile->subjects()->sync($teacherProfileData['subjects']);
        return $teacherProfile;
    }

    function delete($teacher)
    {
        File::delete(public_path('uploads/user/' . $this->getImageName('user', $teacher->image)));
        $teacher->teacher()->delete();
        $teacher->delete();
    }

    function activate($id)
    {
        $teacher = $this->findById($id);
        $teacher->is_active = !$teacher->is_active;
        $teacher->save();
    }

    function getTeachersBySubjectId($subjectId)
    {
        return TeacherProfile::with('teacher')
            ->whereHas('subjects', function ($query) use ($subjectId) {
                $query->where('subject_id', $subjectId);
            })
            ->available()
            ->orderByDesc('created_at')
            ->get();
    }
}
