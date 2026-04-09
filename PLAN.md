# FadeBook — Project Blueprint
> الحلاقة… بشكل جديد

---

## Executive Summary

**FadeBook** is a SaaS reservation platform built specifically for the Egyptian barbershop market. It is a Progressive Web App (PWA) that looks, feels, and installs exactly like a native mobile app on both iPhone and Android — with no App Store or Google Play subscription required. The UI follows the **Liquid Glass design language** (iOS 26 style): frosted glass cards, blur effects, bottom tab bar, swipe gestures, and full RTL Arabic (Egyptian dialect) support.

The platform connects three types of users: **clients** who book haircuts, **barbershop owners / barbers** who manage their shop and schedule, and a **super admin** (you) who manages the entire platform via a Filament 5 panel.

The business model is **commission-only**: a dynamic percentage is taken automatically from each online payment at the gateway level and transferred to your account — no monthly subscription is charged to shops. Each shop's commission rate is set individually by the super admin.

The platform is built with a spec-driven development approach using **GitHub Spec Kit**, meaning every feature is written as a formal specification before any code is generated.

---

## Project Details

| Property | Value |
|---|---|
| **Market** | Egypt only |
| **Language** | Arabic — Egyptian dialect (عامية مصرية) |
| **Layout** | RTL |
| **Type** | PWA (installable, no App Store) |
| **Design** | Liquid Glass — iOS 26 style |
| **Business model** | Commission per booking (dynamic per shop) |
| **Payment gateway** | TBD (Fawaterk / Kashier / other) |
| **Notifications** | WhatsApp custom API only |
| **Timeline** | No deadline — quality first |
| **Team** | Solo developer |
| **Methodology** | Spec-Driven Development (GitHub Spec Kit) |

---

## Tech Stack

| Layer | Technology |
|---|---|
| **Backend framework** | Laravel 13 |
| **Admin panel** | Filament 5 |
| **Reactive UI** | Livewire 4 |
| **Frontend interactivity** | Alpine.js |
| **Styling** | Tailwind CSS |
| **Database** | MySQL |
| **Authentication** | Phone number + password (Laravel built-in + custom OTP) |
| **Notifications** | WhatsApp custom API |
| **Payment** | TBD (Fawaterk / Kashier / other — must support split payments) |
| **PWA** | Laravel PWA package + service worker + web app manifest |

---

## Database & Enum Convention

All enum-like columns in migrations must use `tinyInteger` (not MySQL ENUM type). Each such column must have a corresponding **backed PHP Enum class** (backed by `int`) in `app/Enums/`. This is a non-negotiable constitution rule.

**Example:**
```php
// Migration
$table->tinyInteger('status')->default(0);

// app/Enums/BookingStatus.php
enum BookingStatus: int
{
    case Pending    = 0;
    case Confirmed  = 1;
    case InProgress = 2;
    case Completed  = 3;
    case Cancelled  = 4;
    case NoShow     = 5;
}
```

This applies to all enums across the project: booking status, shop status, payment status, user roles, refund status, etc.

---

## User Roles

### 1. Client
- Registers and logs in with **phone number + password**
- Confirms bookings with a **one-time OTP** sent via WhatsApp
- Browses the marketplace, views shop pages, books services
- Views booking history, upcoming appointments, and can cancel/reschedule
- Receives all notifications via WhatsApp only
- Must **accept the cancellation & refund policy** before completing any booking (checkbox at checkout)
- Subject to **no-show strike system** (see Booking Policies)

### 2. Barbershop Owner / Barber
- Registers with phone number + password + full shop details
- Account is **pending for 24 hours** after registration — super admin approves or rejects via Filament
- Has a dashboard with the **same layout and UI as the client dashboard** but with extended features:
  - Edit all shop settings (name, logo, banner, photos, hours, services)
  - Set **advance booking window** (how many days ahead clients can book — set per shop)
  - Manage barbers (add, edit, remove, assign photos and portfolios)
  - View and manage upcoming reservations
  - View client list
  - Financial reports: earnings, commissions deducted, net payout
  - Choose payment mode per shop: client pays nothing / partial deposit / full amount
  - Mark bookings as: Arrived, Completed, No-Show

### 3. Super Admin
- Accesses the platform via **Filament 5 panel** (separate URL, e.g. `/admin`)
- Full control over everything on the platform
- Approves or rejects barbershop registrations
- Sets **individual commission % per shop** (dynamic, not global)
- Views platform-wide analytics: bookings, revenue, top shops, growth
- Manages users: ban, support, view history
- Views and manages all financial transactions
- Manages **Terms of Use** and **Privacy Policy** page content (editable rich text via Filament)

