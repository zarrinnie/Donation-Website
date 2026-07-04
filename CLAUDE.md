# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this project actually is

Despite `README.md` and `GUIDE.md` describing other projects (a "Gretiva" project-management template and an "MBKM Internship Logbook" system, respectively), this codebase is a **donation website** for a church ("Grace Community Church" — see donation reference prefix `GCC-`). Do not follow README.md/GUIDE.md as architectural sources of truth — they are leftover boilerplate from prior scaffolding and describe entities (Projects, Logbooks, InternshipPeriods) that do not exist here. Trust the actual code in `app/` instead. `.env.example` similarly still has `DB_DATABASE=mbkm_logbook` left over from that boilerplate.

Stack: Laravel 12, Livewire 3/4 (class-based components), Mary UI (DaisyUI + Tailwind v4), Fortify for auth (incl. 2FA), Pest 4 for tests, Spatie Translatable + a Google-Translate-backed auto-translation flow for i18n.

## Commands

```bash
composer dev            # serve + queue:listen + pail (logs) + vite dev, concurrently — the normal local dev loop
php artisan serve        # app server only
npm run dev               # vite dev server only
npm run build              # production asset build

composer lint             # pint --parallel (auto-fixes style)
composer test:lint         # pint --parallel --test (check only, no changes)
composer test              # config:clear + test:lint + php artisan test

php artisan test --compact                          # full suite
php artisan test --compact tests/Feature/DonationFlowTest.php   # one file
php artisan test --compact --filter=testName          # by name/filter
```

Always run `vendor/bin/pint --dirty` before finishing a change (staged/dirty files only). Tests are Pest-style (`it(...)`, `expect(...)`) under `tests/Feature` and `tests/Unit` — there are no raw PHPUnit `TestCase` classes to extend beyond `tests/TestCase.php`.

**Test environment caveat:** `phpunit.xml` hardcodes `DB_CONNECTION=sqlite`/`DB_DATABASE=:memory:` for testing, but some environments (e.g. this sandbox) don't have the `pdo_sqlite` PHP extension installed, so `php artisan test` fails with `could not find driver` regardless of any code change. If that happens, run tests against a real (throwaway) MySQL database instead by exporting env vars before invoking artisan — PHPUnit's `<env>` directives default to `force=false`, so pre-set shell env vars win: `export DB_CONNECTION=mysql DB_DATABASE=<some_test_db> DB_HOST=127.0.0.1 DB_PORT=3306 DB_USERNAME=... DB_PASSWORD=... CACHE_STORE=array SESSION_DRIVER=array MAIL_MAILER=array QUEUE_CONNECTION=sync` then `php artisan test --compact`. Create the throwaway database first (`CREATE DATABASE IF NOT EXISTS <name>`); `RefreshDatabase` runs the actual migrations against it per test.

Laravel Boost MCP tools are configured for this repo (`search-docs`, `tinker`, `database-query`, `browser-logs`, `list-artisan-commands`, `get-absolute-url`) — prefer `search-docs` over guessing at Laravel/Livewire/Pest/Tailwind v4 API details, since versions here are pinned (PHP 8.4, Laravel 12, Livewire 4, Pest 4, Tailwind 4, Fortify 1).

## Architecture

### Action + DTO pattern (the core convention)

Livewire components and controllers are thin. Business logic lives in single-purpose `Action` classes under `app/Actions/{Domain}/`, each with one `execute()` method, invoked via Laravel's container (constructor/method injection, e.g. `public function mount(RememberDonorAction $remember)`). Input to actions is a typed, constructor-promoted DTO under `app/DTOs/{Domain}/` (plain PHP classes, not Eloquent models). Example flow: `Livewire\Public\Donate` validates form input → builds `DonationData` → `CreateDonationAction::execute()` persists a `Donation`.

