![GP Beaver Integration](https://weave-hk-github.b-cdn.net/weave/plugin-header.png)

# Weave Training Plugin

A simple WordPress plugin that adds a dedicated "Website Training" page to the WordPress admin area for our client specific training videos and notes. Bundled on every site by Weave Digital. Uses an iframe from our central training resources.

## Description

The Weave Website Training plugin provides easy access to customised training materials directly from the WordPress admin dashboard. It's designed for internal use by Weave Digital Studio and HumanKind Funeral Websites and deployed across client sites to provide seamless access to training content.

## Features

- **Seamless Full-Screen Display**: Training content takes up the entire admin area with no headers, borders, or distractions
- **Simple Integration**: Adds a "Website Training" menu item to WordPress admin
- **Security**: Iframe sandbox restrictions for safe content display
- **Minimal Configuration**: Uses WordPress constants for easy deployment
- **User Role Control**: Only accessible to users with Editor permissions or higher
- **Loading States**: Smooth loading indicators and error handling

## Installation

1. **Upload the Plugin**
   - Download or clone this repository
   - Upload the `weave-training` folder to your WordPress `/wp-content/plugins/` directory
   - Alternatively, install directly via WordPress admin if available

2. **Activate the Plugin**
   - Go to WordPress Admin → Plugins
   - Find "Weave Training" in the list
   - Click "Activate"

3. **Configure Training URL (Optional)**
   - Add the following constant to your `wp-config.php` file:
   ```php
   define('WEAVE_TRAINING_URL', 'https://your-training-site.com/client-specific-page');
   ```

## Configuration

### Setting Custom Training URL

To set a client-specific training URL, add the following line to your `wp-config.php` file:

```php
define('WEAVE_TRAINING_URL', 'https://your-training-site.com/your-client-name/');
```


### Default Behaviour

If `WEAVE_TRAINING_URL` is not defined, the plugin will display the default weave logo training portal at `https://training.weave.digital`.

### GitHub Updates (Public Repository)

The plugin includes automatic update checking from GitHub releases. 

**Features:**
- Automatic update checking from GitHub releases
- Caches update checks for 4 hours (performance optimisation)

The updater checks for new releases every 4 hours and will show update notifications in the WordPress admin when new versions are available.

## Usage

1. **Access Training**
   - Log into WordPress admin
   - Look for "Website Training" in the admin menu (appears after Dashboard)
   - Click to access your training content

2. **User Permissions**
   - Only users with `edit_posts` capability can access training (Editor role and above)
   - Contributors, Subscribers, and other lower-level users won't see the menu
   - Make sure clients have their account setup

3. **Mobile/Responsive**
   - Training content automatically adjusts to screen size
   - Optimised for both desktop and mobile viewing

## Technical Details

### System Requirements

- **WordPress**: 5.0 or higher
- **PHP**: 7.4 or higher
- **User Capability**: `edit_posts` or higher
- **Browser**: Modern browsers with iframe support

### File Structure

```
weave-training/
├── weave-training.php          # Main plugin file
├── includes/
│   └── github-updater.php      # GitHub update checker
├── assets/
│   ├── css/
│   │   └── weave-training-admin.css    # Admin styles
│   └── js/
│       └── weave-training-admin.js     # Admin JavaScript
├── README.md                   # This file
└── languages/                  # Translation files (if needed)
```

### Security Features

- **Iframe Sandbox**: Uses `allow-scripts allow-same-origin` restrictions
- **Capability Checks**: Validates user permissions on every access
- **URL Validation**: Sanitises and validates all training URLs
- **Direct Access Protection**: Prevents direct file access to plugin files

## Troubleshooting

### Common Issues

**Training page not appearing:**
- Check if user has Editor role or higher
- Verify plugin is activated
- Clear browser cache and reload admin

**Training content not loading:**
- Verify `WEAVE_TRAINING_URL` is set correctly in wp-config.php
- Check if training site is accessible from your server
- Review browser console for any error messages

**"Can't Open This Page" or CSP errors:**
- This is a Content Security Policy (CSP) restriction from the training site
- For **local development**, add your local domain to the training site's CSP `frame-ancestors` directive
- Common local domains to add: `localhost`, `*.local`, `*.test`, `customersite.url`
- For **production sites**, ensure the live domain is added to the training site's CSP
- Make sure you add your domain to the allowed frame ancestors

**Iframe display issues:**
- The plugin creates a seamless full-screen experience with no borders or headers
- If you see borders or headers, check if other plugins are interfering with admin styles
- Try disabling other admin-related plugins temporarily


## License

This plugin is licensed under the GPL v2 or later.

## Changelog

### Version 1.0.0
- Initial release
- Basic iframe functionality
- Responsive design implementation
- WordPress admin integration
- Security measures and capability checks
- Configuration via WordPress constants
- GitHub automatic update checker 
- Full-screen seamless iframe display with admin menu positioning
- Plugin icons and enhanced update UI
- Robust error handling and caching system
- Responsive design for mobile and desktop

---

**Developed by Weave Digital & Gareth Bissland** | [weave.co.nz](https://weave.co.nz) 
