This is the **Definitive Technical Specification** for the DoBu Martial Arts System. This document covers every variable, database interaction, logic gate, and edge case in the system.

It explains **exactly** how the system enforces the Assignment Brief requirements  at a microscopic level.[1]

***

# 1. The Data Architecture (The "Brain")

The system's logic is only as good as its data. Here is the exact schema required to support the rules.

### **Table A: `users` (Member State)**
This table holds the "permissions" for every human in the system.
*   **`id` (INT, PK):** The immutable reference for the user.
*   **`membership_type` (VARCHAR):** The primary key for logic. Values: `basic`, `intermediate`, `advanced`, `elite`, 'junior', `self-defence`.
*   **`chosen_martial_art` (VARCHAR):** Logic Variable A. Stores "Judo", "Karate", etc. Used for Basic/Intermediate checks.
*   **`chosen_martial_art_2` (VARCHAR):** Logic Variable B. **Crucial for Advanced members.** Stores the second art (e.g., "Muay Thai").
*   **`sessions_used_this_week` (INT):** The "Quota Counter."
    *   *Behavior:* Starts at `0`. Increments by `1` on successful booking. Must be manually reset to `0` weekly.
*   **`created_at` (DATETIME):** The "Time Anchor." Used strictly for the Self-Defence 6-week expiry calculation.

### **Table B: `classes` (The Product)**
*   **`id` (INT, PK):** Unique Class ID.
*   **`martial_art` (VARCHAR):** The matching key (e.g., "Jiu-jitsu").
*   **`is_kids_class` (TINYINT/BOOLEAN):** The "Safety Flag."
    *   **Value `0` (Adult):** Visible to Basic, Int, Adv, Elite, Self-Defence. **Invisible/Blocked** for Juniors.
    *   **Value `1` (Kid):** Visible **ONLY** to Juniors. **Blocked** for everyone else.

### **Table C: `bookings` (The Ledger)**
*   **`user_id` + `class_id` (Composite Unique Index):**
    *   *Purpose:* Prevents the "Double Booking" bug. The database engine physically rejects a second row with the same User+Class ID pair.

***

# 2. The Logic Engine (The "Gatekeeper")

When `canUserBookClass()` is called, the system executes a **Waterfall Logic Check**. If the user fails *any* step, they are rejected immediately.

### **Gate 1: The "Clean-Up" (Normalization)**
Before checking anything, the system sanitizes inputs to prevent "human error" mismatches.
*   **Function:** `cleanArtName($text)`
*   **Input:** "Muay-Thai " (User Profile) vs "Muay Thai" (Class Name).
*   **Process:** Remove non-letters, convert to lowercase.
*   **Result:** `muaythai` matches `muaythai`.
*   *Why?* Without this, a simple space or hyphen breaks the entire booking system.

### **Gate 2: The "Identity" Check (Adult vs. Child)**
This is the hardest boundary in the system.
*   **Scenario A:** User is `Junior`.
    *   **Check:** Is `class.is_kids_class == 1`?
    *   *Yes:* **PASS** (Proceed to next gate).
    *   *No:* **FAIL** (Reason: "Juniors cannot book Adult classes").
*   **Scenario B:** User is `Basic`, `Intermediate`, `Advanced`, `Elite`.
    *   **Check:** Is `class.is_kids_class == 0`?
    *   *Yes:* **PASS**.
    *   *No:* **FAIL** (Reason: "Adults cannot book Kids classes").

### **Gate 3: The "Relevance" Check (Does it match?)**
This gate varies by membership tier.

*   **Basic / Intermediate:**
    *   **Logic:** `class_art_clean === user_art_1`
    *   *Fail State:* User picked "Judo", tries to book "Karate". System says "Restricted to Judo".
*   **Advanced:**
    *   **Logic:** `(class_art_clean === user_art_1) OR (class_art_clean === user_art_2)`
    *   *Fail State:* User picked "Judo" + "Karate", tries to book "Muay Thai". System says "Restricted to your 2 chosen arts".
*   **Elite:**
    *   **Logic:** Always `TRUE` (Matches everything).
    *   *Exception:* If Class Name contains "Private", **FAIL**.
*   **Self-Defence:**
    *   **Logic:** `str_contains(class_name, 'defence')`
    *   *Fail State:* Tries to book "Judo". System says "Self-Defence Only".