Domains follow this pattern: `Auth`, `Donation`, `DonationSetting`, `User`, `Doku`, `Subscription`, plus `Fortify` (Fortify's own action contracts: `CreateNewUser`, `ResetUserPassword`).

When adding a feature, check the sibling Action/DTO pair for the closest existing domain before inventing a new shape.

### Donation domain (the main business flow)

Currency is **IDR** throughout (church is Indonesia-based, payment gateway is DOKU) — amounts are formatted `Rp {{ number_format($amount, 0, ',', '.') }}` (whole rupiah, `.`-thousands) everywhere a donation amount is displayed, in-app and in emails. Don't reintroduce `$`/USD formatting.

- **Public flow**: `Landing` → `Donate` (pick/enter amount + support-period + donor bio, stored in `$this->amountOptions`/`$this->rangeOptions` sourced from `DonationSetting`) → intent is stashed in `session('donation_intent')` → `Payment` creates a `pending` `Donation` via `CreateDonationAction`, then calls `GenerateDokuPaymentAction` to get a real DOKU hosted-checkout URL and redirects the browser there (full external redirect — no `navigate: true`, Livewire SPA nav is same-origin only) → donor pays on DOKU's domain → DOKU redirects back to the signed `ThankYou` page and, independently, POSTs a webhook notification that actually confirms the payment (see the Doku section below). `Payment` no longer has fake card fields; there is no more mock gateway.
- `Donation` starts `pending`. Status only ever changes via `UpdateDonationStatusAction(Donation, string $status, string $source = 'admin_manual')` — the DOKU webhook path calls it with `source: Donation::STATUS_SOURCE_WEBHOOK`, the admin panel with the default `STATUS_SOURCE_ADMIN`. `status_source` on the row records which one last touched it. A status change triggers `SendDonationStatusEmailAction` (a status-specific `DonationStatusMail`, now also carrying a signed "View Receipt" link back to `ThankYou`).
- `SendDonationRemindersAction`/`DonationReminderMail`/`reminder_sent_at` (the original one-off "come give again once" flow) still exist and pass their tests, and are still manually triggerable via `php artisan donations:send-reminders`, but are **no longer scheduled** in `routes/console.php` — every DOKU donation now enrolls in the indefinite monthly `DonationSubscription` loop instead (see below), and running both would double-email donors. Don't re-add the old `Schedule::call` for it without removing/reconciling the new one.
- "Remember Me" donor biodata is persisted via `RememberDonorAction` (cookie-backed), not the DB — used to prefill the `Donate` form on repeat visits.
- `DonationSetting` rows are the admin-configurable presets for both amount and time-range choices (`type` = `amount` | `time_range`), toggled via `is_active` and ordered via `sort_order`; scopes `amounts()`/`timeRanges()`/`active()`/`ordered()` compose these.
- **Note:** `routes/console.php` also schedules two `DB::table('news')->update(...)` jobs (daily/monthly view-counter resets) that reference a `news` table with no corresponding migration in this repo — leftover/dead scheduler code from the boilerplate, not part of the donation flow. Don't assume a `news` table exists.

### DOKU payment & recurring monthly subscriptions

Every donation (first-time or renewal) goes through DOKU's hosted checkout, and every successful DOKU payment enrolls the donor in an indefinite monthly recurring "ask" — this is the church's growth model, not an opt-in toggle.

- **`app/Services/Doku/`** owns the wire format, not Actions: `DokuSignature` (the HMAC canonical-string signing/verification math, shared by both directions) and `DokuClient` (the actual HTTP call to DOKU's Checkout API, bound as a singleton in `AppServiceProvider` from `config('services.doku.*')`). This is the only external-API client in the codebase — if you add another third-party HTTP integration, follow this split (client owns wire format, Actions orchestrate) rather than putting `Http::` calls inside an Action.
- **Generating a payment link is always on-demand**, never pre-generated: `GenerateDokuPaymentAction::execute(Donation $donation)` is called only when the donor clicks "Pay" (in `Payment::pay()` for a first gift, or `SubscriptionReview::payAgain()` for a monthly renewal) — both call the same Action, so there's one code path for "ask DOKU for a checkout link," not two.
- **Webhook**: DOKU POSTs to `webhooks.doku.notification` (`DokuWebhookController`, CSRF-exempted in `bootstrap/app.php`'s `validateCsrfTokens(except: [...])`). `VerifyDokuWebhookSignatureAction` must pass (HMAC over the raw body + a 5-minute timestamp freshness window) before `ProcessDokuNotificationAction` runs — the fan-out that updates the donation's status, enrolls/renews the `DonationSubscription`, and sends the status email. It's idempotent by design: if the donation's status already matches the incoming (mapped) status, it's a safe no-op, since DOKU may retry notifications. **The exact DOKU request/webhook payload field names in `DokuClient`/`WebhookNotificationData::fromRequest()` are modeled from DOKU's documented Checkout API but not verified against a live sandbox** — check DOKU's current docs before relying on them in production.
- **`DonationSubscription`** (`donation_subscriptions` table) is the recurring plan: donor biodata snapshotted inline (same "no donor login" convention as `Donation`), `amount`/`next_reminder_at` refreshed on every renewal. `Donation.subscription_id` links each individual charge (origin or renewal) back to its plan. `EnrollOrRenewSubscriptionAction` is the single chokepoint that creates-or-renews (keyed by `donor_email`, at most one `active` subscription per email — enforced in PHP, not a DB constraint). Enrollment only happens via the verified webhook, never from an admin manually marking a donation `successful`.
- **Monthly reminder**: `SendSubscriptionRenewalRemindersAction` (scheduled daily in `routes/console.php`, manually via `php artisan subscriptions:send-renewal-reminders`) finds active subscriptions whose `next_reminder_at` has passed (PHP-side filtering, same MySQL/SQLite-parity reasoning as the legacy reminder action), emails `SubscriptionRenewalMail` with a `URL::temporarySignedRoute('donate.subscription.review', ..., ['subscription' => $id])` link, and re-pushes `next_reminder_at` a month out from send time (self-scheduling, so it can't drift).
- **`SubscriptionReview`** (route `donate.subscription.review/{subscription}`, `signed` middleware) is where a donor lands from that email: only the `amount` is editable (donor identity comes from the subscription record, not the form — can't be tampered with), defaults to the previous amount, and `payAgain()` follows the exact same create-donation → `GenerateDokuPaymentAction` → external-redirect path as first-time `Payment`.
- **Signed URLs are load-bearing security here**, not incidental: `ThankYou` (`donate.thank-you/{donation:reference}`) and `SubscriptionReview` both require the `signed` middleware (same pattern this repo already used for `verification.verify`) — never make either route reachable without a valid signature, since they expose one donor's data by ID/reference.
- `CancelSubscriptionAction` exists (sets `status = 'cancelled'`) but has no donor-facing trigger yet — no unsubscribe link is currently wired into `SubscriptionRenewalMail`.

### Roles & routing

Three roles live on `User::role`: `super_admin`, `admin`, `user` (no separate roles table — enum-like string column). `RoleMiddleware` (aliased as `role` in `bootstrap/app.php`) takes pipe-delimited roles, e.g. `role:super_admin|admin`, and redirects unauthorized users to their own dashboard instead of looping. Route groups in `routes/web.php`:
- Public: `/`, `/about`, `/donate`, `/donate/payment` — no auth. `/donate/thank-you/{donation:reference}` and `/donate/subscription/{subscription}/review` additionally require the `signed` middleware (see the Doku section above — these carry one donor's data by id/reference).
- `POST /webhooks/doku/notification` — DOKU's server-to-server callback; no `auth`/`signed` middleware (it's neither a browser session nor a donor-facing link), CSRF-exempted, authenticity enforced by HMAC inside `DokuWebhookController` itself.
- `/admin/*` under `role:super_admin|admin` (dashboard, donations, donation-settings, donors); `/admin/users` is further restricted to `role:super_admin` only.
- `/user/*` under `role:user` + `verified`.
- The generic `/dashboard` route is Fortify's configured post-login redirect target; it inspects `auth()->user()->isAdmin()` and forwards to `admin.dashboard` or `user.dashboard`.

Middleware pipeline is fully declared in `bootstrap/app.php` (Laravel 12's streamlined structure — no `Http/Kernel.php`). `SetLocale` and `SetTimezone` run on every web request, after the base stack.

### i18n: two independent translation systems

1. **Static UI strings**: `__('...')` calls scanned by `kkomelin/laravel-translatable-string-exporter` (`php artisan translatable:export {locale}`) into `lang/{locale}.json`. A custom command `php artisan translate:json {locale}` (see `app/Console/Commands/AutoTranslateJson.php`) then fills any untranslated (`key === value` or empty) entries via Google Translate, preserving existing manual translations. Source language is assumed English (`en`).
2. **Dynamic DB content**: models needing per-locale fields use Spatie's `HasTranslations` trait (`User` already uses it) over a `json` column. `AutoTranslationService::fillMissingTranslations(['id' => ..., 'en' => ...])` auto-fills whichever of `id`/`en` is missing, given the other.

These two systems don't share code — don't conflate a `lang/*.json` UI-string fix with the DB-content translation path.

### Livewire structure

Components are organized by audience under `app/Livewire/`: `Public/` (Landing, About, Donate, Payment, ThankYou, SubscriptionReview), `Auth/` (Login, Register, ForgotPassword, ResetPassword, VerifyEmail — these back Fortify), `Admin/` (Dashboard, Donation/Index, DonationSetting/Index, Donor/Index, User/Index, GlobalSearch), `User/` (Dashboard). Cross-cutting components (`GlobalSearchBar`, `LanguageSwitcher`, `NavbarNotifications`, `TimezoneDetector`) sit directly under `Livewire/`. Views live in the mirrored `resources/views/livewire/...` path.

Fortify auth actions (`app/Actions/Fortify/`) implement Fortify's own contracts (`CreateNewUser`, `ResetUserPassword`) and are wired in `FortifyServiceProvider`; these are separate from the app's own `Actions/Auth/*` classes used directly by the `Auth` Livewire components.
