<?php

namespace Modules\Session\Service;

use Modules\Session\App\Models\Session;

class SessionService
{
    function findAll($data = [])
    {
        $sessions = Session::query()
        ->when($data['class_id'] ?? null , function($q) use ($data) {
            return $q->where('class_id', $data['class_id']);
        })
        ->with(['class', 'subject', 'teacher.teacher', 'school'])
        ->available()->orderByDesc('created_at');
        return getCaseCollection($sessions, $data);
    }

    function findById($id)
    {
        $session = Session::available()->findOrFail($id);
        return $session;
    }

    function findBy($key, $value)
    {
        $session = Session::available()->where($key, $value)->get();
        return $session;
    }

    function create($data)
    {
        $session = Session::create($data);
        return $session;
    }

    function update($session, $data)
    {
        $updateData = [
            'teacher_id' => $data['teacher_id'] ?? $session->teacher_id,
            'subject_id' => $data['subject_id'] ?? $session->subject_id
        ];
        $session->update($updateData);
        return $session;
    }

    function delete($session)
    {
        $session->delete();
    }
    function deleteMultiple(array $ids)
    {
        Session::whereIn('id', $ids)->delete();
    }
    
    function getSession($data){
        $session = Session::where('class_id', $data['class_id'])
            ->where('day', $data['day'])
            ->where('semester', $data['semester'])
            ->where('session_number', $data['session_number'])
            ->where('year', $data['year'])
            ->when(auth('user')->user()->hasRole('School Manager'), function($q) {
                $q->where('school_id', auth('user')->user()->school_id);
            })
            ->when(auth('user')->user()->hasRole('Super Admin'), function($q) use ($data) {
                $q->where('school_id', $data['school_id']);
            })
            ->first();
        return $session;
    }
}

