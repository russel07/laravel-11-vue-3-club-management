<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Filesystem\Filesystem;

class DeveloperController extends Controller
{
    public function index(){
        echo "welcome";
    }

    public function cacheControl(){

        echo 'Running commands...............'.PHP_EOL;
        Artisan::call('view:clear');
        Artisan::call('config:cache');

        return 'view cache clear commands is in run.....';
    }

    public function dbMigration(){

        echo 'Database migrating....'.PHP_EOL;
        return Artisan::call('migrate', array('--force'=>true, '--verbose'=>true));
    }

    public function dbSeed(){

        echo 'Database seeding....'.PHP_EOL;
        return Artisan::call('db:seed', array('--force'=>true, '--verbose'=>true));
    }

    public function composerDump(){
        echo 'Composer dumping.....'.PHP_EOL.'<pre>';
        return shell_exec('help composer ');
    }

    public function passportInstall(){

        echo 'Passport installing....'.PHP_EOL;
        return Artisan::call('passport:client', array('--force'=>true,));
    }
}
