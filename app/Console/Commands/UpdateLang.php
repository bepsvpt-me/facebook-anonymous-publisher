<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class UpdateLang extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lang:update {--lang=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the language translation.';

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var string
     */
    protected $srcPath;

    /**
     * UpdateLang constructor.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();

        $this->filesystem = $filesystem;

        $this->srcPath = file_build_path('vendor', 'caouecs', 'laravel-lang', 'src');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $langs = $this->option('lang');

        if (! is_null($langs) && $this->filesystem->isDirectory($this->srcPath($langs))) {
            $langs = [$langs];
        } else {
            $langs = $this->filesystem->directories($this->srcPath());
        }

        foreach ($langs as $lang) {
            $this->filesystem->copyDirectory(
                $lang,
                resource_path(file_build_path('lang', $this->filesystem->name($lang)))
            );
        }

        $this->info('Language updated!');
    }

    /**
     * Get the path to the base of the language src.
     *
     * @param string $path
     *
     * @return string
     */
    protected function srcPath($path = '')
    {
        return base_path($this->srcPath.($path ? DIRECTORY_SEPARATOR.$path : $path));
    }
}
