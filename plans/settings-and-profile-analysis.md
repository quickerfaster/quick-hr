# Analysis Report: User Profile, Top Navigation & Settings Architecture

---

## 1. [`my-profile.blade.php`](../../app/Modules/Hr/Resources/views/my-profile.blade.php) — User Profile Page

### Structure & Pattern

This is an extremely thin wrapper — only 22 lines. It uses a **self-service delegation pattern**:

```php
// 1. Find the employee record linked to the authenticated user
$employee = App\Modules\Hr\Models\Employee::where('user_id', Auth::id())->first();

// 2. Derive the record ID
$recordId = $employee->id;

// 3. Reuse the generic employee-detail Livewire component
$customComponent = 'qf.employee-detail';
```

### Layout & Components Used

| Component | Purpose |
|---|---|
| [`x-qf::navigation-layout`](relative://) | Main layout wrapper with config overrides |
| `qf.employee-detail` | Reused employee detail component, passed `recordId`, `configKey`, `returnParams` |

### Layout Overrides

The page explicitly disables several standard chrome elements:

| Override | Value | Effect |
|---|---|---|
| `top_bar.enabled` | `true` | Keeps top nav visible |
| `breadcrumb.enabled` | `false` | Hides breadcrumb trail |
| `title.enabled` | `false` | Hides page title |
| `titleRow.enabled` | `false` | Hides title row area |
| `context_menu.enabled` | `false` | Hides context/action menu |

### Key Architectural Insight

The profile page does **not** have its own dedicated component or Blade template. It reuses [`qf.employee-detail`](relative://) — the same component used for viewing any employee. This means:
- The "My Profile" view is functionally identical to viewing an employee record
- Any changes to employee detail display automatically apply to both contexts
- There is no separate "user preferences" page currently

---

## 2. [`top-nav.blade.php`](../../../Libraries/ui-library/src/Resources/views/livewire/navs/top-nav.blade.php) — Top Navigation Bar

### Component Class: [`TopNav.php`](../../../Libraries/ui-library/src/Http/Livewire/Layouts/Navs/TopNav.php:9)

**Public Properties:**

| Property | Type | Purpose |
|---|---|---|
| `$items` | `array` | Navigation context groups (People, Attendance, etc.) |
| `$activeContext` | `string` | Currently active nav item key |
| `$moduleName` | `string` | Current module slug (e.g., 'hr') |
| `$maxDesktop` | `int` (5) | Max visible desktop items before overflow |
| `$maxMobile` | `int` (3) | Max visible mobile items before overflow |
| `$leftShared` | `array` | Shared nav items on the left |
| `$rightShared` | `array` | Shared nav items on the right |
| `$companies` | `Collection\|null` | Visible companies for switcher |
| `$currentCompanyId` | `?int` | Currently selected company |
| `$currentCompanyName` | `?string` | Display name of current company |

### Nav Bar Layout (Left to Right)

```
[Module Switcher] [Nav Items (desktop)] ... [Company Switcher] [Jobs] [Locale] [Profile ▼]
```

### Profile Dropdown Structure (Lines 278–334)

```
┌─────────────────────────────────┐
│ User Name (dropdown-item-text)  │ ← ucwords(auth()->user()?->name)
├─────────────────────────────────┤
│ 🛠 My Profile        → /hr/my-profile       │ ← hardcoded URL
│ ✏️ Edit My User Account → /hr/my-account    │ ← hardcoded URL
│ ▶ Take the Tour      → route('tour.restart')│ ← desktop only (d-none d-md-block)
├─────────────────────────────────┤
│ 🚪 Logout (wire:click="logout") │
└─────────────────────────────────┘
```

**Critical observations about the profile dropdown:**

1. **All links are hardcoded URLs** — there are no named routes for `/hr/my-profile` or `/hr/my-account`
2. **No preferences/settings link exists** — the dropdown has "My Profile" and "Edit My User Account" but no "Preferences" or "Settings" link
3. **Avatar handling**: Shows user avatar if `avatar_url` exists, otherwise a blue circle with `fa-user` icon
4. **The dropdown uses `wire:ignore`** — meaning Livewire does not re-render it, preserving Bootstrap's JS behavior
5. **"Take the Tour"** is hidden on mobile via `d-none d-md-block`

### Other Nav Features

| Feature | Location | Details |
|---|---|---|
| Module Switcher | Leftmost | Shows QuickHR with dropdown for HR Module + "Coming Soon" Accounts |
| Admin/HR Back Link | Left nav items | Conditional: shows "Admin Panel" or "Back to HR" based on module |
| Dashboard Link | Left nav items | Always present, highlighted when `$activeContext === 'dashboard'` |
| Context Groups | Left nav items | From `$items`, up to `$maxDesktop` (5) visible, rest in overflow dropdown |
| Company Switcher | Right side | Visible for super_admin/company_admin roles; supports "All Companies" |
| Background Jobs | Right side | Opens drawer panel |
| Locale Switcher | Right side | English/Français/Español (dropdown) |

---

## 3. [`app_general_settings.php`](../../app/Modules/app_general_settings.php) — Current Settings Config

### Structure

```php
return [
    'groups'   => [ /* global settings groups */ ],
    'contexts' => [ /* per-context settings groups */ ],
];
```

### Global Groups (`groups` key)

| Group Key | Label | Icon | Settings |
|---|---|---|---|
| `general` | General | `fas fa-cog` | `timezone` (select), `date_format` (select), `currency` (select) |
| `appearance` | Appearance | `fas fa-palette` | `theme` (select: light/dark/auto) |
| `pagination` | Pagination | `fas fa-table` | `pagination.per_page` (number, 5–100) |

### Context-Specific Groups (`contexts` key)

| Context | Group Key | Label | Settings |
|---|---|---|---|
| `people` | `auto_generation` | Auto-Generation | `auto_gen.employee.employee_number.pattern` (text, with help text) |

### Setting Schema

Each setting is an associative array:

| Field | Type | Required | Description |
|---|---|---|---|
| `key` | `string` | Yes | Dotted key for storage (e.g., `pagination.per_page`) |
| `type` | `string` | Yes | `select`, `number`, or `text` |
| `label` | `string` | Yes | Display label |
| `options` | `array\|string` | For `select` | Array of value→label pairs, or `'timezones'` special string |
| `default` | `mixed` | No | Fallback value |
| `min`/`max` | `int` | For `number` | Range constraints |
| `help` | `string` | No | Help text displayed below input |

### How It's Loaded

In [`ModuleServiceProvider.php`](../../../Libraries/ui-library/src/Providers/ModuleServiceProvider.php:323-324):

```php
if (File::exists("{$modulePath}/app_general_settings.php"))
    $this->mergeConfigFrom("{$modulePath}/app_general_settings.php", "app_general_settings");
```

This means:
- There is a **single** `app_general_settings.php` file in `app/Modules/`
- It is merged as Laravel config key `app_general_settings`
- All settings (global + all contexts) live in one monolithic file
- There is **no per-module settings config** mechanism currently

---

## 4. [`SettingsPanel.php`](../../../Libraries/ui-library/src/Http/Livewire/Settings/SettingsPanel.php) — Livewire Component

### Properties

| Property | Type | Default | Purpose |
|---|---|---|---|
| `$mode` | `string` | `'user'` | `'user'` or `'system'` — determines which model stores settings |
| `$context` | `?string` | `null` | Context slug for filtering context-specific groups |
| `$activeGroup` | `string` | `'general'` | Currently selected settings tab |
| `$groups` | `array` | `[]` | All settings groups (global + context-merged) |
| `$overrides` | `array` | `[]` | User's unsaved/stored override values |
| `$effectiveValues` | `array` | `[]` | Resolved effective values (after inheritance) |
| `$inheritance` | `array` | `[]` | Source of each setting's value (user/context/system) |

### `mount()` Flow

```
mount($mode, $context)
  ├── $this->loadGroups()
  │     ├── Read config('app_general_settings.groups')
  │     ├── If $context set: merge config('app_general_settings.contexts.{context}.groups')
  │     └── Set $this->activeGroup = first group key ← ALWAYS first, no override possible
  └── $this->loadCurrentValues()
        └── For each group → each setting:
              ├── Get effective value via SettingsManager
              ├── Get user's own stored value → $overrides
              └── Resolve inheritance source → $inheritance
```

### Critical Limitation: No Initial Tab Selection

The `mount()` method always sets `$activeGroup` to `array_key_first($this->groups)`. There is **no parameter** to specify an initial active group from the outside. This means:

- When embedded in a page, SettingsPanel always opens to the first tab
- There is no way to deep-link to a specific settings tab
- A `query string` or `mount()` parameter would need to be added for tab targeting

---

## 5. [`settings-panel.blade.php`](../../../Libraries/ui-library/src/Resources/views/livewire/settings/settings-panel.blade.php) — Settings Panel View

### Layout

```
┌──────────────────────────────────────────┐
│ .settings-panel                          │
│ ┌──────────┬─────────────────────────────┐│
│ │ Sidebar  │ Content Area                ││
│ │ (col-3)  │ (col-9)                     ││
│ │          │                             ││
│ │ [Tab 1]  │ ┌───────────────────────┐   ││
│ │ [Tab 2]  │ │ Card: Group Label     │   ││
│ │ [Tab 3]  │ │                       │   ││
│ │          │ │ Setting 1: [input] [✓]│   ││
│ │          │ │ Setting 2: [input] [✓]│   ││
│ │          │ │ Setting 3: [input] [✓]│   ││
│ │          │ └───────────────────────┘   ││
│ └──────────┴─────────────────────────────┘│
└──────────────────────────────────────────┘
```

### Tab Rendering

```blade
@foreach($groups as $groupKey => $group)
    <button wire:click="setActiveGroup('{{ $groupKey }}')"
            class="nav-link text-start {{ $activeGroup === $groupKey ? 'active bg-primary text-white' : 'text-dark' }}">
        <i class="{{ $group['icon'] }} me-2"></i>
        {{ $group['label'] }}
    </button>
@endforeach
```

- **Active tab CSS**: `active bg-primary text-white` (Bootstrap nav-pill style)
- **Inactive tab CSS**: `text-dark`
- **Click handler**: `wire:click="setActiveGroup('{key}')"` — triggers Livewire re-render

### Setting Input Rendering

| Type | Input | Wire Model |
|---|---|---|
| `select` | `<select wire:model="overrides.{key}">` with options loop | `overrides.{key}` |
| `number` | `<input type="number" wire:model="overrides.{key}">` | `overrides.{key}` |
| `text` | `<input type="text" wire:model="overrides.{key}">` | `overrides.{key}` |

### Special Handling: Timezone

```blade
@if (is_string($options) && $options === 'timezones')
    $options = timezone_identifiers_list();
    $options = array_combine($options, $options);
@endif
```

When `options` is the string `'timezones'`, PHP's `timezone_identifiers_list()` dynamically populates the select.

### Inheritance Display

```blade
@if($isOverridden)
    <span class="text-warning">✏️ Overridden (current: {{ $effective }})</span>
@else
    <span class="text-muted">🏗 Inherited from {{ ucfirst($inheritedFrom) }}</span>
@endif
```

Shows whether a setting is overridden by the user or inherited from system/context.

---

## 6. Implementation Changes Analysis

### (a) Split General Settings into Its Own Config

**Current state:** All settings live in one monolithic [`app_general_settings.php`](../../app/Modules/app_general_settings.php) at the `app/Modules/` root.

**Proposed change:** Extract global groups (`general`, `appearance`, `pagination`) into a separate file.

**Files affected:**

| File | Action |
|---|---|
| `app/Modules/app_general_settings.php` | Reduce to only `contexts` key (per-context settings) |
| `app/Modules/app_user_preferences.php` (new) | Contains the `groups` key with `general`, `appearance`, `pagination` |
| [`ModuleServiceProvider.php`](../../../Libraries/ui-library/src/Providers/ModuleServiceProvider.php:323-324) | Add `app_user_preferences` config merge |
| [`SettingsPanel.php`](../../../Libraries/ui-library/src/Http/Livewire/Settings/SettingsPanel.php:37) | Update `loadGroups()` to read from `app_user_preferences.groups` instead of `app_general_settings.groups` |

**Configuration loading in `ModuleServiceProvider`:**

```php
// Register global user preferences settings
if (File::exists("{$modulePath}/app_user_preferences.php"))
    $this->mergeConfigFrom("{$modulePath}/app_user_preferences.php", "app_user_preferences");

// Register per-module/context settings (renamed from app_general_settings)
if (File::exists("{$modulePath}/app_context_settings.php"))
    $this->mergeConfigFrom("{$modulePath}/app_context_settings.php", "app_context_settings");
```

---

### (b) Create Per-Module Context Settings Configs

**Current state:** Context settings are embedded in `contexts.{context_slug}.groups` within the monolithic file.

**Proposed change:** Each module gets its own context settings file.

**Approach A — Module-level files (recommended):**

```
app/Modules/Hr/app_context_settings.php    ← HR-specific context settings
app/Modules/Accounts/app_context_settings.php  ← Future module
```

**Approach B — Single file with all contexts:**

```
app/Modules/app_context_settings.php       ← All context settings, keyed by module
```

**Files affected:**

| File | Action |
|---|---|
| `app/Modules/Hr/app_context_settings.php` (new) | Contains HR context settings (e.g., `people.auto_generation`) |
| [`ModuleServiceProvider.php`](../../../Libraries/ui-library/src/Providers/ModuleServiceProvider.php) | Add per-module config discovery loop |
| [`SettingsPanel.php`](../../../Libraries/ui-library/src/Http/Livewire/Settings/SettingsPanel.php:39) | Update `loadGroups()` to read from new config key |

**SettingsPanel `loadGroups()` change:**

```php
// Before:
$contextGroups = config('app_general_settings.contexts.' . $this->context . '.groups', []);

// After:
$contextGroups = config('app_context_settings.' . $this->context . '.groups', []);
// Or per-module:
$contextGroups = config($this->moduleName . '_context_settings.' . $this->context . '.groups', []);
```

---

### (c) Create a User Preferences Page

**Current state:** No dedicated user preferences page exists. The SettingsPanel exists but there's no page that embeds it for user self-service.

**Proposed structure:**

| File | Purpose |
|---|---|
| `app/Modules/Hr/Resources/views/my-preferences.blade.php` (new) | Page wrapper, similar pattern to `my-profile.blade.php` |
| Route in `app/Modules/Hr/Routes/web.php` | `GET /hr/my-preferences` |
| Embed `SettingsPanel` with `mode="user"` and no context | Shows global user preferences |

**Page template (following `my-profile.blade.php` pattern):**

```blade
@php
    // No employee lookup needed — this is user-level, not employee-level
@endphp

<x-qf::navigation-layout configKey="hr.preferences" context="preferences" moduleName="hr" :overrides="[
    'top_bar' => ['enabled' => true],
    'breadcrumb' => ['enabled' => false],
    'title' => ['enabled' => false],
    'titleRow' => ['enabled' => false],
    'context_menu' => ['enabled' => false],
]">
    @livewire('qf.settings-panel', ['mode' => 'user', 'context' => null])
</x-qf::navigation-layout>
```

**Route definition (following existing web.php patterns):**

```php
Route::get('my-preferences', function () {
    return view('hr::my-preferences');
})->name('my-preferences');
```

---

### (d) Add a Preferences Link to the Profile Dropdown

**Current state:** The profile dropdown in [`top-nav.blade.php`](../../../Libraries/ui-library/src/Resources/views/livewire/navs/top-nav.blade.php:292-331) has:
1. "My Profile" → `/hr/my-profile`
2. "Edit My User Account" → `/hr/my-account`
3. "Take the Tour" → `route('tour.restart')`
4. Logout

**Proposed change:** Add a "Preferences" link between "My Profile" and "Edit My User Account".

**Specific insertion point:** Line 304, after the "My Profile" `<li>` block and before "Edit My User Account".

**New markup to insert:**

```blade
<li>
    @auth
        <a class="dropdown-item border-radius-md mb-1" href="/hr/my-preferences">
            <i class="fas fa-sliders-h me-2 opacity-6 text-sm"></i> Preferences
        </a>
    @endauth
</li>
```

**Alternatively**, if you want to use a named route:

```blade
<a class="dropdown-item border-radius-md mb-1" href="{{ route('my-preferences') }}">
```

**Updated profile dropdown structure:**

```
┌─────────────────────────────────┐
│ User Name                       │
├─────────────────────────────────┤
│ 🛠 My Profile        → /hr/my-profile      │
│ ⚙ Preferences        → /hr/my-preferences  │  ← NEW
│ ✏️ Edit My User Account → /hr/my-account   │
│ ▶ Take the Tour      → route('tour.restart')│
├─────────────────────────────────┤
│ 🚪 Logout                        │
└─────────────────────────────────┘
```

---

### (e) Make SettingsPanel Highlight a Specific Tab When Opened from Sidebar

**Current state:** [`SettingsPanel::mount()`](../../../Libraries/ui-library/src/Http/Livewire/Settings/SettingsPanel.php:27-33) always sets `$activeGroup` to the first group key. There is no way to specify a target tab.

**Required changes to `SettingsPanel.php`:**

1. **Add an `$initialGroup` parameter to `mount()`:**

```php
public function mount(string $mode = 'user', ?string $context = null, ?string $initialGroup = null)
{
    $this->mode = $mode;
    $this->context = $context;
    $this->loadGroups();
    
    // Override activeGroup if a valid initial group is specified
    if ($initialGroup && isset($this->groups[$initialGroup])) {
        $this->activeGroup = $initialGroup;
    }
    
    $this->loadCurrentValues();
}
```

2. **Pass `initialGroup` from the parent page:**

```blade
@livewire('qf.settings-panel', [
    'mode' => 'user', 
    'context' => 'people',
    'initialGroup' => 'auto_generation'
])
```

3. **For sidebar deep-linking**, the sidebar link would pass the target group:

```blade
{{-- In a sidebar or context menu --}}
<a href="/hr/settings?group=auto_generation">
    Auto-Generation Settings
</a>
```

Then in the page controller/view:

```blade
@livewire('qf.settings-panel', [
    'mode' => 'user', 
    'context' => 'people',
    'initialGroup' => request()->query('group')
])
```

---

## Summary: Complete Dependency Map

```
┌──────────────────────────────────────────────────────────────────┐
│                    ModuleServiceProvider                          │
│  registerModuleConfig() merges:                                  │
│  • app/Modules/app_general_settings.php → config('app_general_   │
│    settings')                                                     │
└──────────────────────┬───────────────────────────────────────────┘
                       │
         ┌─────────────┼──────────────┐
         ▼             ▼              ▼
┌─────────────────┐ ┌──────────────┐ ┌──────────────────────┐
│ SettingsPanel   │ │ TopNav       │ │ my-profile.blade.php │
│ (Livewire)      │ │ (Livewire)   │ │ (Blade view)         │
│                 │ │              │ │                      │
│ reads config()  │ │ profile      │ │ renders              │
│ loads groups    │ │ dropdown:    │ │ qf.employee-detail   │
│ loads values    │ │ • /hr/my-    │ │ in navigation-       │
│ via Settings    │ │   profile    │ │ layout               │
│ Manager         │ │ • /hr/my-    │ │                      │
│                 │ │   account    │ │                      │
│ Renders tabs    │ │ • tour       │ │                      │
│ via settings-   │ │ • logout     │ │                      │
│ panel.blade.php │ │              │ │                      │
└─────────────────┘ └──────────────┘ └──────────────────────┘
```

---

## Todo Checklist (for Implementation Phase)

- [ ] Split `app_general_settings.php` into `app_user_preferences.php` (global groups) and `app_context_settings.php` (per-context groups)
- [ ] Update `ModuleServiceProvider::registerModuleConfig()` to merge both new config files
- [ ] Create per-module context settings configs (e.g., `app/Modules/Hr/app_context_settings.php`)
- [ ] Add `$initialGroup` parameter to `SettingsPanel::mount()` for tab targeting
- [ ] Create `my-preferences.blade.php` view in Hr module
- [ ] Add `/hr/my-preferences` GET route in Hr module's `web.php`
- [ ] Add "Preferences" link to profile dropdown in `top-nav.blade.php`
- [ ] Add prefixed context settings group for sidebar deep-linking
- [ ] Test: SettingsPanel highlights correct tab when `initialGroup` is passed
- [ ] Test: Profile dropdown renders new Preferences link correctly
