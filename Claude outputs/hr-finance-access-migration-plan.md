# local_hrdepartment / local_financedepartment — Access Model Migration Plan

**ရည်ရွယ်ချက်:** "department name string-matching + blanket employee-record grant" ပုံစံကနေ Moodle ရဲ့ native role/capability engine ကို တကယ်သုံးတဲ့ ပုံစံဆီ **incremental, non-breaking** အနေနဲ့ ရွှေ့ဖို့။ Phase တစ်ခုချင်းစီက ကိုယ်ပိုင် deploy/verify/rollback လုပ်နိုင်ပြီး၊ feature work တွေကို ရပ်ထားစရာ မလိုပါဘူး — အားလပ်ချိန်မှာ တစ်ခုချင်း လုပ်လို့ရပါတယ်။

---

## လက်ရှိအခြေအနေ (baseline, 2026-09-14)

- `local_hrdepartment\access_manager::can_manage($capability)` — `is_siteadmin()` OR (`hrdep_employee` Staff record, department name = "HR" exact-ish string match) OR `has_capability($capability, ...)`
- `local_financedepartment\access_manager::can_manage($capability)` — ပုံစံအတူတူပါပဲ, department name = "Finance"
- `department_manager::PROTECTED_NAMES = ['HR', 'Finance']` — name collision/rename/delete ကို code-level manual guard တစ်ခုတည်းနဲ့ ကာကွယ်ထားတယ်
- `managedepartments` တစ်ခုတည်းက ဒီ blanket grant ကနေ ချန်ထားပြီး pure `has_capability()` — ဒါက migration ပြီးရင် ရောက်ချင်တဲ့ target ပုံစံရဲ့ live example ဖြစ်တယ်
- Navigation (`primary_extend` hook) ၂ ခု၊ `index.php` landing logic တွေက capability/employee-record check ကို သီးခြားစီ ထပ်ခေါ်နေရတယ်
- `local_financedepartment`'s Step 7.12 (`access_summary_manager`, `pages/access/index.php`) — capability ကို role-alic သတ်မှတ်ထားတာ vs `hrdep_employee` ကနေ တကယ် access ရတဲ့သူ ကွာနေမလား စစ်ဖို့ tool အနေနဲ့ ရှိပြီးသား, migration အတွက် reuse လုပ်လို့ရမယ်

---

## Target ပုံစံ

- `hrdep_employee` ဆက်ရှိမယ် — HR business record (hire date, position, department, status) အနေနဲ့ ပဲ ဆက်သုံးမယ်, ဒါကို ဖျက်စရာမလိုဘူး
- Employee record ရဲ့ department က **Moodle system-context role assignment ကို ရလဒ် (derived state) အနေနဲ့ drive** လုပ်မယ် — cohort layer မလိုပါဘူး, `role_assign()`/`role_unassign()` ကို employee record ရဲ့ create/update/department-change/delete point တွေကနေ တိုက်ရိုက် ခေါ်မယ် (ဒီ project ရဲ့ create/update ဟာ `staff_manager`/`lecturer_manager` ထဲမှာ centralized ဖြစ်နေပြီးသားလို့ hook ထည့်ဖို့ လွယ်တယ်)
- `access_manager::can_manage()` ရဲ့ internal implementation ကို `has_capability()` တစ်ခုတည်းပဲ ဖြစ်အောင် ပြောင်းမယ် (public method signature မပြောင်းဘူး — caller code တစ်ခုမှ ပြင်စရာမလိုဘူး)
- Approve-vs-manage လို capability pair တွေကို role level မှာ ကွဲကွဲပြားပြား grant/prevent သတ်မှတ်နိုင်အောင် custom role ၂ ခု (HR Staff, Finance Staff) ဆောက်မယ်
- `PROTECTED_NAMES` guard ကို ဆက်ထားမယ် (defense-in-depth) — ဒါပေမယ့် blast radius ကျဉ်းသွားမယ် (name ပြောင်းလိုက်ရင် access ချက်ချင်း မပျက်တော့ဘူး, employee-id-based role assignment ကို မထိဘူး)

---

## Phase 0 — Prep (behavior change မရှိ, risk 0)

1. Site administration → Users → Define roles မှာ custom role ၂ ခု ဆောက်မယ်:
   - `hrdepartmentstaff` (shortname) — HR ရဲ့ manage* capability အားလုံးကို CAP_ALLOW
   - `financedepartmentstaff` — Finance ရဲ့ manage* capability အားလုံးကို CAP_ALLOW
   - Assignable context: System