---

## Booking Policies

### Identity Verification at the Shop
Each confirmed booking is assigned a **short unique booking code** (e.g. `#4821`), sent to the client via WhatsApp at booking confirmation. When the client arrives at the shop, they provide this code verbally. The barber looks it up in their dashboard — no phone display required.

### Advance Booking Window
Each shop owner sets how many days ahead clients are allowed to book (e.g. 7 days, 14 days, 30 days). This is configured per shop in the shop settings dashboard.

### Barber Unavailability
If a shop owner marks a barber as unavailable for a specific day:
- Their time slots are immediately removed from the booking calendar (no new bookings possible)
- All **existing confirmed bookings** for that barber on that day are **automatically cancelled**
- Affected clients receive a WhatsApp cancellation notice with a link to rebook
- The shop owner is responsible for marking unavailability as early as possible

### No-Show Strike System (Client)
- After **1st no-show**: client receives a WhatsApp warning
- After **2nd no-show**: client account is **flagged and blocked from making new bookings**
- Super admin can manually unblock a flagged client via Filament
- Strike count is visible in the super admin user management panel

### Cancellation & Refund Policy

| Scenario | Outcome |
|---|---|
| Client cancels **more than 2 hours** before appointment | Full refund |
| Client cancels **within 2 hours** of appointment | No refund (slot cannot be filled) |
| Client **no-show** (unpaid booking) | Marked as no-show, strike added |
| Client **no-show** (paid booking) | No refund — full amount kept (shop protected) |
| **Shop cancels** on client | Full refund always, no exceptions |

> The cancellation and refund policy is displayed to the client and must be **explicitly accepted** (checkbox) before every booking is finalised.

### Refunds Table
All refunds are tracked in a dedicated `refunds` table with:
- Booking reference
- Amount refunded
- Reason (`client_cancel_early` / `shop_cancel` / `other`)
- Status: `pending` / `processed` / `failed`
- Timestamp

---

## Booking Status Flow

```
pending → confirmed → in_progress → completed
                    ↘ cancelled
                    ↘ no_show
```

| Status | Trigger |
|---|---|
| `pending` | Booking created, awaiting OTP confirmation |
| `confirmed` | OTP verified + payment (if required) completed |
| `in_progress` | Barber marks client as "Arrived" at the shop |
| `completed` | Barber marks service as done → review WhatsApp sent to client |
| `cancelled` | Client or shop cancels → refund logic triggered |
| `no_show` | Barber marks client as no-show → strike logic triggered |

---

## At-Shop Check-in Flow

### Barber Dashboard (shop side)
1. Barber opens today's upcoming reservations
2. Client arrives and provides their booking code (e.g. `#4821`)
3. Barber finds the booking by code or client name
4. Barber taps **"Mark as Arrived"** → status becomes `in_progress`
5. After service is done → barber taps **"Mark as Completed"** → status becomes `completed`
6. Completion triggers a WhatsApp review request to the client
7. If client doesn't arrive within **15 minutes** of slot start → barber can tap **"Mark as No-Show"**

### Client Side (during visit)
- When marked as arrived: client receives WhatsApp: *"وصلنا عندك عند [اسم الحلاق]، استنى دورك!"*
- When marked as completed: client receives WhatsApp review request

---

## Shop & Service Visibility

### Shop Offline (owner disables shop)
- Shop **remains visible** in the marketplace with an **"مش متاح دلوقتي"** badge
- Booking button is disabled — clients cannot book
- Clients can still view the shop page, services, and reviews

### Service Deactivated (owner deactivates a service)
- Service **remains visible** on the shop page with a **"مش متاح"** badge
- Cannot be selected in the booking flow

---

## Spec Kit Workflow

Every phase below follows this exact process:

```
/constitution → /specify → /plan → /tasks → /implement → review & iterate
```

Each phase produces one spec file. The agent used for implementation can be any compatible tool (Cursor, Claude Code, Codex, Windsurf, etc.).

---

## Phases

---

### Phase 0 — Constitution
**Spec:** `constitution.md`

Defines the non-negotiable rules for the entire project. Must be written before any spec.

