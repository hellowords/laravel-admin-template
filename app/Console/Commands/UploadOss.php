<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\File;

class UploadOss extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload:oss {path=/js/v1} {--F|file=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '将本地文件上传至OSS';

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
        $path = $this->argument('path');
        $file = $this->option('file');
        $this->info($file);
        if (!$file) {
            $this->error('请输入上传文件的路径');
            return;
        }
        $fileInfo = new File($file);
        $info = \Storage::putFileAs($path,$fileInfo,$fileInfo->getFilename());
        $this->info('上传成功！文件路径:'.\Storage::url($info));
        return;
    }
}
