<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\User\App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
class UserDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $admin = $this->adminCreation();
        $this->permissionCreation();
        $role = $this->roleCreation();
        $role2 = $this->role2Creation();
        $admin->assignRole($role);
        $this->role3Creation();
    }

    function adminCreation()
    {
        return $admin = User::create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'phone' => '0123456789',
            'role' => 'Super Admin',
            'password' => Hash::make('123456'),
            'is_active' => 1,
        ]);
    }

    function permissionCreation()
    {
        $permissions = [
            ['Index-user', 'User', 'Index'],
            ['Create-user', 'User', 'Create'],
            ['Edit-user', 'User', 'Edit'],
            ['Delete-user', 'User', 'Delete'],

            ['Index-role', 'Roles', 'Index'],
            ['Create-role', 'Roles', 'Create'],
            ['Edit-role', 'Roles', 'Edit'],
            ['Delete-role', 'Roles', 'Delete'],

            ['Index-school', 'School', 'Index'],
            ['Create-school', 'School', 'Create'],
            ['Edit-school', 'School', 'Edit'],
            ['Delete-school', 'School', 'Delete'],

            ['Index-teacher', 'Teacher', 'Index'],
            ['Create-teacher', 'Teacher', 'Create'],
            ['Edit-teacher', 'Teacher', 'Edit'],
            ['Delete-teacher', 'Teacher', 'Delete'],

            ['Index-student', 'Student', 'Index'],
            ['Create-student', 'Student', 'Create'],
            ['Edit-student', 'Student', 'Edit'],
            ['Delete-student', 'Student', 'Delete'],

            ['Index-student-graduation', 'Student Graduation', 'Index'],
            ['Create-student-graduation', 'Student Graduation', 'Create'],
            ['Edit-student-graduation', 'Student Graduation', 'Edit'],
            ['Delete-student-graduation', 'Student Graduation', 'Delete'],

            ['Index-student-upgrade', 'Student Upgrade', 'Index'],
            ['Create-student-upgrade', 'Student Upgrade', 'Create'],
            ['Edit-student-upgrade', 'Student Upgrade', 'Edit'],
            ['Delete-student-upgrade', 'Student Upgrade', 'Delete'],

            ['Index-class', 'Class', 'Index'],
            ['Create-class', 'Class', 'Create'],
            ['Edit-class', 'Class', 'Edit'],
            ['Delete-class', 'Class', 'Delete'],

            ['Index-subject', 'Subject', 'Index'],
            ['Create-subject', 'Subject', 'Create'],
            ['Edit-subject', 'Subject', 'Edit'],
            ['Delete-subject', 'Subject', 'Delete'],

            ['Index-grade', 'Grade', 'Index'],
            ['Create-grade', 'Grade', 'Create'],
            ['Edit-grade', 'Grade', 'Edit'],
            ['Delete-grade', 'Grade', 'Delete'],

            ['Index-grade-category', 'Grade Category', 'Index'],
            ['Create-grade-category', 'Grade Category', 'Create'],
            ['Edit-grade-category', 'Grade Category', 'Edit'],
            ['Delete-grade-category', 'Grade Category', 'Delete'],

            ['Index-session', 'Session', 'Index'],
            ['Create-session', 'Session', 'Create'],
            ['Edit-session', 'Session', 'Edit'],
            ['Delete-session', 'Session', 'Delete'],

            ['Index-attendance', 'Attendance', 'Index'],
            ['Create-attendance', 'Attendance', 'Create'],
            ['Edit-attendance', 'Attendance', 'Edit'],
            ['Delete-attendance', 'Attendance', 'Delete'],

            ['Index-intro', 'Intro', 'Index'],
            ['Create-intro', 'Intro', 'Create'],
            ['Edit-intro', 'Intro', 'Edit'],
            ['Delete-intro', 'Intro', 'Delete'],
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission[0], 'category' => $permission[1], 'guard_name' => 'user', 'display' => $permission[2]]);
        }
    }

    function roleCreation()
    {
        $role = Role::create(['name' => 'Super Admin', 'guard_name' => 'user']);
        $permissions = Permission::all();
        $role->syncPermissions($permissions);
        return $role;
    }

    function role2Creation()
    {
        $role = Role::create(['name' => 'School Manager', 'guard_name' => 'user']);
        $permissions = Permission::whereIn('category', ['Teacher', 'Student', 'Class', 'Grade', 'Subject', 'Attendance', 'Session', 'Grade Category', 'Student Graduation', 'Student Upgrade'])->get();
        $role->syncPermissions($permissions);
        return $role;
    }
    function role3Creation()
    {
        $role = Role::create(['name' => 'Teacher', 'guard_name' => 'user']);
        $permissions = Permission::where(function ($query) {
            $query->whereIn('category', ['Attendance'])
                ->orWhereIn('name', ['Index-class', 'Index-session', 'Index-student']);
        })->get();
        $role->syncPermissions($permissions);
        return $role;
    }
}