**Covers:**
- Tech stack lock (Laravel 13, Filament 5, Livewire 4, Alpine.js, Tailwind CSS, MySQL)
- Folder structure and naming conventions
- Code style rules (PSR-12, strict types, no raw queries)
- **All enum columns use `tinyInteger` + backed PHP Enum class (see Database & Enum Convention)**
- Testing approach (Pest for unit/feature, no E2E in MVP)
- Database conventions (snake_case, soft deletes everywhere, UUID primary keys)
- RTL-first CSS rules
- PWA requirements (manifest, service worker, offline fallback)
- Liquid Glass design tokens (blur, frosted glass, safe area insets, bottom tab bar)
- WhatsApp API integration rules (no other notification channel)
- Payment gateway interface (must be gateway-agnostic to swap easily)
- Multi-tenancy model: each shop is a tenant with isolated data
- Commission system: dynamic per shop, applied at gateway split level
- Arabic dialect rule: all UI copy in Egyptian Arabic (عامية)
- No push notifications — WhatsApp only
- Booking code generation rule: short, unique, human-readable (4–6 alphanumeric characters)

---

### Phase 1 — Authentication & Onboarding
**Spec:** `001-auth-and-onboarding.md`

**Client registration & login:**
- Register with: phone number, password, name
- Login with: phone number + password (phone is the primary field, not email)
- OTP via WhatsApp is used only to **confirm a booking**, not to log in
- Forgot password: send OTP via WhatsApp → reset password

**Barbershop owner registration:**
- Register with: phone number, password, owner name
- Then fill in full shop details: shop name, address, area, phone, logo, opening hours, services (basic), number of barbers
- After submission → status set to `pending`
- Super admin receives notification in Filament → approves or rejects
- Owner receives WhatsApp message with result
- If approved → shop goes live on the marketplace
- If rejected → owner gets reason via WhatsApp

**Super admin:**
- Created manually via `php artisan` seeder — no public registration

**Roles:** `client`, `barber_owner`, `barber_staff`, `super_admin`

---

### Phase 2 — Marketplace & Shop Pages
**Spec:** `002-marketplace-and-shop-pages.md`

**Main website (marketplace / homepage):**
- Lists all approved barbershops
- Each shop card shows: logo, shop name, area, rating stars, number of reviews, available today indicator
- Shops that are offline show an **"مش متاح دلوقتي"** badge and cannot be booked
- Search by shop name or area
- Filter by: area, rating, availability today
- Sorted by: rating (default), newest, nearest (future)

**Individual shop page (`/shops/{slug}`):**
- Shop banner (full-width hero image)
- Shop logo + name + area + rating
- Gallery: shop interior photos
- Services list: name, duration, price — deactivated services show **"مش متاح"** badge
- Barber cards: each barber has their own card with their **individual photo**, name, specialty
- Reviews section: star rating + comment + client name + date
- Book button → enters booking flow (disabled if shop is offline)

---

### Phase 3 — Shop Management Dashboard
**Spec:** `003-shop-dashboard.md`

The shop owner dashboard uses the **exact same layout and UI shell as the client dashboard**. Same navigation structure, same component patterns, same Liquid Glass design. The difference is in the content and available actions.

**Dashboard home:**
- Upcoming reservations (today + this week)
- Quick stats: total bookings this month, total earnings, commission deducted, net payout
- Recent client activity

**Shop settings:**
- Edit shop name, logo, banner, gallery photos
- Edit opening hours per day
- Enable/disable the shop (go offline temporarily — shows badge, not hidden)
- Set **advance booking window**: how many days ahead clients can book (e.g. 7 / 14 / 30 days)
- Set barber selection mode: "any available barber" or "client picks barber"
- Set payment mode: no payment required / partial deposit / full payment upfront

**Barbers management:**
- Add barber: name, phone, photo, specialties, portfolio photos (haircut examples)
- Edit / remove barbers
- **Mark barber as unavailable** for a specific day → auto-cancels their existing bookings and notifies clients
- Each barber has their own profile visible on the shop page

**Services management:**
- Add service: name, price, duration, description, photo (optional)
- Edit / deactivate services (deactivated services show badge, not hidden)

**Reservations:**
- Calendar view of all upcoming bookings
- Each booking shows: client name, phone, **booking code**, service, barber, time, payment status
- Actions: **Mark as Arrived**, **Mark as Completed**, **Mark as No-Show**, Cancel
- No-show action triggers strike logic and (if paid) no-refund policy

**Clients:**
- List of all clients who have booked at this shop
- View client booking history at this shop

**Financial reports:**
- Total revenue this month / all time
- Commission deducted (shown transparently)
- Net payout
- Breakdown per service and per barber
- Transaction history
- Refund history

---

### Phase 4 — Booking Flow (Client Side)
**Spec:** `004-booking-flow.md`

Full Liquid Glass PWA experience. All screens are mobile-first.

