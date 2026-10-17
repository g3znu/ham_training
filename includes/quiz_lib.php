<?php
declare(strict_types=1);

/**
 * Shared question-bank helpers for the Foundation quiz pages.
 * Save as includes/quiz_lib.php (load includes/user_auth.php first).
 */

/**
 * Course settings. Table and column names come only from this fixed list
 * (never from the web address), so they are safe to use in queries.
 */
function courseConfig(string $course = 'foundation'): array
{
    static $courses = [
        'foundation' => [
            'table' => 'training_foundation',
            'key'   => 'question',
            'type'  => 'F',
            'sections' => [
                1 => ['Amateur Radio, Licences and Callsigns', 6],
                2 => ['Technical Aspects', 3],
                3 => ['Transmitters and Receivers', 3],
                4 => ['Feeders and Antennas', 3],
                5 => ['Propagation', 2],
                6 => ['EMC', 3],
                7 => ['Operating', 3],
                8 => ['Safety', 3],
            ],
        ],
        'intermediate' => [
            'table' => 'training_intermediate',
            'key'   => 'question_i',
            'type'  => 'I',
            'sections' => [
                1 => ['Licence Conditions', 6],
                2 => ['Technical Aspects', 14],
                3 => ['Transmitters and Receivers', 7],
                4 => ['Feeders and Antennas', 4],
                5 => ['Propagation', 3],
                6 => ['EMC', 4],
                7 => ['Operating Practices and Procedures', 2],
                8 => ['Safety', 3],
                9 => ['Measurements and Construction', 3],
            ],
        ],
    ];

    return $courses[$course] ?? $courses['foundation'];
}

/** Mock exam layout: section number => [name, number of questions]. */
function mockSections(string $course = 'foundation'): array
{
    return courseConfig($course)['sections'];
}

/** Every question number in the bank, or just those in one section. */
function allQuestionIds(mysqli $db, ?int $section = null, string $course = 'foundation'): array
{
    $cfg = courseConfig($course);
    $ids = [];

    if ($section === null) {
        $result = $db->query('SELECT ' . $cfg['key'] . ' FROM ' . $cfg['table']);
        while ($row = $result->fetch_row()) {
            $ids[] = (int) $row[0];
        }
        $result->free();
        return $ids;
    }

    $stmt = $db->prepare('SELECT ' . $cfg['key'] . ' FROM ' . $cfg['table'] . ' WHERE section = ?');
    $stmt->bind_param('i', $section);
    $stmt->execute();
    $stmt->bind_result($id);
    while ($stmt->fetch()) {
        $ids[] = (int) $id;
    }
    $stmt->close();
    return $ids;
}

function pickRandom(array $ids, int $count): array
{
    shuffle($ids);
    return array_slice($ids, 0, $count);
}

/** Replaces the learner's current test with these questions (numbered 1, 2, 3...). */
function saveTest(mysqli $db, string $email, array $questionIds, string $course = 'foundation'): void
{
    $cfg = courseConfig($course);

    unset($_SESSION['test_locked']);   // a new test starts unlocked

    $del = $db->prepare('DELETE FROM training_test WHERE email_address = ?');
    $del->bind_param('s', $email);
    $del->execute();
    $del->close();

    $get = $db->prepare(
        'SELECT answer, section FROM ' . $cfg['table'] . ' WHERE ' . $cfg['key'] . ' = ?'
    );
    $ins = $db->prepare(
        'INSERT INTO training_test (email_address, q_no, question, answer, section) VALUES (?, ?, ?, ?, ?)'
    );

    foreach (array_values($questionIds) as $i => $id) {
        $qNo = $i + 1;

        $get->bind_param('i', $id);
        $get->execute();
        $get->store_result();
        $get->bind_result($answer, $section);
        if (!$get->fetch()) {
            continue;
        }
        $get->free_result();

        $ins->bind_param('siisi', $email, $qNo, $id, $answer, $section);
        $ins->execute();
    }

    $get->close();
    $ins->close();
}

/** Pressing Check locks the learner's answers until they start a new test. */
function lockTest(): void
{
    $_SESSION['test_locked'] = true;
}

function testIsLocked(): bool
{
    return !empty($_SESSION['test_locked']);
}

/**
 * Question buttons, the Check / Back to Question selection buttons and the frame the questions open in.
 *
 * $autosave = true:  answers save as soon as they are chosen, and a question's button turns
 *                    yellow only once it has been answered (the question page tells this page).
 * $autosave = false: the older behaviour, where a button turns yellow when it is clicked.
 */
function renderTestUi(int $count, string $checkUrl, bool $autosave = false): void
{
    ?>
    <div class="qgrid" role="group" aria-label="Questions">
      <?php for ($i = 1; $i <= $count; $i++): ?>
        <a class="btn qbtn" href="one_question.php?q=<?= $i ?>" target="a_program" data-q="<?= $i ?>"<?php
            if (!$autosave): ?> onclick="this.classList.add('visited')"<?php endif; ?>><?= $i ?></a>
      <?php endfor; ?>
    </div>

    <div class="btn-row">
      <a class="btn btn-primary" href="<?= h($checkUrl) ?>" onclick="return openCheck(this.href)">Check</a>
      <a class="btn" href="main.php">Back to Question selection</a>
    </div>

    <iframe name="a_program" title="Question" class="qframe" src="about:blank"></iframe>

    <script>
    function openCheck(url) {
      window.open(url, 'results',
        'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=820,height=610');
      return false;
    }
    <?php if ($autosave): ?>

    // The question page reports each saved answer; mark that question's button as answered.
    window.addEventListener('message', function (e) {
      if (e.origin !== window.location.origin || !e.data || e.data.type !== 'answered') { return; }
      var n = Number(e.data.q);
      var button = document.querySelector('.qbtn[data-q="' + n + '"]');
      if (button) {
        button.classList.add('answered');
        button.setAttribute('aria-label', 'Question ' + n + ', answered');
      }
    });
    <?php endif; ?>
    </script>
    <?php
}
