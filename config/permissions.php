<?php

return [

    'dashboard' => [
        'label' => 'Dashboard',
        'features' => [
            'dashboard.access' => 'Access Dashboard',
        ],
    ],

    'messages' => [
        'label' => 'Messages',
        'features' => [
            'messages.access' => 'Access Messages',
            'messages.send' => 'Send Messages',
        ],
    ],

    'users' => [
        'label' => 'Users',
        'features' => [
            'users.list' => 'List Users',
            'users.view' => 'View User Details',
            'users.create' => 'Create User',
            'users.edit' => 'Edit User',
            'users.delete' => 'Delete User',
            'users.ban' => 'Ban User',
            'users.unban' => 'Unban User',
            'users.lock' => 'Lock User',
            'users.unlock' => 'Unlock User',
            'users.force-logout' => 'Force User Logout',
            'users.reset-password' => 'Reset User Password',
            'users.bulk-delete' => 'Bulk Delete Users',
            'users.bulk-ban' => 'Bulk Ban Users',
            'users.bulk-unban' => 'Bulk Unban Users',
            'users.bulk-lock' => 'Bulk Lock Users',
            'users.bulk-unlock' => 'Bulk Unlock Users',
        ],
    ],

    'roles' => [
        'label' => 'Roles',
        'features' => [
            'roles.list' => 'List Roles',
            'roles.create' => 'Create Role',
            'roles.edit' => 'Edit Role',
            'roles.delete' => 'Delete Role',
        ],
    ],

    'permissions' => [
        'label' => 'Permissions',
        'features' => [
            'permissions.manage' => 'Manage Permissions',
        ],
    ],

    'departments' => [
        'label' => 'Departments',
        'features' => [
            'departments.list' => 'List Departments',
            'departments.view' => 'View Department',
            'departments.create' => 'Create Department',
            'departments.edit' => 'Edit Department',
            'departments.delete' => 'Delete Department',
            'departments.toggle-status' => 'Toggle Active/Inactive',
        ],
    ],

    'offices' => [
        'label' => 'Offices',
        'features' => [
            'offices.list' => 'List Offices',
            'offices.view' => 'View Office',
            'offices.create' => 'Create Office',
            'offices.edit' => 'Edit Office',
            'offices.delete' => 'Delete Office',
            'offices.toggle-status' => 'Toggle Active/Inactive',
        ],
    ],

    'documents' => [
        'label' => 'Documents',
        'features' => [
            'documents.list' => 'List Documents',
            'documents.view' => 'View Document',
            'documents.create' => 'Generate Document',
            'documents.edit' => 'Edit Document',
            'documents.delete' => 'Delete Document',
        ],
    ],

    'my-documents' => [
        'label' => 'My Documents',
        'features' => [
            'documents.my' => 'Access My Documents',
        ],
    ],

    'document-receiving' => [
        'label' => 'Document Receiving',
        'features' => [
            'documents.receive' => 'Receive Documents',
        ],
    ],

    'document-finish' => [
        'label' => 'Document Finish',
        'features' => [
            'documents.finish' => 'Finish Document Transaction',
        ],
    ],

    'document-terminate' => [
        'label' => 'Document Terminate',
        'features' => [
            'documents.terminate' => 'Terminate Document',
            'documents.reopen' => 'Reopen Finished/Terminated Document',
        ],
    ],

    'my-scanned' => [
        'label' => 'My Scanned',
        'features' => [
            'documents.my-scanned' => 'Access My Scanned Documents',
        ],
    ],

    'arta' => [
        'label' => 'ARTA Settings',
        'features' => [
            'arta.list' => 'List ARTA Settings',
            'arta.view' => 'View ARTA Setting',
            'arta.create' => 'Create ARTA Setting',
            'arta.edit' => 'Edit ARTA Setting',
            'arta.delete' => 'Delete ARTA Setting',
            'arta.toggle-status' => 'Toggle Active/Inactive',
        ],
    ],

    'document-types' => [
        'label' => 'Document Types',
        'features' => [
            'document-types.list' => 'List Document Types',
            'document-types.view' => 'View Document Type',
            'document-types.create' => 'Create Document Type',
            'document-types.edit' => 'Edit Document Type',
            'document-types.delete' => 'Delete Document Type',
            'document-types.toggle-status' => 'Toggle Active/Inactive',
        ],
    ],

    'activity-logs' => [
        'label' => 'Activity Logs',
        'features' => [
            'activity-logs.access' => 'Access Activity Logs',
        ],
    ],

    'security-logs' => [
        'label' => 'Security Logs',
        'features' => [
            'security-logs.access' => 'Access Security Logs',
        ],
    ],

    'statistics' => [
        'label' => 'Statistics',
        'features' => [
            'statistics.access' => 'Access Statistics',
        ],
    ],

    'email-settings' => [
        'label' => 'Email Settings',
        'features' => [
            'email-settings.access' => 'Access Email Settings',
        ],
    ],

    'settings' => [
        'label' => 'Settings',
        'features' => [
            'settings.access' => 'Access Settings',
        ],
    ],

];
