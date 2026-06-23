# Maison AI — CodeIgniter 3 Virtual Try-On

A complete virtual fitting-room module built with CodeIgniter 3, MySQL, Bootstrap 5, vanilla JavaScript, MediaPipe Pose, Canvas, and the Webcam API.

## Included

- Responsive premium-fashion storefront and product collection
- Live webcam preview with loading, permission, and unsupported-browser states
- MediaPipe Pose tracking for face, shoulders, arms, hips, knees, and ankles
- Real-time transparent garment overlay fitted from shoulder/hip landmarks
- Optional landmark skeleton, camera start/stop, reset, fullscreen, and garment switching
- PNG capture, browser download, Web Share support, and authenticated server save
- Login, registration, session security, favorites, recently tried products, and size recommendation
- Admin dashboard, product CRUD, validated image/PNG uploads, history, and statistics
- JSON APIs with access controls, CSRF validation, Query Builder parameterization, and PNG signature/size checks

## Requirements

- PHP 7.4+ recommended (PHP 8.x supported by this CI3 distribution)
- MySQL 5.7+ or MariaDB 10.3+
- Apache with `mod_rewrite`, or another web server configured to route requests to `index.php`
- HTTPS in production. Browsers permit webcam access only on HTTPS or localhost.
- Internet access for Bootstrap, Google Fonts, and MediaPipe CDN assets

## Installation

1. Put the project in your web root, for example `htdocs/check_virtual_try-on_E-comm_app`.
2. Import [database/virtual_tryon.sql](database/virtual_tryon.sql) into MySQL.
3. Configure database credentials with environment variables or edit `application/config/database.php`:

   ```text
   DB_DATABASE=virtual_tryon
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. Set the public URL and a long random application key:

   ```text
   APP_URL=http://localhost/check_virtual_try-on_E-comm_app/
   APP_KEY=replace-with-a-long-random-secret
   ```

5. Ensure these directories are writable by the web-server process:

   ```text
   assets/uploads/products
   assets/uploads/virtual_tryon
   assets/uploads/screenshots
   application/logs
   ```

6. On Apache, allow `.htaccess` overrides and enable `mod_rewrite`. If rewriting is unavailable, restore `$config['index_page'] = 'index.php'` and include `index.php` in URLs.
7. Open `/try-on`. The seed admin account is `admin@maison.local` / `Admin@123`. Change this password for any real deployment.

## Routes and APIs

| Method | Path | Purpose |
|---|---|---|
| GET | `/try-on` | Virtual fitting room |
| GET | `/products` | Product collection |
| GET | `/api/products` | Active products |
| GET | `/api/product/{id}` | Product detail |
| POST | `/api/save-tryon` | Save authenticated user's PNG |
| GET | `/api/tryon-history/{user_id}` | Own history, or any history for admin |
| POST | `/api/size-recommendation` | Measurement-based size estimate |
| GET | `/admin` | Admin dashboard |

Every POST request is CSRF protected. Browser JavaScript reads the token from page meta tags; external clients must send the current `atelier_csrf` token and cookie together.

## Garment asset guidelines

Upload a transparent, front-facing PNG with generous padding and no model/mannequin. The overlay anchors near landmarks 11/12 (shoulders) and uses landmarks 23/24 (hips) to estimate torso scale. Straight, centered garments give the most stable fit. Starter assets were generated specifically for this demo and converted to alpha PNGs.

## Privacy and production notes

MediaPipe and Canvas process camera frames in the browser. Frames are not posted to the server. Only an explicit **Save look** action sends the captured PNG. Add a retention policy, user-facing deletion controls, CSP headers, CDN self-hosting where required, rate limiting, object storage, and image malware scanning before a high-traffic deployment.

The size recommendation is a deterministic fit estimate, not a trained medical/body-analysis model. Brand-specific garment measurements can replace the thresholds in `Api::size_recommendation()`.

## Main files

- `application/controllers/VirtualTryOn.php`, `Products.php`, `Api.php`, `Auth.php`, `Admin.php`
- `application/models/Product_model.php`, `Tryon_model.php`, `User_model.php`
- `application/views/virtual_tryon.php`, `product_list.php`, `admin/*`, `auth/form.php`
- `assets/js/virtual-tryon.js`
- `assets/css/app.css`
- `database/virtual_tryon.sql`
