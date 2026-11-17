<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Models\Product;
final class CatalogController {
    public function index(): void {
        $search=mb_substr(trim((string)($_GET['q'] ?? '')),0,100);
        $categoryId=max(0,(int)($_GET['categoria'] ?? 0));
        \render('catalog',[
            'title'=>'Cupcakes feitos com carinho','search'=>$search,'categoryId'=>$categoryId,
            'categories'=>Product::categories(),'products'=>Product::catalog($search,$categoryId)
        ]);
    }
}
