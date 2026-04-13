<?php

declare(strict_types=1);

namespace App\Livewire\Categories;

use App\Models\Category;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Categorías')]
class Index extends Component
{
    public string $name = '';
    public string $description = '';
    public string $color = '#6b7280';

    public ?int $editingId = null;
    public string $editName = '';
    public string $editDescription = '';
    public string $editColor = '#6b7280';

    public ?string $flashMessage = null;
    public ?string $flashVariant = null;

    public function mount(): void
    {
        abort_unless(auth()->user()->hasRole('Administrador'), 403);
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'min:2', 'max:50', 'unique:categories,name'],
            'description' => ['nullable', 'string', 'max:255'],
            'color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        Category::create($validated);

        $this->reset('name', 'description');
        $this->color = '#6b7280';
        $this->flashVariant = 'success';
        $this->flashMessage = "Categoría «{$validated['name']}» creada.";
    }

    public function startEdit(int $id): void
    {
        $category = Category::findOrFail($id);
        $this->editingId = $category->id;
        $this->editName = $category->name;
        $this->editDescription = $category->description ?? '';
        $this->editColor = $category->color;
    }

    public function cancelEdit(): void
    {
        $this->reset('editingId', 'editName', 'editDescription', 'editColor');
    }

    public function update(): void
    {
        $category = Category::findOrFail($this->editingId);

        $validated = $this->validate([
            'editName' => ['required', 'string', 'min:2', 'max:50', 'unique:categories,name,'.$category->id],
            'editDescription' => ['nullable', 'string', 'max:255'],
            'editColor' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        $category->update([
            'name' => $validated['editName'],
            'description' => $validated['editDescription'],
            'color' => $validated['editColor'],
        ]);

        $this->cancelEdit();
        $this->flashVariant = 'success';
        $this->flashMessage = 'Categoría actualizada.';
    }

    public function delete(int $id): void
    {
        $category = Category::findOrFail($id);
        $name = $category->name;
        $category->delete();

        $this->flashVariant = 'success';
        $this->flashMessage = "Categoría «{$name}» eliminada. Los productos asociados quedan sin categoría.";
    }

    public function render(): View
    {
        return view('livewire.categories.index', [
            'categories' => Category::withCount('products')->orderBy('name')->get(),
        ]);
    }
}
