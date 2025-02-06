<?php

namespace YourPackage\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

class GenerateRouteTests extends Command
{
    protected $signature = 'your-package:generate-tests';
    protected $description = 'Generate tests for all routes in your package';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Generating tests for all routes in your package...');

        // دریافت روت‌های پروژه
        $routes = Route::getRoutes();

        // مسیر ذخیره تست‌ها
        $testDirectory = base_path('tests/Feature/');
        
        // بررسی وجود دایرکتوری تست‌ها
        if (!File::exists($testDirectory)) {
            File::makeDirectory($testDirectory, 0777, true);
        }

        foreach ($routes as $route) {
            $uri = $route->uri();
            $methods = $route->methods();
            $parameters = $route->parameterNames();
            
            // تولید نام تست
            $testName = 'Test' . str_replace('/', '_', $uri) . 'Test';
            $testClass = '<?php

namespace Tests\Feature;

use Tests\TestCase;

class ' . $testName . ' extends TestCase
{
    public function test_' . strtolower($methods[0]) . '_method()
    {
        $response = $this->' . strtolower($methods[0]) . '(\'' . $uri . '\');
        
        $response->assertStatus(200);
    }
}';
            
            // اگر روت پارامتر داشته باشد
            if (count($parameters) > 0) {
                $testClass .= '
                public function test_with_parameters()
                {
                    $response = $this->' . strtolower($methods[0]) . '(\'' . $uri . '\', [' . implode(', ', array_map(fn($param) => '\'' . $param . '\' => 1', $parameters)) . ']);
                    $response->assertStatus(200);
                }';
            }

            // ذخیره فایل تست
            $testFilePath = $testDirectory . $testName . '.php';
            File::put($testFilePath, $testClass);

            $this->info('Test generated for route: ' . $uri);
        }

        $this->info('Tests generated successfully!');
    }
}
