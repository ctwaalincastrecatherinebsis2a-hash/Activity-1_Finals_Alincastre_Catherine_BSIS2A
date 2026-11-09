
<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$page_title = 'Login';
$base_path = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = strtolower(trim($_POST['email'] ?? ''));
    $pass = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($pass, $user['password'])) {
        $error = 'Invalid email or password.';

    } elseif ($user['status'] !== 'approved') {
        $error = 'Your account is ' .
            ($user['status'] === 'pending'
                ? 'waiting for admin approval'
                : 'not approved') . '.';

    } else {

        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['full_name'],
            'email' => $user['email'],
            'role' => $user['role']
        ];

        header('Location: dashboard.php');
        exit;
    }
}

include 'includes/header.php';
?>

<div class="auth-wrap">
    <div class="auth-card">

        <a class="back-link" href="index.php">
            &larr; Back to books
        </a>

        <h1>Welcome!</h1>

        <p class="muted">
            Log in to your Wattpad Books Hub account.
        </p>

        <?php if ($error): ?>
            <div class="alert error">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

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
                <label for="password">Password</label>

                <div class="password-wrapper">

                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                    >

                    <button
                        type="button"
                        class="password-toggle"
                        id="togglePassword"
                        aria-label="Show password"
                        title="Show password"
                    >
                        <svg
                            id="eyeIcon"
                            xmlns="http://www.w3.org/2000/svg"
                            width="20"
                            height="20"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>

                </div>
            </div>

            <button type="submit" class="btn full">
                Login
            </button>

        </form>

        <p class="muted">
            New here?
            <a href="register.php">Create an account</a>
        </p>

    </div>
</div>

<style>
.password-wrapper {
    position: relative;
    width: 100%;
}

.password-wrapper input {
    width: 100%;
    padding-right: 45px;
    box-sizing: border-box;
}

.password-toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: transparent;
    border: none;
    cursor: pointer;
    color: #777;
    padding: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.password-toggle:hover {
    color: #0B7A3E;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const password = document.getElementById('password');
    const toggle = document.getElementById('togglePassword');
    const eyeIcon = document.getElementById('eyeIcon');

    toggle.addEventListener('click', function () {

        if (password.type === 'password') {
            password.type = 'text';

            toggle.setAttribute('aria-label', 'Hide password');
            toggle.setAttribute('title', 'Hide password');

            eyeIcon.innerHTML = `
                <path d="M3 3l18 18"/>
                <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/>
                <path d="M9.9 5.2A10.5 10.5 0 0 1 12 5c6.5 0 10 7 10 7a14.5 14.5 0 0 1-3.2 4.2"/>
                <path d="M6.2 6.2C3.5 8.1 2 12 2 12s3.5 7 10 7a10.5 10.5 0 0 0 4.2-.9"/>
            `;

        } else {
            password.type = 'password';

            toggle.setAttribute('aria-label', 'Show password');
            toggle.setAttribute('title', 'Show password');

            eyeIcon.innerHTML = `
                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/>
                <circle cx="12" cy="12" r="3"/>
            `;
        }

    });

});
</script>

<?php include 'includes/footer.php'; ?>
