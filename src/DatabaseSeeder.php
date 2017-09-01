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

    const IMAGESET_BUILDINGS = 'buildings';
    const IMAGESET_PEOPLE = 'people';
    const IMAGESET_LANDSCAPES = 'landscapes';
    const IMAGESET_FOOD = 'food';
    const IMAGESET_CITYSCAPES = 'cityscapes';
    const IMAGESET_LOGOS = 'logos';

    public function run()
    {
        DB::connection()->disableQueryLog();

        Model::unguard();

        $this->truncateAll();

        $this->clearMediaDirectory();
    }

    protected function truncateAll()
    {
        Schema::disableForeignKeyConstraints();

        collect(DB::select("SHOW FULL TABLES WHERE Table_Type = 'BASE TABLE'"))
            ->map(function ($tableProperties) {
                return get_object_vars($tableProperties)[key($tableProperties)];
            })
            ->reject(function (string $tableName) {
                return $tableName === 'migrations';
            })
            ->each(function (string $tableName) {
                DB::table($tableName)->truncate();
            });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * @param string[] ...$tables
     *
     * @deprecated
     */
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

    protected function clearMediaDirectory()
    {
        File::cleanDirectory(public_path().'/media');
    }

    protected function addImages(
        HasMedia $model,
        $min = 1,
        $max = 3,
        $collectionName = 'images',
        $setName = 'buildings'
    ) {
        $sourceDirectory = __DIR__.'/../images/'.$setName;
        $this->addFiles($sourceDirectory, $model, $min, $max, $collectionName);
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
                    ->toMediaCollection($collectionName);
            });
    }

    public function addContentBlocks(Model $model, $minimum = 1, $maximum = 3): Model
    {
        $maximum = faker()->numberBetween($minimum, $maximum);

        if ($maximum === 0) {
            return $model;
        }

        foreach (range($minimum, $maximum) as $i) {
            $contentBlock = new ContentBlock([
                'type' => faker()->randomElement(['imageLeft', 'imageRight']),
                'name' => faker()->translate(faker()->sentence()),
                'text' => faker()->translate(faker()->paragraph()),
                'draft' => false,
                'online' => true,
            ]);

            $contentBlock->collection_name = 'default';
            $contentBlock->subject()->associate($model);
            $contentBlock->save();

            $this->addImages($contentBlock, 1, 1, 'image');
        }

        return $model;
    }
}
