import { cn } from '@/lib/utils';
import { Link } from '@inertiajs/react';

const linkGroups = [
    {
        label: '',
        subLinks: [
            {
                label: 'Personal Information',
                path: '/admin/settings/personal-information',
            },
            {
                label: 'Change Password',
                path: '/admin/settings/change-password',
            },
            { label: 'Notification', path: '/admin/settings/notification' },
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
    const { pathname } = window.location;

    return (
        <div className='grid gap-y-8 p-6'>
            {linkGroups.map((linkGroup) => (
                <section key={linkGroup.label}>
                    <h1 className='mb-4 font-semibold uppercase text-black/30'>
                        {linkGroup.label}
                    </h1>
                    <ul className='space-y-2'>
                        {linkGroup.subLinks.map((subLink) => (
                            <li
                                key={subLink.label}
                                className={cn(
                                    'cursor-pointer text-lg hover:underline',
                                    {
                                        'font-medium text-metalic-blue':
                                            pathname === subLink.path,
                                    },
                                )}
                            >
                                <Link href={`${subLink.path}?active=Settings`}>
                                    {subLink.label}
                                </Link>
                            </li>
                        ))}
                    </ul>
                </section>
            ))}
        </div>
    );
}

export default SettingsNavigation;
