<?php
// Simulated AI Generation Script
header('Content-Type: application/json');

if (!isset($_GET['subject'])) {
    echo json_encode([]);
    exit;
}

$subject = strtolower(trim($_GET['subject']));
$questions = [];

// Determine subject category for realistic "AI" formatting
if (strpos($subject, 'math') !== false || strpos($subject, 'algebra') !== false || strpos($subject, 'calculus') !== false) {
    $questions = [
        ['q' => 'What is the standard form of a quadratic equation?', 'a' => 'ax² + bx + c = 0', 'b' => 'y = mx + b', 'c' => 'a² + b² = c²', 'd' => 'E = mc²', 'ans' => 'A'],
        ['q' => 'What is the derivative of x²?', 'a' => 'x', 'b' => '2x', 'c' => 'x³/3', 'd' => '1', 'ans' => 'B'],
        ['q' => 'What is the value of Pi to two decimal places?', 'a' => '3.12', 'b' => '3.16', 'c' => '3.14', 'd' => '3.18', 'ans' => 'C'],
        ['q' => 'What is 15% of 200?', 'a' => '25', 'b' => '45', 'c' => '35', 'd' => '30', 'ans' => 'D'],
        ['q' => 'Find the square root of 144.', 'a' => '12', 'b' => '14', 'c' => '10', 'd' => '16', 'ans' => 'A']
    ];
} elseif (strpos($subject, 'science') !== false || strpos($subject, 'physics') !== false || strpos($subject, 'chemistry') !== false) {
    $questions = [
        ['q' => 'What is the chemical symbol for Gold?', 'a' => 'Ag', 'b' => 'Au', 'c' => 'Fe', 'd' => 'Pb', 'ans' => 'B'],
        ['q' => 'What is the powerhouse of the cell?', 'a' => 'Nucleus', 'b' => 'Mitochondria', 'c' => 'Ribosome', 'd' => 'Chloroplast', 'ans' => 'B'],
        ['q' => 'How many planets are in our solar system?', 'a' => '7', 'b' => '9', 'c' => '8', 'd' => '10', 'ans' => 'C'],
        ['q' => 'What gas do plants absorb during photosynthesis?', 'a' => 'Oxygen', 'b' => 'Nitrogen', 'c' => 'Hydrogen', 'd' => 'Carbon Dioxide', 'ans' => 'D'],
        ['q' => 'Who formulated the theory of relativity?', 'a' => 'Albert Einstein', 'b' => 'Isaac Newton', 'c' => 'Galileo Galilei', 'd' => 'Niels Bohr', 'ans' => 'A']
    ];
} elseif (strpos($subject, 'english') !== false || strpos($subject, 'literature') !== false || strpos($subject, 'grammar') !== false) {
    $questions = [
        ['q' => 'Who wrote "Romeo and Juliet"?', 'a' => 'Charles Dickens', 'b' => 'William Shakespeare', 'c' => 'Mark Twain', 'd' => 'Jane Austen', 'ans' => 'B'],
        ['q' => 'Identify the adjective in this sentence: "The quick brown fox jumps."', 'a' => 'quick', 'b' => 'fox', 'c' => 'jumps', 'd' => 'The', 'ans' => 'A'],
        ['q' => 'What is a synonym for "happy"?', 'a' => 'Sad', 'b' => 'Joyful', 'c' => 'Angry', 'd' => 'Tired', 'ans' => 'B'],
        ['q' => 'Which of the following is a metaphor?', 'a' => 'As brave as a lion', 'b' => 'Time is money', 'c' => 'Like a diamond in the sky', 'd' => 'Singing loudly', 'ans' => 'B'],
        ['q' => 'What is the past tense of "run"?', 'a' => 'Runned', 'b' => 'Running', 'c' => 'Ran', 'd' => 'Runs', 'ans' => 'C']
    ];
} elseif (strpos($subject, 'computer') !== false || strpos($subject, 'programming') !== false || strpos($subject, 'it') !== false) {
    $questions = [
        ['q' => 'What does HTML stand for?', 'a' => 'Hyper Text Markup Language', 'b' => 'High Text Machine Language', 'c' => 'Hyperlink and Text Markup Language', 'd' => 'Home Tool Markup Language', 'ans' => 'A'],
        ['q' => 'Which language is used for styling web pages?', 'a' => 'HTML', 'b' => 'CSS', 'c' => 'XML', 'd' => 'Python', 'ans' => 'B'],
        ['q' => 'What is the main brain of a computer?', 'a' => 'RAM', 'b' => 'Hard Drive', 'c' => 'CPU', 'd' => 'Motherboard', 'ans' => 'C'],
        ['q' => 'Which of these is not a programming language?', 'a' => 'Java', 'b' => 'Python', 'c' => 'C++', 'd' => 'HTTP', 'ans' => 'D'],
        ['q' => 'What does a compiler do?', 'a' => 'Translates source code to machine readable code', 'b' => 'Executes the program immediately', 'c' => 'Finds logic errors in the code', 'd' => 'Uploads code to the server', 'ans' => 'A']
    ];
} else {
    // Generic fallback questions
    $questions = [
        ['q' => 'Which is considered the most fundamental principle in ' . ucfirst($subject) . '?', 'a' => 'Analytical approach', 'b' => 'Theoretical framework', 'c' => 'Core axioms', 'd' => 'Empirical studies', 'ans' => 'C'],
        ['q' => 'Who is recognized as a leading pioneer in the field of ' . ucfirst($subject) . '?', 'a' => 'Dr. Smith', 'b' => 'Various anonymous contributors', 'c' => 'Standard historical figures', 'd' => 'No single individual', 'ans' => 'C'],
        ['q' => 'What is the primary objective when studying ' . ucfirst($subject) . '?', 'a' => 'Memorization of facts', 'b' => 'Understanding core concepts', 'c' => 'Passing the examination', 'd' => 'Reading all assigned literature', 'ans' => 'B'],
        ['q' => 'In practical applications, what is the most significant challenge of ' . ucfirst($subject) . '?', 'a' => 'Lack of resources', 'b' => 'Complex theoretical models', 'c' => 'Implementing theory into practice', 'd' => 'Time management', 'ans' => 'C'],
        ['q' => 'Which of the following best describes the modern approach to ' . ucfirst($subject) . '?', 'a' => 'Strictly traditional', 'b' => 'Highly adaptive and interdisciplinary', 'c' => 'Completely computerized', 'd' => 'Focusing solely on history', 'ans' => 'B']
    ];
}

// Randomly shuffle options slightly or pick subset (here we just return the 5 core AI ones)
echo json_encode($questions);
?>
