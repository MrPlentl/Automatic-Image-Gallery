# PHP Image Gallery

A dark, editorial-style image gallery built with PHP, TypeScript, and Tailwind CSS v4.

---

## Requirements

- PHP 8.0+
- Node.js 18+
- A web server (Apache, Nginx, or PHP's built-in server)

---

## Setup

### 1. Install dependencies

```bash
npm install
```

### 2. Build frontend assets

```bash
npm run build
```

This compiles:
- `src/styles.css` → `dist/styles.css` (Tailwind CSS v4)
- `src/main.ts` → `dist/main.js` (TypeScript)

For development with live reloading:

```bash
npm run watch
```

### 3. Add your images

Organize images in the following structure:

```
/galleries
  /hero.png              ← Hero banner image (required)
  /{Topic}/
    /{tag}/
      image1.jpg         ← Thumbnail
      image2.jpg
      /full/
        image1.jpg       ← Full-resolution version
        image2.jpg
```

**Example:**
```
/galleries
  /hero.png
  /Eras/
    /stone-age/
      machine1.jpg
      machine2.jpg
      /full/
        machine1.jpg
        machine2.jpg
    /modern/
      ...
  /Nature/
    /forests/
      ...
```

> **Notes:**
> - `{Topic}` = first-level folders (shown in the dropdown)
> - `{tag}` = second-level folders (shown as filter buttons)
> - Images inside `/full/` subdirectories are the click-through full-resolution versions
> - If no `/full/` version exists, the thumbnail is used in the lightbox
> - Images are sorted by file modification date (most recent first)

---

## Running Locally

Using PHP's built-in server:

```bash
php -S localhost:8080
```

Then open `http://localhost:8080` in your browser.

---

## Structure

```
root/
├── index.php          # Main gallery page
├── about.php          # About page
├── scan.php           # Image directory scanner (included by index.php)
├── src/
│   ├── styles.css     # Tailwind CSS v4 source
│   └── main.ts        # TypeScript frontend
├── dist/
│   ├── styles.css     # Compiled CSS (generated)
│   └── main.js        # Compiled JS (generated)
├── galleries/            # Your image files go here
├── package.json
└── tsconfig.json
```

---

## Supported Image Formats

`jpg`, `jpeg`, `png`, `gif`, `webp`, `avif` (case-insensitive)

---

## Features

- 📁 Auto-scans `/galleries` directory on each page load
- 🏷️ Topic dropdown + tag filter buttons
- 🗓️ Images sorted by file modification date (newest first)
- 📄 48 images per page with pagination
- 🔍 Full-resolution lightbox with keyboard navigation (← →, Esc)
- 📱 Responsive grid (4 → 3 → 2 → 1 columns)
- ⚡ Lazy loading with skeleton placeholders
- 🎨 Dark editorial aesthetic

---

## Credits

Created by **ThaBamboozler**
