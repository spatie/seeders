<?php

namespace Spatie\Seeders;

use DB;
use Faker\Factory;
use File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Schema;
use Spatie\Seeders\SuperSeeder\Parsers\YamlParser;
use Spatie\Seeders\SuperSeeder\SuperSeeder;
use function Spatie\array_rand_value;

class DatabaseSeeder extends Seeder
{
    /**
     * @var string
     */
    protected $tempImageDir;

    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var array
     */
    protected $excludeWhenTruncatingAll = ['migrations'];

    public function __construct()
    {
        $this->tempImageDir = storage_path('temp/seeder');
        $this->faker = Factory::create();
    }

    /**
     * Run the seeds.
     */
    public function run()
    {
        DB::connection()->disableQueryLog();

        Model::unguard();

        $this->truncateMediaTable();
        $this->truncateActivityTable();

        $this->createTemporaryImageDirectory();

        if (app()->environment() === 'local') {
            $this->clearMediaDirectory();
        }
    }

    /**
     * @param \Spatie\Seeders\SuperSeeder\Factory $factory
     * @param string                              $filename
     */
    protected function superSeeder($factory, $filename)
    {
        $superSeeder = new SuperSeeder($factory);

        $file = base_path("database/seeds/data/$filename.yml");

        return $superSeeder->seedFromFile($file, new YamlParser());
    }

    /**
     * Truncate the given tables.
     *
     * @param string $tables,...
     */
    protected function truncate(...$tables)
    {
        if (config('database.default') === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }

        if (config('database.default') === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    /**
     * Truncate all tables.
     */
    protected function truncateAllTables()
    {
        $this->truncate(...$this->getAllTableNames($this->excludeWhenTruncatingAll));
    }

    /**
     * Truncate the media table.
     */
    protected function truncateMediaTable()
    {
        if (Schema::hasTable('media')) {
            $this->truncate('media');
        }
    }

    /**
     * Truncate the activity log table.
     */
    protected function truncateActivityTable()
    {
        if (Schema::hasTable('activity_log')) {
            $this->truncate('activity_log');
        }
    }

    /**
     * Get the names of all tables.
     *
     * @param array $exclude
     * 
     * @return array
     */
    protected function getAllTableNames($exclude = [])
    {
        $query = sprintf('SELECT TABLE_NAME as name FROM information_schema.tables WHERE table_schema="%s"',
            DB::connection()->getDatabaseName());

        $tableNames = Collection::make(DB::select($query))
            ->map(function (\stdClass $rawResult) {
                return $rawResult->name;
            })
            ->filter(function ($tableName) use ($exclude) {
                return !in_array($tableName, $exclude);
            })
            ->toArray()
        ;

        return $tableNames;
    }

    protected function createTemporaryImageDirectory()
    {
        if (File::isDirectory($this->tempImageDir)) {
            File::cleanDirectory($this->tempImageDir);
        } else {
            File::makeDirectory($this->tempImageDir, 493, true);
        }
    }

    protected function clearMediaDirectory()
    {
        File::cleanDirectory(public_path().'/media');
    }

    /**
     * Add images to the given model.
     *
     * @param \Illuminate\Database\Eloquent $model
     * @param int                           $minAmount
     * @param int                           $maxAmount
     * @param string                        $collectionName
     */
    protected function addImages($model, $minAmount = 1, $maxAmount = 3, $collectionName = 'images')
    {
        $this->addFiles(__DIR__.'/../images', $model, $minAmount, $maxAmount, $collectionName);
    }

    /**
     * Add downloads to the given model.
     *
     * @param \Illuminate\Database\Eloquent $model
     * @param int                           $minAmount
     * @param int                           $maxAmount
     * @param string                        $collectionName
     */
    protected function addDownloads($model, $minAmount = 1, $maxAmount = 1, $collectionName = 'downloads')
    {
        $this->addFiles(__DIR__.'/../downloads', $model, $minAmount, $maxAmount, $collectionName);
    }

    /**
     * Add files to the given model.
     *
     * @param $sourceDirectory
     * @param \Illuminate\Database\Eloquent $model
     * @param int                           $minAmount
     * @param int                           $maxAmount
     * @param string                        $collectionName
     */
    protected function addFiles($sourceDirectory, $model, $minAmount = 1, $maxAmount = 3, $collectionName)
    {
        if (env('APP_ENV') === 'testing') {
            return;
        }

        $files = (new Filesystem(new Local($sourceDirectory)))->listContents();

        foreach (range(1, $this->faker->numberBetween($minAmount, $maxAmount)) as $index) {
            $file = array_rand_value($files)['path'];

            $model
                ->addMedia("{$sourceDirectory}/{$file}")
                ->preservingOriginal()
                ->toCollection($collectionName);
        }
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model Should use \Dimsav\Translatable\Translatable trait
     */
    protected function addTranslations($model)
    {
        foreach (config('app.locales') as $locale) {
            $translation = factory($model->getTranslationModelName())->make(['locale' => $locale]);
            $model->translations()->save($translation);
        }
    }
}
