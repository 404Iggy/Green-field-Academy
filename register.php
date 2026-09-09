<?php
require_once __DIR__ . '/config.php';
$pageTitle = 'Create Account';

// Already logged in? Go to portal
if (isLoggedIn()) {
    header('Location: portal.php');
    exit;
}

$error   = '';
$success = '';

// Preserve field values on error
$fields = [
    'full_name'  => '',
    'email'      => '',
    'role'       => '',
    'level'      => '',
    'student_id' => '',
];

$levels = [
    'early_years' => 'Early Years',
    'o_level'     => 'O Level',
    'a_level'     => 'A Level',
    'university'  => 'University',
];

// Courses grouped by level → department, used to populate the course picker
$courseRows = db()->query("
    SELECT d.level, d.name AS dept_name, c.id, c.code, c.title
    FROM courses c
    JOIN departments d ON d.id = c.department_id
    ORDER BY FIELD(d.level,'early_years','o_level','a_level','university'), d.name, c.code
")->fetchAll();

$coursesByLevel = [];
foreach ($courseRows as $row) {
    $coursesByLevel[$row['level']][$row['dept_name']][] = $row;
}

$selectedCourseIds = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fields['full_name']  = trim($_POST['full_name']  ?? '');
    $fields['email']      = trim($_POST['email']      ?? '');
    $fields['role']       = $_POST['role']            ?? '';
    $fields['level']      = $_POST['level']           ?? '';
    $fields['student_id'] = trim($_POST['student_id'] ?? '');
    $password             = $_POST['password']        ?? '';
    $passwordConfirm      = $_POST['password_confirm'] ?? '';

    // Course IDs submitted (students only)
    $selectedCourseIds = array_filter(array_map('intval', $_POST['course_ids'] ?? []));

    // ── Validation ───────────────────────────────────────────────
    if (!$fields['full_name'] || !$fields['email'] || !$fields['role'] || !$password) {
        $error = 'Full name, email, role and password are all required.';

    } elseif (!filter_var($fields['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';

    } elseif (!in_array($fields['role'], ['student', 'staff'], true)) {
        $error = 'Please select a valid role (Student or Teacher).';

    } elseif ($fields['role'] === 'student' && !array_key_exists($fields['level'], $levels)) {
        $error = 'Students must select their academic level.';

    } elseif ($fields['role'] === 'student' && !$selectedCourseIds) {
        $error = 'Please select at least one course you are taking.';

    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long.';

    } elseif ($password !== $passwordConfirm) {
        $error = 'Passwords do not match. Please try again.';

    } else {
        // Check if email is already taken
        $check = db()->prepare('SELECT id FROM users WHERE email = ?');
        $check->execute([$fields['email']]);

        if ($check->fetch()) {
            $error = 'That email address is already registered. Please log in instead.';
        } else {
            // Hash password & insert
            $hash  = password_hash($password, PASSWORD_BCRYPT);
            $level = $fields['role'] === 'student' ? $fields['level'] : null;
            $sid   = $fields['student_id'] ?: null;

            // Make sure every selected course actually belongs to the chosen level
            $validCourseIds = [];
            if ($fields['role'] === 'student' && $selectedCourseIds) {
                $placeholders = implode(',', array_fill(0, count($selectedCourseIds), '?'));
                $courseCheck = db()->prepare("SELECT id FROM courses WHERE level = ? AND id IN ($placeholders)");
                $courseCheck->execute(array_merge([$level], $selectedCourseIds));
                $validCourseIds = array_column($courseCheck->fetchAll(), 'id');
            }

            try {
                $stmt = db()->prepare('
                    INSERT INTO users (full_name, email, password, role, level, student_id)
                    VALUES (?, ?, ?, ?, ?, ?)
                ');
                $stmt->execute([
                    $fields['full_name'],
                    $fields['email'],
                    $hash,
                    $fields['role'],
                    $level,
                    $sid,
                ]);

                $newUserId = db()->lastInsertId();

                if ($validCourseIds) {
                    $enrollStmt = db()->prepare('INSERT IGNORE INTO enrollments (student_id, course_id) VALUES (?, ?)');
                    foreach ($validCourseIds as $cid) {
                        $enrollStmt->execute([$newUserId, $cid]);
                    }
                }

                // Auto-login after registration
                $_SESSION['user_id'] = $newUserId;
                header('Location: portal.php');
                exit;

            } catch (PDOException $e) {
                // Duplicate student_id (unique constraint)
                if ($e->getCode() === '23000') {
                    $error = 'That student / staff ID is already in use. Leave it blank or use a different one.';
                } else {
                    $error = 'Registration failed. Please try again later.';
                }
            }
        }
    }
}
?>
<?php include __DIR__ . '/header.php'; ?>

<div class="page-hero">
  <div class="container">
    <div class="breadcrumb"><a href="index.php">Home</a> / <a href="login.php">Portal Login</a> / Register</div>
    <h1>Create Your Account</h1>
    <p>Sign up as a student or teacher to access the Greenfield Academy portal.</p>
  </div>
</div>

<section class="section">
  <div class="container" style="max-width:600px;margin:auto;">
    <div class="form-card">

      <h3 style="color:var(--green-dark);margin-bottom:.4rem">New Account</h3>
      <p style="margin-bottom:1.75rem">Fill in your details below. Students will be able to see their assignments and timetable; teachers can assign work and post school news.</p>

      <?php if ($error): ?>
        <div class="alert alert-error"><?= sanitize($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="" id="registerForm">

        <!-- Full Name -->
        <div class="form-group">
          <label for="full_name">Full Name <span style="color:#c0392b">*</span></label>
          <input type="text" id="full_name" name="full_name"
                 placeholder="e.g. Jane Wanjiku"
                 required value="<?= sanitize($fields['full_name']) ?>">
        </div>

        <!-- Email -->
        <div class="form-group">
          <label for="email">Email Address <span style="color:#c0392b">*</span></label>
          <input type="email" id="email" name="email"
                 placeholder="you@example.com"
                 required value="<?= sanitize($fields['email']) ?>">
        </div>

        <!-- Role selector (styled cards) -->
        <div class="form-group">
          <label>I am joining as… <span style="color:#c0392b">*</span></label>
          <div class="role-picker">

            <label class="role-option <?= $fields['role']==='student' ? 'selected' : '' ?>">
              <input type="radio" name="role" value="student" required
                     <?= $fields['role']==='student' ? 'checked' : '' ?>>
              <span class="role-icon">🎓</span>
              <span class="role-label">Student</span>
              <span class="role-desc">View assignments, timetables &amp; news</span>
            </label>

            <label class="role-option <?= $fields['role']==='staff' ? 'selected' : '' ?>">
              <input type="radio" name="role" value="staff"
                     <?= $fields['role']==='staff' ? 'checked' : '' ?>>
              <span class="role-icon">👨‍🏫</span>
              <span class="role-label">Teacher</span>
              <span class="role-desc">Assign work, update timetable &amp; post news</span>
            </label>

          </div>
        </div>

        <!-- Level (shown only for students via JS) -->
        <div class="form-group" id="levelGroup"
             style="<?= $fields['role']==='student' ? '' : 'display:none' ?>">
          <label for="level">Academic Level <span style="color:#c0392b">*</span></label>
          <select id="level" name="level">
            <option value="">Select your level</option>
            <?php foreach ($levels as $key => $label): ?>
              <option value="<?= sanitize($key) ?>"
                <?= $fields['level']===$key ? 'selected' : '' ?>>
                <?= sanitize($label) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Course picker (students only) -->
        <div class="form-group" id="coursesGroup" style="<?= $fields['role']==='student' ? '' : 'display:none' ?>">
          <label>Courses You're Taking <span style="color:#c0392b">*</span></label>
          <p style="font-size:.78rem;color:var(--text-light);margin:-.35rem 0 .6rem">Pick your level above first, then select the subjects you're enrolling in.</p>

          <?php foreach ($levels as $lvKey => $lvLabel): ?>
          <div class="course-select-panel" data-level="<?= sanitize($lvKey) ?>" style="<?= ($fields['level'] ?? '')===$lvKey ? '' : 'display:none' ?>">
            <?php if (!empty($coursesByLevel[$lvKey])): ?>
              <?php foreach ($coursesByLevel[$lvKey] as $deptName => $deptCourses): ?>
              <div class="course-dept-group">
                <h5><?= sanitize($deptName) ?></h5>
                <div class="course-checkbox-grid">
                  <?php foreach ($deptCourses as $c): ?>
                  <label class="course-checkbox">
                    <input type="checkbox" name="course_ids[]" value="<?= (int)$c['id'] ?>"
                      <?= in_array((int)$c['id'], $selectedCourseIds, true) ? 'checked' : '' ?>>
                    <span><strong><?= sanitize($c['code']) ?></strong> — <?= sanitize($c['title']) ?></span>
                  </label>
                  <?php endforeach; ?>
                </div>
              </div>
              <?php endforeach; ?>
            <?php else: ?>
              <p style="font-size:.85rem;color:var(--text-light)">No courses listed for this level yet.</p>
            <?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>

        <!-- Optional student / staff ID -->
        <div class="form-group">
          <label for="student_id">Student / Staff ID <span style="color:var(--text-light);font-weight:400">(optional)</span></label>
          <input type="text" id="student_id" name="student_id"
                 placeholder="e.g. STU-2025-042"
                 value="<?= sanitize($fields['student_id']) ?>">
        </div>

        <!-- Password -->
        <div class="form-row">
          <div class="form-group">
            <label for="password">Password <span style="color:#c0392b">*</span></label>
            <input type="password" id="password" name="password"
                   placeholder="Min. 8 characters" required>
          </div>
          <div class="form-group">
            <label for="password_confirm">Confirm Password <span style="color:#c0392b">*</span></label>
            <input type="password" id="password_confirm" name="password_confirm"
                   placeholder="Repeat password" required>
          </div>
        </div>

        <button type="submit" class="btn-submit">Create Account &amp; Sign In</button>

        <p style="text-align:center;margin-top:1.25rem;font-size:.9rem;color:var(--text-light)">
          Already have an account?
          <a href="login.php" style="color:var(--green-light);font-weight:600">Sign in here</a>
        </p>

      </form>
    </div>
  </div>
</section>

<?php include __DIR__ . '/footer.php'; ?>

<!-- ── Inline JS: show/hide level selector based on role ─────── -->
<script>
(function () {
  const radios       = document.querySelectorAll('input[name="role"]');
  const levelGroup    = document.getElementById('levelGroup');
  const levelSel       = document.getElementById('level');
  const options        = document.querySelectorAll('.role-option');
  const coursesGroup  = document.getElementById('coursesGroup');
  const coursePanels  = document.querySelectorAll('.course-select-panel');

  function updateCoursePanel() {
    coursePanels.forEach(panel => {
      panel.style.display = panel.dataset.level === levelSel.value ? '' : 'none';
    });
  }

  function update() {
    const chosen = document.querySelector('input[name="role"]:checked');
    const isStudent = chosen && chosen.value === 'student';

    levelGroup.style.display   = isStudent ? '' : 'none';
    levelSel.required          = isStudent;
    coursesGroup.style.display = isStudent ? '' : 'none';

    // Uncheck all course checkboxes if the user isn't registering as a student
    if (!isStudent) {
      document.querySelectorAll('input[name="course_ids[]"]').forEach(cb => cb.checked = false);
    }

    // Update visual selection state
    options.forEach(opt => {
      const radio = opt.querySelector('input[type="radio"]');
      opt.classList.toggle('selected', radio.checked);
    });
  }

  radios.forEach(r => r.addEventListener('change', update));
  levelSel.addEventListener('change', () => {
    updateCoursePanel();
    // Clear selections from a previously-viewed level so stale courses don't submit
    coursePanels.forEach(panel => {
      if (panel.dataset.level !== levelSel.value) {
        panel.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
      }
    });
  });

  update();            // run once on load in case of PHP-preserved values
  updateCoursePanel();
})();
</script>

<!-- ── Extra CSS for the role picker cards ───────────────────── -->
<style>
.role-picker {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  margin-top: .25rem;
}
.role-option {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  padding: 1.25rem 1rem;
  border: 2px solid #e0e0e0;
  border-radius: var(--radius);
  cursor: pointer;
  transition: var(--transition);
  background: var(--white);
  gap: .35rem;
}
.role-option input[type="radio"] { display: none; }
.role-option:hover { border-color: var(--green-light); background: var(--green-pale); }
.role-option.selected {
  border-color: var(--green-mid);
  background: var(--green-pale);
  box-shadow: 0 0 0 3px rgba(46,125,82,.15);
}
.role-icon  { font-size: 2rem; }
.role-label { font-weight: 700; color: var(--green-dark); font-size: 1rem; }
.role-desc  { font-size: .78rem; color: var(--text-light); line-height: 1.4; }
</style>