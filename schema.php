<?php
function voyage_migrate_legacy_tables(mysqli $conn): void
{
    $renames = [
        'users' => 'accounts',
        'available_tickets' => 'available_trips',
        'tickets' => 'ticket_records'
    ];

    $tableExists = static function (string $tableName) use ($conn): bool {
        $statement = $conn->prepare(
            'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?'
        );
        if (!$statement) {
            throw new RuntimeException('Unable to inspect the Voyage database schema.');
        }

        $statement->bind_param('s', $tableName);
        if (!$statement->execute()) {
            $statement->close();
            throw new RuntimeException('Unable to inspect the Voyage database schema.');
        }

        $statement->bind_result($tableCount);
        $statement->fetch();
        $statement->close();

        return (int)$tableCount > 0;
    };

    foreach ($renames as $legacyName => $newName) {
        $legacyExists = $tableExists($legacyName);
        $newExists = $tableExists($newName);

        if ($legacyExists && $newExists) {
            throw new RuntimeException("Both $legacyName and $newName exist; merge the tables before continuing.");
        }

        if ($legacyExists && !$conn->query("RENAME TABLE `$legacyName` TO `$newName`")) {
            throw new RuntimeException('Unable to migrate the Voyage database tables.');
        }
    }

    $cardColumn = $conn->prepare(
        "SELECT character_maximum_length FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'ticket_records' AND column_name = 'card_number'"
    );
    if (!$cardColumn || !$cardColumn->execute()) {
        if ($cardColumn) {
            $cardColumn->close();
        }
        throw new RuntimeException('Unable to inspect the ticket record schema.');
    }

    $cardColumn->bind_result($cardNumberLength);
    $hasCardColumn = $cardColumn->fetch();
    $cardColumn->close();

    if ($hasCardColumn && (int)$cardNumberLength < 50 && !$conn->query("ALTER TABLE ticket_records MODIFY COLUMN card_number VARCHAR(50) NOT NULL DEFAULT ''")) {
        throw new RuntimeException('Unable to update the ticket record schema.');
    }

    if ($tableExists('ticket_records')) {
        $qrColumn = $conn->prepare(
            "SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'ticket_records' AND column_name = 'qr_token'"
        );
        if (!$qrColumn || !$qrColumn->execute()) {
            if ($qrColumn) {
                $qrColumn->close();
            }
            throw new RuntimeException('Unable to inspect ticket QR data.');
        }

        $qrColumn->bind_result($qrColumnCount);
        $qrColumn->fetch();
        $qrColumn->close();

        if ((int)$qrColumnCount === 0 && !$conn->query('ALTER TABLE ticket_records ADD COLUMN qr_token CHAR(64) NULL')) {
            throw new RuntimeException('Unable to add ticket QR data.');
        }

        $ticketsWithoutTokens = $conn->query("SELECT id FROM ticket_records WHERE qr_token IS NULL OR qr_token = ''");
        $tokenUpdate = $conn->prepare("UPDATE ticket_records SET qr_token = ? WHERE id = ? AND (qr_token IS NULL OR qr_token = '')");
        if (!$ticketsWithoutTokens || !$tokenUpdate) {
            if ($tokenUpdate) {
                $tokenUpdate->close();
            }
            throw new RuntimeException('Unable to prepare ticket QR data.');
        }

        while ($ticket = $ticketsWithoutTokens->fetch_assoc()) {
            $qrToken = bin2hex(random_bytes(32));
            $ticketId = (int)$ticket['id'];
            $tokenUpdate->bind_param('si', $qrToken, $ticketId);
            if (!$tokenUpdate->execute()) {
                $ticketsWithoutTokens->free();
                $tokenUpdate->close();
                throw new RuntimeException('Unable to create a ticket QR token.');
            }
        }

        $ticketsWithoutTokens->free();
        $tokenUpdate->close();
    }
}