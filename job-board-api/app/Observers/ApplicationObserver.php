<?php

namespace App\Observers;

use App\Models\Application;
use App\Notifications\ApplicationStatusNotification;

class ApplicationObserver
{
    /**
     * Handle the Application "created" event.
     */
    public function created(Application $application): void
    {
        // Notify the employer that a new application was submitted
        $employer = $application->job->employer;
        $candidateName = $application->candidate->name;
        $jobTitle = $application->job->title;

        if ($employer) {
            $employer->notify(new ApplicationStatusNotification(
                'New Application',
                "{$candidateName} has applied for your job: {$jobTitle}."
            ));
        }
    }

    /**
     * Handle the Application "updated" event.
     */
    public function updated(Application $application): void
    {
        // If status was changed, notify candidate
        if ($application->isDirty('status')) {
            $candidate = $application->candidate;
            $jobTitle = $application->job->title;
            $newStatus = $application->status;

            if ($candidate) {
                $candidate->notify(new ApplicationStatusNotification(
                    'Application Status Update',
                    "Your application for {$jobTitle} has been updated to: {$newStatus}."
                ));
            }
        }
    }
}
