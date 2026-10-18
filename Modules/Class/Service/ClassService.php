<?php

namespace Modules\Class\Service;

use Modules\Class\App\Models\Classroom;


class ClassService
{
    function findAll($data = [])
    {
        $classes = Classroom::query()
        ->when($data['grade_id'] ?? null, function ($query) use ($data) {
            return $query->where('grade_id', $data['grade_id']);
            })
            ->available()
            ->with(['school:id,name', 'grade:id,name,grade_category_id', 'grade.gradeCategory:id,name'])
            ->orderByDesc('created_at');
        return getCaseCollection($classes, $data);
    }

    function findById($id)
    {
        return Classroom::findOrFail($id);
    }

    function findBy($key, $value)
    {
        return Classroom::where($key, $value)->get();
    }

    function create($data)
    {
        $class = Classroom::create($data);
        return $class;
    }

    function update($class, $data)
    {
        $class->update($data);
        return $class;
    }

    function delete($class)
    {
        $class->delete();
    }

    function activate($id)
    {
        $class = $this->findById($id);
        $class->is_active = !$class->is_active;
        $class->save();
    }
}
