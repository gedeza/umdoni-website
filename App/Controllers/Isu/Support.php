<?php

namespace App\Controllers\Isu;

use App\Models\IsuTicket;
use App\Models\IsuAdmin;
use App\Models\IsuAudit;
use Core\View;

/**
 * Support — ISU helpdesk / ticketing (Phase 5).
 *
 *   /isu/support/index      ticket list (filter by status) + new-ticket form
 *   /isu/support/create     (POST) log a new ticket
 *   /isu/support/view?id=   ticket detail: thread + status/priority/assignee
 *   /isu/support/reply      (POST) add a reply/update to a ticket
 *   /isu/support/update     (POST) change status / priority / assignee
 *
 * ISU-only: staff log requests uMdoni raise by email/phone.
 *
 * @author Nhlanhla Mnyandu <nhlanhla@isutech.co.za>
 */
class Support extends Guarded
{
    public function indexAction()
    {
        $filter = $_GET['status'] ?? '';
        if (!array_key_exists($filter, IsuTicket::statuses())) { $filter = ''; }

        View::render('isu/support/index.php', [
            'page_title' => 'Support',
            'page_desc'  => 'Log and track support requests from uMdoni.',
            'tickets'    => IsuTicket::all($filter),
            'counts'     => IsuTicket::counts(),
            'filter'     => $filter,
            'statuses'   => IsuTicket::statuses(),
            'priorities' => IsuTicket::priorities(),
            'categories' => IsuTicket::categories(),
            'csrf_token' => $this->csrfToken(),
            'flash'      => $this->takeFlash(),
        ], 'isu');
    }

    public function createAction()
    {
        if (!$this->guardPost()) return;

        $subject = trim($_POST['subject'] ?? '');
        if ($subject === '' || mb_strlen($subject) > 200) {
            return $this->back('error', 'Please enter a subject (up to 200 characters).');
        }
        $id = IsuTicket::create([
            'subject'           => $subject,
            'description'       => trim($_POST['description'] ?? ''),
            'category'          => in_array($_POST['category'] ?? '', IsuTicket::categories(), true) ? $_POST['category'] : 'Other',
            'priority'          => $_POST['priority'] ?? 'normal',
            'requester_name'    => trim($_POST['requester_name'] ?? ''),
            'requester_contact' => trim($_POST['requester_contact'] ?? ''),
            'created_by'        => $this->isuUser['id'] ?? null,
        ]);

        if (!$id) {
            return $this->back('error', 'Could not create the ticket. Please try again.');
        }
        IsuAudit::log('ticket.create', 'Created ticket ' . $subject, $this->me());
        $this->flash('success', 'Ticket created.');
        redirect('isu/support/view?id=' . $id);
    }

    public function viewAction()
    {
        $id = (int) ($_GET['id'] ?? 0);
        $ticket = IsuTicket::getById($id);
        if (!$ticket) {
            $this->flash('error', 'Ticket not found.');
            redirect('isu/support/index');
            return;
        }
        $assignees = [];
        foreach (IsuAdmin::getAll() as $a) {
            if ((int) $a['active'] === 1) { $assignees[] = $a['username']; }
        }

        View::render('isu/support/view.php', [
            'page_title' => 'Ticket ' . ($ticket['ref'] ?: ('#' . $ticket['id'])),
            'page_desc'  => $ticket['subject'],
            'ticket'     => $ticket,
            'replies'    => IsuTicket::replies($id),
            'statuses'   => IsuTicket::statuses(),
            'priorities' => IsuTicket::priorities(),
            'assignees'  => $assignees,
            'csrf_token' => $this->csrfToken(),
            'flash'      => $this->takeFlash(),
        ], 'isu');
    }

    public function replyAction()
    {
        if (!$this->guardPost()) return;
        $id = (int) ($_POST['id'] ?? 0);
        $body = trim($_POST['body'] ?? '');
        if ($body === '') {
            $this->flash('error', 'Reply cannot be empty.');
            redirect('isu/support/view?id=' . $id);
            return;
        }
        if (IsuTicket::addReply($id, $this->me(), $body)) {
            IsuAudit::log('ticket.reply', 'Replied to ticket #' . $id, $this->me());
            $this->flash('success', 'Reply added.');
        } else {
            $this->flash('error', 'Could not add the reply.');
        }
        redirect('isu/support/view?id=' . $id);
    }

    public function updateAction()
    {
        if (!$this->guardPost()) return;
        $id = (int) ($_POST['id'] ?? 0);
        $status   = $_POST['status'] ?? 'open';
        $priority = $_POST['priority'] ?? 'normal';
        $assigned = trim($_POST['assigned_to'] ?? '');

        if (IsuTicket::updateMeta($id, $status, $priority, $assigned)) {
            IsuAudit::log('ticket.update', 'Updated ticket #' . $id . ' (' . $status . ')', $this->me());
            $this->flash('success', 'Ticket updated.');
        } else {
            $this->flash('error', 'Could not update the ticket.');
        }
        redirect('isu/support/view?id=' . $id);
    }

    /* ------------------------------------------------------------------ */

    private function me(): array
    {
        return [
            'id'       => $this->isuUser['id'] ?? null,
            'username' => $this->isuUser['username'] ?? null,
            'email'    => $this->isuUser['email'] ?? null,
        ];
    }

    private function guardPost(): bool
    {
        $isPost = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
        if (!$isPost || !$this->validCsrf()) {
            $this->flash('error', 'Invalid or expired request. Please try again.');
            redirect('isu/support/index');
            return false;
        }
        return true;
    }

    private function back(string $type, string $message): void
    {
        $this->flash($type, $message);
        redirect('isu/support/index');
    }

    private function flash(string $type, string $message): void
    {
        $_SESSION['isu_support_flash'] = ['type' => $type, 'message' => $message];
    }

    private function takeFlash(): ?array
    {
        $f = $_SESSION['isu_support_flash'] ?? null;
        unset($_SESSION['isu_support_flash']);
        return $f;
    }
}
