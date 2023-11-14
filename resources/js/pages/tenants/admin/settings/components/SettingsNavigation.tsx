const linkGroups = [
    {
        label: 'Business Information',
        subLinks: [
            { label: 'Change Password', path: '/admin/settings' },
            { label: 'Notification', path: '/admin/settings' },
            { label: 'Automated Messages', path: '/admin/settings' },
            { label: 'User Management', path: '/admin/settings' },
        ],
    },
    {
        label: 'Support',
        subLinks: [
            { label: 'ADS', path: '/admin/settings' },
            { label: 'FAQs', path: '/admin/settings' },
            { label: 'Help Desk', path: '/admin/settings' },
            { label: 'User Management', path: '/admin/settings' },
        ],
    },
    {
        label: 'Privacy',
        subLinks: [
            { label: 'Data Privacy', path: '/admin/settings' },
            { label: 'Terms of Use', path: '/admin/settings' },
        ],
    },
];

function SettingsNavigation() {
    return (
        <div className='grid gap-y-8 p-6'>
            {linkGroups.map((linkGroup) => (
                <section key={linkGroup.label}>
                    <h1 className='mb-2 font-semibold uppercase text-metalic-blue'>
                        {linkGroup.label}
                    </h1>
                    <ul className='space-y-2'>
                        {linkGroup.subLinks.map((subLink) => (
                            <li
                                key={subLink.label}
                                className='cursor-pointer hover:underline'
                            >
                                {subLink.label}
                            </li>
                        ))}
                    </ul>
                </section>
            ))}
        </div>
    );
}

export default SettingsNavigation;
