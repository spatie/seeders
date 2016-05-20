<?php

namespace Spatie\Seeders;

use DB;
use File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Schema;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMedia;
use function Spatie\array_rand_value;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        if (!app()->environment('local', 'testing')) {
            throw new \Exception('Sorry, no full seeds on production!');
        }

        DB::connection()->disableQueryLog();

        Model::unguard();

        $this->truncateMediaTable();
        $this->truncateActivityTable();

        $this->clearMediaDirectory();
    }

    protected function truncate(string ...$tables)
    {
        collect($tables)->each(function (string $table) {
            if (str_contains($table, 'App\Models')) {
                $table = (new $table)->getTable();
            }

            $this->disableForeignKeyChecks();
            DB::table($table)->truncate();
            $this->enableForeignKeyChecks();
        });
    }

    protected function disableForeignKeyChecks()
    {
        if (config('database.default') === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }
    }

    protected function enableForeignKeyChecks()
    {
        if (config('database.default') === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    protected function truncateMediaTable()
    {
        if (Schema::hasTable('media')) {
            $this->truncate('media');
        }
    }

    protected function truncateActivityTable()
    {
        if (Schema::hasTable('activity_log')) {
            $this->truncate('activity_log');
        }
    }

    protected function clearMediaDirectory()
    {
        File::cleanDirectory(public_path().'/media');
    }

    protected function addImages(
        HasMedia $model,
        $min = 1,
        $max = 3,
        $collectionName = 'images'
    ) {
        $this->addFiles(__DIR__.'/../images', $model, $min, $max, $collectionName);
    }

    protected function addDownloads(
        HasMedia $model,
        $min = 1,
        $max = 3,
        $collectionName = 'downloads'
    ) {
        $this->addFiles(__DIR__.'/../downloads', $model, $min, $max, $collectionName);
    }

    protected function addFiles(
        string $sourceDirectory,
        HasMedia $model,
        int $min = 1,
        int $max = 3,
        string $collectionName
    ) {
        $files = (new Filesystem(new Local($sourceDirectory)))->listContents();

        foreach (range($min, mt_rand($min, $max)) as $index) {
            $file = array_rand_value($files)['path'];

            $model
                ->addMedia("{$sourceDirectory}/{$file}")
                ->preservingOriginal()
                ->toCollection($collectionName);
        }
    }
}
