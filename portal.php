<?php
require_once __DIR__ . '/config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user = currentUser();
$pageTitle = 'Portal';

$levels = [
    'early_years' => 'Early Years',
    'o_level'     => 'O Level',
    'a_level'     => 'A Level',
    'university'  => 'University',
];

$success = '';
$error = '';

$dayOptions = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
$studentToEdit = null;
$selectedCourseIds = [];
$allCourses = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($user['role'], ['staff', 'admin'], true)) {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_assignment') {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $level = $_POST['level'] ?? '';
        $dueDate = $_POST['due_date'] ?? '';

        if (!$title || !$level || !$dueDate) {
            $error = 'Please provide title, level and due date for the assignment.';
        } elseif (!isset($levels[$level])) {
            $error = 'Please select a valid level.';
        } else {
            $stmt = db()->prepare('INSERT INTO assignments (title, description, level, due_date, assigned_by) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$title, $description, $level, $dueDate, $user['id']]);
            $success = 'Assignment has been created successfully.';
        }
    }

    if ($action === 'delete_assignment') {
        $assignmentId = intval($_POST['assignment_id'] ?? 0);
        if ($assignmentId > 0) {
            $stmt = db()->prepare('DELETE FROM assignments WHERE id = ?');
            $stmt->execute([$assignmentId]);
            $success = 'Assignment removed successfully.';
        }
    }

    if ($action === 'add_timetable') {
        $level = $_POST['level'] ?? '';
        $day = $_POST['day'] ?? '';
        $subject = trim($_POST['subject'] ?? '');
        $teacher = trim($_POST['teacher'] ?? '');
        $venue = trim($_POST['venue'] ?? '');
        $startTime = $_POST['start_time'] ?? '';
        $endTime = $_POST['end_time'] ?? '';

        if (!$level || !$day || !$subject || !$startTime || !$endTime) {
            $error = 'Please fill in level, day, times and subject for the timetable entry.';
        } elseif (!isset($levels[$level]) || !in_array($day, $dayOptions, true)) {
            $error = 'Please select a valid level and day.';
        } else {
            $stmt = db()->prepare('INSERT INTO timetables (level, day_of_week, start_time, end_time, subject, teacher, venue) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$level, $day, $startTime, $endTime, $subject, $teacher, $venue]);
            $success = 'Timetable entry has been saved successfully.';
        }
    }

    if ($action === 'delete_timetable') {
        $timetableId = intval($_POST['timetable_id'] ?? 0);
        if ($timetableId > 0) {
            $stmt = db()->prepare('DELETE FROM timetables WHERE id = ?');
            $stmt->execute([$timetableId]);
            $success = 'Timetable entry removed successfully.';
        }
    }

    if ($action === 'post_news') {
        $title = trim($_POST['news_title'] ?? '');
        $excerpt = trim($_POST['news_excerpt'] ?? '');
        $content = trim($_POST['news_content'] ?? '');
        $publishedAt = $_POST['published_at'] ?? date('Y-m-d');

        if (!$title || !$excerpt) {
            $error = 'Please provide both a title and short excerpt for the news item.';
        } else {
            $stmt = db()->prepare('INSERT INTO news (title, excerpt, content, author_id, published_at) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$title, $excerpt, $content, $user['id'], $publishedAt]);
            $success = 'News announcement has been published.';
        }
    }

    if ($action === 'delete_news') {
        $newsId = intval($_POST['news_id'] ?? 0);
        if ($newsId > 0) {
            $stmt = db()->prepare('DELETE FROM news WHERE id = ?');
            $stmt->execute([$newsId]);
            $success = 'News announcement removed successfully.';
        }
    }

    if ($action === 'update_student') {
        $studentId = intval($_POST['student_id'] ?? 0);
        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $studentNumber = trim($_POST['student_id_number'] ?? '');
        $level = $_POST['level'] ?? '';
        $courseIds = array_values(array_unique(array_map('intval', $_POST['course_ids'] ?? [])));

        if ($studentId <= 0 || !$fullName || !$email || !$studentNumber || !isset($levels[$level])) {
            $error = 'Please complete the student name, email, student ID and level before saving.';
        } else {
            $emailCheck = db()->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
            $emailCheck->execute([$email, $studentId]);
            if ($emailCheck->fetch()) {
                $error = 'Another user already uses that email address.';
            } else {
                $studentNumberCheck = db()->prepare('SELECT id FROM users WHERE student_id = ? AND id != ?');
                $studentNumberCheck->execute([$studentNumber, $studentId]);
                if ($studentNumberCheck->fetch()) {
                    $error = 'That student ID is already assigned to another student.';
                } else {
                    try {
                        db()->beginTransaction();

                        $stmt = db()->prepare('UPDATE users SET full_name = ?, email = ?, student_id = ?, level = ? WHERE id = ? AND role = ?');
                        $stmt->execute([$fullName, $email, $studentNumber, $level, $studentId, 'student']);

                        $deleteEnrollments = db()->prepare('DELETE FROM enrollments WHERE student_id = ?');
                        $deleteEnrollments->execute([$studentId]);

                        if ($courseIds) {
                            $insertEnrollment = db()->prepare('INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)');
                            foreach ($courseIds as $courseId) {
                                $insertEnrollment->execute([$studentId, $courseId]);
                            }
                        }

                        db()->commit();
                        $success = 'Student details and course selections were updated successfully.';
                    } catch (Exception $e) {
                        db()->rollBack();
                        $error = 'Unable to save the student update. Please try again.';
                    }
                }
            }
        }
    }

    if ($action === 'delete_student') {
        $studentId = intval($_POST['student_id'] ?? 0);
        if ($studentId > 0) {
            $stmt = db()->prepare('DELETE FROM users WHERE id = ? AND role = ?');
            $stmt->execute([$studentId, 'student']);
            $success = 'Student removed successfully.';
        }
    }

    if ($action === 'update_admission_status') {
        $admissionId = intval($_POST['admission_id'] ?? 0);
        $newStatus = $_POST['status'] ?? '';
        if ($admissionId > 0 && in_array($newStatus, ['pending','reviewed','accepted','declined'], true)) {
            $stmt = db()->prepare('UPDATE admissions SET status = ? WHERE id = ?');
            $stmt->execute([$newStatus, $admissionId]);
            $success = 'Application status updated.';
        }
    }

    if ($action === 'toggle_contact_replied') {
        $contactId = intval($_POST['contact_id'] ?? 0);
        if ($contactId > 0) {
            $stmt = db()->prepare('UPDATE contact_messages SET replied = 1 - replied WHERE id = ?');
            $stmt->execute([$contactId]);
            $success = 'Message status updated.';
        }
    }
}

$newsItems = db()->query('SELECT * FROM news ORDER BY published_at DESC LIMIT 5')->fetchAll();

if ($user['role'] === 'student') {
    $assignmentStmt = db()->prepare('SELECT * FROM assignments WHERE level = ? ORDER BY due_date ASC');
    $assignmentStmt->execute([$user['level']]);
    $studentAssignments = $assignmentStmt->fetchAll();

    $timetableStmt = db()->prepare('SELECT * FROM timetables WHERE level = ? ORDER BY FIELD(day_of_week, "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"), start_time ASC');
    $timetableStmt->execute([$user['level']]);
    $studentTimetable = $timetableStmt->fetchAll();
}

$staffAssignments = db()->query('SELECT a.*, u.full_name AS teacher_name FROM assignments a LEFT JOIN users u ON a.assigned_by = u.id ORDER BY a.level, a.due_date ASC')->fetchAll();
$staffTimetable = db()->query('SELECT * FROM timetables ORDER BY FIELD(day_of_week, "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"), level, start_time ASC')->fetchAll();

if (in_array($user['role'], ['staff', 'admin'], true)) {
    $selectedStudentId = intval($_GET['edit_student_id'] ?? $_POST['student_id'] ?? 0);
    if ($selectedStudentId > 0) {
        $studentStmt = db()->prepare('SELECT id, full_name, email, student_id, level FROM users WHERE id = ? AND role = ?');
        $studentStmt->execute([$selectedStudentId, 'student']);
        $studentToEdit = $studentStmt->fetch() ?: null;

        if ($studentToEdit) {
            $courseStmt = db()->prepare('SELECT course_id FROM enrollments WHERE student_id = ?');
            $courseStmt->execute([$selectedStudentId]);
            $selectedCourseIds = array_map('intval', array_column($courseStmt->fetchAll(), 'course_id'));
        }
    }

    $allCourses = db()->query('SELECT id, code, title, level FROM courses ORDER BY level, code')->fetchAll();

    // Every student with the courses they're enrolled in
    $studentRows = db()->query("
        SELECT u.id, u.full_name, u.email, u.student_id, u.level,
               GROUP_CONCAT(c.code, ':', c.title ORDER BY c.code SEPARATOR ';;') AS courses_raw
        FROM users u
        LEFT JOIN enrollments e ON e.student_id = u.id
        LEFT JOIN courses c ON c.id = e.course_id
        WHERE u.role = 'student'
        GROUP BY u.id
        ORDER BY FIELD(u.level,'early_years','o_level','a_level','university'), u.full_name
    ")->fetchAll();

    foreach ($studentRows as &$sr) {
        $sr['courses'] = [];
        if ($sr['courses_raw']) {
            foreach (explode(';;', $sr['courses_raw']) as $pair) {
                [$code, $title] = array_pad(explode(':', $pair, 2), 2, '');
                $sr['courses'][] = ['code' => $code, 'title' => $title];
            }
        }
    }
    unset($sr);

    // Admissions enquiries + contact messages
    $admissionEnquiries = db()->query('SELECT * FROM admissions ORDER BY submitted_at DESC')->fetchAll();
    $contactEnquiries   = db()->query('SELECT * FROM contact_messages ORDER BY sent_at DESC')->fetchAll();
}

?>
<?php include __DIR__ . '/header.php'; ?>

<div class="page-hero">
  <div class="container">
    <div class="breadcrumb"><a href="index.php">Home</a> / Portal</div>
    <h1>Welcome back, <?= sanitize($user['full_name']) ?></h1>
    <p><?= $user['role'] === 'student' ? 'View your current assignments, timetable and school announcements.' : 'Manage assignments, update timetables and send news to the school community.' ?></p>
  </div>
</div>

<section class="section section-alt portal-actions-section">
  <div class="container">
    <div class="portal-actions">
      <?php if ($user['role'] === 'student'): ?>
        <a class="portal-action-card" href="#assignments">
          <span class="portal-action-icon">📚</span>
          <strong>See Assignments</strong>
          <small>Open all work for your level</small>
        </a>
        <a class="portal-action-card" href="#timetable">
          <span class="portal-action-icon">🗓️</span>
          <strong>View Timetable</strong>
          <small>Check your weekly schedule</small>
        </a>
        <a class="portal-action-card" href="#news">
          <span class="portal-action-icon">📰</span>
          <strong>Read News</strong>
          <small>Stay updated on school announcements</small>
        </a>
      <?php else: ?>
        <a class="portal-action-card" href="#create-assignment">
          <span class="portal-action-icon">📝</span>
          <strong>Assign Work</strong>
          <small>Create study tasks for learners</small>
        </a>
        <a class="portal-action-card" href="#create-timetable">
          <span class="portal-action-icon">📅</span>
          <strong>Update Timetable</strong>
          <small>Schedule lessons quickly</small>
        </a>
        <a class="portal-action-card" href="#publish-news">
          <span class="portal-action-icon">📣</span>
          <strong>Publish News</strong>
          <small>Share announcements schoolwide</small>
        </a>
        <a class="portal-action-card" href="#students">
          <span class="portal-action-icon">🎓</span>
          <strong>Students &amp; Courses</strong>
          <small>See who's enrolled in what</small>
        </a>
        <a class="portal-action-card" href="#enquiries">
          <span class="portal-action-icon">📬</span>
          <strong>Enquiries</strong>
          <small>Reply to admissions &amp; contact messages</small>
        </a>
      <?php endif; ?>
    </div>
  </div>
</section>

<section class="section section-alt">
  <div class="container">
    <?php if ($success): ?><div class="alert alert-success"><?= sanitize($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><?= sanitize($error) ?></div><?php endif; ?>

    <?php if ($user['role'] === 'student'): ?>
      <div class="portal-grid">
        <div class="portal-tile">
          <div class="tile-icon">📚</div>
          <h4>Assignments</h4>
          <p>Your assignments for <?= sanitize($levels[$user['level']] ?? 'your level') ?>.</p>
        </div>
        <div class="portal-tile">
          <div class="tile-icon">🗓️</div>
          <h4>Timetable</h4>
          <p>See the lesson plan assigned for your level.</p>
        </div>
        <div class="portal-tile">
          <div class="tile-icon">📰</div>
          <h4>Latest News</h4>
          <p>School announcements and updates from teachers.</p>
        </div>
      </div>

      <div id="assignments" class="portal-section">
        <h2>Assignments</h2>
        <?php if ($studentAssignments): ?>
          <div class="portal-card">
            <table class="portal-table">
              <thead>
                <tr><th>Title</th><th>Due Date</th><th>Description</th><th>Level</th></tr>
              </thead>
              <tbody>
                <?php foreach ($studentAssignments as $assignment): ?>
                  <tr>
                    <td><?= sanitize($assignment['title']) ?></td>
                    <td><?= sanitize(date('d M Y', strtotime($assignment['due_date']))) ?></td>
                    <td><?= sanitize($assignment['description']) ?></td>
                    <td><?= sanitize($levels[$assignment['level']]) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <p class="mt-2">No assignments have been assigned for your level yet. Please check back soon.</p>
        <?php endif; ?>
      </div>

      <div id="timetable" class="portal-section">
        <h2>Timetable</h2>
        <?php if ($studentTimetable): ?>
          <div class="portal-card">
            <table class="portal-table">
              <thead>
                <tr><th>Day</th><th>Time</th><th>Subject</th><th>Teacher</th><th>Venue</th></tr>
              </thead>
              <tbody>
                <?php foreach ($studentTimetable as $item): ?>
                  <tr>
                    <td><?= sanitize($item['day_of_week']) ?></td>
                    <td><?= sanitize(date('H:i', strtotime($item['start_time']))) ?> – <?= sanitize(date('H:i', strtotime($item['end_time']))) ?></td>
                    <td><?= sanitize($item['subject']) ?></td>
                    <td><?= sanitize($item['teacher']) ?></td>
                    <td><?= sanitize($item['venue']) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <p class="mt-2">No timetable has been published for your level yet. Teachers will update this soon.</p>
        <?php endif; ?>
      </div>

      <div id="news" class="portal-section">
        <h2>Latest News</h2>
        <?php if ($newsItems): ?>
          <?php foreach ($newsItems as $news): ?>
          <div class="portal-card">
            <h3><?= sanitize($news['title']) ?></h3>
            <p style="margin:.5rem 0;font-weight:600;color:var(--green-mid)"><?= sanitize(date('d M Y', strtotime($news['published_at']))) ?></p>
            <p><?= sanitize($news['excerpt']) ?></p>
          </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="mt-2">No news announcements are available yet.</p>
        <?php endif; ?>
      </div>

    <?php else: ?>

      <div class="portal-grid">
        <div class="portal-tile">
          <div class="tile-icon">📝</div>
          <h4>Assign Work</h4>
          <p>Create assignments for any academic level.</p>
        </div>
        <div class="portal-tile">
          <div class="tile-icon">📅</div>
          <h4>Update Timetable</h4>
          <p>Set lesson schedules for teachers and students.</p>
        </div>
        <div class="portal-tile">
          <div class="tile-icon">📣</div>
          <h4>Send News</h4>
          <p>Publish announcements that display on the home page and portal.</p>
        </div>
      </div>

      <div class="portal-section portal-flex">
        <div id="create-assignment" class="portal-card portal-panel">
          <h2>Create Assignment</h2>
          <form method="POST" action="">
            <input type="hidden" name="action" value="add_assignment">
            <div class="form-group">
              <label for="title">Assignment Title</label>
              <input type="text" id="title" name="title" placeholder="e.g. Chemistry Lab Report" required>
            </div>
            <div class="form-group">
              <label for="level">Level</label>
              <select id="level" name="level" required>
                <option value="">Select level</option>
                <?php foreach ($levels as $key => $label): ?>
                <option value="<?= sanitize($key) ?>"><?= sanitize($label) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="due_date">Due Date</label>
              <input type="date" id="due_date" name="due_date" required>
            </div>
            <div class="form-group">
              <label for="description">Description</label>
              <textarea id="description" name="description" rows="4" placeholder="Assignment details and instructions"></textarea>
            </div>
            <button type="submit" class="btn-submit">Save Assignment</button>
          </form>
        </div>

        <div id="create-timetable" class="portal-card portal-panel">
          <h2>Create Timetable Entry</h2>
          <form method="POST" action="">
            <input type="hidden" name="action" value="add_timetable">
            <div class="form-group">
              <label for="level-timetable">Level</label>
              <select id="level-timetable" name="level" required>
                <option value="">Select level</option>
                <?php foreach ($levels as $key => $label): ?>
                <option value="<?= sanitize($key) ?>"><?= sanitize($label) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="day">Day</label>
              <select id="day" name="day" required>
                <option value="">Select day</option>
                <?php foreach ($dayOptions as $day): ?>
                <option value="<?= sanitize($day) ?>"><?= sanitize($day) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label for="start_time">Start Time</label>
                <input type="time" id="start_time" name="start_time" required>
              </div>
              <div class="form-group">
                <label for="end_time">End Time</label>
                <input type="time" id="end_time" name="end_time" required>
              </div>
            </div>
            <div class="form-group">
              <label for="subject">Subject</label>
              <input type="text" id="subject" name="subject" placeholder="e.g. Biology" required>
            </div>
            <div class="form-group">
              <label for="teacher">Teacher</label>
              <input type="text" id="teacher" name="teacher" placeholder="e.g. Mr. Kiprotich">
            </div>
            <div class="form-group">
              <label for="venue">Venue</label>
              <input type="text" id="venue" name="venue" placeholder="e.g. Room 12">
            </div>
            <button type="submit" class="btn-submit">Save Timetable Entry</button>
          </form>
        </div>
      </div>

      <div id="publish-news" class="portal-section portal-panel-full">
        <h2>Publish News</h2>
        <form method="POST" action="">
          <input type="hidden" name="action" value="post_news">
          <div class="form-group">
            <label for="news_title">Title</label>
            <input type="text" id="news_title" name="news_title" placeholder="Announcement title" required>
          </div>
          <div class="form-group">
            <label for="news_excerpt">Excerpt</label>
            <textarea id="news_excerpt" name="news_excerpt" rows="3" placeholder="Short summary" required></textarea>
          </div>
          <div class="form-group">
            <label for="news_content">Full Content</label>
            <textarea id="news_content" name="news_content" rows="4" placeholder="Optional detailed content"></textarea>
          </div>
          <div class="form-group">
            <label for="published_at">Publish Date</label>
            <input type="date" id="published_at" name="published_at" value="<?= date('Y-m-d') ?>">
          </div>
          <button type="submit" class="btn-submit">Publish News</button>
        </form>
      </div>

      <div class="portal-section">
        <h2>Current Assignments</h2>
        <?php if ($staffAssignments): ?>
          <div class="portal-card">
            <table class="portal-table">
              <thead>
                <tr><th>Title</th><th>Level</th><th>Due</th><th>Added By</th><th>Action</th></tr>
              </thead>
              <tbody>
                <?php foreach ($staffAssignments as $assignment): ?>
                <tr>
                  <td><?= sanitize($assignment['title']) ?></td>
                  <td><?= sanitize($levels[$assignment['level']]) ?></td>
                  <td><?= sanitize(date('d M Y', strtotime($assignment['due_date']))) ?></td>
                  <td><?= sanitize($assignment['teacher_name'] ?? 'Staff') ?></td>
                  <td>
                    <form method="POST" action="" style="display:inline">
                      <input type="hidden" name="action" value="delete_assignment">
                      <input type="hidden" name="assignment_id" value="<?= sanitize($assignment['id']) ?>">
                      <button type="submit" class="btn-submit" style="width:auto;padding:.65rem 1rem;background:#d9534f;">Delete</button>
                    </form>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <p>No assignments have been created yet.</p>
        <?php endif; ?>
      </div>

      <div class="portal-section">
        <h2>Timetable</h2>
        <?php if ($staffTimetable): ?>
          <div class="portal-card">
            <table class="portal-table">
              <thead>
                <tr><th>Level</th><th>Day</th><th>Time</th><th>Subject</th><th>Venue</th><th>Action</th></tr>
              </thead>
              <tbody>
                <?php foreach ($staffTimetable as $item): ?>
                <tr>
                  <td><?= sanitize($levels[$item['level']] ?? $item['level']) ?></td>
                  <td><?= sanitize($item['day_of_week']) ?></td>
                  <td><?= sanitize(date('H:i', strtotime($item['start_time']))) ?>–<?= sanitize(date('H:i', strtotime($item['end_time']))) ?></td>
                  <td><?= sanitize($item['subject']) ?></td>
                  <td><?= sanitize($item['venue']) ?></td>
                  <td>
                    <form method="POST" action="" style="display:inline">
                      <input type="hidden" name="action" value="delete_timetable">
                      <input type="hidden" name="timetable_id" value="<?= sanitize($item['id']) ?>">
                      <button type="submit" class="btn-submit" style="width:auto;padding:.65rem 1rem;background:#d9534f;">Delete</button>
                    </form>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <p>No timetable items have been entered yet.</p>
        <?php endif; ?>
      </div>

      <div class="portal-section">
        <h2>Recent News</h2>
        <?php if ($newsItems): ?>
          <div class="portal-card">
            <table class="portal-table">
              <thead>
                <tr><th>Title</th><th>Date</th><th>Excerpt</th><th>Action</th></tr>
              </thead>
              <tbody>
                <?php foreach ($newsItems as $news): ?>
                <tr>
                  <td><?= sanitize($news['title']) ?></td>
                  <td><?= sanitize(date('d M Y', strtotime($news['published_at']))) ?></td>
                  <td><?= sanitize($news['excerpt']) ?></td>
                  <td>
                    <form method="POST" action="" style="display:inline">
                      <input type="hidden" name="action" value="delete_news">
                      <input type="hidden" name="news_id" value="<?= sanitize($news['id']) ?>">
                      <button type="submit" class="btn-submit" style="width:auto;padding:.65rem 1rem;background:#d9534f;">Delete</button>
                    </form>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <p>No news announcements are available yet.</p>
        <?php endif; ?>
      </div>

      <div id="students" class="portal-section">
        <h2>Students &amp; Their Courses</h2>
        <?php if ($studentToEdit): ?>
          <div class="portal-card" style="margin-bottom:1.25rem">
            <h3 style="margin-bottom:1rem">Edit Student</h3>
            <form method="POST" action="">
              <input type="hidden" name="action" value="update_student">
              <input type="hidden" name="student_id" value="<?= (int)$studentToEdit['id'] ?>">
              <div class="form-row">
                <div class="form-group">
                  <label for="edit-full-name">Full Name</label>
                  <input type="text" id="edit-full-name" name="full_name" value="<?= sanitize($studentToEdit['full_name']) ?>" required>
                </div>
                <div class="form-group">
                  <label for="edit-email">Email</label>
                  <input type="email" id="edit-email" name="email" value="<?= sanitize($studentToEdit['email']) ?>" required>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label for="edit-student-id">Student ID</label>
                  <input type="text" id="edit-student-id" name="student_id_number" value="<?= sanitize($studentToEdit['student_id']) ?>" required>
                </div>
                <div class="form-group">
                  <label for="edit-level">Level</label>
                  <select id="edit-level" name="level" required>
                    <option value="">Select level</option>
                    <?php foreach ($levels as $key => $label): ?>
                      <option value="<?= sanitize($key) ?>" <?= ($studentToEdit['level'] === $key) ? 'selected' : '' ?>><?= sanitize($label) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label>Courses</label>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:.75rem;">
                  <?php foreach ($allCourses as $course): ?>
                    <label style="display:flex;align-items:center;gap:.5rem;padding:.6rem .75rem;border:1px solid #dfe7e2;border-radius:8px;background:#f7fbf8;">
                      <input type="checkbox" name="course_ids[]" value="<?= (int)$course['id'] ?>" <?= in_array((int)$course['id'], $selectedCourseIds, true) ? 'checked' : '' ?>>
                      <span><?= sanitize($course['code']) ?> — <?= sanitize($course['title']) ?></span>
                    </label>
                  <?php endforeach; ?>
                </div>
              </div>
              <div class="form-actions" style="display:flex;gap:.75rem;flex-wrap:wrap;margin-top:1rem;">
                <button type="submit" class="btn-submit">Save Student</button>
                <a href="portal.php#students" class="btn-mini filled">Cancel</a>
              </div>
            </form>
          </div>
        <?php endif; ?>

        <?php if ($studentRows): ?>
          <div class="portal-card">
            <table class="portal-table">
              <thead>
                <tr><th>Name</th><th>Student ID</th><th>Level</th><th>Courses</th><th>Email</th><th>Action</th></tr>
              </thead>
              <tbody>
                <?php foreach ($studentRows as $sr): ?>
                <tr>
                  <td><?= sanitize($sr['full_name']) ?></td>
                  <td><?= sanitize($sr['student_id'] ?? '—') ?></td>
                  <td><?= sanitize($levels[$sr['level']] ?? $sr['level'] ?? '—') ?></td>
                  <td>
                    <?php if ($sr['courses']): ?>
                      <?php foreach ($sr['courses'] as $c): ?>
                        <span class="course-chip" title="<?= sanitize($c['title']) ?>"><?= sanitize($c['code']) ?></span>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <span style="color:var(--text-light);font-size:.85rem">Not enrolled in any course yet</span>
                    <?php endif; ?>
                  </td>
                  <td><a href="mailto:<?= sanitize($sr['email']) ?>" class="btn-mini">✉️ Email</a></td>
                  <td>
                    <a href="portal.php?edit_student_id=<?= (int)$sr['id'] ?>#students" class="btn-mini filled" style="margin-right:.5rem">Edit</a>
                    <form method="POST" action="" style="display:inline">
                      <input type="hidden" name="action" value="delete_student">
                      <input type="hidden" name="student_id" value="<?= sanitize($sr['id']) ?>">
                      <button type="submit" class="btn-submit" style="width:auto;padding:.55rem 1rem;background:#d9534f;border:none;color:#fff;">Delete</button>
                    </form>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <p>No students have registered yet.</p>
        <?php endif; ?>
      </div>

      <div id="enquiries" class="portal-section">
        <div class="portal-section-header">
          <div>
            <h2>Enquiries</h2>
            <p class="section-intro">Admissions applications and contact form messages from prospective families. Use the actions below to reply quickly or update each request.</p>
          </div>
          <span class="panel-note"><?= count($admissionEnquiries) + count($contactEnquiries) ?> total enquiries</span>
        </div>

        <div class="enquiries-grid">
          <section class="enquiry-column">
            <div class="section-panel">
              <div class="section-panel-heading">
                <h3>Admissions Applications</h3>
                <span class="panel-note"><?= count($admissionEnquiries) ?> applications</span>
              </div>

              <?php if ($admissionEnquiries): ?>
                <?php foreach ($admissionEnquiries as $adm): ?>
                <article class="enquiry-card">
                  <div class="enquiry-card-head">
                    <div>
                      <strong><?= sanitize($adm['applicant_name']) ?></strong>
                      <div class="enquiry-meta">
                        <?= sanitize($levels[$adm['level_applying']] ?? $adm['level_applying']) ?> &middot; <?= sanitize(date('d M Y', strtotime($adm['submitted_at']))) ?>
                      </div>
                    </div>
                    <span class="status-pill status-<?= sanitize($adm['status']) ?>"><?= sanitize(ucfirst($adm['status'])) ?></span>
                  </div>

                  <div class="enquiry-card-body">
                    <p><strong>Email:</strong> <?= sanitize($adm['email']) ?></p>
                    <?php if ($adm['phone']): ?><p><strong>Phone:</strong> <?= sanitize($adm['phone']) ?></p><?php endif; ?>
                    <?php if ($adm['parent_name']): ?><p><strong>Guardian:</strong> <?= sanitize($adm['parent_name']) ?><?= $adm['parent_phone'] ? ' • ' . sanitize($adm['parent_phone']) : '' ?></p><?php endif; ?>
                    <?php if ($adm['message']): ?><p class="message-text"><?= sanitize($adm['message']) ?></p><?php endif; ?>
                    <?php
                      $admCourses = [];
                      if (!empty($adm['selected_courses'])) {
                          $admCourses = json_decode($adm['selected_courses'], true);
                          if (!is_array($admCourses)) {
                              $admCourses = array_filter(array_map('trim', explode(',', $adm['selected_courses'])));
                          }
                      }
                    ?>
                    <?php if ($admCourses): ?>
                      <div class="enquiry-meta enquiry-courses">
                        <strong>Selected courses:</strong> <?= implode(', ', array_map('sanitize', $admCourses)) ?>
                      </div>
                    <?php endif; ?>
                  </div>

                  <div class="enquiry-actions">
                    <a class="btn-mini filled" href="mailto:<?= sanitize($adm['email']) ?>?subject=<?= rawurlencode('Your Greenfield Academy Application') ?>&body=<?= rawurlencode("Dear {$adm['applicant_name']},\n\nThank you for your interest in Greenfield Academy.\n\n") ?>">✉️ Email Applicant</a>

                    <form method="POST" action="" class="status-form">
                      <input type="hidden" name="action" value="update_admission_status">
                      <input type="hidden" name="admission_id" value="<?= (int)$adm['id'] ?>">
                      <select name="status" onchange="this.form.submit()">
                        <?php foreach (['pending','reviewed','accepted','declined'] as $st): ?>
                          <option value="<?= $st ?>" <?= $adm['status'] === $st ? 'selected' : '' ?>><?= ucfirst($st) ?></option>
                        <?php endforeach; ?>
                      </select>
                    </form>
                  </div>
                </article>
                <?php endforeach; ?>
              <?php else: ?>
                <p class="empty-state">No admissions applications yet.</p>
              <?php endif; ?>
            </div>
          </section>

          <section class="enquiry-column">
            <div class="section-panel">
              <div class="section-panel-heading">
                <h3>Contact Messages</h3>
                <span class="panel-note"><?= count($contactEnquiries) ?> messages</span>
              </div>

              <?php if ($contactEnquiries): ?>
                <?php foreach ($contactEnquiries as $msg): ?>
                <article class="enquiry-card">
                  <div class="enquiry-card-head">
                    <div>
                      <strong><?= sanitize($msg['name']) ?></strong>
                      <div class="enquiry-meta">
                        <?= sanitize($msg['subject'] ?: 'General Enquiry') ?> &middot; <?= sanitize(date('d M Y', strtotime($msg['sent_at']))) ?>
                      </div>
                    </div>
                    <span class="status-pill status-<?= $msg['replied'] ? 'replied' : 'unreplied' ?>"><?= $msg['replied'] ? 'Replied' : 'Awaiting Reply' ?></span>
                  </div>

                  <div class="enquiry-card-body">
                    <p><strong>Email:</strong> <?= sanitize($msg['email']) ?></p>
                    <p class="message-text"><?= sanitize($msg['message']) ?></p>
                  </div>

                  <div class="enquiry-actions">
                    <a class="btn-mini filled" href="mailto:<?= sanitize($msg['email']) ?>?subject=<?= rawurlencode('Re: ' . ($msg['subject'] ?: 'Your Enquiry to Greenfield Academy')) ?>&body=<?= rawurlencode("Dear {$msg['name']},\n\nThank you for reaching out to Greenfield Academy.\n\n") ?>">✉️ Email</a>
                    <form method="POST" action="" class="status-form">
                      <input type="hidden" name="action" value="toggle_contact_replied">
                      <input type="hidden" name="contact_id" value="<?= (int)$msg['id'] ?>">
                      <button type="submit" class="btn-mini"><?= $msg['replied'] ? 'Mark as Awaiting Reply' : 'Mark as Replied' ?></button>
                    </form>
                  </div>
                </article>
                <?php endforeach; ?>
              <?php else: ?>
                <p class="empty-state">No contact messages yet.</p>
              <?php endif; ?>
            </div>
          </section>
        </div>
      </div>

    <?php endif; ?>
  </div>
</section>

<?php include __DIR__ . '/footer.php'; ?>