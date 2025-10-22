# Landing Page Structure Documentation

## 📁 File Structure

```
resources/views/
├── welcome.blade.php          # Main landing page (orchestrator)
└── landing/                   # Landing page components
    ├── hero.blade.php         # Hero section with CTA
    ├── about.blade.php        # About SISARAYA section
    ├── values.blade.php       # 4 core values (Kolaborasi, Inovasi, etc)
    ├── portfolio.blade.php    # 4 pillars (Teman Event, Musik, etc)
    ├── collaboration.blade.php # Collaboration opportunities
    └── contact.blade.php      # Contact information
```

## 🎯 Main File: `welcome.blade.php`

**Purpose**: Orchestrator file yang memanggil semua section components

**Contents**:
- `<!DOCTYPE html>` header
- `<head>` with meta tags, fonts, and styles
- Navigation bar (fixed, sticky)
- `@include()` directives for all sections
- Footer
- Closing `</body></html>`

**Key Styles**:
```css
.font-display      → Playfair Display for headings
.font-body         → Inter for body text
.gradient-hero     → Hero overlay gradient (dark → violet → blue → emerald)
.gradient-section  → Section background gradient
.pattern-dots      → Dotted pattern overlay
.text-shadow-strong → Strong text shadows for hero
.animate-float     → Floating animation (7s)
.animate-fade-in   → Fade in animation (1s)
```

## 📄 Component Files

### 1. `landing/hero.blade.php`
**Section ID**: N/A (first section, no anchor)
**Purpose**: Hero section with main headline and CTAs
**Key Elements**:
- Full-screen height (`min-h-screen`)
- Background image with gradient overlay
- Animated headline (floating effect)
- 2 CTA buttons (Kenali Kami + Login/Dashboard)
- Scroll-down indicator (bouncing arrow)

**Responsiveness**:
- Mobile: Single column, smaller text
- Tablet: Medium text sizes
- Desktop: Full large text (up to `text-8xl`)

---

### 2. `landing/about.blade.php`
**Section ID**: `#about`
**Purpose**: Introduce SISARAYA and core mission
**Key Elements**:
- Section badge "Siapa Kami"
- 2-column grid (text + image)
- 3 checkmark features:
  - Kolaborasi Lintas Disiplin
  - Pengembangan Karya & Portofolio
  - Jejaring Kreator Profesional
- Team image with decorative blur background

**Layout**:
- Mobile: Stacked (image first, text second)
- Desktop: 2 columns (text left, image right on md+)

---

### 3. `landing/values.blade.php`
**Section ID**: `#values`
**Purpose**: Display 4 core values of SISARAYA
**Key Elements**:
- Section badge "Filosofi Kami"
- 4 value cards:
  1. **Kolaborasi** (Violet) - People icon
  2. **Inovasi** (Blue) - Lightbulb icon
  3. **Profesionalisme** (Emerald) - Badge icon
  4. **Dampak** (Purple) - Globe icon

**Card Interactions**:
- Hover: Lift up (`-translate-y-2`)
- Shadow: Expands on hover
- Border-top: Colored accent (4px)

**Grid**:
- Mobile: 1 column
- Tablet: 2 columns (`sm:grid-cols-2`)
- Desktop: 4 columns (`lg:grid-cols-4`)

---

### 4. `landing/portfolio.blade.php`
**Section ID**: `#portfolio`
**Purpose**: Showcase 4 pillars of SISARAYA's work
**Key Elements**:
- Section badge "Portofolio"
- 4 pillar cards with hover effects:
  1. **Teman Event** (Violet-Blue gradient) - Calendar icon
  2. **Musik & Band** (Blue-Cyan gradient) - Music note icon
  3. **Kewirausahaan** (Emerald-Teal gradient) - Lightning icon
  4. **Media Kreatif** (Purple-Pink gradient) - Film icon

**Hover Effect**:
- Background: Full gradient overlay appears
- Icon: Changes color (white → colored)
- Text: All text turns white

**Grid**: Same as values section (1→2→4 columns)

---

### 5. `landing/collaboration.blade.php`
**Section ID**: `#collaboration`
**Purpose**: Collaboration opportunities for new members
**Key Elements**:
- Gradient background (violet → blue → emerald)
- Dotted pattern overlay
- Section badge "Bergabung dengan Kami"
- 4 opportunity cards:
  1. **Bertemu Profesional** - Networking
  2. **Kembangkan Portofolio** - Portfolio development
  3. **Workshop & Program** - Skill development
  4. **Proyek Berdampak** - Impactful projects
- CTA button (Login/Dashboard)

**Card Style**:
- Glass morphism effect (`backdrop-blur-md`)
- White overlay on hover
- Icons in white/20 rounded boxes

**Grid**: 2 columns on desktop

---

### 6. `landing/contact.blade.php`
**Section ID**: N/A (no anchor needed)
**Purpose**: Contact information and final CTA
**Key Elements**:
- Section badge "Kontak"
- Gradient contact card (violet → blue → emerald)
- Phone icon + number: **+62 813-5601-9609**
- Clickable `tel:` link

