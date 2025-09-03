<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Проверим, существуют ли уже эти внешние ключи, чтобы избежать ошибок
        $existingForeignKeysResult = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'plan_change_logs'
            AND REFERENCED_TABLE_NAME IS NOT NULL
            AND CONSTRAINT_NAME IN (
                'fk_plan_change_logs_service_id',
                'fk_plan_change_logs_old_plan_id',
                'fk_plan_change_logs_new_plan_id'
            )
        ");
        $existingForeignKeys = collect($existingForeignKeysResult)->pluck('CONSTRAINT_NAME')->toArray();

        try {
            if (!in_array('fk_plan_change_logs_service_id', $existingForeignKeys)) {
                echo "Добавляем внешний ключ для service_id...\n";
                DB::statement("
                    ALTER TABLE plan_change_logs
                    ADD CONSTRAINT `fk_plan_change_logs_service_id`
                    FOREIGN KEY (`service_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
                ");
            }

            if (!in_array('fk_plan_change_logs_old_plan_id', $existingForeignKeys)) {
                echo "Добавляем внешний ключ для old_plan_id...\n";
                DB::statement("
                    ALTER TABLE plan_change_logs
                    ADD CONSTRAINT `fk_plan_change_logs_old_plan_id`
                    FOREIGN KEY (`old_plan_id`) REFERENCES `packages` (`id`)
                ");
            }

            if (!in_array('fk_plan_change_logs_new_plan_id', $existingForeignKeys)) {
                echo "Добавляем внешний ключ для new_plan_id...\n";
                DB::statement("
                    ALTER TABLE plan_change_logs
                    ADD CONSTRAINT `fk_plan_change_logs_new_plan_id`
                    FOREIGN KEY (`new_plan_id`) REFERENCES `packages` (`id`)
                ");
            }
        } catch (\Exception $e) {
            // Если возникла ошибка, попробуем более грубый способ - сначала удалить возможные сломанные FK
            echo "Ошибка при добавлении FK: " . $e->getMessage() . "\n";
            echo "Попытка восстановления...\n";

            // Попробуем удалить возможные сломанные или неправильно названные FK
            DB::statement("ALTER TABLE plan_change_logs DROP FOREIGN KEY IF EXISTS plan_change_logs_service_id_foreign");
            DB::statement("ALTER TABLE plan_change_logs DROP FOREIGN KEY IF EXISTS plan_change_logs_old_plan_id_foreign");
            DB::statement("ALTER TABLE plan_change_logs DROP FOREIGN KEY IF EXISTS plan_change_logs_new_plan_id_foreign");
            DB::statement("ALTER TABLE plan_change_logs DROP FOREIGN KEY IF EXISTS fk_plan_change_logs_service_id");
            DB::statement("ALTER TABLE plan_change_logs DROP FOREIGN KEY IF EXISTS fk_plan_change_logs_old_plan_id");
            DB::statement("ALTER TABLE plan_change_logs DROP FOREIGN KEY IF EXISTS fk_plan_change_logs_new_plan_id");
            // Также попробуем удалить индексы с такими именами
            DB::statement("ALTER TABLE plan_change_logs DROP INDEX IF EXISTS plan_change_logs_service_id_foreign");
            DB::statement("ALTER TABLE plan_change_logs DROP INDEX IF EXISTS plan_change_logs_old_plan_id_foreign");
            DB::statement("ALTER TABLE plan_change_logs DROP INDEX IF EXISTS plan_change_logs_new_plan_id_foreign");
            DB::statement("ALTER TABLE plan_change_logs DROP INDEX IF EXISTS fk_plan_change_logs_service_id");
            DB::statement("ALTER TABLE plan_change_logs DROP INDEX IF EXISTS fk_plan_change_logs_old_plan_id");
            DB::statement("ALTER TABLE plan_change_logs DROP INDEX IF EXISTS fk_plan_change_logs_new_plan_id");

            // И попробуем снова
            echo "Повторная попытка добавления внешних ключей...\n";
            DB::statement("
                ALTER TABLE plan_change_logs
                ADD CONSTRAINT `fk_plan_change_logs_service_id`
                FOREIGN KEY (`service_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
            ");
            DB::statement("
                ALTER TABLE plan_change_logs
                ADD CONSTRAINT `fk_plan_change_logs_old_plan_id`
                FOREIGN KEY (`old_plan_id`) REFERENCES `packages` (`id`)
            ");
            DB::statement("
                ALTER TABLE plan_change_logs
                ADD CONSTRAINT `fk_plan_change_logs_new_plan_id`
                FOREIGN KEY (`new_plan_id`) REFERENCES `packages` (`id`)
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            echo "Удаляем внешние ключи...\n";
            DB::statement("ALTER TABLE plan_change_logs DROP FOREIGN KEY IF EXISTS fk_plan_change_logs_service_id");
            DB::statement("ALTER TABLE plan_change_logs DROP FOREIGN KEY IF EXISTS fk_plan_change_logs_old_plan_id");
            DB::statement("ALTER TABLE plan_change_logs DROP FOREIGN KEY IF EXISTS fk_plan_change_logs_new_plan_id");
        } catch (\Exception $e) {
            echo "Ошибка при удалении FK (это может быть нормально, если ключи не существуют): " . $e->getMessage() . "\n";
        }
    }
};
