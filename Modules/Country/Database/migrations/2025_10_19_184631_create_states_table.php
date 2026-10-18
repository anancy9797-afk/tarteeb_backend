<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Modules\Country\App\Models\Governorate;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignIdFor(Governorate::class)->index()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

    \Modules\Country\App\Models\State::insert([
        // مسقط
        ['name' => 'مسقط', 'governorate_id' => 1, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'مطرح', 'governorate_id' => 1, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'بوشر', 'governorate_id' => 1, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'السيب', 'governorate_id' => 1, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'العامرات', 'governorate_id' => 1, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'قريات', 'governorate_id' => 1, 'created_at' => now(), 'updated_at' => now()],

        // ظفار
        ['name' => 'صلالة', 'governorate_id' => 2, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'طاقة', 'governorate_id' => 2, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'مرباط', 'governorate_id' => 2, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'رخيوت', 'governorate_id' => 2, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'ثمريت', 'governorate_id' => 2, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'ضلكوت', 'governorate_id' => 2, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'مقشن', 'governorate_id' => 2, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'شليم وجزر الحلانيات', 'governorate_id' => 2, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'المزيونة', 'governorate_id' => 2, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'سدح', 'governorate_id' => 2, 'created_at' => now(), 'updated_at' => now()],

        // مسندم
        ['name' => 'خصب', 'governorate_id' => 3, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'بخاء', 'governorate_id' => 3, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'دبا', 'governorate_id' => 3, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'مدحاء', 'governorate_id' => 3, 'created_at' => now(), 'updated_at' => now()],

        // البريمي
        ['name' => 'البريمي', 'governorate_id' => 4, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'محضة', 'governorate_id' => 4, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'السنينة', 'governorate_id' => 4, 'created_at' => now(), 'updated_at' => now()],

        // الداخلية
        ['name' => 'نزوى', 'governorate_id' => 5, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'سمائل', 'governorate_id' => 5, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'بهلاء', 'governorate_id' => 5, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'أدم', 'governorate_id' => 5, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'الحمراء', 'governorate_id' => 5, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'منح', 'governorate_id' => 5, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'إزكي', 'governorate_id' => 5, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'بدبد', 'governorate_id' => 5, 'created_at' => now(), 'updated_at' => now()],

        // شمال الباطنة
        ['name' => 'صحار', 'governorate_id' => 6, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'شناص', 'governorate_id' => 6, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'لوى', 'governorate_id' => 6, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'صحم', 'governorate_id' => 6, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'الخابورة', 'governorate_id' => 6, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'السويق', 'governorate_id' => 6, 'created_at' => now(), 'updated_at' => now()],

        // جنوب الباطنة
        ['name' => 'الرستاق', 'governorate_id' => 7, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'العوابي', 'governorate_id' => 7, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'نخل', 'governorate_id' => 7, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'وادي المعاول', 'governorate_id' => 7, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'بركاء', 'governorate_id' => 7, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'المصنعة', 'governorate_id' => 7, 'created_at' => now(), 'updated_at' => now()],

        // جنوب الشرقية
        ['name' => 'صور', 'governorate_id' => 8, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'الكامل والوافي', 'governorate_id' => 8, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'جعلان بني بو حسن', 'governorate_id' => 8, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'جعلان بني بو علي', 'governorate_id' => 8, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'مصيرة', 'governorate_id' => 8, 'created_at' => now(), 'updated_at' => now()],

        // شمال الشرقية
        ['name' => 'إبراء', 'governorate_id' => 9, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'المضيبي', 'governorate_id' => 9, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'بدية', 'governorate_id' => 9, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'القابل', 'governorate_id' => 9, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'وادي بني خالد', 'governorate_id' => 9, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'دماء والطائيين', 'governorate_id' => 9, 'created_at' => now(), 'updated_at' => now()],

        // الظاهرة
        ['name' => 'عبري', 'governorate_id' => 10, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'ينقل', 'governorate_id' => 10, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'ضنك', 'governorate_id' => 10, 'created_at' => now(), 'updated_at' => now()],

        // الوسطى
        ['name' => 'هيما', 'governorate_id' => 11, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'محوت', 'governorate_id' => 11, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'الدقم', 'governorate_id' => 11, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'الجازر', 'governorate_id' => 11, 'created_at' => now(), 'updated_at' => now()],
    ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('states');
    }
};
