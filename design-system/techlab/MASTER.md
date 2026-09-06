# TechLab Design System - MASTER.md

*Generated with ui-ux-pro-max and apple-design principles for TechLab project*

---

## Product Overview

**Product Type:** Educational technology / developer portfolio / chat interface platform  
**Target Audience:** Students, developers, tech enthusiasts  
**Style Keywords:** Apple-inspired, clean, minimal, vibrant accents, fluid motion  
**Stack:** React + Three.js + Framer Motion + Tailwind CSS  
**Landing Page:** http://127.0.0.1:8001/student/planet/programming

---

## 1. Color System

Based on apple-design brand blue `#0a5bd6` and TechLab's existing palette:

| Token | Value | Usage |
|-------|-------|-------|
| `color-primary` | `#0a5bd6` | Primary actions, links, brand accent |
| `color-primary-hover` | `#00d4ff` | Hover states |
| `color-background` | `#fafafa` | Light background |
| `color-surface` | `#ffffff` | Card/surface backgrounds |
| `color-on-background` | `#1a1a1a` | Text on light surfaces |
| `color-on-surface-variant` | `#6b6b6b` | Secondary text |
| `color-divider` | `#e0e0e0` | Separators |
| `color-error` | `#ff3366` | Error states (existing accent) |
| `color-success` | `#00ffc6` | Success states (existing accent) |

### Dark Mode (prefers-color-scheme: dark)

| Token | Value |
|-------|-------|
| `color-background-dark` | `#121212` |
| `color-surface-dark` | `#1e1e1e` |
| `color-on-background-dark` | `#e0e0e0` |
| `color-divider-dark` | `#333333` |

---

## 2. Typography System

- **Base font:** `system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif`
- **Size scale (clamp-based responsive):**

| Token | Value | Example |
|-------|-------|---------|
| `type-xs` | `clamp(0.65rem, 0.5vw + 0.3rem, 0.75rem)` | 10px mobile |
| `type-sm` | `clamp(0.75rem, 0.5vw + 0.4rem, 0.875rem)` | 12px |
| `type-base` | `clamp(0.875rem, 0.5vw + 0.5rem, 1rem)` | 14px body (default 100%) |
| `type-lg` | `clamp(1.125rem, 0.5vw + 0.6rem, 1.25rem)` | 18px |
| `type-xl` | `clamp(1.5rem, 0.5vw + 0.8rem, 2rem)` | 24px headings |
| `type-2xl` | `clamp(2rem, 1vw + 1rem, 3rem)` | 32px section headings |
| `type-3xl` | `clamp(3rem, 1.5vw + 1.2rem, 4rem)` | 48px hero headings |

- **Line height:** `type-base` → `1.6`, `type-xl` → `1.3`, `type-2xl` → `1.2`, `type-3xl` → `1.1`
- **Letter tracking (size-specific):** 
  - Display/large text: `-0.02em` (negative tracking)
  - Body text: `0` (near zero)
  - Small text: `0.01em` (slight positive for legibility)
- **Font optical sizing:** `font-optical-sizing: auto` on all text elements

---

## 3. Spacing & Layout System

- **Base unit:** `1rem` = 16px (root font-size)
- **Spacing scale (preferred: 24px at low density, 16px at high density):**

| Token | Value | CSS Variable |
|-------|-------|--------------|
| `space-1` | `0.25rem` (4px) | `--space-1` |
| `space-2` | `0.5rem` (8px) | `--space-2` |
| `space-3` | `0.75rem` (12px) | `--space-3` |
| `space-4` | `1rem` (16px) | `--space-4` |
| `space-5` | `1.25rem` (20px) | `--space-5` |
| `space-6` | `1.5rem` (24px) | `--space-6` |
| `space-8` | `2rem` (32px) | `--space-8` |
| `space-10` | `2.5rem` (40px) | `--space-10` |
| `space-12` | `3rem` (48px) | `--space-12` |

- **Container max-width:** `1400px` with `mx-auto`
- **Grid columns:** 12-column responsive grid, gutters `var(--space-6)`

### Responsive Breakpoints

