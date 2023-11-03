import {
    BarChart3,
    EyeIcon,
    ListIcon,
    LucideIcon,
    PlusIcon,
} from 'lucide-react';

function Overview() {
    return (
        <article className='grid h-full grid-rows-[auto_1fr] gap-4 rounded-lg border p-4 shadow-lg'>
            <h1 className='text-[22px] font-semibold'>Overview</h1>
            <ul className='space-y-4'>
                <li>
                    <OverviewItem
                        icon={BarChart3}
                        title='₱ 45,787'
                        description='Last month sales'
                    />
                </li>
                <li>
                    <OverviewItem
                        icon={PlusIcon}
                        title='350'
                        description='New listings posted'
                    />
                </li>
                <li>
                    <OverviewItem
                        icon={ListIcon}
                        title='1,538'
                        description='Total listings'
                    />
                </li>
                <li>
                    <OverviewItem
                        icon={EyeIcon}
                        title='15,950'
                        description='Organic views'
                    />
                </li>
            </ul>
        </article>
    );
}

type OverviewItemProps = {
    icon: LucideIcon;
    title: string;
    description: string;
};

function OverviewItem({ title, description, ...props }: OverviewItemProps) {
    return (
        <div className='flex gap-4  overflow-hidden rounded-md border shadow-lg'>
            <div className='grid w-[80px] place-items-center bg-metalic-blue text-white'>
                <props.icon className='h-10 w-10' />
            </div>
            <div className='p-2'>
                <h1 className='text-[25px] font-semibold'>{title}</h1>
                <p className='text-[12px] italic text-gray-500'>
                    {description}
                </p>
            </div>
        </div>
    );
}

export default Overview;
