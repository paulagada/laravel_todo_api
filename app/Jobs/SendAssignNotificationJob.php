<?php

namespace App\Jobs;

use App\Mail\AssignNotice;
use App\Models\DeviceToken;
use App\Services\FCMService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendAssignNotificationJob implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    protected $userEmail;
    protected $assignerMail;
    protected $todoTitle;
    protected $userId;
    protected $assignerId;
    protected $todoId;

    /**
     * Create a new job instance.
     */
    public function __construct($userEmail, $assignerMail, $todoTitle, $userId, $assignerId, $todoId)
    {
        $this->userEmail = $userEmail;
        $this->assignerMail = $assignerMail;
        $this->todoTitle = $todoTitle;
        $this->userId = $userId;
        $this->assignerId = $assignerId;
        $this->todoId = $todoId;
    }

    /**
     * Execute the job.
     */
    public function handle(FCMService $fcm): void
    {
        // Send Email


        Mail::to($this->userEmail)
            ->send(new AssignNotice($this->userEmail, $this->assignerMail, $this->todoTitle, true));

        // Send Push Notification
        $tokens = DeviceToken::where('user_id', $this->userId)
            ->pluck('token')->toArray();
        if (!empty($tokens)) {
            $fcm->sendToTokens(
                $tokens,
                'Todo notification',
                "You were assigned to: {$this->todoTitle}",
                [
                    'todo_id' => (string) $this->todoId,
                    'assigner_id' => (string) $this->assignerId
                ]
            );
        }
    }
}
