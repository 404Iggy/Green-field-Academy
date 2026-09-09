<?php
require_once __DIR__ . '/config.php';
$pageTitle = 'Admissions';

$success = $error = '';

$courseRows = db()->query(
    "SELECT id, code, title, level FROM courses ORDER BY FIELD(level,'early_years','o_level','a_level','university'), code"
)->fetchAll();

$coursesByLevel = [];
foreach ($courseRows as $course) {
    $coursesByLevel[$course['level']][] = $course;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['applicant_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $parentName = trim($_POST['parent_name'] ?? '');
    $parentEmail = trim($_POST['parent_email'] ?? '');
    $parentPhone = trim($_POST['parent_phone'] ?? '');
    $level = $_POST['level_applying'] ?? '';
    $selectedCourseIds = array_filter(array_map('intval', $_POST['course_ids'] ?? []));
    $message = trim($_POST['message'] ?? '');

    if (!$name || !$email || !$level) {
        $error = 'Name, email and the level you are applying for are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (!in_array($level, ['early_years','o_level','a_level','university'], true)) {
        $error = 'Please select a valid academic level.';
    } elseif (!$selectedCourseIds) {
        $error = 'Please choose at least one course for the level you are applying to.';
    } elseif ($level !== 'university' && (!$parentName || !$parentPhone)) {
        $error = 'Parent/guardian name and phone are required for Early Years, O Level and A Level applications.';
    } elseif ($parentEmail && !filter_var($parentEmail, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid parent/guardian email address.';
    } else {
        $validCourseIds = [];
        $selectedCourseTitles = [];
        foreach ($courseRows as $course) {
            if ($course['level'] === $level && in_array((int)$course['id'], $selectedCourseIds, true)) {
                $validCourseIds[] = $course['id'];
                $selectedCourseTitles[] = $course['code'] . ' – ' . $course['title'];
            }
        }

        if (!$validCourseIds) {
            $error = 'Please select at least one valid course for the chosen level.';
        } else {
            try {
                $stmt = db()->prepare('INSERT INTO admissions (applicant_name, email, phone, parent_name, parent_email, parent_phone, level_applying, message, selected_courses) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([
                    $name,
                    $email,
                    $phone,
                    $parentName ?: null,
                    $parentEmail ?: null,
                    $parentPhone ?: null,
                    $level,
                    $message,
                    json_encode($selectedCourseTitles, JSON_UNESCAPED_UNICODE),
                ]);
                $success = 'Thank you for your application enquiry. We will contact you soon.';
            } catch (PDOException $e) {
                $error = 'Unable to submit your enquiry right now. Please try again later.';
            }
        }
    }
}

$levels = [
    'early_years' => 'Early Years',
    'o_level'     => 'O Level',
    'a_level'     => 'A Level',
    'university'  => 'University',
];

$selectedCourseIds = array_filter(array_map('intval', $_POST['course_ids'] ?? []));
?>
<?php include __DIR__ . '/header.php'; ?>


<div class="page-hero">
  <div class="container">
    <div class="breadcrumb"><a href="index.php">Home</a> / Admissions</div>
    <h1>Admissions Enquiry</h1>
    <p>Submit your enquiry and our admissions team will guide you through the application process.</p>
  </div>
</div>

<section class="section section-alt">
  <div class="container">
    <div class="section-head center">
      <span class="section-label">Apply Now</span>
      <h2 class="section-title">Start Your Greenfield Journey</h2>
      <div class="divider"></div>
      <p>Complete the form below and we will contact you with the next steps for admissions.</p>
    </div>

    <?php if ($success): ?><div class="alert alert-success"><?= sanitize($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><?= sanitize($error) ?></div><?php endif; ?>

    <div class="form-card" style="max-width:760px;margin:auto;">
      <form method="POST" action="">
        <div class="form-row">
          <div class="form-group">
            <label for="applicant_name">Full Name</label>
            <input type="text" id="applicant_name" name="applicant_name" placeholder="Applicant name" required value="<?= sanitize($_POST['applicant_name'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="you@example.com" required value="<?= sanitize($_POST['email'] ?? '') ?>">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="text" id="phone" name="phone" placeholder="e.g. +254 7XX XXX XXX" value="<?= sanitize($_POST['phone'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label for="level_applying">Applying For</label>
            <select id="level_applying" name="level_applying" required>
              <option value="">Please choose</option>
              <?php foreach ($levels as $key => $label): ?>
                <option value="<?= sanitize($key) ?>" <?= isset($_POST['level_applying']) && $_POST['level_applying'] === $key ? 'selected' : '' ?>><?= sanitize($label) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-row course-picker-group <?= isset($_POST['level_applying']) && $_POST['level_applying'] ? '' : 'hidden' ?>">
          <div class="form-group" style="width:100%;">
            <label>Courses you are interested in</label>
            <p style="font-size:.88rem;color:var(--text-light);margin:.25rem 0 1rem">Choose at least one course for the academic level selected.</p>
            <?php foreach ($coursesByLevel as $lvKey => $courses): ?>
            <div class="course-select-panel" data-level="<?= sanitize($lvKey) ?>" style="<?= isset($_POST['level_applying']) && $_POST['level_applying'] === $lvKey ? '' : 'display:none' ?>">
              <div class="course-panel-heading">Available <?= sanitize($levels[$lvKey] ?? ucfirst($lvKey)) ?> Courses</div>
              <div class="course-checkbox-grid">
                <?php foreach ($courses as $course): ?>
                <label class="course-checkbox">
                  <input type="checkbox" name="course_ids[]" value="<?= (int)$course['id'] ?>" <?= in_array((int)$course['id'], $selectedCourseIds, true) ? 'checked' : '' ?>>
                  <div class="course-checkbox-content">
                    <strong><?= sanitize($course['code']) ?></strong>
                    <span><?= sanitize($course['title']) ?></span>
                  </div>
                </label>
                <?php endforeach; ?>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="form-row parent-details-group <?= isset($_POST['level_applying']) && $_POST['level_applying'] !== 'university' ? '' : 'hidden' ?>">
          <div class="form-group">
            <label for="parent_name">Parent / Guardian Name</label>
            <input type="text" id="parent_name" name="parent_name" placeholder="Parent or guardian full name" value="<?= sanitize($_POST['parent_name'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label for="parent_phone">Parent / Guardian Phone</label>
            <input type="text" id="parent_phone" name="parent_phone" placeholder="Parent or guardian phone" value="<?= sanitize($_POST['parent_phone'] ?? '') ?>">
          </div>
        </div>

        <div class="form-row parent-details-group <?= isset($_POST['level_applying']) && $_POST['level_applying'] !== 'university' ? '' : 'hidden' ?>">
          <div class="form-group" style="width:100%;">
            <label for="parent_email">Parent / Guardian Email (optional)</label>
            <input type="email" id="parent_email" name="parent_email" placeholder="parent@example.com" value="<?= sanitize($_POST['parent_email'] ?? '') ?>">
          </div>
        </div>

        <div class="form-group">
          <label for="message">Tell us more</label>
          <textarea id="message" name="message" rows="5" placeholder="Tell us about the applicant or any questions you have."><?= sanitize($_POST['message'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn-submit">Submit Enquiry</button>
      </form>
    </div>
  </div>
</section>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const levelSelect = document.getElementById('level_applying');
    const parentGroups = document.querySelectorAll('.parent-details-group');
    const coursePickerGroup = document.querySelector('.course-picker-group');
    const coursePanels = document.querySelectorAll('.course-select-panel');

    const updateFormSections = () => {
      const selectedLevel = levelSelect.value;
      const showParents = selectedLevel !== 'university';
      parentGroups.forEach(group => {
        group.classList.toggle('hidden', !showParents);
      });
      if (coursePickerGroup) {
        coursePickerGroup.classList.toggle('hidden', !selectedLevel);
      }
      coursePanels.forEach(panel => {
        const visible = panel.dataset.level === selectedLevel;
        panel.style.display = visible ? '' : 'none';
        if (!visible) {
          panel.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
        }
      });
    };

    if (levelSelect) {
      levelSelect.addEventListener('change', updateFormSections);
      updateFormSections();
    }
  });
</script>

<?php include __DIR__ . '/footer.php'; ?>
