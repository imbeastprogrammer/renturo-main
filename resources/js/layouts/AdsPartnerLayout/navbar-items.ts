type NavBarItem = {
    label: string;
    path: string;
    isIndex?: boolean;
};

export const navbarItems: NavBarItem[] = [
    { label: 'Dashboard', path: '', isIndex: true },
    { label: 'Analytics', path: '/analytics' },
    { label: 'Advertisements', path: '/advertisement' },
    { label: 'Payments', path: '/payments' },
];
