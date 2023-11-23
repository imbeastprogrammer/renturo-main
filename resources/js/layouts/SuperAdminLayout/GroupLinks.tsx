import { ReactNode, useState } from 'react';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';
import { GoTriangleDown } from 'react-icons/go';
import { cn } from '@/lib/utils';
import { Link } from '@inertiajs/react';
import { IconType } from 'react-icons';

type GroupLinksProps = {
    label: string;
    icon: IconType;
    links: { label: string; path: string }[];
    isActive: boolean;
};

function GroupLinks({ label, icon: Icon, links, isActive }: GroupLinksProps) {
    const [isOpen, setIsOpen] = useState(false);

    const handleOpenChange = (open: boolean) => setIsOpen(open);

    return (
        <Collapsible open={isOpen} onOpenChange={handleOpenChange}>
            <CollapsibleTrigger className='flex w-full items-center gap-4 text-white'>
                <div
                    className={cn(
                        'absolute -left-4 h-[40px] w-1 rounded-lg bg-white/50 opacity-0',
                        isActive && 'opacity-100',
                    )}
                />
                <Icon className='h-[30px] w-[30px]' />
                <h1
                    className={cn(
                        'text-[15px] transition',
                        isOpen && 'font-medium',
                    )}
                >
                    {label}
                </h1>
                <GoTriangleDown
                    className={cn(
                        'ml-auto -rotate-90 object-contain transition',
                        isOpen && 'rotate-0',
                    )}
                />
            </CollapsibleTrigger>
            <CollapsibleContent>
                <div className='grid gap-2 p-4 text-[13px] text-white'>
                    {links.map((link) => (
                        <SubLink key={link.path} path={link.path}>
                            {link.label}
                        </SubLink>
                    ))}
                </div>
            </CollapsibleContent>
        </Collapsible>
    );
}

type SubLinkProps = { path: string; children: ReactNode };
function SubLink({ path, children }: SubLinkProps) {
    return <Link href={path}>{children}</Link>;
}

export default GroupLinks;
