<?php
session_start();
require_once __DIR__ . '/schema.php';
if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    header('Location: login.html');
    exit;
}

if (empty($_SESSION['account_delete_token'])) {
    $_SESSION['account_delete_token'] = bin2hex(random_bytes(32));
}
if (empty($_SESSION['account_role_token'])) {
    $_SESSION['account_role_token'] = bin2hex(random_bytes(32));
}
$accountActionNotice = $_SESSION['account_action_notice'] ?? null;
unset($_SESSION['account_action_notice']);

$accounts = [];
$databaseError = '';
$conn = new mysqli('localhost', 'root', '', 'ticket_system');
if ($conn->connect_error) {
    $databaseError = 'Customer records are temporarily unavailable.';
} else {
    voyage_migrate_legacy_tables($conn);
    $result = $conn->query('SELECT id, username, email, phone, created_at, is_admin FROM accounts ORDER BY created_at DESC, id DESC');
    if ($result) {
        while ($account = $result->fetch_assoc()) {
            $accounts[] = $account;
        }
    } else {
        $databaseError = 'Customer records could not be loaded.';
    }
    $conn->close();
}

$adminCount = 0;
foreach ($accounts as $account) {
    if (!empty($account['is_admin'])) {
        $adminCount++;
    }
}
$accountCount = count($accounts);
$memberCount = $accountCount - $adminCount;
$escape = static function ($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>(function(){try{var t=localStorage.getItem("voyage-theme")||"dark";document.documentElement.classList.add(t+"-mode");if(document.body)document.body.classList.add(t+"-mode");else document.addEventListener("DOMContentLoaded",function(){document.body.classList.add(t+"-mode")});}catch(e){}})();</script>
    <script src="theme.js" defer></script>
    <title>Admin - Customers</title>
    <link rel="icon" type="image/x-icon" href="./pics/Icon/voyage1.ico">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <?php include __DIR__ . '/header.php'; ?>
    <main class="ticket-admin-page account-admin-page">
        <header class="ticket-admin-header">
            <div>
                <p class="ticket-admin-eyebrow">Admin / Access</p>
                <h1>Site customers</h1>
                <p class="ticket-admin-intro">Review customer contact details and access roles.</p>
            </div>
        </header>

        <?php if (is_array($accountActionNotice) && isset($accountActionNotice['message'], $accountActionNotice['type'])): ?>
            <div class="account-action-notice account-action-notice-<?php echo $escape($accountActionNotice['type']); ?>" role="status">
                <?php echo $escape($accountActionNotice['message']); ?>
            </div>
        <?php endif; ?>

        <section class="ticket-summary" aria-label="Customer summary">
            <div class="ticket-summary-item">
            <span>All customers</span>
                <strong><?php echo $accountCount; ?></strong>
            </div>
            <div class="ticket-summary-item">
                <span>Customers</span>
                <strong><?php echo $memberCount; ?></strong>
            </div>
            <div class="ticket-summary-item">
                <span>Administrators</span>
                <strong><?php echo $adminCount; ?></strong>
            </div>
        </section>

        <section class="ticket-records-panel" aria-label="Site customers">
            <div class="ticket-records-toolbar">
                <div>
                    <p id="account-results-count" aria-live="polite">Showing <?php echo $accountCount; ?> of <?php echo $accountCount; ?> customers</p>
                </div>
                <?php if ($databaseError === '' && $accounts): ?>
                    <div class="ticket-records-filters">
                        <label class="ticket-sr-only" for="account-search">Search customers</label>
                        <input id="account-search" type="search" placeholder="Search @CustomerID, +PhoneNumber or Email">
                        <label class="ticket-sr-only" for="account-role-filter">Filter by access role</label>
                        <select id="account-role-filter">
                            <option value="all">All</option>
                            <option value="member">Customer</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($databaseError !== ''): ?>
                <div class="ticket-empty-state" role="alert"><?php echo $escape($databaseError); ?></div>
            <?php elseif (!$accounts): ?>
                <div class="ticket-empty-state">
                    <strong>No customers found</strong>
                    <span>Registered customers will appear here.</span>
                </div>
            <?php else: ?>
                <div class="ticket-table-scroll">
                    <table class="ticket-records-table account-records-table">
                        <thead>
                            <tr>
                                <th scope="col">Customer</th>
                                <th scope="col">Contact</th>
                                <th scope="col">Created</th>
                                <th scope="col">Role</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($accounts as $account): ?>
                                <?php
                                    $role = !empty($account['is_admin']) ? 'admin' : 'member';
                                    $createdTimestamp = strtotime((string)$account['created_at']);
                                    $searchText = strtolower(implode(' ', [
                                        (string)$account['username'],
                                        (string)$account['email']
                                    ]));
                                ?>
                                <tr class="account-record-row" data-role="<?php echo $role; ?>" data-customer-id="<?php echo $escape($account['id']); ?>" data-phone-number="<?php echo $escape($account['phone']); ?>" data-search="<?php echo $escape($searchText); ?>">
                                    <td class="ticket-reference">
                                        <strong>@<?php echo $escape($account['id']); ?></strong>
                                        <span><?php echo $escape($account['username']); ?></span>
                                    </td>
                                    <td class="account-contact">
                                        <strong><?php echo $escape($account['email']); ?></strong>
                                        <span><?php echo $escape($account['phone']); ?></span>
                                    </td>
                                    <td>
                                        <?php if ($createdTimestamp !== false): ?>
                                            <time datetime="<?php echo $escape(date('c', $createdTimestamp)); ?>">
                                                <?php echo $escape(date('M j, Y', $createdTimestamp)); ?>
                                                <span class="ticket-cell-subtext"><?php echo $escape(date('H:i', $createdTimestamp)); ?></span>
                                            </time>
                                        <?php else: ?>
                                            <span class="ticket-cell-subtext">Not recorded</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="account-role-cell">
                                        <?php if ((int)$account['id'] === (int)$_SESSION['user_id']): ?>
                                            <span class="account-role account-role-<?php echo $role; ?>"><?php echo $role === 'admin' ? 'Administrator' : 'Customer'; ?></span>
                                        <?php else: ?>
                                            <form class="account-role-form" method="post" action="update_account_role.php">
                                                <input type="hidden" name="account_id" value="<?php echo $escape($account['id']); ?>">
                                                <input type="hidden" name="csrf_token" value="<?php echo $escape($_SESSION['account_role_token']); ?>">
                                                <label class="ticket-sr-only" for="account-role-<?php echo $escape($account['id']); ?>">Role for <?php echo $escape($account['username']); ?></label>
                                                <select id="account-role-<?php echo $escape($account['id']); ?>" name="role">
                                                    <option value="member"<?php echo $role === 'member' ? ' selected' : ''; ?>>Customer</option>
                                                    <option value="admin"<?php echo $role === 'admin' ? ' selected' : ''; ?>>Administrator</option>
                                                </select>
                                                <button class="account-role-save" type="submit">Save</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                    <td class="account-actions">
                                        <?php if ((int)$account['id'] === (int)$_SESSION['user_id']): ?>
                                            <span class="account-current-label">Current Administrator</span>
                                        <?php else: ?>
                                            <form method="post" action="delete_account.php" onsubmit="return confirm('Delete this customer and all its ticket records? This cannot be undone.');">
                                                <input type="hidden" name="account_id" value="<?php echo $escape($account['id']); ?>">
                                                <input type="hidden" name="csrf_token" value="<?php echo $escape($_SESSION['account_delete_token']); ?>">
                                                <button class="account-delete-button" type="submit">Delete</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr id="account-filter-empty" hidden>
                                <td colspan="5" class="ticket-filter-empty-cell">No customers match these filters.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <script>
        (function () {
            const searchInput = document.getElementById('account-search');
            const roleFilter = document.getElementById('account-role-filter');
            const resultCount = document.getElementById('account-results-count');
            const emptyRow = document.getElementById('account-filter-empty');
            const rows = Array.from(document.querySelectorAll('.account-record-row'));

            if (!searchInput || !roleFilter || !resultCount || rows.length === 0) return;

            function filterAccounts() {
                const query = searchInput.value.trim().toLowerCase();
                const selectedRole = roleFilter.value;
                let visibleCount = 0;

                rows.forEach(function (row) {
                    let matchesQuery = query === '';
                    if (query.startsWith('@')) {
                        const customerIdQuery = query.slice(1);
                        matchesQuery = customerIdQuery !== '' && row.dataset.customerId.includes(customerIdQuery);
                    } else if (query.startsWith('+')) {
                        matchesQuery = query.length > 1 && row.dataset.phoneNumber.includes(query);
                    } else if (query !== '') {
                        matchesQuery = row.dataset.search.includes(query);
                    }
                    const matchesRole = selectedRole === 'all' || row.dataset.role === selectedRole;
                    row.hidden = !(matchesQuery && matchesRole);
                    if (!row.hidden) visibleCount++;
                });

                if (emptyRow) emptyRow.hidden = visibleCount !== 0;
                resultCount.textContent = 'Showing ' + visibleCount + ' of ' + rows.length + ' customers';
            }

            searchInput.addEventListener('input', filterAccounts);
            roleFilter.addEventListener('change', filterAccounts);
        })();
    </script>

    <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>