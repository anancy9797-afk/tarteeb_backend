<?php

use Modules\Common\App\Models\History;
use Modules\Session\App\Models\Session;
use Modules\Student\App\Models\Student;



function saveHistory($attendance, $teacherTakenAttendance, $teacherTakenAttendanceProfile)
{
    // $session = Session::findOrFail($session_id);
    History::create([
        // 'day' => $session->day,
        // 'session_number' => $session->session_number,
        // 'semester' => $session->semester,
        // 'year' => $session->year,
        // 'teacher_id' => $session->teacher_id,
        'attendance_taken_by' => $teacherTakenAttendanceProfile->id,
        'student_id' => $attendance['student_id'],
        // 'subject_id' => $session->subject_id,
        // 'session_id' => $session->id,
        // 'class_id' => $session->class_id,
        'school_id' => $teacherTakenAttendance->school_id,
        'is_present' => $attendance['is_present'],
        // 'teacher_name' => $session->teacher->teacher->name,
        'attendance_taken_by_name' => $teacherTakenAttendance->name,
        'student_name' => Student::find($attendance['student_id'])->name,
        // 'subject_name' => $session->subject->name,
        // 'class_name' => $session->class->name,
        // 'school_name' => $session->school->name,
    ]);
}


