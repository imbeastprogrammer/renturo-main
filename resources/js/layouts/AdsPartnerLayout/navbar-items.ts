import { IconType } from 'react-icons';
import { BiMessageSquareAdd } from 'react-icons/bi';
import { AiFillHome } from 'react-icons/ai';
import { FaUsers } from 'react-icons/fa';
import { FiUser } from 'react-icons/fi';

type NavBarItem = {
    label: string;
    path: string;
};

export const navbarItems: NavBarItem[] = [
    { label: 'Dashboard', path: '' },
    { label: 'Analytics', path: '/analytics' },
    { label: 'Advertisements', path: '/advertisement' },
    { label: 'Payments', path: '/payments' },
];
