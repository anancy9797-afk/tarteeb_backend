<?php

namespace Modules\Grade\Service;

use Modules\Grade\App\Models\GradeCategory;

class GradeCategoryService
{
    function findAll($data = [])
    {
        $gradeCategories = GradeCategory::query()
            ->when($data['name'] ?? null, function ($query) use ($data) {
                $query->where('name', 'like', '%' . $data['name'] . '%');
            })
            ->with('school')
            ->available()
            ->orderByDesc('created_at');
        return getCaseCollection($gradeCategories, $data);
    }

    function findById($id)
    {
        $gradeCategory = GradeCategory::findOrFail($id);
        return $gradeCategory;
    }

    function findBy($key, $value)
    {

        $gradeCategory = GradeCategory::where($key, $value)->get();
        return $gradeCategory;
    }


    function create($data)
    {
        foreach ($data['names'] as $name) {
            GradeCategory::create([
                'name' => $name,
                'school_id' => $data['school_id']
            ]);
        }
    }

    function update($gradeCategory, $data)
    {
        $gradeCategory->update($data);
        return $gradeCategory;
    }

    function delete($gradeCategory)
    {
        $gradeCategory->delete();
    }
}
