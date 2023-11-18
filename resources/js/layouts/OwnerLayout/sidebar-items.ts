type GroupLink = {
    label: string;
    icon: string;
    path: string;
    sublinks?: Omit<GroupLink, 'icon'>[];
};

export const sidebarItems: GroupLink[] = [
    { label: 'Dashboard', icon: 'this is icon', path: '/' },
    {
        label: 'Post',
        icon: 'this is icon',
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
];