**Step 1 — Pick shop**
Client arrives on marketplace or directly on a shop page via shared link.

**Step 2 — Pick service**
Choose from the shop's active services. Shows name, duration, price.

**Step 3 — Pick barber** *(conditional)*
Only shown if shop owner has enabled "client picks barber" mode.
Each barber shown with their photo, name, and specialty.
If mode is "any available barber", this step is skipped.

**Step 4 — Pick date & time slot**
Calendar date picker limited to the shop's configured **advance booking window**.
Available time slots shown for that date.
Slots respect: barber working hours, existing bookings, service duration.
Real-time slot locking to prevent double booking.

**Step 5 — Confirm & pay**
- Policy acceptance: client must check a box confirming they have read and accepted the **cancellation & refund policy** before proceeding.
- If shop payment mode is **"no payment"**: booking confirmed directly.
- If shop payment mode is **"partial deposit"**: client pays the deposit amount via payment gateway.
- If shop payment mode is **"full payment"**: client pays the full service price via payment gateway.
- OTP sent via WhatsApp to confirm identity before payment/booking is finalised.
- Booking confirmation (including **booking code**) sent via WhatsApp after success.

**Waitlist:**
If all slots are full, client can join a waitlist for a specific time slot.
When a cancellation occurs → WhatsApp message sent automatically to next person on waitlist.

---

### Phase 5 — Payments & Commission
**Spec:** `005-payments-and-commission.md`

**Gateway requirement:**
The payment gateway must support **split payments** (sub-merchant / marketplace model).
Gateway is TBD — candidates: Fawaterk, Kashier, or other Egyptian-friendly gateway.
The integration must be built behind a gateway interface/adapter so it can be swapped without rewriting business logic.

**Payment flow:**
1. Client pays online at booking time (if shop requires it)
2. Gateway automatically splits the payment:
   - **Your commission %** (set per shop by super admin) → credited to your account
   - **Remaining amount** → credited to shop's account
3. No manual intervention required

**Commission rules:**
- Commission % is set **per shop individually** by the super admin via Filament
- Different shops can have different commission rates
- Commission is deducted from every online payment automatically
- Both you and the shop can see the breakdown in their financial dashboards

**Refunds:**
- Client cancels **more than 2 hours** before appointment → full refund
- Client cancels **within 2 hours** → no refund
- Client no-show (paid) → no refund, full amount kept
- Shop cancels → full refund to client, always
- All refunds are logged in the `refunds` table (booking ref, amount, reason, status, timestamp)
- Refund logic must account for commission already split at gateway level

---

### Phase 6 — Notifications (WhatsApp Only)
**Spec:** `006-notifications.md`

**Notification channel:** WhatsApp custom API only. No SMS, no email, no push notifications.

**Triggers and messages (all in Egyptian Arabic):**

| Event | Recipient | Message content |
|---|---|---|
| Booking confirmed | Client | Shop name, service, barber, date, time, **booking code**, cancellation link |
| Booking confirmed | Shop owner | Client name, service, barber, date, time, booking code |
| OTP for booking | Client | OTP code |
| Smart reminder | Client | Sent 24h before if booking is >24h away. Sent 1h before if booking is same-day or <24h away. Skipped if booking is within the next hour. |
| Client marked as arrived | Client | *"وصلنا عندك عند [اسم الحلاق]، استنى دورك!"* |
| Booking completed | Client | Review request with link |
| Booking cancelled by client | Shop owner | Client name, cancelled slot |
| Booking cancelled by shop | Client | Cancellation notice + full refund info |
| Auto-cancel (barber unavailable) | Client | Cancellation notice + rebook link |
| Waitlist slot opened | Client on waitlist | *"فيه وقت اتفتح"* + booking link |
| No-show warning (1st strike) | Client | Warning message |
| No-show block (2nd strike) | Client | Account blocked notice |
| Shop registration approved | Shop owner | Approval confirmation |
| Shop registration rejected | Shop owner | Rejection reason |
| Password reset OTP | Any user | OTP code |

---

### Phase 7 — Ratings & Reviews
**Spec:** `007-ratings-and-reviews.md`

**Post-visit review flow:**
- After barber marks booking as `completed` → WhatsApp message sent to client with a review link
- Client clicks link → opens review page in PWA
- Client rates: the shop (1–5 stars) + the barber (1–5 stars, optional)
- Client writes a text comment (optional)
- One review per booking — cannot review twice

**Public display:**
- Reviews shown on shop page: star rating, comment, client first name, date
- Barber cards show their individual average rating
- Marketplace listing shows shop average rating
- Reviews can be sorted by: newest, highest, lowest

