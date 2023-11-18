import { IconType } from 'react-icons';
import { BiMessageSquareAdd } from 'react-icons/bi';
import { AiFillHome } from 'react-icons/ai';
import { FaUsers } from 'react-icons/fa';
import { FiUser } from 'react-icons/fi';

type GroupLink = {
    label: string;
    icon: IconType;
    path: string;
    sublinks?: Omit<GroupLink, 'icon'>[];
};

export const sidebarItems: GroupLink[] = [
    { label: 'Dashboard', icon: AiFillHome, path: '/' },
    {
        label: 'Post',
        icon: BiMessageSquareAdd,
        path: '/post-management',
        sublinks: [
            { label: 'List of Properties', path: '/' },
            {
                label: 'Analytics',
                path: '/analytics',
                sublinks: [
                    { label: 'Listings', path: '/listings' },
                    { label: 'Promotions', path: '/promotions' },
                    { label: 'Ads', path: '/ads' },
                ],
            },
            { label: 'Calendar', path: '/calendar' },
        ],
    },
    { label: 'Users', path: '/user-management', icon: FaUsers },
    { label: 'Settings', path: '/settings', icon: FiUser },
];
