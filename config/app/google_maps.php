<?php
/**
 * Google Maps Configuration
 * Configuration for Google Maps API integration
 */

return [
    'api_key' => $_ENV['GOOGLE_MAPS_API_KEY'] ?? 'YOUR_GOOGLE_MAPS_API_KEY',
    'libraries' => ['places'],
    'callback' => 'initAutocomplete'
];
