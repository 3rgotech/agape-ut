<?php

return [
    'sections' => [
        'projectcalls' => 'Project Calls',
        'data'         => 'Data',
        'admin'        => 'Admin',
    ],

    'dashboard' => [
        'title' => 'Dashboard',
    ],

    'roles' => [
        'administrator' => 'Administrator',
        'manager'       => 'Manager',
        'applicant'     => 'Applicant',
        'expert'        => 'Expert',
    ],

    'users' => [
        'impersonate'                => 'Impersonate',
        'blocked_filter'             => 'Block Status',
        'all'                        => 'All',
        'unblocked'                  => 'Unblocked',
        'blocked'                    => 'Blocked',
        'block'                      => 'Block',
        'unblock'                    => 'Unblock',
        'invite_user'                => 'Invite User',
        'invitation_language'        => 'Invitation Language',
        'invitation_language_all'    => 'All',
        'invitation_duplicate_email' => 'The email entered is already present in the database of users or invitations.',
        'invitation_error'           => 'An error occurred while sending the invitation: ',
        'invitation_success'         => 'The invitation was sent successfully.',
    ],

    'invitations' => [
        'invitations_title' => 'Pending Invitations',
        'last_mail'         => 'Last Sent',
        'retry_count'       => 'Retry Count',
        'retry'             => 'Resend',
        'cancel'            => 'Cancel',
    ],

    'translatable_fields' => [
        'title'       => 'Multilingual Fields',
        'description' => 'The values displayed for these fields will be those in the language chosen by the user. Be sure to enter the correct values for all languages configured in the application!',
    ],

    'archived_records' => [
        'label' => 'Archived Records',
        'with'  => 'Only Non-Archived',
        'only'  => 'Only Archived',
        'all'   => 'All',
    ],

    'public'                          => 'Public',
    'make_public'                     => 'Make Public',
    'preview_application_form'        => 'Preview application',
    'pdf_export'                      => 'PDF Export',
    'pdf_export_anonymous'            => 'Anonymous PDF Export',
    'evaluation_pdf_export'           => 'Evaluations PDF Export',
    'evaluation_pdf_export_anonymous' => 'Anonymous Evaluations PDF Export',
    'close'                           => 'Close',

    'dynamic_attributes' => [
        'create'           => 'Add Dynamic Field',
        'after_field_help' => 'Leave blank to add the field to the end of the section',
        'add_option'       => 'Add Option',
        'add_choice'       => 'Add Choice',
    ],

    'dates'                => 'Calendar',
    'notation_description' => 'The criteria will be presented to experts in the order specified.',
    'files'                => 'File Templates',
    'files_description'    => 'These templates will be offered for download to applicants.',
    'default_number_help'  => 'If this value is set to zero, the corresponding field in the applicant form will be hidden.',
    'never'                => 'Never',
    'unsubmit'             => 'Devalidate',
    'force_submit'         => 'Submit Manually',
    'submission_status'    => [
        'draft'       => 'Draft',
        'submitted'   => 'Submitted',
        'devalidated' => 'Devalidated',
    ],

    'application' => [
        'offers'                       => 'Experts (:count)',
        'evaluations'                  => 'Evaluations (:count)',
        'add_expert'                   => 'Add Expert',
        'existing_expert'              => 'Existing Expert',
        'new_expert'                   => 'Email of Expert to Invite',
        'add_selection_comity_opinion' => 'Selection Comity',
    ],

    'evaluation_offer' => [
        'list_title'        => 'Evaluation Offers for Application :application',
        'rejection_title'   => 'Evaluation Offer Rejected for Reason: ',
        'status'            => [
            'accepted' => 'Accepted',
            'rejected' => 'Rejected',
            'pending'  => 'Pending',
        ],
        'retries'     => 'Retries',
        'retry'       => 'Send Reminder',
        'cancel'      => 'Cancel',
        'show_reason' => 'Show Reason',

        'success_sent'             => 'An evaluation offer has been sent to the chosen expert.',
        'success_invited'          => 'An invitation to join the platform has been sent to the chosen expert.',
        'success_linked'           => 'The chosen expert has already been invited but has not yet joined the platform. The evaluation offer has been linked to their invitation and a notification has been sent to them.',
        'error_no_expert_or_email' => 'You must choose an expert or enter an email address to send an invitation.',
    ],

    'evaluation' => [
        'list_title' => 'Evaluations for Application :application',
        'show_title' => 'Evaluation of Application :application by :expert',
        'status'     => [
            'accepted' => 'Accepted',
            'rejected' => 'Rejected',
            'pending'  => 'Pending',
        ],
        'grades'      => 'Grades',
        'retries'     => 'Retries',
        'retry'       => 'Send Reminder',
        'cancel'      => 'Cancel',
        'show_reason' => 'Show Reason',
    ],

    'settings' => [
        'title'    => 'Settings',

        'sections' => [
            'projectCalls'   => 'Project Calls',
            'defaultNumbers' => 'Default Number of ...',
            'evaluation'     => 'Evaluation',
        ],

        'fields' => [
            'defaultNumberOfWorkshopDates' => 'Predicted Dates (Workshop)',
            'defaultNumberOfExperts'       => 'Experts',
            'defaultNumberOfDocuments'     => 'Attached Documents',
            'defaultNumberOfLaboratories'  => 'Laboratories',
            'defaultNumberOfStudyFields'   => 'Study Fields',
            'defaultNumberOfKeywords'      => 'Keywords',
            'enableBudgetIncomeOutcome'    => 'Total Budget Projection (Income & Expenses)',
            'forbiddenDomains'             => 'Forbidden Email Domains for Registrations',
            'applicationForm'              => 'Application Form',
            'financialForm'                => 'Financial Form',
            'additionalInformation'        => 'Additional Information',
            'otherAttachments'             => 'Other Files',
            'enable'                       => 'Enable?',
            'extensions'                   => 'Allowed Extensions',
            'grades'                       => 'Grades',
            'gradeGrade'                   => 'Grade',
            'gradeLabel'                   => 'Label (Translated)',
            'notation'                     => 'Notation Criteria',
            'notationTitle'                => 'Title of Criterion (Translated)',
            'notationDescription'          => 'Description of Criterion (Translated)',
        ],

        'description' => [
            'grades'   => 'The first grade is the lowest, the last is the highest',
            'notation' => 'These notation criteria are the default values for each Project Call. They will be presented to experts in the order specified.'
        ],
        'actions' => [
            'addGrade'    => 'Add Grade',
            'addNotation' => 'Add Criterion',
        ],
    ]
];
