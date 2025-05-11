/**
 * Category Filter Component
 * 
 * A reusable component for filtering data by categories and their children
 * 
 * @version 1.0.0
 */

class CategoryFilter {
    /**
     * Initialize a new CategoryFilter instance
     * 
     * @param {Object} options - Configuration options
     * @param {string} options.listSelector - Selector for the categories list container
     * @param {string} options.tableSelector - Selector for the data table to filter
     * @param {string} options.applyButtonSelector - Selector for the apply filter button
     * @param {string} options.selectAllSelector - Selector for the select all button
     * @param {string} options.unselectAllSelector - Selector for the unselect all button
     * @param {string} options.summarySelector - Selector for the selection summary element
     * @param {string} options.countBadgeSelector - Selector for the count badge element
     * @param {string} options.modalSelector - Selector for the modal element (optional)
     * @param {Function} options.onApplyFilter - Callback when filter is applied (optional)
     */
    constructor(options) {
        this.options = options || {};
        
        // DOM Elements
        this.categoriesList = document.querySelector(this.options.listSelector || '#categoriesList');
        this.dataTable = document.querySelector(this.options.tableSelector || '#product-sales-table');
        this.applyButton = document.querySelector(this.options.applyButtonSelector || '#applyCategoryFilter');
        this.selectAllButton = document.querySelector(this.options.selectAllSelector || '#selectAllCategories');
        this.unselectAllButton = document.querySelector(this.options.unselectAllSelector || '#unselectAllCategories');
        this.summaryElement = document.querySelector(this.options.summarySelector || '.selected-summary');
        this.countBadge = document.querySelector(this.options.countBadgeSelector || '#selectedCategoriesCount');
        
        // Store the modal element if selector provided
        this.modal = this.options.modalSelector ? document.querySelector(this.options.modalSelector) : null;
        
        // State
        this.selectedCategories = new Map(); // Map of selected items (id -> {id, name, isParent})
        this.filtersApplied = false;
        
        // Initialize
        this.init();
    }
    
    /**
     * Initialize the component
     */
    init() {
        // Check if all required elements exist
        if (!this.categoriesList) {
            console.error('CategoryFilter: Categories list element not found with selector:', this.options.listSelector || '#categoriesList');
            return;
        }
        
        this.setupEventListeners();
        this.updateSelectionCounter();
    }
    
