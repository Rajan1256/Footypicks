<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();


        \Schema::disableForeignKeyConstraints();
        foreach(\DB::select('SHOW TABLES') as $table) {
            $table_array = get_object_vars($table);
            \DB::table($table_array[key($table_array)])->truncate();
        }
        \Schema::enableForeignKeyConstraints();

        return $app;
    }
}