2. `local_hrdepartment` နဲ့ `local_financedepartment` ရဲ့ `db/access.php` ထဲက capability တွေမှာ archetype default ကို ပြောင်းစရာမလိုဘူး — role UI ကနေ manual ချထားရုံပါ
3. Verify: ဒီ role ကို user တစ်ယောက်ကို manual assign လုပ်ကြည့်ပြီး `has_capability()` က true ပြန်လား စစ်မယ် (ဒီအဆင့်မှာ `can_manage()` ကို ဘာမှ မပြောင်းသေးဘူး, ဒါကြောင့် production ပေါ်မှာ ဘာမှ ပြောင်းမသွားဘူး)

**Rollback:** role ကို ဖျက်လိုက်ရုံ၊ code ဘာမှ မထိထားလို့ zero risk။

---

## Phase 1 — Dual-write: employee record → role assignment (additive, ဘေးမှာထပ်ထည့်ရုံ)

1. `staff_manager::create()` / `update()` / `set_employment_status()` (နှင့် `lecturer_manager` ရဲ့ အလားတူ method) ထဲမှာ, employee record ရဲ့ department ID ကို department name ("HR"/"Finance") နဲ့ resolve ပြီး, ဆီလျော်တဲ့ role ("hrdepartmentstaff"/"financedepartmentstaff") ကို system context မှာ `role_assign()` ခေါ်မယ်; status = terminated ဒါမှမဟုတ် department ပြောင်းရင် `role_unassign()` ခေါ်မယ်
2. `department_manager` ရဲ့ delete/rename path တွေအတွက်လည်း သက်ဆိုင်ရာ employee record အားလုံးကို loop ပတ်ပြီး role sync ထပ်ခေါ်ပေးမယ် (rename ဖြစ်ရင် name-key က ပြောင်းရုံပါ, employeeid-based assignment ကတော့ မထိဘူး)
3. **Backfill script တစ်ခု ရေးမယ်** (`cli/backfill_department_roles.php`, Moodle CLI script, plugin codebase ထဲမှာ မပါဘဲ standalone) — လက်ရှိ `hrdep_employee` row အားလုံးကို loop ပတ်ပြီး role assignment ကို retroactively ဆောက်ပေးမယ်
4. **ဒီ phase မှာ `can_manage()` ကို ဘာမှ မပြောင်းသေးဘူး** — role assignment က "shadow"/parallel state ပဲ ဖြစ်နေမယ်, production access ကို ဘာမှ မထိသေးဘူး
5. Verify: `access_summary_manager::get_capability_summary()` (Step 7.12, financedepartment) ကို HR plugin အတွက်လည်း တူတူ ရေးမယ် (သို့) ရှိပြီးသား Finance ဗားရှင်းကို widen လုပ်မယ်၊ role-based grant list ကို `hrdep_employee`-derived list နဲ့ ယှဉ်ကြည့်ပြီး **တူညီမှန်ကန်ကြောင်း confirm** လုပ်မယ်

**Rollback:** role_assign() call site တွေကို revert လုပ်လိုက်ရုံ၊ shadow role assignment တွေ ကျန်နေတာက harmless (can_manage() က မကိုးစားသေးလို့)

**Risk:** low — dual-write ဖြစ်လို့ existing behavior ကို လုံးဝ မထိဘူး

---

## Phase 2 — Cutover can_manage() to has_capability()-only

1. Phase 1 ရဲ့ verify step က clean ဖြစ်ပြီဆိုရင် — `access_manager::can_manage($capability)` ရဲ့ body ကို:
   ```php
   public static function can_manage(string $capability, int $userid = 0): bool {
       return has_capability($capability, \context_system::instance(), $userid);
   }
   ```
   လို့ ပြောင်းမယ် (`is_siteadmin()` စစ်ဖို့ မလိုတော့ဘူး — site admin တိုင်း capability အားလုံး default ရှိပြီးသား)
2. `can_access_hr_department()` / `can_access_finance_department()` ကိုလည်း "holds any manage* capability OR is_siteadmin" ပုံစံအဖြစ် ပြန်ရေးမယ် (nav/index.php landing check တွေအတွက်)
3. Staging/test site တစ်ခုမှာ အရင် deploy ပြီး regression စစ်မယ် — Phase 1 ရဲ့ backfill script ကို run ပြီးမှသာ ဒီ phase ကို production ပေါ် တင်ရမယ် (မဟုတ်ရင် access ပျက်မယ်)
4. Production deploy အတွက် sequence: backfill script run → verify (access_summary tool) → code deploy → cache purge

