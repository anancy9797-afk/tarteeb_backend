<?php

namespace Modules\Common\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Common\App\Models\Intro;

class CommonDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $intros = [
            // first section
            [
                'title_ar' => 'تتبع حضور الطلاب بسهولة',
                'title_en' => 'Effortless Student Attendance Tracking',
                'description_ar' => 'ودع الحضور اليدوي والأوراق. يساعد نظامنا الذكي المدارس على تتبع الحضور، وإخطار أولياء الأمور فوراً، وإنشاء تقارير مفيدة - كل ذلك في مكان واحد',
                'description_en' => 'Say goodbye to manual roll calls and paperwork. Our smart system helps schools track attendance, notify parents instantly, and generate insightful reports—all in one place',
                'image' => 'first-section-intro-image.jpg',
                'section' => 'first',
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // second section
            [
                'title_ar' => 'لماذا اختيار نظامنا؟',
                'title_en' => 'Why Choose Our System?',
                'description_ar' => 'تم تصميم نظامنا لتبسيط إدارة الحضور للمدارس والمدرسين',
                'description_en' => 'Our system is designed to simplify attendance management for schools, teachers',
                'image' => 'second-section-intro-image.jpg',
                'section' => 'second',
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // second section details
            [
                'title_ar' => 'تتبع الحضور الآلي',
                'title_en' => 'Automated Attendance Tracking',
                'description_ar' => 'ودع الحضور اليدوي! يمكن للمدرسين تسجيل الحضور بنقرة واحدة، ويتم تسجيل الغياب فورًا.',
                'description_en' => 'Say goodbye to manual roll calls! Teachers can mark attendance with a single click, and absences are recorded instantly.',
                'image' => 'rectangle.jpg',
                'section' => 'second',
                'parent_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title_ar' => 'الوصول المتعدد والأدوار المخصصة',
                'title_en' => ' Multi-User Access & Role-Based Permissions',
                'description_ar' => 'مصمم للمديرين والمدرسين، يضمن أن يكون لكل شخص الوصول الصحيح إلى البيانات التي يحتاجونها.',
                'description_en' => 'Designed for administrators and teachers, ensuring that everyone has the right level of access to the data they need.',
                'image' => 'rectangle.jpg',
                'section' => 'second',
                'parent_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title_ar' => 'الوصول المتعدد والأدوار المخصصة',
                'title_en' => 'Cloud-Based & Secure',
                'description_ar' => 'الوصول إلى النظام من أي مكان، على أي جهاز بتشفير بيانات كاملة وتخزين البيانات المحمي بأمان لحماية المعلومات الخاصة بالطلاب.',
                'description_en' => 'Access the system from anywhere, on any device with full data encryption and secure cloud storage to protect student information.',
                'image' => 'rectangle.jpg',
                'section' => 'second',
                'parent_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title_ar' => 'السياسات المتعددة للحضور',
                'title_en' => 'Customizable Attendance Policies',
                'description_ar' => 'يمكن للمدارس تعريف القواعد الخاصة بالحضور والأداء المتأخر والأجور المتأخرة، مما يجعل النظام قابلاً للتكيف لمختلف المدارس التعليمية',
                'description_en' => 'Schools can define their own attendance rules, late policies, and absence thresholds, making the system adaptable to different educational institutions',
                'image' => 'rectangle.jpg',
                'section' => 'second',
                'parent_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // third section
            [
                'title_ar' => 'كيف يعمل؟ دليل خطوة بخطوة',
                'title_en' => 'How It Works? Step-by-Step Guide',
                'description_ar' => 'يعمل نظامنا بشكل بسيط وفعال - فقط 3 خطوات سهلة',
                'description_en' => 'Our system is simple and efficient—just 3 easy steps',
                'image' => null,
                'section' => 'third',
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // third section details
            [
                'title_ar' => 'تسجيل الدخول إلى النظام',
                'title_en' => 'Login to the System',
                'description_ar' => 'يمكن للمدرسين والمديرين تسجيل الدخول بأمان من أي جهاز',
                'description_en' => 'Teachers & admins sign in securely from any device.',
                'image' => null,
                'section' => 'third',
                'parent_id' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title_ar' => 'تسجيل الحضور',
                'title_en' => 'Mark Attendance',
                'description_ar' => 'يمكن للمدرسين تسجيل الحضور بنقرة واحدة',
                'description_en' => 'Easily track student attendance with a single click.',
                'image' => null,
                'section' => 'third',
                'parent_id' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title_ar' => 'إخطار الأولياء وإنشاء التقارير',
                'title_en' => 'Notify & Generate Reports',
                'description_ar' => 'يمكن للمدرسين إخطار الأولياء وإنشاء التقارير بسهولة',
                'description_en' => 'receive real-time updates, and schools get insightful attendance reports.',
                'image' => null,
                'section' => 'third',
                'parent_id' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        Intro::insert($intros);
    }
}
