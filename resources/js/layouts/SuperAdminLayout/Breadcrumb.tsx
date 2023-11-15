import { ReactNode } from 'react';
import { Link } from '@inertiajs/react';
import { ChevronRightIcon } from 'lucide-react';
import { cn } from '@/lib/utils';

import { LabelMap } from '.';

function Breadcrumb() {
    const { pathname } = window.location;
    const pathnames = pathname
        .split('/')
        .filter((path) => path !== 'super-admin');

    return (
        <div className='flex items-center'>
            {pathnames.map((route, index) => {
                const routesTo = `/super-admin${pathnames
                    .slice(0, index + 1)
                    .join('/')}`;
                const notLast = index > 0 && index < pathnames.length - 1;

                return (
                    <>
                        <BreadcrumbLink
                            key={index}
                            path={routesTo}
                            isActive={routesTo === pathname}
                        >
                            {LabelMap[routesTo] || route}
                        </BreadcrumbLink>
                        {notLast && (
                            <ChevronRightIcon className='mx-1 h-4 w-4' />
                        )}
                    </>
                );
            })}
        </div>
    );
}

type BreadcrumbLinkProps = {
    children: ReactNode;
    path: string;
    isActive: boolean;
};

function BreadcrumbLink({ children, path, isActive }: BreadcrumbLinkProps) {
    return (
        <Link
            href={path}
            className={cn('capitalize', isActive && 'text-picton-blue')}
        >
            {children}
        </Link>
    );
}

export default Breadcrumb;