**Rollback:** `can_manage()` ကို Phase-0 အရင် ကုဒ်ဆီ ပြန်ပြောင်းလို့ရတယ် (git revert) — role assignment data တွေကတော့ ကျန်နေမှာမို့ ချက်ချင်း ပြန်ပြင်လို့ရမယ်

**Risk:** medium — access ချက်ချင်း ပြောင်းတဲ့ phase ပဲ ဖြစ်လို့, backfill script မှန်ကန်စွာ run ပြီးမှသာ ဒီ step ကို လုပ်ရမယ်

---

## Phase 3 — Granular capability split (self-approval bug class ကို root ကနေ ပိတ်ခြင်း)

1. `managescholarships`/`approvescholarships` (Finance) စတဲ့ capability pair တွေကို "hrdepartmentstaff"/"financedepartmentstaff" role တစ်ခုတည်းမှာ အကုန် CAP_ALLOW ပေးမယ့်အစား, role ၂ ခု ခွဲမယ် — ဥပမာ `financestaff` (request/submit-level) vs `financeapprover` (approve-level)
2. **ဒါက optional/later phase ပါ** — Phase 2 ပြီးရင် system အလုပ်လုပ်ပြီးသားမို့, ဒီ phase ကို လိုအပ်မှ (approve/manage ခွဲချင်တဲ့ organizational need ပေါ်လာမှ) လုပ်ရင်ရပါတယ်
3. လုပ်မယ်ဆိုရင် — requestedby-vs-reviewer manual guard code တွေ (self-approval fix အဟောင်းများ) ကို role-level prevention က ကျော်သွားနိုင်လို့ တစ်ခါတည်း ဖျက်မနေဘဲ, defense-in-depth အနေနဲ့ ဆက်ထားရုံ လုံလောက်ပါတယ်

---

## Phase 4 — Navigation/index.php cleanup

1. Phase 2 ပြီးရင် `primary_extend.php` hook (HR နှင့် Finance) နှစ်ခုစလုံးက `access_manager::can_manage()`/capability check တွေကိုပဲ တိုက်ရိုက် ခေါ်နေတာမို့ ကုဒ်ဘာမှ ပြောင်းစရာ မလိုတော့ဘူး (already routes through the shared method)
2. `can_view_navigation_entry()` (Finance) ကဲ့သို့ shared method ကို HR plugin ဘက်မှာလည်း တစ်ခုတည်း consolidate လုပ်ထားရင် ပိုကောင်းမယ် — nav hook ၂ ခု logic ကွဲနေတဲ့ bug class ကို ထပ်မဖြစ်အောင်

---

## အကျဉ်းချုပ် — ဘာတွေ ပြောင်းမလဲ / ဘာတွေ မပြောင်းဘူး

| အရာ | ပြောင်းမလား |
|---|---|
| `hrdep_employee` table၊ department field | **မပြောင်းဘူး** — HR/Finance business data အနေနဲ့ ဆက်ရှိမယ် |
| `PROTECTED_NAMES` guard | **ဆက်ထားမယ်** — defense-in-depth, ဒါပေမယ့် blast radius ကျဉ်းသွားမယ် |
| `can_manage()` public method signature | **မပြောင်းဘူး** — caller (page/form) code တစ်ခုမှ ပြင်စရာမလို |
| `can_manage()` internal implementation | **ပြောင်းမယ်** — employee-record query → `has_capability()` |
| Department name string-match | **access decision အနေနဲ့ မသုံးတော့ဘူး** — role assignment (employeeid-keyed) ကသာ decision ပြုလုပ်မယ် |
| `managedepartments` ရဲ့ ခွဲထားတဲ့ pattern | **တစ်ခြား capability တွေလည်း ဒီပုံစံ ရောက်လာမယ်** |

---

## Sequencing အကြံပြုချက်

Phase 0-1 (prep + dual-write) ကို session တစ်ခုအတွင်း အဆင်ပြေတဲ့အချိန် လုပ်လို့ရပါတယ် — risk 0 ဖြစ်လို့။ Phase 2 (cutover) ကိုတော့ traffic နည်းတဲ့ window (ညအချိန်/စနေ-တနင်္ဂနွေ) မှာ backfill script run ပြီးမှ deploy လုပ်ဖို့ အကြံပြုပါတယ်။ Phase 3-4 ကတော့ urgent မဟုတ်ဘူး — organizational need ပေါ်မှ (သို့) code cleanup pass တစ်ခုအနေနဲ့ လုပ်ရင်ရပါတယ်။

Deploy လုပ်ချင်ရင် ပြောပါ — Phase 0 ကနေ စပြီး တစ်ဆင့်ချင်း implement လုပ်ပေးနိုင်ပါတယ်။
