# BPA Logo Setup Instructions

## To complete the logo setup:

1. **Save the BPA logo image** that you provided as `bpa-logo.png`

2. **Copy the logo file** to the following location in your project:
   ```
   public/images/bpa-logo.png
   ```

3. **Create the images directory** if it doesn't exist:
   ```bash
   mkdir public/images
   ```

4. **Copy your logo file** to the directory:
   ```bash
   cp /path/to/your/bpa-logo.png public/images/bpa-logo.png
   ```

## Alternative: Base64 Encoding

If you prefer to embed the logo directly in the code, you can:

1. Convert your logo to base64
2. Replace the `src="{{ asset('images/bpa-logo.png') }}"` in the login view with:
   ```html
   src="data:image/png;base64,YOUR_BASE64_STRING_HERE"
   ```

## Verification

After copying the logo, you should see it displayed on:
- Login page (large logo with company name)
- Admin panel sidebar (small logo)
- Browser favicon

The logo should appear as a circular emblem with "BPA" text and "BROADCASTING AND PUBLICATIONS AUTHORITY" around the border.
