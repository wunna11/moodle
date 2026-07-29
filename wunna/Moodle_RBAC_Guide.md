# Moodle Role Based Access Control (RBAC) Complete Setup & Management Guide

Moodle တွင် အသုံးပြုသူများ၏ ရာထူး/အခန်းကဏ္ဍအလိုက် (Roles) စနစ်တွင်း လုပ်ဆောင်နိုင်သည့် လုပ်ပိုင်ခွင့်များ (Permissions/Capabilities) ကို စနစ်တကျ သတ်မှတ်စီမံခန့်ခွဲနိုင်သော **Role Based Access Control (RBAC)** အသုံးပြုနည်း Guide ဖြစ်ပါသည်။

---

## Overview of RBAC Architecture in Moodle

Moodle ၏ RBAC စနစ်တွင် အဓိက ဒေါက်တိုင် (၃) ခု ပါဝင်ပါသည်:

1. **Role (ရာထူး):** စနစ်တွင်း သတ်မှတ်ထားသော အမည် (ဥပမာ - *Teacher, Student, Manager, Course Creator*)။
2. **Context (နယ်ပယ်/အဆင့်):** ရာထူးတစ်ခု၏ သက်ရောက်မှု အတိုင်းအတာ (ဥပမာ - *System Level, Category Level, Course Level, Activity Level*)။
3. **Capability & Permission (လုပ်ပိုင်ခွင့်):** သီးသန့် လုပ်ဆောင်ချက်တစ်ခုကို ခွင့်ပြု/ပိတ်ပင်ခြင်း (ဥပမာ - `moodle/course:create`, `mod/quiz:attempt`)။

---

## Part 1: Default Moodle Roles & Contexts

### Built-in System Roles
| Role Name | Default Context | Description / လုပ်ပိုင်ခွင့် |
| :--- | :--- | :--- |
| **Administrator** | System | စနစ်တစ်ခုလုံး၏ Settings, Plugins နှင့် Users များကို အပြည့်အဝ စီမံနိုင်သူ။ |
| **Manager** | System / Category | System Admin မဟုတ်သော်လည်း Category အလိုက် Course များနှင့် User များကို စီမံနိုင်သူ။ |
| **Course Creator** | Category | Course သစ်များ စတင် ဖန်တီးနိုင်သူ။ |
| **Teacher (Editing)** | Course | Course ထဲတွင် သင်ရိုး၊ Quiz, Assignment များကို ပြင်ဆင်/အမှတ်ပေးနိုင်သူ။ |
| **Non-editing Teacher** | Course | သင်ရိုးများကို ဝင်ရောက်ကြည့်ရှုပြီး အမှတ်ပေးရုံသာ ပြုလုပ်နိုင်သူ (ပြင်ဆင်ခွင့်မရှိ)။ |
| **Student** | Course | သင်ရိုးများကို လေ့လာခြင်း၊ စာမေးပွဲဖြေဆိုခြင်းနှင့် Assignment တင်ခြင်းများသာ ပြုလုပ်နိုင်သူ။ |
| **Guest** | System / Course | အကောင့်မရှိဘဲ ခေတ္တ ဝင်ရောက် ကြည့်ရှုသူ (မေးခွန်းဖြေခွင့်/စာတိုင်ခွင့် မရှိ)။ |

---

## Part 2: Assigning Roles (အသုံးပြုသူများအား ရာထူး သတ်မှတ်ပေးခြင်း)

### 1. Assigning Roles at Course Level (Course တစ်ခုတည်းအတွက် ရာထူးပေးခြင်း)
1. သက်ဆိုင်ရာ Course ထဲသို့ ဝင်ရောက်ပါ။
2. **`Participants`** Tab သို့ သွားပါ။
3. **`Enrol users`** ကို နှိပ်ပါ။
4. User/Cohort ကို ရှာဖွေပြီး **`Assign role`** Dropdown Menu မှ မိမိ ပေးလိုသော Role (ဥပမာ - *Student* သို့မဟုတ် *Teacher*) ကို ရွေးချယ်၍ Enrol ပြုလုပ်ပါ။

### 2. Assigning System / Category Roles (စနစ်/ကဏ္ဍအလိုက် ရာထူးပေးခြင်း)
1. **`Site administration > Users > Permissions > Assign system roles`** သို့ သွားပါ။
2. ပေးလိုသော Role (ဥပမာ - *Manager*) ကို ရွေးပါ။
3. ဘက်ယာဘက်အကွက်မှ User ကို ရွေးချယ်ပြီး **`Add`** နှိပ်၍ Assign လုပ်ပါ။

---

## Part 3: Creating Custom Roles (စိတ်ကြိုက် ရာထူးသစ် ဖန်တီးခြင်း)

ကျောင်း/တက္ကသိုလ်၏ သီးသန့် မူဝါဒများအတွက် Custom Role သစ်များ (ဥပမာ - *Exam Monitor, Parent/Guardian, Department Head*) ဖန်တီးနိုင်ပါသည်။

### Step-by-Step Custom Role Creation
1. **`Site administration > Users > Permissions > Define roles`** သို့ သွားပါ။
2. အောက်ခြေရှိ **`Add a new role`** ကို နှိပ်ပါ။
3. **Use role or archetype:** အခြေခံယူလိုသော Role (ဥပမာ - *Teacher* သို့မဟုတ် *No archetype*) ကို ရွေးပြီး **`Continue`** နှိပ်ပါ။
4. **General Settings:**
   * **Short name:** (ဥပမာ - `exam_monitor`)
   * **Custom full name:** (ဥပမာ - *Exam Monitor*)
   * **Context types where this role may be assigned:** ရာထူး သက်ရောက်မည့် အဆင့်ကို Check ခြစ်ပါ (ဥပမာ - *Course, Activity*)။
5. **Capability Permissions:**
   * လိုအပ်သော Capability များကို **Allow** (ခွင့်ပြု), **Prevent** (တားမြစ်), သို့မဟုတ် **Prohibit** (တင်းကျပ်စွာ ပိတ်ပင်) ရွေးချယ်ပါ။
6. အောက်ခြေရှိ **`Create this role`** ကို နှိပ်ပါ။

---

## Part 4: Overriding Permissions (လုပ်ပိုင်ခွင့်များ သီးသန့် ပြင်ဆင်ခြင်း)

Course တစ်ခု သို့မဟုတ် Activity တစ်ခုတည်းတွင် Role တစ်ခု၏ Permission ကို သီးသန့် ပြောင်းလဲလိုပါက Override ပြုလုပ်နိုင်ပါသည်။

### Example: Specific Quiz တွင် Student များ အချင်းချင်း အဖြေမမြင်ရအောင် ပြုလုပ်ခြင်း
1. သက်ဆိုင်ရာ Activity (ဥပမာ - *Quiz* သို့မဟုတ် *Forum*) ထဲသို့ ဝင်ပါ။
2. **`Settings > Permissions`** သို့ သွားပါ။
3. ရှာဖွေလိုသော Capability ကို Search ရိုက်ပါ (ဥပမာ - `mod/forum:rate`)။
4. Thresh/Permission ဇယားတွင် သက်ဆိုင်ရာ Role ဘေးရှိ **`+`** (Allow) သို့မဟုတ် **`X`** (Prevent) ကို နှိပ်၍ ပြင်ဆင်ပါ။

---

## Part 5: Permission Conflicts & Resolution Rules

Moodle တွင် Permission များ ပဋိပက္ခဖြစ်ပါက အောက်ပါ ဦးစားပေး အစဉ်အတိုင်း ဆုံးဖြတ်ပါသည်။