    /**
     * Set up event listeners
     */
    setupEventListeners() {
        // Parent checkboxes
        const parentCheckboxes = this.categoriesList ? this.categoriesList.querySelectorAll('.parent-checkbox') : [];
        parentCheckboxes.forEach(checkbox => {
            if (checkbox) {
                checkbox.addEventListener('change', (e) => {
                    const id = e.target.getAttribute('data-id');
                    const isChecked = e.target.checked;
                    
                    // Find parent item
                    const parentItem = e.target.closest('.category-item');
                    const parentName = parentItem ? parentItem.textContent.trim() : '';
                    
                    // Update selected categories map
                    if (isChecked) {
                        this.selectedCategories.set(id, {
                            id,
                            name: parentName,
                            isParent: true
                        });
                    } else {
                        this.selectedCategories.delete(id);
                    }
                    
                    // Find and update all child checkboxes
                    const childContainer = parentItem ? parentItem.nextElementSibling : null;
                    if (childContainer && childContainer.classList.contains('child-categories')) {
                        const childCheckboxes = childContainer.querySelectorAll('.child-checkbox');
                        childCheckboxes.forEach(childCheckbox => {
                            childCheckbox.checked = isChecked;
                            
                            const childId = childCheckbox.getAttribute('data-id');
                            const childName = childCheckbox.value;
                            
                            if (isChecked) {
                                this.selectedCategories.set(childId, {
                                    id: childId,
                                    name: childName,
                                    isParent: false,
                                    parentId: id
                                });
                            } else {
                                this.selectedCategories.delete(childId);
                            }
                        });
                    }
                    
                    this.updateSelectionCounter();
                });
            }
        });
        
        // Child checkboxes
        const childCheckboxes = this.categoriesList ? this.categoriesList.querySelectorAll('.child-checkbox') : [];
        childCheckboxes.forEach(checkbox => {
            if (checkbox) {
                checkbox.addEventListener('change', (e) => {
                    const id = e.target.getAttribute('data-id');
                    const parentId = e.target.getAttribute('data-parent');
                    const isChecked = e.target.checked;
                    const name = e.target.value;
                    
                    // Update selected categories map
                    if (isChecked) {
                        this.selectedCategories.set(id, {
                            id,
                            name,
                            isParent: false,
                            parentId
                        });
                    } else {
                        this.selectedCategories.delete(id);
                    }
                    
                    // Update parent checkbox status
                    if (parentId) {
                        this.updateParentCheckbox(parentId);
                    }
                    
                    this.updateSelectionCounter();
                });
            }
        });
        
        // Category toggle icons
        const toggleIcons = this.categoriesList ? this.categoriesList.querySelectorAll('.toggle-icon') : [];

        toggleIcons.forEach(icon => {
            if (icon) {
                icon.addEventListener('click', (e) => {
                    const targetId = e.target.getAttribute('data-target');
                    let childCategoriesContainer = null;
                    
                    // First try to find by ID
                    if (targetId) {
                        childCategoriesContainer = document.getElementById(targetId);
                    }
                    
                    // If not found by ID, fall back to sibling element lookup
                    if (!childCategoriesContainer) {
                        const parentItem = e.target.closest('.category-parent');
                        childCategoriesContainer = parentItem ? parentItem.nextElementSibling : null;
                    }
                    
                    if (childCategoriesContainer) {
                        // Toggle visibility
                        const isVisible = childCategoriesContainer.style.display === 'block';
                        childCategoriesContainer.style.display = isVisible ? 'none' : 'block';
                        e.target.textContent = isVisible ? '▶' : '▼';
                        e.target.classList.toggle('expanded', !isVisible);
                        console.log('Set display to:', childCategoriesContainer.style.display);
                    } else {
                    
                    }
                });
            }
        });
        
        // Apply filter button
        if (this.applyButton) {
            this.applyButton.addEventListener('click', () => {
                this.applyFilter();
                
                // Close modal if exists
                if (this.modal && typeof $(this.modal).modal === 'function') {
                    $(this.modal).modal('hide');
                }
            });
        }
        
        // Select all button
        if (this.selectAllButton) {
            this.selectAllButton.addEventListener('click', () => {
                const allCheckboxes = this.categoriesList ? this.categoriesList.querySelectorAll('.checkbox-input') : [];
                allCheckboxes.forEach(checkbox => {
                    checkbox.checked = true;
                    
                    // Trigger change event manually
                    const changeEvent = new Event('change', { bubbles: true });
                    checkbox.dispatchEvent(changeEvent);
                });
            });
        }
        
        // Unselect all button
        if (this.unselectAllButton) {
            this.unselectAllButton.addEventListener('click', () => {
                const allCheckboxes = this.categoriesList ? this.categoriesList.querySelectorAll('.checkbox-input') : [];
                allCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                    
                    // Trigger change event manually
                    const changeEvent = new Event('change', { bubbles: true });
                    checkbox.dispatchEvent(changeEvent);
                });
                
                // Clear the map directly
                this.selectedCategories.clear();
                this.updateSelectionCounter();
            });
        }
    }
    
    /**
     * Update the parent checkbox based on child selections
     * @param {string} parentId 
     */
    updateParentCheckbox(parentId) {
        if (!this.categoriesList) return;
        
        const parentCheckbox = this.categoriesList.querySelector(`.parent-checkbox[data-id="${parentId}"]`);
        if (!parentCheckbox) return;
        
        const childContainer = parentCheckbox.closest('.category-item').nextElementSibling;
        if (!childContainer || !childContainer.classList.contains('child-categories')) return;
        
        const childCheckboxes = childContainer.querySelectorAll('.child-checkbox');
        const totalChildren = childCheckboxes.length;
        const checkedChildren = Array.from(childCheckboxes).filter(checkbox => checkbox.checked).length;
        
        // Determine checkbox state
        if (checkedChildren === 0) {
            parentCheckbox.checked = false;
            parentCheckbox.indeterminate = false;
            this.selectedCategories.delete(parentId);
        } else if (checkedChildren === totalChildren) {
            parentCheckbox.checked = true;
            parentCheckbox.indeterminate = false;
            
            // Add parent to selected map
            const parentItem = parentCheckbox.closest('.category-item');
            const parentName = parentItem ? parentItem.textContent.trim() : '';
            
            this.selectedCategories.set(parentId, {
                id: parentId,
                name: parentName,
                isParent: true
            });
        } else {
            parentCheckbox.checked = false;
            parentCheckbox.indeterminate = true;
            this.selectedCategories.delete(parentId);
        }
    }
    
    /**
     * Update the selection counter and badge
     */
    updateSelectionCounter() {
        const selectedParents = Array.from(this.selectedCategories.values())
            .filter(item => item.isParent)
            .length;
            
        const selectedChildren = Array.from(this.selectedCategories.values())
            .filter(item => !item.isParent)
            .length;
        
        // Update the summary text
        if (this.summaryElement) {
            this.summaryElement.textContent = `${selectedParents} ក្រុម និង ${selectedChildren} ផលិតផលត្រូវបានជ្រើសរើស`;
        }
        
        // Update the badge count
        const totalCount = this.selectedCategories.size;
        if (this.countBadge) {
            this.countBadge.textContent = totalCount;
            this.countBadge.style.display = totalCount > 0 ? 'inline-block' : 'none';
        }
    }
    
    /**
     * Apply the filter to the data table
     */
    applyFilter() {
        if (!this.dataTable) return;
        
        const selectedCategoryIds = Array.from(this.selectedCategories.values())
            .filter(item => item.isParent)
            .map(item => item.id);
            
        const selectedProductIds = Array.from(this.selectedCategories.values())
            .filter(item => !item.isParent)
            .map(item => item.id);
            
        const rows = this.dataTable.querySelectorAll('tbody tr');
        
        // If nothing is selected, show all rows
        if (selectedCategoryIds.length === 0 && selectedProductIds.length === 0) {
            rows.forEach(row => {
                row.style.display = '';
            });
            return;
        }
        
        rows.forEach(row => {
            const categoryId = row.getAttribute('data-category-id');
            const productId = row.getAttribute('data-product-id');
            const productName = row.getAttribute('data-product-name');
            
            // Check if this row's category is selected
            const categorySelected = selectedCategoryIds.includes(categoryId);
            
            // Check if this product is individually selected
            const productSelected = selectedProductIds.includes(productId) || 
                                    Array.from(this.selectedCategories.values())
                                        .filter(item => !item.isParent)
                                        .some(item => item.name === productName);
            
            // Show if category is selected or if product is individually selected
            if (categorySelected || productSelected) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
        
        this.filtersApplied = true;
    }
}

// For jQuery compatibility
(function($) {
    $.fn.categoryFilter = function(options) {
        try {
            return this.each(function() {
                try {
                    const $this = $(this);
                    const instanceOptions = $.extend({}, options, {
                        listSelector: options.listSelector || '#categoriesList',
                        tableSelector: options.tableSelector || '#product-sales-table',
                        modalSelector: options.modalSelector || null
                    });
                    
                    if (!$this.data('categoryFilter')) {
                        $this.data('categoryFilter', new CategoryFilter(instanceOptions));
                    }
                    
                    return $this.data('categoryFilter');
                } catch (err) {
                    console.error('Error initializing categoryFilter for element:', this, err);
                }
            });
        } catch (err) {
            console.error('Error in categoryFilter plugin:', err);
            return this; // Return the jQuery object to maintain chainability
        }
    };
})(jQuery); 