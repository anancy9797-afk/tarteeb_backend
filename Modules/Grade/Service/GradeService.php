<?php

namespace Modules\Grade\Service;

use Modules\Grade\App\Models\Grade;

class GradeService
{
    function findAll($data = [])
    {
        $grades = Grade::query()
            ->when($data['name'] ?? null, function ($query) use ($data) {
                $query->where('name', 'like', '%' . $data['name'] . '%');
            })
            ->with(['gradeCategory', 'school'])
            ->available()
            ->orderByDesc('created_at');
        return getCaseCollection($grades, $data);
    }

    function findById($id)
    {
        $grade = Grade::findOrFail($id);
        return $grade;
    }

    function findBy($key, $value)
    {
        $grade = Grade::where($key, $value)->get();
        return $grade;
    }


    function create($data)
    {
        foreach ($data['grades'] as $grade) {
            Grade::create([
                'name' => $grade['name'],
                'grade_category_id' => $data['grade_category_id'],
                'school_id' => $data['school_id'],
                'is_final' => $grade['is_final'] ?? 0
            ]);
        }
    }

    function update($grade, $data)
    {
        $grade->update($data);
        return $grade;
    }

    function delete($grade)
    {
        $grade->delete();
    }

    function getGradesByGradeCategory($data, $gradeCategoryId)
    {
        $grades = Grade::available()->with('gradeCategory')->where('grade_category_id', 1);
        return getCaseCollection($grades, $data);
    }
}