| Token | Value | CSS |
|-------|-------|-----|
| `bp-mobile` | `640px` | `@media (min-width: 640px)` |
| `bp-tablet` | `768px` | `@media (min-width: 768px)` |
| `bp-laptop` | `1024px` | `@media (min-width: 1024px)` |
| `bp-desktop` | `1280px` | `@media (min-width: 1280px)` |

---

## 4. Motion System (Apple-Design Inspired)

Based on apple-design principles from WWDC *Designing Fluid Interfaces*:

### Spring Configurations

| Interaction | Damping | Response (seconds) | Usage |
|-------------|---------|-------------------|-------|
| Default UI motion | `1.0` (critically damped) | `0.3` | General transitions, hover, subtle motion |
| Momentum/flick interactions | `0.8` (slight bounce) | `0.3` | Drag releases, card throws, menu dismissals |
| Drawer/sheet motion | `0.8` | `0.3` | Modal panels, side panels |
| Rotation motion | `0.8` | `0.4` | Card rotations, 3D transforms |

### Core Motion Principles

1. **Response on pointer-down, not release** - Hover/focus states activate immediately on press/touch-down
2. **1:1 direct tracking** - Drag elements follow pointer with offset respect
3. **Interruptibility** - All animations interruptible and re-targetable from current value
4. **Velocity handoff** - On gesture end, spring continues at finger's exact velocity
5. **Momentum projection** - Flick gestures project to resting position using velocity decay
6. **Spatial consistency** - Enter/exit along same path, anchor to trigger element
7. **Hint in gesture direction** - Motion telegraphs outcome direction
8. **Rubber-banding** - Progressive resistance at boundaries, not hard stops
9. **Reduced motion support** - `prefers-reduced-motion: reduce` → cross-fades, no springs
10. **Translucency & depth** - `backdrop-filter: blur()` for chrome/sheets

### Framer Motion / Motion UI Patterns

```jsx
// Critically damped (default, no overshoot)
<motion.div 
  animate={{ y: 0 }} 
  type="spring" 
  spring={{ damping: 1.0, stiffness: 100 }}
/>

// Momentum interaction with bounce
<motion.div 
  animate={{ y: target }} 
  type="spring" 
  spring={{ damping: 0.8, stiffness: 100 }}
/>

// Gesture velocity handoff example
<motion.div 
  animate={{ x: finalX }} 
  type="spring" 
  spring={{ 
    damping: 1.0, 
    stiffness: 150, 
    velocity: gestureVelocity / (finalX - currentX) 
  }}
/>

// Reduced motion fallback
<motion.div 
  animate={{ opacity: 1 }} 
  transition={{ 
    duration: 0.2, 
    type: "tween" 
  }}
  whileHover={{ opacity: 0.8 }}
/>
```

---

## 5. Icon System

Based on ui-ux-pro-max icon guidelines (105 curated icons, SVG preferred):

### Icon Usage Rules

1. **SVG icons only** - Never use emoji as icons
2. **Accessible labels** - All icon buttons must have `aria-label` or be decorative with `aria-hidden`
3. **Minimum size:** 24×24px for interactive, 16×16px for decorative
4. **Color:** Use `currentColor` or explicit color from token system
5. **Stroke width:** 1.5px–2px for consistency

### Recommended Icon Libraries

- **react-icons/pi** - Already installed (used in robot-hero.tsx)
- **react-icons/fa** - Font Awesome
- **lucide-react** - Modern consistent icon set
- ** heroicons** - Tailwind-compatible

### Icon Placement in Modules

Each module should have a representative icon:

| Module | Suggested Icon | Purpose |
|--------|---------------|---------|
| Home/Planets | `🌐` or `PiBold` | Represents the programming/planet concept |
| Courses | `BookOpen` or `GraduationCap` | Educational content |
| Settings | `SlidersHorizontal` or `Gear` | Configuration |
| Profile | `User` or `UserRound` | User account |
| Messages | `MessageCircle` or `Chat` | Communications |

---

## 6. Component Guidelines

### Buttons

