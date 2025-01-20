<?php

namespace App\Http\Controllers\Catalog;

use App\Enums\Actions\SaveAndAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\Category\CategoryDeleteRequest;
use App\Http\Requests\Catalog\Category\CategoryStoreRequest;
use App\Http\Requests\Catalog\Category\CategoryUpdateRequest;
use App\Models\Category;
use App\Services\Catalog\CategoryService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService,
    ) {
        //
    }

    public function list(): Factory|View|Application
    {
        return view(view: "catalog.category.category-list", data: [
            'categories' => $this->categoryService->getCategories(),
        ]);
    }

    public function create(): Factory|View|Application
    {
        return view(view: "catalog.category.category-create");
    }

    public function store(CategoryStoreRequest $request): RedirectResponse
    {
        $category = $this->categoryService->store(data: $request->validated());

        return $this->saveAndAction(
            action: $request->get('action'),
            category: $category,
        );
    }

    public function show(Category $category): Factory|View|Application
    {
        return view(view: 'catalog.category.category-show', data: [
            'category' => $category,
        ]);
    }

    public function edit(Request $request, Category $category): Factory|View|Application
    {
        return view(view: 'catalog.category.category-edit', data: [
            'category' => $category,
            'action' => $request->get('action'),
        ]);
    }

    public function update(CategoryUpdateRequest $request): RedirectResponse
    {
        $category = $this->categoryService->findByUuid(uuid: $request->get('uuid'));

        $this->categoryService->store(
            data: $request->validated(),
            category: $category,
        );

        return $this->saveAndAction(
            action: $request->get('action'),
            category: $category,
        );
    }

    public function delete(CategoryDeleteRequest $request): RedirectResponse
    {
        $this->categoryService->delete($request->get('uuid'));

        return Redirect::route('catalog.categories.list');
    }

    private function saveAndAction(?string $action, Category $category): RedirectResponse
    {
        if ($action) {
            $action = SaveAndAction::tryFromName($action);
        }

        return match ($action) {
            SaveAndAction::CREATE_AGAIN => Redirect::route('catalog.categories.create'),
            SaveAndAction::TO_OVERVIEW => Redirect::route('catalog.categories.list'),
            SaveAndAction::TO_MODEL => Redirect::route('catalog.categories.show', $category),
            default => null
        } ?? Redirect::route('catalog.categories.show', $category); // Redirect fallback
    }
}
