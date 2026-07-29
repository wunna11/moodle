# Moodle Exam & GPA Automation Complete Setup Guide

Moodle တွင် စာမေးပွဲများနှင့် အမှတ်ပေးစနစ်များ (Grading System/GPA Calculation) ကို Automatic တွက်ချက်နိုင်ရန် စနစ်တကျ Setup ပြုလုပ်နည်း Guide ဖြစ်ပါသည်။

---

## Part 1: Automated Exams & Quizzes (အလိုအလျောက် စာမေးပွဲစနစ်)

### Step 1: Create an Exam / Quiz
1. Course ထဲတွင် **Edit mode** ကို **ON** ပေးပါ။
2. **`+ Add an activity or resource`** ကို နှိပ်ပြီး **`Quiz`** ကို ရွေးချယ်ပါ။
3. **General:** Quiz Name (ဥပမာ - *Midterm Examination*) ကို ဖြည့်ပါ။
4. **Timing:** 
   * **Open/Close the quiz:** စာမေးပွဲ စမည့်ရက်/ပြီးမည့်ရက် သတ်မှတ်ပါ။
   * **Time limit:** ဖြေဆိုခွင့်ကြာချိန် (ဥပမာ - 60 mins) သတ်မှတ်ပါ။
   * **When time expires:** `Open attempts are submitted automatically` ဟု ထားပါ (အချိန်ပြည့်ပါက Automatic Submit ဖြစ်စေရန်)။
5. **Grade:**
   * **Grade category:** သက်ဆိုင်ရာ Grade Category ကို ရွေးပါ။
   * **Grade to pass:** အောင်မှတ် (ဥပမာ - 50) သတ်မှတ်ပါ။
   * **Attempts allowed:** ဖြေဆိုခွင့်အကြိမ်ရေ (ဥပမာ - `1` ကြိမ်) သတ်မှတ်ပါ။
6. **Review options:** စာမေးပွဲဖြေဆိုနေစဉ်နှင့် ဖြေဆိုပြီးချိန်တွင် ကျောင်းသားများ အဖြေမှန်/အမှတ် တန်းကြည့်ခွင့် ပြ/မပြ သတ်မှတ်ပါ။
7. **`Save and display`** ကို နှိပ်ပါ။

### Step 2: Add Auto-Graded Questions
1. Quiz ထဲသို့ ဝင်ရောက်ပြီး **`Add question`** ကို နှိပ်ပါ။
2. Auto-grading ထောက်ပံ့သော မေးခွန်းပုံစံများကို ရွေးချယ်ပါ:
   * **Multiple Choice** (အမှန်ရွေး)
   * **True/False** (မှန်/မှား)
   * **Matching** (တွဲဖက်)
   * **Short Answer** (အတိုချုံး ဖြေ)
3. မေးခွန်း အဖြေမှန်များတွင် **Grade (100%)** သတ်မှတ်ပေးခဲ့ပါ။
4. **Question Bank** သို့မဟုတ် **Random questions** များကို အသုံးပြုပါက ကျောင်းသားတစ်ဦးစီအတွက် မေးခွန်းများ Automatic လဲလှယ် (Shuffle) ပေးမည် ဖြစ်ပါသည်။

---

## Part 2: GPA & Automated Grade Calculation (GPA တွက်ချက်မှုစနစ်)

Moodle Gradebook ကို အသုံးပြု၍ Course ရမှတ်များနှင့် GPA များကို Automated တွက်ချက်နိုင်ပါသည်။

### Step 1: Setup Gradebook Categories & Weights
1. Course Top Menu မှ **`Grades`** > **`Gradebook setup`** သို့ သွားပါ။
2. အကဲဖြတ်မှု အမျိုးအစားအလိုက် Category များ ခွဲခြားရန် အောက်ခြေရှိ **`Add category`** ကို နှိပ်ပါ (ဥပမာ - *Quizzes (20%)*, *Assignments (30%)*, *Final Exam (50%)*)။
3. **Aggregation Method:** 
   * Weighted Average (အလေးချိန်အလိုက် တွက်ချက်ရန်) သို့မဟုတ်
   * Natural (ရမှတ် စုစုပေါင်းအလိုက် တွက်ချက်ရန်) ကို ရွေးချယ်ပါ။
4. Category တစ်ခုချင်းစီအတွက် Weight (ရာခိုင်နှုန်း) များကို ဖြည့်စွက်ပေးပြီး **`Save changes`** ကို နှိပ်ပါ။

### Step 2: Configure GPA / Letter Grade Scales
Moodle မှ ရမှတ် ရာခိုင်နှုန်းအပေါ် မူတည်၍ Grade Point (4.0 Scale) သို့မဟုတ် Letter Grade (A, B, C, D, F) သို့ Auto ပြောင်းလဲပေးနိုင်ပါသည်။

1. Gradebook ထဲရှိ Dropdown Menu မှ **`Letters`** (သို့မဟုတ် *Grade letters*) သို့ သွားပါ။
2. **`Edit grade letters`** ကို နှိပ်ပါ။
3. မိမိ ကျောင်း/တက္ကသိုလ်၏ GPA Scale အတိုင်း သတ်မှတ်ပေးပါ:
   * **Letter Grade** / **Grade Point (GPA)** / **Lowest Boundary (%)**
   * *A (4.0)* : 85% to 100%
   * *B (3.0)* : 70% to 84%
   * *C (2.0)* : 55% to 69%
   * *D (1.0)* : 40% to 54%
   * *F (0.0)* : 0% to 39%
4. **`Save changes`** ကို နှိပ်ပါ။

### Step 3: Enable GPA Display in Gradebook
1. **`Gradebook setup`** > **`Course grade settings`** သို့ သွားပါ။
2. **Grade display type:** တွင် `Real (Letter)` သို့မဟုတ် `Letter (Percentage)` ကို ရွေးပေးပါ (ကျောင်းသားများကို ရမှတ်နှင့်အတူ GPA / Letter Grade အလိုအလျောက် ပူးတွဲပြသပေးမည် ဖြစ်သည်)။

---

## Part 3: Automated Transcripts & GPA Reports

ကျောင်းသားများ၏ Course အလိုက် Total GPA ကို စနစ်တကျ Exastud / Custom Reports များမှတစ်ဆင့် Automate ထုတ်ယူနိုင်ပါသည်။

1. **Course Completion Setup:** 
   * Course Settings မှ **`Course completion`** ကို ဝင်ရောက်ပြီး ပြီးမြောက်ရမည့် Quiz/Assignment Grade Threshold များကို သတ်မှတ်ပါ။
2. **Exporting Cumulative GPA:**
   * **Grades** > **Export** > **Excel spreadsheet** သို့ သွားပါ။
   * လိုအပ်သော Category ရမှတ်များနှင့် **Course total (Letter/GPA)** များကို ရွေးချယ်၍ Download ဆွဲယူနိုင်ပါသည်။