```jsx
/* Primary button */
<button 
  className="inline-flex items-center justify-center rounded-full px-6 py-3 text-sm font-bold 
             transition-colors focus-visible:outline-none focus-visible:ring-2 
             focus-visible:ring-primary focus-visible:ring-offset-2 
             shadow-sm hover:bg-primary/90 active:scale-95"
>
  CTA Text
</button>

/* Secondary button */
<button 
  className="inline-flex items-center justify-center rounded-full px-6 py-3 text-sm font-medium 
             transition-colors border-2 border-divider hover:bg-primary/10 
             focus-visible:outline-none focus-visible:ring-2 
             focus-visible:ring-primary focus-visible:ring-offset-2"
>
  Secondary Text
</button>
```

### Form Elements

- **Labels always visible** - Never placeholder-only labels
- **Error messaging** - Inline, near field with `role="alert"`
- **Focus ring** - `focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2`
- **Touch minimum** - 44×44px minimum hit area

### Cards

- **Translucent material** - `background: rgba(255, 255, 255, 0.6); backdrop-filter: blur(20px);`
- **Shadow** - `shadow-sm` for elevated, `shadow-md` for prominent
- **Radius** - `rounded-lg` consistently
- **Padding** - `p-6` for content, `p-4` for compact

---

## 7. Accessibility (Critical - Priority 1)

### Must-Haves

- **Contrast ratio:** 4.5:1 minimum, 3:1 for large text (AA WCAG)
- **Alt text** on all informative images/icons
- **Keyboard navigation** - Tab order logical, focus visible
- **Aria-labels** on icon buttons and interactive SVGs
- **Skip links** for navigation
- **`prefers-reduced-motion`** support throughout

### Reduced Motion Media Queries

```css
@media (prefers-reduced-motion: reduce) {
  .motion-reduce {
    transition: none !important;
    animation: none !important;
    /* Use cross-fades instead of slides */
    transition: opacity 200ms ease-in-out !important;
  }
  
  /* Solid surfaces instead of translucent */
  .toolbar-reduce {
    background: white;
    backdrop-filter: none;
  }
}
```

### Focus Management

```css
/* Focus visible outline */
button:focus-visible,
a:focus-visible,
input:focus-visible,
select:focus-visible,
textarea:focus-visible {
  outline: 2px solid var(--color-primary);
  outline-offset: 2px;
}

/* Don't obscure focus on interactive elements */
.motion-reduce *:focus {
  /* Keep element visible, no transform that moves it */
}
```

---

## 8. Dark Mode

```css
@media (prefers-color-scheme: dark) {
  :root {
    --color-background: #121212;
    --color-surface: #1e1e1e;
    --color-on-background: #e0e0e0;
    --color-divider: #333333;
  }
}
```

---

## 9. Pre-Delivery Checklist (from ui-ux-pro-max pro-rules.md)

- [ ] All interactive elements have 44×44px minimum hit area
- [ ] Color contrast ≥ 4.5:1 (3:1 large text)
- [ ] Alt text provided on all informative images
- [ ] Focus rings visible on keyboard navigation
- [ ] `prefers-reduced-motion` support implemented
- [ ] Dark mode styles for key components
- [ ] Icon buttons have accessible labels (aria-label or svg role=img aria-hidden)
- [ ] No horizontal scroll on any breakpoint
- [ ] Layout shift (CLS) minimized - reserve space for dynamic content
- [ ] Touch targets have hysteresis/10px padding
- [ ] Navigation has ≤ 5 bottom nav items (if using bottom nav)
- [ ] Error messages appear near the field with clear guidance

---

## 10. Anti-Patterns to Avoid

- ❌ Mixing flat & skeuomorphic styles randomly
- ❌ Emoji as icons
- ❌ Placeholder-only labels (never visible labels)
- ❌ Instant state changes (0ms transitions)
- ❌ Horizontal scroll on any viewport
- ❌ Layout thrashing / CLS from unreserved space
- ❌ Animating width/height properties
- ❌ Focus rings removed entirely
- ❌ Gray-on-gray text (legibility collapse)
- ❌ Full-viewport moving backgrounds
- ❌ Slow looping oscillations (~0.2 Hz)
- ❌ Abrupt brightness jumps on theme switch

---

*Design System Version: 1.0*  
*Generated: 2026-09-04*  
*Primary Design Reference: Apple Design (WWDC 2018 - Designing Fluid Interfaces)*  
*Enhanced with ui-ux-pro-max intelligence*