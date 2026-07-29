# Moodle Attendance Feature Complete Setup Guide

Moodle တွင် Attendance Activity (ခွင့်/ကျောင်းတက် စာရင်း) ကို စနစ်တကျ Setup ပြုလုပ်ပြီး အသုံးပြုနိုင်ရန် Guide ဖြစ်ပါသည်။

---

## Step 1: Add Attendance Activity to Course
1. Moodle Course ထဲသို့ ဝင်ရောက်ပါ။
2. **Edit mode** (သို့မဟုတ် *Turn editing on*) ကို **ON** ပေးပါ။
3. လိုအပ်သော Section အောက်ရှိ **`+ Add an activity or resource`** ကို နှိပ်ပါ။
4. **`Attendance`** Activity ကို ရွေးချယ်ပါ။
5. **Name** (ဥပမာ - *Class Attendance*) နှင့် Grade Settings များကို ဖြည့်စွက်ပြီး **`Save and display`** ကို နှိပ်ပါ။

---

## Step 2: Create Class Sessions
1. Attendance Activity ထဲရှိ **`Add session`** Tab သို့ သွားပါ။
2. **Date & Time:** အတန်းချိန်၏ Date နှင့် Time Range ကို သတ်မှတ်ပါ (ဥပမာ - 09:00 to 10:00)။
3. **Multiple Sessions (အပတ်စဉ် အတန်းများအတွက်):**
   * **`Repeat the session above as follows`** ကို Check ခြစ်ပါ။
   * အတန်းရှိသည့် ရက်များကို ရွေးပါ (ဥပမာ - Monday, Wednesday)။
   * **`Repeat every`** တွင် `1 week` ဟု ထားပါ၊ **`Repeat until`** တွင် အတန်းပြီးဆုံးမည့် ရက်စွဲကို ထည့်ပေးပါ။

---

## Step 3: Attendance Marking Methods

### Method A: Teacher Manual Marking (ဆရာ/မ ကိုယ်တိုင် မှတ်ခြင်း)
* **Student recording** ကို Uncheck ထားပါ။
* အတန်းချိန်တွင် **Sessions** Tab သို့သွားပြီး သက်ဆိုင်ရာ ရက်၏ **Play (▶️) Icon / Take attendance** ကို နှိပ်ပါ။
* ကျောင်းသားတစ်ယောက်ချင်းစီအတွက် **P (Present)**, **A (Absent)**, **L (Late)**, **E (Excused)** ရွေးပေးပြီး Save လုပ်ပါ။

### Method B: Student Self-Marking via QR Code/Password (ကျောင်းသားများကိုယ်တိုင် မှတ်ခြင်း)
1. Session ဆောက်စဉ် **`Student recording`** Section အောက်သို့ သွားပါ။
2. **`Allow students to record own attendance`** ကို Check ခြစ်ပါ။
3. **`Include QR code`** သို့မဟုတ် **`Random password`** ကို Toggle **ON** ပေးပါ။
4. အတန်းထဲတွင် Moodle မှ ထွက်လာသော QR Code သို့မဟုတ် Password ကို ပြပေးထားပြီး ကျောင်းသားများက Mobile Phone မှတစ်ဆင့် ကိုယ်တိုင် Attendance နှိပ်နိုင်ပါသည်။

---

## Step 4: Customize Status Scale (Optional)
1. **`Status set`** Tab သို့ သွားပါ။
2. Default ပါဝင်သော P, A, L, E များ၏ Description နှင့် Point/Grade များကို မိမိ ကျောင်း၏ Policy အလိုက် ပြင်ဆင်ပြီး **`Update`** ကို နှိပ်ပါ။

---

## Step 5: Export Attendance Reports
1. **`Report`** Tab တွင် ကျောင်းသားများ၏ Attendance ရာခိုင်နှုန်းကို စစ်ဆေးနိုင်ပါသည်။
2. **`Export`** Tab မှတစ်ဆင့် **Excel (.xlsx)** သို့မဟုတ် **CSV** format ဖြင့် Download ဆွဲယူနိုင်ပါသည်။