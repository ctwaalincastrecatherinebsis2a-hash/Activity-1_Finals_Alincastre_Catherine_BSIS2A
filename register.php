
<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$page_title = 'Create Account';
$base_path = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['full_name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $pass = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    // validate input
    if (
        $name === '' ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($pass) < 8 ||
        $pass !== $confirm
    ) {
        $error = 'Please complete all fields. Use a valid email and password with at least 8 characters.';
    } else {

        try {
            // check if email already exists
            $check = $pdo->prepare(
                "SELECT id FROM users WHERE email = ?"
            );
            $check->execute([$email]);

            if ($check->fetch()) {
                $error = 'This email is already registered.';
            } else {

                // create account with pending status
                $stmt = $pdo->prepare("
                    INSERT INTO users
                    (full_name, email, password, role, status)
                    VALUES (?, ?, ?, 'user', 'pending')
                ");

                $stmt->execute([
                    $name,
                    $email,
                    password_hash($pass, PASSWORD_DEFAULT)
                ]);

                flash(
                    'Registration submitted. Please wait for admin approval.',
                    'warning'
                );

                header('Location: login.php');
                exit;
            }

        } catch (PDOException $e) {
            $error = 'Unable to create account. Please try again.';
        }
    }
}

include 'includes/header.php';
?>

<div class="auth-wrap">
    <div class="auth-card">

        <a class="back-link" href="index.php">
            &larr; Back to books
        </a>

        <h1>Create your account</h1>

        <p class="muted">
            Join the Wattpad Books Hub community.
        </p>

        <?php if ($error): ?>
            <div class="alert error">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <div class="notice">
            Your account needs admin approval before you can log in.
        </div>

        <form method="POST">

            <div class="field">
                <label for="full_name">Full name</label>

                <input
                    type="text"
                    id="full_name"
                    name="full_name"
                    required
                    value="<?= e($_POST['full_name'] ?? '') ?>"
                >
            </div>

            <div class="field">
                <label for="email">Email address</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    required
                    value="<?= e($_POST['email'] ?? '') ?>"
                >
            </div>

            <div class="field">
                <label for="password">
                    Password (minimum 8 characters)
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    minlength="8"
                    required
                >
            </div>

            <div class="field">
                <label for="confirm_password">
                    Confirm password
                </label>

                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    required
                >
            </div>

            <button type="submit" class="btn full">
                Create account
            </button>

        </form>

        <p class="muted">
            Already registered?
            <a href="login.php">Login</a>
        </p>

    </div>
</div>

<?php include 'includes/footer.php'; ?>
