<?php
require_once __DIR__ . '/config.php';
$pageTitle = 'Academics';

// Fetch all departments with their courses
$depts = db()->query("
    SELECT d.*, GROUP_CONCAT(c.id,'|',c.code,'|',c.title,'|',c.description,'|',c.credits
                             ORDER BY c.code SEPARATOR ';;')
           AS courses_raw
    FROM departments d
    LEFT JOIN courses c ON c.department_id = d.id
    GROUP BY d.id
    ORDER BY FIELD(d.level,'early_years','o_level','a_level','university'), d.name
")->fetchAll();

// Organise by level
$byLevel = [];
foreach($depts as $d) {
    $d['courses'] = [];
    if ($d['courses_raw']) {
        foreach(explode(';;', $d['courses_raw']) as $cr) {
            [$id,$code,$title,$desc,$credits] = explode('|', $cr, 5);
            $d['courses'][] = compact('id','code','title','desc','credits');
        }
    }
    unset($d['courses_raw']);
    $byLevel[$d['level']][] = $d;
}

$levels = [
    'early_years' => ['label'=>'Early Years',  'icon'=>'🌱'],
    'o_level'     => ['label'=>'O Level',       'icon'=>'🔬'],
    'a_level'     => ['label'=>'A Level',       'icon'=>'⚗️'],
    'university'  => ['label'=>'University',    'icon'=>'🏛️'],
];
$activeLevel = $_GET['level'] ?? 'early_years';
if (!array_key_exists($activeLevel, $levels)) $activeLevel = 'early_years';
?>
<?php include __DIR__ . '/header.php'; ?>

<div class="page-hero">
  <div class="container">
    <div class="breadcrumb"><a href="index.php">Home</a> / Academics</div>
    <h1>Academic Programmes</h1>
    <p>Explore our full curriculum — from Early Years all the way to University.</p>
  </div>
</div>

<section class="section section-alt">
  <div class="container">

    <!-- LEVEL TABS -->
    <div class="level-tabs">
      <?php foreach($levels as $key => $lv): ?>
      <button class="level-tab <?= $key===$activeLevel?'active':'' ?>" data-level="<?= $key ?>">
        <?= $lv['icon'] ?> <?= $lv['label'] ?>
      </button>
      <?php endforeach; ?>
    </div>

    <!-- LEVEL PANELS -->
    <?php foreach($levels as $key => $lv): ?>
    <div class="level-panel <?= $key !== $activeLevel ? 'hidden' : '' ?>" data-level="<?= $key ?>">

      <div class="section-head">
        <span class="section-label"><?= $lv['icon'] ?> <?= $lv['label'] ?></span>
        <?php
        $descriptions = [
          'early_years' => 'Our Early Years programme (ages 2–7) draws on Montessori and play-based learning principles to ignite a lifelong love of discovery.',
          'o_level'     => 'Our IGCSE O Level programme follows the Cambridge curriculum, equipping students with internationally recognised qualifications.',
          'a_level'     => 'Cambridge A Level courses provide the academic depth required for entry into top universities in Kenya and abroad.',
          'university'  => 'Our degree programmes are fully accredited and designed with industry input to ensure graduates are career-ready from day one.',
        ];
        ?>
        <p class="section-sub"><?= $descriptions[$key] ?></p>
      </div>

      <?php if(isset($byLevel[$key])): foreach($byLevel[$key] as $dept): ?>
      <div class="mt-3">
        <h3 style="color:var(--green-dark);margin-bottom:1rem">
          <?= sanitize($dept['icon']??'📘') ?> <?= sanitize($dept['name']) ?>
        </h3>
        <?php if($dept['description']): ?>
        <p style="margin-bottom:1.25rem"><?= sanitize($dept['description']) ?></p>
        <?php endif; ?>

        <?php if($dept['courses']): ?>
        <div class="courses-grid">
          <?php foreach($dept['courses'] as $c): ?>
          <div class="course-card">
            <span class="course-code"><?= sanitize($c['code']) ?></span>
            <h4><?= sanitize($c['title']) ?></h4>
            <p><?= sanitize($c['desc']) ?></p>
            <?php if($c['credits'] > 0): ?>
            <p class="mt-1" style="font-size:.78rem;color:var(--green-light);font-weight:600"><?= (int)$c['credits'] ?> Credits</p>
            <?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p style="color:var(--text-light)">Courses coming soon.</p>
        <?php endif; ?>
      </div>
      <?php endforeach; else: ?>
      <p style="color:var(--text-light)">No programmes found for this level yet.</p>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>

  </div>
</section>

<div class="cta-band">
  <div class="container">
    <h2>Interested in Enrolling?</h2>
    <p>Our admissions team is happy to guide you through the right programme for your child.</p>
    <a href="admissions.php" class="btn-dark">Apply Now</a>
  </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>