# WordPress REST API CORS

A lightweight plugin to enable and manage Cross-Origin Resource Sharing (CORS) for your WordPress REST API endpoints. Easily configure allowed HTTP methods (POST, GET, OPTIONS, PUT, DELETE) and origins for your API requests.

## Features

- Easy-to-use admin interface
- Support for all major HTTP methods (POST, GET, OPTIONS, PUT, DELETE)
- Customizable origin settings
- Support for credentials
- Header exposure control
- Compatible with WooCommerce REST API
- Minimal performance impact

## Installation

1. Download the plugin package using the **Download ZIP** button
2. Log in to your WordPress admin panel
3. Navigate to **Plugins** → **Add New**
4. Click the **Upload Plugin** button at the top
5. Choose the downloaded ZIP file and click **Install Now**
6. After installation, click **Activate Plugin**

## Configuration

1. Go to **Settings** → **WP-REST-API Options** in your WordPress admin panel
2. Configure the following options:
   - Enable/Disable CORS
   - Select allowed HTTP methods
   - Set allowed origins (`*` for all, specific domain, or `null`)
   - Enable/disable credentials
   - Control header exposure
3. Click **Save Values** to apply your settings

## Options Explained

- **Enable/Disable CORS**: Master switch for CORS functionality
- **HTTP Methods**: Select which methods to allow (POST, GET, OPTIONS, PUT, DELETE)
- **Origin**: Control which domains can access your API
  - `*` - Allow all origins
  - `specific domain` - e.g., `https://example.com`
  - `null` - Restrict to same origin
- **Allow Credentials**: Enable if your API requires authentication
- **HEAD**: Enable to expose Link headers

## Security Considerations

- Only enable the HTTP methods you actually need
- Avoid using `*` for origins in production unless absolutely necessary
- Enable credentials only if required for authentication
- Regularly review your CORS settings

## Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher

## Support

For bug reports and feature requests, please use the [GitHub issues page](https://github.com/yourusername/wp-restapi-cors/issues).

## Documentation

For more information about CORS, visit the [Mozilla Developer Network](https://developer.mozilla.org/docs/Web/HTTP/CORS).

## License

This plugin is licensed under the GPL v2 or later.

## Changelog

### 2.0.0
- Initial public release
- Added support for all major HTTP methods
- Introduced admin interface
- Added origin control
- Added credentials support

## Credits

Developed by Tobias Lorsbach  
Website: [https://tobeworks.de](https://tobeworks.de)