**Styling**:
- Centered content
- Max-width container (`max-w-3xl`)
- Large rounded card with shadow

---

## 🎨 Design System

### Colors (Tailwind)
- **Primary**: `violet-600`
- **Secondary**: `blue-600`
- **Accent**: `emerald-500`
- **Neutral**: `gray-50` to `gray-900`

### Gradients
```
from-violet-600 via-blue-600 to-emerald-500
from-violet-50 via-blue-50 to-emerald-50
from-violet-600 to-purple-600
from-blue-600 to-cyan-600
from-emerald-600 to-teal-600
from-purple-600 to-pink-600
```

### Spacing
- Section padding: `py-24` (96px top/bottom)
- Container: `max-w-7xl mx-auto px-4 sm:px-6 lg:px-8`
- Section header margin: `mb-16` (64px)

### Typography
- **Headings**: Playfair Display (font-display)
- **Body**: Inter (font-body)
- **Sizes**:
  - H1: `text-5xl sm:text-6xl lg:text-8xl`
  - H2: `text-4xl sm:text-5xl`
  - H3: `text-xl` to `text-2xl`
  - Body: `text-base` to `text-lg`

### Shadows
- Cards: `shadow-lg hover:shadow-2xl`
- Buttons: `shadow-2xl`
- Hero text: Custom `text-shadow-strong`

### Transitions
- Duration: `duration-200` to `duration-300`
- Easing: Default ease
- Properties: `transition-all`, `transition-colors`, `transition-opacity`

---

## 🔧 Maintenance Guide

### Adding a New Section
1. Create new file in `resources/views/landing/newsection.blade.php`
2. Add `@include('landing.newsection')` in `welcome.blade.php`
3. Follow existing structure patterns
4. Use consistent spacing (`py-24`, `mb-16`)
5. Add section ID if needs anchor navigation

### Modifying Existing Section
1. Edit only the specific component file
2. No need to touch `welcome.blade.php` unless changing order
3. Test responsiveness on mobile/tablet/desktop
4. Check hover states and animations

### Changing Colors
1. Update gradient definitions in `<style>` block in `welcome.blade.php`
2. Update Tailwind classes in components
3. Ensure contrast ratios for accessibility

### Adding New Navigation Link
1. Edit navigation bar in `welcome.blade.php`
2. Add anchor link (`href="#section-id"`)
3. Ensure target section has matching `id` attribute

---

## ✅ Testing Checklist

- [ ] All sections load without errors
- [ ] Navigation links scroll to correct sections
- [ ] Login/Dashboard buttons work correctly
- [ ] Phone number link opens dialer
- [ ] Images load (logo, background)
- [ ] Animations run smoothly
- [ ] Hover effects work on all cards
- [ ] Responsive on mobile (320px+)
- [ ] Responsive on tablet (768px+)
- [ ] Responsive on desktop (1024px+)
- [ ] No duplicate HTML tags
- [ ] No console errors
- [ ] Proper text contrast for accessibility

---

## 📊 Performance Optimization

**Current Setup**:
- ✅ Modular components (6 partials)
- ✅ Minimal CSS (inline styles)
- ✅ Font preconnect for Bunny Fonts
- ✅ Vite for asset bundling
- ✅ SVG icons (no icon library needed)

**Future Improvements** (optional):
- Lazy load images below fold
- Compress background image (Asset.jpg)
- Add webp format for images
- Implement CSS purging (Tailwind default)
- Add skeleton loaders for slow connections

---

## 🚀 Deployment Notes

**Assets to Check**:
- `public/logo-no-bg.png` - Logo image
- `public/Asset.jpg` - Hero background image
- `public/favicon.ico` - Browser favicon

**Environment**:
- Laravel 12.33+
- PHP 8.4+
- Vite 7.1+
- Tailwind 3.x+

**Build Command**:
```bash
npm run build
```

**Development**:
```bash
npm run dev
# or
composer run dev
```

---

## 📝 Change Log

### v1.0.0 - Modular Structure (Current)
- Split monolithic welcome.blade.php into 7 files
- Created `landing/` directory for components
- Separated concerns: navigation, sections, footer
- Easier to maintain and test
- Reduced file size per component

### Previous
- Single 600+ line welcome.blade.php file
- Hard to maintain
- Risk of merge conflicts
- Difficult to test individual sections

---

## 👥 Team Notes

**File Ownership**:
- `welcome.blade.php` - Core team (requires review)
- `landing/*.blade.php` - Can be edited by designers/frontend devs
- Navigation bar - Requires backend sync (auth logic)

**Review Process**:
1. Changes to partials - Standard review
2. Changes to main file - Senior dev review
3. New sections - Design + dev review
4. Style changes - Design approval required

---

**Last Updated**: October 22, 2025
**Maintained By**: SISARAYA Dev Team
**Documentation Version**: 1.0.0
