<?php

namespace YK\Basic\Console;

use Illuminate\Console\Command;
use YK\Basic\Extensions\AdminUserFactory;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'basic:install';

    protected $directory ='';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the commands necessary to prepare Basic for use';


    /**
     * @param $addition
     * @return false|string
     */
    private function formatTimetamp($addition)
    {
        return date('Y_m_d_His', time() + $addition);
    }


    private function registerMigrations()
    {
        $migrationsPath = __DIR__ . '/../../database/migrations/';

        $fileList=$this->laravel['files']->allFiles(database_path('migrations'));
        $items = [
            'create_admin_table.php',
            'add_custom_field_permission_tables.php',
            'create_menu_table.php',
            'create_permission_group_table.php',
            'create_param_table.php',
            'create_dictionary_table.php',
        ];

        //$paths = [];
        foreach ($items as $key => $name) {
            $newfile=database_path('migrations') . "/". $this->formatTimetamp($key+1) . '_' . $name;
            $existfile=false;
            foreach($fileList as $f){
                if (strpos($f,$name) !== false){
                    $existfile=true;
                    break;
                }
            }
            if (!$existfile) {
                $this->laravel['files']->copy($migrationsPath . $name, $newfile);
                $this->line('<info>copy file:</info> '.$name);
            }else
                $this->line('<info>skip copy file:</info> '.$name);

            //$paths[$migrationsPath . $name] = database_path('migrations') . "/". $this->formatTimetamp($key+1) . '_' . $name;
        }


    }
    /**
     * Create tables and seed it.
     *
     * @return void
     */
    public function initDatabase()
    {
        $this->call('migrate');

        if (AdminUserFactory::adminUser()->count()==0) {
            $this->line('<info>DB:seed---------</info> ');
            $this->call('db:seed', ['--class' => \YK\Basic\database\BasicSeeder::class]);
        }
    }

    /**
     * Initialize the admAin directory.
     *
     * @return void
     */
    protected function initAdminDirectory()
    {
        $this->directory = config('admin.directory');

        if (is_dir($this->directory)) {
            $this->line("<error>{$this->directory} directory already exists !</error> ");
            return;
        }

        $this->makeDir('/');
        $this->line('<info>Admin directory was created:</info> '.str_replace(base_path(), '', $this->directory));

        $this->makeDir('Controllers');
        $this->makeDir('Models');
        $this->makeDir('Requests');
        $this->makeDir('Resources');

        $this->createRoutesFile();
    }

    /**
     * Create routes file.
     *
     * @return void
     */
    protected function createRoutesFile()
    {
        $file = $this->directory.'/routes.php';

        $contents = $this->getStub('routes');
        $this->laravel['files']->put($file, str_replace('DummyNamespace', config('admin.route.namespace'), $contents));
        $this->line('<info>Routes file was created:</info> '.str_replace(base_path(), '', $file));
    }

    /**
     * Get stub contents.
     *
     * @param $name
     *
     * @return string
     */
    protected function getStub($name)
    {
        return $this->laravel['files']->get(__DIR__."/stubs/$name.stub");
    }

    /**
     * Make new directory.
     *
     * @param string $path
     */
    protected function makeDir($path = '')
    {
        $this->laravel['files']->makeDirectory("{$this->directory}/$path", 0755, true, true);
    }

    /**
     * Execute the console command.
     *
     * @author nash
     * @return mixed
     */
    public function handle()
    {

        $this->call('vendor:publish', ['--provider' => 'Spatie\Permission\PermissionServiceProvider']);
        $this->call('vendor:publish', ['--provider' => 'SMartins\PassportMultiauth\Providers\MultiauthServiceProvider']);
        //$this->call('vendor:publish', ['--provider' => 'YK\Basic\Providers\BasicServiceProvider']);
        $this->registerMigrations();
        $this->initDatabase();
        $this->initAdminDirectory();
    }
}
