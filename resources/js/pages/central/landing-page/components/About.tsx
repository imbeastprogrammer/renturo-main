import {
    AnalyticsAndReportingIcon,
    BookAndReserveIcon,
    ListItemIcon,
} from '@/assets/central/landing-page';

function About() {
    return (
        <div className='bg-metalic-blue/10'>
            <article className='mx-auto max-w-[1556px] space-y-10 p-8'>
                <div className='text-center'>
                    <h1 className='text-[64px] font-bold text-black/90'>
                        Why <span className='text-metalic-blue'>Renturo?</span>
                    </h1>
                    <p className='mx-auto max-w-[50ch] text-[32px]'>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit,
                        sed do eiusmod tempor incididunt ut labore et dolore
                        magna aliqua.
                    </p>
                </div>
                <div className='flex justify-center gap-8'>
                    <ServiceCard
                        title='List Items'
                        description='Lorem ipsum dolor sit amet, consectetur adipiscing elit'
                        icon={ListItemIcon}
                    />
                    <ServiceCard
                        title='Book and Reserve'
                        description='Lorem ipsum dolor sit amet, consectetur adipiscing elit'
                        icon={BookAndReserveIcon}
                    />
                    <ServiceCard
                        title='Analytics and Reporting'
                        description='Lorem ipsum dolor sit amet, consectetur adipiscing elit'
                        icon={AnalyticsAndReportingIcon}
                    />
                </div>
            </article>
        </div>
    );
}

type ServiceCardProps = { title: string; description: string; icon: string };
function ServiceCard({ title, description, icon }: ServiceCardProps) {
    return (
        <div className='space-y-4 rounded-lg bg-white p-8 text-center'>
            <div className='mx-auto grid h-[140px] w-[140px] place-items-center rounded-full bg-metalic-blue'>
                <img
                    src={icon}
                    alt='service icon'
                    className='h-[70px] w-[70px] object-contain'
                />
            </div>
            <h2 className='text-[32px] font-semibold'>{title}</h2>
            <p className='text-[24px] text-black/90'>{description}</p>
        </div>
    );
}

export default About;
