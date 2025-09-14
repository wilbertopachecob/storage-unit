# Google Maps API Setup

This document explains how to set up Google Maps API for the profile page storage unit selection feature.

## Prerequisites

1. A Google Cloud Platform account
2. A Google Maps API key with the following APIs enabled:
   - Places API
   - Maps JavaScript API

## Setup Steps

### 1. Get Google Maps API Key

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Enable the following APIs:
   - Places API
   - Maps JavaScript API
4. Go to "Credentials" and create an API key
5. Restrict the API key to your domain for security

### 2. Configure the API Key

You have two options to configure the API key:

#### Option A: Environment Variable (Recommended)
Set the `GOOGLE_MAPS_API_KEY` environment variable:
```bash
export GOOGLE_MAPS_API_KEY="your_actual_api_key_here"
```

#### Option B: Direct Configuration
Edit `config/app/google_maps.php` and replace `YOUR_GOOGLE_MAPS_API_KEY` with your actual API key.

### 3. Test the Configuration

1. Navigate to the profile page: `/index.php?script=profile`
2. Try typing in the storage unit address field
3. You should see Google Maps autocomplete suggestions

## Security Notes

- Always restrict your API key to specific domains
- Monitor your API usage in the Google Cloud Console
- Consider setting up billing alerts to avoid unexpected charges

## Troubleshooting

### No Autocomplete Suggestions
- Check that the Places API is enabled
- Verify the API key is correct
- Check browser console for JavaScript errors

### API Key Not Working
- Ensure the API key has the correct permissions
- Check that the domain is allowed in the API key restrictions
- Verify the API key is not expired

## Cost Considerations

- Google Maps API has usage-based pricing
- Places API has specific pricing tiers
- Consider implementing usage limits for production use
