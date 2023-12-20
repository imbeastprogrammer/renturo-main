import { IconType } from 'react-icons';
import { BiMessageSquareAdd } from 'react-icons/bi';
import { AiFillHome } from 'react-icons/ai';
import { FaUsers } from 'react-icons/fa';

type GroupLink = {
    label: string;
    icon: IconType;
    path: string;
    sublinks?: Omit<GroupLink, 'icon'>[];
};

export const sidebarItems: GroupLink[] = [
    { label: 'Dashboard', icon: AiFillHome, path: '' },
    {
        label: 'Post',
        icon: BiMessageSquareAdd,
        path: '/post-management',
        sublinks: [
            { label: 'List of Properties', path: '/properties' },
            { label: 'Bookings', path: '/bookings' },
            { label: 'Categories', path: '/categories' },
            { label: 'Sub-Categories', path: '/sub-categories' },
            { label: 'Promotions', path: '/promotions' },
            { label: 'Ads', path: '/ads' },
            { label: 'Form Builder', path: '/form-builder' },
        ],
    },
    {
        label: 'Users',
        path: '/user-management',
        icon: FaUsers,
        sublinks: [
            { label: 'Admins', path: '/admins' },
            { label: 'Owners', path: '/owners' },
            { label: 'Sub Owners', path: '/sub-owners' },
            { label: 'Users', path: '/users' },
            { label: 'Reports', path: '/reports' },
        ],
    },
    // { label: 'Settings', path: '/settings/personal-information', icon: FiUser },
];
