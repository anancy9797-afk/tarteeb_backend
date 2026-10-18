<?php

namespace Modules\Common\Service;

use Modules\Common\App\Models\History;

class HistoryService
{
    public function findAll($data = [])
    {

        $histories = History::query()
            ->when(isset($data['from']) && $data['from'] !== null && isset($data['to']) && $data['to'] !== null, function ($query) use ($data) {
                return $query->whereDate('created_at', '>=', $data['from'])->whereDate('created_at', '<=', $data['to']);
            })
            ->available()
            ->with(['student.grade.gradeCategory', 'student.grade','student', 'teacher', 'grade', 'class', 'subject', 'school', 'attendanceTakenBy'])
            ->orderByDesc('created_at');

        return getCaseCollection($histories, $data);
    }
}