### **Gate 4: The "Temporal" Check (Self-Defence Only)**
*   **Variable:** `$window_end = $user_created_at + 6 weeks`.
*   **Logic:** `NOW() > $window_end`.
*   **Edge Case:** User joined Jan 1st. Course ends Feb 12th.
    *   *Attempt:* Feb 13th booking.
    *   *Result:* **FAIL** ("Course Expired").

### **Gate 5: The "Quota" Check (The Limit)**
This checks the `sessions_used_this_week` integer.
*   **Basic:** Fail if `>= 2`.
*   **Intermediate:** Fail if `>= 3`.
*   **Advanced:** Fail if `>= 5`.
*   **Self-Defence:** Fail if `>= 2`.
*   **Elite / Junior:** Skip this check (Unlimited).

***

# 3. The Transaction Cycle (The Code Execution)

When the user actually clicks "Confirm Booking," the following **Atomic Transaction** occurs. This prevents race conditions (two users booking the last slot at the exact same millisecond).

1.  **Start Transaction:** `$conn->begin_transaction();` (Database locks logic).
2.  **Safety Check 1:** Re-run `canUserBookClass()` to ensure they didn't hack the frontend button.
3.  **Safety Check 2:** `SELECT count(*) FROM bookings WHERE user_id = ? AND class_id = ?`.
    *   If `> 0`, **ROLLBACK** (Stop). Return error: "Already booked".
4.  **Insert Booking:** `INSERT INTO bookings (user_id, class_id, status) VALUES (..., 'confirmed')`.
5.  **Update Quota:** `UPDATE users SET sessions_used_this_week = sessions_used_this_week + 1 WHERE id = ?`.
    *   *Crucial Detail:* This increment happens *inside* the transaction.
6.  **Commit:** `$conn->commit();` (Save everything permanently).

***

# 4. The Admin Lifecycle (Management)

### **A. Creating a Class**
*   **Input:** Admin creates "Tuesday 5pm Karate".
*   **The Trap:** There is a checkbox `[ ] Is Kids Class?`.
*   **Impact:**
    *   If **Unchecked (`0`)**: Only Adults (Basic/Int/Adv/Elite) can see it.
    *   If **Checked (`1`)**: Only Juniors can see it.
    *   *There is no "Mixed" class in this system.*

### **B. The Weekly Reset (Maintenance)**
*   **Problem:** `sessions_used_this_week` counts up forever (2, 3, 4, 5...).
*   **Solution:** A script must run every Sunday night (or Admin clicks a "Reset Week" button).
*   **SQL:** `UPDATE users SET sessions_used_this_week = 0`.
*   **Result:** Every member's quota is refreshed. A Basic member who had 2/2 bookings last week now has 0/2 for the new week.
*   **Automation:** Use `php scripts/reset_sessions.php` from the project root as part of the weekly maintenance schedule. Example cron entry:
    ```bash
    0 0 * * 0 cd /c/xampp/htdocs/htu_martial_arts-main && php scripts/reset_sessions.php >> /var/log/htu_weekly_reset.log 2>&1
    ```

***

# 5. Specialized Edge Cases & Facilities

### **A. Private Tuition (£15/hr)**
*   **Mechanism:** This is not a "Class" in the traditional sense. It's a "Service".
*   **Logic:** The system looks for the string "Private" in the class name.
*   **Restriction:**
    *   **Elite Members:** Cannot book this for free (it's extra). Logic: `if (Elite AND class contains 'Private') -> BLOCK`.
    *   **Private Members:** Can **ONLY** book this. Logic: `if (PrivatePlan AND class !contains 'Private') -> BLOCK`.

### **B. Fitness Room (£6/visit)**
*   **Mechanism:** A distinct membership type `fitness`.
*   **Logic:**
    *   Can they book "Judo"? **No.**
    *   Can they book "Fitness Room Slot"? **Yes.**
    *   *How?* The logic checks `if (Plan == 'fitness' AND class_name != 'Fitness Room') -> BLOCK`.

### **C. Personal Training (formerly Open Mat)**
*   **Audience:** Elite, Advanced, Intermediate, Basic members.
*   **Logic:** Their plan already verifies the chosen martial art, but `Personal Training` is treated as a general-access session, so as long as the member has that martial art selected (or the class is generic), it counts toward the weekly allowance.
*   **Result:** No extra restriction—if a Basic or Intermediate member wants to spend one of their weekly sessions on `Personal Training`, the system does not block it just because the class name differs from the art they originally picked.

This document represents the complete logical truth of your system. Every line of code in `membership_rules.php` exists to enforce one of the sentences written above.


 
 