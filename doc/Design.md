# DESIGN.md

# Online Camera Rental System

Version: 1.0

---

# Design Philosophy

The website should feel like a premium camera brand rather than a traditional e-commerce store.

Design inspiration comes from:

- Canon Product Pages
- Sony Alpha
- Leica
- Apple Product Pages

The design must be:

- Clean
- Minimal
- Professional
- Premium
- Photography-focused

Avoid colorful UI, unnecessary animations, and clutter.

---

# Theme

## Theme Name

Obsidian Black

---

## Color Palette

| Purpose | Color | Hex |
|----------|---------|---------|
| Primary Background | Black | #000000 |
| Secondary Background | Dark Gray | #0D0D0D |
| Card Background | Graphite | #141414 |
| Navbar | Black | #000000 |
| Footer | Black | #000000 |
| Primary Text | White | #FFFFFF |
| Secondary Text | Light Gray | #B8B8B8 |
| Muted Text | Gray | #8A8A8A |
| Border | Dark Border | #242424 |
| Primary Button | Canon Red | #F44336 |
| Button Hover | Bright Red | #FF5C5C |
| Success | Green | #22C55E |
| Error | Red | #D32F2F |

---

# Theme Rules

Never use:

- Blue
- Purple
- Neon colors
- Rainbow gradients
- Glassmorphism
- Heavy shadows
- Bootstrap colors

Keep everything neutral.

The camera images should be the focus.

---

# Typography

Preferred Font

Poppins

Fallback

Arial, Helvetica, sans-serif

---

## Font Sizes

### Hero Heading

60px

Weight

700

Example

RENT

PROFESSIONAL

CAMERAS

---

### Section Heading

38px

Weight

700

---

### Card Title

24px

Weight

600

---

### Normal Text

16px

Weight

400

---

### Small Text

14px

Weight

400

---

### Button Text

16px

Weight

600

Uppercase

---

# Border Radius

Buttons

8px

Cards

12px

Inputs

8px

Images

12px

---

# Shadows

Very minimal.

Cards

box-shadow:

0 10px 30px rgba(0,0,0,.20);

Hover

0 15px 35px rgba(0,0,0,.35);

---

# Layout

Website Width

1200px

Container

margin:auto;

padding:0 20px;

---

# Spacing

Section Top

100px

Section Bottom

100px

Card Gap

30px

Navbar Height

80px

Button Padding

15px 32px

---

# Navbar

Background

Black

Height

80px

Logo Left

Navigation Right

Menu

Home

Cameras

Accessories

About

Contact

Login

Hover

Underline

Accent Red

Sticky

Yes

Transparent on top

Black on scroll

---

# Hero Section

Layout

Two Columns

Left

Large Heading

Small Description

Primary Button

Secondary Button

Right

Large Camera PNG

The hero image should occupy around 45–50% of the section width.

---

# Buttons

Primary Button

Background

#F44336

Text

White

Hover

#FF5C5C

Transition

0.3s

---

Secondary Button

Transparent

White Border

White Text

Hover

Dark Gray Background

---

# Cards

Background

#141414

Border

1px solid #242424

Radius

12px

Padding

20px

Image

Top

Content

Bottom

Hover

Translate Y

-5px

---

# Product Card

Camera Image

Camera Name

Category

Price Per Day

Rent Button

No rating.

No discount badge.

No unnecessary labels.

---

# Forms

Background

#141414

Input Background

#1B1B1B

Border

#2A2A2A

Focus Border

#F44336

Label

White

Placeholder

Gray

Buttons

Red

---

# Tables (Admin)

Header

Black

Rows

Dark Gray

Hover

Slightly Lighter

Border

#242424

---

# Dashboard Cards

Dark Background

Small Icon

White Title

Large Number

Minimal

---

# Images

Use

High Resolution

Transparent PNG

Dark Photography

Professional Product Images

Avoid

Stock Photos

Cartoons

Illustrations

Anime

Low Resolution

---

# Icons

Simple

Line Icons

White

Accent

Red

No colorful icons.

---

# Sections

Homepage

1. Navbar

2. Hero

3. Featured Camera

4. Featured Lens

5. Categories

6. Why Choose Us

7. Rental Process

8. Customer Reviews

9. Contact CTA

10. Footer

---

# Camera Details Page

Large Image

Gallery

Specifications

Rental Price

Availability

Description

Rent Button

Related Cameras

---

# Admin Design

Simple

Professional

No fancy dashboard.

Dark Theme

Sidebar

Top Header

Cards

Tables

Forms

---

# Animations

Only CSS

Allowed

Hover

Fade

Image Zoom

Button Hover

Navbar Transition

Card Lift

Not Allowed

Bounce

Rotate

Flash

Typing Animation

Parallax

Heavy Motion

---

# Responsive Design

Desktop

1200px

Laptop

992px

Tablet

768px

Mobile

480px

Navigation

Desktop

Horizontal

Mobile

Hamburger Menu

---

# CSS Rules

Single reusable stylesheet

css/style.css

Use reusable utility classes whenever possible.

Avoid inline CSS.

Avoid duplicate styles.

---

# Naming Convention

Classes

kebab-case

Example

camera-card

hero-section

section-title

btn-primary

btn-secondary

dashboard-card

Variables

Meaningful names

Example

camera_price

booking_date

user_name

---

# UI Principles

Every page should follow the same design language.

Use plenty of whitespace.

Large photography should always be the focal point.

Buttons should be easy to identify.

Forms should be clean.

Cards should be simple.

Maintain consistency across all pages.

The interface should feel like a premium camera rental brand while remaining easy to build using only HTML, CSS, JavaScript, PHP, and MySQL compatible with WAMP 5.2.