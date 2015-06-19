<?php

namespace Spatie\Seeders;

use Faker\Factory;
use DB;
use File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Spatie\MediaLibrary\MediaLibraryModel\MediaLibraryModelInterface;
use Spatie\MediaLibrary\Models\Media;
use Spatie\SuperSeeder\Parsers\YamlParser;
use Spatie\SuperSeeder\SuperSeeder;

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

    public function __construct()
    {
        $this->tempImageDir = storage_path() . '/tempSeeder';
        $this->faker        = Factory::create();
    }

    /**
     * Run the seeds
     */
    public function run()
    {
        DB::connection()->disableQueryLog();

        Model::unguard();

        $this->truncate((new Media)->getTable());

        $this->createTemporaryImageDirectory();

        if (app()->environment() == 'local') {
            $this->clearMediaDirectory();
        }
    }

    /**
     * @param  \Spatie\SuperSeeder\Factory $factory
     * @param  string $filename
     */
    protected function superSeeder($factory, $filename)
    {
        $superSeeder = new SuperSeeder($factory);

        $file = base_path("database/seeds/data/$filename.yml");

        return $superSeeder->seedFromFile($file, new YamlParser);
    }

    /**
     * @param  string $tables,...
     */
    protected function truncate(...$tables)
    {
        DB::statement("SET FOREIGN_KEY_CHECKS=0");

        foreach($tables as $table) {
            DB::table($table)->truncate();
        }
        
        DB::statement("SET FOREIGN_KEY_CHECKS=1");
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
     * @param  \Spatie\MediaLibrary\MediaLibraryModel\MediaLibraryModelInterface $model
     * @param  int $minAmount
     * @param  int $maxAmount
     */
    protected function addImages(MediaLibraryModelInterface $model, $minAmount = 1, $maxAmount = 3)
    {
        foreach (range(1, $this->faker->numberBetween($minAmount, $maxAmount)) as $index) {
            $model->addMedia($this->faker->image($this->tempImageDir, 640, 480), 'images', false, false);
        }
    }
}
