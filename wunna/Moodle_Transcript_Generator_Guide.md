# Moodle Transcript Generator (Enrollments & Transcripts) Setup Guide

Moodle တွင် ကျောင်းသားများ၏ သင်တန်းတက်ရောက်မှု စာရင်းများ (Enrollments) နှင့် ရမှတ် စာရွက်စာတမ်း (Transcripts / Academic Records) များကို စနစ်တကျ Auto-generate ထုတ်ယူနိုင်ရန် Guide ဖြစ်ပါသည်။

---

## Overview

Moodle Core တွင် သီးသန့် Transcript Generator Button တစ်ခုတည်း ပါဝင်သည်မဟုတ်ဘဲ **Enrollment Data** + **Gradebook Records** + **Reporting/Certificate Plugins** (ဥပမာ - Exastud, Custom Certificate, သို့မဟုတ် Configurable Reports) များကို ပေါင်းစပ်၍ Transcript ထုတ်ယူရခြင်းဖြစ်ပါသည်။

---

## Step 1: Manage Student Enrollments (ကျောင်းသားများ စာရင်းသွင်းခြင်း)

Transcript တစ်ခုတွင် ကျောင်းသား၏ Enrollment Status နှင့် Course Duration ပါဝင်ရန်လိုအပ်ပါသည်။

1. **Course Enrollments Check ပြုလုပ်ခြင်း:**
   * Course ထဲသို့ ဝင်ရောက်ပြီး **`Participants`** Tab သို့ သွားပါ။
   * ကျောင်းသားများ၏ **Status** (Active / Completed) နှင့် **Enrollment Method** (Manual, Self-enrollment, Cohort) များကို စစ်ဆေးပါ။
2. **Cohort (အစုအဖွဲ့လိုက် Enrollment) အသုံးပြုခြင်း:**
   * **Site administration > Users > Cohorts** သို့ သွားပါ။
   * မေဂျာ/ပညာသင်နှစ်အလိုက် Cohort (ဥပမာ - *CS-2026-Batch*) ဆောက်၍ ကျောင်းသားများကို စုစည်းထားပါက တက္ကသိုလ်အဆင့် Transcript များ ထုတ်ယူရာတွင် လွယ်ကူမြန်ဆန်စေပါသည်။

---

## Step 2: Configure Course Completion & Final Grades

Transcript ထဲတွင် အောင်မြင်ပြီးစီးကြောင်း (Passed/Completed) ပေါ်လွင်စေရန် Course Completion ကို သတ်မှတ်ရပါမည်။

1. Course Settings မှ **`Course completion`** သို့ သွားပါ။
2. **Completion conditions:**
   * **Course grade:** သင်တန်းအောင်မြင်ရန် လိုအပ်သော အနည်းဆုံး Grade Threshold (ဥပမာ - `50%` သို့မဟုတ် `GPA 2.0`) သတ်မှတ်ပါ။
   * **Activity completion:** လိုအပ်သော Quiz / Assignment များကို မဖြစ်မနေ ပြီးမြောက်ရမည်ဟု Check ခြစ်ပေးပါ။
3. **Save changes** ကို နှိပ်ပါ။

---

## Step 3: Set Up Transcript Generation Plugin / Tool

Transcript ကို DOCX / PDF အဖြစ် Automate ထုတ်ယူရန် နည်းလမ်း (၃) မျိုး ရှိပါသည်။

### Option A: Using Exastud Block (Student Review & Transcripts)
1. Course/System တွင် Exastud Block ကို ထည့်သွင်းထားပါ။
2. **`Reports`** Tab သို့ သွားပါ။
3. **Template Section** တွင် *Academic Record* သို့မဟုတ် *Transcript Template* (.docx) ကို ရွေးချယ်ပါ။
4. သက်ဆိုင်ရာ ကျောင်းသားများကို Check ခြစ်၍ **`Generate / Download`** ကို နှိပ်ပြီး Official Transcript DOCX/PDF ကို ထုတ်ယူပါ။

### Option B: Using Custom Certificate Plugin
1. Course ထဲတွင် **`Custom Certificate`** Activity ကို ထည့်ပါ။
2. **Certificate Element** များတွင် dynamic tags များ ထည့်သွင်းပါ:
   * `{user_fullname}` - ကျောင်းသားအမည်
   * `{course_fullname}` - ဘာသာရပ်အမည်
   * `{grade}` - ရမှတ် / Letter Grade
   * `{completion_date}` - ပြီးစီးခဲ့သည့် ရက်စွဲ
3. ကျောင်းသား Course ပြီးမြောက်ပါက Official Transcript Certificate ကို PDF အဖြစ် Auto Download ဆွဲခွင့်ပြုပါ။

---

## Step 4: Exporting System-wide Transcripts (Admin Level)

ကျောင်းသား အများအပြား၏ Transcript / Grade History ကို စနစ်တစ်ခုလုံးမှ ထုတ်ယူရန်:

1. **Site administration > Grades > General settings** သို့ သွားပါ။
2. **Grades Export:**
   * **`Site administration > Reports`** သို့ သွား၍ **Grades Export** သို့မဟုတ် **Configurable Reports** ကို အသုံးပြုပါ။
   * File Format ကို **Excel (.xlsx)** သို့မဟုတ် **PDF** ရွေးချယ်ပြီး All Courses / All Enrollments အလိုက် Cumulative Transcript Data များကို Export ထုတ်ယူနိုင်ပါသည်။