<?php

namespace Database\Seeders\products;

use App\Services\Catalog\ProductService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;

class ProductSeeder extends Seeder
{
     private string $ignoreExampleYamlFileName = "example.yaml";
     private string $yamlContentRoot = "database/seeders/products/yaml";

     public function __construct(
         private ProductService $productService,
     ) {
         //
     }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seed();
    }

    public function seed(): void
     {
        $productFiles = $this->getProductYamlFiles();

         /** @var SplFileInfo $productFile */
         foreach ($productFiles as $productFile) {
             if ($productFile->getFilename() === $this->ignoreExampleYamlFileName) {
                 continue; // We will skip the example file
             }

             $productArray = $this->readYamlFile($productFile);
             try {
                 $product = DB::transaction(function() use ($productArray) {
                     return $this->productService->importProductFromArray($productArray);
                 });
             }  catch (\Exception $exception) {
                 $this->command->alert("Something went wrong with `{$productArray['name']}`... Writing exception to log and skipping.");
                 throw new \Exception($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
             }

             $this->command->info("Successfully created: `{$product->name}`");
         }
     }

     private function getProductYamlFiles(): array
     {
        return File::files($this->yamlContentRoot);
     }

     private function readYamlFile(SplFileInfo $file)
     {
         return Yaml::parse($file->getContents());
     }
}
