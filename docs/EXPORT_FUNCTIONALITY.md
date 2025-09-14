# Export Functionality

The Storage Unit Management System now includes comprehensive export functionality that allows users to download their data in CSV format.

## Accessing Export Options

### Main Export Page
- Navigate to the **Export** link in the main navigation menu
- This will take you to `/index.php?script=export`
- The export page provides an overview of all available export options

### Export Options Available

#### 1. All Items Export
- **Location**: Main export page → "Export All Items (CSV)" button
- **URL**: `/export/items.php`
- **Description**: Exports all items with complete details including categories, locations, and metadata
- **Includes**: Item details, descriptions, category and location information, quantities, timestamps, and image references

#### 2. Categories Export
- **Location**: Main export page → "Export Categories (CSV)" button
- **URL**: `/export/categories.php`
- **Description**: Exports your category structure with item counts and metadata
- **Includes**: Category names, colors, icons, item counts per category, and timestamps

#### 3. Locations Export
- **Location**: Main export page → "Export Locations (CSV)" button
- **URL**: `/export/locations.php`
- **Description**: Exports your location hierarchy with item counts and full paths
- **Includes**: Location hierarchy, full location paths, item counts per location, and timestamps

#### 4. Search Results Export
- **Location**: Search page → "Export Results (CSV)" button
- **URL**: `/export/search.php?q=search_term`
- **Description**: Exports filtered search results with custom criteria
- **Includes**: Custom search terms, category and location filters, and filtered results only

#### 5. Category-Specific Export
- **Location**: Main export page → Individual category cards
- **URL**: `/export/category.php?id=category_id`
- **Description**: Exports items filtered by specific category
- **Includes**: All items belonging to the selected category

#### 6. Location-Specific Export
- **Location**: Main export page → Individual location cards
- **URL**: `/export/location.php?id=location_id`
- **Description**: Exports items filtered by specific location
- **Includes**: All items stored in the selected location

## Export File Details

### File Format
- **Format**: CSV (Comma-Separated Values)
- **Encoding**: UTF-8 with BOM for Excel compatibility
- **Delimiter**: Comma (,)
- **Quote Character**: Double quote (")

### File Naming Convention
- **All Items**: `storage_items_YYYY-MM-DD_HH-MM-SS.csv`
- **Categories**: `storage_categories_YYYY-MM-DD_HH-MM-SS.csv`
- **Locations**: `storage_locations_YYYY-MM-DD_HH-MM-SS.csv`
- **Search Results**: `storage_items_search_[term]_YYYY-MM-DD_HH-MM-SS.csv`
- **Category Filtered**: `storage_items_category_[category_name]_YYYY-MM-DD_HH-MM-SS.csv`
- **Location Filtered**: `storage_items_location_[location_name]_YYYY-MM-DD_HH-MM-SS.csv`

### Data Security
- Only authenticated users can access export functionality
- Users can only export their own data
- No shared or system data is included in exports
- All exports are user-specific and private

## Technical Implementation

### Controllers
- **ExportController**: Main controller handling all export operations
- **Location**: `app/Controllers/ExportController.php`

### Public Endpoints
- **Location**: `public/export/`
- **Files**:
  - `items.php` - All items export
  - `categories.php` - Categories export
  - `locations.php` - Locations export
  - `search.php` - Search results export
  - `category.php` - Category-specific export
  - `location.php` - Location-specific export

### Views
- **Export Page**: `resources/views/export/export.php`
- **Route**: Added to `routes/routes.php` as 'export' case

### Navigation
- **Export Link**: Added to main navigation in `resources/views/header.php`
- **Search Export**: Added export button to search results page

## Usage Examples

### Export All Data
1. Click "Export" in the main navigation
2. Click "Export All Items (CSV)" button
3. File will download automatically

### Export Specific Category
1. Click "Export" in the main navigation
2. Scroll to "Export by Category" section
3. Click "Export" button on desired category card
4. File will download automatically

### Export Search Results
1. Perform a search using the search form
2. On the search results page, click "Export Results (CSV)" button
3. File will download automatically

## Troubleshooting

### Common Issues
1. **Export fails**: Check if user is logged in
2. **Empty export**: Verify user has data to export
3. **File not downloading**: Check browser download settings
4. **Encoding issues**: Ensure CSV is opened with UTF-8 encoding

### Error Handling
- All export endpoints include proper error handling
- Errors are logged to the system log
- User-friendly error messages are displayed
- HTTP status codes are properly set for different error conditions

## Future Enhancements

Potential future improvements to the export functionality:
- Additional export formats (JSON, XML, Excel)
- Scheduled exports
- Email delivery of exports
- Custom field selection for exports
- Export templates and presets
- Bulk export operations
