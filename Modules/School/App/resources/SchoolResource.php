<?php

namespace Modules\School\App\resources;

use Modules\User\App\resources\UserResource;
use Modules\Class\App\resources\ClassResource;
use Modules\Grade\App\resources\GradeResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Session\App\resources\SessionResource;
use Modules\Student\App\resources\StudentResource;
use Modules\Subject\App\resources\SubjectResource;
use Modules\Teacher\App\resources\TeacherResource;

class SchoolResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'is_active' => (bool) $this->is_active,
            'manager' => [
                'id' => $this->manager->id,
                'name' => $this->manager->name,
                'email' => $this->manager->email,
                'phone' => $this->manager->phone,
                'image' => $this->manager->image,
                'role' => $this->manager->role,
                'created_at' => $this->manager->created_at->format('Y-m-d h:i A'),
                'updated_at' => $this->manager->updated_at->format('Y-m-d h:i A'),
            ],
            'grades' => GradeResource::collection($this->grades),
            'classes' => ClassResource::collection($this->classes),
            'subjects' => SubjectResource::collection($this->subjects),
            'teachers' => TeacherResource::collection($this->teachers),
            'students' => StudentResource::collection($this->students),
            'sessions' => SessionResource::collection($this->sessions),
            'created_at' => $this->created_at->format('Y-m-d h:i A'),
            'updated_at' => $this->updated_at->format('Y-m-d h:i A'),
        ];
    }
}