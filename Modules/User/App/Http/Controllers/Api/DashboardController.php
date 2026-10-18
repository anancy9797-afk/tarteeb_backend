<?php

namespace Modules\User\App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\School\App\Models\School;
use Modules\Class\App\Models\Classroom;
use Modules\Student\App\Models\Student;
use Modules\Session\App\Models\Attendance;
use Modules\Teacher\App\Models\TeacherProfile;
use Modules\User\App\Http\Requests\AttendanceStatistics;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:user');
        $this->middleware('role:Super Admin')->only(['getAttendanceStatisticsComparison']);
        $this->middleware('role:Super Admin|School Manager')->only(['index']);
        $this->middleware('role:Super Admin|School Manager|Teacher')->only(['getAttendanceStatistics', 'getTodaysAttendanceStatistics', 'getWeeklyAttendanceReport']);
        $this->middleware('role:Teacher')->only(['getGenderStatistics']);
    }
    public function index()
    {
        $data = [];
        if (auth('user')->check()) {
            if (auth('user')->user()->hasRole('Super Admin')) {
                $data['schools'] = School::count();
            }
            $data['teachers'] = TeacherProfile::available()->count();
            $data['students'] = Student::available()->count();
            $data['classes'] = Classroom::available()->count();
        }
        return returnMessage(true, 'Dashboard Data Fetched Successfully', $data);
    }


    public function getAttendanceStatistics(AttendanceStatistics $request)
    {
        $startDate = \Carbon\Carbon::createFromDate($request->validated()['year'], $request->validated()['month'], 1);
        $endDate = $startDate->copy()->endOfMonth();

        $totalAttendance = Attendance::available()->whereBetween('created_at', [$startDate, $endDate])->count();

        $totalAbsences = Attendance::available()->where('is_present', false)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $absenceRate = $totalAttendance > 0 ? number_format(($totalAbsences / $totalAttendance) * 100, 2) : 0;

        $mostAbsentClass = Attendance::available()->with('session')
            ->selectRaw('session_id, COUNT(*) as absence_count')
            ->where('is_present', 0)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('session_id')
            ->orderByDesc('absence_count')
            ->first();

        $className = null;
        $classAbsenceRate = 0;

        if ($mostAbsentClass) {
            $className = Classroom::find($mostAbsentClass->session->class_id)->name;
            $classTotalAttendance = Attendance::where('session_id', $mostAbsentClass->session_id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            $classAbsenceRate = $classTotalAttendance > 0 ? number_format(($mostAbsentClass->absence_count / $classTotalAttendance) * 100, 2) : 0;
        }

        $attendanceRatePerWeekday = Attendance::available()->join('sessions', 'attendances.session_id', '=', 'sessions.id')
            ->selectRaw('sessions.day as session_day, COUNT(attendances.id) as attendance_count')
            ->where('attendances.is_present', true)
            ->whereBetween('attendances.created_at', [$startDate, $endDate])
            ->groupBy('sessions.day')
            ->get()
            ->mapWithKeys(function ($item) use ($startDate, $endDate) {
                $totalForDay = Attendance::join('sessions', 'attendances.session_id', '=', 'sessions.id')
                    ->whereBetween('attendances.created_at', [$startDate, $endDate])
                    ->where('sessions.day', $item->session_day)
                    ->count();
                $rate = $totalForDay > 0 ? number_format(($item->attendance_count / $totalForDay) * 100, 2) : 0;

                return [$item->session_day => $rate];
            });

        return returnMessage(true, 'Attendance Statistics Fetched Successfully', [
            'absence_rate' => $absenceRate,
            'most_absent_class' => $className,
            'class_absence_rate' => $classAbsenceRate,
            'attendance_rate_per_weekday' => $attendanceRatePerWeekday,
        ]);
    }


    public function getTodaysAttendanceStatistics()
    {
        $today = \Carbon\Carbon::today();

        $totalStudents = Student::available()->count();

        $attendedStudentIds = Attendance::available()
            ->where('is_present', 1)
            ->whereDate('created_at', $today)
            ->distinct('student_id')
            ->pluck('student_id');

        $attendedToday = $attendedStudentIds->count();

        $totalAbsencesToday = $totalStudents - $attendedToday;

        return returnMessage(true, 'Today\'s Attendance Statistics Fetched Successfully', [
            'absent' => $totalAbsencesToday,
            'attended' => $attendedToday,
            'total_students' => $totalStudents,
        ]);
    }


    public function getAttendanceStatisticsComparison()
    {
        $currentMonthStart = \Carbon\Carbon::now()->startOfMonth();
        $currentMonthEnd = \Carbon\Carbon::now()->endOfMonth();
        $previousMonthStart = \Carbon\Carbon::now()->subMonth()->startOfMonth();
        $previousMonthEnd = \Carbon\Carbon::now()->subMonth()->endOfMonth();

        // Calculate attendance for the current month
        $currentMonthAttendance = Attendance::available()
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->count();

        $currentMonthAbsences = Attendance::available()
            ->where('is_present', false)
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->count();

        $currentMonthAttendanceRate = $currentMonthAttendance > 0 ? number_format((($currentMonthAttendance - $currentMonthAbsences) / $currentMonthAttendance) * 100, 2) : 0;

        // Calculate attendance for the previous month
        $previousMonthAttendance = Attendance::available()
            ->whereBetween('created_at', [$previousMonthStart, $previousMonthEnd])
            ->count();

        $previousMonthAbsences = Attendance::available()
            ->where('is_present', false)
            ->whereBetween('created_at', [$previousMonthStart, $previousMonthEnd])
            ->count();

        $previousMonthAttendanceRate = $previousMonthAttendance > 0 ? number_format((($previousMonthAttendance - $previousMonthAbsences) / $previousMonthAttendance) * 100, 2) : 0;

        return returnMessage(true, 'Attendance Statistics Fetched Successfully', [
            'current_month_attendance_rate' => $currentMonthAttendanceRate,
            'previous_month_attendance_rate' => $previousMonthAttendanceRate,
        ]);
    }


    public function getWeeklyAttendanceReport()
    {
        $startDate = \Carbon\Carbon::now()->startOfWeek();
        $endDate = \Carbon\Carbon::now()->endOfWeek();

        $weeklyReport = [];

        foreach (range(0, 6) as $dayOffset) {
            $date = $startDate->copy()->addDays($dayOffset);
            $dayName = $date->format('l');

            $presentStudentIds = Attendance::available()
                ->where('is_present', 1)
                ->whereDate('created_at', $date)
                ->distinct('student_id')
                ->pluck('student_id');

            $attendedCount = $presentStudentIds->count();

            $absentStudentIds = Attendance::available()
                ->where('is_present', 0)
                ->whereDate('created_at', $date)
                ->distinct('student_id')
                ->pluck('student_id');

            $absentCount = $absentStudentIds->diff($presentStudentIds)->count();

            $weeklyReport[$dayName] = [
                'attended' => $attendedCount,
                'absent' => $absentCount,
            ];
        }

        return returnMessage(true, 'Weekly Attendance Report Fetched Successfully', $weeklyReport);
    }

    public function getGenderStatistics()
    {
        $teacherId = auth('user')->user()->teacherProfile->id;

        $totalStudents = Student::whereHas('class.sessions', function ($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->count();

        $maleCount = Student::whereHas('class.sessions', function ($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->where('gender', 'M')->count();

        $femaleCount = Student::whereHas('class.sessions', function ($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->where('gender', 'F')->count();

        $malePercentage = $totalStudents > 0 ? number_format(($maleCount / $totalStudents) * 100, 2) : 0;
        $femalePercentage = $totalStudents > 0 ? number_format(($femaleCount / $totalStudents) * 100, 2) : 0;

        return returnMessage(true, 'Gender Statistics Fetched Successfully', [
            'male_percentage' => $malePercentage,
            'female_percentage' => $femalePercentage,
        ]);
    }
}
