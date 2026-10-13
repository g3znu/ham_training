<?php
declare(strict_types=1);

/**
 * Shared question-bank helpers for the Foundation quiz pages.
 * Save as includes/quiz_lib.php (load includes/user_auth.php first).
 */

/** Mock exam layout: section number => [name, number of questions]. */
function mockSections(): array
{
    return [
        1 => ['Amateur Radio, Licences and Callsigns', 6],
        2 => ['Technical Aspects', 3],
        3 => ['Transmitters and Receivers', 3],
        4 => ['Feeders and Antennas', 3],
        5 => ['Propagation', 2],
        6 => ['EMC', 3],
        7 => ['Operating', 3],
        8 => ['Safety', 3],
    ];
}

/** Every question number in the bank, or just those in one section. */
function allQuestionIds(mysqli $db, ?int $section = null): array
{
    $ids = [];

    if ($section === null) {
        $result = $db->query('SELECT question FROM training_foundation');
        while ($row = $result->fetch_row()) {
            $ids[] = (int) $row[0];
        }
        $result->free();
        return $ids;
    }

    $stmt = $db->prepare('SELECT question FROM training_foundation WHERE section = ?');
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
function saveTest(mysqli $db, string $email, array $questionIds): void
{
    $del = $db->prepare('DELETE FROM training_test WHERE email_address = ?');
    $del->bind_param('s', $email);
    $del->execute();
    $del->close();

    $get = $db->prepare('SELECT answer, section FROM training_foundation WHERE question = ?');
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

/** Question buttons, the Check / More Q's buttons and the frame the questions open in. */
function renderTestUi(int $count, string $checkUrl): void
{
    ?>
    <div class="qgrid" role="group" aria-label="Questions">
      <?php for ($i = 1; $i <= $count; $i++): ?>
        <a class="btn qbtn" href="one_question.php?q=<?= $i ?>" target="a_program"
           onclick="this.classList.add('visited')"><?= $i ?></a>
      <?php endfor; ?>
    </div>

    <div class="btn-row">
      <a class="btn btn-primary" href="<?= h($checkUrl) ?>" onclick="return openCheck(this.href)">Check</a>
      <a class="btn" href="main.php">More Q's</a>
    </div>

    <iframe name="a_program" title="Question" class="qframe" src="about:blank"></iframe>

    <script>
    function openCheck(url) {
      window.open(url, 'results',
        'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=820,height=610');
      return false;
    }
    </script>
    <?php
}
