<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\SimpleRedirects;
use Illuminate\Support\Facades\Log;

class LoadRedirectsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'simple_redirector:load
    {file : CSV file to load}
    {--refresh : Delete previous redirect entries}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get all the redirects from a CSV file and load them into the database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $t=new SimpleRedirects();

        if (!\Schema::hasTable(env('REDIRECTS_TABLE', 'omatech_simple_redirects'))) 
        {
            $t->recreateTable();
            Log::notice('Redirect table regenerated');
        }
        else
        {
            if ($this->option('refresh')) 
            {
                $t->recreateTable();
                Log::notice('Redirect table regenerated');
            }
        }


        $rows=$t->getCSVRecords($this->argument('file'), ';');

        $result=array();
        $parsed=array();
        foreach ($rows as $row)
        {
            if (!in_array($row['original_uri'], $parsed))
            {
                $result[]=$row;
                $parsed[]=$row['original_uri'];
            }
        }

        foreach ($result as $row)
        {
            $res=$t->where('original_uri',$row['original_uri'])->delete();
            if ($res)
            {
                echo "Removing previous url ".$row['original_uri']."\n";
            }
            echo "Loading redirection from:".$row['original_uri']." to:".$row['redirect_uri']."\n";
        }

        $t->bulkImport($result);
    }

}
