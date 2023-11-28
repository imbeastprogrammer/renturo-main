import { Fragment } from 'react';
import { InertiaLinkProps, Link } from '@inertiajs/react';
import { ChevronRightIcon, Router } from 'lucide-react';
import { cn } from '@/lib/utils';

import { LabelMap } from '.';

function Breadcrumb() {
    const { pathname } = window.location;
    const pathnames = pathname.split('/').filter((path) => path !== 'admin');

    return (
        <div className='flex items-center'>
            {pathnames.map((route, index) => {
                const routesTo = `/admin${pathnames
                    .slice(0, index + 1)
                    .join('/')}`;
                const notLast = index > 0 && index < pathnames.length - 1;

                return (
                    <Fragment key={index}>
                        <BreadcrumbLink
                            href={routesTo}
                            isActive={routesTo === pathname}
                        >
                            {LabelMap[routesTo] || route}
                        </BreadcrumbLink>
                        {notLast && (
                            <ChevronRightIcon className='mx-1 h-4 w-4' />
                        )}
                    </Fragment>
                );
            })}
        </div>
    );
}

type BreadcrumbLinkProps = {
    isActive: boolean;
} & InertiaLinkProps;

function BreadcrumbLink({ isActive, ...props }: BreadcrumbLinkProps) {
    return (
        <Link
            className={cn(
                'capitalize text-black/50',
                isActive && 'text-jasper-orange',
            )}
            {...props}
        >
            {props.children}
        </Link>
    );
}

export default Breadcrumb;
