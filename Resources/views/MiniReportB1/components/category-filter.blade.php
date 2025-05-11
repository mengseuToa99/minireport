{{-- Category Filter Component --}}
{{-- 
Usage: 
@include('minireportb1::MiniReportB1.components.category-filter', [
    'categories' => $categories,
    'products_by_category' => $products_by_category,
    'buttonId' => 'categoriesFilterBtn',
    'modalId' => 'categoriesFilterModal',
    'buttonText' => 'Categories'
])
--}}

<div class="filter-container">
    <button class="filter-button" id="{{ $buttonId ?? 'categoriesFilterBtn' }}" data-toggle="modal" data-target="#{{ $modalId ?? 'categoriesFilterModal' }}">
        <span>{{ $buttonText ?? 'ជ្រើសរើសក្រុម' }}</span>
        <span class="badge" id="selectedCategoriesCount">0</span>
        <span class="arrow">▼</span>
    </button>

    {{-- Categories Filter Modal --}}
    <div class="modal fade" id="{{ $modalId ?? 'categoriesFilterModal' }}" tabindex="-1" role="dialog" aria-labelledby="{{ $modalId ?? 'categoriesFilterModal' }}Label" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="{{ $modalId ?? 'categoriesFilterModal' }}Label">{{ $modalTitle ?? 'ជ្រើសរើសក្រុម' }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="filter-actions mb-3">
                        <button class="filter-action select-all-btn" id="selectAllCategories">ជ្រើសរើសទាំងអស់</button>
                        <button class="filter-action unselect-all-btn" id="unselectAllCategories">លុបការជ្រើសរើសទាំងអស់</button>
                    </div>
                    
                    <div class="categories-list" id="categoriesList">
                        {{-- Debug info --}}
                        <div class="debug-info" style="margin-bottom: 10px; padding: 8px; background: #f8f9fa; border-radius: 4px; font-size: 12px;">
                            <div>Categories: {{ count($categories) }}</div>
                            <div>Category groups: {{ count($products_by_category) }}</div>
                            <div>Category IDs: {{ implode(', ', array_keys($products_by_category)) }}</div>
                        </div>
                        
                        {{-- Show categories and their products --}}
                        @if(count($products_by_category) > 0)
                            @foreach($products_by_category as $category_id => $category_data)
                                @php
                                    $category_name = $categories[$category_id] ?? $category_data['name'] ?? ($category_id === 'no_category' ? 'គ្មានក្រុម' : 'មិនបានចាត់ក្រុម');
                                    $children = $category_data['products'] ?? [];
                                @endphp
                                
                                {{-- Parent Category --}}
                                <div class="category-item category-parent">
                                    <label class="checkbox-wrapper">
                                        <input type="checkbox" class="checkbox-input parent-checkbox" data-id="{{ $category_id }}">
                                        <span class="checkbox-custom"></span>
                                        <span class="category-name">{{ $category_name }}</span>
                                        <span class="product-count">({{ count($children) }})</span>
                                    </label>
                                    @if(count($children) > 0)
                                        <span class="toggle-icon" data-target="child-{{ $category_id }}">▶</span>
                                    @endif
                                </div>
                                
                                {{-- Child Products --}}
                                @if(count($children) > 0)
                                    <div class="child-categories" id="child-{{ $category_id }}" style="display: none;">
                                        @foreach($children as $product)
                                            <div class="category-item category-child">
                                                <label class="checkbox-wrapper">
                                                    <input type="checkbox" class="checkbox-input child-checkbox" 
                                                        data-parent="{{ $category_id }}" 
                                                        data-id="{{ $product['product_id'] }}"
                                                        value="{{ $product['product_name'] }}">
                                                    <span class="checkbox-custom"></span>
                                                    {{ $product['product_name'] }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <div class="alert alert-info">
                                No categories found. Please try adjusting your filters.
                            </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="w-100 d-flex justify-content-between">
                        <span class="selected-summary">0 ក្រុម និង 0 ផលិតផលត្រូវបានជ្រើសរើស</span>
                        <div>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">បិទ</button>
                            <button type="button" class="btn btn-primary" id="applyCategoryFilter">
                                <i class="fas fa-filter mr-1"></i> អនុវត្តតម្រង
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Include the CSS --}}
<style>
    /* Category Filter Button Styles */
    .filter-container {
        position: relative;
        min-width: 200px;
    }
    
    .filter-button {
        width: 100%;
        padding: 8px 16px;
        background-color: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease;
    }
    
    .filter-button:hover {
        background-color: #f9f9f9;
        border-color: #d0d0d0;
    }
    
    .filter-button .arrow {
        transition: transform 0.3s ease;
    }
    
    .filter-button .badge {
        background-color: #3d87ff;
        color: white;
        border-radius: 10px;
        padding: 1px 8px;
        font-size: 12px;
        margin-left: 6px;
        display: none;
    }
    
    /* Filter Actions */
    .filter-actions {
        display: flex;
        justify-content: space-between;
        padding: 10px 16px;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .filter-action {
        border: none;
        background: none;
        color: #3d87ff;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 4px;
        transition: all 0.2s ease;
    }
    
    .filter-action:hover {
        background-color: #f0f7ff;
    }
    
    /* Category List Styles */
    .categories-list {
        max-height: 350px;
        overflow-y: auto;
    }
    
    .category-item {
        padding: 8px 16px 8px 16px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
    }
    
    .category-item:hover {
        background-color: #f5f5f5;
    }
    
    .category-parent {
        font-weight: 500;
    }
    
    .category-child {
        padding-left: 34px;
        font-size: 13px;
    }
    
    .product-count {
        font-size: 12px;
        color: #6c757d;
        margin-left: 5px;
    }
    
    /* Checkbox Styles */
    .checkbox-wrapper {
        display: flex;
        align-items: center;
        position: relative;
        width: 100%;
    }
    
    .checkbox-input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }
    
    .checkbox-custom {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 1px solid #d0d0d0;
        border-radius: 3px;
        margin-right: 10px;
        position: relative;
        transition: all 0.2s ease;
    }
    
    .checkbox-input:checked ~ .checkbox-custom {
        background-color: #3d87ff;
        border-color: #3d87ff;
    }
    
    .checkbox-input:checked ~ .checkbox-custom:after {
        content: '';
        position: absolute;
        left: 5px;
        top: 2px;
        width: 4px;
        height: 8px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }
    
    .checkbox-input:indeterminate ~ .checkbox-custom {
        background-color: #e0e0e0;
        border-color: #d0d0d0;
    }
    
    .checkbox-input:indeterminate ~ .checkbox-custom:after {
        content: '';
        position: absolute;
        left: 3px;
        top: 7px;
        width: 10px;
        height: 2px;
        background-color: #fff;
    }
    
    /* Toggle Icon */
    .toggle-icon {
        margin-left: auto;
        color: #909090;
        font-size: 16px;
        transition: transform 0.3s ease;
        margin-left: 10px;
        user-select: none;
        cursor: pointer;
    }
    
    .toggle-icon.expanded {
        transform: rotate(90deg);
    }
    
    /* Child Categories */
    .child-categories {
        display: none;
    }
    
    .child-categories.visible {
        display: block;
    }
    
    /* Selection Info */
    .selected-summary {
        font-size: 13px;
        color: #6c757d;
    }
</style>

<script>
// Make toggle icons clickable
document.addEventListener('DOMContentLoaded', function() {
    const toggleIcons = document.querySelectorAll('.toggle-icon');
    console.log('Toggle icons found:', toggleIcons.length);
    
    toggleIcons.forEach(icon => {
        icon.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const childContainer = document.getElementById(targetId);
            
            console.log('Toggle clicked for', targetId);
            
            if (childContainer) {
                const isVisible = childContainer.style.display === 'block';
                childContainer.style.display = isVisible ? 'none' : 'block';
                this.textContent = isVisible ? '▶' : '▼';
                this.classList.toggle('expanded', !isVisible);
                console.log('Child container visibility toggled:', !isVisible);
            } else {
                console.error('Child container not found for ID:', targetId);
            }
        });
    });
});
</script> 