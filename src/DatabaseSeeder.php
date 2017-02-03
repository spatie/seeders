<?php

namespace Spatie\Seeders;

use DB;
use File;
use Schema;
use App\Models\ContentBlock;
use Illuminate\Database\Seeder;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMedia;

class DatabaseSeeder extends Seeder
{
    public static $withMedia = true;

    public function run()
    {
        DB::connection()->disableQueryLog();

        Model::unguard();

        $this->truncateMediaTable();
        $this->truncateContentBlocksTable();
        $this->truncateActivityTable();

        $this->clearMediaDirectory();
    }

    protected function truncate(string ...$tables)
    {
        collect($tables)->each(function (string $table) {
            if (str_contains($table, 'App\Models')) {
                $table = (new $table)->getTable();
            }

            Schema::disableForeignKeyConstraints();
            DB::table($table)->truncate();
            Schema::enableForeignKeyConstraints();
        });
    }

    protected function truncateMediaTable()
    {
        if (Schema::hasTable('media')) {
            $this->truncate('media');
        }
    }

    protected function truncateContentBlocksTable()
    {
        if (Schema::hasTable('content_blocks')) {
            $this->truncate('content_blocks');
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
        int $min,
        int $max,
        string $collectionName
    ) {
        $files = collect(
            (new Filesystem(new Local($sourceDirectory)))->listContents()
        );

        collect(range($min, mt_rand($min, $max)))
            ->each(function () use ($files, $model, $sourceDirectory, $collectionName) {
                $file = $files->random()['path'];

                $model
                    ->addMedia("{$sourceDirectory}/{$file}")
                    ->preservingOriginal()
                    ->toMediaLibrary($collectionName);
            });
    }

    public function addContentBlocks(Model $model, $minimum = 1, $maximum = 3): Model
    {
        $maximum = faker()->numberBetween($minimum, $maximum);

        if ($maximum === 0) {
            return $model;
        }

        foreach (range($minimum, $maximum) as $i) {
            $contentBlock = ContentBlock::create([
                'type' => faker()->randomElement(['imageLeft', 'imageRight']),
                'name' => faker()->translate(faker()->sentence()),
                'text' => faker()->translate(faker()->paragraph()),
                'draft' => false,
                'online' => true,
            ]);

            $this->addImages($contentBlock, 1, 1, 'image');
            $contentBlock->collection_name = 'default';
            $contentBlock->subject()->associate($model);
            $contentBlock->save();
        }

        return $model;
    }
}
