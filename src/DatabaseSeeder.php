<?php

namespace Spatie\Seeders;

use DB;
use Faker\Factory;
use File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Schema;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\Media;
use Spatie\Seeders\SuperSeeder\Parsers\YamlParser;
use Spatie\Seeders\SuperSeeder\SuperSeeder;

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
        $this->tempImageDir = storage_path().'/tempSeeder';
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
        $mediaTable = (new Media())->getTable();

        if (Schema::hasTable($mediaTable)) {
            $this->truncate($mediaTable);
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
            ->map(function(\stdClass $rawResult) {
                return $rawResult->name;
            })
            ->filter(function ($tableName) {
                return !in_array($tableName, $this->excludeWhenTruncatingAll);
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
     * @param \Spatie\MediaLibrary\MediaLibraryModel\HasMedia $model
     * @param int                                             $minAmount
     * @param int                                             $maxAmount
     * @param string                                          $collectionName
     */
    protected function addImages(HasMedia $model, $minAmount = 1, $maxAmount = 3, $collectionName = 'images')
    {
        if (env('APP_ENV') === 'testing') {
            return;
        }
        
        foreach (range(1, $this->faker->numberBetween($minAmount, $maxAmount)) as $index) {
            $model->addMedia($this->faker->image($this->tempImageDir, 640, 480), $collectionName, false, false);
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