**Moderation:**
- Super admin can remove reviews via Filament
- Shop owner can flag a review for admin review

---

### Phase 8 — Super Admin Panel (Filament 5)
**Spec:** `008-super-admin.md`

Full Filament 5 panel. Accessible at `/admin`. No public registration — created via seeder.

**Shops management:**
- List all shops with status (pending / active / suspended)
- Approve or reject pending shop registrations (with rejection reason)
- Suspend / reactivate active shops
- View full shop details, bookings, and financials
- **Set commission % per shop individually** — each shop can have a different rate
- Override shop settings if needed

**Users management:**
- List all clients and barber owners
- View user profile, booking history, payment history
- View **no-show strike count** per client
- **Manually unblock** clients flagged by the strike system
- Ban / unban users
- Send WhatsApp message to a user directly from Filament

**Financial overview:**
- Total platform revenue (all commissions collected)
- Per-shop commission breakdown
- Transaction log: every payment, split, refund
- **Refund log**: all refunds with reason, amount, and status
- Export to CSV

**Platform analytics:**
- Total bookings per day / week / month
- Top shops by bookings and revenue
- Growth charts
- No-show rate across platform

**Content moderation:**
- Review flagging queue
- Remove or approve flagged reviews

**Static pages management:**
- Edit **Terms of Use** page content (rich text editor)
- Edit **Privacy Policy** page content (rich text editor)
- Changes are reflected immediately on the live `/terms` and `/privacy` pages

---

### Phase 9 — Shop Analytics Dashboard
**Spec:** `009-shop-analytics.md`

Built inside the shop owner dashboard (same UI shell — see Phase 3). This phase adds dedicated analytics screens.

**Metrics:**
- Bookings: total, completed, cancelled, no-show — per day / week / month
- Revenue: gross, commission deducted, net payout — per period
- Top services by bookings and revenue
- Top barbers by bookings and rating
- Repeat client rate
- Busiest days and time slots

**Visuals:**
- Bar charts and line charts (Livewire + Alpine.js + Chart.js or similar)
- All in Egyptian Arabic labels
- Mobile-first, Liquid Glass card style

---

### Phase 10 — Static & Policy Pages
**Spec:** `010-static-pages.md`

Two public-facing static pages editable by the super admin via Filament 5.

**Pages:**
- `/terms` — Terms of Use
- `/privacy` — Privacy Policy

**Rules:**
- Content is stored in the database and rendered as HTML
- Edited via a rich text editor in the Filament admin panel
- No code deployment needed to update content
- Both pages are linked in the booking checkout flow (client must accept before booking)
- Both pages are linked in the PWA footer/navigation

---

## PWA & Design System Notes

**PWA requirements:**
- Web app manifest with name, icons, theme color, display: standalone
- Service worker for offline fallback page
- Installable on iPhone (Add to Home Screen) and Android (Install prompt)
- Full-screen experience, no browser chrome when installed
- Safe area insets for iPhone notch and home indicator

**Liquid Glass design system:**
- Frosted glass cards: `backdrop-filter: blur(20px)` + semi-transparent white/dark backgrounds
- Bottom tab bar navigation (fixed, safe-area-aware)
- Swipe gestures for navigation between screens
- iOS-style transitions and animations
- All components RTL-ready by default
- Dark mode support (follows system preference)

---

## What Is NOT in This Version (Deferred)

| Feature | Reason |
|---|---|
| Walk-in QR queue | Cancelled — online booking only |
| Push notifications | Cancelled — WhatsApp only |
| Subscription plans for shops | Deferred — commission only for now |
| Multi-country / multi-language | Deferred — Egypt + Arabic only for now |
| Native iOS / Android app | Not needed — PWA replaces it |
| Shop custom domain | Deferred — `/shops/{slug}` only for now |

---

## File Structure (Spec Kit)

```
project/
├── constitution.md
├── specs/
│   ├── 001-auth-and-onboarding.md
│   ├── 002-marketplace-and-shop-pages.md
│   ├── 003-shop-dashboard.md
│   ├── 004-booking-flow.md
│   ├── 005-payments-and-commission.md
│   ├── 006-notifications.md
│   ├── 007-ratings-and-reviews.md
│   ├── 008-super-admin.md
│   ├── 009-shop-analytics.md
│   └── 010-static-pages.md
├── plans/
├── tasks/
└── src/
```

---

*Blueprint version 2.0 — updated with booking policies, check-in flow, refund system, enum conventions, and static pages*
