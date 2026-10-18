<?php

use Modules\Country\App\Models\State;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignIdFor(State::class)->index()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        \Modules\Country\App\Models\Region::insert([
            // مسقط - مسقط
            ['name' => 'روي', 'state_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'الموالح', 'state_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'الخوض', 'state_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'الغبرة', 'state_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'الموج', 'state_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'القرم', 'state_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'المعبيلة', 'state_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'الحيل', 'state_id' => 1, 'created_at' => now(), 'updated_at' => now()],

            // مسقط - مطرح
            ['name' => 'مطرح', 'state_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'وادي عدي', 'state_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'روي', 'state_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'الحمرية', 'state_id' => 2, 'created_at' => now(), 'updated_at' => now()],

            // مسقط - بوشر
            ['name' => 'بوشر', 'state_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'الأنصب', 'state_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'الصاروج', 'state_id' => 3, 'created_at' => now(), 'updated_at' => now()],

            // مسقط - السيب
            ['name' => 'السيب', 'state_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'الموالح الجنوبية', 'state_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'الحيل الشمالية', 'state_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'الحيل الجنوبية', 'state_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'الموج', 'state_id' => 4, 'created_at' => now(), 'updated_at' => now()],

            // مسقط - العامرات
            ['name' => 'العامرات', 'state_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'مسقط الجديدة', 'state_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'الحرث', 'state_id' => 5, 'created_at' => now(), 'updated_at' => now()],

            // مسقط - قريات
            ['name' => 'قريات', 'state_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'دغمر', 'state_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'حيل الغاف', 'state_id' => 6, 'created_at' => now(), 'updated_at' => now()],

            // ظفار - صلالة
            ['name' => 'صلالة', 'state_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'أوقد', 'state_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'حدبين', 'state_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ريسوت', 'state_id' => 7, 'created_at' => now(), 'updated_at' => now()],

            // ظفار - طاقة
            ['name' => 'طاقة', 'state_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'مدينة الحق', 'state_id' => 8, 'created_at' => now(), 'updated_at' => now()],

            // ظفار - مرباط
            ['name' => 'مرباط', 'state_id' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'حاسك', 'state_id' => 9, 'created_at' => now(), 'updated_at' => now()],

            // ظفار - رخيوت
            ['name' => 'رخيوت', 'state_id' => 10, 'created_at' => now(), 'updated_at' => now()],

            // ظفار - ثمريت
            ['name' => 'ثمريت', 'state_id' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'حبروت', 'state_id' => 11, 'created_at' => now(), 'updated_at' => now()],

            // مسندم - خصب
            ['name' => 'خصب', 'state_id' => 17, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'كمزار', 'state_id' => 17, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'قدع', 'state_id' => 17, 'created_at' => now(), 'updated_at' => now()],

            // مسندم - بخاء
            ['name' => 'بخاء', 'state_id' => 18, 'created_at' => now(), 'updated_at' => now()],

            // مسندم - دبا
            ['name' => 'دبا', 'state_id' => 19, 'created_at' => now(), 'updated_at' => now()],

            // مسندم - مدحاء
            ['name' => 'مدحاء', 'state_id' => 20, 'created_at' => now(), 'updated_at' => now()],

            // البريمي - البريمي
            ['name' => 'البريمي', 'state_id' => 21, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'حفيت', 'state_id' => 21, 'created_at' => now(), 'updated_at' => now()],

            // البريمي - محضة
            ['name' => 'محضة', 'state_id' => 22, 'created_at' => now(), 'updated_at' => now()],

            // البريمي - السنينة
            ['name' => 'السنينة', 'state_id' => 23, 'created_at' => now(), 'updated_at' => now()],

            // الداخلية - نزوى
            ['name' => 'نزوى', 'state_id' => 24, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'بركة الموز', 'state_id' => 24, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'الجبل الأخضر', 'state_id' => 24, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'تنوف', 'state_id' => 24, 'created_at' => now(), 'updated_at' => now()],

            // الداخلية - سمائل
            ['name' => 'سمائل', 'state_id' => 25, 'created_at' => now(), 'updated_at' => now()],

            // الداخلية - بهلاء
            ['name' => 'بهلاء', 'state_id' => 26, 'created_at' => now(), 'updated_at' => now()],

            // الداخلية - أدم
            ['name' => 'أدم', 'state_id' => 27, 'created_at' => now(), 'updated_at' => now()],

            // الداخلية - الحمراء
            ['name' => 'الحمراء', 'state_id' => 28, 'created_at' => now(), 'updated_at' => now()],

            // الداخلية - منح
            ['name' => 'منح', 'state_id' => 29, 'created_at' => now(), 'updated_at' => now()],

            // الداخلية - إزكي
            ['name' => 'إزكي', 'state_id' => 30, 'created_at' => now(), 'updated_at' => now()],

            // الداخلية - بدبد
            ['name' => 'بدبد', 'state_id' => 31, 'created_at' => now(), 'updated_at' => now()],

            // شمال الباطنة - صحار
            ['name' => 'صحار', 'state_id' => 32, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'مجز', 'state_id' => 32, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'الطريف', 'state_id' => 32, 'created_at' => now(), 'updated_at' => now()],

            // شمال الباطنة - شناص
            ['name' => 'شناص', 'state_id' => 33, 'created_at' => now(), 'updated_at' => now()],

            // شمال الباطنة - لوى
            ['name' => 'لوى', 'state_id' => 34, 'created_at' => now(), 'updated_at' => now()],

            // شمال الباطنة - صحم
            ['name' => 'صحم', 'state_id' => 35, 'created_at' => now(), 'updated_at' => now()],

            // شمال الباطنة - الخابورة
            ['name' => 'الخابورة', 'state_id' => 36, 'created_at' => now(), 'updated_at' => now()],

            // شمال الباطنة - السويق
            ['name' => 'السويق', 'state_id' => 37, 'created_at' => now(), 'updated_at' => now()],

            // جنوب الباطنة - الرستاق
            ['name' => 'الرستاق', 'state_id' => 38, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'الحوقين', 'state_id' => 38, 'created_at' => now(), 'updated_at' => now()],

            // جنوب الباطنة - العوابي
            ['name' => 'العوابي', 'state_id' => 39, 'created_at' => now(), 'updated_at' => now()],

            // جنوب الباطنة - نخل
            ['name' => 'نخل', 'state_id' => 40, 'created_at' => now(), 'updated_at' => now()],

            // جنوب الباطنة - وادي المعاول
            ['name' => 'وادي المعاول', 'state_id' => 41, 'created_at' => now(), 'updated_at' => now()],

            // جنوب الباطنة - بركاء
            ['name' => 'بركاء', 'state_id' => 42, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'الملدة', 'state_id' => 42, 'created_at' => now(), 'updated_at' => now()],

            // جنوب الباطنة - المصنعة
            ['name' => 'المصنعة', 'state_id' => 43, 'created_at' => now(), 'updated_at' => now()],

            // جنوب الشرقية - صور
            ['name' => 'صور', 'state_id' => 44, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'رأس الحد', 'state_id' => 44, 'created_at' => now(), 'updated_at' => now()],

            // جنوب الشرقية - الكامل والوافي
            ['name' => 'الكامل والوافي', 'state_id' => 45, 'created_at' => now(), 'updated_at' => now()],

            // جنوب الشرقية - جعلان بني بو حسن
            ['name' => 'جعلان بني بو حسن', 'state_id' => 46, 'created_at' => now(), 'updated_at' => now()],

            // جنوب الشرقية - جعلان بني بو علي
            ['name' => 'جعلان بني بو علي', 'state_id' => 47, 'created_at' => now(), 'updated_at' => now()],

            // جنوب الشرقية - مصيرة
            ['name' => 'مصيرة', 'state_id' => 48, 'created_at' => now(), 'updated_at' => now()],

            // شمال الشرقية - إبراء
            ['name' => 'إبراء', 'state_id' => 49, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'سناو', 'state_id' => 49, 'created_at' => now(), 'updated_at' => now()],

            // شمال الشرقية - المضيبي
            ['name' => 'المضيبي', 'state_id' => 50, 'created_at' => now(), 'updated_at' => now()],

            // شمال الشرقية - بدية
            ['name' => 'بدية', 'state_id' => 51, 'created_at' => now(), 'updated_at' => now()],

            // شمال الشرقية - القابل
            ['name' => 'القابل', 'state_id' => 52, 'created_at' => now(), 'updated_at' => now()],

            // شمال الشرقية - وادي بني خالد
            ['name' => 'وادي بني خالد', 'state_id' => 53, 'created_at' => now(), 'updated_at' => now()],

            // شمال الشرقية - دماء والطائيين
            ['name' => 'دماء والطائيين', 'state_id' => 54, 'created_at' => now(), 'updated_at' => now()],

            // الظاهرة - عبري
            ['name' => 'عبري', 'state_id' => 55, 'created_at' => now(), 'updated_at' => now()],

            // الظاهرة - ينقل
            ['name' => 'ينقل', 'state_id' => 56, 'created_at' => now(), 'updated_at' => now()],

            // الظاهرة - ضنك
            ['name' => 'ضنك', 'state_id' => 57, 'created_at' => now(), 'updated_at' => now()],

            // الوسطى - هيما
            ['name' => 'هيما', 'state_id' => 58, 'created_at' => now(), 'updated_at' => now()],

            // الوسطى - محوت
            ['name' => 'محوت', 'state_id' => 59, 'created_at' => now(), 'updated_at' => now()],

            // الوسطى - الدقم
            ['name' => 'الدقم', 'state_id' => 60, 'created_at' => now(), 'updated_at' => now()],

            // الوسطى - الجازر
            ['name' => 'الجازر', 'state_id' => 61, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};
